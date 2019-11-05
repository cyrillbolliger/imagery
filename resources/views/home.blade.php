@extends('layouts.app')

@section('content')
    <script>
        window.user = {!! json_encode(Auth::user()) !!}
    </script>
    <div id="app">

    </div>
@endsection
