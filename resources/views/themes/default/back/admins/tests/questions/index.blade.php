@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.test_questions') - {{ $test->name }}
@endsection

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">

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
        }
    }
};
</script>

<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<link rel="stylesheet" href="{{ asset('css/tests-questions.css') }}">

<style>
    .question-type-select.custom-question-type-select {
        width: 190px !important;
        min-width: 190px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    html[dir="ltr"] .question-type-select.custom-question-type-select {
        padding-right: 42px !important;
        background-position: right 12px center !important;
        background-size: 16px 12px !important;
    }

    html[dir="rtl"] .question-type-select.custom-question-type-select {
        padding-left: 42px !important;
        background-position: left 12px center !important;
        background-size: 16px 12px !important;
    }

    .options-container {
        width: 100%;
    }

    /* ===== Admin Questions Page Safe Polish ===== */
    .questions-page-header {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: #fff;
        border-radius: 18px;
        padding: 24px 26px;
        margin-bottom: 22px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 14px 34px rgba(30, 64, 175, 0.16);
    }

    .questions-page-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: -90px;
        width: 180px;
        height: 100%;
        background: rgba(255, 255, 255, 0.12);
        transform: skewX(-15deg);
    }

    .questions-page-header h4,
    .questions-page-header p {
        color: #fff !important;
        position: relative;
        z-index: 2;
    }

    .questions-page-header h4 {
        font-weight: 900;
        font-size: 1.45rem;
        line-height: 1.35;
    }

    .questions-page-header p {
        opacity: 0.92;
        font-weight: 700;
    }

    .questions-header-actions {
        position: relative;
        z-index: 2;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .questions-header-actions .btn {
        border-radius: 10px;
        font-weight: 800;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
    }

    .modules-summary-card {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .modules-summary-card .card-body {
        padding: 20px 22px;
    }

    .module-counter-box {
        min-width: 150px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 13px 16px;
        transition: all 0.2s ease;
    }

    .module-counter-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.07);
    }

    .module-counter-box h6 {
        font-weight: 900;
        margin-bottom: 8px;
    }

    .module-counter-box .badge {
        padding: 8px 12px;
        border-radius: 999px;
        font-weight: 900;
    }

    .questions-status-alert {
        border-radius: 14px;
        border: none;
        font-weight: 700;
        padding: 15px 18px;
    }

    .quiz-container {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 18px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
    }

    .question-card {
        border-radius: 16px;
        overflow: hidden;
    }

    .floating-add-btn {
        box-shadow: 0 12px 30px rgba(37, 99, 235, 0.3);
    }

    @media (max-width: 768px) {
        .questions-page-header {
            padding: 22px;
        }

        .questions-page-header h4 {
            font-size: 1.25rem;
        }

        .questions-header-actions {
            width: 100%;
            justify-content: stretch;
            margin-top: 14px;
        }

        .questions-header-actions .btn {
            width: 100%;
            justify-content: center;
        }

        .module-counter-box {
            width: 100%;
        }
    }

</style>
@endsection

@section('content')
    <div class="main-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @can('show lectures')
            <div class="questions-page-header d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-question-circle me-2"></i>
                        @lang('l.test_questions') -
                        <span>{{ $test->name }}</span>
                    </h4>
                    <p class="mb-0">{{ $test->course->name ?? '' }}</p>
                </div>

                <div class="questions-header-actions">
                    <a href="{{ route('dashboard.admins.tests-show', ['id' => encrypt($test->id)]) }}" class="btn btn-light waves-effect waves-light">
                        <i class="fas fa-eye me-2"></i>
                        @lang('l.view_test')
                    </a>

                    <a href="{{ route('dashboard.admins.tests-preview', ['id' => encrypt($test->id)]) }}" class="btn btn-success waves-effect waves-light" target="_blank">
                        <i class="fas fa-user-graduate me-2"></i>
                        @lang('l.preview_as_student')
                    </a>

                    <a href="{{ route('dashboard.admins.tests') }}" class="btn btn-secondary waves-effect waves-light">
                        <i class="fas fa-arrow-left me-2"></i>
                        @lang('l.back_to_list')
                    </a>
                </div>
            </div>

            <div class="card modules-summary-card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex flex-row flex-wrap justify-content-center w-100">
                                @php
                                    $activeModules = array_filter($modules, fn($m) => ($m['max'] ?? 0) > 0);
                                @endphp

                                @foreach($activeModules as $partKey => $module)
                                    <div class="module-counter-box mx-2 mb-2 text-center">
                                        <h6 class="{{ $loop->index % 2 == 0 ? 'text-primary' : 'text-success' }}">
                                            {{ $module['label'] }}
                                        </h6>

                                        <div class="badge bg-primary fs-6">
                                            {{ $module['current'] }}/{{ $module['max'] }}
                                        </div>

                                        <div class="small">@lang('l.questions')</div>

                                        @if($module['remaining'] > 0)
                                            <div class="text-warning small mt-1">
                                                {{ $module['remaining'] }} @lang('l.questions_remaining')
                                            </div>
                                        @else
                                            <div class="text-success small mt-1">
                                                @lang('l.completed')
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                @if(count($activeModules) === 0)
                                    <div class="text-muted">
                                        @lang('l.no_modules_defined')
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(!$allModulesComplete)
                <div class="alert alert-warning questions-status-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>@lang('l.questions_incomplete'):</strong>
                    @php $first = true; @endphp
                    @foreach($modules as $module)
                        @if(($module['max'] ?? 0) > 0 && $module['remaining'] > 0)
                            @if(!$first), @endif
                            {{ $module['label'] }}: {{ $module['remaining'] }} @lang('l.questions_remaining')
                            @php $first = false; @endphp
                        @endif
                    @endforeach
                </div>
            @else
                <div class="alert alert-success questions-status-alert">
                    <i class="fas fa-check-circle"></i>
                    <strong>@lang('l.test_ready')!</strong> @lang('l.all_questions_added').
                </div>
            @endif

            <div class="quiz-container">
                <div id="questionsContainer">
                    @forelse($questions as $index => $question)
                        <div class="question-card" data-question-id="{{ $question->id }}" id="question-{{ $question->id }}">
                            @include('themes.default.back.admins.tests.questions.partials.question-view', [
                                'question' => $question,
                                'index' => $index
                            ])
                        </div>
                    @empty
                        <div class="text-center py-5" id="emptyState">
                            <i class="fas fa-question-circle fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">@lang('l.no_questions_yet')</h5>
                            <p class="text-muted">@lang('l.start_adding_first_question')</p>
                            <button type="button" class="btn btn-primary" onclick="addNewQuestion()">
                                <i class="fas fa-plus me-2"></i>
                                @lang('l.add_first_question')
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        @endcan
    </div>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">@lang('l.loading')...</span>
            </div>
            <p class="mt-2 mb-0">@lang('l.saving')...</p>
        </div>
    </div>

    @can('add lectures')
        <button type="button" class="floating-add-btn" onclick="addNewQuestion()" title="@lang('l.add_new_question')" id="floatingAddBtn">
            <i class="fas fa-plus"></i>
        </button>
    @endcan
@endsection

@section('js')
<script>
window.testId = '{{ encrypt($test->id) }}';
window.questionStatus = @json($questionStatus);
window.modules = @json($modules);
window.availableParts = @json($modules);

window.Laravel = {
    csrfToken: '{{ csrf_token() }}'
};

window.routes = {
    questionsStore: '{{ route("dashboard.admins.tests-questions-store") }}',
    questionsUpdate: '{{ route("dashboard.admins.tests-questions-update") }}',
    questionsDelete: '{{ route("dashboard.admins.tests-questions-delete") }}'
};

window.testScoring = {
    part1: {
        easy: {{ (int) $test->module1_easy_score }},
        medium: {{ (int) $test->module1_medium_score }},
        hard: {{ (int) $test->module1_hard_score }},
    },
    part2: {
        easy: {{ (int) $test->module2_easy_score }},
        medium: {{ (int) $test->module2_medium_score }},
        hard: {{ (int) $test->module2_hard_score }},
    },
    part3: {
        easy: {{ (int) $test->module3_easy_score }},
        medium: {{ (int) $test->module3_medium_score }},
        hard: {{ (int) $test->module3_hard_score }},
    },
    part4: {
        easy: {{ (int) $test->module4_easy_score }},
        medium: {{ (int) $test->module4_medium_score }},
        hard: {{ (int) $test->module4_hard_score }},
    },
    part5: {
        easy: {{ (int) $test->module5_easy_score }},
        medium: {{ (int) $test->module5_medium_score }},
        hard: {{ (int) $test->module5_hard_score }},
    }
};

window.translations = {
    all_questions_added_already: '@lang("l.all_questions_added_already")',
    max_options_limit: '@lang("l.max_options_limit")',
    question_not_found: '@lang("l.question_not_found")',
    question_text_required: '@lang("l.question_text_required")',
    question_part_required: '@lang("l.question_part_required")',
    min_two_options_required: '@lang("l.min_two_options_required")',
    must_select_correct_answer: '@lang("l.must_select_correct_answer")',
    must_select_tf_answer: '@lang("l.must_select_tf_answer")',
    numeric_answer_required: '@lang("l.numeric_answer_required")',
    save_question_error: '@lang("l.save_question_error")',
    delete_question_error: '@lang("l.delete_question_error")',
    unknown_error: '@lang("l.unknown_error")',
    question_saved_successfully: '@lang("l.question_saved_successfully")',
    question_deleted_successfully: '@lang("l.question_deleted_successfully")',
    confirm_delete_question: '@lang("l.confirm_delete_question")',

    multiple_choice: '@lang("l.multiple_choice")',
    true_false: '@lang("l.true_false")',
    numeric_question: '@lang("l.numeric_question")',

    question_text: '@lang("l.question_text")',
    question_text_placeholder: '@lang("l.question_text_placeholder")',
    math_support_note: '@lang("l.math_support_note")',
    question_image_optional: '@lang("l.question_image_optional")',
    image_size_limit: '@lang("l.image_size_limit")',
    question_part: '@lang("l.question_part")',
    select_part: '@lang("l.select_part")',

    part_first: '@lang("l.first_part")',
    part_second: '@lang("l.second_part")',
    part_third: '@lang("l.third_part")',
    part_fourth: '@lang("l.fourth_part")',
    part_fifth: '@lang("l.fifth_part")',

    points_label: '@lang("l.points_label")',
    question_explanation_optional: '@lang("l.question_explanation_optional")',
    question_explanation_placeholder: '@lang("l.question_explanation_placeholder")',
    options: '@lang("l.options")',
    correct_answer: '@lang("l.correct_answer")',
    option_text_placeholder: '@lang("l.option_text_placeholder")',
    add_option: '@lang("l.add_option")',
    correct_answer_label: '@lang("l.correct_answer_label")',
    true: '@lang("l.true")',
    false: '@lang("l.false")',
    correct_numeric_answer: '@lang("l.correct_numeric_answer")',
    enter_correct_number: '@lang("l.enter_correct_number")',
    decimal_numbers_allowed: '@lang("l.decimal_numbers_allowed")',

    mcq: '@lang("l.mcq")',
    tf: '@lang("l.tf")',
    numeric: '@lang("l.numeric")',

    save: '@lang("l.save")',
    delete: '@lang("l.delete")',
    numbering_will_be_set: '@lang("l.numbering_will_be_set")',
    explanation_image_optional: '@lang("l.explanation_image_optional")',
    option_image_optional: '@lang("l.option_image_optional")',

    difficulty_label: '@lang("l.difficulty_label")',
    select_difficulty: '@lang("l.select_difficulty")',
    easy: '@lang("l.easy")',
    medium: '@lang("l.medium")',
    hard: '@lang("l.hard")',
    difficulty_required: '@lang("l.difficulty_required")',

    content_label: '@lang("l.content_label")',
    select_content: '@lang("l.select_content")',
    content_required: '@lang("l.content_required")',

    // احتياطي فقط لأي اعتماد قديم داخل js الحالي
    default_score: '{{ (int) ($test->module1_easy_score ?? 0) }}'
};
</script>

<script src="{{ asset('js/tests-questions.js') }}"></script>

<script>
(function () {
    function getModulesArray() {
        const src = window.modules || {};
        if (Array.isArray(src)) {
            return src;
        }

        return Object.keys(src).map(function (key) {
            const m = src[key] || {};
            if (!m.key) {
                m.key = key;
            }
            return m;
        });
    }

    function rebuildModuleSelects() {
        const modulesArr = getModulesArray();
        if (!modulesArr.length) return;

        const selects = document.querySelectorAll('select');
        selects.forEach(function (select) {
            const options = Array.from(select.options || []);
            const hasOldPattern = options.some(function (opt) {
                const txt = (opt.textContent || '').trim();
                return /^Module [1-5] \(0\/0\)$/.test(txt);
            });

            if (!hasOldPattern) {
                return;
            }

            const oldValue = select.value;

            while (select.firstChild) {
                select.removeChild(select.firstChild);
            }

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = (window.translations && window.translations.select_part)
                ? window.translations.select_part
                : 'Select Module';
            select.appendChild(placeholder);

            modulesArr.forEach(function (m) {
                if (!m) return;

                const max = m.max || 0;
                const current = m.current || 0;
                const remaining = (typeof m.remaining !== 'undefined') ? m.remaining : (max - current);
                const canAdd = (m.hasOwnProperty('can_add')) ? m.can_add : (remaining > 0);

                if (max <= 0) return;
                if (!canAdd) return;

                const opt = document.createElement('option');
                opt.value = m.key;
                opt.textContent = m.label + ' (' + current + '/' + max + ')';
                select.appendChild(opt);
            });

            if (oldValue) {
                const exists = Array.from(select.options).some(function (o) {
                    return o.value === oldValue;
                });

                if (exists) {
                    select.value = oldValue;
                }
            }
        });
    }

    function getQuestionRoot(element) {
        if (!element) return null;
        return element.closest('.question-card') || element.closest('.question-body') || element.closest('.question-item') || element.closest('.quiz-question') || element.parentElement;
    }

    function getScoreInput(root) {
        if (!root) return null;
        return root.querySelector('.question-score, .score-input, input[name="score"], input[placeholder*="Point"], input[placeholder*="point"]');
    }

    function getDifficultySelect(root) {
        if (!root) return null;
        return root.querySelector('.question-difficulty, select[name="difficulty"]');
    }

    function getPartSelect(root) {
        if (!root) return null;
        return root.querySelector('.question-part, .part-select, select[name="part"]');
    }

    function resolveScore(part, difficulty) {
        if (!part || !difficulty) return null;
        if (!window.testScoring || !window.testScoring[part]) return null;

        const score = window.testScoring[part][difficulty];
        return (typeof score !== 'undefined' && score !== null) ? score : null;
    }

    function applyAutoScore(root) {
        if (!root) return;

        const partSelect = getPartSelect(root);
        const difficultySelect = getDifficultySelect(root);
        const scoreInput = getScoreInput(root);

        if (!partSelect || !difficultySelect || !scoreInput) return;

        const part = partSelect.value;
        const difficulty = difficultySelect.value;
        const score = resolveScore(part, difficulty);

        if (score === null) return;

        scoreInput.value = score;
        // gscoreInput.setAttribute('readonly', 'readonly');
    }

    function initializeExistingQuestions() {
        document.querySelectorAll('.question-card, .question-body').forEach(function (root) {
            applyAutoScore(root);
        });
    }

    document.addEventListener('change', function (e) {
        const target = e.target;
        if (!target) return;

        if (
            target.classList.contains('question-difficulty') ||
            target.classList.contains('question-part') ||
            target.classList.contains('part-select') ||
            target.matches('select[name="difficulty"]') ||
            target.matches('select[name="part"]')
        ) {
            const root = getQuestionRoot(target);
            applyAutoScore(root);
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        rebuildModuleSelects();

        setTimeout(function () {
            initializeExistingQuestions();
        }, 100);

        const container = document.getElementById('questionsContainer');
        if (container && 'MutationObserver' in window) {
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    mutation.addedNodes.forEach(function (node) {
                        if (!(node instanceof HTMLElement)) return;

                        if (node.matches('.question-card, .question-body')) {
                            applyAutoScore(node);
                        } else {
                            const nestedCards = node.querySelectorAll
                                ? node.querySelectorAll('.question-card, .question-body')
                                : [];
                            nestedCards.forEach(function (nested) {
                                applyAutoScore(nested);
                            });
                        }
                    });
                });
            });

            observer.observe(container, {
                childList: true,
                subtree: true
            });
        }
    });

    setInterval(rebuildModuleSelects, 1000);
})();
</script>
@endsection