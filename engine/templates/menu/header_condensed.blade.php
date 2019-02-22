<ul class="nav navbar-nav">
@foreach($pages as $page)
<li @if($page['active']) class="active" @endif><a href="{{ getRealLinkURL('pages:'.$page['__id']) }}">{{$page['Header']}}</a></li>
@endforeach
</ul>