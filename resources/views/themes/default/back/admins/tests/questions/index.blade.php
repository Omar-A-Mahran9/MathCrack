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
            console.log('✅ MathJax loaded successfully for test questions');
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">
                        @lang('l.test_questions') -
                        <span class="text-primary">{{ $test->name }}</span>
                    </h4>
                    <p class="text-muted mb-0">{{ $test->course->name ?? '' }}</p>
                </div>

                <div>
                    <a href="{{ route('dashboard.admins.tests-show', ['id' => encrypt($test->id)]) }}" class="btn btn-info waves-effect waves-light">
                        <i class="fas fa-eye me-2"></i>
                        @lang('l.view_test')
                    </a>

                    <a href="{{ route('dashboard.admins.tests-preview', ['id' => encrypt($test->id)]) }}" class="btn btn-success waves-effect waves-light" target="_blank">
                        <i class="fas fa-eye me-2"></i>
                        @lang('l.preview_as_student')
                    </a>

                    <a href="{{ route('dashboard.admins.tests') }}" class="btn btn-secondary waves-effect waves-light">
                        <i class="fas fa-arrow-left me-2"></i>
                        @lang('l.back_to_list')
                    </a>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex flex-row flex-wrap justify-content-center w-100">
                                @php
                                    $activeModules = array_filter($modules, fn($m) => ($m['max'] ?? 0) > 0);
                                @endphp

                                @foreach($activeModules as $partKey => $module)
                                    <div class="mx-3 mb-2 text-center" style="min-width: 120px;">
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
                <div class="alert alert-warning">
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
                <div class="alert alert-success">
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

    default_score: '{{ $test->default_question_score }}'
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

    document.addEventListener('DOMContentLoaded', rebuildModuleSelects);
    setInterval(rebuildModuleSelects, 1000);
})();
</script>
@endsection