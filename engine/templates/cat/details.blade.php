@extends('site.layout_inner')
@section('content')
<div id="single-product" class="row js-cart-item">
 <div class="no-margin col-xs-12 col-sm-6 col-md-5 gallery-holder">
    <div class="product-item-holder size-big single-product-gallery small-gallery product-image">
        <div id="owl-single-product" class="owl-carousel">
			@if(count($gallery) > 1)
				<?$i = 0;?>
				@foreach($gallery as $image)
				<div class="single-product-gallery-item" id="slide{{$i}}">
					<a data-rel="prettyphoto" href="{{$dir_prefix}}{{$image['bigURL']}}" class="image-box" rel="gal">
						<img class="img-responsive js-cart-image" alt="" src="{{$dir_prefix}}{{$image['bigURL']}}" data-echo="{{$dir_prefix}}{{$image['bigURL']}}" />
					</a>
				</div><!-- /.single-product-gallery-item -->
				<?$i++;?>
				@endforeach
			
			@else
			<div class="single-product-gallery-item" id="slide0">
				<a data-rel="prettyphoto" href="{{$dir_prefix}}{{$gallery[0]['bigURL']}}" class="image-box" rel="gal">
					<img class="img-responsive" alt="" src="{{$dir_prefix}}{{$gallery[0]['bigURL']}}" data-echo="{{$dir_prefix}}{{$gallery[0]['bigURL']}}" />
				</a>
			</div><!-- /.single-product-gallery-item -->
			@endif
		 </div><!-- /.single-product-slider -->
	
		@if(count($gallery) > 1)
        <div class="single-product-gallery-thumbs gallery-thumbs">

            <div id="owl-single-product-thumbnails" class="owl-carousel">
				<?$i = 0;?>
				@foreach($gallery as $image)
                <a class="horizontal-thumb active" data-target="#owl-single-product" data-slide="{{$i}}" href="javascript:void(0)">
                    <img width="67" alt="" src="" data-echo="{{$dir_prefix}}{{$image['smallURL']}}" />
                </a>
				<?$i++;?>
				@endforeach
			</div><!-- /#owl-single-product-thumbnails -->
			
			<div class="nav-holder left hidden-xs">
                <a class="prev-btn slider-prev" data-target="#owl-single-product-thumbnails" href="#prev"></a>
            </div><!-- /.nav-holder -->
            
            <div class="nav-holder right hidden-xs">
                <a class="next-btn slider-next" data-target="#owl-single-product-thumbnails" href="#next"></a>
            </div><!-- /.nav-holder -->

        </div><!-- /.gallery-thumbs -->
		@endif

    </div><!-- /.single-product-gallery -->
</div><!-- /.gallery-holder -->	

<div class="no-margin col-xs-12 col-sm-7 body-holder" id="product">
    <div class="body">
        <div class="title">{{$Name}}</div>
        <div class="brand">@if(!empty($Code))
                АРТИКУЛ {{$Code}}
                @endif</div>
        <div class="prices">
            @if(!empty($Price))
                <div class="price">{{$Price}} <i class="fa fa-rub f-24"></i></div>
            @else
                <span class="no-price">Цена по запросу</span>

            @endif
            
			@if(!empty($PriceOld))
                <div class="price-old">
                    {{$PriceOld}} <i class="fa fa-rub f-16"></i>
                </div>
            @endif
        </div>
        <div class="available_wrap text-left">
            {!!$AvailableString!!}
        </div>
        <div class="qnt-holder">
            @if(!empty($Price))
                <div class="le-quantity">
                    <form>
                        <a class="minus" href="#reduce"></a>
                        <input name="qty" readonly="readonly" type="text" value="1" class="js-cart-qty" />
                        <a class="plus" href="#add"></a>
                    </form>
                </div>
    			<input type="hidden" name="pid" value="{{$__id}}" class="js-cart-pid">
               
                    <a id="addto-cart" href="javascript:void(0)" class="le-button huge btn-add-cart js-cart-add">В КОРЗИНУ</a>
            @endif 
        </div><!-- /.qnt-holder -->

        <div class="BriefDetails">
            {{$BriefDetails}}
        </div>

    </div><!-- /.body -->

</div><!-- /.body-holder -->

</div><!-- /.row #single-product -->

<div class="space-30"></div>
<ul class="nav nav-tabs product-d-tabs">
    <li class="active"><a href="#desc" data-toggle="tab">Описание и характеристики</a></li>
    <li><a href="#reviews" data-toggle="tab">Описание раздела</a></li>
	<li><a href="#delivery" data-toggle="tab">Доставка</a></li>
</ul>
<div class="tab-content text-default">
    <div class="tab-pane @if(!empty($Body)) active @endif fade in" id="desc">
		<div class="product-params-item">
			<div class="product-params-item-name">Производитель:  </div>
			<div class="product-params-item-value">{{ $Brandname }}</div>
		</div>
		@foreach ($Parameters as $param)
			<div class="product-params-item">
				<div class="product-params-item-name">{{$param['name']}}:  </div>
				<div class="product-params-item-value">{{$param['val']}}</div>
			</div>
		@endforeach
	   {!! $Body !!}
    </div>
    <div class="tab-pane @if(empty($Body)) active @endif fade in" id="reviews">
		{!! $SectionDesc !!}
    </div>
	<div class="tab-pane fade in" id="delivery">
	{!!$delivery_info!!}
	</div>
</div>

<div class="space-30"></div>

{!! $SeeAlso !!}

@stop