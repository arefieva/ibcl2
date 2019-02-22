<div class="reviews">
    <ul class="reviews-items">
        @forelse($reviews as $review)
        <li class="reviews-item {{$review['HiddenClass']}}">
            <div class="reviews-item-info">
                <span class="reviews-item-date">{{$review['DateTime']}}</span>
                <span class="reviews-item-rating reviews-item-rating-{{$review['Rating']}}">
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                </span>
                <span class="reviews-item-name">{{$review['Name']}}</span>
                @if(!empty($review['City']))<span class="reviews-item-city">({{$review['City']}})</span>@endif
            </div>
            <div class="reviews-item-question">
                {{$review['Question']}}
            </div>
            @if(!empty($review['Body']))
            <div class="reviews-item-answer">
                <div class="reviews-item-answer-name">Консультант</div>
                <div class="reviews-item-answer-text">{!! $review['Body'] !!}</div>
            </div>
            @endif
        </li>
        @empty
        <div class="text-center">
            Отзывов нет
            <div class="space-25"></div>
        </div>
        @endforelse
    </ul>
</div>
<div class="reviews-options">
    <span class="reviews-options-comment">
        <i class="fa fa-comment-o"></i> <a class="fb-form fb-reviews" href="#form-reviews">Оставить отзыв</a>
    </span>
    @if(count($reviews) > 3)
    <span class="reviews-options-showall">
        <i class="fa fa-bars"></i> <a id="reviews-all" href="javascript:void(0)">Посмотреть все отзывы</a> ({{count($reviews)}})
    </span>
    @endif
</div>