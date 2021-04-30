@if ( app('request')->input('translate') )
    <script type="text/javascript">
        var _jipt = [];
        _jipt.push(['project', 'imagery']);
    </script>
    <script type="text/javascript" src="//cdn.crowdin.com/jipt/jipt.js"></script>
    <script>var crowdin_lang = 'zu';</script>
@endif

@if( Auth::user() && ! Auth::user()->pending_approval )
    <script src="{{ mix('js/app.js') }}" defer></script>
@endif
