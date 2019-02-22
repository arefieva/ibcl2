<div class="product-qty">
    <div class="qty">
        <input type="text"
        <?php foreach($attributes as $name => $value){
        echo ' '.$name.'="'.$value.'"';
        }?>
        >
    </div>
    <div class="btn-plus">
        <a href="#" class="btn-plus-up">
            <i class="fa fa-caret-up"></i>
        </a>
        <a href="#" class="btn-plus-down">
            <i class="fa fa-caret-down"></i>
        </a>
    </div>
</div>