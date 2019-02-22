@extends('site.layout')
@section('main')
<main class="container">
    
        <div class="col-xs-12 sidemenu-holder">
			{!! displayCatMenu() !!}
		</div><!-- /.sidemenu-holder -->
		<div class="col-xs-12 hidden-xs hidden-sm homebanner-holder">
			{!! displaySlider() !!}		
		</div><!-- /.homebanner-holder -->
		
		<div class="col-xs-12 col-sm-12 home-page-tabs-holder">
			{!!displayHitsIndex() !!}
		</div><!-- /.home-page-tabs-holder -->
		
		<div class="col-xs-12 col-sm-12 col-md-9 top-brands-holder">
		{!!displayBrandList() !!}
		</div><!-- /.top-brands-holder -->
		<div class="col-xs-12 col-sm-12 col-md-9 about-us-holder">
			{!!displayAboutPage()!!}
		</div><!-- /.about-us-holder -->
        
    
</main>

@stop