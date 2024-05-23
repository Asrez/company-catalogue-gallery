<?php
if (!defined('MY_APP')) die('Direct access not permitted');
?>
<!DOCTYPE html>
<html dir="ltr" lang="en_US">
<head>
	<meta charset="UTF-8">
	<title>Rug Categories</title>
	<link rel="stylesheet" href="assets/style.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
	<div class="container">
		<h1>Rug Categories</h1>

		<?php foreach ($_categories as $category_item) { ?>
		<div class="category">
			<a href="?category=<?= $category_item["name"] ?>">
				<h2><?= $category_item["name"] ?></h2>
			</a>
			<a href="?category=<?= $category_item["name"] ?>">
				<img src="<?= $category_item["image"] ?>" alt="<?= $category_item["name"] ?>">
			</a>
		</div>
		<?php } ?>
	</div>
</body>
</html>
