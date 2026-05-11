@extends('themes.default.layouts.back.student-master')

@section('title')
    Break Time - {{ $test->name }}
@endsection

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    :root {
        --mc-primary:#1e40af;
        --mc-primary-2:#3b82f6;
        --mc-success:#10b981;
        --mc-success-2:#059669;
        --mc-warn:#f59e0b;
        --mc-warn-2:#d97706;
        --mc-border:#e5e7eb;
        --mc-bg:#f8fafc;
        --mc-text:#1f2937;
        --mc-muted:#6b7280;
        --mc-dark:#111827;
    }

    body {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    }

    .break-page {
        min-height: calc(100vh - 80px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 35px 18px;
    }

    .break-card {
        background: #fff;
        border-radius: 24px;
        max-width: 860px;
        width: 100%;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
        border: 1px solid rgba(226, 232, 240, 0.9);
    }

    .break-hero {
        background: linear-gradient(135deg, var(--mc-primary) 0%, var(--mc-primary-2) 100%);
        color: #fff;
        padding: 34px 34px 28px;
        position: relative;
        overflow: hidden;
    }

    .break-hero::before {
        content: '';
        position: absolute;
        top: 0;
        right: -90px;
        width: 190px;
        height: 100%;
        background: rgba(255, 255, 255, 0.12);
        transform: skewX(-15deg);
    }

    .break-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .break-icon {
        width: 78px;
        height: 78px;
        background: rgba(255, 255, 255, 0.18);
        color: #fff;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        border: 1px solid rgba(255, 255, 255, 0.26);
        backdrop-filter: blur(10px);
        flex-shrink: 0;
    }

    .break-title {
        font-size: 2rem;
        font-weight: 900;
        margin-bottom: 6px;
        color: #fff;
    }

    .break-subtitle {
        color: rgba(255, 255, 255, 0.92);
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
    }

    .break-body {
        padding: 30px 34px 34px;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }

    .summary-item {
        background: var(--mc-bg);
        border: 1px solid var(--mc-border);
        border-radius: 14px;
        padding: 14px 16px;
    }

    .summary-label {
        color: var(--mc-muted);
        font-size: 0.82rem;
        font-weight: 800;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .summary-value {
        color: var(--mc-text);
        font-size: 1.05rem;
        font-weight: 900;
    }

    .module-progress-box {
        background: #f8fafc;
        border: 1px solid var(--mc-border);
        border-radius: 18px;
        padding: 18px;
        margin-bottom: 24px;
    }

    .module-progress-title {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--mc-text);
        font-weight: 900;
        margin-bottom: 14px;
    }

    .module-progress {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .module {
        padding: 8px 13px;
        border-radius: 999px;
        font-size: 13px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-weight: 800;
    }

    .module.current {
        background: #fef3c7;
        color: #92400e;
        border-color: #f59e0b;
    }

    .module.next {
        background: #dbeafe;
        color: #1e40af;
        border-color: #3b82f6;
    }

    .module.done {
        background: #dcfce7;
        color: #166534;
        border-color: #22c55e;
    }

    .break-timer-box {
        background: linear-gradient(135deg, #0f766e, #14b8a6);
        color: #fff;
        padding: 26px;
        border-radius: 20px;
        margin-bottom: 24px;
        text-align: center;
        box-shadow: 0 12px 28px rgba(20, 184, 166, 0.20);
    }

    .timer-label {
        font-weight: 800;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 8px;
    }

    .timer {
        font-size: 3.8rem;
        font-weight: 900;
        line-height: 1;
        letter-spacing: 0.03em;
    }

    .progress {
        height: 8px;
        background: rgba(255,255,255,0.24);
        border-radius: 999px;
        margin-top: 18px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        background: #fff;
        border-radius: 999px;
        transition: width 1s linear;
    }

    .notice-box {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #93c5fd;
        color: #1e3a8a;
        border-radius: 16px;
        padding: 15px 16px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 24px;
        font-weight: 700;
        line-height: 1.6;
    }

    .notice-box i {
        margin-top: 4px;
        flex-shrink: 0;
    }

    .btns {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-main,
    .btn-light-custom {
        border: none;
        padding: 14px 24px;
        border-radius: 12px;
        font-weight: 900;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        cursor: pointer;
        transition: all 0.25s ease;
        min-width: 180px;
    }

    .btn-main {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: #fff;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.28);
    }

    .btn-main:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 14px 30px rgba(37, 99, 235, 0.34);
    }

    .btn-light-custom {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .btn-light-custom:hover {
        color: #1e40af;
        border-color: #93c5fd;
        background: #eff6ff;
        transform: translateY(-1px);
    }

    @media (max-width: 768px) {
        .break-hero {
            padding: 28px 22px;
        }

        .break-hero-content {
            flex-direction: column;
            text-align: center;
        }

        .break-title {
            font-size: 1.65rem;
        }

        .break-body {
            padding: 24px 20px 26px;
        }

        .timer {
            font-size: 3rem;
        }

        .btn-main,
        .btn-light-custom {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
@php
    $totalModules = 0;

    foreach (range(1, 5) as $i) {
        $timeField = 'part' . $i . '_time_minutes';
        $questionsField = 'part' . $i . '_questions_count';

        if (!empty($test->$timeField) || !empty($test->$questionsField)) {
            $totalModules = $i;
        }
    }

    if ($totalModules === 0) {
        $totalModules = 2;
    }

    $currentModule = (int) ($currentModule ?? 1);
    $nextModule = $currentModule + 1;
    $nextModule = min($nextModule, $totalModules);

    $breakMinutes = (int) ($test->break_time_minutes ?? 0);
@endphp

<div class="break-page">
    <div class="break-card">
        <div class="break-hero">
            <div class="break-hero-content">
                <div class="break-icon">
                    <i class="fas fa-mug-hot"></i>
                </div>

                <div>
                    <div class="break-title">Break Time</div>
                    <p class="break-subtitle">
                        You finished Module {{ $currentModule }}. You can start Module {{ $nextModule }} anytime.
                    </p>
                </div>
            </div>
        </div>

        <div class="break-body">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Test</div>
                    <div class="summary-value">{{ $test->name }}</div>
                </div>

                <div class="summary-item">
                    <div class="summary-label">Completed</div>
                    <div class="summary-value">Module {{ $currentModule }}</div>
                </div>

                <div class="summary-item">
                    <div class="summary-label">Next</div>
                    <div class="summary-value">Module {{ $nextModule }}</div>
                </div>
            </div>

            <div class="module-progress-box">
                <div class="module-progress-title">
                    <i class="fas fa-layer-group"></i>
                    Module Progress
                </div>

                <div class="module-progress">
                    @for($i = 1; $i <= $totalModules; $i++)
                        @php
                            $cls = 'module';

                            if ($i <= $currentModule) {
                                $cls .= ' done';
                            } elseif ($i == $nextModule) {
                                $cls .= ' next';
                            }
                        @endphp

                        <div class="{{ $cls }}">Module {{ $i }}</div>
                    @endfor
                </div>
            </div>

            @if($breakMinutes > 0)
                <div class="break-timer-box">
                    <div class="timer-label">Break ends in</div>

                    <div class="timer">
                        <span id="min">00</span>:<span id="sec">00</span>
                    </div>

                    <div class="progress">
                        <div id="progress-bar" class="progress-bar"></div>
                    </div>
                </div>
            @endif

            <div class="notice-box">
                <i class="fas fa-info-circle"></i>
                <span>
                    Starting now will skip the remaining break time. Once the next module starts, the timer for that module will begin.
                </span>
            </div>

            <div class="btns">
                <button id="startBtn" class="btn-main" onclick="startModule()">
                    <i class="fas fa-play"></i>
                    Start Module {{ $nextModule }}
                </button>

                <a href="{{ route('dashboard.users.tests.index', request()->only('track')) }}" class="btn-light-custom">
                    <i class="fas fa-arrow-left"></i>
                    Back to Tests
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let interval;
    const total = {{ max(1, $breakMinutes * 60) }};
    const breakMinutes = {{ $breakMinutes }};
    const nextModule = {{ $nextModule }};

    function initializeTimer() {
        const endTime = new Date('{{ $studentTest->part1_ended_at ?? now() }}').getTime()
            + (breakMinutes * 60 * 1000);

        function update() {
            const now = new Date().getTime();
            let remaining = Math.floor((endTime - now) / 1000);

            if (remaining < 0) remaining = 0;

            let m = Math.floor(remaining / 60);
            let s = remaining % 60;

            const minEl = document.getElementById('min');
            const secEl = document.getElementById('sec');
            const progressEl = document.getElementById('progress-bar');
            const btn = document.getElementById('startBtn');

            if (minEl) minEl.innerText = String(m).padStart(2, '0');
            if (secEl) secEl.innerText = String(s).padStart(2, '0');

            let percent = (remaining / total) * 100;
            if (progressEl) progressEl.style.width = percent + "%";

            if (btn) {
                if (remaining > 0) {
                    btn.innerHTML = `<i class="fas fa-play"></i> Start Now (${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')})`;
                } else {
                    btn.innerHTML = `<i class="fas fa-play"></i> Start Module ${nextModule}`;
                    clearInterval(interval);
                }
            }
        }

        update();
        interval = setInterval(update, 1000);
    }

    function startModule() {
        let hasRemainingBreak = false;
        const minEl = document.getElementById('min');
        const secEl = document.getElementById('sec');

        if (minEl && secEl) {
            hasRemainingBreak = !(minEl.innerText === "00" && secEl.innerText === "00");
        }

        if (hasRemainingBreak) {
            Swal.fire({
                icon: 'warning',
                title: 'Start now?',
                text: 'You will skip the remaining break time.',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, start',
                cancelButtonText: 'Stay on break'
            }).then((result) => {
                if (result.isConfirmed) {
                    goStart();
                }
            });

            return;
        }

        goStart();
    }

    function goStart() {
        const btn = document.getElementById('startBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Starting...`;
        }

        fetch(`/dashboard/users/tests/{{ $test->id }}/start-module/{{ $nextModule }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(() => {
            window.location.href = `/dashboard/users/tests/{{ $test->id }}/take`;
        }).catch(() => {
            window.location.href = `/dashboard/users/tests/{{ $test->id }}/take`;
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        if (breakMinutes > 0) {
            initializeTimer();
        }
    });
</script>
@endsection
