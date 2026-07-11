<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{route('index')}}" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                <img src="{{asset($settings['logo'])}}" alt="" class="logo logo-lg w-100" />
                <img src="{{asset($settings['favicon'])}}" alt="" class="logo logo-sm w-100" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                {{-- <li class="nxl-item nxl-caption">
                    <label>Navigation</label>
                </li> --}}
                <li class="nxl-item {{request()->is('home') ? 'active' : ''}}">
                    <a href="{{route('home')}}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">@lang('l.Dashboard')</span>
                    </a>
                </li>
                @if (!auth()->user()->roles()->exists()) <!-- users links -->
                    @include('themes.default.layouts.back.headerUsers')
                @else
                    <li class="nxl-item nxl-caption">
                        <label>-----</label>
                    </li>
                    @can('show lectures')
                        <li class="nxl-item {{request()->is('lectures') ? 'active' : ''}}">
                            <a href="{{route('dashboard.admins.lectures')}}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-video"></i></span>
                                <span class="nxl-mtext">@lang('l.Lectures')</span>
                            </a>
                        </li>
                    @endcan
                    @can('show lives')
                        <li class="nxl-item {{request()->is('lives') ? 'active' : ''}}">
                            <a href="{{route('dashboard.admins.lives')}}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-tv"></i></span>
                                <span class="nxl-mtext">@lang('l.Live')</span>
                            </a>
                        </li>
                    @endcan
                    @can('show tests')
                        <li class="nxl-item {{request()->is('tests') ? 'active' : ''}}">
                            <a href="{{route('dashboard.admins.tests')}}" class="nxl-link">
                                <span class="nxl-micon">
                                    <i class="feather-activity"></i>
                                </span>
                                <span class="nxl-mtext">@lang('l.Tests')</span>
                            </a>
                        </li>
                    @endcan
                    <li class="nxl-item nxl-caption">
                        <label>-----</label>
                    </li>
                    @can('show courses')
                        <li class="nxl-item {{request()->is('courses') ? 'active' : ''}}">
                            <a href="{{route('dashboard.admins.courses')}}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-book-open"></i></span>
                                <span class="nxl-mtext">@lang('l.Courses')</span>
                            </a>
                        </li>

                        <li class="nxl-item {{request()->is('admins/books*') ? 'active' : ''}}">
                            <a href="{{route('dashboard.admins.books')}}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-book"></i></span>
                                <span class="nxl-mtext">Books</span>
                            </a>
                        </li>
                    @endcan
                    @can('show levels')
                        <li class="nxl-item {{request()->is('levels') ? 'active' : ''}}">
                            <a href="{{route('dashboard.admins.levels')}}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-layers"></i></span>
                                <span class="nxl-mtext">@lang('l.Levels')</span>
                            </a>
                        </li>
                    @endcan
                    <li class="nxl-item nxl-caption">
                        <label>-----</label>
                    </li>
                    @can('show contact_us')
                        <li class="nxl-item {{request()->is('contact-us') ? 'active' : ''}}">
                            <a href="{{route('dashboard.admins.contacts')}}" class="nxl-link position-relative">
                                <span class="nxl-micon"><i class="feather-message-circle"></i></span>
                                <span class="nxl-mtext">@lang('l.Contact Us')</span>
                                @php $newRequests = \App\Models\Contact::where('status', 0)->count(); @endphp
                                @if ($newRequests > 0)
                                    <span
                                        class="notification-badge"
                                        style="
                                            position: absolute;
                                            top: 8px;
                                            right: 18px;
                                            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
                                            color: #fff;
                                            border-radius: 50%;
                                            min-width: 24px;
                                            height: 24px;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            font-size: 0.95em;
                                            font-weight: bold;
                                            box-shadow: 0 2px 8px rgba(255,75,43,0.15);
                                            border: 2px solid #fff;
                                            z-index: 2;
                                            animation: pulse-badge 1.2s infinite;
                                        ">
                                        {{ $newRequests }}
                                    </span>
                                    <style>
                                        @keyframes pulse-badge {
                                            0% { box-shadow: 0 0 0 0 rgba(255,75,43,0.5);}
                                            70% { box-shadow: 0 0 0 10px rgba(255,75,43,0);}
                                            100% { box-shadow: 0 0 0 0 rgba(255,75,43,0);}
                                        }
                                    </style>
                                @endif
                            </a>
                        </li>
                    @endcan
                    @can('show students')
                        <li class="nxl-item {{request()->is('customers') ? 'active' : ''}}">
                            <a href="{{route('dashboard.admins.customers')}}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-users"></i></span>
                                <span class="nxl-mtext">@lang('l.Customers')</span>
                            </a>
                        </li>
                    @endcan
                    @can('show invoices')
                        <li class="nxl-item {{request()->is('invoices') ? 'active' : ''}}">
                            <a href="{{route('dashboard.admins.invoices')}}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-file-text"></i></span>
                                <span class="nxl-mtext">@lang('l.Invoices')</span>
                            </a>
                        </li>
                    @endcan
                    @can('show users')
                        <li class="nxl-item {{request()->is('users') ? 'active' : ''}}">
                            <a href="{{route('dashboard.admins.users')}}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-users"></i></span>
                                <span class="nxl-mtext">@lang('l.Users')</span>
                            </a>
                        </li>
                    @endcan
                    @can('show roles')
                        <li class="nxl-item" {{request()->is('roles') ? 'active' : ''}}>
                            <a href="{{route('dashboard.admins.roles')}}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-lock"></i></span>
                                <span class="nxl-mtext">@lang('l.Roles')</span>
                            </a>
                        </li>
                    @endcan
                    <li class="nxl-item nxl-caption">
                        <label>-----</label>
                    </li>
                    @can('show settings')
                        <li class="nxl-item {{request()->is('settings') ? 'active' : ''}}">
                            <a href="{{route('dashboard.admins.settings')}}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-settings"></i></span>
                                <span class="nxl-mtext">@lang('l.Settings')</span>
                            </a>
                        </li>
                        <li class="nxl-item {{request()->is('payments') ? 'active' : ''}}">
                            <a href="{{route('dashboard.admins.payments')}}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                                <span class="nxl-mtext">@lang('l.Payment Gateways')</span>
                            </a>
                        </li>
                    @endcan
                @endif
                <li class="nxl-item nxl-caption">
                    <label>-----</label>
                </li>
                <li class="nxl-item {{ Route::is('dashboard.profile') ? 'active' : '' }}">
                    <a href="{{route('dashboard.profile')}}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-user"></i></span>
                        <span class="nxl-mtext">@lang('l.Account')</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>