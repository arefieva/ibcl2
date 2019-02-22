@foreach($categories as $category)
<li class="dropdown menu-item" @if($category['active']) class="active" @endif>
	<a href="{{$category['Link']}}" @if(!emptyChildCatCategories($category['__id']))class="dropdown-toggle" data-toggle="dropdown"@endif>{{$category['Name']}}<i class="fa fa-chevron-right" aria-hidden="true"></i></a>
	@if(!emptyChildCatCategories($category['__id']))
		<ul class="dropdown-menu mega-menu">
			{!!displayChildCatCategories($category['__id'],"menu.categories-mega-menu")!!}
		</ul>
	@endif
</li>
@endforeach


