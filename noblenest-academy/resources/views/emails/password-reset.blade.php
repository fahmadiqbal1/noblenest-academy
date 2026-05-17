<h2>{{ __('emails.password_reset_heading') }}</h2>
<p>{{ __('emails.password_reset_intro') }}</p>
<p><a href="{{ url('/reset-password/' . $token . '?email=' . urlencode($email)) }}">{{ __('emails.password_reset_button') }}</a></p>
<p>{{ __('emails.password_reset_expiry') }}</p>
<p>{{ __('emails.password_reset_ignore') }}</p>
<p>{{ __('emails.password_reset_signature') }}</p>
