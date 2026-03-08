<?php

namespace App\Http\Controllers\Web\Back\Users\Tests;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\{
    Test,
    Course,
    StudentTest,
    TestQuestion,
    StudentTestAnswer
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    DB,
    Validator,
    Log,
    Schema
};

class TestsController extends Controller
{
    const STATUS_PART1     = 'part1_in_progress';
    const STATUS_BREAK     = 'break_time';
    const STATUS_PART2     = 'part2_in_progress';
    const STATUS_COMPLETED = 'completed';

    const DEFAULT_MODULE_TIME = 35;
    const DEFAULT_BREAK_TIME  = 10;

    const MAX_MODULES = 5;

    public function index(Request $request)
    {
        $user = auth()->user();

        $levelId  = $request->get('level_id');
        $courseId = $request->get('course_id');

        $levels = Level::orderBy('name')->get();

        $courses = collect();
        if ($levelId) {
            $courses = Course::where('level_id', $levelId)->orderBy('name')->get();
        }

        $coursesQuery = Course::with(['activeTests' => function ($q) {
                $q->orderBy('id');
            }])
            ->whereHas('activeTests')
            ->orderBy('id');

        if ($levelId) {
            $coursesQuery->where('level_id', $levelId);
        }

        if ($courseId) {
            $coursesQuery->where('id', $courseId);
        }

        $coursesData = $coursesQuery->get();

        $coursesWithTests = $coursesData->map(function ($course) use ($user) {
            $tests = $course->activeTests->map(function ($test) use ($user) {
                return $this->formatTestData($test, $user);
            });

            return [
                'id'                => $course->id,
                'name'              => $course->name,
                'tests_price'       => $course->tests_price,
                'has_purchased_all' => $user->hasPurchasedCourseQuizzes($course->id),
                'tests'             => $tests,
                'level_id'          => $course->level_id,
            ];
        });

        return view('themes.default.back.users.tests.index', compact(
            'coursesWithTests',
            'levels',
            'courses',
            'levelId',
            'courseId'
        ));
    }

    public function mockTest(Request $request)
{
    $user = auth()->user();

    $courseId = (int) $request->query('course', 0);
    $testKey  = trim((string) $request->query('test', 'mock'));
    $levelId  = (int) ($user->level_id ?? 0);

    $test = null;

    $baseQuery = Test::query()->with('course');

    if (Schema::hasColumn('tests', 'is_active')) {
        $baseQuery->where('is_active', 1);
    }

    if ($courseId > 0) {
        $query = (clone $baseQuery)->where('course_id', $courseId);

        $test = $this->findMockCandidate($query, $testKey);

        if (!$test) {
            return redirect()
                ->route('dashboard.users.tests.index')
                ->with('error', 'No mock test available for this course.');
        }
    } elseif ($levelId > 0) {
        $course = Course::where('level_id', $levelId)->orderBy('id')->first();

        if (!$course) {
            return redirect()
                ->route('dashboard.users.tests.index')
                ->with('error', 'No course found for your selected level.');
        }

        $query = (clone $baseQuery)->where('course_id', $course->id);

        $test = $this->findMockCandidate($query, $testKey);

        if (!$test) {
            return redirect()
                ->route('dashboard.users.tests.index')
                ->with('error', 'No mock test available for your level.');
        }
    } else {
        return redirect()
            ->route('dashboard.users.tests.index')
            ->with('error', 'Level is not assigned to this account.');
    }

    return redirect()->route('dashboard.users.tests.show', $test->id);
}


    public function show($id)
    {
        $test = Test::with(['course'])->findOrFail($id);
        $user = auth()->user();

        $allAttempts = StudentTest::where('student_id', $user->id)
            ->where('test_id', $id)
            ->orderBy('attempt_number', 'desc')
            ->get();

        $activeAttempt = $allAttempts->whereIn('status', [
            self::STATUS_PART1,
            self::STATUS_BREAK,
            self::STATUS_PART2,
        ])->first();

        $canAccess = $user->canAccessTest($test->id);

        $completedAttempts  = $allAttempts->where('status', self::STATUS_COMPLETED)->count();
        $remainingAttempts  = max(0, $test->max_attempts - $completedAttempts);

        return view('themes.default.back.users.tests.show', compact(
            'test',
            'allAttempts',
            'activeAttempt',
            'canAccess',
            'completedAttempts',
            'remainingAttempts'
        ));
    }

    public function results(Request $request, $id)
    {
        $test = Test::findOrFail($id);
        $user = auth()->user();

        $attemptQuery = StudentTest::where('student_id', $user->id)
            ->where('test_id', $id);

        if ($request->filled('attempt_id')) {
            $attemptQuery->where('id', $request->input('attempt_id'));
        } elseif ($request->filled('student_test_id')) {
            $attemptQuery->where('id', $request->input('student_test_id'));
        }

        $studentTest = $attemptQuery
            ->orderBy('attempt_number', 'desc')
            ->firstOrFail();

        $allQuestions = TestQuestion::where('test_id', $test->id)
            ->with([
                'options',
                'answers' => function ($query) use ($studentTest) {
                    $query->where('student_test_id', $studentTest->id);
                },
            ])
            ->orderBy('part')
            ->orderBy('id')
            ->get();

        $test->setRelation('questions', $allQuestions);

        return view('themes.default.back.users.tests.results', [
            'test'        => $test,
            'studentTest' => $studentTest,
        ]);
    }

    public function start($id)
    {
        $user = auth()->user();
        $test = Test::findOrFail($id);

        if (!$this->canUserAccessTest($user, $test)) {
            return $this->jsonError('You are not allowed to access this test', 403);
        }

        $attemptCheck = $this->checkAttempts($user, $test);
        if (!$attemptCheck['allowed']) {
            return $this->jsonError($attemptCheck['message'], 400);
        }

        try {
            DB::beginTransaction();

            $this->createStudentTest($user, $test, $attemptCheck['next_attempt']);

            DB::commit();

            return $this->jsonSuccess([
                'redirect'           => route('dashboard.users.tests.take', $test->id),
                'attempt_number'     => $attemptCheck['next_attempt'],
                'remaining_attempts' => $attemptCheck['remaining_attempts'],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error starting test', $this->getLogContext($user, $test, $e));

            return $this->jsonError('Error starting test: ' . $e->getMessage(), 500);
        }
    }

    public function take($id)
    {
        $user = auth()->user();
        $test = Test::with(['questions.options'])->findOrFail($id);

        $studentTest = $this->getActiveStudentTest($user, $test);
        if (!$studentTest) {
            return redirect()
                ->route('dashboard.users.tests.show', $test->id)
                ->with('error', 'No active test for this user');
        }

        if ($studentTest->status === self::STATUS_BREAK) {
            $nextModule = $studentTest->current_module + 1;

            $totalModules = $this->getTotalModules($test);
            if ($nextModule > $totalModules) {
                $this->completeTest($studentTest);
                return redirect()->route('dashboard.users.tests.results', $test->id);
            }

            $this->startNextModule($studentTest, $nextModule);
            $studentTest->refresh();
        }

        if ($this->isTimeUp($studentTest)) {
            $this->completeTest($studentTest);
            return redirect()->route('dashboard.users.tests.results', $test->id);
        }

        $currentModule    = $studentTest->current_module;
        $totalModules     = $this->getTotalModules($test);
        $remainingSeconds = $this->getRemainingTimeSeconds($studentTest);

        $currentPart = 'part' . $currentModule;

        $questions = $test->questions()
            ->where('part', $currentPart)
            ->with('options')
            ->orderBy('id')
            ->get();

        return view('themes.default.back.users.tests.take', [
            'test'             => $test,
            'studentTest'      => $studentTest,
            'questions'        => $questions,
            'currentModule'    => $currentModule,
            'currentPart'      => $currentPart,
            'totalModules'     => $totalModules,
            'remainingSeconds' => $remainingSeconds,
        ]);
    }

    public function break($id)
    {
        $user = auth()->user();
        $test = Test::findOrFail($id);

        $studentTest = StudentTest::where('student_id', $user->id)
            ->where('test_id', $test->id)
            ->where('status', self::STATUS_BREAK)
            ->first();

        if (!$studentTest) {
            $activeTest = $this->getActiveStudentTest($user, $test);
            if ($activeTest) {
                return redirect()->route('dashboard.users.tests.take', $test->id);
            }

            return redirect()->route('dashboard.users.tests.show', $test->id);
        }

        $totalModules  = $this->getTotalModules($test);
        $currentModule = $studentTest->current_module;
        $nextModule    = $currentModule + 1;

        if ($this->isTimeUp($studentTest)) {
            $this->startNextModule($studentTest, $nextModule);
            return redirect()->route('dashboard.users.tests.take', $test->id);
        }

        return view('themes.default.back.users.tests.break', [
            'test'          => $test,
            'studentTest'   => $studentTest,
            'currentModule' => $currentModule,
            'nextModule'    => $nextModule,
            'totalModules'  => $totalModules,
        ]);
    }

    public function submit(Request $request, $id)
    {
        $user = auth()->user();
        $test = Test::findOrFail($id);
        $studentTest = $this->getActiveStudentTest($user, $test);

        if (!$studentTest) {
            return $this->jsonError('No active test found', 404);
        }

        try {
            $currentModule = (int) $studentTest->current_module;
            $totalModules  = (int) $this->getTotalModules($test);

            $remainingSeconds = $request->input('remaining_seconds', $studentTest->remaining_seconds);
            $studentTest->update(['remaining_seconds' => $remainingSeconds]);

            $this->finalizeModuleTiming($studentTest, $test, $currentModule, $remainingSeconds);

            if ($currentModule >= $totalModules) {
                $this->completeTest($studentTest);

                return $this->jsonSuccess([
                    'redirect' => route('dashboard.users.tests.results', $id),
                    'message'  => 'Test completed successfully',
                ]);
            }

            $nextModule = $currentModule + 1;

            if ($this->shouldHaveBreakAfterModule($test, $currentModule)) {
                $studentTest->update([
                    'status'           => self::STATUS_BREAK,
                    'break_started_at' => now(),
                ]);

                return $this->jsonSuccess([
                    'redirect' => route('dashboard.users.tests.break', $id),
                    'message'  => 'Taking a break before next module',
                ]);
            }

            $this->startNextModule($studentTest, $nextModule);

            return $this->jsonSuccess([
                'redirect' => route('dashboard.users.tests.take', $test->id),
                'message'  => 'Moving to module ' . $nextModule,
            ]);
        } catch (\Exception $e) {
            Log::error('Error submitting test', $this->getLogContext($user, $test, $e));
            return $this->jsonError('Error submitting test', 500);
        }
    }

    public function saveAnswer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_test_id' => 'required|exists:student_tests,id',
            'question_id'     => 'required|exists:test_questions,id',
            'answer'          => 'required',
        ]);

        if ($validator->fails()) {
            return $this->jsonError('Invalid data', 400, $validator->errors());
        }

        $user = auth()->user();
        $studentTest = $this->validateStudentTestAccess($user, $request->student_test_id);

        if (!$studentTest) {
            return $this->jsonError('Student test not found', 404);
        }

        if (!$this->isInProgress($studentTest)) {
            return $this->jsonError('Test is not active', 400);
        }

        try {
            $answerValue = $this->extractAnswerValue($request->input('answer'));
            if ($answerValue === null) {
                return $this->jsonError('Invalid answer value', 400);
            }

            $answer = StudentTestAnswer::saveAnswer(
                $request->student_test_id,
                $request->question_id,
                $answerValue
            );

            $this->updateCurrentScore($studentTest);

            return $this->jsonSuccess([
                'is_correct'    => $answer->is_correct,
                'score_earned'  => $answer->score_earned,
                'current_score' => $studentTest->fresh()->current_score,
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving answer', [
                'user_id'         => $user->id,
                'student_test_id' => $request->student_test_id,
                'question_id'     => $request->question_id,
                'error'           => $e->getMessage(),
            ]);

            return $this->jsonError('Error saving answer', 500);
        }
    }

    public function getRemainingTime($id)
    {
        $user = auth()->user();
        $test = Test::findOrFail($id);

        $studentTest = $this->getActiveStudentTest($user, $test);

        if (!$studentTest) {
            return $this->jsonError('No active test found', 404);
        }

        $remaining = $this->getRemainingTimeSeconds($studentTest);

        return $this->jsonSuccess([
            'remaining_seconds' => max(0, $remaining),
            'status'            => $studentTest->status,
        ]);
    }

    public function updateTimer(Request $request, $id)
    {
        $user = auth()->user();
        $test = Test::findOrFail($id);

        $studentTest = $this->getActiveStudentTest($user, $test);
        if (!$studentTest) {
            return $this->jsonError('No active test found', 404);
        }

        $studentTest->update([
            'remaining_seconds' => $request->input('remaining_seconds'),
            'is_paused'         => $request->input('is_paused', false),
        ]);

        return $this->jsonSuccess(['message' => 'Timer updated']);
    }

    private function canUserAccessTest($user, $test)
    {
        if ($test->price > 0) {
            return $user->canAccessTest($test->id);
        }
        return true;
    }

    private function checkAttempts($user, $test)
    {
        $completedAttempts = StudentTest::where('student_id', $user->id)
            ->where('test_id', $test->id)
            ->where('status', self::STATUS_COMPLETED)
            ->count();

        $nextAttempt       = $completedAttempts + 1;
        $remainingAttempts = max(0, $test->max_attempts - $nextAttempt);

        if ($completedAttempts >= $test->max_attempts) {
            return [
                'allowed' => false,
                'message' => "You have used all available attempts ({$test->max_attempts} attempts)",
            ];
        }

        $activeTest = $this->getActiveStudentTest($user, $test);
        if ($activeTest) {
            return [
                'allowed' => false,
                'message' => 'You already have a test in progress',
            ];
        }

        return [
            'allowed'            => true,
            'next_attempt'       => $nextAttempt,
            'remaining_attempts' => $remainingAttempts,
            'completed_attempts' => $completedAttempts,
        ];
    }

    private function createStudentTest($user, $test, $attemptNumber)
    {
        return StudentTest::create([
            'student_id'                => $user->id,
            'test_id'                   => $test->id,
            'attempt_number'            => $attemptNumber,
            'status'                    => self::STATUS_PART1,
            'current_score'             => $test->initial_score,
            'started_at'                => now(),
            'current_module'            => 1,
            'current_module_started_at' => now(),
            'remaining_seconds'         => $this->getModuleTime($test, 1) * 60,
            'part1_started_at'          => now(),
        ]);
    }

    private function getActiveStudentTest($user, $test)
    {
        return StudentTest::where('student_id', $user->id)
            ->where('test_id', $test->id)
            ->whereIn('status', [self::STATUS_PART1, self::STATUS_BREAK, self::STATUS_PART2])
            ->with('test')
            ->orderBy('attempt_number', 'desc')
            ->first();
    }

    private function getModuleTime($test, $moduleNumber)
    {
        $timeField = 'part' . $moduleNumber . '_time_minutes';

        return (!empty($test->$timeField) && $test->$timeField > 0)
            ? $test->$timeField
            : self::DEFAULT_MODULE_TIME;
    }

    private function extractAnswerValue($answerData)
    {
        if (is_array($answerData)) {
            if (isset($answerData['option_id'])) {
                return $answerData['option_id'];
            }

            if (isset($answerData['text'])) {
                return $answerData['text'];
            }
        }

        return $answerData;
    }

    private function formatTestData($test, $user)
    {
        return [
            'id'              => $test->id,
            'name'            => $test->name,
            'description'     => $test->description,
            'price'           => $test->price,
            'total_score'     => $test->total_score,
            'total_questions' => $test->total_questions_count,
            'total_time'      => $test->total_time_minutes,
            'has_paid'        => $user->canAccessTest($test->id),
            'status'          => $this->getTestStatus($user, $test),
        ];
    }

    private function jsonSuccess($data = [])
    {
        return response()->json(array_merge(['success' => true], $data));
    }

    private function jsonError($message, $code = 400, $errors = null)
    {
        $response = [
            'success' => false,
            'error'   => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    private function getLogContext($user, $test, $exception = null)
    {
        $context = [
            'user_id'   => $user->id,
            'test_id'   => $test->id,
            'test_name' => $test->name,
        ];

        if ($exception) {
            $context['error'] = $exception->getMessage();
        }

        return $context;
    }

    private function isInProgress($studentTest)
    {
        return in_array($studentTest->status, [
            self::STATUS_PART1,
            self::STATUS_BREAK,
            self::STATUS_PART2,
        ]);
    }

    private function isTimeUp($studentTest)
    {
        $remaining = $this->getRemainingTimeSeconds($studentTest);
        return $remaining <= 0;
    }

    private function completeTest($studentTest)
    {
        $studentTest->update([
            'status'      => self::STATUS_COMPLETED,
            'completed_at'=> now(),
            'final_score' => $studentTest->current_score,
        ]);

        return true;
    }

    private function finalizeModuleTiming($studentTest, $test, $moduleNumber, $remainingSeconds)
    {
        $timeMinutes = $this->getModuleTime($test, $moduleNumber);
        $moduleSeconds = $timeMinutes * 60;

        $spent = $moduleSeconds - (int) $remainingSeconds;
        if ($spent < 0) $spent = 0;
        if ($spent > $moduleSeconds) $spent = $moduleSeconds;

        if ($moduleNumber === 1 && $this->hasColumn($studentTest, 'time_spent_part1')) {
            $studentTest->update(['time_spent_part1' => $spent]);
        }

        if ($moduleNumber === 2 && $this->hasColumn($studentTest, 'time_spent_part2')) {
            $studentTest->update(['time_spent_part2' => $spent]);
        }

        $endedAtColumn = 'part' . $moduleNumber . '_ended_at';
        if ($this->hasColumn($studentTest, $endedAtColumn)) {
            $studentTest->update([$endedAtColumn => now()]);
        }

        if ($moduleNumber === 1 && $this->hasColumn($studentTest, 'part1_ended_at')) {
            $studentTest->update(['part1_ended_at' => now()]);
        }
    }

    private function hasColumn($model, $column)
    {
        return array_key_exists($column, $model->getAttributes());
    }

    private function getRemainingTimeSeconds($studentTest)
    {
        $test = $studentTest->test ?: Test::find($studentTest->test_id);

        $now           = now();
        $currentModule = (int) $studentTest->current_module;

        if ($studentTest->status === self::STATUS_PART1) {
            $timeMinutes = $this->getModuleTime($test, $currentModule);

            if (!$studentTest->part1_started_at) {
                return $timeMinutes * 60;
            }

            $endTime = $studentTest->part1_started_at->copy()->addMinutes($timeMinutes);
            return max(0, $now->diffInSeconds($endTime, false));
        }

        if ($studentTest->status === self::STATUS_BREAK) {
            $breakMinutes = (!empty($test->break_time_minutes) && $test->break_time_minutes > 0)
                ? $test->break_time_minutes
                : self::DEFAULT_BREAK_TIME;

            if (!$studentTest->break_started_at) {
                return $breakMinutes * 60;
            }

            $endTime = $studentTest->break_started_at->copy()->addMinutes($breakMinutes);
            return max(0, $now->diffInSeconds($endTime, false));
        }

        if ($studentTest->status === self::STATUS_PART2) {
            $timeMinutes = $this->getModuleTime($test, $currentModule);

            $startTime = $studentTest->current_module_started_at ?: now();
            $endTime   = $startTime->copy()->addMinutes($timeMinutes);

            return max(0, $now->diffInSeconds($endTime, false));
        }

        return self::DEFAULT_MODULE_TIME * 60;
    }

    private function updateCurrentScore($studentTest)
    {
        $totalScore = $studentTest->answers()->sum('score_earned');
        $studentTest->update(['current_score' => $totalScore]);
    }

    private function validateStudentTestAccess($user, $studentTestId)
    {
        return StudentTest::where('id', $studentTestId)
            ->where('student_id', $user->id)
            ->first();
    }

    private function getTestStatus($user, $test)
    {
        $studentTest = StudentTest::where('student_id', $user->id)
            ->where('test_id', $test->id)
            ->orderBy('attempt_number', 'desc')
            ->first();

        return $studentTest ? $studentTest->status : 'not_started';
    }

    private function startNextModule($studentTest, $nextModule)
    {
        $test = $studentTest->test ?: Test::find($studentTest->test_id);

        $timeMinutes = $this->getModuleTime($test, $nextModule);

        $updates = [
            'current_module'            => $nextModule,
            'current_module_started_at' => now(),
            'remaining_seconds'         => $timeMinutes * 60,
            'status'                    => ($nextModule == 1) ? self::STATUS_PART1 : self::STATUS_PART2,
        ];

        if ($nextModule == 2 && !$studentTest->part2_started_at && $this->hasColumn($studentTest, 'part2_started_at')) {
            $updates['part2_started_at'] = now();
        }

        $studentTest->update($updates);

        return true;
    }

    private function getTotalModules($test)
    {
        $totalModules = 0;

        for ($i = 1; $i <= self::MAX_MODULES; $i++) {
            $partName = 'part' . $i;

            $hasQuestions = $test->questions()
                ->where('part', $partName)
                ->exists();

            if ($hasQuestions) {
                $totalModules = $i;
            }
        }

        return max(1, $totalModules);
    }

    private function shouldHaveBreakAfterModule($test, $moduleNumber)
    {
        $totalModules = $this->getTotalModules($test);

        return $moduleNumber < $totalModules
            && !($test->should_hide_break_time ?? false)
            && ((int) $test->break_time_minutes > 0);
    }

    private function findMockCandidate($query, $testKey = 'mock')
    {
        $testKey = trim((string) $testKey);

        if ($testKey === '') {
            $testKey = 'mock';
        }

        if (Schema::hasColumn('tests', 'is_mock')) {
            $candidate = (clone $query)->where('is_mock', 1)->orderBy('id')->first();
            if ($candidate) {
                return $candidate;
            }
        }

        if (Schema::hasColumn('tests', 'slug')) {
            $candidate = (clone $query)->where('slug', $testKey)->orderBy('id')->first();
            if ($candidate) {
                return $candidate;
            }
        }

        if (Schema::hasColumn('tests', 'name')) {
            $candidate = (clone $query)
                ->where(function ($q) use ($testKey) {
                    $q->where('name', 'like', '%' . $testKey . '%');

                    if (strtolower($testKey) !== 'mock') {
                        $q->orWhere('name', 'like', '%mock%');
                    }
                })
                ->orderBy('id')
                ->first();

            if ($candidate) {
                return $candidate;
            }
        }

        return null;
    }
}