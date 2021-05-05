@component('mail::message')
Hello {{{ config('app.feedback_recipients') }}}


{{ e($user->first_name) }} {{e($user->last_name)}} \<{{ e($user->email) }}\>
sent you some feedback for {{ config('app.name') }}. See below.

Have an excellent day
<br>
{{ config('app.name') }}


# Feedback

## Message

{{ e($message) }}


## Meta

* User agent: {{ e($userAgent) }}
* App language: {{ e($user->lang) }}
* User managed by: {{ e($user->managedBy->name) }}
* Is admin: {{ $user->is_admin ? 'yes' : 'no' }}
@endcomponent
