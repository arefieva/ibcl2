<ul>
    @foreach($cats as $cat)
    <li @if($cat['active']) class="active" @endif><a href="{{$cat['link']}}">{{$cat['Name']}}</a></li>
    @endforeach
</ul>