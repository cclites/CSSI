@component('mail::message')
# Employment Verification Request

Submitted by {{ $check->user->full_name }} {{ $check->user->email }}
<br>
<br>

{!! nl2br(e($check->content)) !!}

@component('mail::button', ['url' => secure_url('checks/'.$check->id)])
Details
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
