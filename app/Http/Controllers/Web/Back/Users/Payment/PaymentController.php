<?php

namespace App\Http\Controllers\Web\Back\Users\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payments\KashierPaymentSessionService;
use App\Models\Book;
use App\Models\Lecture;
use App\Models\Test;
use App\Models\Invoice;
use App\Models\Course;
use App\Models\Live;
use App\Models\UserBook;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\PaymentGateway;
use Nafezly\Payments\Classes\KashierPayment;

class PaymentController extends Controller
{
    private const PENDING_KASHIER_INVOICE_MAX_AGE_MINUTES = 30;

    /**
     * معالجة طلب الدفع
     */
    public function processPayment(Request $request)
    {
        // التحقق من نوع العنصر المراد شراؤه
        if ($request->has('book_id')) {
            return $this->processBookPayment($request);
        } elseif ($request->has('lecture_id')) {
            return $this->processLecturePayment($request);
        } elseif ($request->has('test_id') || ($request->has('payment_type') && in_array($request->payment_type, ['single_test', 'course_tests']))) {
            return $this->processTestPayment($request);
        } elseif ($request->has('live_id')) {
            return $this->processLivePayment($request);
        }

        return redirect()->back()->with('error', 'نوع الدفع غير صحيح');
    }

    /**
     * التحقق من انتهاء صلاحية الوصول وإظهار رسالة تحذيرية
     */
    private function checkAccessExpiry($user, $courseId = null)
    {
        if (!$courseId) {
            return null;
        }

        $course = Course::find($courseId);
        if (!$course || !$course->hasAccessDurationLimit()) {
            return null;
        }

        // التحقق من انتهاء المدة
        if ($user->isCourseAccessExpired($courseId)) {
            $expiryDate = $user->getCourseAccessExpiryDate($courseId);
            return [
                'expired' => true,
                'message' => 'انتهت مدة الإتاحة لهذا الكورس. يجب إعادة الشراء للحصول على الوصول.',
                'expiry_date' => $expiryDate ? $expiryDate->format('Y-m-d') : null
            ];
        }

        // التحقق من اقتراب انتهاء المدة (آخر 3 أيام)
        $daysLeft = $user->getCourseAccessDaysLeft($courseId);

        if ($daysLeft !== null && $daysLeft <= 3 && $daysLeft > 0) {
            $expiryDate = $user->getCourseAccessExpiryDate($courseId);
            return [
                'expired' => false,
                'warning' => true,
                'message' => "ستنتهي مدة الإتاحة خلال {$daysLeft} أيام. تاريخ الانتهاء: " . ($expiryDate ? $expiryDate->format('Y-m-d') : 'غير محدد'),
                'days_left' => $daysLeft,
                'expiry_date' => $expiryDate ? $expiryDate->format('Y-m-d') : null
            ];
        }

        return null;
    }

    /**
     * إضافة معلومات انتهاء الصلاحية للعرض
     */
    private function addExpiryInfoToView($courseId, $viewData = [])
    {
        $user = auth()->user();
        $course = Course::find($courseId);

        if (!$course || !$course->hasAccessDurationLimit()) {
            return $viewData;
        }

        $expiryDate = $user->getCourseAccessExpiryDate($courseId);
        $daysLeft = $user->getCourseAccessDaysLeft($courseId);

        if ($expiryDate) {
            $viewData['access_expiry'] = [
                'expiry_date' => $expiryDate,
                'days_left' => $daysLeft,
                'formatted_duration' => $course->getFormattedAccessDuration(),
                'is_expired' => $user->isCourseAccessExpired($courseId),
                'is_warning' => $daysLeft !== null && $daysLeft <= 3 && $daysLeft > 0
            ];
        }

        return $viewData;
    }

    /**
     * معالجة دفع المحاضرات
     */
    private function processLecturePayment(Request $request)
    {
        $request->validate([
            'lecture_id' => 'required|exists:lectures,id',
            'payment_type' => 'required|in:single,course,month'
        ]);

        $user = auth()->user();
        $lecture = Lecture::with('course')->findOrFail($request->lecture_id);

        // التحقق من صحة نوع الدفع مع نوع المحاضرة
        if ($request->payment_type !== 'course' && !$this->validatePaymentType($lecture, $request->payment_type)) {
            return redirect()->back()->with('error', __('l.invalid_payment_type'));
        }

        // التحقق من عدم وجود دفع سابق
        if ($this->hasAlreadyPaid($user, $lecture, $request->payment_type)) {
            // التحقق من انتهاء صلاحية الوصول
            $expiryCheck = $this->checkAccessExpiry($user, $lecture->course_id);
            if ($expiryCheck && $expiryCheck['expired']) {
                // انتهت الصلاحية، يجب إعادة الشراء
                session()->flash('warning', $expiryCheck['message']);
            } else {
                return redirect()->route('dashboard.users.courses-lectures-show', ['id' => encrypt($lecture->id)])
                       ->with('info', __('l.already_purchased'));
            }
        // } else {
        //     // التحقق من اقتراب انتهاء الصلاحية للمحتوى المدفوع مسبقاً
        //     $expiryCheck = $this->checkAccessExpiry($user, $lecture->course_id);
        //     if ($expiryCheck && $expiryCheck['warning']) {
        //         session()->flash('warning', $expiryCheck['message']);
        //     }
        }

        try {
            // حساب قيمة المدفوع
            if ($request->payment_type === 'course') {
                // شراء كورس مباشر
                $amount = $lecture->course->price ?? 0;
            } else {
                $paymentInfo = $this->getPaymentInfo($lecture, $user);
                $amount = $paymentInfo['amount'];
            }

            // حساب رسوم بوابة الدفع
            $gatewayPercentage = PaymentGateway::where('name', 'kashier')->first()->fees ?? 0;
            $gatewayFee = $amount * $gatewayPercentage / 100;
            $total = $amount + $gatewayFee;

            // إعداد بوابة الدفع Kashier
            if (!$this->configureKashierGatewayFromDatabase()) {
                return $this->kashierGatewayUnavailableResponse();
            }

            $usePaymentSessions = $this->usesKashierPaymentSessions();
            $sourceMethods = $usePaymentSessions
                ? (string) config('nafezly-payments.KASHIER_ALLOWED_METHODS')
                : 'card,bank_installments,wallet,fawry';
            $this->logKashierPaymentCreation('before_pay', $request, $user, $amount, $gatewayPercentage, $gatewayFee, $total, $sourceMethods);

            if ($usePaymentSessions) {
                $description = $request->payment_type === 'course'
                    ? 'Course payment: ' . $lecture->course->name
                    : 'Lecture payment: ' . $lecture->name;
                $response = $this->createKashierPaymentSession($user, $total, $description);
            } else {
                $payment = new KashierPayment();
                $response = $payment
                    ->setAmount(round($total, 2))
                    ->setSource($sourceMethods)
                    ->pay();
            }

            $this->logKashierPaymentCreation('after_pay', $request, $user, $amount, $gatewayPercentage, $gatewayFee, $total, $sourceMethods, $response);

            // إنشاء الفاتورة مع بيانات الدفع
            $invoice = $this->createInvoiceWithPayment($user, $lecture, $request->payment_type, $total, $response['payment_id'] ?? null);
            $this->logKashierInvoiceCreated($invoice, $response, $sourceMethods);

            // توجيه المستخدم لصفحة الدفع
            return $usePaymentSessions
                ? $this->kashierSessionView($response)
                : view('themes/default/back.users.payment.pay', ['link' => $response['html']]);

        } catch (\Exception $e) {
            logger('Payment Error: ' . $e->getMessage());
            return redirect()->back()->with('error', __('l.payment_failed'));
        }
    }

    private function processBookPayment(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'track' => 'nullable|string|max:100',
        ]);

        $user = auth()->user();
        $book = Book::with('course')->findOrFail($request->book_id);
        $track = $request->input('track');

        if ($book->status !== 'ready' || ! $this->bookBelongsToStudentCourse($book, $user, $track)) {
            abort(404);
        }

        if ($this->hasActiveBookAccess($user, $book)) {
            return redirect()->route('books.reader.read', ['book' => $book->slug])
                ->with('info', 'Book access is already available.');
        }

        $amount = (float) $book->price;

        if ($amount <= 0) {
            $this->grantBookAccess($user, $book, 'free_book');

            return redirect()->route('books.reader.read', ['book' => $book->slug])
                ->with('success', 'Book access is now available.');
        }

        try {
            $gatewayPercentage = PaymentGateway::where('name', 'kashier')->first()->fees ?? 0;
            $gatewayFee = $amount * $gatewayPercentage / 100;
            $total = $amount + $gatewayFee;

            if (!$this->configureKashierGatewayFromDatabase()) {
                return $this->kashierGatewayUnavailableResponse();
            }

            $usePaymentSessions = $this->usesKashierPaymentSessions();
            $sourceMethods = $usePaymentSessions
                ? (string) config('nafezly-payments.KASHIER_ALLOWED_METHODS')
                : 'card,bank_installments,wallet,fawry';

            $this->logKashierPaymentCreation('before_pay', $request, $user, $amount, $gatewayPercentage, $gatewayFee, $total, $sourceMethods);

            if ($usePaymentSessions) {
                $response = $this->createKashierPaymentSession($user, $total, 'Book payment: ' . $book->title);
            } else {
                $payment = new KashierPayment();
                $response = $payment
                    ->setAmount(round($total, 2))
                    ->setSource($sourceMethods)
                    ->pay();
            }

            $this->logKashierPaymentCreation('after_pay', $request, $user, $amount, $gatewayPercentage, $gatewayFee, $total, $sourceMethods, $response);

            $invoice = $this->createBookInvoiceWithPayment($user, $book, $total, $response['payment_id'] ?? null);
            $this->logKashierInvoiceCreated($invoice, $response, $sourceMethods);

            return $usePaymentSessions
                ? $this->kashierSessionView($response)
                : view('themes/default/back.users.payment.pay', ['link' => $response['html']]);
        } catch (\Exception $e) {
            logger()->error('Book Payment Error: ' . $e->getMessage(), [
                'book_id' => $request->input('book_id'),
                'user_id' => $user?->id,
                'track' => $track,
            ]);

            $message = 'Book payment could not be started. Please try again or contact support.';

            if (app()->environment('local')) {
                $message .= ' Reason: ' . $e->getMessage();
            }

            return redirect()->back()->with('error', $message);
        }
    }

    /**
     * معالجة دفع الاختبارات
     */
    private function processTestPayment(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|in:single_test,course_tests',
            'test_id' => 'required_if:payment_type,single_test|exists:tests,id',
            'course_id' => 'required_if:payment_type,course_tests|exists:courses,id'
        ]);

        $user = auth()->user();

        try {
            if ($request->payment_type === 'single_test') {
                // شراء اختبار مفرد
                $test = Test::with('course')->findOrFail($request->test_id);

                // التحقق من عدم وجود دفع سابق
                if ($user->canAccessTest($test->id)) {
                    // التحقق من انتهاء صلاحية الوصول
                    $expiryCheck = $this->checkAccessExpiry($user, $test->course_id);
                    if ($expiryCheck && $expiryCheck['expired']) {
                        // انتهت الصلاحية، يجب إعادة الشراء
                        session()->flash('warning', $expiryCheck['message']);
                    } else {
                        return redirect()->route('dashboard.users.tests.show', $test->id)
                            ->with('info', 'لقد اشتريت هذا الاختبار مسبقاً');
                    }
                // } else {
                //     // التحقق من اقتراب انتهاء الصلاحية
                //     $expiryCheck = $this->checkAccessExpiry($user, $test->course_id);
                //     if ($expiryCheck && $expiryCheck['warning']) {
                //         session()->flash('warning', $expiryCheck['message']);
                //     }
                }

                $amount = $test->price;
                $description = 'شراء اختبار: ' . $test->name;
                $courseId = $test->course_id;
                $typeValue = $test->id;

            } else {
                // شراء جميع اختبارات الكورس
                $course = Course::with('activeTests')->findOrFail($request->course_id);

                // التحقق من عدم وجود دفع سابق
                if ($user->hasPurchasedCourseQuizzes($course->id)) {
                    // التحقق من انتهاء صلاحية الوصول
                    $expiryCheck = $this->checkAccessExpiry($user, $course->id);
                    if ($expiryCheck && $expiryCheck['expired']) {
                        // انتهت الصلاحية، يجب إعادة الشراء
                        session()->flash('warning', $expiryCheck['message']);
                    } else {
                        return redirect()->route('dashboard.users.tests')
                            ->with('info', 'لقد اشتريت جميع اختبارات هذا الكورس مسبقاً');
                    }
                // } else {
                //     // التحقق من اقتراب انتهاء الصلاحية
                //     $expiryCheck = $this->checkAccessExpiry($user, $course->id);
                //     if ($expiryCheck && $expiryCheck['warning']) {
                //         session()->flash('warning', $expiryCheck['message']);
                //     }
                }

                $amount = $course->tests_price ?: $course->activeTests->sum('price');
                $description = 'شراء جميع اختبارات كورس: ' . $course->name;
                $courseId = $course->id;
                $typeValue = $course->id;
            }

            // حساب رسوم بوابة الدفع
            $gatewayPercentage = PaymentGateway::where('name', 'kashier')->first()->fees ?? 0;
            $gatewayFee = $amount * $gatewayPercentage / 100;
            $total = $amount + $gatewayFee;

            // إعداد بوابة الدفع Kashier
            if (!$this->configureKashierGatewayFromDatabase()) {
                return $this->kashierGatewayUnavailableResponse();
            }

            $usePaymentSessions = $this->usesKashierPaymentSessions();
            $sourceMethods = $usePaymentSessions
                ? (string) config('nafezly-payments.KASHIER_ALLOWED_METHODS')
                : 'card,bank_installments,wallet,fawry';
            $this->logKashierPaymentCreation('before_pay', $request, $user, $amount, $gatewayPercentage, $gatewayFee, $total, $sourceMethods);

            if ($usePaymentSessions) {
                $response = $this->createKashierPaymentSession($user, $total, $description);
            } else {
                $payment = new KashierPayment();
                $response = $payment
                    ->setAmount(round($total, 2))
                    ->setSource($sourceMethods)
                    ->pay();
            }

            $this->logKashierPaymentCreation('after_pay', $request, $user, $amount, $gatewayPercentage, $gatewayFee, $total, $sourceMethods, $response);

            // إنشاء الفاتورة مع بيانات الدفع
            $invoice = $this->createTestInvoiceWithPayment(
                $user,
                $request->payment_type,
                $courseId,
                $typeValue,
                $total,
                $description,
                $response['payment_id'] ?? null
            );
            $this->logKashierInvoiceCreated($invoice, $response, $sourceMethods);

            // توجيه المستخدم لصفحة الدفع
            return $usePaymentSessions
                ? $this->kashierSessionView($response)
                : view('themes/default/back.users.payment.pay', ['link' => $response['html']]);

        } catch (\Exception $e) {
            logger('Test Payment Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'فشل في معالجة الدفع');
        }
    }

    /**
     * معالجة دفع البث المباشر (اللايفات)
     */
    private function processLivePayment(Request $request)
    {
        $request->validate([
            'live_id' => 'required|exists:lives,id'
        ]);

        $user = auth()->user();
        $live = Live::with('course')->findOrFail($request->live_id);

        // التحقق من نوع البث المباشر - يجب أن يكون مجاني أو مدفوع فقط
        if (!in_array($live->type, ['free', 'price'])) {
            return redirect()->back()->with('error', 'نوع البث المباشر غير مدعوم. يجب أن يكون مجاني أو مدفوع فقط.');
        }

        // إذا كان البث مجاني، توجيه المستخدم مباشرة
        if ($live->isFree()) {
            return redirect()->route('dashboard.users.lives.show', $live->id)
                   ->with('success', 'يمكنك الانضمام للبث المباشر مجاناً');
        }

        // التحقق من عدم وجود دفع سابق للبث المدفوع
        if ($user->hasPaidLive($live->id)) {
            // التحقق من انتهاء صلاحية الوصول
            $expiryCheck = $this->checkAccessExpiry($user, $live->course_id);
            if ($expiryCheck && $expiryCheck['expired']) {
                // انتهت الصلاحية، يجب إعادة الشراء
                session()->flash('warning', $expiryCheck['message']);
            } else {
                return redirect()->route('dashboard.users.lives.show', $live->id)
                       ->with('info', 'لقد اشتريت هذا البث المباشر مسبقاً');
            }
        // } else {
        //     // التحقق من اقتراب انتهاء الصلاحية
        //     $expiryCheck = $this->checkAccessExpiry($user, $live->course_id);
        //     if ($expiryCheck && $expiryCheck['warning']) {
        //         session()->flash('warning', $expiryCheck['message']);
        //     }
        }

        try {
            // حساب قيمة المدفوع للبث المدفوع
            $amount = $live->price;

            if ($amount <= 0) {
                return redirect()->back()->with('error', 'سعر البث المباشر غير صحيح');
            }

            // حساب رسوم بوابة الدفع
            $gatewayPercentage = PaymentGateway::where('name', 'kashier')->first()->fees ?? 0;
            $gatewayFee = $amount * $gatewayPercentage / 100;
            $total = $amount + $gatewayFee;

            // إعداد بوابة الدفع Kashier
            if (!$this->configureKashierGatewayFromDatabase()) {
                return $this->kashierGatewayUnavailableResponse();
            }

            $usePaymentSessions = $this->usesKashierPaymentSessions();
            $sourceMethods = $usePaymentSessions
                ? (string) config('nafezly-payments.KASHIER_ALLOWED_METHODS')
                : 'card,bank_installments,wallet,fawry';
            $this->logKashierPaymentCreation('before_pay', $request, $user, $amount, $gatewayPercentage, $gatewayFee, $total, $sourceMethods);

            if ($usePaymentSessions) {
                $response = $this->createKashierPaymentSession($user, $total, 'Live payment: ' . $live->name);
            } else {
                $payment = new KashierPayment();
                $response = $payment
                    ->setAmount(round($total, 2))
                    ->setSource($sourceMethods)
                    ->pay();
            }

            $this->logKashierPaymentCreation('after_pay', $request, $user, $amount, $gatewayPercentage, $gatewayFee, $total, $sourceMethods, $response);

            // إنشاء الفاتورة مع بيانات الدفع
            $invoice = $this->createLiveInvoiceWithPayment(
                $user,
                $live,
                $total,
                $response['payment_id'] ?? null
            );
            $this->logKashierInvoiceCreated($invoice, $response, $sourceMethods);

            // توجيه المستخدم لصفحة الدفع
            return $usePaymentSessions
                ? $this->kashierSessionView($response)
                : view('themes/default/back.users.payment.pay', ['link' => $response['html']]);

        } catch (\Exception $e) {
            logger('Live Payment Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'فشل في معالجة الدفع');
        }
    }

    /**
     * التحقق من صحة نوع الدفع
     */
    private function validatePaymentType($lecture, $paymentType)
    {
        switch ($lecture->type) {
            case 'price':
                // للمحاضرات المدفوعة - دفع محاضرة واحدة
                return $paymentType === 'single';
            case 'course':
            case 'month':
                // للمحاضرات الشهرية والكورس - دفع الكورس بالكامل
                return $paymentType === 'course';
            default:
                return false;
        }
    }

    /**
     * التحقق من الدفع السابق
     */
    private function hasAlreadyPaid($user, $lecture, $paymentType)
    {
        switch ($paymentType) {
            case 'single':
                // التحقق من دفع المحاضرة المنفردة
                return $user->hasPaidLecture($lecture->id, $lecture->course_id);
            case 'course':
                // التحقق من شراء الكورس بالكامل (ينطبق على course و month)
                return $user->hasPurchasedCourseLectures($lecture->course_id);
            default:
                return false;
        }
    }

    /**
     * إنشاء الفاتورة مع بيانات الدفع
     */
    private function createInvoiceWithPayment($user, $lecture, $paymentType, $amount, $paymentId)
    {
        $invoiceData = [
            'user_id' => $user->id,
            'category' => 'lecture',
            'type' => $paymentType,
            'amount' => $amount,
            'status' => 'pending',
            'pid' => $paymentId
        ];

        // تحديد type_value حسب نوع الدفع
        switch ($paymentType) {
            case 'single':
                $invoiceData['type_value'] = $lecture->id;
                $invoiceData['course_id'] = $lecture->course_id;
                break;
            case 'course':
            case 'month':
                $invoiceData['type_value'] = $lecture->course_id;
                $invoiceData['course_id'] = $lecture->course_id;
                break;
        }

        $invoice = Invoice::create($invoiceData);
        $this->rememberPendingKashierInvoice($invoice);

        return $invoice;
    }

    /**
     * إنشاء فاتورة للاختبارات مع بيانات الدفع
     */
    private function createTestInvoiceWithPayment($user, $paymentType, $courseId, $typeValue, $amount, $description, $paymentId)
    {
        $invoiceData = [
            'user_id' => $user->id,
            'category' => 'quiz',
            'type' => $paymentType === 'single_test' ? 'single' : 'course',
            'type_value' => $typeValue,
            'course_id' => $courseId,
            'amount' => $amount,
            'status' => 'pending',
            'pid' => $paymentId,
        ];

        $invoice = Invoice::create($invoiceData);
        $this->rememberPendingKashierInvoice($invoice);

        return $invoice;
    }

    /**
     * إنشاء فاتورة للبث المباشر مع بيانات الدفع
     */
    private function createLiveInvoiceWithPayment($user, $live, $amount, $paymentId)
    {
        $invoiceData = [
            'user_id' => $user->id,
            'category' => 'live',
            'type' => 'single',
            'type_value' => $live->id,
            'course_id' => $live->course_id,
            'amount' => $amount,
            'status' => 'pending',
            'pid' => $paymentId,
        ];

        $invoice = Invoice::create($invoiceData);
        $this->rememberPendingKashierInvoice($invoice);

        return $invoice;
    }

    private function createBookInvoiceWithPayment($user, Book $book, $amount, $paymentId)
    {
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'category' => 'book',
            'type' => 'single',
            'type_value' => $book->id,
            'course_id' => $book->course_id,
            'amount' => $amount,
            'status' => 'pending',
            'pid' => $paymentId,
        ]);

        $this->rememberPendingKashierInvoice($invoice);

        return $invoice;
    }

    /**
     * إنشاء الفاتورة (للدفع المباشر بدون بوابة)
     */
    private function createInvoice($user, $lecture, $paymentType, $amount)
    {
        $invoiceData = [
            'user_id' => $user->id,
            'category' => 'lecture',
            'type' => $paymentType,
            'amount' => $amount,
            'status' => 'pending'
        ];

        // تحديد type_value حسب نوع الدفع
        switch ($paymentType) {
            case 'single':
                $invoiceData['type_value'] = $lecture->id;
                $invoiceData['course_id'] = $lecture->course_id;
                break;
            case 'course':
                $invoiceData['type_value'] = $lecture->course_id;
                $invoiceData['course_id'] = $lecture->course_id;
                break;
        }

        return Invoice::create($invoiceData);
    }

    /**
     * تحديث حالة الفاتورة إلى مدفوع
     */
    private function markInvoiceAsPaid($invoice)
    {
        $invoice->update([
            'status' => 'paid',
        ]);
    }

    /**
     * حساب معلومات الدفع للمحاضرة
     */
    public function getPaymentInfo($lecture, $user)
    {
        switch ($lecture->type) {
            case 'price':
                // دفع محاضرة واحدة
                return [
                    'type' => 'single',
                    'amount' => $lecture->price,
                    'description' => __('l.single_lecture_payment'),
                    'course_id' => $lecture->course_id
                ];

            case 'course':
            case 'month':
                // دفع الكورس بالكامل (لكل من course و month)
                return [
                    'type' => 'course',
                    'amount' => $lecture->course->price ?? 0,
                    'description' => __('l.full_course_access'),
                    'course_id' => $lecture->course_id
                ];

            default:
                return [
                    'type' => 'unknown',
                    'amount' => 0,
                    'description' => __('l.unknown_payment_type')
                ];
        }
    }



    /**
     * التحقق من حالة الدفع
     */
    public function verify($payment, Request $request)
    {
        $invoice = $this->resolveInvoiceFromGatewayReference($request);

        try {
            logger('Kashier payment verification started', $this->safeKashierCallbackContext($request, $invoice));

            $kashierConfigured = $this->configureKashierGatewayFromDatabase();

            logger('Kashier payment verification configuration result', [
                'configured_from_database' => $kashierConfigured,
            ]);

            if (!$kashierConfigured) {
                if ($invoice?->status === 'paid') {
                    return $this->redirectToPaymentSuccess($invoice, $request);
                }

                return $this->redirectToPaymentFailed($invoice, $request)
                    ->with('error', 'Payment gateway is not available. Please contact support.');
            }

            $payment = new KashierPayment();
            $verificationRequest = $request->duplicate();
            $callbackStatus = strtoupper(trim((string) $request->input('paymentStatus', '')));
            $gatewayReference = $this->gatewayReference($request->all());

            if (in_array($callbackStatus, ['SUCCESS', 'CAPTURED', 'PAID', 'APPROVED'], true)) {
                // The package only recognizes SUCCESS, then confirms CAPTURED via Kashier's API.
                $verificationRequest->merge(['paymentStatus' => 'SUCCESS']);
            }

            if (!$verificationRequest->filled('merchantOrderId') && $gatewayReference !== null) {
                $verificationRequest->merge(['merchantOrderId' => $gatewayReference]);
            }

            $response = $payment->verify($verificationRequest);

            logger('Kashier payment verification response', $this->safeKashierCallbackContext($request, $invoice, $response));

            // جلب الفاتورة من قاعدة البيانات باستخدام معرف الدفع
            $invoice = $this->resolveInvoiceFromGatewayReference($request, $response) ?? $invoice;

            logger('Kashier payment verification invoice lookup', $this->safeKashierCallbackContext($request, $invoice, $response));

            $verified = filter_var($response['success'] ?? false, FILTER_VALIDATE_BOOLEAN);

            // A signed webhook may finish before the browser returns from Kashier.
            if ($invoice?->status === 'paid') {
                return $this->redirectToPaymentSuccess($invoice, $request, $response);
            }

            if ($verified && $invoice) {
                if ($invoice->status != 'paid') {
                    $invoice->status = 'paid';
                    $invoice->save();
                }

                return $this->redirectToPaymentSuccess($invoice, $request, $response);
            } else {
                if ($verified && !$invoice) {
                    logger()->warning(
                        'Kashier verification succeeded without matching invoice',
                        $this->safeKashierCallbackContext($request, null, $response)
                    );
                }

                if ($invoice && $invoice->status !== 'paid') {
                    $invoice->status = 'failed';
                    $invoice->save();
                }

                return $this->redirectToPaymentFailed($invoice, $request, $response)
                    ->with('error', __('l.payment_failed'));
            }
        } catch (\Throwable $e) {
            logger('Payment Verification Error: ' . $e->getMessage());

            $invoice = $this->resolveInvoiceFromGatewayReference($request) ?? $invoice;

            if ($invoice?->status === 'paid') {
                return $this->redirectToPaymentSuccess($invoice, $request);
            }

            return $this->redirectToPaymentFailed($invoice, $request)
                ->with('error', __('l.payment_failed'));
        }
    }

    /**
     * صفحة تأكيد الدفع
     */
    public function paymentSuccess(Request $request)
    {
        $invoiceId = $request->get('invoice_id');
        $invoice = Invoice::with(['student', 'lecture', 'course', 'book'])
                         ->where('user_id', auth()->id())
                         ->findOrFail($invoiceId);

        if ($invoice->status !== 'paid') {
            return $this->redirectToPaymentFailed($invoice, $request);
        }

        if ($invoice->category === 'book' && ! $this->grantBookAccessForPaidInvoice($invoice)) {
            return $this->redirectToPaymentFailed($invoice, $request)
                ->with('error', 'Book payment was paid, but access could not be assigned. Please contact support.');
        }

        return view('themes/default/back.users.payment.success', compact('invoice'));
    }

    /**
     * صفحة فشل الدفع
     */
    public function paymentFailed(Request $request)
    {
        $invoiceId = $this->normalizedGatewayValue($request->get('invoice_id'));
        $paymentReference = $this->gatewayReference($request->all());

        $invoice = null;

        if ($invoiceId !== null && ctype_digit($invoiceId) && (int) $invoiceId > 0) {
            $invoice = Invoice::with(['student', 'lecture', 'course', 'book'])
                ->where('user_id', auth()->id())
                ->find($invoiceId);
        }

        $invoice ??= $this->resolveInvoiceFromGatewayReference($request);
        $invoice ??= $this->resolvePendingKashierInvoice($request);

        if ($invoice?->status === 'paid') {
            return $this->redirectToPaymentSuccess($invoice, $request);
        }

        if ($invoice === null && $invoiceId === null && $paymentReference === null) {
            $recentPaidInvoices = Invoice::with(['student', 'lecture', 'course', 'book'])
                ->where('user_id', auth()->id())
                ->where('status', 'paid')
                ->where(function ($query) {
                    $cutoff = now()->subMinutes(10);

                    $query->where('created_at', '>=', $cutoff)
                        ->orWhere('updated_at', '>=', $cutoff);
                })
                ->latest('updated_at')
                ->limit(2)
                ->get();

            if ($recentPaidInvoices->count() === 1) {
                return $this->redirectToPaymentSuccess($recentPaidInvoices->first(), $request);
            }

            logger()->warning('Kashier failed return could not be resolved uniquely', [
                'user_id' => auth()->id(),
                'recent_paid_invoice_count' => $recentPaidInvoices->count(),
                'window_minutes' => 10,
            ]);
        }

        $supportTimestamp = now();

        return view('themes/default/back.users.payment.failed', compact('invoice', 'paymentReference', 'supportTimestamp'));
    }

    private function resolveInvoiceFromGatewayReference(Request $request, array $response = []): ?Invoice
    {
        $reference = $this->gatewayReference($response) ?? $this->gatewayReference($request->all());

        if ($reference === null) {
            return null;
        }

        return Invoice::with(['student', 'lecture', 'course', 'book'])
            ->where('user_id', auth()->id())
            ->where('pid', $reference)
            ->first();
    }

    private function rememberPendingKashierInvoice(Invoice $invoice): void
    {
        session()->put([
            'pending_kashier_invoice_id' => $invoice->id,
            'pending_kashier_invoice_pid' => $invoice->pid,
        ]);
    }

    private function resolvePendingKashierInvoice(Request $request): ?Invoice
    {
        $invoiceId = $this->normalizedGatewayValue($request->session()->get('pending_kashier_invoice_id'));
        $invoicePid = $this->normalizedGatewayValue($request->session()->get('pending_kashier_invoice_pid'));

        if ($invoiceId === null || !ctype_digit($invoiceId) || (int) $invoiceId <= 0 || $invoicePid === null) {
            return null;
        }

        return Invoice::with(['student', 'lecture', 'course', 'book'])
            ->where('user_id', auth()->id())
            ->where('pid', $invoicePid)
            ->where('created_at', '>=', now()->subMinutes(self::PENDING_KASHIER_INVOICE_MAX_AGE_MINUTES))
            ->find((int) $invoiceId);
    }

    private function clearPendingKashierInvoice(Invoice $invoice, ?Request $request = null): void
    {
        $session = $request?->session() ?? session();
        $invoiceId = $this->normalizedGatewayValue($session->get('pending_kashier_invoice_id'));
        $invoicePid = $this->normalizedGatewayValue($session->get('pending_kashier_invoice_pid'));

        if ($invoiceId === (string) $invoice->id && $invoicePid === $this->normalizedGatewayValue($invoice->pid)) {
            $session->forget(['pending_kashier_invoice_id', 'pending_kashier_invoice_pid']);
        }
    }

    private function gatewayReference(array $data): ?string
    {
        foreach (['payment_id', 'merchantOrderId', 'merchant_order_id', 'orderId', 'order_id', 'paymentId'] as $key) {
            $value = $this->normalizedGatewayValue($data[$key] ?? null);

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    private function normalizedGatewayValue($value): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '' || in_array(strtolower($value), ['null', 'undefined'], true)) {
            return null;
        }

        return $value;
    }

    private function redirectToPaymentSuccess(Invoice $invoice, ?Request $request = null, array $response = [])
    {
        $target = route('dashboard.users.payment-success', ['invoice_id' => $invoice->id]);

        if ($invoice->category === 'book') {
            if (! $this->grantBookAccessForPaidInvoice($invoice)) {
                return $this->redirectToPaymentFailed($invoice, $request ?? request(), $response)
                    ->with('error', 'Book payment was paid, but access could not be assigned. Please contact support.');
            }
        }

        $this->clearPendingKashierInvoice($invoice, $request);

        logger('Kashier payment redirect', array_merge(
            $request ? $this->safeKashierCallbackContext($request, $invoice, $response) : $this->safeKashierInvoiceContext($invoice),
            ['redirect_target' => $target]
        ));

        return redirect($target)
            ->with('success', __('l.payment_successful'));
    }

    private function redirectToPaymentFailed(?Invoice $invoice, Request $request, array $response = [])
    {
        $parameters = [];

        if ($invoice) {
            $parameters['invoice_id'] = $invoice->id;
        } else {
            $reference = $this->gatewayReference($response) ?? $this->gatewayReference($request->all());

            if ($reference !== null) {
                $parameters['payment_id'] = $reference;
            }
        }

        $target = route('dashboard.users.payment-failed', $parameters);

        logger()->warning('Kashier payment redirect', array_merge(
            $this->safeKashierCallbackContext($request, $invoice, $response),
            ['redirect_target' => $target]
        ));

        return redirect($target);
    }

    /**
     * صفحة إلغاء الدفع
     */
    public function paymentCancelled(Request $request)
    {
        return view('themes/default/back.users.payment.cancelled');
    }

    private function safeKashierCallbackContext(Request $request, ?Invoice $invoice = null, array $response = []): array
    {
        $requestData = $request->all();

        return array_merge($this->safeKashierInvoiceContext($invoice), [
            'merchant_order_id' => $this->gatewayReference($response) ?? $this->gatewayReference($requestData),
            'gateway_payment_reference' => $this->firstScalarValue($response, ['transactionId', 'transaction_id', 'paymentId', 'payment_id'])
                ?? $this->firstScalarValue($requestData, ['transactionId', 'transaction_id', 'paymentId', 'payment_id']),
            'selected_payment_method' => $this->safePaymentMethodValue($requestData),
            'callback_status' => $this->firstScalarValue($requestData, ['paymentStatus', 'status', 'transactionStatus']),
            'verification_succeeded' => array_key_exists('success', $response)
                ? filter_var($response['success'], FILTER_VALIDATE_BOOLEAN)
                : null,
        ]);
    }

    private function safeKashierInvoiceContext(?Invoice $invoice): array
    {
        return [
            'invoice_id' => $invoice?->id,
            'invoice_pid' => $invoice?->pid,
            'invoice_status' => $invoice?->status,
            'invoice_category' => $invoice?->category,
        ];
    }

    private function firstScalarValue(array $data, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (!isset($data[$key]) || !is_scalar($data[$key])) {
                continue;
            }

            $value = trim((string) $data[$key]);

            if ($value !== '') {
                return mb_substr($value, 0, 150);
            }
        }

        return null;
    }

    private function safePaymentMethodValue(array $data): ?string
    {
        $value = $this->firstScalarValue($data, ['paymentMethod', 'payment_method', 'method', 'source', 'paymentSource']);

        if ($value === null || !preg_match('/\A[a-zA-Z][a-zA-Z0-9_-]{0,49}\z/', $value)) {
            return null;
        }

        return $value;
    }

    private function logKashierInvoiceCreated(Invoice $invoice, array $response, string $sourceMethods): void
    {
        logger('Kashier invoice created', array_merge($this->safeKashierInvoiceContext($invoice), [
            'merchant_order_id' => $invoice->pid,
            'gateway_payment_reference' => $this->gatewayReference($response),
            'selected_payment_method' => null,
            'allowed_payment_methods' => $sourceMethods,
            'redirect_target' => route(config('nafezly-payments.VERIFY_ROUTE_NAME'), ['payment' => 'kashier']),
        ]));
    }

    private function logKashierPaymentCreation(
        string $stage,
        Request $request,
        $user,
        $amount,
        $gatewayPercentage,
        $gatewayFee,
        $total,
        string $sourceMethods,
        ?array $response = null
    ): void {
        $webhookUrl = (string) config('nafezly-payments.KASHIER_WEBHOOK_URL');

        logger('Kashier payment creation ' . $stage, [
            'flow_type' => $this->getPaymentFlowTypeForLog($request),
            'base_amount' => $amount,
            'gateway_percentage' => $gatewayPercentage,
            'gateway_fee' => $gatewayFee,
            'final_total_sent_to_kashier' => round($total, 2),
            'payment_type' => $request->input('payment_type'),
            'book_id' => $request->input('book_id'),
            'lecture_id' => $request->input('lecture_id'),
            'test_id' => $request->input('test_id'),
            'course_id' => $request->input('course_id'),
            'live_id' => $request->input('live_id'),
            'user_id' => $user?->id,
            'returned_payment_id' => $response['payment_id'] ?? null,
            'html_exists' => isset($response['html']) && $response['html'] !== '',
            'session_url_exists' => isset($response['session_url']) && $response['session_url'] !== '',
            'source_methods' => $sourceMethods,
            'payment_creation_flow' => $this->usesKashierPaymentSessions() ? 'payment_sessions' : 'hosted_checkout',
            'kashier_mode' => config('nafezly-payments.KASHIER_MODE'),
            'kashier_currency' => config('nafezly-payments.KASHIER_CURRENCY'),
            'webhook_url_host' => parse_url($webhookUrl, PHP_URL_HOST),
            'redirect_route_name' => config('nafezly-payments.VERIFY_ROUTE_NAME'),
            'redirect_url' => route(config('nafezly-payments.VERIFY_ROUTE_NAME'), ['payment' => 'kashier']),
        ]);
    }

    private function getPaymentFlowTypeForLog(Request $request): string
    {
        if ($request->has('book_id')) {
            return 'book';
        }

        if ($request->has('live_id')) {
            return 'live';
        }

        if ($request->has('test_id') || in_array($request->input('payment_type'), ['single_test', 'course_tests'], true)) {
            return 'test';
        }

        if ($request->has('lecture_id')) {
            return 'lecture';
        }

        return 'unknown';
    }

    private function hasActiveBookAccess($user, Book $book): bool
    {
        return UserBook::query()
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->exists();
    }

    private function bookBelongsToStudentCourse(Book $book, $user, ?string $track): bool
    {
        if (! $book->course_id || ! $user?->level_id) {
            return false;
        }

        return Course::query()
            ->where('id', $book->course_id)
            ->where('level_id', $user->level_id)
            ->when($track, function ($query) use ($track) {
                $query->where('track_slug', $track);
            })
            ->exists();
    }

    private function grantBookAccessForPaidInvoice(Invoice $invoice): ?UserBook
    {
        if ($invoice->status !== 'paid' || $invoice->category !== 'book' || $invoice->type !== 'single') {
            return null;
        }

        $user = auth()->user();
        $book = Book::with('course')->find($invoice->type_value);

        if (! $user || ! $book || (int) $invoice->user_id !== (int) $user->id) {
            return null;
        }

        if ((int) $invoice->course_id !== (int) $book->course_id || ! $this->bookBelongsToStudentCourse($book, $user, null)) {
            logger()->warning('Paid book invoice failed access validation', [
                'invoice_id' => $invoice->id,
                'invoice_user_id' => $invoice->user_id,
                'book_id' => $book->id,
                'invoice_course_id' => $invoice->course_id,
                'book_course_id' => $book->course_id,
                'user_level_id' => $user->level_id,
            ]);

            return null;
        }

        $source = 'kashier_book:' . $invoice->id;
        $existingAccess = UserBook::query()
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        if ($existingAccess && $this->isActiveBookAccess($existingAccess)) {
            return $existingAccess->load('book');
        }

        if ($existingAccess && $existingAccess->source === $source) {
            return null;
        }

        return $this->grantBookAccess($user, $book, $source);
    }

    private function grantBookAccess($user, Book $book, string $source): UserBook
    {
        $startsAt = now();
        $expiresAt = $book->access_duration_days
            ? now()->addDays((int) $book->access_duration_days)
            : null;

        return UserBook::updateOrCreate(
            [
                'user_id' => $user->id,
                'book_id' => $book->id,
            ],
            [
                'source' => $source,
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'is_active' => true,
            ]
        )->load('book');
    }

    private function isActiveBookAccess(UserBook $userBook): bool
    {
        return $userBook->is_active
            && ($userBook->starts_at === null || $userBook->starts_at->lte(now()))
            && ($userBook->expires_at === null || $userBook->expires_at->gte(now()));
    }

    private function usesKashierPaymentSessions(): bool
    {
        return filter_var(
            config('nafezly-payments.KASHIER_USE_PAYMENT_SESSIONS', false),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    private function createKashierPaymentSession($user, $amount, string $description): array
    {
        return app(KashierPaymentSessionService::class)->create(
            (string) Str::uuid(),
            round((float) $amount, 2),
            $user,
            $description
        );
    }

    private function kashierSessionView(array $response)
    {
        return view('themes/default/back.users.payment.pay', [
            'sessionUrl' => $response['session_url'],
        ]);
    }

    private function configureKashierGatewayFromDatabase(): bool
    {
        $gateway = PaymentGateway::with('settings')
            ->where('name', 'kashier')
            ->first();

        $settings = $gateway
            ? $gateway->settings->pluck('value', 'key')
            : collect();

        $accountKey = trim((string) $settings->get('KASHIER_ACCOUNT_KEY'));
        $iframeKey = trim((string) $settings->get('KASHIER_IFRAME_KEY'));
        $token = trim((string) $settings->get('KASHIER_TOKEN'));
        $isActive = (bool) ($gateway?->status);

        logger('Kashier gateway configuration check', [
            'gateway_exists' => (bool) $gateway,
            'gateway_active' => $isActive,
            'account_key_present' => $accountKey !== '',
            'iframe_key_present' => $iframeKey !== '',
            'token_present' => $token !== '',
            'mode' => config('nafezly-payments.KASHIER_MODE'),
        ]);

        if (!$gateway || !$isActive || $accountKey === '' || $iframeKey === '' || $token === '') {
            return false;
        }

        config([
            'nafezly-payments.KASHIER_ACCOUNT_KEY' => $accountKey,
            'nafezly-payments.KASHIER_IFRAME_KEY' => $iframeKey,
            'nafezly-payments.KASHIER_TOKEN' => $token,
        ]);

        return true;
    }

    private function kashierGatewayUnavailableResponse()
    {
        logger('Kashier payment blocked because gateway is inactive or credentials are missing.');

        return redirect()->back()
            ->with('error', 'Payment gateway is not available. Please contact support.');
    }

}
