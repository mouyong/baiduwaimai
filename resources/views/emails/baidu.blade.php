@component('mail::message')
# Bug

{!! $trace !!}


Thanks,<br>
{{ config('app.name') }}
@endcomponent
