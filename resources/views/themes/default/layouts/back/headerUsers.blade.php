<li class="nxl-item nxl-caption">
    <label>-----</label>
</li>

<li class="nxl-item {{ request()->routeIs('dashboard.users.courses') ? 'active' : '' }}">
    <a href="{{ route('dashboard.users.courses') }}" class="nxl-link">
        <span class="nxl-micon"><i class="feather-book"></i></span>
        <span class="nxl-mtext">@lang('l.Courses')</span>
    </a>
</li>

<li class="nxl-item {{ request()->routeIs('dashboard.users.books') || request()->routeIs('dashboard.users.books-show') || request()->routeIs('dashboard.users.books-purchase') ? 'active' : '' }}">
    <a href="{{ route('dashboard.users.books') }}" class="nxl-link">
        <span class="nxl-micon"><i class="feather-book-open"></i></span>
        <span class="nxl-mtext">Books</span>
    </a>
</li>

<li class="nxl-item {{ request()->routeIs('dashboard.users.invoices') ? 'active' : '' }}">
    <a href="{{ route('dashboard.users.invoices') }}" class="nxl-link">
        <span class="nxl-micon"><i class="feather-file-text"></i></span>
        <span class="nxl-mtext">@lang('l.Invoices')</span>
    </a>
</li>