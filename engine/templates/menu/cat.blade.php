@foreach($cats as $cat)
<div class="vertical-menu-content hidden-xs">
    <div class="head">{{$cat['Name']}}</div>
	<ul class="vertical-menu-list">
	{!!displayChildCatCategories($cat['__id'], 'menu.categories-menu')!!}
	</ul>
</div>
@endforeach
<div class="vertical-menu-content news hidden-xs">
	<div class="head">Новости</div>
	<ul class="nav">
	{!!getNewsListForMenu()!!}
		<li class="menu-item">
			<div class="head all-news">
				<a href="{{$dir_prefix}}news">
					<img src="{{$dir_prefix}}images/list-news.png" alt="" width="18" height="15"/>
					<span class="all-news-decorated">
					Все новости
					</span>
				</a>
			</div>
	    </li><!-- /.menu-item -->
	</ul>	
</div>

<div class='mobile-menu accordeon hidden-lg hidden-md hidden-sm' id='menu-parent'>
    <button class='btn btn-primary pull-left js-showmenu' data-parent='#menu-parent' data-target="#navbar-cat">Каталог <i class='fa fa-bars'></i></button>
    <button class='btn btn-default pull-right js-showmenu' data-parent='#menu-parent' data-target="#navbar2">Меню <i class='fa fa-bars'></i></button>

    <div class="clearfix">
        <div id="navbar2" class="collapse js-panels">
            <nav class="navbar navbar-default clearfix">
                {!! displayHeaderMenu(2) !!}
            </nav>
        </div>
        <div id="navbar-cat" class="collapse js-panels">
            <nav class="navbar navbar-default clearfix">
                <ul class="nav navbar-nav" style="margin-right: 0px">
                    {!!displayChildCatCategories(0, "menu.categories-mega-menu")!!}
                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- ================================== TOP NAVIGATION : END ================================== -->