<div class="container">
	<div class="block-header-top12">
		<div class="block-search">
			<form class="form-inline" action="{{$dir_prefix}}search/">
				<div class="form-group input-serach">
					<input name="s" type="text" placeholder="Поиск по каталогу..." value="{{$s}}">
				</div>
				<button type="submit" class="pull-right btn-search"><i class="fa fa-search"></i></button>
			</form>
		</div>
		{!! displayCart() !!}
	</div>
</div>