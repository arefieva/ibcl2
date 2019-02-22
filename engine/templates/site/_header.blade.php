<div id="header" class="header style12">
	<nav class="top-bar">
		<div class="container">
			<div id="main-menu" class="col-sm-12 col-md-12 main-menu no-margin">
				{!! displayHeaderMenu() !!}
			</div>
		</div><!-- /.container -->
	</nav><!-- /.top-bar -->
    <div class="container main-header">
        <div class="row">
            <div class="col-xs-12 col-md-3 col-sm-12 logo">
                @if($is_index)
                <?=displayLogo()?>
                
                @else
                <a href="{{$dir_prefix}}"><img alt="{{site_name}}" src="{{$dir_prefix}}images/img/logo.png" /></a>
                @endif
            </div>
			
		<div class="col-xs-12 col-sm-12 col-md-6 top-search-holder no-margin">
			<div class="contact-row">
				<div class="phone inline">
					<i class="fa fa-phone"></i> {{ getSystemVariable("footer_phone") }}
					
				</div>
				<div class="callback inline">
					<a class="open-form5" href="javascript:void(0)">Перезвоните мне</a>
				</div>
				<div class="contact inline">
					<a href="mailto:info@site.ru"><i class="fa fa-envelope"></i>{!! highlightEmails(getSystemVariable("footer_email")) !!}</a>
				</div>
			</div><!-- /.contact-row -->
			<!-- ============================================================= SEARCH AREA ============================================================= -->

						<div class="block-search search-area">
							<form class="form-inline" action="{{$dir_prefix}}search/">
								<div class="form-group input-search control-group">
									<input name="s" class="search-field" type="text" placeholder="Введите поисковый запрос. Например: «Промышленные пылесосы»" value="{{$s}}">
									
								</div>
								
								<div class="custom-select categories-filter"><!-- animate-dropdown-->			
								
									<section>     
										<select name="cid" class="styled">
											<option value="0">Все разделы</option>

											{!! getTopCatCategory() !!}
											
										</select>
									</section>
								</div>
								<button type="submit" class="pull-right search-button"><i class="fa fa-search"></i></button>
							</form>
						</div>

			<!-- ============================================================= SEARCH AREA : END ============================================================= -->
		</div><!-- /.top-search-holder -->

		<div class="col-xs-12 col-sm-12 col-md-3 top-cart-row no-margin">

			<!-- ============================================================= SHOPPING CART DROPDOWN ============================================================= -->
			<div class="top-cart-holder">
					{!! displayCart() !!}
				
			</div><!-- /.top-cart-holder -->
		<!-- ============================================================= SHOPPING CART DROPDOWN : END ============================================================= -->
		</div><!-- /.top-cart-row -->
			
				

        </div>
    </div> 
</div>