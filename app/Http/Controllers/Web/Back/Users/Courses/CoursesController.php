<?php

namespace App\Http\Controllers\Web\Back\Users\Courses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Course;
use App\Models\Level;
use App\Models\Lecture;
use App\Models\Invoice;
use App\Models\LectureAssignment;
use App\Models\StudentLectureAssignment;
use App\Models\StudentLectureAnswer;
use App\Services\PaymentService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class CoursesController extends Controller
{
    public function index(Request $request)
    {
        $track = $request->query('track');

        $coursesQuery = Course::where('level_id', auth()->user()->level_id)
            ->with(['level', 'lectures.assignments.studentAssignments'])
            ->withCount('lectures');

        if ($track) {
            $coursesQuery->where('track_slug', $track);
        }

        $allCourses = $coursesQuery->get();

        $courses = $allCourses
            ->where('lectures_count', '>', 0)
            ->sortByDesc('lectures_count')
            ->unique('track_slug')
            ->values();

        if (!$track && $courses->count() === 1 && $courses->first()->track_slug) {
            return redirect()->route('dashboard.users.courses', [
                'track' => $courses->first()->track_slug
            ]);
        }

        return view('themes/default/back.users.courses.courses-list', compact('courses', 'track'));
    }
    public function show(Request $request)
    {
        $course = Course::with(['level', 'lectures.assignments'])
            ->where('level_id', auth()->user()->level_id)
            ->findOrFail(decrypt($request->id));

        if ($request->ajax()) {
            $lectures = $course->lectures();

            // تطبيق الفلاتر
            if ($request->filled('type')) {
                $lectures->where('type', $request->type);
            }

            if ($request->filled('price_range')) {
                switch ($request->price_range) {
                    case 'free':
                        $lectures->where(function($query) {
                            $query->whereNull('price')->orWhere('price', 0);
                        });
                        break;
                    case 'paid':
                        $lectures->where('price', '>', 0);
                        break;
                }
            }

            if ($request->filled('date_from')) {
                $lectures->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $lectures->whereDate('created_at', '<=', $request->date_to);
            }

            $lectures = $lectures->with('assignments')->get();

            return DataTables::of($lectures)
                ->addIndexColumn()
                ->addColumn('name', function($row) {
                    return Str::limit($row->name, 50);
                })
                ->addColumn('type', function($row) {
                    $types = [
                        'free' => '<span class="badge bg-success">' . __('l.Free') . '</span>',
                        'price' => '<span class="badge bg-primary">' . __('l.Paid') . '</span>',
                        'month' => '<span class="badge bg-warning">' . __('l.Monthly') . '</span>',
                        'course' => '<span class="badge bg-info">' . __('l.Course') . '</span>'
                    ];
                    return $types[$row->type] ?? $row->type;
                })
                ->addColumn('price', function($row) {
                    if ($row->type === 'free') {
                        return __('l.Free');
                    }
                    if ($row->type === 'course') {
                        return '-';
                    }
                    if ($row->type === 'month') {
                        return $row->price ? $row->price . ' ' . __('l.Month') : '-';
                    }
                    return $row->price ? $row->price : '-';
                })
                ->addColumn('assignments_count', function($row) {
                    $count = $row->assignments->count();
                    return $count > 0 ? '<span class="badge bg-info">' . $count . ' ' . __('l.assignments') . '</span>' : '-';
                })
                ->addColumn('image', function($row) {
                    return $row->image ? '<img src="' . asset($row->image) . '" alt="lecture" width="50" height="50" class="rounded" />' : '<div class="text-muted"><i class="fas fa-image fa-2x"></i></div>';
                })
                ->editColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '-';
                })
                ->addColumn('action', function($row) {
                    return '<a class="btn btn-primary btn-sm" href="' . route('dashboard.users.courses-lectures-show', ['id' => encrypt($row->id)]) . '">
                                <i class="fas fa-eye me-1"></i>' . __('l.view_lecture') . '
                            </a>';
                })
                ->rawColumns(['name', 'type', 'price', 'assignments_count', 'image', 'action'])
                ->make(true);
        }

        return view('themes/default/back.users.courses.course-lectures', compact('course'));
    }

    public function lectureShow(Request $request)
    {
        $user = auth()->user();

        $lecture = Lecture::with([
                'course.level',
                'course.lectures.assignments.studentAssignments',
                'assignments.questions',
                'assignments.studentAssignments'
            ])
            ->findOrFail(decrypt($request->id));

        $courseLectures = $lecture->course->lectures
            ->sortBy('id')
            ->values();

        $currentLectureIndex = $courseLectures->search(function ($item) use ($lecture) {
            return $item->id === $lecture->id;
        });

        $previousLecture = $currentLectureIndex !== false && $currentLectureIndex > 0
            ? $courseLectures[$currentLectureIndex - 1]
            : null;

        $nextLecture = $currentLectureIndex !== false && $currentLectureIndex < ($courseLectures->count() - 1)
            ? $courseLectures[$currentLectureIndex + 1]
            : null;

        $completedLessons = $courseLectures->filter(function ($courseLecture) use ($user) {
            if (!$courseLecture->assignments || $courseLecture->assignments->count() === 0) {
                return false;
            }

            foreach ($courseLecture->assignments as $assignment) {
                $studentAssignment = $assignment->studentAssignments
                    ->where('student_id', $user->id)
                    ->first();

                if (!$studentAssignment || !$studentAssignment->submitted_at) {
                    return false;
                }
            }

            return true;
        })->count();

        $courseProgress = $courseLectures->count() > 0
            ? round(($completedLessons / $courseLectures->count()) * 100)
            : 0;

        $viewData = compact(
            'lecture',
            'courseLectures',
            'currentLectureIndex',
            'previousLecture',
            'nextLecture',
            'completedLessons',
            'courseProgress'
        );

        // هل مجانية
        if ($lecture->type == 'free') {
            return view('themes/default/back.users.courses.lecture-show', $viewData);
        // هل مدفوعة وقام بدفعها او قام بدفع الكورس بالكامل
        } elseif ($lecture->type == 'price') {
            if ($user->hasPaidLecture($lecture->id, $lecture->course_id) || $user->hasPurchasedCourseLectures($lecture->course_id)) {
                return view('themes/default/back.users.courses.lecture-show', $viewData);
            } else {
                return view('themes/default/back.users.courses.lecture-purchase', compact('lecture'));
            }
        // هل شهرية أو كورس وقام بشراء الكورس بالكامل
        } elseif ($lecture->type == 'month' || $lecture->type == 'course') {
            if ($user->hasPurchasedCourseLectures($lecture->course_id)) {
                return view('themes/default/back.users.courses.lecture-show', $viewData);
            } else {
                return view('themes/default/back.users.courses.lecture-purchase', compact('lecture'));
            }
        }
    }

    public function purchaseCourse(Request $request)
    {
        $user = auth()->user();

        if ($request->has('course_id')) {
            // شراء كورس كامل
            $courseId = decrypt($request->course_id);
            $course = Course::with('lectures')->findOrFail($courseId);

            // التحقق من أن المستخدم لم يشتري الكورس مسبقاً
            if ($user->hasPurchasedCourseLectures($courseId)) {
                return redirect()->route('dashboard.users.courses-lectures', ['id' => encrypt($courseId)])
                    ->with('info', __('l.already_purchased'));
            }

            // عرض صفحة الدفع للكورس
            return view('themes/default/back.users.courses.course-purchase', compact('course'));
        }

        return redirect()->route('dashboard.users.courses')->with('error', __('l.course_not_found'));
    }

    public function purchaseLecture(Request $request)
    {
        $user = auth()->user();
        // سيتم تطوير هذه الدالة لاحقاً
    }


    // ==================== الواجبات ====================

    public function startAssignment(Request $request)
    {
        $user = auth()->user();
        $assignment = LectureAssignment::with(['lecture.course', 'questions.options'])
            ->findOrFail(decrypt($request->id));

        // التحقق من الصلاحية
        if (!$this->canAccessAssignment($user, $assignment)) {
            return redirect()->back()->with('error', __('l.you_dont_have_permission_to_access_this_assignment'));
        }

        // التحقق من وجود واجب سابق
        $studentAssignment = StudentLectureAssignment::where('student_id', $user->id)
            ->where('lecture_assignment_id', $assignment->id)
            ->first();

        if ($studentAssignment && $studentAssignment->submitted_at) {
            return redirect()->route('dashboard.users.assignments-results', ['id' => encrypt($studentAssignment->id)]);
        }

        // إنشاء أو تحديث واجب الطالب
        if (!$studentAssignment) {
            $studentAssignment = StudentLectureAssignment::create([
                'student_id' => $user->id,
                'lecture_assignment_id' => $assignment->id,
                'started_at' => now(),
            ]);
        } elseif (!$studentAssignment->started_at) {
            $studentAssignment->update(['started_at' => now()]);
        }

        return redirect()->route('dashboard.users.assignments-take', ['id' => encrypt($studentAssignment->id)]);
    }

    public function takeAssignment(Request $request)
    {
        $user = auth()->user();
        $studentAssignment = StudentLectureAssignment::with(['lectureAssignment.questions.options', 'answers'])
            ->where('student_id', $user->id)
            ->findOrFail(decrypt($request->id));

        // التحقق من الصلاحية
        if (!$this->canAccessAssignment($user, $studentAssignment->lectureAssignment)) {
            return redirect()->back()->with('error', __('l.you_dont_have_permission_to_access_this_assignment'));
        }

        // التحقق من انتهاء الوقت
        if ($studentAssignment->isTimeExpired()) {
            return redirect()->route('dashboard.users.assignments-results', ['id' => encrypt($studentAssignment->id)]);
        }

        return view('themes/default/back.users.courses.assignment-take', compact('studentAssignment'));
    }

    public function submitAssignment(Request $request)
    {
        $user = auth()->user();
        $studentAssignment = StudentLectureAssignment::with(['lectureAssignment.questions.options'])
            ->where('student_id', $user->id)
            ->findOrFail(decrypt($request->id));

        // التحقق من الصلاحية
        if (!$this->canAccessAssignment($user, $studentAssignment->lectureAssignment)) {
            return response()->json(['error' => __('l.you_dont_have_permission_to_access_this_assignment')], 403);
        }

        // التحقق من انتهاء الوقت
        if ($studentAssignment->isTimeExpired()) {
            return response()->json(['error' => __('l.time_expired')], 400);
        }

        $answers = $request->input('answers', []);

        logger('Submit Assignment - Start Processing', [
            'assignment_id' => $studentAssignment->id,
            'user_id' => $user->id,
            'answers_received' => $answers
        ]);

        // حذف جميع الإجابات السابقة
        $studentAssignment->answers()->delete();

        $totalPoints = 0;
        $earnedPoints = 0;
        $correctAnswersCount = 0;

        foreach ($studentAssignment->lectureAssignment->questions as $question) {
            $totalPoints += $question->points;

            // البحث عن إجابة الطالب لهذا السؤال
            $studentAnswer = $answers[$question->id] ?? null;

            if ($studentAnswer === null || $studentAnswer === '') {
                // لا توجد إجابة - حفظ إجابة فارغة
                StudentLectureAnswer::create([
                    'student_lecture_assignment_id' => $studentAssignment->id,
                    'lecture_question_id' => $question->id,
                    'answer_text' => null,
                    'selected_option_id' => null,
                    'is_correct' => false,
                    'points_earned' => 0,
                ]);
                continue;
            }

            // تقييم الإجابة
            $evaluationResult = $this->evaluateAnswer($question, $studentAnswer);

            logger('Submit Assignment - Question Evaluation', [
                'question_id' => $question->id,
                'question_type' => $question->type,
                'student_answer' => $studentAnswer,
                'correct_answer' => $question->correct_answer,
                'is_correct' => $evaluationResult['is_correct'],
                'points_earned' => $evaluationResult['points_earned']
            ]);

            // حفظ الإجابة
            $answerData = [
                'student_lecture_assignment_id' => $studentAssignment->id,
                'lecture_question_id' => $question->id,
                'answer_text' => null,
                'selected_option_id' => null,
                'is_correct' => $evaluationResult['is_correct'],
                'points_earned' => $evaluationResult['points_earned'],
            ];

            // تحديد كيفية حفظ الإجابة حسب نوع السؤال
            if ($question->type === 'mcq') {
                $answerData['selected_option_id'] = $studentAnswer;
            } else {
                $answerData['answer_text'] = (string) $studentAnswer;
            }

            StudentLectureAnswer::create($answerData);

            // حساب النقاط
            if ($evaluationResult['is_correct']) {
                $earnedPoints += $evaluationResult['points_earned'];
                $correctAnswersCount++;
            }
        }

        // تحديث نتيجة الطالب
        $percentage = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;

        $studentAssignment->update([
            'submitted_at' => now(),
            'score' => $earnedPoints,
            'total_points' => $totalPoints,
            'percentage' => $percentage,
        ]);

        logger('Submit Assignment - Final Results', [
            'assignment_id' => $studentAssignment->id,
            'total_points' => $totalPoints,
            'earned_points' => $earnedPoints,
            'percentage' => $percentage,
            'correct_answers_count' => $correctAnswersCount,
            'total_questions' => $studentAssignment->lectureAssignment->questions->count()
        ]);

        return response()->json([
            'success' => true,
            'message' => __('l.assignment_submitted_successfully'),
            'redirect' => route('dashboard.users.assignments-results', ['id' => encrypt($studentAssignment->id)])
        ]);
    }

    public function saveAssignmentProgress(Request $request)
    {
        $user = auth()->user();
        $studentAssignment = StudentLectureAssignment::with(['lectureAssignment.questions'])
            ->where('student_id', $user->id)
            ->findOrFail(decrypt($request->id));

        // التحقق من الصلاحية
        if (!$this->canAccessAssignment($user, $studentAssignment->lectureAssignment)) {
            return response()->json(['error' => __('l.you_dont_have_permission_to_access_this_assignment')], 403);
        }

        // التحقق من انتهاء الوقت
        if ($studentAssignment->isTimeExpired()) {
            return response()->json(['error' => __('l.time_expired')], 400);
        }

        $answers = $request->input('answers', []);

        logger('Save Progress - Received answers', [
            'assignment_id' => $studentAssignment->id,
            'answers' => $answers
        ]);

        foreach ($answers as $questionId => $answer) {
            $question = $studentAssignment->lectureAssignment->questions->find($questionId);
            if (!$question) continue;

            // حذف الإجابة السابقة إن وجدت
            $studentAssignment->answers()->where('lecture_question_id', $questionId)->delete();

            // حفظ الإجابة الجديدة (بدون تقييم - سيتم التقييم عند التسليم)
            $answerData = [
                'student_lecture_assignment_id' => $studentAssignment->id,
                'lecture_question_id' => $questionId,
                'answer_text' => null,
                'selected_option_id' => null,
                'is_correct' => null, // سيتم تقييمها عند التسليم النهائي
                'points_earned' => 0,
            ];

            // تحديد كيفية حفظ الإجابة حسب نوع السؤال
            if ($question->type === 'mcq') {
                $answerData['selected_option_id'] = $answer;
            } else {
                $answerData['answer_text'] = (string) $answer;
            }

            StudentLectureAnswer::create($answerData);
        }

        return response()->json([
            'success' => true,
            'message' => __('l.progress_saved_successfully')
        ]);
    }

    public function assignmentResults(Request $request)
    {
        $user = auth()->user();
        $studentAssignment = StudentLectureAssignment::with([
            'lectureAssignment.questions.options',
            'answers.lectureQuestion',
            'answers.selectedOption'
        ])->where('student_id', $user->id)
            ->findOrFail(decrypt($request->id));

        // إعادة حساب النتائج إذا كانت غير صحيحة
        $this->recalculateAssignmentResults($studentAssignment);

        return view('themes/default/back.users.courses.assignment-results', compact('studentAssignment'));
    }

    /**
     * إعادة حساب نتائج الواجب إذا كانت غير صحيحة
     */
    private function recalculateAssignmentResults($studentAssignment)
    {
        $totalPoints = 0;
        $earnedPoints = 0;
        $correctAnswersCount = 0;
        $needsRecalculation = false;

        foreach ($studentAssignment->answers as $answer) {
            $question = $answer->lectureQuestion;
            $totalPoints += $question->points;

            // إعادة تقييم الإجابة
            if ($answer->selected_option_id) {
                // إجابة MCQ
                $studentAnswer = $answer->selected_option_id;
            } else {
                // إجابة TF, Numeric, Essay
                $studentAnswer = $answer->answer_text;
            }

            $evaluationResult = $this->evaluateAnswer($question, $studentAnswer);

            // التحقق من أن التقييم السابق صحيح
            if ($answer->is_correct !== $evaluationResult['is_correct'] ||
                $answer->points_earned !== $evaluationResult['points_earned']) {
                $needsRecalculation = true;

                // تحديث الإجابة
                $answer->update([
                    'is_correct' => $evaluationResult['is_correct'],
                    'points_earned' => $evaluationResult['points_earned']
                ]);
            }

            if ($evaluationResult['is_correct']) {
                $earnedPoints += $evaluationResult['points_earned'];
                $correctAnswersCount++;
            }
        }

        // تحديث نتيجة الواجب إذا لزم الأمر
        if ($needsRecalculation) {
            $percentage = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;

            $studentAssignment->update([
                'score' => $earnedPoints,
                'total_points' => $totalPoints,
                'percentage' => $percentage,
            ]);

            logger('Assignment Results Recalculated', [
                'assignment_id' => $studentAssignment->id,
                'total_points' => $totalPoints,
                'earned_points' => $earnedPoints,
                'percentage' => $percentage,
                'correct_answers_count' => $correctAnswersCount
            ]);
        }
    }

    private function canAccessAssignment($user, $assignment)
    {
        $lecture = $assignment->lecture;

        // التحقق من نوع المحاضرة والصلاحيات
        if ($lecture->type == 'free') {
            return true;
        } elseif ($lecture->type == 'price') {
            // للمحاضرات المدفوعة - التحقق من دفع المحاضرة أو شراء الكورس
            return $user->hasPaidLecture($lecture->id, $lecture->course_id) || $user->hasPurchasedCourseLectures($lecture->course_id);
        } elseif ($lecture->type == 'month' || $lecture->type == 'course') {
            // للمحاضرات الشهرية والكورس - التحقق من شراء الكورس بالكامل
            return $user->hasPurchasedCourseLectures($lecture->course_id);
        }

        return false;
    }

    /**
     * تقييم إجابة واحدة وإرجاع النتيجة
     */
    private function evaluateAnswer($question, $studentAnswer)
    {
        $isCorrect = false;
        $pointsEarned = 0;

        switch ($question->type) {
            case 'mcq':
                // للاختيار من متعدد - التحقق من الخيار الصحيح
                $correctOption = $question->options->where('is_correct', true)->first();
                $studentAnswerId = (int) $studentAnswer;
                $correctOptionId = $correctOption ? (int) $correctOption->id : null;
                $isCorrect = $correctOption && $correctOptionId === $studentAnswerId;
                $pointsEarned = $isCorrect ? $question->points : 0;

                logger('MCQ Evaluation Debug', [
                    'question_id' => $question->id,
                    'student_answer' => $studentAnswer,
                    'student_answer_int' => $studentAnswerId,
                    'correct_option_id' => $correctOptionId,
                    'all_options' => $question->options->map(function($opt) {
                        return [
                            'id' => $opt->id,
                            'text' => $opt->option_text,
                            'is_correct' => $opt->is_correct
                        ];
                    })->toArray(),
                    'comparison_result' => $isCorrect
                ]);
                break;

            case 'tf':
                // لصح وخطأ - مقارنة النص
                $isCorrect = strtolower(trim($studentAnswer)) === strtolower(trim($question->correct_answer));
                $pointsEarned = $isCorrect ? $question->points : 0;
                break;

            case 'numeric':
                // للإجابات الرقمية - استخدام التحويل الرقمي مع tolerance
                $studentNumeric = $this->evaluateNumericAnswer($studentAnswer);
                $correctNumeric = $this->evaluateNumericAnswer($question->correct_answer);

                if ($studentNumeric !== null && $correctNumeric !== null) {
                    $tolerance = 0.001;
                    $isCorrect = abs($studentNumeric - $correctNumeric) < $tolerance;
                    $pointsEarned = $isCorrect ? $question->points : 0;
                } else {
                    $isCorrect = false;
                    $pointsEarned = 0;
                }
                break;

            case 'essay':
                // للأسئلة المقالية - تحتاج تقييم يدوي
                $isCorrect = null; // سيتم تقييمها لاحقاً
                $pointsEarned = 0;
                break;

            default:
                $isCorrect = false;
                $pointsEarned = 0;
                break;
        }

        return [
            'is_correct' => $isCorrect,
            'points_earned' => $pointsEarned
        ];
    }

    private function evaluateNumericAnswer($answer)
    {
        if (empty($answer)) {
            return null;
        }

        // تنظيف الإجابة
        $cleanAnswer = preg_replace('/\s+/', '', trim($answer));

        logger('Evaluating numeric answer', [
            'original' => $answer,
            'cleaned' => $cleanAnswer
        ]);

        // التحقق من أن الإجابة رقم بسيط أولاً
        if (is_numeric($cleanAnswer)) {
            $result = (float) $cleanAnswer;
            if (is_finite($result)) {
                logger('Simple numeric answer', ['result' => $result]);
                return $result;
            }
        }

        // إذا لم تكن رقماً بسيطاً، تحقق من التعبيرات الرياضية
        return $this->evaluateMathExpression($cleanAnswer);
    }

    /**
     * تقييم التعبيرات الرياضية البسيطة بطريقة آمنة
     */
    private function evaluateMathExpression($expression)
    {
        // التحقق من الأمان - السماح فقط بالأرقام والعمليات الأساسية
        if (!preg_match('/^[0-9+\-*\/\.\(\)\s]+$/', $expression)) {
            logger('Invalid math expression format', ['expression' => $expression]);
            return null;
        }

        // منع التعبيرات المعقدة أو الخطيرة
        if (preg_match('/[a-zA-Z_$]|\/\*|\*\/|\/\/|function|eval|exec/', $expression)) {
            logger('Dangerous expression detected', ['expression' => $expression]);
            return null;
        }

        // تحويل العمليات إلى صيغة PHP آمنة
        $safeExpression = $this->makeMathExpressionSafe($expression);

        if ($safeExpression === null) {
            return null;
        }

        try {
            // استخدام مكتبة تقييم آمنة بدلاً من eval
            $result = $this->safeEvaluate($safeExpression);

            if (is_numeric($result) && is_finite($result)) {
                logger('Math expression evaluated', [
                    'expression' => $expression,
                    'safe_expression' => $safeExpression,
                    'result' => $result
                ]);
                return (float) $result;
            }

            return null;
        } catch (\Exception $e) {
            logger('Math expression evaluation failed', [
                'expression' => $expression,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * جعل التعبير الرياضي آمناً
     */
    private function makeMathExpressionSafe($expression)
    {
        // إزالة المسافات
        $clean = preg_replace('/\s+/', '', $expression);

        // التحقق من الأقواس المتوازنة
        if (substr_count($clean, '(') !== substr_count($clean, ')')) {
            return null;
        }

        // التحقق من عدم وجود عمليات متتالية
        if (preg_match('/[\+\-\*\/]{2,}/', $clean)) {
            return null;
        }

        // التحقق من بداية ونهاية صحيحة
        if (preg_match('/^[\+\*\/]|[\+\-\*\/]$/', $clean)) {
            return null;
        }

        return $clean;
    }

    /**
     * تقييم آمن للتعبيرات الرياضية
     */
    private function safeEvaluate($expression)
    {
        // قائمة العمليات المسموحة
        $allowedOperators = ['+', '-', '*', '/', '(', ')'];

        // تحليل التعبير إلى tokens
        $tokens = $this->tokenizeMathExpression($expression);

        if (empty($tokens)) {
            return null;
        }

        // تقييم باستخدام خوارزمية Shunting Yard Algorithm
        return $this->evaluateTokens($tokens);
    }

    /**
     * تحليل التعبير إلى tokens
     */
    private function tokenizeMathExpression($expression)
    {
        $tokens = [];
        $current = '';

        for ($i = 0; $i < strlen($expression); $i++) {
            $char = $expression[$i];

            if (is_numeric($char) || $char === '.') {
                $current .= $char;
            } else {
                if ($current !== '') {
                    $tokens[] = (float) $current;
                    $current = '';
                }

                if (in_array($char, ['+', '-', '*', '/', '(', ')'])) {
                    $tokens[] = $char;
                }
            }
        }

        if ($current !== '') {
            $tokens[] = (float) $current;
        }

        return $tokens;
    }

    /**
     * تقييم tokens باستخدام خوارزمية آمنة
     */
    private function evaluateTokens($tokens)
    {
        // تطبيق خوارزمية Shunting Yard Algorithm مبسطة
        $output = [];
        $operators = [];

        $precedence = ['+' => 1, '-' => 1, '*' => 2, '/' => 2];

        foreach ($tokens as $token) {
            if (is_numeric($token)) {
                $output[] = $token;
            } elseif ($token === '(') {
                $operators[] = $token;
            } elseif ($token === ')') {
                while (!empty($operators) && end($operators) !== '(') {
                    $output[] = array_pop($operators);
                }
                array_pop($operators); // إزالة (
            } elseif (isset($precedence[$token])) {
                while (!empty($operators) &&
                       end($operators) !== '(' &&
                       isset($precedence[end($operators)]) &&
                       $precedence[end($operators)] >= $precedence[$token]) {
                    $output[] = array_pop($operators);
                }
                $operators[] = $token;
            }
        }

        while (!empty($operators)) {
            $output[] = array_pop($operators);
        }

        // تقييم النتيجة من RPN
        return $this->evaluateRPN($output);
    }

    /**
     * تقييم Reverse Polish Notation
     */
    private function evaluateRPN($tokens)
    {
        $stack = [];

        foreach ($tokens as $token) {
            if (is_numeric($token)) {
                $stack[] = $token;
            } else {
                if (count($stack) < 2) {
                    throw new \Exception('Invalid expression');
                }

                $b = array_pop($stack);
                $a = array_pop($stack);

                switch ($token) {
                    case '+':
                        $stack[] = $a + $b;
                        break;
                    case '-':
                        $stack[] = $a - $b;
                        break;
                    case '*':
                        $stack[] = $a * $b;
                        break;
                    case '/':
                        if ($b == 0) {
                            throw new \Exception('Division by zero');
                        }
                        $stack[] = $a / $b;
                        break;
                    default:
                        throw new \Exception('Unknown operator');
                }
            }
        }

        if (count($stack) !== 1) {
            throw new \Exception('Invalid expression');
        }

        return $stack[0];
    }
}
