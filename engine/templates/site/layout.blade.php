<!DOCTYPE html>
<html>
<head>
    <title>{{$Title}}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="{{$MetaKeywords}}">
    <meta name="description" content="{{$MetaDescription}}">
    <meta name="yandex-verification" content="3f519cca88db9bd3" />
    <!-- <link href="{{$dir_prefix}}favicon.ico" rel="shortcut icon" type="image/x-icon" /> -->
    @yield('css-before')
    <link href='https://fonts.googleapis.com/css?family=Arimo:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Oswald:400,300,700&subset=latin-ext' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="{{$dir_prefix}}images/plugins/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="{{$dir_prefix}}images/plugins/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="{{$dir_prefix}}images/plugins/select2/css/select2.min.css" />
    <link rel="stylesheet" type="text/css" href="{{$dir_prefix}}images/plugins/jquery.bxslider/jquery.bxslider.css" />
    <link rel="stylesheet" type="text/css" href="{{$dir_prefix}}images/plugins/owl.carousel/owl.carousel.css" />
    <link rel="stylesheet" type="text/css" href="{{$dir_prefix}}images/plugins/fancyBox/jquery.fancybox.css" />
    <link rel="stylesheet" type="text/css" href="{{$dir_prefix}}images/plugins/jquery-ui/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href="{{$dir_prefix}}images/plugins/animate.css" />
    <link rel="stylesheet" type="text/css" href="{{$dir_prefix}}images/plugins/reset.css" />
    <link rel="stylesheet" type="text/css" href="{{$dir_prefix}}images/css/theme.css" />
    <link rel="stylesheet" type="text/css" href="{{$dir_prefix}}images/css/responsive.css" />
    <link rel="stylesheet" type="text/css" href="{{$dir_prefix}}images/css/option12.css" />
    <link rel="stylesheet" type="text/css" href="{{$dir_prefix}}images/css/style.css" />
    @yield('css')
    <link rel="stylesheet" type="text/css" href="{{$dir_prefix}}images/css/helpers.css" />
    @yield('css-after')
</head>
<body class="home option12">
    @include('site._header')
	
    @yield('main')
    @include('site._footer')
	
	<div class="overlay-popup" id="form5-popup">
        <div class="overflow">
            <div class="table-view">
                <div class="cell-view">
                    <div class="close-layer"></div>
                    <div class="popup-container">
                        <div class="cont_form5"><?=displayForm(5)?></div>
                        <div class="close-popup"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
    <a href="#" class="scroll_top" title="Наверх" style="display: inline;">Наверх</a>
    <div class="display-none">
        {!! displayFormPreorder() !!}
    </div>
    @yield('js-before')
    <script type="text/javascript">
        var dp = "{{dir_prefix}}";
        var language = "{{language}}";
        var title = "{{ getSystemVariable($db, "default_title".language_suffix) }}";
        var url = "http://www.{{ getSystemVariable($db, "admin_title".language_suffix) }}";
    </script>
    
    <script type="text/javascript" src="{{$dir_prefix}}images/plugins/jquery/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="{{$dir_prefix}}images/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="{{$dir_prefix}}images/plugins/select2/js/select2.min.js"></script>
    <script type="text/javascript" src="{{$dir_prefix}}images/plugins/jquery.bxslider/jquery.bxslider.min.js"></script>
    <script type="text/javascript" src="{{$dir_prefix}}images/plugins/owl.carousel/owl.carousel.min.js"></script>
    <script type="text/javascript" src="{{$dir_prefix}}images/plugins/countdown/jquery.plugin.js"></script>
    <script type="text/javascript" src="{{$dir_prefix}}images/plugins/countdown/jquery.countdown.js"></script>
    <script type="text/javascript" src="{{$dir_prefix}}images/plugins/jquery.actual.min.js"></script>
    <script type="text/javascript" src="{{$dir_prefix}}images/plugins/fancyBox/jquery.fancybox.js"></script>
    <script type="text/javascript" src="{{$dir_prefix}}images/plugins/jquery.elevatezoom.js"></script>
    <script type="text/javascript" src="{{$dir_prefix}}images/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="{{$dir_prefix}}images/js/theme.js"></script>
    <script type="text/javascript" src="{{$dir_prefix}}images/js/cart.js"></script>
    <script type="text/javascript" src="{{$dir_prefix}}images/js/forms.js"></script>
    <script type="text/javascript" src="{{$dir_prefix}}images/js/ajaxform.js"></script>
	<script type="text/javascript" src="{{$dir_prefix}}images/plugins/jquery.customSelect.min.js"></script>
	<script type="text/javascript" src="{{$dir_prefix}}images/js/echo.min.js"></script>
    @yield('js')
    <script type="text/javascript" src="{{$dir_prefix}}images/js/init.js"></script>
	<?=getSystemVariable($db, "counters", null, false); ?>
</body>
</html>