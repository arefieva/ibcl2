<div id="preorder" class="preorder">
	<form method="POST" action="./">
		<input id="preorder-id" type="hidden" name="pid" value="">
		<input type="hidden" name="action" value="doProcessPreorder">
		<div class="preorder-title margin-bottom-15">
			<h2>Предзаказ</h2>
		</div>
		<div class="preorder-name margin-bottom-15">
			<b id="preorder-name"><!-- data-product-name --></b>
		</div>
		<div class="preorder-info margin-bottom-15">
			В данный момент этого товара нет на складе интернет-магазина. Если вы оставите предзаказ, то мы свяжемся с нашими поставщиками и постараемся привезти выбранный товар в течении 1 рабочего дня. Как только товар появится у нас, мы сразу уведомим Вас о его поступлении по электронной почте. Для этого заполните форму ниже.
		</div>
		<div class="margin-bottom-15">
			<input type="text" placeholder="Ваше имя *" name="Name" class="input" data-required="name">
		</div>
		<div class="margin-bottom-15">
			<input type="text" placeholder="Ваш email *" name="Email" class="input" data-required="email">
		</div>
		<div class="margin-bottom-15">
			<input type="text" placeholder="Ваш телефон" name="Phone" class="input">
		</div>
		<div class="margin-bottom-15">
			<input type="text" placeholder="Ваш город" name="City" class="input">
		</div>
		<div class="text-center">
			<button class="button" type="submit">Отправить</button>
		</div>
	</form>
</div>