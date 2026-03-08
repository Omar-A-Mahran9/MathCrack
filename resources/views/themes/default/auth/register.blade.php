{{-- resources/views/themes/default/auth/register.blade.php --}}

@extends('themes.default.auth.layout')

@section('title')
    @lang('l.Register')
@endsection

@section('page-css')
<style>
  .auth-minimal-wrapper{ background:#f4f6fb !important; }

  body,
  main,
  .auth-minimal-inner,
  .minimal-card-wrapper,
  .card,
  .card-body{ background:transparent !important; }

  .card{ background:#ffffff !important; }

  .mathcrack-original-header{
    text-align:center;
    margin:0 auto 20px auto;
    padding-top:20px;
  }

  .mathcrack-logo-img{
    height:60px;
    width:auto;
    margin-bottom:10px;
    object-fit:contain;
  }

  .mathcrack-original-title{
    font-size:28px;
    font-weight:800;
    color:#1e293b;
    margin:0 0 4px 0;
    letter-spacing:-0.5px;
  }

  .prof-name-original{
    font-size:14px;
    color:#64748b;
    margin:0;
    font-weight:500;
  }

  .card-body{ padding-top:5px !important; }

  .fs-20{
    margin-top:0 !important;
    margin-bottom:25px !important;
    font-size:22px !important;
    color:#1e293b;
  }

  .wd-50{ display:none !important; }

  .error-message{ color:red; }

  .iti{ width:100%; }
  .iti__country{ direction:ltr; }
  .iti__country-list{ left:0; }
  #phone{ text-align:left; }
  .iti__selected-flag{ direction:ltr; }

  .form-label{
    font-weight:600;
    margin-bottom:6px;
    display:inline-block;
  }

  .password-group{ direction:ltr; }
  .password-group .form-control{
    border-right:0;
    padding-right:14px;
  }
  .password-group .input-group-text{
    background:transparent;
    cursor:pointer;
    user-select:none;
    border-left:0;
    min-width:46px;
    justify-content:center;
  }
  .password-group .input-group-text i{
    font-size:18px;
    color:#6c757d;
    line-height:1;
  }

  .pass-help{
    font-size:13px;
    color:#6c757d;
    margin-top:6px;
  }
  .pass-error{
    display:none;
    font-size:13px;
    color:#dc3545;
    margin-top:6px;
  }

  .form-control{ border:1px solid #e5e7eb; }
  .form-control:focus{
    border-color:#3b82f6;
    box-shadow:0 0 0 3px rgba(59,130,246,0.1);
  }

  .name-row{
    display:flex;
    gap:15px;
    margin-bottom:1rem;
  }
  .name-col{ flex:1; }

  @media (max-width:576px){
    .name-row{ flex-direction:column; gap:1rem; }
  }
</style>
@endsection

@section('content')
@php
  $mockAction = old('action', $mockFlow['action'] ?? request()->query('action', ''));
  $mockRedirect = old('redirect', $mockFlow['redirect'] ?? request()->query('redirect', ''));
  $mockCourse = old('course', $mockFlow['course'] ?? request()->query('course', 1));
  $mockTest = old('test', $mockFlow['test'] ?? request()->query('test', 'mock'));
@endphp

<main class="auth-minimal-wrapper">
  <div class="auth-minimal-inner">
    <div class="minimal-card-wrapper">
      <div class="card mb-4 mt-5 mx-4 mx-sm-0">

        <div class="mathcrack-original-header">
          <img
            src="{{ asset('logo.png') }}"
            alt="MathCrack Logo"
            class="mathcrack-logo-img"
            onerror="this.style.display='none'; document.getElementById('text-logo').style.display='block';"
          >

          <div id="text-logo" style="display:none;">
            <h1 class="mathcrack-original-title">MathCrack</h1>
            <p class="prof-name-original">Prof. Ahmed Omar</p>
          </div>
        </div>

        <div class="card-body p-sm-5" style="padding-top:15px !important;">
          <h2 class="fs-20 fw-bolder mb-4">@lang('l.Register')</h2>

          <form id="formAuthentication" class="w-100 mt-4 pt-2" method="post" action="{{ route('register') }}">
            @csrf

            <input type="hidden" name="action" value="{{ $mockFlow['action'] ?? '' }}">
            <input type="hidden" name="redirect" value="{{ $mockFlow['redirect'] ?? '' }}">
            <input type="hidden" name="test" value="{{ $mockFlow['test'] ?? 'mock' }}">
            <div class="name-row">
              <div class="name-col">
                <label class="form-label">@lang('l.First Name')</label>
                <input
                  type="text"
                  class="form-control"
                  id="firstname"
                  name="firstname"
                  value="{{ old('firstname') }}"
                  placeholder="@lang('l.Enter your first name')"
                  autofocus
                  required
                >
                <x-input-error class="mt-2 error-message" :messages="$errors->get('firstname')" />
              </div>

              <div class="name-col">
                <label class="form-label">@lang('l.Last Name')</label>
                <input
                  type="text"
                  class="form-control"
                  id="lastname"
                  name="lastname"
                  value="{{ old('lastname') }}"
                  placeholder="@lang('l.Enter your last name')"
                  required
                >
                <x-input-error class="mt-2 error-message" :messages="$errors->get('lastname')" />
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label">@lang('l.Email')</label>
              <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="@lang('l.Enter your email')"
                required
              >
              <x-input-error class="mt-2 error-message" :messages="$errors->get('email')" />
            </div>

            <div class="mb-4">
              <label class="form-label">@lang('l.Phone')</label>

              <input
                type="hidden"
                id="phone_code"
                name="phone_code"
                value="{{ old('phone_code', '20') }}"
                required
              >

              <div class="input-group">
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  class="form-control multi-steps-mobile"
                  value="{{ old('phone') }}"
                  required
                >
              </div>

              <x-input-error class="mt-2 error-message" :messages="$errors->get('phone')" />
              <x-input-error class="mt-2 error-message" :messages="$errors->get('phone_code')" />
            </div>

            <div class="mb-4">
              <label class="form-label">@lang('l.Level')</label>
              <select id="level_id" class="form-control" name="level_id" required>
                <option value="">Select your level</option>
                @foreach ($levels as $level)
                  <option value="{{ $level->id }}" {{ (string)old('level_id') === (string)$level->id ? 'selected' : '' }}>
                    {{ $level->name }}
                  </option>
                @endforeach
              </select>

              <x-input-error class="mt-2 error-message" :messages="$errors->get('level_id')" />
            </div>

            <input type="text" name="fax_number" style="display:none">

            <div class="mb-4">
              <label class="form-label">@lang('l.Password')</label>

              <div class="input-group password-group">
                <input
                  type="password"
                  id="password"
                  class="form-control"
                  name="password"
                  minlength="8"
                  placeholder="Enter your password"
                  required
                >
                <span class="input-group-text password-toggle" data-target="password" role="button" tabindex="0" aria-label="Toggle password">
                  <i class="feather-eye"></i>
                </span>
              </div>

              <div class="pass-help">Minimum 8 characters</div>
              <x-input-error class="mt-2 error-message" :messages="$errors->get('password')" />
            </div>

            <div class="mb-4">
              <label class="form-label">Confirm Password</label>

              <div class="input-group password-group">
                <input
                  type="password"
                  id="password_confirmation"
                  name="password_confirmation"
                  class="form-control"
                  minlength="8"
                  placeholder="Enter your password again"
                  required
                >
                <span class="input-group-text password-toggle" data-target="password_confirmation" role="button" tabindex="0" aria-label="Toggle confirm password">
                  <i class="feather-eye"></i>
                </span>
              </div>

              <div id="passMismatch" class="pass-error">Password and confirm password do not match</div>
            </div>

            @if (!empty($settings['recaptcha']))
              <div class="g-recaptcha mb-4" data-sitekey="{{ config('app.recaptcha.key') }}"></div>
              <input type="hidden" name="disabled-submit" id="disabled-submit">
              <x-input-error class="mt-2 error-message" :messages="$errors->get('g-recaptcha-response')" />
            @endif

            <div class="mt-5">
              <button class="btn btn-lg btn-primary w-100" id="submitButton" type="submit">
                @lang('l.Sign up')
              </button>
            </div>
          </form>

          <div class="mt-5 text-muted">
            <span>@lang('l.Already have an account?')</span>
            <a href="{{ route('login') }}" class="fw-bold">
              <span>@lang('l.Sign in instead')</span>
            </a>
          </div>

          @php
            $activeLogins = collect([
              $settings['facebookLogin'] ?? null,
              $settings['googleLogin'] ?? null,
              $settings['twitterLogin'] ?? null,
            ])->filter()->count();
          @endphp

          <div class="w-100 mt-5 text-center mx-auto">
            @if (($settings['facebookLogin'] ?? false) || ($settings['googleLogin'] ?? false) || ($settings['twitterLogin'] ?? false))
              <div class="mb-4 border-bottom position-relative">
                <span class="small py-1 px-3 text-uppercase text-muted bg-white position-absolute translate-middle">
                  @lang('l.or')
                </span>
              </div>
            @endif

            <div class="d-flex align-items-center justify-content-center gap-2">
              @if ($settings['facebookLogin'] ?? false)
                <a href="{{ route('auth.facebook') }}" class="btn btn-light-brand flex-fill" title="Login with Facebook">
                  <i class="feather-facebook"></i>
                </a>
              @endif

              @if ($settings['googleLogin'] ?? false)
                <a href="{{ route('auth.google') }}" class="btn btn-light-brand flex-fill" title="Login with Google">
                  <i class="fa fa-google"></i>
                </a>
              @endif

              @if ($settings['twitterLogin'] ?? false)
                <a href="{{ route('auth.twitter') }}" class="btn btn-light-brand flex-fill" title="Login with Twitter">
                  <i class="feather-twitter"></i>
                </a>
              @endif
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</main>
@endsection

@section('page-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<script>
$(document).ready(function () {
  var phoneInput = document.querySelector("#phone");
  var phoneCodeInput = document.querySelector("#phone_code");

  var iti = window.intlTelInput(phoneInput, {
    initialCountry: "eg",
    geoIpLookup: function (callback) {
      $.get('https://ipinfo.io', function () {}, "jsonp").always(function (resp) {
        var countryCode = (resp && resp.country) ? resp.country : "eg";
        callback(countryCode);
      });
    },
    nationalMode: false,
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
    separateDialCode: true,
    formatOnDisplay: true,
    preferredCountries: ["eg", "sa", "ae"]
  });

  function syncDialCode() {
    var dial = iti.getSelectedCountryData().dialCode || "";
    if (phoneCodeInput) phoneCodeInput.value = String(dial);
  }

  if (phoneCodeInput && phoneCodeInput.value) {
    var raw = String(phoneCodeInput.value).replace("+", "");
    phoneCodeInput.value = raw;
  } else {
    syncDialCode();
  }

  phoneInput.addEventListener("countrychange", function () {
    syncDialCode();
  });

  @if(old('phone'))
    setTimeout(function() {
      var oldPhone = "{{ old('phone') }}";
      if (!oldPhone) return;

      oldPhone = oldPhone.replace(/\s+/g, '').replace('+', '');

      var oldCode = "{{ old('phone_code', '') }}";
      oldCode = String(oldCode).replace('+', '');

      if (oldCode && oldPhone.startsWith(oldCode)) {
        oldPhone = oldPhone.substring(oldCode.length);
      }

      phoneInput.value = oldPhone;
    }, 300);
  @endif

  function togglePassword(targetId, el) {
    var field = document.getElementById(targetId);
    if (!field) return;

    var icon = el.querySelector('i');

    if (field.type === 'password') {
      field.type = 'text';
      icon.classList.remove('feather-eye');
      icon.classList.add('feather-eye-off');
    } else {
      field.type = 'password';
      icon.classList.remove('feather-eye-off');
      icon.classList.add('feather-eye');
    }
  }

  document.querySelectorAll('.password-toggle').forEach(function (el) {
    el.addEventListener('click', function () {
      togglePassword(this.getAttribute('data-target'), this);
    });

    el.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        togglePassword(this.getAttribute('data-target'), this);
      }
    });
  });

  function checkPasswordMatch() {
    var pass = document.getElementById('password').value;
    var conf = document.getElementById('password_confirmation').value;
    var msg = document.getElementById('passMismatch');

    if (!conf) {
      msg.style.display = 'none';
      return true;
    }

    if (pass !== conf) {
      msg.style.display = 'block';
      return false;
    }

    msg.style.display = 'none';
    return true;
  }

  document.getElementById('password').addEventListener('input', checkPasswordMatch);
  document.getElementById('password_confirmation').addEventListener('input', checkPasswordMatch);

  document.getElementById('formAuthentication').addEventListener('submit', function (e) {
    if (!checkPasswordMatch()) e.preventDefault();
    syncDialCode();
  });
});
</script>

@if (!empty($settings['recaptcha']))
<script>
window.addEventListener('load', function () {
  var el = document.querySelector('#g-recaptcha-response');
  if (el) el.setAttribute('required', 'required');
});
</script>
@endif
@endsection