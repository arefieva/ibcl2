<script type="text/javascript">
		function UpdateForm5(){
			if(checkForm5()){
				var data = $('#form5ID').serialize();
				$.ajax({
				  type: "POST",
				  url: "./",
				  data: data,
				  success: function(msg){
					$('#form5-popup .cont_form5').html(msg);
				  }
				 });
			}
		}

		function checkForm5() {
		  var frm = document.forms['mail'];
		  var error = "";

		  if($('#form5ID #Name').val() == "" || $('#form5ID #Name').val() == "Ваше имя") error+="Пожалуйста, введите Ваше имя\n";

		  if($('#form5ID #phone').val() == "" || $('#form5ID #phone').val() == "Ваш телефон") error+="Пожалуйста, введите Ваш контактный телефон\n";
		  else if(isNaN($('#form5ID #phone').val())) error=error+"Пожалуйста, напишите Ваш телефон цифрами\n";

		  if($('#form5ID #Answer').val() == "") error=error+"Пожалуйста введите ответ на вопрос\n";
		  else if(isNaN($('#form5ID #Answer').val())) error=error+"Пожалуйста напишите ответ цифрами\n";
		  
		  if(error==""){
			$('#form5ID #action_post').val("doPostForm");
			return true;
		  }
		  else {
			alert(error);
			return false;
		  }
		} 
</script>
<h3 class="block-title main-heading" style="font-size: 22px;">Обратная связь</h3>
<div id="container_form">
	<form id="form5ID" name="mail" method="post" action="./" onSubmit="return checkForm5()">
		<input type="hidden" name="ajax" value="5">

		<input type="hidden" name="action" id="action_post" value="doPostForm">
		<input type="hidden" name="mailSubject" value="Звонок с сайта {{$admin_title}}">
		<input type="hidden" name="formid" value="{{$form}}">
		<input type="hidden" name="id" value="{{$id}}">
		<input type="hidden" name="ans" value="{{$ans}}"/>
		<input type="hidden" name="required" value="Name, phone, Answer">

		<div class="row">
		  <div class="form5ID-wrapper">
		    <label>Ваше имя <span>*</span></label>
		    <input class="simple-field" name="Name" id="Name" type="text" placeholder="Ваше имя" required value="" />

		    <label>Ваш телефон <span>*</span></label>
		    <input class="simple-field" name="phone" id="phone" type="text" placeholder="Ваш телефон" required value="" />

		    <label>Защита от автоматических сообщений<br><br>Сколько будет {{$a}} плюс {{$b}}? Введите число: <span>*</span></label>
		    <input class="simple-field" name="Answer" id="Answer" type="text" placeholder="Ответ" value="" />

		    <div class="button style-10">
				<button type="button" value="" onclick="UpdateForm5()">
					Отправить
				</button>
			</div>
		  </div>
		</div>
	</form>
</div>