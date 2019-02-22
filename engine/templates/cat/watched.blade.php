<div class="watched">
	<div class="watched-header">
		Вы недавно смотрели:
	</div>
	<div class="watched-carousel owl-carousel" data-owl="1">
		@foreach($items as $item)
		<div class="watched-item">
			<a href="{{$item['Link']}}" class="watched-item-img table">
				<div class="text-middle">
					<img src="{{$dir_prefix}}{{$item['ListImg']}}" class="img-responsive">
				</div>
			</a>
			<a href="{{$item['Link']}}" class="watched-item-name">
				{{$item['Name']}}
			</a>
			<div class="watched-item-price">
				Цена:
				@if(!empty($item['PriceOld']))<span class="watched-item-price-old">{{$item['PriceOld']}} <i class="fa fa-rub"></i></span>&nbsp;&nbsp;@endif
				<span class="watched-item-price-actual">{{$item['Price']}} <i class="fa fa-rub"></i></span>
			</div>
			@if(!empty($item['PriceOld']))
			<div class="watched-item-profit f-15">
			Вы экономите {{$item['PriceOld'] - $item['Price']}} <i class="fa fa-rub f-13" style="margin-top: 3px;"></i>
			</div>
			@endif
		</div>
		@endforeach
	</div>
</div>