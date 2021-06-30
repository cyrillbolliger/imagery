@component('mail::message')
# @lang('pending-approval.title')

@lang('pending-approval.hello')


@lang('pending-approval.who', [
'firstName' => e($applicant->first_name),
'lastName'  => e($applicant->last_name),
'email'     => e($applicant->email),
'groups'    => e(implode(', ', $groups)),
])


@lang('pending-approval.instruction1')

@lang('pending-approval.instruction2')


@component('mail::button', ['url' => url("/admin/users/{$applicant->id}?activation={$applicant->activation_token}")])
@lang('pending-approval.button', [
    'firstName' => e($applicant->first_name),
    'lastName'  => e($applicant->last_name)
])
@endcomponent


@lang('pending-approval.bye')
<br>
{{ config('app.name') }}
@endcomponent
