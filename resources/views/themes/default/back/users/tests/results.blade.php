@extends('themes.default.layouts.back.student-master')

@section('title')
    @lang('l.test_results') - {{ $test->name }}
@endsection

@section('css')
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']],
                processEscapes: true,
                processEnvironments: true,
                packages: {'[+]': ['ams', 'newcommand', 'mathtools', 'cancel', 'color', 'bbox']},
                macros: {
                    colorbox: ['\\bbox[border:1px solid #1;background:#1;color:#2;padding:2px]{#3}', 3],
                    textcolor: ['\\color{#1}{#2}', 2],
                    Mycircled: ['\\bbox[border:2px solid black;border-radius:50%;padding:3px]{\\text{#1}}', 1],
                    item: '\\bullet\\;',
                    hspace: ['\\phantom{\\rule{#1}{0pt}}', 1],
                    vspace: ['\\\\[#1]', 1],
                    dfrac: ['\\displaystyle\\frac{#1}{#2}', 2],
                    Large: ['\\large{#1}', 1]
                }
            },
            options: {
                skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code']
            },
            chtml: {
                scale: 1.0,
                minScale: 0.5,
                matchFontHeight: false
            },
            startup: {
                ready: function () {
                    MathJax.startup.defaultReady();
                    console.log('✅ MathJax loaded successfully for test results');
                }
            }
        };
    </script>
    <script id="MathJax-script" async
            src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <style>
        .results-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
            border-radius: 15px;
            position: relative;
            overflow: hidden;
        }

        .results-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: -100px;
            width: 200px;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transform: skewX(-15deg);
        }

        .results-header h1 {
            font-size: 2.5rem;
            font-weight: 300;
            margin-bottom: 10px;
            color: white !important;
        }

        .results-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
            color: white !important;
        }

        .score-summary {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .score-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
        }

        .score-header h3 {
            margin: 0;
            color: #1f2937;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .score-body {
            padding: 30px;
        }

        .main-content {
            max-width: 1900px;
            margin: 0 auto 40px;
        }

        .score-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .score-item {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .score-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .score-item-number {
            font-size: 2rem;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 8px;
            line-height: 1;
        }

        .score-item-label {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .score-item.correct .score-item-number { color: #10b981; }
        .score-item.incorrect .score-item-number { color: #dc2626; }
        .score-item.earned .score-item-number { color: #059669; }

        .score-summary-box {
            background: #ffffff;
            border-radius: 20px;
            padding: 16px 22px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
            border: 1px solid #e5e7eb;
            max-width: 260px;
            margin: 0 auto 10px;
            position: relative;
        }

        .score-main-label {
            font-size: 1.5rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 6px;
        }

        .score-main-number.score-fraction {
            display: inline-flex;
            align-items: baseline;
            justify-content: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            background: radial-gradient(circle at top, #eff6ff 0%, #ffffff 50%, #e0ecff 100%);
        }

        .score-frac-part {
            font-size: 1.7rem;
            font-weight: 700;
            color: #1d4ed8;
        }

        .score-frac-part.total { color: #4b5563; }

        .score-frac-slash {
            font-size: 1.5rem;
            color: #9ca3af;
        }

        .score-main-percentage {
            display: inline-block;
            margin-top: 8px;
            padding: 6px 16px;
            border-radius: 999px;
            background: linear-gradient(135deg, #bbf7d0 0%, #6ee7b7 100%);
            color: #065f46;
            font-size: 1.5rem;
            font-weight: 700;
            border: 1px solid #22c55e;
        }

        .test-parts {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
            margin: 0 0 30px;
        }

        .part-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            margin: 0;
            box-sizing: border-box !important;
        }

        .part-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .part-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
            color: white !important;
            flex-grow: 1;
        }

        .part-stats {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .question-item {
            border-bottom: 2px solid #d1d5db;
            padding: 25px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            background: #f9fafb;
            border-radius: 12px;
        }

        .question-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .question-item:hover { background: #f8fafc; }

        .question-header {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
            width: 100%;
        }

        .question-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .question-correct .question-number {
            background: #10b981;
            color: white;
        }

        .question-incorrect .question-number {
            background: #dc2626;
            color: white;
        }

        .question-unanswered .question-number {
            background: #6b7280;
            color: white;
        }

        .question-content {
            flex: 1;
            min-width: 0;
        }

        .question-text {
            font-size: 1rem;
            color: #1f2937;
            line-height: 1.5;
            margin-bottom: 10px;
            word-wrap: break-word;
        }

        .question-image,
        .explanation-image {
            margin-bottom: 12px;
            text-align: center;
        }

        .question-image img,
        .explanation-image-wrapper img {
            max-width: 250px;
            width: 100%;
            height: auto;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.12);
        }

        .option-image-wrapper img {
            max-width: 180px;
            width: 100%;
            height: auto;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .options-review img {
            max-width: 200px;
            width: 100%;
            height: auto;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            cursor: zoom-in;
        }

        .option-image-wrapper {
            margin-bottom: 6px;
        }

        .option-image-wrapper img {
            max-width: 200px;
            width: 100%;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            cursor: zoom-in;
        }

        .question-explanation {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 8px;
            padding: 12px;
            margin: 10px 0;
            font-size: 0.9rem;
            color: #0369a1;
            display: none;
        }

        .explanation-btn {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 200px;
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
        }

        .explanation-btn:hover {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            transform: translateY(-1px);
        }

        .explanation-btn.active {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        }

        .question-score-info {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.85rem;
            color: #6b7280;
            margin-left: auto;
            text-align: center;
            min-width: 100px;
            flex-shrink: 0;
        }

        .question-correct .question-score-info {
            background: #d1fae5;
            border-color: #10b981;
            color: #065f46;
        }

        .question-incorrect .question-score-info {
            background: #fee2e2;
            border-color: #dc2626;
            color: #991b1b;
        }

        .answer-section { margin-top: 15px; }

        .answer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 0.9rem;
        }

        .answer-label { font-weight: 500; color: #6b7280; }
        .answer-value { font-weight: 600; }
        .answer-correct { color: #10b981; }
        .answer-incorrect { color: #dc2626; }
        .answer-unanswered { color: #6b7280; font-style: italic; }

        .options-review { margin-top: 10px; }

        .option-review {
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 5px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .option-letter {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            flex-shrink: 0;
        }

        .option-correct {
            background: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
        }

        .option-correct .option-letter {
            background: #10b981;
            color: white;
        }

        .option-selected {
            background: #dbeafe;
            border: 1px solid #3b82f6;
            color: #1e40af;
        }

        .option-selected .option-letter {
            background: #3b82f6;
            color: white;
        }

        .option-selected.option-incorrect {
            background: #fee2e2;
            border: 1px solid #dc2626;
            color: #991b1b;
        }

        .option-selected.option-incorrect .option-letter {
            background: #dc2626;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn-action {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-primary-action {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
        }

        .btn-primary-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
            color: white;
        }

        .btn-secondary-action {
            background: white;
            color: #6b7280;
            border: 2px solid #e5e7eb;
        }

        .btn-secondary-action:hover {
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .btn-success-action {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-success-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .btn-warning-action {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
        }

        .btn-warning-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(234, 88, 12, 0.3);
            color: white;
        }

        .completion-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .image-lightbox-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .image-lightbox-overlay img {
            max-width: 90%;
            max-height: 90%;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
        }

        @media (max-width: 768px) {
            .results-header h1 { font-size: 2rem; }
            .score-grid { grid-template-columns: repeat(2, 1fr); }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-action {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .part-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .part-stats {
                flex-wrap: wrap;
                justify-content: flex-start;
            }

            .question-header {
                flex-direction: column;
            }

            .question-score-info {
                margin-left: 0;
                margin-top: 10px;
            }
        }

        /* Force correct option to be green even if selected */
.option-review.option-correct{
  background: #d1fae5;
  border: 1px solid #10b981;
  color: #065f46;
}

.option-review.option-correct .option-letter{
  background: #10b981;
  color: #fff;
}

/* When correct AND selected, keep it green not blue */
.option-review.option-correct.option-selected{
  background: #d1fae5;
  border: 1px solid #10b981;
  color: #065f46;
}

.option-review.option-correct.option-selected .option-letter{
  background: #10b981;
  color: #fff;
}
    </style>
@endsection

@section('content')
    @php
        $allQuestions = $test->questions()->with(['answers' => function($query) use ($studentTest) {
            $query->where('student_test_id', $studentTest->id);
        }])->get();

        $sectionsData = $allQuestions->groupBy(function($q) {
            $fields = ['section', 'module_number', 'part', 'part_number'];
            foreach ($fields as $field) {
                if (!is_null($q->{$field}) && $q->{$field} != '') {
                    return (int) $q->{$field};
                }
            }
            return 1;
        })->sortKeys();

        if ($sectionsData->count() <= 1) {
            $modules = collect();
            $start   = 0;
            foreach (range(1, 5) as $i) {
                $count = (int) ($test->{'part'.$i.'_questions_count'} ?? 0);
                if ($count > 0) {
                    $modules[$i] = $allQuestions->slice($start, $count)->values();
                    $start      += $count;
                }
            }
            if ($modules->isNotEmpty()) {
                $sectionsData = $modules;
            }
        }

        $totalQuestions    = $allQuestions->count();
        $correctAnswers    = 0;
        $wrongAnswers      = 0;
        $answeredQuestions = 0;

        $rawEarnedFromAnswers = 0;
        foreach ($allQuestions as $q) {
            $answer = $q->answers->first();
            if ($answer) {
                $answeredQuestions++;
                if ($answer->is_correct) $correctAnswers++;
                else $wrongAnswers++;

                $rawEarnedFromAnswers += ($answer->score_earned ?? 0);
            }
        }

        $baseScore = 200;
        $maxScore  = 800;
        $targetQuestionsTotal = $maxScore - $baseScore;

        $rawQuestionsTotal = (float) $allQuestions->sum('score');
        $scale = 0;
        if ($rawQuestionsTotal > 0) {
            $scale = $targetQuestionsTotal / $rawQuestionsTotal;
        }

        $earnedFromQuestionsScaled = $rawEarnedFromAnswers * $scale;

        $finalScoreDisplayed = $baseScore + $earnedFromQuestionsScaled;

        $allowedLevels = ['Digital SAT','EST I','EST II','ACT I','ACT II'];
        $levelName = $test->course->level->name ?? '';

        $roundedScore = $finalScoreDisplayed;
        if ($roundedScore > 0) {
            $mod = ((int) round($roundedScore)) % 10;
            if ($mod !== 0) {
                $roundedScore = (int) round($roundedScore) + (10 - $mod);
            } else {
                $roundedScore = (int) round($roundedScore);
            }
        } else {
            $roundedScore = 0;
        }

        if (in_array($levelName, $allowedLevels)) {
            $finalScoreDisplayed = $roundedScore;
        } else {
            $finalScoreDisplayed = (int) round($finalScoreDisplayed);
        }

        $percentage = 0;
        if ($maxScore > 0) {
            $percentage = ($finalScoreDisplayed / $maxScore) * 100;
        }
    @endphp

    <div class="main-content">
        <div class="results-header">
            <div class="completion-badge">
                <i class="fas fa-check-circle"></i>
                @lang('l.completed')
            </div>
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1>@lang('l.test_results')</h1>
                        <p>{{ $test->name }} - {{ $test->course->name ?? '' }}</p>
                        <p><strong>Attempt</strong> {{ $studentTest->attempt_number }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex align-items-center justify-content-end gap-3">
                            <i class="fas fa-chart-line fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="score-summary">
            <div class="score-header">
                <h3>@lang('l.test_summary') - Attempt {{ $studentTest->attempt_number }}</h3>
            </div>

            <div class="score-body">
                <div class="score-summary-box">
                    <div class="score-main-label">FINAL SCORE</div>

                    <div class="score-main-number score-fraction">
                        <span id="scoreValue" class="score-frac-part">
                            {{ $finalScoreDisplayed }}
                        </span>
                        <span class="score-frac-slash">/</span>
                        <span class="score-frac-part total">{{ $maxScore }}</span>
                    </div>

                    <div class="score-main-percentage">
                        <span id="percentageValue">
                            {{ number_format($percentage, 1) }}%
                        </span>
                    </div>
                </div>

                <div class="score-grid">
                    <div class="score-item">
                        <div class="score-item-number">{{ $totalQuestions }}</div>
                        <div class="score-item-label">@lang('l.total_questions')</div>
                    </div>

                    <div class="score-item">
                        <div class="score-item-number">{{ $answeredQuestions }}</div>
                        <div class="score-item-label">@lang('l.answered_questions')</div>
                    </div>

                    <div class="score-item correct">
                        <div class="score-item-number">{{ $correctAnswers }}</div>
                        <div class="score-item-label">@lang('l.correct_answers')</div>
                    </div>

                    <div class="score-item incorrect">
                        <div class="score-item-number">{{ $wrongAnswers }}</div>
                        <div class="score-item-label">@lang('l.wrong_answers')</div>
                    </div>

                    <div class="score-item earned">
                        <div class="score-item-number">{{ $finalScoreDisplayed }}</div>
                        <div class="score-item-label">@lang('l.points_earned')</div>
                    </div>

                    <div class="score-item">
                        <div class="score-item-number">{{ $baseScore }}</div>
                        <div class="score-item-label">@lang('l.initial_score')</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="test-parts">
            @php $moduleCounter = 1; @endphp
            @foreach($sectionsData as $sectionNumber => $questions)
                @if($questions->count() > 0)
                    @php
                        $moduleTotalQuestions = $questions->count();
                        $moduleCorrect        = 0;
                        $moduleWrong          = 0;

                        $moduleRawEarned = 0;
                        $moduleRawTotal  = 0;

                        foreach ($questions as $q) {
                            $moduleRawTotal += (float) $q->score;
                            $ans = $q->answers->first();
                            if ($ans) {
                                if ($ans->is_correct) $moduleCorrect++;
                                else $moduleWrong++;

                                $moduleRawEarned += (float) ($ans->score_earned ?? 0);
                            }
                        }

                        $moduleEarnedScaled = $moduleRawEarned * $scale;
                        $moduleTotalScaled  = $moduleRawTotal * $scale;

                        $moduleEarnedScaled = (int) round($moduleEarnedScaled);
                        $moduleTotalScaled  = (int) round($moduleTotalScaled);
                    @endphp

                    <div class="part-section">
                        <div class="part-header"
                             data-bs-toggle="collapse"
                             data-bs-target="#module-{{ $moduleCounter }}"
                             aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                             style="cursor: pointer;">
                            <h4 class="part-title mb-0">
                                Module {{ $moduleCounter }}
                            </h4>
                            <div class="part-stats d-flex align-items-center gap-3">
                                <span>{{ $moduleTotalQuestions }} @lang('l.questions')</span>
                                <span>{{ $moduleCorrect }}/{{ $moduleTotalQuestions }} @lang('l.correct_answers')</span>
                                <span>{{ $moduleEarnedScaled }}/{{ $moduleTotalScaled }} @lang('l.points')</span>
                                <i class="fas fa-chevron-down ms-2"></i>
                            </div>
                        </div>

                        <div id="module-{{ $moduleCounter }}"
                             class="collapse {{ $loop->first ? 'show' : '' }}">
                            <div class="questions-list">
                                @foreach($questions as $index => $question)
                                    @php
                                        $answer     = $question->answers->first();
                                        $isCorrect  = $answer ? $answer->is_correct : false;
                                        $isAnswered = $answer ? (!is_null($answer->answer_text) || !is_null($answer->selected_option_id)) : false;

                                        $qTotalScaled  = (int) round(((float) $question->score) * $scale);
                                        $qEarnedScaled = 0;
                                        if ($answer) {
                                            $qEarnedScaled = (int) round(((float) ($answer->score_earned ?? 0)) * $scale);
                                        }
                                    @endphp

                                    <div class="question-item {{ $isCorrect ? 'question-correct' : ($isAnswered ? 'question-incorrect' : 'question-unanswered') }}">
                                        <div class="question-header">
                                            <div class="question-number">{{ $index + 1 }}</div>

                                            <div class="question-content">
                                                @if($question->question_image)
                                                    <div class="question-image">
                                                        <img src="{{ asset($question->question_image) }}"
                                                             alt="Question Image"
                                                             class="zoomable-image">
                                                    </div>
                                                @endif

                                                <div class="question-text">
                                                    {!! nl2br(e($question->question_text)) !!}
                                                </div>
                                            </div>

                                            <div class="question-score-info">
                                                {{ $answer ? $qEarnedScaled : 0 }}/{{ $qTotalScaled }}<br>
                                                <small>@lang('l.points')</small>
                                            </div>
                                        </div>

                                        <div class="answer-section">
                                            @switch($question->type)
                                                @case('mcq')
                                                    <div class="answer-row">
                                                        <span class="answer-label">@lang('l.your_answer'):</span>
                                                        <span class="answer-value {{ $isCorrect ? 'answer-correct' : ($isAnswered ? 'answer-incorrect' : 'answer-unanswered') }}">
                                                            @if($answer && $answer->selectedOption)
                                                                {{ $answer->selectedOption->option_text }}
                                                            @else
                                                                @lang('l.not_answered')
                                                            @endif
                                                        </span>
                                                    </div>

                                                    <div class="answer-row">
                                                        <span class="answer-label">@lang('l.correct_answer'):</span>
                                                        <span class="answer-value answer-correct">
                                                            @php
                                                                $correctOption = $question->options->where('is_correct', true)->first();
                                                            @endphp
                                                            {{ $correctOption ? $correctOption->option_text : '-' }}
                                                        </span>
                                                    </div>

                                                    <div class="options-review">
                                                        @foreach($question->options as $optionIndex => $option)
                                                            <div class="option-review
                                                                @if($option->is_correct) option-correct @endif
                                                                @if($answer && $answer->selectedOption && $answer->selectedOption->id == $option->id)
                                                                    option-selected {{ $option->is_correct ? '' : 'option-incorrect' }}
                                                                @endif">

                                                                <div class="option-letter">{{ chr(65 + $optionIndex) }}</div>

                                                                <div class="option-text">
                                                                    @if(!empty($option->option_image))
                                                                        <div class="option-image-wrapper">
                                                                            <img src="{{ asset($option->option_image) }}"
                                                                                 alt="Option image"
                                                                                 class="zoomable-image">
                                                                        </div>
                                                                    @endif

                                                                    <div class="option-text-body">
                                                                        {!! nl2br(e($option->option_text)) !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @break

                                                @case('tf')
                                                    <div class="answer-row">
                                                        <span class="answer-label">@lang('l.your_answer'):</span>
                                                        <span class="answer-value {{ $isCorrect ? 'answer-correct' : ($isAnswered ? 'answer-incorrect' : 'answer-unanswered') }}">
                                                            @if($isAnswered && $answer)
                                                                {{ $answer->answer_text == '1' ? __('l.true') : __('l.false') }}
                                                            @else
                                                                @lang('l.not_answered')
                                                            @endif
                                                        </span>
                                                    </div>

                                                    <div class="answer-row">
                                                        <span class="answer-label">@lang('l.correct_answer'):</span>
                                                        <span class="answer-value answer-correct">
                                                            {{ $question->correct_answer == '1' ? __('l.true') : __('l.false') }}
                                                        </span>
                                                    </div>
                                                    @break

                                                @case('numeric')
                                                    <div class="answer-row">
                                                        <span class="answer-label">@lang('l.your_answer'):</span>
                                                        <span class="answer-value {{ $isCorrect ? 'answer-correct' : ($isAnswered ? 'answer-incorrect' : 'answer-unanswered') }}">
                                                            {{ ($isAnswered && $answer) ? $answer->answer_text : __('l.not_answered') }}
                                                        </span>
                                                    </div>

                                                    <div class="answer-row">
                                                        <span class="answer-label">@lang('l.correct_answer'):</span>
                                                        <span class="answer-value answer-correct">{{ $question->correct_answer }}</span>
                                                    </div>
                                                    @break
                                            @endswitch

                                            @if($question->explanation)
                                                <div style="margin-top: 15px;">
                                                    <button type="button"
                                                            class="explanation-btn"
                                                            onclick="toggleExplanation({{ $moduleCounter }}, {{ $index }}, this)">
                                                        <i class="fas fa-lightbulb"></i>
                                                        @lang('l.show_explanation')
                                                    </button>

                                                    <div class="question-explanation"
                                                         id="explanation-{{ $moduleCounter }}-{{ $index }}">
                                                        @if(!empty($question->explanation_image ?? null))
                                                            <div class="explanation-image">
                                                                <img src="{{ asset($question->explanation_image) }}"
                                                                     alt="Explanation Image"
                                                                     class="zoomable-image">
                                                            </div>
                                                        @endif

                                                        <strong>@lang('l.explanation')</strong>
                                                        <div class="explanation-text">
                                                            {{ $question->explanation }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @php $moduleCounter++; @endphp
                @endif
            @endforeach
        </div>

        <div class="action-buttons">
            <a href="{{ route('dashboard.users.tests') }}" class="btn-action btn-primary-action">
                <i class="fas fa-list"></i>
                @lang('l.back_to_tests')
            </a>

            <a href="{{ route('dashboard.users.tests.comparison', [
                    'id'         => $test->id,
                    'attempt_id' => $studentTest->id,
                ]) }}"
               class="btn-action btn-warning-action">
                <i class="fas fa-balance-scale"></i>
                View Comparison
            </a>

            <button type="button" class="btn-action btn-success-action" onclick="printResults()">
                <i class="fas fa-print"></i>
                @lang('l.print_results')
            </button>

            <button type="button" class="btn-action btn-secondary-action" onclick="shareResults()">
                <i class="fas fa-share"></i>
                @lang('l.share_results')
            </button>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            initializeMathJax();

            $('.score-summary, .part-section').each(function (index) {
                $(this).css('opacity', '0').css('transform', 'translateY(20px)').delay(index * 100).animate({
                    opacity: 1
                }, 600).css('transform', 'translateY(0)');
            });

            $('.question-item').hover(
                function () { $(this).css('transform', 'translateX(5px)'); },
                function () { $(this).css('transform', 'translateX(0)'); }
            );
        });

        function initializeMathJax() {
            if (window.MathJax) {
                MathJax.typesetPromise([document.body]).then(() => {
                    console.log('MathJax rendering complete');
                }).catch((err) => {
                    console.error('MathJax rendering error:', err);
                });
            }
        }

        function toggleExplanation(sectionIndex, questionIndex, button) {
            const explanationId = 'explanation-' + sectionIndex + '-' + questionIndex;
            const explanationDiv = document.getElementById(explanationId);
            if (!explanationDiv) return;

            if (explanationDiv.style.display === 'none' || explanationDiv.style.display === '') {
                explanationDiv.style.display = 'block';
                button.innerHTML = '<i class="fas fa-eye-slash"></i> @lang("l.hide_explanation")';
                button.classList.add('active');
            } else {
                explanationDiv.style.display = 'none';
                button.innerHTML = '<i class="fas fa-lightbulb"></i> @lang("l.show_explanation")';
                button.classList.remove('active');
            }
        }

        function printResults() { window.print(); }

        function shareResults() {
            const score      = {{ $finalScoreDisplayed }};
            const total      = {{ $maxScore }};
            const percentage = {{ round($percentage, 1) }};
            const testName   = '{{ $test->name }}';
            const attempt    = {{ $studentTest->attempt_number }};

            const shareText = `I scored ${score} out of ${total} points (${percentage}%) in test "${testName}" - attempt ${attempt}.`;

            if (navigator.share) {
                navigator.share({
                    title: '@lang("l.test_results")',
                    text: shareText,
                    url: window.location.href
                }).catch(console.error);
            } else {
                navigator.clipboard.writeText(shareText + '\n' + window.location.href).then(() => {
                    Swal.fire({
                        title: '@lang("l.copied")',
                        text: '@lang("l.results_copied_to_clipboard")',
                        icon: 'success',
                        confirmButtonColor: '#1e40af',
                        timer: 2000
                    });
                }).catch(() => {
                    Swal.fire({
                        title: '@lang("l.share_results")',
                        text: shareText,
                        icon: 'info',
                        confirmButtonColor: '#1e40af'
                    });
                });
            }
        }

        @if($percentage >= 80)
        setTimeout(() => {
            const duration = 3000;
            const end = Date.now() + duration;

            (function frame() {
                confetti({
                    particleCount: 5,
                    startVelocity: 30,
                    spread: 360,
                    ticks: 60,
                    colors: ['#1e40af', '#3b82f6', '#10b981', '#059669'],
                    origin: {
                        x: Math.random(),
                        y: Math.random() - 0.2
                    }
                });

                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            }());
        }, 1000);
        @endif
    </script>

    <script>
        document.addEventListener('click', function (e) {
            const img = e.target.closest('.zoomable-image, .options-review img');
            if (!img) return;

            const overlay = document.createElement('div');
            overlay.className = 'image-lightbox-overlay';

            const bigImg = document.createElement('img');
            bigImg.src = img.src;
            overlay.appendChild(bigImg);

            overlay.addEventListener('click', function () {
                document.body.removeChild(overlay);
            });

            document.body.appendChild(overlay);
        });
    </script>

    @if($percentage >= 80)
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    @endif
@endsection
