<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div id="navbar" class="navbar-collapse">
            <ul class="nav navbar-nav">
            @foreach($pages as $page)
            <li @if($page['active']) class="active" @endif><a href="{{ getRealLinkURL('pages:'.$page['__id']) }}">{{$page['Header']}}</a></li>
            @endforeach
            </ul>
        </div>
    </div>
</nav>
