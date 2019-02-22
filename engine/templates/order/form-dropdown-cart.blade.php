@section('js')
<script type="text/javascript" src="{{$dir_prefix}}images/js/order.js"></script>
<script type="text/javascript" src="{{$dir_prefix}}images/js/order_calculate.js"></script>
@stop

@foreach($items as $item)
<li>
	<div class="cart-item">
		<div class="row">
			<div class="col-xs-4 col-sm-4 no-margin text-center">
				<div class="thumb">
					{!!$item['photo']!!}
				</div>
			</div>
			<div class="col-xs-8 col-sm-8 no-margin">
				<div class="title"><a href="{{$item['Link']}}">{{$item['Name']}} @if(!empty($item['Code'])) (Арт. {{$item['Code']}}) @endif</a></div>
				<div class="price">{{$item['cost']}} <i class="fa fa-rub"></i></div>
			</div>
		</div>
		<a onclick="return order.delete({{$item['no']}})" href="javascript:void(0)" class="close-btn"></a>
	</div>
</li>
@endforeach

