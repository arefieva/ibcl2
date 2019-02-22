<div class="carousel-holder hover">
    <div class="title-nav">
        <h1>С этим товаром покупают</h1>
		<div class="nav-holder">
			<a href="#prev" data-target="#owl-recently-viewed-2" class="slider-prev btn-prev fa fa-angle-left"></a>
			<a href="#next" data-target="#owl-recently-viewed-2" class="slider-next btn-next fa fa-angle-right"></a>
		</div>
    </div>
    <div class="owl-carousel product-grid-holder" id="owl-recently-viewed-2" data-dots="false">
        @foreach($seealso as $item)        
		
		<div class="no-margin carousel-item product-item-holder <?php echo $productItemSize;?> hover">
			<div class="product-item js-cart-item">
				@if($item['Special']>0)<div class="ribbon green"><span>СКИДКА</span></div> @endif
				@if($item['New']>0)<div class="ribbon blue"><span>НОВИНКА</span></div> @endif
				@if($item['Hit']>0)<div class="ribbon orange"><span>ХИТ ПРОДАЖ</span></div>  @endif
				<div class="image">
					<a href="{{$item['Link']}}">
						<img class="js-cart-image" src="{{$dir_prefix}}{{$item['ListImg']}}"/>
					</a>
				</div>
				<div class="body">
				@if($item['discount']>0 and $item['Special']>0)<div class="label-discount red">-{{$item['discount']}}%</div>@endif
					<div class="title">
						<a href="{{$item['Link']}}">{{$item['Name']}}</a>
					</div>
					<div class="brand">Stiga</div>
				</div>
				<div class="prices">
					@if($item['PriceOld'])<div class="price-prev">{{$item['PriceOld']}}<span class="sign"><i class="fa fa-rub"></i></span></div>@endif
					@if(!empty($item['Price']))
						<div class="price-current pull-right">{{$item['Price']}}<span class="sign"><i class="fa fa-rub"></i></span></div>
					@else
						<span class="no-price">Цена по запросу</span>
					@endif
				</div>
				<div class="available_wrap text-center">
					{!!$item['AvailableString']!!}
				</div>
				<div class="add-cart-button">
					<?if(!empty($item['Price'])) {?> 
					<a href="javascript:void(0)" class="le-button js-cart-add">В КОРЗИНУ</a>
					<input type="hidden" name="pid" value="{{$item['__id']}}" class="js-cart-pid">
                    <input type="hidden" name="qty" value="1" class="js-cart-qty">
					<? } ?>
				</div>

			</div><!-- /.product-item -->
		</div><!-- /.product-item-holder -->
		@endforeach

    </div>
</div>