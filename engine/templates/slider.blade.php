<div class="slide owl-carousel" data-dots="true" data-autoplay="true" data-loop="true" data-nav="false" data-items="1">
    <div>
	    <img src="{{$dir_prefix}}{{$slide['Photo']}}">
	    @if(!empty($slide['Name']))
			<div class="container-fluid">
				<div class="caption vertical-center text-left">
					<div class="big-text fadeInDown-1">
						<span class="big">{{$slide['Name']}}</span>
					</div>
					<div class="small fadeInDown-2">
						{{$slide['Brief']}}
					</div>
					
				</div><!-- /.caption -->
			</div><!-- /.container-fluid -->
	    @endif
		<div>
			<a class="btn-add-cart btn-more" href="{{$slide['Link']}}"><i class="fa fa-chevron-right" aria-hidden="true"></i>Подробнее</a>
		</div>
    </div>
    @endforeach
</div>


