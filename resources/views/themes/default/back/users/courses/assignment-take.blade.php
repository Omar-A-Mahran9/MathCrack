{{-- resources/views/themes/default/back/users/courses/assignment-take.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        use Carbon\Carbon;

        $assignment = $studentAssignment->lectureAssignment;
        $lesson = $assignment->lecture;
        $course = $lesson->course;
        $questions = $assignment->questions;
        $totalQuestions = $questions->count();

        $displayCourseName = $course->name;

        if (($course->track_slug ?? null) === 'digital-sat') {
            $displayCourseName = __('l.digital_sat_course');
        }

        $timeLimitMinutes = (int) ($assignment->time_limit ?? 0);
        $durationSec = $timeLimitMinutes > 0 ? $timeLimitMinutes * 60 : 0;
        $timerSeconds = $durationSec;

        if ($durationSec > 0 && !empty($studentAssignment->started_at)) {
            $startedAt = Carbon::parse($studentAssignment->started_at);
            $endAt = $startedAt->copy()->addSeconds($durationSec);
            $timerSeconds = max(0, Carbon::now()->diffInSeconds($endAt, false));
        }

        $courseBackUrl = route('dashboard.users.courses-lectures', ['id' => encrypt($course->id)]);
        $headerTitle = $assignment->title ?? __('l.assignment');
    @endphp

    <title>{{ $headerTitle }} - @lang('l.assignment')</title>

    <style>
        :root {
            --bg:#f6f7fb;
            --ink:#0e1325;
            --muted:#6b7280;
            --brand:#1d4ed8;
            --brand2:#2563eb;
            --ok:#059669;
            --bad:#dc2626;
            --card:#ffffff;
            --line:#e5e7eb;
            --warning:#f59e0b;
            --critical:#dc2626;
            --dark:#111827;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            line-height: 1.6;
        }

        .app {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: #eaf0ff;
            border-bottom: 1px solid #dbe3ff;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .topbar-inner {
            max-width: 1280px;
            margin: auto;
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 16px;
            position: relative;
        }

        .brand {
            font-weight: 800;
            color: #1e3a8a;
            font-size: 1.05rem;
            max-width: 430px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .timer {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            background: #111827;
            color: #fff;
            border: 2px solid #2563eb;
            border-radius: 999px;
            padding: 8px 20px;
            font-size: 18px;
            font-weight: 900;
            min-width: 108px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .timer.no-limit {
            background: #374151;
            border-color: #6b7280;
        }

        .timer.timer-warning {
            background: var(--warning);
            border-color: var(--warning);
            animation: pulse 1.5s infinite;
        }

        .timer.timer-critical {
            background: var(--critical);
            border-color: var(--critical);
            animation: pulse 1s infinite;
        }

        .timer-controls {
            display: flex;
            gap: 8px;
            margin-left: auto;
            align-items: center;
            flex-wrap: wrap;
        }

        .timer-btn,
        .btn-sm {
            background: #374151;
            color: #fff;
            border: none;
            border-radius: 999px;
            padding: 10px 18px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 800;
            transition: all 0.2s ease;
            min-width: 90px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .timer-btn:hover,
        .btn-sm:hover {
            background: #4b5563;
            transform: translateY(-1px);
            color: #fff;
        }

        .pause-btn {
            background: #f59e0b;
        }

        .resume-btn {
            background: #059669;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.72; }
            100% { opacity: 1; }
        }

        .container {
            max-width: 1280px;
            margin: 16px auto 24px;
            padding: 0 20px;
        }

        .banner {
            background: #0f172a;
            color: #fff;
            border-radius: 12px;
            padding: 14px 20px;
            text-align: center;
            font-weight: 900;
            margin-bottom: 20px;
            letter-spacing: 0.03em;
        }

        .module-indicator {
            display: inline-flex;
            align-items: center;
            padding: 10px 22px;
            border-radius: 999px;
            background: #020617;
            color: #e5e7eb;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.45);
            border: 1px solid #1e293b;
            gap: 12px;
        }

        .module-indicator-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #22c55e;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.25);
            flex-shrink: 0;
        }

        .module-indicator-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .module-indicator-label {
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-size: 11px;
            color: #9ca3af;
            font-weight: 700;
        }

        .module-indicator-value {
            font-size: 16px;
            font-weight: 800;
            color: #f9fafb;
        }

        .assignment-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid var(--line);
            color: #374151;
            font-weight: 800;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .workspace {
            margin-top: 16px;
            display: grid;
            gap: 20px;
            align-items: start;
            justify-content: center;
            grid-template-columns: minmax(620px, 820px);
        }

        .q-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
            width: 100%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .q-head {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--line);
            justify-content: space-between;
            background: #f8fafc;
        }

        .q-head-left,
        .q-head-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .q-num {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: #111827;
            color: #fff;
            font-weight: 800;
            border: none;
            flex-shrink: 0;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .q-num.answered {
            background: #059669;
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(5,150,105,0.3);
        }

        .abc-toggle-btn,
        .mark-pill,
        .save-pill {
            border: 1px solid var(--line);
            border-radius: 24px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            background: #fff;
            cursor: pointer;
            font-weight: 800;
            margin: 0;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .abc-toggle-btn:hover,
        .mark-pill:hover,
        .save-pill:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .abc-toggle-btn.active,
        .mark-pill.active {
            background: #fde68a;
            border-color: #f59e0b;
            color: #92400e;
        }

        .save-pill {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #c7d2fe;
        }

        .q-body {
            padding: 24px;
        }

        .stem {
            margin: 0 0 20px 0;
            line-height: 1.7;
            font-size: 18px;
            color: #1f2937;
            text-align: justify;
            text-justify: inter-word;
        }

        .stem p {
            margin: 0.5rem 0 !important;
        }

        .options {
            display: grid;
            gap: 12px;
            margin-top: 12px;
        }

        .option-row {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .option-item {
            display: grid;
            grid-template-columns: 42px 1fr;
            gap: 10px;
            align-items: center;
            border: 2px solid var(--line);
            border-radius: 8px;
            padding: 10px;
            background: #fff;
            cursor: pointer;
            transition: box-shadow .15s, border-color .15s, transform .15s;
            position: relative;
            flex: 1;
        }

        .option-item:hover {
            border-color: #c7d2fe;
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
            transform: translateY(-1px);
        }

        .option-item.selected {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.2);
            background: #f8fbff;
        }

        .option-label {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #d1d5db;
            border-radius: 999px;
            background: #fff;
            font-weight: 900;
            font-size: 15px;
            position: relative;
            flex-shrink: 0;
            transition: all .2s ease;
        }

        .option-item.selected .option-label {
            border-color: #1d4ed8;
            color: #1d4ed8;
        }

        .option-text {
            line-height: 1.7;
            word-wrap: anywhere;
            font-size: 18px;
            flex: 1;
            color: #374151;
            width: 100%;
            text-align: justify;
            text-justify: inter-word;
        }

        .external-elimination-letter {
            width: 30px;
            height: 30px;
            display: none;
            align-items: center;
            justify-content: center;
            background: #dc2626;
            color: white;
            border-radius: 50%;
            font-size: 13px;
            font-weight: 900;
            cursor: pointer;
            z-index: 10;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            border: 2px solid #fff;
            transition: all .2s ease;
            flex-shrink: 0;
        }

        .external-elimination-letter.eliminated {
            background: #059669;
            text-decoration: line-through;
        }

        .option-strike {
            position: absolute;
            top: 50%;
            left: 54px;
            right: 16px;
            height: 3px;
            background: #dc2626;
            display: none;
            z-index: 5;
            transform: translateY(-50%);
        }

        .option-item.eliminated {
            opacity: .6;
            background: #fef2f2;
            border-color: #fecaca;
            cursor: not-allowed;
        }

        .option-item.eliminated .option-text {
            color: #9ca3af;
        }

        .elimination-mode-active .external-elimination-letter {
            display: flex;
        }

        .elimination-mode-active .option-item.eliminated .option-strike {
            display: block;
        }

        .question-image {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .question-image img,
        .stem img {
            max-width: 40% !important;
            max-height: 220px !important;
            width: auto !important;
            height: auto !important;
            object-fit: contain !important;
            cursor: zoom-in;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .option-text img,
        .option-image img {
            max-width: 40% !important;
            max-height: 110px !important;
            width: auto !important;
            height: auto !important;
            object-fit: contain !important;
            cursor: zoom-in;
            display: block;
            margin: 10px auto 0;
            border-radius: 6px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }

        .numeric-answer-wrapper {
            margin-top: 1.5rem;
            display: flex;
            justify-content: flex-start;
        }

        .numeric-answer-box {
            position: relative;
            width: 65%;
            max-width: 240px;
            padding: 22px 22px 34px;
            border-radius: 12px;
            border: 1.8px solid #111827;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        }

        .numeric-answer-box::after {
            content: "";
            position: absolute;
            left: 18px;
            right: 18px;
            bottom: 14px;
            height: 2px;
            background: #111827;
            border-radius: 999px;
        }

        .numeric-answer-input {
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
            font-size: 18px;
            text-align: center;
            padding: 0;
            margin: 0;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .essay-answer {
            width: 100%;
            min-height: 180px;
            border: 2px solid var(--line);
            border-radius: 10px;
            padding: 16px;
            font-size: 16px;
            line-height: 1.7;
            resize: vertical;
            outline: none;
        }

        .essay-answer:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }

        .q-nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            padding: 16px 20px;
            border-top: 1px solid var(--line);
            justify-content: flex-end;
            background: #f8fafc;
        }

        .q-nav-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            background: #1d4ed8;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 800;
            transition: all .2s ease;
            text-decoration: none;
        }

        .btn:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(37,99,235,0.3);
            color: #fff;
        }

        .btn-dark {
            background: #111827;
        }

        .btn-success {
            background: #059669;
        }

        .questions-bar {
            background: #111827;
            color: white;
            padding: 14px 24px;
            border-top: 2px solid #374151;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.3);
        }

        .questions-bar-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .questions-bar-title {
            font-weight: 800;
            font-size: 14px;
            color: #d1d5db;
            white-space: nowrap;
        }

        .questions-scroll-container {
            flex: 1;
            overflow-x: auto;
            padding: 4px 0;
        }

        .questions-numbers {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .question-bar-btn {
            min-width: 38px;
            height: 38px;
            border: 2px solid #374151;
            background: #1f2937;
            color: #f3f4f6;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            transition: all .2s ease;
            flex-shrink: 0;
        }

        .question-bar-btn.current {
            background: #2563eb;
            border-color: #3b82f6;
            color: white;
            transform: scale(1.05);
        }

        .question-bar-btn.answered {
            background: #059669;
            border-color: #10b981;
            color: white;
        }

        .question-bar-btn.marked {
            background: #d97706;
            border-color: #f59e0b;
            color: white;
        }

        .content-wrapper {
            padding-bottom: 90px;
        }

        .footer {
            margin-top: auto;
            border-top: 1px solid var(--line);
            background: #fff;
            position: relative;
            z-index: 999;
        }

        .footer-inner {
            max-width: 1280px;
            margin: auto;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
        }

        .pill {
            margin-left: auto;
            background: #111827;
            color: #fff;
            border-radius: 999px;
            padding: 8px 14px;
            font-weight: 800;
            font-size: 14px;
        }

        .warning-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.6);
            backdrop-filter: blur(4px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }

        .warning-modal {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            width: 90%;
            max-width: 480px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .modal-header {
            background: linear-gradient(135deg,#fef3c7,#f59e0b);
            padding: 24px;
            text-align: center;
        }

        .warning-icon {
            font-size: 52px;
            margin-bottom: 12px;
        }

        .modal-title {
            font-size: 22px;
            font-weight: 900;
            color: #92400e;
            margin: 0;
        }

        .modal-body {
            padding: 28px;
            text-align: center;
        }

        .unanswered-count {
            font-size: 52px;
            font-weight: 900;
            color: #dc2626;
            margin: 12px 0;
        }

        .unanswered-text {
            font-size: 18px;
            color: #374151;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .questions-preview {
            background: #f8fafc;
            border-radius: 12px;
            padding: 18px;
            margin: 20px 0;
            border: 1px solid #e5e7eb;
        }

        .questions-scroll {
            max-height: 120px;
            overflow-y: auto;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
        }

        .question-bubble {
            background: #fff;
            border: 2px solid #dc2626;
            border-radius: 20px;
            padding: 6px 12px;
            font-size: 14px;
            font-weight: 800;
            color: #dc2626;
            min-width: 40px;
            text-align: center;
            cursor: pointer;
        }

        .modal-footer {
            padding: 20px 24px;
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .modal-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: all .2s ease;
            min-width: 130px;
        }

        .btn-cancel {
            background: #6b7280;
            color: #fff;
        }

        .btn-submit {
            background: #dc2626;
            color: #fff;
        }

        #imgZoom img {
            max-width: 95vw !important;
            max-height: 92vh !important;
            width: auto !important;
            height: auto !important;
        }

        @media (max-width: 768px) {
            .workspace {
                grid-template-columns: minmax(300px,1fr);
            }

            .q-head {
                flex-direction: column;
                gap: 10px;
                align-items: stretch;
            }

            .q-head-left,
            .q-head-right {
                justify-content: center;
            }

            .numeric-answer-box {
                width: 70%;
            }

            .container {
                padding: 0 16px;
            }
        }

        @media (max-width: 640px) {
            .timer {
                font-size: 14px;
                padding: 6px 14px;
                position: static;
                transform: none;
                margin-left: auto;
            }

            .topbar-inner {
                flex-wrap: wrap;
                justify-content: space-between;
                gap: 10px;
            }

            .brand {
                max-width: 100%;
                width: 100%;
            }

            .q-body {
                padding: 18px;
            }

            .footer-inner {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .pill {
                margin-left: 0;
            }
        }
    </style>

    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['\\(','\\)'], ['$','$']],
                displayMath: [['\\[','\\]'], ['$$','$$']],
                processEscapes: true,
                processEnvironments: true
            },
            options: {
                skipHtmlTags: ['script','noscript','style','textarea','pre','code'],
                ignoreHtmlClass: 'tex-ignore',
                processHtmlClass: 'tex-process'
            },
            svg: {
                fontCache: 'global',
                scale: 0.9,
                displayAlign: 'center'
            }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>
</head>

<body>
<div id="imgZoom" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:20000;align-items:center;justify-content:center;padding:20px;">
    <img id="imgZoomSrc" src="" alt="" style="max-width:95vw;max-height:92vh;width:auto;height:auto;border-radius:12px;background:#fff;display:block;">
</div>

<div class="app">
    <div class="topbar">
        <div class="topbar-inner">
            <div class="brand">{{ $headerTitle }}</div>

            <div class="timer {{ $durationSec > 0 ? '' : 'no-limit' }}" id="timer-display">
                {{ $durationSec > 0 ? '--:--' : 'No Limit' }}
            </div>

            <div class="timer-controls">
                <button type="button" class="timer-btn pause-btn" id="pauseTimerBtn" {{ $durationSec > 0 ? '' : 'style=display:none' }}>Pause</button>
                <button type="button" class="timer-btn resume-btn" id="resumeTimerBtn" style="display:none">Resume</button>

                <a href="{{ $courseBackUrl }}" class="btn-sm">← Course</a>
                <button type="button" class="btn-sm" onclick="saveProgress(true)">💾 Save</button>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="container">
            <div class="banner">THIS IS AN ASSIGNMENT</div>

            <div class="module-indicator">
                <div class="module-indicator-dot"></div>
                <div class="module-indicator-text">
                    <span class="module-indicator-label">Current assignment</span>
                    <span class="module-indicator-value">{{ $displayCourseName }}</span>
                </div>
            </div>

            <div class="assignment-meta">
                <span class="meta-pill">📘 {{ $lesson->name }}</span>
                <span class="meta-pill">❓ {{ $totalQuestions }} @lang('l.questions')</span>
                <span class="meta-pill">💾 Auto-save every 30 seconds</span>
            </div>

            <div class="workspace" id="workspace">
                <div class="q-card" id="qCard">
                    <div class="q-head">
                        <div class="q-head-left">
                            <div class="q-num" id="current-question-display">1</div>
                            <button type="button" id="btnMark" class="mark-pill">🔖 Mark for Review</button>
                        </div>

                        <div class="q-head-right">
                            <button type="button" id="btnABC" class="abc-toggle-btn">✏️ Elimination Mode</button>
                        </div>
                    </div>

                    <form id="answerForm">
                        <div class="q-body">
                            @foreach($questions as $i => $q)
                                <div class="question-item"
                                     data-question-index="{{ $i }}"
                                     data-question-id="{{ $q->id }}"
                                     data-type="{{ $q->type }}"
                                     style="display: {{ $i === 0 ? 'block':'none' }};">

                                    @if(!empty($q->question_image))
                                        <div class="question-image">
                                            <img src="{{ asset($q->question_image) }}" alt="Question Image" onerror="this.style.display='none';">
                                        </div>
                                    @endif

                                    <div class="stem">{!! nl2br($q->question_text) !!}</div>

                                    @if($q->type === 'mcq')
                                        <div class="options options-container">
                                            @if(isset($q->options) && count($q->options) > 0)
                                                @foreach($q->options as $opt)
                                                    @php $optionImage = $opt->image ?? $opt->option_image ?? null; @endphp

                                                    <div class="option-row">
                                                        <div class="option-item" data-option-id="{{ $opt->id }}" onclick="selectMCQOption(this, {{ $q->id }})">
                                                            <div class="option-label"><span>{{ $opt->label ?? chr(64 + $loop->iteration) }}</span></div>

                                                            <div class="option-text">
                                                                {!! nl2br($opt->option_text) !!}

                                                                @if($optionImage)
                                                                    <div class="option-image">
                                                                        <img src="{{ asset($optionImage) }}" alt="Option Image" onerror="this.style.display='none';">
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <div class="option-strike"></div>
                                                        </div>

                                                        <div class="external-elimination-letter" data-letter="{{ $opt->label ?? chr(64 + $loop->iteration) }}">
                                                            {{ $opt->label ?? chr(64 + $loop->iteration) }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>

                                    @elseif($q->type === 'tf')
                                        <div class="options tf-options">
                                            <div class="option-row">
                                                <div class="option-item tf-option" data-value="true" onclick="selectTFOption(this, {{ $q->id }})">
                                                    <div class="option-label"><span>T</span></div>
                                                    <div class="option-text">@lang('l.true')</div>
                                                    <div class="option-strike"></div>
                                                </div>
                                                <div class="external-elimination-letter" data-letter="T">T</div>
                                            </div>

                                            <div class="option-row">
                                                <div class="option-item tf-option" data-value="false" onclick="selectTFOption(this, {{ $q->id }})">
                                                    <div class="option-label"><span>F</span></div>
                                                    <div class="option-text">@lang('l.false')</div>
                                                    <div class="option-strike"></div>
                                                </div>
                                                <div class="external-elimination-letter" data-letter="F">F</div>
                                            </div>
                                        </div>

                                    @elseif($q->type === 'essay')
                                        <textarea class="essay-answer"
                                                  placeholder="@lang('l.write_your_answer_here')"
                                                  data-question="{{ $i }}"
                                                  onblur="saveEssayAnswer(this, {{ $q->id }})"></textarea>

                                    @elseif($q->type === 'numeric')
                                        <div class="numeric-answer-wrapper">
                                            <div class="numeric-answer-box">
                                                <input
                                                    class="numeric-answer-input"
                                                    type="text"
                                                    inputmode="decimal"
                                                    dir="ltr"
                                                    autocomplete="off"
                                                    oninput="
                                                        this.value = sanitizeSatNumeric(this.value);
                                                        saveNumericAnswer(this, {{ $q->id }});
                                                    "
                                                >
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="q-nav">
                            <div class="q-nav-buttons">
                                <button type="button" class="btn btn-dark" id="prev-btn" onclick="previousQuestion()">Previous</button>
                                <button type="button" class="btn" id="next-btn" onclick="nextQuestion()">Next</button>
                                <button type="button" class="btn btn-success" id="submit-btn" style="display:none" onclick="submitAssignment()">Submit Assignment</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="questions-bar">
        <div class="questions-bar-inner">
            <div class="questions-bar-title">Questions:</div>

            <div class="questions-scroll-container">
                <div class="questions-numbers" id="questionsBarNumbers">
                    @foreach($questions as $i => $q)
                        <button type="button"
                                class="question-bar-btn {{ $i === 0 ? 'current' : '' }}"
                                data-question-index="{{ $i }}"
                                onclick="goToQuestion({{ $i }})">
                            {{ $i + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>

            <button type="button" class="btn btn-dark" onclick="nextQuestion()" style="padding:8px 16px;font-size:14px">Next</button>
        </div>
    </div>

    <div class="footer">
        <div class="footer-inner">
            <div>{{ auth()->user()->name ?? 'Student' }}</div>
            <div class="pill">Question <span id="current-question-number">1</span> of {{ $totalQuestions }}</div>
            <button type="button" class="btn" onclick="nextQuestion()" style="margin-left:auto">Next</button>
        </div>
    </div>

    <div id="warningModal" class="warning-modal-backdrop">
        <div class="warning-modal">
            <div class="modal-header">
                <div class="warning-icon">⚠️</div>
                <h2 class="modal-title">Unanswered Questions</h2>
            </div>

            <div class="modal-body">
                <div class="unanswered-count" id="unansweredCount">0</div>
                <div class="unanswered-text">You still have unanswered questions in this assignment.</div>

                <div class="questions-preview">
                    <div style="font-size:14px;color:#6b7280;margin-bottom:10px;">Unanswered questions:</div>
                    <div class="questions-scroll" id="questionsList"></div>
                </div>

                <div style="color:#6b7280;font-size:14px;">You can review them or submit anyway.</div>
            </div>

            <div class="modal-footer">
                <button type="button" class="modal-btn btn-cancel" onclick="closeWarningModal()">Review</button>
                <button type="button" class="modal-btn btn-submit" onclick="submitAnswers()">Submit Anyway</button>
            </div>
        </div>
    </div>
</div>

<script>
    const AssignmentConfig = {
        STUDENT_ASSIGNMENT_ID: @json($studentAssignment->id),
        ENCRYPTED_ID: @json(encrypt($studentAssignment->id)),
        totalQuestions: {{ (int) $totalQuestions }},
        questionIds: @json($questions->pluck('id')->values()),
        URLs: {
            SAVE_PROGRESS: @json(route('dashboard.users.assignments-save-progress')),
            SUBMIT: @json(route('dashboard.users.assignments-submit'))
        },
        Timer: {
            remaining: Math.floor({{ (int) $timerSeconds }}),
            duration: Math.floor({{ (int) $durationSec }}),
            isPaused: false,
            interval: null,
            lastUpdate: Date.now()
        }
    };

    const AssignmentState = {
        currentQuestionIndex: 0,
        answers: {},
        answeredQuestions: new Set(),
        markedQuestions: new Set(),
        eliminationMode: false,
        eliminatedOptions: new Map(),

        isLastQuestion() {
            return this.currentQuestionIndex === AssignmentConfig.totalQuestions - 1;
        },

        getUnansweredQuestions() {
            const unanswered = [];
            for (let i = 0; i < AssignmentConfig.totalQuestions; i++) {
                if (!this.answeredQuestions.has(i)) unanswered.push(i + 1);
            }
            return unanswered;
        }
    };

    @foreach ($studentAssignment->answers as $answer)
        AssignmentState.answers[{{ $answer->lecture_question_id }}] = @json($answer->answer_text ?? $answer->selected_option_id);
    @endforeach

    const TimerSystem = {
        init() {
            if (AssignmentConfig.Timer.duration <= 0) return;

            this.updateDisplay(AssignmentConfig.Timer.remaining);
            this.start();
        },

        start() {
            clearInterval(AssignmentConfig.Timer.interval);
            AssignmentConfig.Timer.lastUpdate = Date.now();

            AssignmentConfig.Timer.interval = setInterval(() => {
                if (AssignmentConfig.Timer.isPaused) return;

                const now = Date.now();
                const elapsedSeconds = Math.floor((now - AssignmentConfig.Timer.lastUpdate) / 1000);
                AssignmentConfig.Timer.lastUpdate = now;

                AssignmentConfig.Timer.remaining = Math.max(0, AssignmentConfig.Timer.remaining - elapsedSeconds);
                this.updateDisplay(AssignmentConfig.Timer.remaining);

                if (AssignmentConfig.Timer.remaining === 0) {
                    clearInterval(AssignmentConfig.Timer.interval);
                    submitAnswers();
                }
            }, 1000);
        },

        updateDisplay(seconds) {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            const el = document.getElementById('timer-display');
            if (!el) return;

            el.textContent = `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            el.classList.remove('timer-warning', 'timer-critical');

            if (seconds <= 60) el.classList.add('timer-critical');
            else if (seconds <= 300) el.classList.add('timer-warning');
        },

        pause() {
            if (AssignmentConfig.Timer.duration <= 0) return;
            AssignmentConfig.Timer.isPaused = true;
            document.getElementById('pauseTimerBtn').style.display = 'none';
            document.getElementById('resumeTimerBtn').style.display = 'inline-flex';
        },

        resume() {
            if (AssignmentConfig.Timer.duration <= 0) return;
            AssignmentConfig.Timer.isPaused = false;
            AssignmentConfig.Timer.lastUpdate = Date.now();
            document.getElementById('pauseTimerBtn').style.display = 'inline-flex';
            document.getElementById('resumeTimerBtn').style.display = 'none';
        }
    };

    function getHeaders() {
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        };
    }

    function markAnsweredByQuestionId(questionId) {
        const index = AssignmentConfig.questionIds.indexOf(Number(questionId));
        if (index !== -1) AssignmentState.answeredQuestions.add(index);
    }

    function saveAnswerByQuestionId(questionId, answer) {
        AssignmentState.answers[questionId] = answer;

        if (answer !== undefined && answer !== null && String(answer).trim() !== '') {
            markAnsweredByQuestionId(questionId);
        }

        localStorage.setItem(
            `assignment_progress_${AssignmentConfig.STUDENT_ASSIGNMENT_ID}`,
            JSON.stringify(AssignmentState.answers)
        );

        NavigationSystem.updateUI();
    }

    function selectMCQOption(el, questionId) {
        if (el.classList.contains('eliminated')) return;

        el.closest('.options-container').querySelectorAll('.option-item').forEach(x => x.classList.remove('selected'));
        el.classList.add('selected');

        saveAnswerByQuestionId(questionId, el.dataset.optionId);
        saveProgress(false);
    }

    function selectTFOption(el, questionId) {
        if (el.classList.contains('eliminated')) return;

        el.closest('.tf-options').querySelectorAll('.tf-option').forEach(x => x.classList.remove('selected'));
        el.classList.add('selected');

        saveAnswerByQuestionId(questionId, el.dataset.value);
        saveProgress(false);
    }

    function saveEssayAnswer(el, questionId) {
        saveAnswerByQuestionId(questionId, el.value);
        saveProgress(false);
    }

    function saveNumericAnswer(el, questionId) {
        saveAnswerByQuestionId(questionId, el.value);
    }

    const NavigationSystem = {
        goToQuestion(index) {
            if (index < 0 || index >= AssignmentConfig.totalQuestions) return;

            document.querySelectorAll('.question-item').forEach(el => el.style.display = 'none');

            const target = document.querySelector(`.question-item[data-question-index="${index}"]`);
            if (target) target.style.display = 'block';

            AssignmentState.currentQuestionIndex = index;

            if (window.MathJax?.typesetPromise && target) MathJax.typesetPromise([target]);

            this.updateUI();

            const currentBtn = document.querySelector(`.question-bar-btn[data-question-index="${index}"]`);
            if (currentBtn) {
                currentBtn.scrollIntoView({ behavior:'smooth', block:'nearest', inline:'center' });
            }
        },

        updateUI() {
            const index = AssignmentState.currentQuestionIndex;
            const number = index + 1;

            document.getElementById('current-question-display').textContent = number;
            document.getElementById('current-question-number').textContent = number;

            const qNumDisplay = document.getElementById('current-question-display');
            qNumDisplay.classList.toggle('answered', AssignmentState.answeredQuestions.has(index));

            document.querySelectorAll('.question-bar-btn').forEach(btn => {
                const questionIndex = Number(btn.dataset.questionIndex);
                btn.classList.toggle('current', questionIndex === index);
                btn.classList.toggle('answered', AssignmentState.answeredQuestions.has(questionIndex));
                btn.classList.toggle('marked', AssignmentState.markedQuestions.has(questionIndex));
            });

            document.getElementById('prev-btn').disabled = index === 0;
            document.getElementById('next-btn').style.display = AssignmentState.isLastQuestion() ? 'none' : 'inline-block';
            document.getElementById('submit-btn').style.display = AssignmentState.isLastQuestion() ? 'inline-block' : 'none';

            EliminationSystem.updateEliminationState();
        }
    };

    function nextQuestion() {
        if (AssignmentState.currentQuestionIndex < AssignmentConfig.totalQuestions - 1) {
            NavigationSystem.goToQuestion(AssignmentState.currentQuestionIndex + 1);
        }
    }

    function previousQuestion() {
        if (AssignmentState.currentQuestionIndex > 0) {
            NavigationSystem.goToQuestion(AssignmentState.currentQuestionIndex - 1);
        }
    }

    function goToQuestion(index) {
        NavigationSystem.goToQuestion(index);
    }

    function saveProgress(showNotification = false) {
        fetch(AssignmentConfig.URLs.SAVE_PROGRESS, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({
                id: AssignmentConfig.ENCRYPTED_ID,
                answers: AssignmentState.answers
            })
        })
        .then(response => response.json())
        .then(data => {
            if (showNotification) {
                showToast(data.success ? 'Progress saved' : 'Progress saved locally');
            }
        })
        .catch(() => {
            if (showNotification) showToast('Progress saved locally');
        });
    }

    function submitAssignment() {
        const unanswered = AssignmentState.getUnansweredQuestions();

        if (unanswered.length > 0) {
            showWarningModal(unanswered);
            return;
        }

        submitAnswers();
    }

    function showWarningModal(unansweredQuestions) {
        const modal = document.getElementById('warningModal');
        const countElement = document.getElementById('unansweredCount');
        const questionsList = document.getElementById('questionsList');

        countElement.textContent = unansweredQuestions.length;
        questionsList.innerHTML = '';

        unansweredQuestions.slice(0, 12).forEach(qNum => {
            const bubble = document.createElement('div');
            bubble.className = 'question-bubble';
            bubble.textContent = qNum;
            bubble.onclick = () => {
                closeWarningModal();
                NavigationSystem.goToQuestion(qNum - 1);
            };
            questionsList.appendChild(bubble);
        });

        if (unansweredQuestions.length > 12) {
            const moreText = document.createElement('div');
            moreText.style.cssText = 'color:#6b7280;font-size:14px;margin-top:8px;';
            moreText.textContent = `and ${unansweredQuestions.length - 12} more`;
            questionsList.appendChild(moreText);
        }

        modal.style.display = 'flex';
    }

    function closeWarningModal() {
        document.getElementById('warningModal').style.display = 'none';
    }

    function submitAnswers() {
        closeWarningModal();

        fetch(AssignmentConfig.URLs.SUBMIT, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({
                id: AssignmentConfig.ENCRYPTED_ID,
                answers: AssignmentState.answers
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.redirect) {
                localStorage.removeItem(`assignment_progress_${AssignmentConfig.STUDENT_ASSIGNMENT_ID}`);
                window.location.href = data.redirect;
                return;
            }

            alert(data.error || data.message || 'Something went wrong');
        })
        .catch(error => {
            alert(error.message || 'Something went wrong');
        });
    }

    const MarkSystem = {
        init() {
            const btnMark = document.getElementById('btnMark');
            if (!btnMark) return;

            btnMark.addEventListener('click', () => {
                const index = AssignmentState.currentQuestionIndex;

                if (AssignmentState.markedQuestions.has(index)) {
                    AssignmentState.markedQuestions.delete(index);
                    btnMark.classList.remove('active');
                    btnMark.innerHTML = '🔖 Mark for Review';
                } else {
                    AssignmentState.markedQuestions.add(index);
                    btnMark.classList.add('active');
                    btnMark.innerHTML = '🔖 Marked';
                }

                NavigationSystem.updateUI();
            });
        }
    };

    const EliminationSystem = {
        init() {
            const btnABC = document.getElementById('btnABC');
            const qCard = document.getElementById('qCard');

            if (btnABC) {
                btnABC.addEventListener('click', () => {
                    AssignmentState.eliminationMode = !AssignmentState.eliminationMode;
                    btnABC.classList.toggle('active', AssignmentState.eliminationMode);
                    btnABC.innerHTML = AssignmentState.eliminationMode ? '✏️ Elimination Mode (ON)' : '✏️ Elimination Mode';
                    qCard.classList.toggle('elimination-mode-active', AssignmentState.eliminationMode);
                });
            }

            document.addEventListener('click', (e) => {
                if (!e.target.classList.contains('external-elimination-letter')) return;
                if (!AssignmentState.eliminationMode) return;

                const letter = e.target.getAttribute('data-letter');
                const optionRow = e.target.closest('.option-row');
                const optionItem = optionRow.querySelector('.option-item');

                if (!AssignmentState.eliminatedOptions.has(AssignmentState.currentQuestionIndex)) {
                    AssignmentState.eliminatedOptions.set(AssignmentState.currentQuestionIndex, new Set());
                }

                const currentSet = AssignmentState.eliminatedOptions.get(AssignmentState.currentQuestionIndex);

                if (currentSet.has(letter)) {
                    currentSet.delete(letter);
                    optionItem.classList.remove('eliminated');
                    e.target.classList.remove('eliminated');
                } else {
                    currentSet.add(letter);
                    optionItem.classList.add('eliminated');
                    e.target.classList.add('eliminated');
                }

                e.stopPropagation();
            });
        },

        updateEliminationState() {
            const currentSet = AssignmentState.eliminatedOptions.get(AssignmentState.currentQuestionIndex) || new Set();

            document.querySelectorAll('.option-row').forEach(optionRow => {
                const optionItem = optionRow.querySelector('.option-item');
                const letterElement = optionRow.querySelector('.external-elimination-letter');
                if (!letterElement || !optionItem) return;

                const letter = letterElement.getAttribute('data-letter');

                optionItem.classList.toggle('eliminated', currentSet.has(letter));
                letterElement.classList.toggle('eliminated', currentSet.has(letter));
            });
        }
    };

    const ImageZoomSystem = {
        init() {
            const modal = document.getElementById('imgZoom');
            const modalImg = document.getElementById('imgZoomSrc');

            if (!modal || !modalImg) return;

            document.addEventListener('click', (e) => {
                const img = e.target.closest('.question-image img, .stem img, .option-text img, .option-image img');
                if (!img) return;

                e.preventDefault();
                e.stopPropagation();

                modalImg.src = img.currentSrc || img.src;
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }, true);

            modal.addEventListener('click', () => {
                modal.style.display = 'none';
                modalImg.src = '';
                document.body.style.overflow = '';
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    modal.style.display = 'none';
                    modalImg.src = '';
                    document.body.style.overflow = '';
                }
            });
        }
    };

    function sanitizeSatNumeric(value) {
        value = String(value ?? '').trim();
        value = value.replace(/\s+/g, '');
        value = value
            .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
            .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));

        value = value
            .replace(/\u066B/g, '.')
            .replace(/\u066C/g, '')
            .replace(/[⁄／]/g, '/');

        value = value.replace(/[^0-9\-./]/g, '');
        value = value.replace(/(?!^)-/g, '');

        const slashCount = (value.match(/\//g) || []).length;

        if (slashCount > 1) {
            const firstSlash = value.indexOf('/');
            value = value.slice(0, firstSlash + 1) + value.slice(firstSlash + 1).replace(/\//g, '');
        }

        if (value.includes('/')) {
            value = value.replace(/\./g, '');
        } else {
            const dotCount = (value.match(/\./g) || []).length;

            if (dotCount > 1) {
                const firstDot = value.indexOf('.');
                value = value.slice(0, firstDot + 1) + value.slice(firstDot + 1).replace(/\./g, '');
            }
        }

        return value;
    }

    function restoreSavedAnswers() {
        const local = localStorage.getItem(`assignment_progress_${AssignmentConfig.STUDENT_ASSIGNMENT_ID}`);

        if (local) {
            try {
                AssignmentState.answers = { ...AssignmentState.answers, ...JSON.parse(local) };
            } catch (e) {}
        }

        Object.keys(AssignmentState.answers).forEach(questionId => {
            const answer = AssignmentState.answers[questionId];
            markAnsweredByQuestionId(questionId);

            const question = document.querySelector(`.question-item[data-question-id="${questionId}"]`);
            if (!question) return;

            const type = question.dataset.type;

            if (type === 'mcq') {
                const option = question.querySelector(`.option-item[data-option-id="${answer}"]`);
                if (option) option.classList.add('selected');
            }

            if (type === 'tf') {
                const option = question.querySelector(`.tf-option[data-value="${answer}"]`);
                if (option) option.classList.add('selected');
            }

            if (type === 'essay') {
                const textarea = question.querySelector('.essay-answer');
                if (textarea) textarea.value = answer;
            }

            if (type === 'numeric') {
                const input = question.querySelector('.numeric-answer-input');
                if (input) input.value = answer;
            }
        });

        NavigationSystem.updateUI();
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.textContent = message;
        toast.style.cssText = `
            position:fixed;
            top:80px;
            right:24px;
            z-index:12000;
            background:#111827;
            color:#fff;
            padding:12px 16px;
            border-radius:10px;
            font-weight:800;
            box-shadow:0 8px 24px rgba(15,23,42,.25);
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
        }, 1800);
    }

    document.addEventListener('DOMContentLoaded', () => {
        TimerSystem.init();
        MarkSystem.init();
        EliminationSystem.init();
        ImageZoomSystem.init();
        restoreSavedAnswers();

        const pauseBtn = document.getElementById('pauseTimerBtn');
        const resumeBtn = document.getElementById('resumeTimerBtn');

        if (pauseBtn) pauseBtn.addEventListener('click', () => TimerSystem.pause());
        if (resumeBtn) resumeBtn.addEventListener('click', () => TimerSystem.resume());

        setInterval(() => saveProgress(false), 30000);

        if (window.MathJax?.typesetPromise) {
            MathJax.typesetPromise();
        }
    });

    window.addEventListener('beforeunload', () => {
        saveProgress(false);
    });
</script>
</body>
</html>
