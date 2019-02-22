<div id="cart" class="block-mini-cart">
	<div id="cart-block" class="shopping-cart-box">
		<a class="cart-link dropdown-toggle" data-toggle="dropdown" href="{{$dir_prefix}}order">
			<div class="cart-item-count">
				<span id="cart-count" class="count">{{$total or 0}}</span>
				<img src="{{$dir_prefix}}images/icon-cart.png" alt="" />
			</div>
			<div class="total-price-cart"> 
				<span class="lbl-cart">В корзине:</span>
				<span id="cart-count2" class="count lbl-bold"> {{$total or 0}}</span>
				<span id="cart-units" class="lbl"> {{$units or "товаров"}}</span>
				<div>
				<span class="total-price">
					<span class="lbl">На сумму: </span>
					<span id="cart-total" class="lbl-bold">{{$price_total or 0}}</span> 
					<span class="sign">
						<i class="fa fa-rub"></i>
					</span>
				</span>
				</div>
			</div>
		</a>

	@if($total < 1)
		<ul class="dropdown-menu">
			<div class='empty-cart'>
				В вашей корзине нет товаров<br>
				<a href="{{route::link('cat')}}">В каталог</a>
			</div>
		</ul>
	@else
		<ul class="dropdown-menu">
			{!!displayOrder2()!!}
			<li class="checkout">
				<div class="cart-item">
					<div class="row">
						<div class="col-xs-12 col-sm-12" style="text-align: center;">
							<a href="{{$dir_prefix}}order" class="le-button">Перейти <br>в корзину</a>
						</div>
						
					</div>
				</div>
			</li>
		</ul>
	@endif
		
	</div>
</div>