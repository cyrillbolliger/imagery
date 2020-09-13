@component('mail::message')
@lang('Hello :firstName', ['firstName' => e($user->first_name)])


@lang(
"You've been invited by :firstName :lastName to use :app. It's a super simple tool to create images in the corporate design of the GREENS.",
[
    'app' => config('app.name'),
    'firstName' => e($manager->first_name),
    'lastName' => e($manager->last_name),
]) @lang("You can use it with the GREEN login (same as for the chat).")


@component('mail::button', ['url' => url('home')])
    @lang('Try the GREEN graphics designer')
@endcomponent


@lang("If you don't have a login yet, you may register yourself. Just click 'register' on the login screen and subscribe yourself with :email.", ['email' => e($user->email)])


@lang('Have an excellent day,')
<br>
{{ config('app.name') }}
@endcomponent
