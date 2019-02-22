<footer class="footer4">
	 <div class="container">
		 <div class="footer-top">
			<div class="row">
				 <div class="col-md-3"><!--col-sm-3-->
				 	<div class="widget-container">
				 		<div class="footer-logo">
							<?=displayLogo()?> 
						</div>
						{{ getSystemVariable("footer_address") }}<br>
						Телефон: {{ getSystemVariable("footer_phone") }}<br>
						Email: {!! highlightEmails(getSystemVariable("footer_email")) !!}
						
				 	</div>
				 </div>
				 
						<div class="col-xs-12 col-md-9 no-margin">
							<div id="main-menu" class="col-sm-12 col-md-12 main-menu no-margin">
								{!! displayHeaderMenu() !!}
							</div>
						</div><!-- /.col -->
			 </div>
		 </div>
		 
	 </div>
	 <div class="copyright-bar">
        <div class="container">
            <div class="col-xs-6 col-sm-3 no-margin copyright">
                    &copy; 2017 BeClean 
            </div><!-- /.copyright -->
            <div class="col-xs-6 col-sm-4 no-margin">
                
				<a href="{{dir_prefix}}jural-info">Юридическая информация</a>
            </div>
			<div class="col-xs-12 col-sm-4 no-margin webis">
				<a target="_blank" href="http://www.webisgroup.ru/" rel="nofollow" title="Создание сайтов">
					<img src="{{dir_prefix}}images/webis.png" alt="webis" width="67" height="21">Создание сайтов
				</a>
			</div>
        </div><!-- /.container -->
    </div><!-- /.copyright-bar -->
</footer>