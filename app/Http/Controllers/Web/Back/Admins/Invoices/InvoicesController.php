<?php

namespace App\Http\Controllers\Web\Back\Admins\Invoices;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Course;
use App\Models\Test;
use App\Models\Lecture;
use App\Models\LectureAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class InvoicesController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show invoices')) {
            return view('themes/default/back.permission-denied');
        }

        if ($request->ajax()) {
            $invoices = Invoice::with(['student', 'course', 'lecture', 'test', 'book']);

            // تطبيق الفلاتر
            if ($request->filled('student_id')) {
                $invoices->where('user_id', $request->student_id);
            }

            if ($request->filled('category')) {
                $invoices->where('category', $request->category);
            }

            if ($request->filled('type')) {
                $invoices->where('type', $request->type);
            }

            if ($request->filled('status')) {
                $invoices->where('status', $request->status);
            }

            if ($request->filled('amount_range')) {
                switch ($request->amount_range) {
                    case 'low':
                        $invoices->whereBetween('amount', [0, 100]);
                        break;
                    case 'medium':
                        $invoices->whereBetween('amount', [101, 500]);
                        break;
                    case 'high':
                        $invoices->where('amount', '>', 500);
                        break;
                }
            }

            if ($request->filled('date_from')) {
                $invoices->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $invoices->whereDate('created_at', '<=', $request->date_to);
            }

            return DataTables::of($invoices)
                ->addIndexColumn()
                ->addColumn('student_name', function($row) {
                    return '<a href="'.route('dashboard.admins.users-show', ['id' => encrypt($row->student->id)]).'">'.($row->student ? $row->student->name : '-').'</a>';
                })
                ->addColumn('student_email', function($row) {
                    return $row->student ? $row->student->email : '-';
                })
                ->addColumn('course_name', function($row) {
                    return $row->course ? $row->course->name : '-';
                })
                ->addColumn('category_badge', function($row) {
                    $badgeClass = match($row->category) {
                        'quiz' => 'bg-warning',
                        'live' => 'bg-danger',
                        'book' => 'bg-primary',
                        default => 'bg-info',
                    };
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->category) . '</span>';
                })
                ->addColumn('type_badge', function($row) {
                    $badgeClass = match($row->type) {
                        'single' => 'bg-primary',
                        'month' => 'bg-success',
                        'course' => 'bg-secondary',
                        default => 'bg-light'
                    };
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->type) . '</span>';
                })
                ->addColumn('status_badge', function($row) {
                    $badgeClass = match($row->status) {
                        'pending' => 'bg-warning',
                        'paid' => 'bg-success',
                        'failed' => 'bg-danger',
                        default => 'bg-light'
                    };
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('amount_formatted', function($row) {
                    return '<span class="fw-bold">' . $row->amount . ' ' . __('l.currency') . '</span>';
                })
                ->addColumn('type_value_display', function($row) {
                    if ($row->type === 'course' && $row->course) {
                        return $row->course->name;
                    } elseif ($row->type === 'single' && $row->category === 'lecture' && $row->lecture) {
                        return $row->lecture->name;
                    } elseif ($row->type === 'single' && $row->category === 'quiz' && $row->test) {
                        return $row->test->name;
                    } elseif ($row->type === 'single' && $row->category === 'book' && $row->book) {
                        return $row->book->title;
                    } elseif ($row->type === 'month') {
                        $courseName = $row->course ? $row->course->name : 'عام';
                        return 'اشتراك شهري لكورس ' . $courseName . ' - ' . date('F Y', strtotime($row->type_value . '-01'));
                    }
                    return $row->type_value;
                })
                ->editColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '-';
                })
                ->addColumn('action', function($row) {
                    $actionBtn = view('themes.default.back.admins.invoices.action-buttons', ['row' => $row])->render();
                    return $actionBtn;
                })
                ->rawColumns(['action', 'category_badge', 'type_badge', 'status_badge', 'amount_formatted', 'student_name'])
                ->make(true);
        }

        return view('themes/default/back.admins.invoices.invoices-list');
    }

    public function getStudents(Request $request)
    {
        if (!Gate::allows('add invoices')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $search = $request->get('search', '');
        $students = User::where('min_role', 0)
            ->doesntHave('roles')
            ->where(function($query) use ($search) {
                $query->where('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->select('id', 'firstname', 'lastname', 'email')
            ->limit(20)
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->firstname . ' ' . $student->lastname,
                    'email' => $student->email
                ];
            });

        return response()->json($students);
    }

    public function getItems(Request $request)
    {
        if (!Gate::allows('add invoices')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $type = $request->get('type');
        $category = $request->get('category');
        $courseId = $request->get('course_id');
        $search = $request->get('search', '');

        // التحقق من صحة المعاملات
        if (!in_array($type, ['course', 'single'])) {
            return response()->json([]);
        }

        if ($type === 'single' && !in_array($category, ['quiz', 'lecture'])) {
            return response()->json([]);
        }

        $items = [];

        if ($type === 'course') {
            $items = Course::where('name', 'like', "%{$search}%")
                ->select('id', 'name')
                ->limit(20)
                ->get()
                ->map(function($item) {
                    return ['id' => $item->id, 'text' => $item->name];
                });
        } elseif ($type === 'single') {
            // للفواتير الفردية، جلب المحاضرات/الكويزات حسب الكورس المختار
            if (!$courseId) {
                return response()->json([]);
            }

            if ($category === 'lecture') {
                $items = Lecture::where('course_id', $courseId)
                    ->where('name', 'like', "%{$search}%")
                    ->select('id', 'name')
                    ->limit(20)
                    ->get()
                    ->map(function($item) {
                        return ['id' => $item->id, 'text' => $item->name];
                    });
            } elseif ($category === 'quiz') {
                $items = Test::where('course_id', $courseId)
                    ->where('name', 'like', "%{$search}%")
                    ->select('id', 'name')
                    ->limit(20)
                    ->get()
                    ->map(function($item) {
                        return ['id' => $item->id, 'text' => $item->name];
                    });
            }
        }

        return response()->json($items);
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add invoices')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category' => 'required|in:quiz,lecture',
            'type' => 'required|in:single,course',
            'type_value' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'amount' => 'required|numeric|min:0',
        ], [
            'user_id.required' => 'Please select a student.',
            'user_id.exists' => 'Selected student does not exist.',
            'category.required' => 'Please select a category.',
            'category.in' => 'Invalid category selected.',
            'type.required' => 'Please select a type.',
            'type.in' => 'Invalid type selected.',
            'type_value.required' => 'Please select an item.',
            'amount.required' => 'Please enter an amount.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be greater than or equal to 0.',
            'course_id.required' => 'Please select a course.',
            'course_id.exists' => 'Selected course does not exist.',
        ]);

        // التحقق من صحة type_value حسب النوع
        if ($request->type === 'course') {
            $course = Course::find($request->type_value);
            if (!$course) {
                return redirect()->back()->withErrors(['type_value' => 'Course not found'])->withInput();
            }
        } elseif ($request->type === 'single') {
            if ($request->category === 'lecture') {
                $lecture = Lecture::find($request->type_value);
                if (!$lecture) {
                    return redirect()->back()->withErrors(['type_value' => 'Lecture not found'])->withInput();
                }
            } elseif ($request->category === 'quiz') {
                $test = Test::find($request->type_value);
                if (!$test) {
                    return redirect()->back()->withErrors(['type_value' => 'Test not found'])->withInput();
                }
            }
        }

        // التحقق من عدم وجود فاتورة مكررة
        $existingInvoiceQuery = Invoice::where('user_id', $request->user_id)
            ->where('category', $request->category)
            ->where('type', $request->type)
            ->where('type_value', $request->type_value)
            ->where('status', 'paid');

        // إضافة شرط course_id لجميع الأنواع
        if ($request->course_id) {
            $existingInvoiceQuery->where('course_id', $request->course_id);
        }

        $existingInvoice = $existingInvoiceQuery->first();

        if ($existingInvoice) {
            return redirect()->back()->withErrors(['type_value' => 'Invoice already exists for this item'])->withInput();
        }

        $invoiceData = [
            'user_id' => $request->user_id,
            'category' => $request->category,
            'type' => $request->type,
            'type_value' => $request->type_value,
            'amount' => $request->amount,
            'status' => 'paid',
        ];

        // إضافة course_id لجميع الأنواع
        if ($request->course_id) {
            $invoiceData['course_id'] = $request->course_id;
        }

        Invoice::create($invoiceData);

        // إذا كان الطلب من صفحة تفاصيل الطالب، ارجع إلى نفس الصفحة
        if ($request->has('from_student_page')) {
            return redirect()->route('dashboard.admins.customers-show', ['id' => encrypt($request->user_id)])->with('success', 'Invoice added successfully.');
        }

        return redirect()->back()->with('success', 'Invoice added successfully.');
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete invoices')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $invoice = Invoice::find($id);

        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice does not exist.');
        }

        $invoice->delete();

        return redirect()->back()->with('success', 'Invoice deleted successfully');
    }

    public function deleteSelected(Request $request)
    {
        if (!Gate::allows('delete invoices')) {
            return view('themes/default/back.permission-denied');
        }

        $ids = $request->ids;
        if (!empty($ids)) {
            foreach ($ids as $encryptedId) {
                $id = decrypt($encryptedId);
                $invoice = Invoice::find($id);
                if ($invoice) {
                    $invoice->delete();
                }
            }
        }

        return redirect()->back()->with('success', 'Selected invoices deleted successfully.');
    }

    public function show(Request $request)
    {
        if (!Gate::allows('show invoices')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $invoice = Invoice::with(['student', 'course', 'lecture', 'test', 'book'])->find($id);

        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice not found.');
        }

        return view('themes/default/back.admins.invoices.show-invoice', compact('invoice'));
    }

    public function studentInvoices(Request $request)
    {
        if (!Gate::allows('show invoices')) {
            return view('themes/default/back.permission-denied');
        }

        $studentId = $request->student_id;
        $student = User::findOrFail($studentId);

        return view('themes/default/back.admins.invoices.student-invoices', compact('student'));
    }
}
