@if(count($items) > 0)
<div class="sortPagiBar">
    <div class="clearfix sortbar">
        <form id="sort-products" action="./" method="get">
            <input type="hidden" name="cid" value="{{$cid}}">
            <input type="hidden" name="p" value="{{$p}}">
            <input type="hidden" name="s" value="{{$s}}">
            <input type="hidden" name="brand_id" value="{{$brand_id}}">
            <div class="sort-product">
                Сортировать:
                <select name="sort">
                    @foreach($sorts as $sort)
                        <option {{$sort['selected']}} value="{{$sort['value']}}">{{$sort['name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="show-product-item">
                Показывать по: <select id="number_of_products" name="limit">
                    @foreach($limits as $limit)
                    <option {{$limit['selected']}} value="{{$limit['value']}}">{{$limit['value']}}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

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
				<?if(!empty($item['Price'])) {?> 
					<a href="javascript:void(0)" class="le-button js-cart-add">В КОРЗИНУ</a>
					<input type="hidden" name="pid" value="{{$item['__id']}}" class="js-cart-pid">
	                <input type="hidden" name="qty" value="1" class="js-cart-qty">
				<? } ?>
			</div>
		</div>
	</div>
    
    @endforeach
	</div>
</div>
<div class="sortPagiBar clearfix">
    <span class="page-noite">{{$navbar_info}}</span>
    <div class="bottom-pagination">
        {!! $navbar !!}
    </div>
</div>
@else
<div class="margin-top-40 text-center">@if($action == 'search') Результатов нет @else В этом разделе нет позиций @endif</div>
@endif
