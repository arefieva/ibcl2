<?php 
$cats_nofollow = "";
if (!empty($cats)) { ?>
	<?php foreach ($cats as $cat) { ?>
		<div class="col-lg-3 col-md-4 col-xs-6 categories-item">
		<div class="categories-item-content">
			<div class="categories-item-thumb">
				<a href="<?= $cat['link'] ?>"><img src="<?= $cat['Image'] ?>" alt="<?=$cat['Name']?>"></a>
			</div>
				<h5 class="categories-item-name">
					<a href="<?= $cat['link'] ?>"><?= $cat['Name'] ?></a>
				</h5>
			</div>
		</div>
	<?php } ?>
<?php } ?>
<?= !empty($items) ? $items : '' ?>
<script>
	$(function () {
		catalogue();
		catitemCount();
		catitemAboutPrice();
	});
</script>
