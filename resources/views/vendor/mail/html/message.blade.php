@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
<img src="{{ asset('images/logo-dark-400.png') }}" alt="@lang('Logo')" width="200">

&nbsp;

{!! config('app.imprint_addr') !!}

@endcomponent
@endslot
@endcomponent
