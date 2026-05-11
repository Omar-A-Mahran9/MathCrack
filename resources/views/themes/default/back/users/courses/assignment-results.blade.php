@extends('themes.default.layouts.back.student-master')

@section('title')
    @lang('l.assignment_results') - {{ $studentAssignment->lectureAssignment->title }}
@endsection

@section('css')
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']],
                processEscapes: true,
                processEnvironments: true,
                packages: {'[+]': ['ams', 'newcommand', 'mathtools', 'cancel', 'color', 'bbox']},
                macros: {
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
            }
        };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <style>
        .main-content {
            max-width: 1900px;
            margin: 0 auto 40px;
        }

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

        .results-header p,
        .results-header h3 {
            color: white !important;
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
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
            z-index: 3;
        }

        .header-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 14px;
        }

        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 999px;
            padding: 8px 14px;
            font-weight: 700;
            backdrop-filter: blur(10px);
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
            font-weight: 700;
        }

        .score-body {
            padding: 30px;
        }

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
            font-weight: 800;
            color: #1d4ed8;
        }

        .score-frac-part.total {
            color: #4b5563;
        }

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
            font-weight: 800;
            border: 1px solid #22c55e;
        }

        .score-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
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
            font-weight: 800;
            color: #1e40af;
            margin-bottom: 8px;
            line-height: 1;
        }

        .score-item-label {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .score-item.correct .score-item-number { color: #10b981; }
        .score-item.incorrect .score-item-number { color: #dc2626; }
        .score-item.earned .score-item-number { color: #059669; }
        .score-item.pending .score-item-number { color: #f59e0b; }

        .part-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            margin: 0 0 30px;
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
            cursor: pointer;
        }

        .part-title {
            font-size: 1.2rem;
            font-weight: 700;
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
            flex-wrap: wrap;
        }

        .questions-list {
            padding: 20px;
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

        .question-item:hover {
            background: #f8fafc;
        }

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
            font-weight: 700;
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

        .question-pending .question-number {
            background: #f59e0b;
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
            line-height: 1.65;
            margin-bottom: 10px;
            word-wrap: break-word;
            white-space: normal !important;
            text-align: left !important;
            overflow-wrap: break-word !important;
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

        .question-pending .question-score-info {
            background: #fef3c7;
            border-color: #f59e0b;
            color: #92400e;
        }

        .question-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            margin-top: 8px;
        }

        .status-correct {
            background: #d1fae5;
            color: #065f46;
        }

        .status-incorrect {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-unanswered {
            background: #e5e7eb;
            color: #374151;
        }

        .question-image,
        .explanation-image {
            margin-bottom: 12px;
            text-align: center;
        }

        .question-image img,
        .explanation-image img,
        .explanation-image-wrapper img {
            display: inline-block;
            max-width: 400px;
            width: auto;
            height: auto;
            max-height: 300px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.12);
            background: #fff;
            padding: 4px;
            cursor: zoom-in;
        }

        .option-image-wrapper img,
        .options-review img {
            display: inline-block;
            max-width: 220px;
            width: auto;
            height: auto;
            max-height: 220px;
            object-fit: contain;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            background: #fff;
            padding: 4px;
            cursor: zoom-in;
        }

        .answer-section {
            margin-top: 15px;
        }

        .answer-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            padding: 8px 0;
            font-size: 0.9rem;
            border-bottom: 1px solid #eef2f7;
        }

        .answer-row:last-child {
            border-bottom: none;
        }

        .answer-label {
            font-weight: 700;
            color: #6b7280;
            min-width: 130px;
        }

        .answer-value {
            font-weight: 700;
            flex: 1;
            text-align: right;
            word-break: break-word;
        }

        .answer-correct { color: #10b981; }
        .answer-incorrect { color: #dc2626; }
        .answer-pending { color: #f59e0b; }
        .answer-unanswered { color: #6b7280; font-style: italic; }

        .options-review {
            margin-top: 10px;
        }

        .option-review {
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 5px;
            font-size: 0.9rem;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
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
            font-weight: 700;
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

        .explanation-btn {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 200px;
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
            margin-top: 15px;
        }

        .explanation-btn:hover {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            transform: translateY(-1px);
        }

        .explanation-btn.active {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
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

        .teacher-feedback {
            background: #fff7ed;
            border: 1px solid #fdba74;
            border-radius: 8px;
            padding: 12px;
            margin-top: 12px;
            color: #9a3412;
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
            font-weight: 700;
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

        .btn-success-action {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-success-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .image-lightbox-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
        }

        .image-lightbox-overlay img {
            max-width: 90%;
            max-height: 90%;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
            background: #fff;
        }

        mjx-container {
            overflow-x: auto;
            overflow-y: hidden;
            max-width: 100%;
        }

        @media (max-width: 768px) {
            .results-header h1 {
                font-size: 2rem;
            }

            .score-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .part-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .part-stats {
                justify-content: flex-start;
            }

            .question-header {
                flex-direction: column;
            }

            .question-score-info {
                margin-left: 0;
                margin-top: 10px;
            }

            .answer-row {
                flex-direction: column;
            }

            .answer-value {
                text-align: left;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-action {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $assignment = $studentAssignment->lectureAssignment;
        $lesson = $assignment->lecture;
        $course = $lesson->course;

        $displayCourseName = $course->name;

        if (($course->track_slug ?? null) === 'digital-sat') {
            $displayCourseName = __('l.digital_sat_course');
        }

        $questions = $assignment->questions;
        $answers = $studentAssignment->answers;
        $totalQuestions = $questions->count();
        $answeredQuestions = $answers->count();
        $correctAnswers = $answers->where('is_correct', true)->count();
        $pendingAnswers = $answers->where('is_correct', null)->count();
        $wrongAnswers = $answers->filter(function ($answer) {
            return $answer->is_correct === false || $answer->is_correct === 0;
        })->count();

        $earnedScore = (float) ($studentAssignment->score ?? 0);
        $maxScore = (float) ($studentAssignment->total_points ?? $questions->sum(function ($q) {
            return (float) ($q->points ?? $q->score ?? 0);
        }));

        $percentage = (float) ($studentAssignment->percentage ?? ($maxScore > 0 ? ($earnedScore / $maxScore) * 100 : 0));

        $resolveImageUrl = function ($path) {
            if (empty($path)) return null;

            $path = trim($path);

            if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '//', 'data:'])) {
                return $path;
            }

            if (\Illuminate\Support\Str::startsWith($path, 'public/')) {
                return asset('storage/' . \Illuminate\Support\Str::after($path, 'public/'));
            }

            if (\Illuminate\Support\Str::startsWith($path, ['storage/', 'assets/', 'uploads/', 'images/', 'back-assets/'])) {
                return asset($path);
            }

            return asset('storage/' . ltrim($path, '/'));
        };

        $submittedAt = !empty($studentAssignment->submitted_at)
            ? \Carbon\Carbon::parse($studentAssignment->submitted_at)->format('Y-m-d H:i')
            : '-';
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
                        <h1>@lang('l.assignment_results')</h1>
                        <h3 class="mb-2">{{ $assignment->title }}</h3>
                        <p class="mb-0">{{ $displayCourseName }} - {{ $lesson->name }}</p>

                        <div class="header-badges">
                            <span class="header-badge">
                                <i class="fas fa-book"></i>
                                {{ $displayCourseName }}
                            </span>

                            <span class="header-badge">
                                <i class="fas fa-video"></i>
                                {{ $lesson->name }}
                            </span>

                            <span class="header-badge">
                                <i class="fas fa-calendar"></i>
                                {{ $submittedAt }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-4 text-end">
                        <i class="fas fa-chart-line fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="score-summary">
            <div class="score-header">
                <h3>@lang('l.assignment_summary')</h3>
            </div>

            <div class="score-body">
                <div class="score-summary-box">
                    <div class="score-main-label">FINAL SCORE</div>

                    <div class="score-main-number score-fraction">
                        <span class="score-frac-part">{{ round($earnedScore, 1) }}</span>
                        <span class="score-frac-slash">/</span>
                        <span class="score-frac-part total">{{ round($maxScore, 1) }}</span>
                    </div>

                    <div class="score-main-percentage">
                        {{ number_format($percentage, 1) }}%
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

                    <div class="score-item pending">
                        <div class="score-item-number">{{ $pendingAnswers }}</div>
                        <div class="score-item-label">@lang('l.pending_review')</div>
                    </div>

                    <div class="score-item earned">
                        <div class="score-item-number">{{ round($earnedScore, 1) }}</div>
                        <div class="score-item-label">@lang('l.points_earned')</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="part-section">
            <div class="part-header"
                 data-bs-toggle="collapse"
                 data-bs-target="#assignment-review"
                 aria-expanded="true">
                <h4 class="part-title mb-0">
                    @lang('l.questions_review')
                </h4>

                <div class="part-stats">
                    <span>{{ $totalQuestions }} @lang('l.questions')</span>
                    <span>{{ $correctAnswers }}/{{ $totalQuestions }} @lang('l.correct_answers')</span>
                    <span>{{ round($earnedScore, 1) }}/{{ round($maxScore, 1) }} @lang('l.points')</span>
                    <i class="fas fa-chevron-down ms-2"></i>
                </div>
            </div>

            <div id="assignment-review" class="collapse show">
                <div class="questions-list">
                    @foreach($questions as $index => $question)
                        @php
                            $studentAnswer = $answers->where('lecture_question_id', $question->id)->first();

                            $isAnswered = (bool) $studentAnswer;
                            $isPending = false;
                            $isCorrect = false;

                            if ($studentAnswer) {
                                if ($question->type === 'essay' || is_null($studentAnswer->is_correct)) {
                                    $isPending = true;
                                } elseif ((bool) $studentAnswer->is_correct) {
                                    $isCorrect = true;
                                }
                            }

                            $statusClass = !$isAnswered
                                ? 'question-unanswered'
                                : ($isPending ? 'question-pending' : ($isCorrect ? 'question-correct' : 'question-incorrect'));

                            $statusBadge = !$isAnswered
                                ? 'status-unanswered'
                                : ($isPending ? 'status-pending' : ($isCorrect ? 'status-correct' : 'status-incorrect'));

                            $statusText = !$isAnswered
                                ? __('l.not_answered')
                                : ($isPending ? __('l.pending_review') : ($isCorrect ? __('l.correct') : __('l.incorrect')));

                            $questionPoints = (float) ($question->points ?? $question->score ?? 0);
                            $questionEarned = ($studentAnswer && $isCorrect) ? $questionPoints : 0;

                            $questionImageUrl = $resolveImageUrl($question->question_image ?? null);
                            $explanationImageUrl = $resolveImageUrl($question->explanation_image ?? null);
                        @endphp

                        <div class="question-item {{ $statusClass }}">
                            <div class="question-header">
                                <div class="question-number">{{ $index + 1 }}</div>

                                <div class="question-content">
                                    @if($questionImageUrl)
                                        <div class="question-image">
                                            <img src="{{ $questionImageUrl }}"
                                                 alt="Question Image"
                                                 class="zoomable-image"
                                                 loading="lazy">
                                        </div>
                                    @endif

                                    <div class="question-text">
                                        {!! $question->question_text !!}
                                    </div>

                                    <span class="question-status-badge {{ $statusBadge }}">
                                        @if($isCorrect)
                                            <i class="fas fa-check-circle"></i>
                                        @elseif($isPending)
                                            <i class="fas fa-clock"></i>
                                        @elseif(!$isAnswered)
                                            <i class="fas fa-minus-circle"></i>
                                        @else
                                            <i class="fas fa-times-circle"></i>
                                        @endif
                                        {{ $statusText }}
                                    </span>
                                </div>

                                <div class="question-score-info">
                                    {{ round($questionEarned, 1) }}/{{ round($questionPoints, 1) }}<br>
                                    <small>@lang('l.points')</small>
                                </div>
                            </div>

                            <div class="answer-section">
                                @switch($question->type)
                                    @case('mcq')
                                        <div class="answer-row">
                                            <span class="answer-label">@lang('l.your_answer'):</span>
                                            <span class="answer-value {{ $isCorrect ? 'answer-correct' : ($isAnswered ? 'answer-incorrect' : 'answer-unanswered') }}">
                                                @if($studentAnswer && $studentAnswer->selectedOption)
                                                    {!! $studentAnswer->selectedOption->option_text !!}
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
                                                {!! $correctOption ? $correctOption->option_text : '-' !!}
                                            </span>
                                        </div>

                                        <div class="options-review">
                                            @foreach($question->options as $optionIndex => $option)
                                                @php
                                                    $optionImageUrl = $resolveImageUrl($option->option_image ?? $option->image ?? null);
                                                    $isSelectedOption = $studentAnswer && $studentAnswer->selected_option_id == $option->id;
                                                @endphp

                                                <div class="option-review
                                                    @if($option->is_correct) option-correct @endif
                                                    @if($isSelectedOption)
                                                        option-selected {{ $option->is_correct ? '' : 'option-incorrect' }}
                                                    @endif">
                                                    <div class="option-letter">{{ $option->label ?? chr(65 + $optionIndex) }}</div>

                                                    <div class="option-text">
                                                        @if($optionImageUrl)
                                                            <div class="option-image-wrapper">
                                                                <img src="{{ $optionImageUrl }}"
                                                                     alt="Option image"
                                                                     class="zoomable-image"
                                                                     loading="lazy">
                                                            </div>
                                                        @endif

                                                        <div class="option-text-body">
                                                            {!! $option->option_text !!}
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
                                                @if($studentAnswer)
                                                    {{ in_array(strtolower((string) $studentAnswer->answer_text), ['1', 'true', 'yes']) ? __('l.true') : __('l.false') }}
                                                @else
                                                    @lang('l.not_answered')
                                                @endif
                                            </span>
                                        </div>

                                        <div class="answer-row">
                                            <span class="answer-label">@lang('l.correct_answer'):</span>
                                            <span class="answer-value answer-correct">
                                                {{ in_array(strtolower((string) $question->correct_answer), ['1', 'true', 'yes']) ? __('l.true') : __('l.false') }}
                                            </span>
                                        </div>
                                        @break

                                    @case('numeric')
                                        <div class="answer-row">
                                            <span class="answer-label">@lang('l.your_answer'):</span>
                                            <span class="answer-value {{ $isCorrect ? 'answer-correct' : ($isAnswered ? 'answer-incorrect' : 'answer-unanswered') }}">
                                                {{ ($studentAnswer && $studentAnswer->answer_text !== null) ? $studentAnswer->answer_text : __('l.not_answered') }}
                                            </span>
                                        </div>

                                        <div class="answer-row">
                                            <span class="answer-label">@lang('l.correct_answer'):</span>
                                            <span class="answer-value answer-correct">
                                                {{ $question->correct_answer ?: __('l.not_available') }}
                                            </span>
                                        </div>
                                        @break

                                    @case('essay')
                                        <div class="answer-row">
                                            <span class="answer-label">@lang('l.your_answer'):</span>
                                            <span class="answer-value {{ $isPending ? 'answer-pending' : 'answer-correct' }}">
                                                {{ ($studentAnswer && $studentAnswer->answer_text !== null) ? $studentAnswer->answer_text : __('l.not_answered') }}
                                            </span>
                                        </div>
                                        @break

                                    @default
                                        <div class="answer-row">
                                            <span class="answer-label">@lang('l.your_answer'):</span>
                                            <span class="answer-value {{ $isCorrect ? 'answer-correct' : ($isAnswered ? 'answer-incorrect' : 'answer-unanswered') }}">
                                                {{ ($studentAnswer && $studentAnswer->answer_text !== null) ? $studentAnswer->answer_text : __('l.not_answered') }}
                                            </span>
                                        </div>

                                        @if($question->correct_answer)
                                            <div class="answer-row">
                                                <span class="answer-label">@lang('l.correct_answer'):</span>
                                                <span class="answer-value answer-correct">
                                                    {{ $question->correct_answer }}
                                                </span>
                                            </div>
                                        @endif
                                @endswitch

                                @if($question->explanation || $explanationImageUrl)
                                    <button type="button"
                                            class="explanation-btn"
                                            onclick="toggleExplanation({{ $index }}, this)">
                                        <i class="fas fa-lightbulb"></i>
                                        @lang('l.show_explanation')
                                    </button>

                                    <div class="question-explanation" id="explanation-{{ $index }}">
                                        @if($explanationImageUrl)
                                            <div class="explanation-image">
                                                <img src="{{ $explanationImageUrl }}"
                                                     alt="Explanation Image"
                                                     class="zoomable-image"
                                                     loading="lazy">
                                            </div>
                                        @endif

                                        @if($question->explanation)
                                            <strong>@lang('l.explanation')</strong>
                                            <div class="explanation-text">
                                                {!! $question->explanation !!}
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if($studentAnswer && $studentAnswer->teacher_feedback)
                                    <div class="teacher-feedback">
                                        <strong>@lang('l.teacher_feedback'):</strong><br>
                                        {{ $studentAnswer->teacher_feedback }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="{{ route('dashboard.users.courses-lectures-show', ['id' => encrypt($lesson->id)]) }}"
               class="btn-action btn-primary-action">
                <i class="fas fa-arrow-left"></i>
                @lang('l.back_to_lecture')
            </a>

            <a href="{{ route('dashboard.users.courses-lectures', ['id' => encrypt($course->id)]) }}"
               class="btn-action btn-success-action">
                <i class="fas fa-book"></i>
                @lang('l.back_to_course')
            </a>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const showExplanationText = @json(__('l.show_explanation'));
        const hideExplanationText = @json(__('l.hide_explanation'));

        function toggleExplanation(index, button) {
            const explanation = document.getElementById(`explanation-${index}`);
            if (!explanation) return;

            const isVisible = explanation.style.display === 'block';

            explanation.style.display = isVisible ? 'none' : 'block';

            if (button) {
                button.classList.toggle('active', !isVisible);
                button.innerHTML = isVisible
                    ? `<i class="fas fa-lightbulb"></i> ${showExplanationText}`
                    : `<i class="fas fa-eye-slash"></i> ${hideExplanationText}`;
            }

            if (!isVisible && window.MathJax && MathJax.typesetPromise) {
                MathJax.typesetPromise([explanation]);
            }
        }

        document.addEventListener('click', function (event) {
            const image = event.target.closest('.zoomable-image');
            if (!image) return;

            const overlay = document.createElement('div');
            overlay.className = 'image-lightbox-overlay';

            const img = document.createElement('img');
            img.src = image.src;

            overlay.appendChild(img);
            document.body.appendChild(overlay);

            overlay.addEventListener('click', function () {
                overlay.remove();
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            if (window.MathJax && MathJax.typesetPromise) {
                MathJax.typesetPromise();
            }

            $('.question-item, .score-item').each(function(index) {
                $(this).css('opacity', '0').css('transform', 'translateY(20px)');
                $(this).delay(index * 60).animate({ opacity: 1 }, 350);
            });
        });
    </script>
@endsection
