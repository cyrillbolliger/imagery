@component('mail::message')
@lang('account-created.hello', ['firstName' => e($user->first_name)])


@lang('account-created.intro',
[
    'app' => config('app.name'),
    'firstName' => e($manager->first_name),
    'lastName' => e($manager->last_name),
]) @lang('account-created.sso')


@component('mail::button', ['url' => route('home')])
    @lang('account-created.button')
@endcomponent


@lang('account-created.register', ['email' => e($user->email)])


@lang('account-created.bye')
<br>
{{ config('app.name') }}
@endcomponent
