@foreach($categories as $cat)
<div class="space-20"></div>
<div class="row">
    <div class="col-md-12">
        <div class="block-tab-category men">
            <div class="head">
                <h2 class="title">
                    <a href="{{$cat['Link']}}">{{$cat['Name']}}</a>
                </h2>
                <ul class="box-tabs nav-tab">
                    @foreach($cat['groups'] as $group)
                        <li @if($group == reset($cat['groups'])) class="active" @endif><a data-toggle="tab" href="#tab-{{$group['key']}}-{{$cat['__id']}}">{{$group['name']}}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="inner">
                <div class="block-banner">
                    <span class="banner-img"><a href="{{$cat['Link']}}"><img src="{{$dir_prefix}}{{$cat['ImageIndex']}}" alt="{{$cat['Name']}}"></a></span>
                </div>
                <div class="block-content">
                    <div class="tab-container">
                        @foreach($cat['groups'] as $group)
                        <div id="tab-{{$group['key']}}-{{$cat['__id']}}" class="tab-panel @if($group == reset($cat['groups'])) active @endif">
                            <ul class="tab-products">
                                @foreach($group['items'] as $item)
                                <li class="product-style3 js-cart-item">
                                    <div class="product-info">
                                        <h4 class="product-name"><a href="{{$item['Link']}}">{{$item['Name']}}</a></h4>
                                        <span class="price">{{$item['Price']}} <i class="fa fa-rub"></i></span>&nbsp;&nbsp;
                                        <span class="price product-price-old">{{$item['PriceOld']}} <i class="fa fa-rub"></i></span>
                                    </div>
                                    <div class="product-thumb">
                                        <a href="{{$item['Link']}}"><img class="js-cart-image" src="{{$dir_prefix}}{{$item['ListImg']}}" alt=""></a>
                                    </div>
                                    <div class="available_wrap text-center">
                                        {!!$item['AvailableString']!!}
                                    </div>
                                    <!-- @if($item['Available']) -->
                                    <a href="javascript:void(0)" class="btn-add-to-cart js-cart-add">В корзину</a>
                                    <input type="hidden" name="pid" value="{{$item['__id']}}" class="js-cart-pid">
                                    <input type="hidden" name="qty" value="1" class="js-cart-qty">
                                    <!-- @else -->
                                    <!-- <a href="#preorder" data-pid="{{$item['__id']}}" data-product-name="{{$item['Name']}}" class="btn-add-to-cart fb-form fb-preorder">Предзаказ</a> -->
                                    <!-- @endif -->
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach