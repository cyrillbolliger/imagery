@component('mail::message')
# @lang('Pending Approval')

@lang('Hello Admin')


@lang(':firstName :lastName (:email) from :groups just applied for a login.', [
'firstName' => e($applicant->first_name),
'lastName'  => e($applicant->last_name),
'email'     => e($applicant->email),
'groups'    => e(implode(', ', $groups)),
])


@lang('Verify, that this person is permitted to have an account, before approving the request for an account.')

@lang("If approving, don't forget to grant privileges to some groups.")


@component('mail::button', ['url' => url("/admin/users/{$applicant->id}?activation={$applicant->activation_token}")])
@lang('Grant access to :firstName :lastName', [
    'firstName' => e($applicant->first_name),
    'lastName'  => e($applicant->last_name)
])
@endcomponent


@lang('Have an excellent day,')
<br>
{{ config('app.name') }}
@endcomponent
