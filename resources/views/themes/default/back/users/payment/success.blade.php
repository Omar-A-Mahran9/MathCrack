@extends('themes.default.layouts.back.student-master')

@section('title')
    @lang('l.payment_successful')
@endsection

@section('css')
    <style>
        .success-card {
            border: none;
            box-shadow: 0 0 30px rgba(40,167,69,0.2);
            border-radius: 20px;
            overflow: hidden;
        }

        .success-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: successPulse 2s infinite;
        }

        @keyframes successPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .success-body {
            padding: 2rem;
        }

        .invoice-details {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.1rem;
            color: #28a745;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 500;
            flex: 1;
        }

        .btn-outline-success-custom {
            border: 2px solid #28a745;
            color: #28a745;
            background: transparent;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            flex: 1;
        }

        .btn-success-custom:hover,
        .btn-outline-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40,167,69,0.3);
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .success-header {
                padding: 2rem 1rem;
            }

            .success-body {
                padding: 1.5rem;
            }
        }
    </style>
@endsection

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12">
                <div class="card success-card">
                    <!-- Header -->
                    <div class="success-header">
                        <div class="success-icon">
                            <i class="fas fa-check fa-3x"></i>
                        </div>
                        <h2 class="mb-2">@lang('l.payment_successful')</h2>
                        <p class="mb-0 opacity-90">@lang('l.thank_you_for_purchase')</p>
                    </div>

                    <!-- Body -->
                    <div class="success-body">
                        <div class="text-center mb-4">
                            <h5 class="text-success">@lang('l.payment_completed_successfully')</h5>
                            <p class="text-muted">@lang('l.access_granted_message')</p>
                        </div>

                        <!-- تفاصيل الفاتورة -->
                        <div class="invoice-details">
                            <h6 class="mb-3">
                                <i class="fas fa-receipt me-2"></i>@lang('l.invoice_details')
                            </h6>

                            <div class="detail-row">
                                <span>@lang('l.invoice_number')</span>
                                <span class="fw-bold">#{{ $invoice->id }}</span>
                            </div>

                            <div class="detail-row">
                                <span>@lang('l.payment_id')</span>
                                <span class="fw-bold">{{ $invoice->pid }}</span>
                            </div>

                            <div class="detail-row">
                                <span>@lang('l.item')</span>
                                <span>{{ $invoice->type_value_display }}</span>
                            </div>

                            <div class="detail-row">
                                <span>@lang('l.type')</span>
                                <span>
                                    @if($invoice->category == 'book')
                                        Book
                                    @elseif($invoice->category == 'quiz')
                                        @if($invoice->type == 'single')
                                            @lang('l.single_test')
                                        @elseif($invoice->type == 'course')
                                            @lang('l.all_course_tests')
                                        @endif
                                    @else
                                        @if($invoice->type == 'single')
                                            @lang('l.single_lecture')
                                        @elseif($invoice->type == 'course')
                                            @lang('l.full_course')
                                        @elseif($invoice->type == 'month')
                                            اشتراك شهري{{ $invoice->course ? ' لكورس ' . $invoice->course->name : ' عام' }}
                                        @endif
                                    @endif
                                </span>
                            </div>

                            <div class="detail-row">
                                <span>@lang('l.payment_date')</span>
                                <span>{{ $invoice->updated_at->format('Y-m-d H:i') }}</span>
                            </div>

                            <div class="detail-row">
                                <span>@lang('l.amount_paid')</span>
                                <span>{{ number_format($invoice->amount, 2) }} @lang('l.currency')</span>
                            </div>
                        </div>

                        <!-- أزرار العمل -->
                        <div class="action-buttons">
                            @if($invoice->category == 'book')
                                @if($invoice->book)
                                    <a href="{{ route('books.reader.read', ['book' => $invoice->book->slug]) }}"
                                       class="btn btn-success-custom">
                                        <i class="fas fa-book-open me-2"></i>Read Book
                                    </a>
                                @else
                                    <a href="{{ route('dashboard.users.books') }}"
                                       class="btn btn-success-custom">
                                        <i class="fas fa-book me-2"></i>View Books
                                    </a>
                                @endif
                            @elseif($invoice->category == 'quiz')
                                @if($invoice->type == 'single')
                                    @php
                                        $test = \App\Models\Test::find($invoice->type_value);
                                    @endphp
                                    @if($test)
                                        <a href="{{ route('dashboard.users.tests.show', $test->id) }}"
                                           class="btn btn-success-custom">
                                            <i class="fas fa-edit-3 me-2"></i>@lang('l.start_test')
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('dashboard.users.tests') }}"
                                       class="btn btn-success-custom">
                                        <i class="fas fa-edit-3 me-2"></i>@lang('l.view_tests')
                                    </a>
                                @endif
                            @else
                                @if($invoice->type == 'single' && $invoice->lecture)
                                    <a href="{{ route('dashboard.users.courses-lectures-show', ['id' => encrypt($invoice->lecture->id)]) }}"
                                       class="btn btn-success-custom">
                                        <i class="fas fa-play me-2"></i>@lang('l.watch_lecture')
                                    </a>
                                @elseif($invoice->type == 'course' && $invoice->course)
                                    <a href="{{ route('dashboard.users.courses-lectures', ['id' => encrypt($invoice->course->id)]) }}"
                                       class="btn btn-success-custom">
                                        <i class="fas fa-book me-2"></i>@lang('l.view_course')
                                    </a>
                                @elseif($invoice->type == 'month' && $invoice->course)
                                    <a href="{{ route('dashboard.users.courses-lectures', ['id' => encrypt($invoice->course->id)]) }}"
                                       class="btn btn-success-custom">
                                        <i class="fas fa-calendar me-2"></i>عرض المحاضرات الشهرية للكورس
                                    </a>
                                @else
                                    <a href="{{ route('dashboard.users.courses') }}"
                                       class="btn btn-success-custom">
                                        <i class="fas fa-home me-2"></i>@lang('l.go_to_courses')
                                    </a>
                                @endif
                            @endif

                            <a href="{{ route('dashboard.users.invoices') }}"
                               class="btn btn-outline-success-custom">
                                <i class="fas fa-file-invoice me-2"></i>@lang('l.view_invoices')
                            </a>
                        </div>

                        <!-- معلومات إضافية -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                @lang('l.invoice_email_sent_message')
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
