@extends('themes.default.layouts.back.master')

@section('title')
    @lang('l.Invoice Details')
@endsection

@section('content')
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">@lang('l.Invoice Details')</h4>
            </div>
            <div>
                <a href="{{ route('dashboard.admins.invoices') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>@lang('l.Back to List')
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title mb-4">@lang('l.Invoice Information')</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">@lang('l.Invoice ID'):</td>
                                <td>#{{ $invoice->id }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('l.Category'):</td>
                                <td>{!! $invoice->category_badge !!}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('l.Type'):</td>
                                <td>{!! $invoice->type_badge !!}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('l.Item'):</td>
                                <td>{{ $invoice->type_value_display }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('l.Amount'):</td>
                                <td><span class="fw-bold text-primary">{{ $invoice->amount }} @lang('l.currency')</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('l.Status'):</td>
                                <td>{!! $invoice->status_badge !!}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('l.Payment ID'):</td>
                                <td>{{ $invoice->pid ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('l.Created At'):</td>
                                <td>{{ $invoice->created_at ? $invoice->created_at->format('Y-m-d H:i:s') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('l.Updated At'):</td>
                                <td>{{ $invoice->updated_at ? $invoice->updated_at->format('Y-m-d H:i:s') : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="card-title mb-4">@lang('l.Student Information')</h5>
                        @if($invoice->student)
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">@lang('l.Name'):</td>
                                    <td>{{ $invoice->student->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('l.Email'):</td>
                                    <td>{{ $invoice->student->email }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('l.Phone'):</td>
                                    <td>{{ $invoice->student->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('l.Created At'):</td>
                                    <td>{{ $invoice->student->created_at ? $invoice->student->created_at->format('Y-m-d H:i:s') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('l.Level'):</td>
                                    <td>{{ $invoice->student->level->name ?? '-' }}</td>
                                </tr>
                            </table>
                        @else
                            <div class="alert alert-warning">
                                @lang('l.Student information not available')
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="card-title mb-4">@lang('l.Item Details')</h5>
                        @if($invoice->type === 'course' && $invoice->course)
                            <div class="card">
                                <div class="card-body">
                                    <h6>@lang('l.Course Information')</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">@lang('l.Course Name'):</td>
                                            <td>{{ $invoice->course->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.Level'):</td>
                                            <td>{{ $invoice->course->level->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.Price'):</td>
                                            <td>{{ $invoice->course->price ?? 0 }} @lang('l.currency')</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @elseif($invoice->type === 'single' && $invoice->category === 'lecture' && $invoice->lecture)
                            <div class="card">
                                <div class="card-body">
                                    <h6>@lang('l.Lecture Information')</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">@lang('l.Lecture Title'):</td>
                                            <td>{{ $invoice->lecture->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.Course'):</td>
                                            <td>{{ $invoice->lecture->course->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.Type'):</td>
                                            <td>{{ ucfirst($invoice->lecture->type ?? 'free') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.Price'):</td>
                                            <td>{{ $invoice->lecture->price ?? 0 }} @lang('l.currency')</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @elseif($invoice->type === 'single' && $invoice->category === 'quiz' && $invoice->lectureAssignment)
                            <div class="card">
                                <div class="card-body">
                                    <h6>@lang('l.Quiz Information')</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">@lang('l.Quiz Title'):</td>
                                            <td>{{ $invoice->lectureAssignment->title }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.Lecture'):</td>
                                            <td>{{ $invoice->lectureAssignment->lecture->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.Course'):</td>
                                            <td>{{ $invoice->lectureAssignment->lecture->course->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.Time Limit'):</td>
                                            <td>{{ $invoice->lectureAssignment->time_limit ? $invoice->lectureAssignment->time_limit . ' minutes' : 'No limit' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.Questions Count'):</td>
                                            <td>{{ $invoice->lectureAssignment->questions_count ?? 0 }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @elseif($invoice->type === 'single' && $invoice->category === 'book' && $invoice->book)
                            <div class="card">
                                <div class="card-body">
                                    <h6>Book Information</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Book Title:</td>
                                            <td>{{ $invoice->book->title }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.Course'):</td>
                                            <td>{{ $invoice->book->course->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">@lang('l.Price'):</td>
                                            <td>{{ $invoice->book->price ?? 0 }} @lang('l.currency')</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Access:</td>
                                            <td>{{ $invoice->book->access_duration_text }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @elseif($invoice->type === 'month')
                            <div class="card">
                                <div class="card-body">
                                    <h6>@lang('l.Monthly Subscription')</h6>
                                    <p class="mb-0">@lang('l.Month'): {{ $invoice->type_value }}</p>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                @lang('l.Item details not available')
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            @can('delete invoices')
                                <form method="POST"
                                      action="{{ route('dashboard.admins.invoices-delete') }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this invoice? This may affect access records.')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{ encrypt($invoice->id) }}">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-1"></i>@lang('l.Delete')
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
