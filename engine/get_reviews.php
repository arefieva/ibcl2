<?php

function displayReviews($CatItemID){
	$reviews = db::select("SELECT * FROM Reviews WHERE Active=1 AND CatItemID=$CatItemID ORDER BY DateTime DESC");
	$count = 1;
	foreach ($reviews as &$review) {
		$review['DateTime'] = date('d.m.Y', strtotime($review['DateTime']));
		if($count > 3){
			$review['HiddenClass'] = 'display-none';
		}
		$count ++;
	}
	return blade::render('reviews.list', ['reviews' => $reviews]);
}

function displayReviewsForm($CatItemID, $CatItemName){
	return blade::render('reviews.form', compact('CatItemID', 'CatItemName'));
}

function addReview(){
	global $CatItemID, $Name, $City, $Question, $Rating, $DateTime, $Email, $Active, $globalMailTo;
	$Rating = intval($Rating);
	if($Rating == 0) $Rating = 1;
	$CatItemID = intval($CatItemID);
	$Question = trim($Question);
	$City = trim($City);
	$Email = trim($Email);
	if(empty($CatItemID) || empty($Name) || empty($Question) || empty($Email)){
		$response['message'] = '<div class="alert alert-danger">Не заполнены все необходимые поля</div>';
		$response['back_text'] = 'Назад';
		$response['back_link'] = "$.fancybox.open('#form-reviews')";
		return json_encode(['html' => blade::render('forms.response', $response)]);
	}
	$Active = '0';
	$DateTime = date('Y-m-d H:i:s');
	db::insert('Reviews');
	$ItemName = db::field("SELECT Name FROM CatItems WHERE __id = ".$CatItemID);
	$Link = getRealLinkURL('pid:'.$CatItemID, true);
	$mailBody = nl2br(blade::render('reviews.email', compact('ItemName', 'Link', 'Name', 'Email', 'City', 'Rating', 'Question')));
	doMail(getSystemVariable('ReviewsMailTo'), 'Новый отзыв о товаре', $mailBody, $Email, $Name);
	$response['message'] = '<div class="alert alert-success">Ваш отзыв успешно отправлен</div>';
	$response['back_text'] = 'Закрыть';
	$response['back_link'] = "$.fancybox.close()";
	return json_encode(['html' => blade::render('forms.response', $response), 'success' => 1]);
}