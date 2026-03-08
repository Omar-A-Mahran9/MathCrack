<?php

namespace App\Http\Controllers\Web\Back\Admins\Tests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\TestQuestionOption;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TestQuestionsController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $test = Test::with('course')->find(decrypt($request->test_id));
        if (!$test) {
            return redirect()->route('dashboard.admins.tests')->with('error', __('l.test_not_found'));
        }

        $questions = TestQuestion::where('test_id', $test->id)
            ->with('options')
            ->orderBy('part')
            ->orderBy('question_order')
            ->get();

        $partLabels = [
            'part1' => __('l.first_part'),
            'part2' => __('l.second_part'),
            'part3' => __('l.third_part'),
            'part4' => __('l.fourth_part'),
            'part5' => __('l.fifth_part'),
        ];

        $modules = [];
        $allParts = ['part1', 'part2', 'part3', 'part4', 'part5'];

        foreach ($allParts as $partKey) {
            $countField = $partKey . '_questions_count';
            $maxQuestions = (int) ($test->$countField ?? 0);

            $currentCount = $test->questions()
                ->where('part', $partKey)
                ->count();

            if ($maxQuestions <= 0) {
                $remaining = 0;
                $canAdd   = true;
            } else {
                $remaining = max($maxQuestions - $currentCount, 0);
                $canAdd   = $remaining > 0;
            }

            $modules[$partKey] = [
                'number'    => str_replace('part', '', $partKey),
                'key'       => $partKey,
                'label'     => $partLabels[$partKey] ?? "Module " . str_replace('part', '', $partKey),
                'current'   => $currentCount,
                'max'       => $maxQuestions,
                'remaining' => $remaining,
                'can_add'   => $canAdd,
            ];
        }

        $allModulesComplete = true;
        foreach ($modules as $module) {
            if ($module['remaining'] > 0) {
                $allModulesComplete = false;
                break;
            }
        }

        $availableParts = [];
        foreach ($allParts as $partKey) {
            $countField = $partKey . '_questions_count';
            $max = (int) ($test->$countField ?? 0);
            $current = $test->questions()->where('part', $partKey)->count();

            if ($max <= 0) {
                $remaining = 0;
                $canAdd    = true;
            } else {
                $remaining = max($max - $current, 0);
                $canAdd    = $remaining > 0;
            }

            $availableParts[$partKey] = [
                'label'     => $partLabels[$partKey] ?? "Module " . str_replace('part', '', $partKey),
                'current'   => $current,
                'max'       => $max,
                'remaining' => $remaining,
                'can_add'   => $canAdd,
            ];
        }

        $questionStatus = [
            'part1_count'     => $test->questions()->where('part', 'part1')->count(),
            'part1_max'       => $test->part1_questions_count ?? 0,
            'part1_complete'  => $test->questions()->where('part', 'part1')->count() >= ($test->part1_questions_count ?? 0),

            'part2_count'     => $test->questions()->where('part', 'part2')->count(),
            'part2_max'       => $test->part2_questions_count ?? 0,
            'part2_complete'  => $test->questions()->where('part', 'part2')->count() >= ($test->part2_questions_count ?? 0),

            'part3_count'     => $test->questions()->where('part', 'part3')->count(),
            'part3_max'       => $test->part3_questions_count ?? 0,
            'part3_complete'  => $test->questions()->where('part', 'part3')->count() >= ($test->part3_questions_count ?? 0),

            'part4_count'     => $test->questions()->where('part', 'part4')->count(),
            'part4_max'       => $test->part4_questions_count ?? 0,
            'part4_complete'  => $test->questions()->where('part', 'part4')->count() >= ($test->part4_questions_count ?? 0),

            'part5_count'     => $test->questions()->where('part', 'part5')->count(),
            'part5_max'       => $test->part5_questions_count ?? 0,
            'part5_complete'  => $test->questions()->where('part', 'part5')->count() >= ($test->part5_questions_count ?? 0),

            'all_complete'    => $this->areAllQuestionsAdded($test),
        ];

        return view('themes.default.back.admins.tests.questions.index', compact(
            'test',
            'questions',
            'modules',
            'allModulesComplete',
            'availableParts',
            'questionStatus'
        ));
    }

    private function areAllQuestionsAdded(Test $test): bool
    {
        $parts = ['part1', 'part2', 'part3', 'part4', 'part5'];

        foreach ($parts as $part) {
            $countField = $part . '_questions_count';
            $maxCount   = $test->$countField ?? 0;

            if ($maxCount > 0) {
                $currentCount = $test->questions()->where('part', $part)->count();
                if ($currentCount < $maxCount) {
                    return false;
                }
            }
        }

        return true;
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add lectures')) {
            return response()->json([
                'success' => false,
                'message' => __('l.permission_denied')
            ], 403);
        }

        $test = Test::find(decrypt($request->test_id));
        if (!$test) {
            return response()->json([
                'success' => false,
                'message' => __('l.test_not_found')
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'question_text'          => 'required|string',
            'type'                   => 'required|in:mcq,tf,numeric',
            'part'                   => 'nullable|in:part1,part2,part3,part4,part5',
            'score'                  => 'required|integer|min:1|max:100',
            'difficulty'             => 'required|in:easy,medium,hard',
            'content'                => 'required|in:algebra,advanced_math,problem_solving_and_data_analysis,geometry_and_trigonometry',
            'explanation'            => 'nullable|string',
            'question_image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'explanation_image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'correct_answer'         => 'required_if:type,tf,numeric|nullable',
            'options'                => 'required_if:type,mcq|array|min:2|max:6',
            'options.*.option_text'  => 'required_if:type,mcq|string',
            'options.*.is_correct'   => 'nullable|boolean',
            'options.*.option_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('l.validation_error'),
                'errors'  => $validator->errors()
            ], 422);
        }

        if ($request->type === 'mcq' && $request->has('options')) {
            $hasCorrectAnswer = false;
            foreach ($request->options as $option) {
                if (isset($option['is_correct']) && $option['is_correct']) {
                    $hasCorrectAnswer = true;
                    break;
                }
            }

            if (!$hasCorrectAnswer) {
                return response()->json([
                    'success' => false,
                    'message' => __('l.must_select_correct_answer'),
                    'errors'  => ['options' => [__('l.must_select_correct_answer')]]
                ], 422);
            }
        }

        $selectedPart = $request->part;
        $countField   = $selectedPart . '_questions_count';

        $maxQuestions = (int) ($test->$countField ?? 0);
        $currentCount = $test->questions()->where('part', $selectedPart)->count();

        if ($maxQuestions > 0 && $currentCount >= $maxQuestions) {
            return response()->json([
                'success' => false,
                'message' => __('l.part_questions_complete'),
                'errors'  => ['part' => [__('l.part_questions_complete')]]
            ], 400);
        }

        try {
            DB::beginTransaction();

            $questionOrder = TestQuestion::where('test_id', $test->id)
                ->where('part', $selectedPart)
                ->max('question_order') ?? 0;
            $questionOrder = $questionOrder + 1;

            $questionImagePath = null;
            if ($request->hasFile('question_image')) {
                $questionImagePath = upload_to_public($request->file('question_image'), 'images/questions');
            }

            $explanationImagePath = null;
            if ($request->hasFile('explanation_image')) {
                $explanationImagePath = upload_to_public($request->file('explanation_image'), 'images/explanations');
            }

            $question = TestQuestion::create([
                'test_id'           => $test->id,
                'question_text'     => $request->question_text,
                'explanation'       => $request->explanation,
                'explanation_image' => $explanationImagePath,
                'question_image'    => $questionImagePath,
                'type'              => $request->type,
                'part'              => $selectedPart,
                'question_order'    => $questionOrder,
                'score'             => $request->score,
                'difficulty'        => $request->difficulty,
                'content'           => $request->content,
                'correct_answer'    => $request->type === 'mcq' ? '' : $request->correct_answer
            ]);

            if ($request->type === 'mcq' && $request->has('options')) {
                Log::info('Creating MCQ options', ['options' => $request->options]);

                foreach ($request->options as $index => $optionData) {
                    $optionImagePath = null;

                    if (isset($optionData['option_image']) && $optionData['option_image']) {
                        $optionImagePath = upload_to_public($optionData['option_image'], 'images/options');
                    }

                    TestQuestionOption::create([
                        'test_question_id' => $question->id,
                        'option_text'      => $optionData['option_text'] ?? '',
                        'option_image'     => $optionImagePath,
                        'is_correct'       => isset($optionData['is_correct']) && $optionData['is_correct'] == true,
                        'option_order'     => $index + 1
                    ]);
                }
            }

            DB::commit();

            $partLabels = [
                'part1' => __('l.first_part'),
                'part2' => __('l.second_part'),
                'part3' => __('l.third_part'),
                'part4' => __('l.fourth_part'),
                'part5' => __('l.fifth_part'),
            ];

            $partName = $partLabels[$selectedPart] ?? $selectedPart;
            $currentCount = TestQuestion::where('test_id', $test->id)
                ->where('part', $selectedPart)
                ->count();

            $remaining = max($maxQuestions - $currentCount, 0);

            return response()->json([
                'success'       => true,
                'message'       => __('l.question_created_successfully'),
                'part_name'     => $partName,
                'current_count' => $currentCount,
                'max_count'     => $maxQuestions,
                'remaining'     => $remaining,
                'question_id'   => $question->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating test question: ' . $e->getMessage(), [
                'test_id'      => $test->id,
                'request_data' => $request->except(['question_image', 'explanation_image']),
                'trace'        => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('l.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Request $request)
    {
        if (!Gate::allows('edit lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $test = Test::with('course')->find(decrypt($request->test_id));
        $question = TestQuestion::with('options')->find(decrypt($request->id));

        if (!$test || !$question || $question->test_id !== $test->id) {
            return redirect()->back()->with('error', __('l.question_not_found'));
        }

        return view('themes.default.back.admins.tests.questions.edit', compact('test', 'question'));
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit lectures')) {
            return response()->json([
                'success' => false,
                'message' => __('l.permission_denied')
            ], 403);
        }

        $question = TestQuestion::with('options')->find($request->id);

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => __('l.question_not_found')
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'question_text'          => 'required|string',
            'type'                   => 'required|in:mcq,tf,numeric',
            'part'                   => 'required|in:part1,part2,part3,part4,part5',
            'score'                  => 'required|integer|min:1|max:100',
            'difficulty'             => 'required|in:easy,medium,hard',
            'content'                => 'required|in:algebra,advanced_math,problem_solving_and_data_analysis,geometry_and_trigonometry',
            'explanation'            => 'nullable|string',
            'question_image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'explanation_image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'correct_answer'         => 'required_if:type,tf,numeric|nullable',
            'options'                => 'required_if:type,mcq|array|min:2|max:6',
            'options.*.option_text'  => 'required_if:type,mcq|string',
            'options.*.is_correct'   => 'nullable|boolean',
            'options.*.option_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('l.validation_error'),
                'errors'  => $validator->errors()
            ], 422);
        }

        if ($request->type === 'mcq' && $request->has('options')) {
            $hasCorrectAnswer = false;
            foreach ($request->options as $option) {
                if (isset($option['is_correct']) && $option['is_correct']) {
                    $hasCorrectAnswer = true;
                    break;
                }
            }

            if (!$hasCorrectAnswer) {
                return response()->json([
                    'success' => false,
                    'message' => __('l.must_select_correct_answer'),
                    'errors'  => ['options' => [__('l.must_select_correct_answer')]]
                ], 422);
            }
        }

        $selectedPart = $request->part;
        $oldPart      = $question->part;

        $test = Test::find($question->test_id);

        if ($test) {
            $countField   = $selectedPart . '_questions_count';
            $maxQuestions = (int) ($test->$countField ?? 0);
            $currentCount = $test->questions()->where('part', $selectedPart)->count();

            if ($maxQuestions > 0) {
                if ($selectedPart !== $oldPart && $currentCount >= $maxQuestions) {
                    return response()->json([
                        'success' => false,
                        'message' => __('l.part_questions_complete'),
                        'errors'  => ['part' => [__('l.part_questions_complete')]]
                    ], 400);
                }
            }
        }

        try {
            DB::beginTransaction();

            $questionImagePath    = $question->question_image;
            $explanationImagePath = $question->explanation_image;

            if ($request->has('remove_question_image') && $question->question_image) {
                delete_from_public($question->question_image);
                $questionImagePath = null;
            }

            if ($request->has('remove_explanation_image') && $question->explanation_image) {
                delete_from_public($question->explanation_image);
                $explanationImagePath = null;
            }

            if ($request->hasFile('question_image')) {
                if ($question->question_image) {
                    delete_from_public($question->question_image);
                }
                $questionImagePath = upload_to_public($request->file('question_image'), 'images/questions');
            }

            if ($request->hasFile('explanation_image')) {
                if ($question->explanation_image) {
                    delete_from_public($question->explanation_image);
                }
                $explanationImagePath = upload_to_public($request->file('explanation_image'), 'images/explanations');
            }

            $question->update([
                'question_text'     => $request->question_text,
                'explanation'       => $request->explanation,
                'explanation_image' => $explanationImagePath,
                'question_image'    => $questionImagePath,
                'type'              => $request->type,
                'part'              => $selectedPart,
                'score'             => $request->score,
                'difficulty'        => $request->difficulty,
                'content'           => $request->content,
                'correct_answer'    => $request->type === 'mcq' ? '' : $request->correct_answer
            ]);

            if ($request->type === 'mcq' && $request->has('options')) {
                $oldOptionsImages = [];
                foreach ($question->options as $oldOption) {
                    $oldOptionsImages[$oldOption->option_order] = $oldOption->option_image;
                }

                $question->options()->delete();

                Log::info('Updating MCQ options', ['options' => $request->options]);

                foreach ($request->options as $index => $optionData) {
                    $optionImagePath = null;

                    if (isset($optionData['option_image']) && $optionData['option_image']) {
                        $optionImagePath = upload_to_public($optionData['option_image'], 'images/options');

                        if (isset($oldOptionsImages[$index + 1]) && $oldOptionsImages[$index + 1]) {
                            delete_from_public($oldOptionsImages[$index + 1]);
                        }
                    } else {
                        $optionImagePath = $oldOptionsImages[$index + 1] ?? null;
                    }

                    TestQuestionOption::create([
                        'test_question_id' => $question->id,
                        'option_text'      => $optionData['option_text'] ?? '',
                        'option_image'     => $optionImagePath,
                        'is_correct'       => isset($optionData['is_correct']) && $optionData['is_correct'] == true,
                        'option_order'     => $index + 1
                    ]);
                }

                foreach ($oldOptionsImages as $order => $imagePath) {
                    if ($imagePath && $order > count($request->options)) {
                        delete_from_public($imagePath);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('l.question_updated_successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating test question: ' . $e->getMessage(), [
                'question_id'  => $question->id,
                'request_data' => $request->except(['question_image', 'explanation_image']),
                'trace'        => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('l.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function preview($id)
    {
        if (!Gate::allows('show lectures')) {
            return view('themes/default/back.permission-denied');
        }

        $test = Test::with(['course', 'questions.options'])->findOrFail(decrypt($id));

        $modulesQuestions = [];
        for ($i = 1; $i <= 5; $i++) {
            $modulesQuestions[$i] = $test->questions()
                ->where('part', 'part' . $i)
                ->with('options')
                ->get();
        }

        $part1Questions = $modulesQuestions[1];
        $part2Questions = $modulesQuestions[2];

        return view('themes.default.back.admins.tests.preview', compact(
            'test',
            'part1Questions',
            'part2Questions',
            'modulesQuestions'
        ));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete lectures')) {
            return response()->json([
                'success' => false,
                'message' => __('l.permission_denied')
            ], 403);
        }

        $question = TestQuestion::with('options')->find($request->id);

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => __('l.question_not_found')
            ], 404);
        }

        try {
            DB::beginTransaction();

            foreach ($question->options as $option) {
                if ($option->option_image) {
                    delete_from_public($option->option_image);
                }
            }

            if ($question->question_image) {
                delete_from_public($question->question_image);
            }

            if ($question->explanation_image) {
                delete_from_public($question->explanation_image);
            }

            $question->options()->delete();
            $question->delete();

            $remainingQuestions = TestQuestion::where('test_id', $question->test_id)
                ->where('part', $question->part)
                ->orderBy('question_order')
                ->get();

            foreach ($remainingQuestions as $index => $q) {
                $q->update(['question_order' => $index + 1]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('l.question_deleted_successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => __('l.error_occurred')
            ], 500);
        }
    }

    private function getAvailableParts(Test $test)
    {
        $availableParts = [];

        $partsMeta = [
            'part1' => [
                'count_field' => 'part1_questions_count',
                'label'       => __('l.first_part'),
            ],
            'part2' => [
                'count_field' => 'part2_questions_count',
                'label'       => __('l.second_part'),
            ],
            'part3' => [
                'count_field' => 'part3_questions_count',
                'label'       => __('l.third_part'),
            ],
            'part4' => [
                'count_field' => 'part4_questions_count',
                'label'       => __('l.fourth_part'),
            ],
            'part5' => [
                'count_field' => 'part5_questions_count',
                'label'       => __('l.fifth_part'),
            ],
        ];

        foreach ($partsMeta as $partKey => $meta) {
            if (!isset($test->{$meta['count_field']})) {
                $max = 0;
            } else {
                $max = (int) $test->{$meta['count_field']};
            }

            $count = $test->questions()->where('part', $partKey)->count();

            if ($max <= 0) {
                $remaining = 0;
                $canAdd    = true;
            } else {
                $remaining = max($max - $count, 0);
                $canAdd    = ($max - $count) > 0;
            }

            $availableParts[$partKey] = [
                'label'     => $meta['label'],
                'current'   => $count,
                'max'       => $max,
                'remaining' => $remaining,
                'can_add'   => $canAdd,
            ];
        }

        return $availableParts;
    }
}