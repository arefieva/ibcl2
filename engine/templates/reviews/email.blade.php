Здравствуйте!

На сайте {{getSystemVariable('site_name')}} был оставлен отзыв о товаре <a target="_blank" href="{{$Link}}">{{$ItemName}}</a>
Ниже приведены все данные.

<hr>
Имя: {{$Name}}
Email: {{$Email}}
@if(!empty($City)) Город: {{$City}} @endif

Оценка: {{$Rating}}

Текст отзыва:
{{$Question}}

<hr>
Всего хорошего!
(Это письмо сгенерировано роботом)