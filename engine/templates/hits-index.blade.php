<h2>ПОПУЛЯРНЫЕ ТОВАРЫ</h2>	
<div id="products-tab" class="wow fadeInUp">
    <div class="product-grid-holder">              
	@foreach($items as $item)
		 <div class="col-sm-6 col-md-4  no-margin product-item-holder hover">
		 <div class="product-item js-cart-item">
			@if($item['Special']>0)<div class="ribbon green"><span>СКИДКА</span></div> @endif
			@if($item['New']>0)<div class="ribbon blue"><span>НОВИНКА</span></div> @endif
			@if($item['Hit']>0)<div class="ribbon orange"><span>ХИТ ПРОДАЖ</span></div>  @endif
				<div class="image">
					<a href="{{$item['Link']}}">
						<img class="js-cart-image" alt="product" src="{{$dir_prefix}}{{$item['ListImg']}}"/>
					</a>
				</div>
				<div class="body">
					@if($item['discount']>0 and $item['Special']>0)<div class="label-discount red">-{{$item['discount']}}%</div>@endif
					<div class="title">
						<a href="{{$item['Link']}}">{{$item['Name']}}</a>
					</div>
					<div class="spec">{{$item['Parameters'][0]["val"]}}</div>
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
					<?if(!empty($item['Price']))  {?> 
						<a href="javascript:void(0)" class="le-button js-cart-add">В КОРЗИНУ</a>
						<input type="hidden" name="pid" value="{{$item['__id']}}" class="js-cart-pid">
	                    <input type="hidden" name="qty" value="1" class="js-cart-qty">
					<? } ?>
				</div>
			</div>
		</div>
		 
	@endforeach
		<div class="col-xs-12 loadmore-holder">
			<div class="text-center">
				<a class="btn-loadmore" href="{{$dir_prefix}}hits">
					<img src="assets/images/icon_more.png" alt="icon_more" width="19" height="15">
					<span class="text-loadmore">загрузить больше товаров</span></a>
			</div> 
		</div> 
	</div>
</div>