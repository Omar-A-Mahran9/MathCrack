@extends('themes.default.layouts.back.student-master')

@section('title')
    Break Time - {{ $test->name }}
@endsection

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
body {
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
}

.break-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.break-card {
    background: #fff;
    border-radius: 24px;
    padding: 40px 30px;
    max-width: 520px;
    width: 100%;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,0.08);
}

.break-icon {
    width: 70px;
    height: 70px;
    background: #ecfdf5;
    color: #10b981;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: auto;
}

.break-title {
    font-size: 1.8rem;
    font-weight: 700;
}

.break-subtitle {
    color: #6b7280;
    margin-bottom: 20px;
}

.test-info {
    background: #f9fafb;
    padding: 15px;
    border-radius: 16px;
    text-align: left;
    margin-bottom: 20px;
}

.module-progress {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.module {
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    background: #f1f5f9;
}

.module.current { background: #fef3c7; }
.module.next { background: #dbeafe; }
.module.done { background: #dcfce7; }

.break-timer-box {
    background: linear-gradient(135deg, #0f766e, #14b8a6);
    color: #fff;
    padding: 25px;
    border-radius: 18px;
    margin-bottom: 25px;
}

.timer {
    font-size: 3.5rem;
    font-weight: 900;
}

.progress {
    height: 6px;
    background: rgba(255,255,255,0.2);
    border-radius: 10px;
    margin-top: 10px;
}

.progress-bar {
    height: 100%;
    background: #fff;
    border-radius: 10px;
    transition: width 1s linear;
}

.btns {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.btn-main {
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    color: #fff;
    border: none;
    padding: 14px 24px;
    border-radius: 12px;
    font-weight: 700;
    box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
}

.btn-light {
    background: #f1f5f9;
    border: none;
    padding: 14px 24px;
    border-radius: 12px;
}
</style>
@endsection

@section('content')

@php
$totalModules = 5;
$currentModule = $currentModule ?? 1;
$nextModule = $currentModule + 1;
@endphp

<div class="break-container">
    <div class="break-card">

        <div class="break-icon">
            <i class="fas fa-mug-hot"></i>
        </div>

        <div class="break-title">Break Time</div>
        <div class="break-subtitle">You can start Module {{ $nextModule }} anytime</div>

        <div class="test-info">
            <strong>{{ $test->name }}</strong>

            <div class="module-progress mt-2">
                @for($i=1;$i<=$totalModules;$i++)
                    @php
                        $cls = 'module';
                        if($i < $currentModule) $cls .= ' done';
                        elseif($i == $currentModule) $cls .= ' current';
                        elseif($i == $nextModule) $cls .= ' next';
                    @endphp
                    <div class="{{ $cls }}">Module {{ $i }}</div>
                @endfor
            </div>
        </div>

        @if($test->break_time_minutes > 0)
        <div class="break-timer-box">
            <div>Break ends in</div>
            <div class="timer">
                <span id="min">00</span> : <span id="sec">00</span>
            </div>

            <div class="progress">
                <div id="progress-bar" class="progress-bar"></div>
            </div>
        </div>
        @endif

        <div class="btns">
            <button id="startBtn" class="btn-main" onclick="startModule()">
                <i class="fas fa-play"></i> Start Module {{ $nextModule }}
            </button>

            <a href="{{ route('dashboard.users.tests') }}" class="btn-light">
                Exit
            </a>
        </div>

    </div>
</div>

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let interval;
const total = {{ $test->break_time_minutes * 60 }};

function initializeTimer() {

    const endTime = new Date('{{ $studentTest->part1_ended_at ?? now() }}').getTime()
        + ({{ $test->break_time_minutes }} * 60 * 1000);

    function update() {
        const now = new Date().getTime();
        let remaining = Math.floor((endTime - now) / 1000);

        if (remaining < 0) remaining = 0;

        let m = Math.floor(remaining / 60);
        let s = remaining % 60;

        document.getElementById('min').innerText = String(m).padStart(2, '0');
        document.getElementById('sec').innerText = String(s).padStart(2, '0');

        let percent = (remaining / total) * 100;
        document.getElementById('progress-bar').style.width = percent + "%";

        const btn = document.getElementById('startBtn');

        if (remaining > 0) {
            btn.innerHTML = `<i class="fas fa-play"></i> Start Now (${m}:${s})`;
        } else {
            btn.innerHTML = `<i class="fas fa-play"></i> Start Module`;
            clearInterval(interval);
        }
    }

    update();
    interval = setInterval(update, 1000);
}

function startModule() {

    const min = document.getElementById('min').innerText;

    if (min !== "00") {
        Swal.fire({
            icon: 'warning',
            title: 'Start now?',
            text: 'You will skip the remaining break time.',
            showCancelButton: true,
            confirmButtonText: 'Yes, start'
        }).then((result) => {
            if (result.isConfirmed) {
                goStart();
            }
        });
    } else {
        goStart();
    }
}

function goStart() {
    fetch(`/dashboard/users/tests/{{ $test->id }}/start-module/{{ $nextModule }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(() => {
        window.location.href = `/dashboard/users/tests/{{ $test->id }}/take`;
    });
}

document.addEventListener("DOMContentLoaded", function () {

    if ({{ (int) $test->break_time_minutes }} > 0) {
        initializeTimer();
    }

});
</script>
@endsection