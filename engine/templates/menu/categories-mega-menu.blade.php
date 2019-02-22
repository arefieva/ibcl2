@foreach($categories as $category)
	<li><a href="{{$category['Link']}}">{{$category['Name']}}</a></li>
@endforeach


