<?php

namespace App\Http\Controllers\Web\Back\Admins\Tests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Test;
use App\Models\Course;
use App\Models\TestQuestion;
use App\Models\TestQuestionOption;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestsController extends Controller
{
    /*========================
     *  INDEX (LIST PAGE)
     *========================*/
    public function index(Request $request)
    {
        if (!Gate::allows('show tests')) {
            return view('themes/default/back.permission-denied');
        }

        if ($request->ajax()) {
            $tests = Test::with(['course'])
                ->select([
                    'id',
                    'name',
                    'description',
                    'course_id',
                    'price',
                    'total_score',
                    'initial_score',
                    'is_active',
                    'created_at',
                    'part1_questions_count',
                    'part2_questions_count',
                    'part3_questions_count',
                    'part4_questions_count',
                    'part5_questions_count',
                ])
                ->orderBy('id', 'asc');

            if ($request->filled('course_id')) {
                $tests->where('course_id', $request->course_id);
            }

            if ($request->filled('status') && $request->status !== '') {
                $tests->where('is_active', $request->status);
            }

            if ($request->filled('price_type')) {
                if ($request->price_type === 'free') {
                    $tests->where('price', 0);
                } elseif ($request->price_type === 'paid') {
                    $tests->where('price', '>', 0);
                }
            }

            return DataTables::of($tests)
                ->addIndexColumn()
                ->addColumn('course_name', function ($test) {
                    return $test->course->name ?? '';
                })
                ->addColumn('price_formatted', function ($test) {
                    if ($test->price > 0) {
                        return number_format($test->price, 2) . ' ' . __('l.currency');
                    }
                    return '<span class="text-muted">' . __('l.free') . '</span>';
                })
                ->addColumn('questions_status', function ($test) {
                    $totalQuestions = $test->questions()->count();

                    $expectedQuestions =
                        (int) $test->part1_questions_count +
                        (int) $test->part2_questions_count +
                        (int) $test->part3_questions_count +
                        (int) $test->part4_questions_count +
                        (int) $test->part5_questions_count;

                    if ($expectedQuestions <= 0) {
                        return '<span class="badge bg-secondary">' . __('l.no_structure_defined') . '</span>';
                    }

                    if ($totalQuestions >= $expectedQuestions) {
                        return '<span class="badge bg-success">' . __('l.complete') . '</span>';
                    }

                    return '<span class="badge bg-warning">' . __('l.incomplete') . '</span> (' .
                        $totalQuestions . '/' . $expectedQuestions . ')';
                })
                ->addColumn('students_count', function ($test) {
                    return $test->studentTests()->count();
                })
                ->addColumn('status', function ($test) {
                    $checked = $test->is_active ? 'checked' : '';
                    return '<div class="form-check form-switch">
                                <input class="form-check-input status-toggle" type="checkbox"
                                       data-id="' . $test->id . '" ' . $checked . '>
                            </div>';
                })
                ->addColumn('action', function ($test) {
                    $buttons = '<div class="btn-group" role="group">';

                    $buttons .= '<a href="' . route('dashboard.admins.tests-show', ['id' => encrypt($test->id)]) . '"
                                   class="btn btn-sm btn-info" title="' . __('l.View') . '">
                                   <i class="fas fa-eye"></i>
                                </a>';

                    $buttons .= '<a href="' . route('dashboard.admins.tests-questions', ['test_id' => encrypt($test->id)]) . '"
                                   class="btn btn-sm btn-primary" title="' . __('l.questions') . '">
                                   <i class="fas fa-question-circle"></i>
                                </a>';

                    $buttons .= '<a href="' . route('dashboard.admins.tests-edit', ['id' => encrypt($test->id)]) . '"
                                   class="btn btn-sm btn-warning" title="' . __('l.Edit') . '">
                                   <i class="fas fa-edit"></i>
                                </a>';

                    $buttons .= '<a href="' . route('dashboard.admins.tests-results-print', ['id' => encrypt($test->id)]) . '"
                                   class="btn btn-sm btn-secondary" target="_blank" title="Print Results">
                                   <i class="fas fa-print"></i>
                                </a>';

                    if (!$test->studentTests()->exists()) {
                        $buttons .= '<a href="' . route('dashboard.admins.tests-delete', ['id' => encrypt($test->id)]) . '"
                                       class="btn btn-sm btn-danger delete-record" title="' . __('l.delete') . '">
                                       <i class="fas fa-trash"></i>
                                    </a>';
                    }

                    $buttons .= '</div>';
                    return $buttons;
                })
                ->rawColumns(['price_formatted', 'questions_status', 'status', 'action'])
                ->make(true);
        }

        $courses = Course::orderBy('name')->get();
        return view('themes.default.back.admins.tests.index', compact('courses'));
    }

    /*========================
     *  STORE
     *========================*/
    public function store(Request $request)
    {
        if (!Gate::allows('add tests')) {
            return redirect()->back()->with('error', __('l.permission_denied'));
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tests')->where(function ($query) use ($request) {
                    return $query->where('course_id', $request->course_id);
                })
            ],
            'description'            => 'nullable|string',
            'course_id'              => 'required|exists:courses,id',
            'price'                  => 'required|numeric|min:0',
            'total_score'            => 'required|integer|min:1|max:1000',
            'initial_score'          => 'required|integer|min:0|max:800',
            'default_question_score' => 'required|integer|min:1|max:100',

            'part1_questions_count'  => 'required|integer|min:1|max:100',
            'part1_time_minutes'     => 'required|integer|min:1|max:300',

            'part2_questions_count'  => 'nullable|integer|min:0|max:100',
            'part2_time_minutes'     => 'nullable|integer|min:0|max:300',

            'part3_questions_count'  => 'nullable|integer|min:0|max:100',
            'part3_time_minutes'     => 'nullable|integer|min:0|max:300',

            'part4_questions_count'  => 'nullable|integer|min:0|max:100',
            'part4_time_minutes'     => 'nullable|integer|min:0|max:300',

            'part5_questions_count'  => 'nullable|integer|min:0|max:100',
            'part5_time_minutes'     => 'nullable|integer|min:0|max:300',

            'break_time_minutes'     => 'nullable|integer|min:0|max:60',
            'max_attempts'           => 'required|integer|min:1|max:10',
            'is_active'              => 'nullable|boolean',
        ], [
            'name.unique'        => __('l.test_name_exists_in_course'),
            'course_id.required' => __('l.course_required'),
            'price.required'     => __('l.price_required'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', __('l.validation_error'));
        }

        try {
            $data = $validator->validated();

            foreach ([2, 3, 4, 5] as $i) {
                $data["part{$i}_questions_count"] = $data["part{$i}_questions_count"] ?? 0;
                $data["part{$i}_time_minutes"]    = $data["part{$i}_time_minutes"] ?? 0;
            }

            $data['break_time_minutes'] = $data['break_time_minutes'] ?? 0;
            $data['is_active']          = $request->has('is_active') ? 1 : 0;

            Test::create($data);

            return redirect()->back()->with('success', __('l.test_created_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('l.error_occurred'))
                ->withInput();
        }
    }

    /*========================
     *  EDIT
     *========================*/
    public function edit(Request $request)
    {
        if (!Gate::allows('edit tests')) {
            return view('themes/default/back.permission-denied');
        }

        $test = Test::with('course')->find(decrypt($request->id));
        if (!$test) {
            return redirect()->back()->with('error', __('l.test_not_found'));
        }

        $courses = Course::orderBy('name')->get();
        return view('themes.default.back.admins.tests.edit', compact('test', 'courses'));
    }

    /*========================
     *  UPDATE
     *========================*/
    public function update(Request $request)
    {
        if (!Gate::allows('edit tests')) {
            return redirect()->back()->with('error', __('l.permission_denied'));
        }

        $test = Test::find(decrypt($request->id));
        if (!$test) {
            return redirect()->back()->with('error', __('l.test_not_found'));
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tests')->where(function ($query) use ($request) {
                    return $query->where('course_id', $request->course_id);
                })->ignore($test->id)
            ],
            'description'            => 'nullable|string',
            'course_id'              => 'required|exists:courses,id',
            'price'                  => 'required|numeric|min:0',

            'total_score'            => 'required|integer|min:1|max:1000',
            'initial_score'          => 'required|integer|min:0|max:800',
            'default_question_score' => 'required|integer|min:1|max:100',

            'part1_questions_count'  => 'required|integer|min:1|max:100',
            'part1_time_minutes'     => 'required|integer|min:1|max:300',

            'part2_questions_count'  => 'nullable|integer|min:0|max:100',
            'part2_time_minutes'     => 'nullable|integer|min:0|max:300',

            'part3_questions_count'  => 'nullable|integer|min:0|max:100',
            'part3_time_minutes'     => 'nullable|integer|min:0|max:300',

            'part4_questions_count'  => 'nullable|integer|min:0|max:100',
            'part4_time_minutes'     => 'nullable|integer|min:0|max:300',

            'part5_questions_count'  => 'nullable|integer|min:0|max:100',
            'part5_time_minutes'     => 'nullable|integer|min:0|max:300',

            'break_time_minutes'     => 'nullable|integer|min:0|max:60',
            'max_attempts'           => 'required|integer|min:1|max:10',
            'is_active'              => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', __('l.validation_error'));
        }

        try {
            $data = $validator->validated();

            foreach ([2, 3, 4, 5] as $i) {
                $data["part{$i}_questions_count"] = $data["part{$i}_questions_count"] ?? 0;
                $data["part{$i}_time_minutes"]    = $data["part{$i}_time_minutes"] ?? 0;
            }

            $data['break_time_minutes'] = $data['break_time_minutes'] ?? 0;
            $data['is_active']          = $request->has('is_active') ? 1 : 0;

            $test->update($data);

            return redirect()->back()->with('success', __('l.test_updated_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('l.error_occurred'))
                ->withInput();
        }
    }

    /*========================
     *  SHOW
     *========================*/
    public function show(Request $request)
    {
        if (!Gate::allows('show tests')) {
            return view('themes/default/back.permission-denied');
        }

        $test = Test::with(['course', 'questions.options', 'studentTests'])
            ->find(decrypt($request->id));

        if (!$test) {
            return redirect()->back()->with('error', __('l.test_not_found'));
        }

        $moduleStats   = [];
        $expectedTotal = 0;
        $totalTime     = 0;

        for ($i = 1; $i <= 5; $i++) {
            $partKey = 'part' . $i;

            $questionsCount = $test->questions()
                ->where('part', $partKey)
                ->count();

            $expected      = (int) $test->{"part{$i}_questions_count"};
            $timeForPart   = (int) $test->{"part{$i}_time_minutes"};
            $expectedTotal += $expected;
            $totalTime     += $timeForPart;

            $moduleStats[$i] = [
                'questions' => $questionsCount,
                'expected'  => $expected,
                'complete'  => $expected > 0 ? $questionsCount >= $expected : true,
                'time'      => $timeForPart,
            ];
        }

        $totalQuestions = array_sum(array_column($moduleStats, 'questions'));

        $stats = [
            'total_questions'    => $totalQuestions,
            'expected_questions' => $expectedTotal,
            'part1_questions'    => $moduleStats[1]['questions'],
            'part2_questions'    => $moduleStats[2]['questions'],
            'part3_questions'    => $moduleStats[3]['questions'],
            'part4_questions'    => $moduleStats[4]['questions'],
            'part5_questions'    => $moduleStats[5]['questions'],
            'total_students'     => $test->studentTests()->count(),
            'completed_students' => $test->completedStudentTests()->count(),
            'average_score'      => $test->completedStudentTests()->avg('final_score') ?: 0,
            'highest_score'      => $test->completedStudentTests()->max('final_score') ?: 0,
            'lowest_score'       => $test->completedStudentTests()->min('final_score') ?: 0,
            'total_time'         => $totalTime,
            'break_time'         => (int) $test->break_time_minutes,
        ];

        $questionStatus = [
            'part1_complete' => $moduleStats[1]['complete'],
            'part2_complete' => $moduleStats[2]['complete'],
            'part3_complete' => $moduleStats[3]['complete'],
            'part4_complete' => $moduleStats[4]['complete'],
            'part5_complete' => $moduleStats[5]['complete'],
            'all_complete'   => $expectedTotal > 0 ? ($totalQuestions >= $expectedTotal) : true,
        ];

        return view('themes.default.back.admins.tests.show', compact(
            'test',
            'stats',
            'questionStatus',
            'moduleStats'
        ));
    }

    /*========================
     *  DELETE
     *========================*/
    public function delete(Request $request)
    {
        if (!Gate::allows('delete tests')) {
            return redirect()->back()->with('error', __('l.permission_denied'));
        }

        $test = Test::find(decrypt($request->id));
        if (!$test) {
            return redirect()->back()->with('error', __('l.test_not_found'));
        }

        if ($test->studentTests()->exists()) {
            return redirect()->back()
                ->with('error', __('l.cannot_delete_test_with_students'));
        }

        try {
            $testName = $test->name;
            $test->delete();

            return redirect()->back()->with('success', __('l.test_deleted_successfully', ['name' => $testName]));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('l.error_occurred'));
        }
    }

    /*========================
     *  TOGGLE STATUS (AJAX)
     *========================*/
    public function toggleStatus(Request $request)
    {
        if (!Gate::allows('edit tests')) {
            return response()->json(['success' => false, 'message' => __('l.permission_denied')]);
        }

        $test = Test::find($request->id);
        if (!$test) {
            return response()->json(['success' => false, 'message' => __('l.test_not_found')]);
        }

        try {
            $test->update(['is_active' => !$test->is_active]);

            $message = $test->is_active ? __('l.test_activated') : __('l.test_deactivated');

            return response()->json([
                'success'    => true,
                'message'    => $message,
                'new_status' => $test->is_active,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => __('l.error_occurred')]);
        }
    }

    /*========================
     *  PREVIEW (ADMIN VIEW)
     *========================*/
    public function preview(Request $request, $id = null)
    {
        if (!Gate::allows('show tests')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId = $id ?: $request->query('id');
        if (!$encryptedId) {
            abort(404);
        }

        try {
            $testId = decrypt($encryptedId);
        } catch (\Throwable $e) {
            abort(404);
        }

        $test = Test::with(['course', 'questions.options'])->findOrFail($testId);

        $modulesQuestions    = [];
        $totalQuestionsAdded = 0;
        $partsStats          = [];
        $totalTime           = 0;

        for ($partNumber = 1; $partNumber <= 5; $partNumber++) {
            $partKey = 'part' . $partNumber;

            $questions = $test->questions()
                ->where('part', $partKey)
                ->orderBy('question_order')
                ->get();

            $modulesQuestions[$partNumber] = $questions;
            $totalQuestionsAdded += $questions->count();

            $countField  = $partKey . '_questions_count';
            $timeField   = $partKey . '_time_minutes';

            $expected    = (int) $test->$countField;
            $timeMinutes = (int) $test->$timeField;
            $pointsSum   = (int) $questions->sum('score');

            $partsStats[$partNumber] = [
                'expected_count' => $expected,
                'time_minutes'   => $timeMinutes,
                'points_sum'     => $pointsSum,
            ];

            $totalTime += $timeMinutes;
        }

        return view('themes.default.back.admins.tests.preview', compact(
            'test',
            'modulesQuestions',
            'totalQuestionsAdded',
            'partsStats',
            'totalTime'
        ));
    }

    public function printResults($id)
    {
        if (!Gate::allows('show tests')) {
            return view('themes/default/back.permission-denied');
        }

        $testId = decrypt($id);

        $lastAttemptsSql = DB::table('student_tests')
            ->select('student_id', 'test_id', DB::raw('MAX(attempt_number) AS max_attempt'))
            ->where('test_id', $testId)
            ->groupBy('student_id', 'test_id');

        $rows = DB::table('student_tests as st')
            ->joinSub($lastAttemptsSql, 'last_attempts', function ($join) {
                $join->on('last_attempts.student_id', '=', 'st.student_id')
                    ->on('last_attempts.test_id', '=', 'st.test_id')
                    ->on('last_attempts.max_attempt', '=', 'st.attempt_number');
            })
            ->join('users as u', 'u.id', '=', 'st.student_id')
            ->join('tests as t', 't.id', '=', 'st.test_id')
            ->join('courses as c', 'c.id', '=', 't.course_id')
            ->leftJoin('student_test_answers as sta', 'sta.student_test_id', '=', 'st.id')
            ->where('st.test_id', $testId)
            ->select([
                DB::raw("CONCAT(u.firstname,' ',u.lastname) as student_name"),
                'u.email',
                'c.name as course_name',
                't.name as test_name',
                'st.attempt_number as last_attempt',
                'st.status',
                'st.final_score',
                't.total_score as test_total_score',
                'st.started_at',
                DB::raw('SUM(CASE WHEN sta.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
                DB::raw('SUM(CASE WHEN sta.is_correct = 0 THEN 1 ELSE 0 END) as wrong_answers'),
            ])
            ->groupBy(
                'u.id',
                'u.firstname',
                'u.lastname',
                'u.email',
                'c.name',
                't.name',
                'st.attempt_number',
                'st.status',
                'st.final_score',
                't.total_score',
                'st.started_at'
            )
            ->orderBy('student_name')
            ->get();

        return view('themes.default.back.admins.tests.print', compact('rows'));
    }

    /* ========================
     *  QUESTIONS MANAGEMENT
     * ======================== */

    /**
     * عرض صفحة إدارة الأسئلة
     */
    public function questions(Request $request)
    {
        if (!Gate::allows('edit tests')) {
            return view('themes/default/back.permission-denied');
        }

        $testId = decrypt($request->test_id);
        $test = Test::with(['course'])->findOrFail($testId);

        $questions = TestQuestion::where('test_id', $testId)
            ->with('options')
            ->orderBy('part')
            ->orderBy('question_order')
            ->get();

        return view('themes.default.back.admins.tests.questions', compact('test', 'questions'));
    }

    /**
     * تحديث السؤال (مع إمكانية حذف الصور)
     */
    public function updateQuestion(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:test_questions,id',
            'test_id' => 'required|exists:tests,id',
            'question_text' => 'required|string',
            'part' => 'required|string',
            'score' => 'required|integer|min:1',
            'type' => 'required|in:mcq,tf,numeric',
            'correct_answer' => 'nullable|string',
            'difficulty' => 'nullable|string',
            'content' => 'nullable|string',
            'explanation' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        $question = TestQuestion::findOrFail($request->id);

        // تحديث البيانات الأساسية
        $question->question_text = $request->question_text;
        $question->part = $request->part;
        $question->score = $request->score;
        $question->type = $request->type;
        $question->difficulty = $request->difficulty;
        $question->content = $request->content;
        $question->explanation = $request->explanation;

        // معالجة صورة السؤال
        if ($request->has('remove_question_image') && $request->remove_question_image == '1') {
            if ($question->question_image && file_exists(public_path($question->question_image))) {
                unlink(public_path($question->question_image));
            }
            $question->question_image = null;
        }

        if ($request->hasFile('question_image')) {
            if ($question->question_image && file_exists(public_path($question->question_image))) {
                unlink(public_path($question->question_image));
            }
            
            $image = $request->file('question_image');
            $imageName = time() . '_question_' . $question->id . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/questions', $imageName, 'public');
            $question->question_image = 'storage/' . $path;
        }

        // معالجة صورة الشرح
        if ($request->has('remove_explanation_image') && $request->remove_explanation_image == '1') {
            if ($question->explanation_image && file_exists(public_path($question->explanation_image))) {
                unlink(public_path($question->explanation_image));
            }
            $question->explanation_image = null;
        }

        if ($request->hasFile('explanation_image')) {
            if ($question->explanation_image && file_exists(public_path($question->explanation_image))) {
                unlink(public_path($question->explanation_image));
            }
            
            $image = $request->file('explanation_image');
            $imageName = time() . '_explanation_' . $question->id . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/explanation', $imageName, 'public');
            $question->explanation_image = 'storage/' . $path;
        }

        // معالجة الإجابة حسب نوع السؤال
        if ($request->type === 'tf') {
            $question->correct_answer = $request->correct_answer === '1' ? 'true' : 'false';
        } elseif ($request->type === 'numeric') {
            $question->correct_answer = $request->correct_answer;
        }

        $question->save();

        // ==================== معالجة خيارات MCQ مع حذف الصور ====================
        if ($request->type === 'mcq') {
            
            // ========== الخطوة 1: حذف صور الخيارات المحددة ==========
            if ($request->has('remove_option_image')) {
                foreach ($request->remove_option_image as $optionId => $shouldRemove) {
                    if ($shouldRemove == '1') {
                        // البحث عن الخيار بواسطة المعرف
                        $option = TestQuestionOption::find($optionId);
                        if ($option) {
                            if ($option->option_image && file_exists(public_path($option->option_image))) {
                                unlink(public_path($option->option_image));
                                Log::info('Deleted option image: ' . $option->option_image);
                            }
                            $option->option_image = null;
                            $option->save();
                        }
                    }
                }
            }
            // ==========================================================
            
            // جلب الخيارات الحالية
            $existingOptions = $question->options;
            
            // معالجة الخيارات الجديدة
            if ($request->has('options')) {
                // حذف الخيارات القديمة مع صورها (التي لم يتم تحديثها)
                foreach ($existingOptions as $oldOption) {
                    // التحقق مما إذا كان هذا الخيار قد تم تحديثه في الطلب
                    $isUpdated = false;
                    foreach ($request->options as $newOption) {
                        if (isset($newOption['id']) && $newOption['id'] == $oldOption->id) {
                            $isUpdated = true;
                            break;
                        }
                    }
                    
                    // إذا لم يتم تحديث الخيار، احذفه مع صورته
                    if (!$isUpdated) {
                        if ($oldOption->option_image && file_exists(public_path($oldOption->option_image))) {
                            unlink(public_path($oldOption->option_image));
                        }
                        $oldOption->delete();
                    }
                }

                // إضافة أو تحديث الخيارات
                foreach ($request->options as $index => $optionData) {
                    $optionId = $optionData['id'] ?? null;
                    
                    if ($optionId && ($existingOption = TestQuestionOption::find($optionId))) {
                        // تحديث خيار موجود
                        $existingOption->option_text = $optionData['option_text'] ?? '';
                        $existingOption->is_correct = isset($optionData['is_correct']) && $optionData['is_correct'] == '1' ? 1 : 0;
                        
                        // رفع صورة جديدة للخيار إذا وجدت
                        if (isset($optionData['option_image']) && $optionData['option_image'] instanceof \Illuminate\Http\UploadedFile) {
                            // حذف الصورة القديمة
                            if ($existingOption->option_image && file_exists(public_path($existingOption->option_image))) {
                                unlink(public_path($existingOption->option_image));
                            }
                            
                            $image = $optionData['option_image'];
                            $imageName = time() . '_option_' . $question->id . '_' . $index . '.' . $image->getClientOriginalExtension();
                            $path = $image->storeAs('uploads/options', $imageName, 'public');
                            $existingOption->option_image = 'storage/' . $path;
                        }
                        
                        $existingOption->save();
                    } else {
                        // إضافة خيار جديد
                        $option = new TestQuestionOption();
                        $option->test_question_id = $question->id;
                        $option->option_text = $optionData['option_text'] ?? '';
                        $option->is_correct = isset($optionData['is_correct']) && $optionData['is_correct'] == '1' ? 1 : 0;
                        
                        if (isset($optionData['option_image']) && $optionData['option_image'] instanceof \Illuminate\Http\UploadedFile) {
                            $image = $optionData['option_image'];
                            $imageName = time() . '_option_' . $question->id . '_' . $index . '.' . $image->getClientOriginalExtension();
                            $path = $image->storeAs('uploads/options', $imageName, 'public');
                            $option->option_image = 'storage/' . $path;
                        }
                        
                        $option->save();
                    }
                }
            }
        }
        // ====================================================================

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => __('l.question_updated_successfully'),
            'question' => $question->fresh(['options'])
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating question: ' . $e->getMessage(), [
            'request' => $request->all(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error updating question: ' . $e->getMessage()
        ], 500);
    }
}
    
    //  * حذف السؤال
    //  */
    public function deleteQuestion(Request $request)
    {
        try {
            $questionId = $request->input('question_id');
            
            $question = TestQuestion::findOrFail($questionId);
            $testId = $question->test_id;
            
            DB::beginTransaction();
            
            // حذف صورة السؤال
            if ($question->question_image && file_exists(public_path($question->question_image))) {
                unlink(public_path($question->question_image));
            }
            
            // حذف صورة الشرح
            if ($question->explanation_image && file_exists(public_path($question->explanation_image))) {
                unlink(public_path($question->explanation_image));
            }
            
            // حذف الخيارات والصور المرتبطة بها
            foreach ($question->options as $option) {
                if ($option->option_image && file_exists(public_path($option->option_image))) {
                    unlink(public_path($option->option_image));
                }
                $option->delete();
            }
            
            // حذف الإجابات المرتبطة
            \App\Models\StudentTestAnswer::where('question_id', $questionId)->delete();
            
            // حذف السؤال
            $question->delete();
            
            // إعادة ترتيب الأسئلة
            $this->reorderQuestions($testId);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => __('l.question_deleted_successfully')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting question: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إعادة ترتيب الأسئلة بعد الحذف
     */
    private function reorderQuestions($testId)
    {
        $questions = TestQuestion::where('test_id', $testId)
            ->orderBy('part')
            ->orderBy('question_order')
            ->get();
        
        $currentOrder = 1;
        $currentPart = null;
        
        foreach ($questions as $question) {
            if ($currentPart !== $question->part) {
                $currentOrder = 1;
                $currentPart = $question->part;
            }
            
            $question->question_order = $currentOrder;
            $question->save();
            
            $currentOrder++;
        }
    }

    /**
     * حذف صورة خيار
     */
    public function deleteOptionImage(Request $request)
    {
        try {
            $optionId = $request->input('option_id');

            if (!$optionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Option ID is required'
                ], 422);
            }

            $option = TestQuestionOption::find($optionId);

            if (!$option) {
                return response()->json([
                    'success' => false,
                    'message' => 'Option not found'
                ], 404);
            }

            if ($option->option_image && file_exists(public_path($option->option_image))) {
                unlink(public_path($option->option_image));
            }

            $option->option_image = null;
            $option->save();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting option image: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error deleting image: ' . $e->getMessage()
            ], 500);
        }
    }
}
