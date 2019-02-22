<nav>
  <ul class="pagination">
  	@if($prev)
  		<li><a href="./?p={{$prevlink}}{{$params}}"><span>&laquo;</span></a></li>
  	@endif
  	@foreach($pages as $page)
    	<li @if($page['active']) class="active" @endif><a href="./?p={{$page['pos']}}{{$params}}">{{$page['pos']}}</a></li>
    @endforeach
  	@if($next)
  		<li><a href="./?p={{$nextlink}}{{$params}}"><span>&raquo;</span></a></li>
  	@endif
  </ul>
</nav>