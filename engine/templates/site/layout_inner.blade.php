@extends('site.layout')
@section('main')
<main class="container inner">
	{!! displayBreadCrumbs($Header) !!}
	<div class="row">
		<div class="col-md-3 sidebar">
			{!! displayCatMenu() !!}
			
		</div>
		<div class="col-md-9 content">
			@if(!$no_header)
			<h1 class="page-heading">
                <span class="page-heading-title">{{$Header}}</span>
            </h1>
            @endif
			@yield('content')
		</div>
	</div>
</main>
@stop