<x-mail::message>
{{ __('Hello,') }}

{{ __('Your one-time verification code is:') }}

<x-mail::panel>
<strong style="letter-spacing: 0.08em">{{ $code }}</strong>
</x-mail::panel>

{{ __('If you did not request this code, ignore this email and consider changing your password.') }}

<br>
{{ __('Thanks,') }}<br>
{{ config('app.name') }}
</x-mail::message>
