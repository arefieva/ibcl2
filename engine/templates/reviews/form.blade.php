<div id="form-reviews">
	<form method="POST" action="./">
		<input type="hidden" name="CatItemID" value="{{$CatItemID}}">
		<input type="hidden" name="action" value="doAddReview">
		<input type="hidden" name="Rating" value="0">
		<div class="margin-bottom-15">
			<h2>Оставить отзыв</h2>
		</div>
		<div class="margin-bottom-15">
			<b>{{$CatItemName}}</b>
		</div>
		<div class="margin-bottom-15">
			<input type="text" placeholder="Ваше имя *" name="Name" class="input" data-required="name">
		</div>
		<div class="margin-bottom-15">
			<input type="text" placeholder="Ваш Email * (не отображается)" name="Email" class="input" data-required="email">
		</div>
		<div class="margin-bottom-15">
			<input type="text" placeholder="Город" name="City" class="input">
		</div>
		<div class="margin-bottom-15">
			<div class="reviews-rating-choose">
				Ваша оценка:&nbsp;&nbsp;
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i>
			</div>
		</div>
		<div class="margin-bottom-15">
			<textarea name="Question" rows="6" class="input" placeholder="Текст отзыва *" data-required="review-text"></textarea>
		</div>
		<div class="text-center">
			<button class="button" type="submit">Отправить</button>
		</div>
	</form>
</div>