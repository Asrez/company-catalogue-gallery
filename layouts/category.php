<?php
if (!defined('MY_APP')) die('Direct access not permitted');
?>
<!DOCTYPE html>
<html dir="ltr" lang="en_US">
<head>
	<meta charset="UTF-8">
	<title>Rug - <?= $title ?></title>
	<link rel="stylesheet" href="assets/style.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
	<a href="<?= $currentUrl ?>">Back to catalog</a>
	<h1>Rugs of <?= $title ?></h1>
	<h3>Count: <?= count($allImages) ?> carpets</h3>

	<div class="gallery" id="lightgallery">
		<?php foreach ($allImages as $image) { ?>
			<?php $name = pathinfo(basename($image), PATHINFO_FILENAME) ?>
			<div class="gallery-item">
				<a href="<?= $image ?>" data-sub-html="<h4><?= $title ?> - <?= $name ?></h4>">
					<img src="?image=<?= urlencode($image) ?>" alt="<?= $name ?>">
				</a>
			</div>
		<?php } ?>
	</div>
	<script src="assets/lightgallery.min.js"></script>
	<script>
	document.addEventListener("DOMContentLoaded", function() {
		lightGallery(document.getElementById('lightgallery'), {
			selector: 'a'
		});
	});
	</script>
</body>
</html>
