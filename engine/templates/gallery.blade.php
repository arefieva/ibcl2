{{-- Basic image gallery --}}
<div class="row gallery">
	@foreach($photos as $photo)
	<div class="gallery-item">
		<div class="gallery-img">
			@if(!empty($photo['bigURL']))
			<a href="{{$dir_prefix.$photo['bigURL']}}" class="fb-img">
				<img title="{{$photo['Name']}}" class="img-responsive margin-auto" src="{{$dir_prefix.$photo['smallURL']}}">
			</a>
			@else
			<img class="img-responsive margin-auto" src="{{$dir_prefix.$photo['smallURL']}}">
			@endif
		</div>
		@if($display_labels)
		<div class="gallery-label">
			{{$photo['Name']}}
		</div>
		@endif
	</div>
	@endforeach
</div>