<?php
/*
 * 
 * @Date: 05/23/2024
 * @Author: Max Base
 * @URL: https://github.com/basemax
 * @Copyright: asrez group <https://asrez.com>
 * @Project: Toos Catalogue - Amir Reza Heydari
 * 
 */

define('MY_APP', true);

//////////// CONFIGS ////////////
$currentUrl = "index.php"; // FEEL FREE TO CHANGE YOUR FULL URL without index.php such as: https://site.com/gallery/
$directoryPath = 'files' . DIRECTORY_SEPARATOR;
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
$thumbnailWidth = 70;
$thumbnailHeight = 70;

//////////// FUNCTIONS ////////////
function getDirectories(string $dir): array {
	$result = [];
	if (is_dir($dir)) {
		$items = scandir($dir);
		foreach ($items as $item) {
			if ($item === '.' || $item === '..') {
				continue;
			}
			$path = $dir . DIRECTORY_SEPARATOR . $item;
			if (is_dir($path)) {
				$result[] = $item;
			}
		}
	}
	return $result;
}

function findFirstImage(string $dir, array $allowedExtensions): ?string {
	$dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

	$files = scandir($dir);
	foreach ($files as $file) {
		if ($file === '.' || $file === '..') {
			continue;
		}

		$filePath = $dir . $file;

		if (is_dir($filePath)) {
			$result = findFirstImage($filePath, $allowedExtensions);
			if ($result !== false) {
				return $result;
			}
		} else {
			$extension = pathinfo($file, PATHINFO_EXTENSION);
			if (in_array(strtolower($extension), $allowedExtensions)) {
				return $filePath;
			}
		}
	}

	return false;
}

function listAllImages(string $dir, array $allowedExtensions): array {
	global $directoryPath;

	$images = [];

	$dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

	$files = scandir($dir);
	foreach ($files as $file) {
		if ($file === '.' || $file === '..') {
			continue;
		}

		$filePath = $dir . $file;

		if (is_dir($filePath)) {
			$images = array_merge($images, listAllImages($filePath, $allowedExtensions));
		} else {
			$extension = pathinfo($file, PATHINFO_EXTENSION);
			if (in_array(strtolower($extension), $allowedExtensions) && filesize($filePath) > 0) {
				$images[] = $filePath;
			}
		}
	}

	return $images;
}

function createThumbnail(string $src, int $targetWidth, int $targetHeight): void {
	$imageData = file_get_contents($src);
	if ($imageData === false) {
		die('Failed to read the image file.');
	}
	
	$image = imagecreatefromstring($imageData);
	if ($image === false) {
		die('Failed to create image from string.');
	}

	$originalWidth = imagesx($image);
	$originalHeight = imagesy($image);

	$originalAspect = $originalWidth / $originalHeight;
	$thumbAspect = $targetWidth / $targetHeight;

	if ($originalAspect >= $thumbAspect) {
		$newHeight = $targetHeight;
		$newWidth = $originalWidth / ($originalHeight / $targetHeight);
	} else {
		$newWidth = $targetWidth;
		$newHeight = $originalHeight / ($originalWidth / $targetWidth);
	}

	$thumb = imagecreatetruecolor($targetWidth, $targetHeight);

	imagecopyresampled($thumb, $image, 0 - ($newWidth - $targetWidth) / 2, 0 - ($newHeight - $targetHeight) / 2, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

	header('Content-Type: image/jpeg');
	imagejpeg($thumb, null, 80);

	imagedestroy($image);
	imagedestroy($thumb);
}

function sanitizePath(string $path): string {
	$path = str_replace(array('./', '../', '.\\', '..\\'), '', $path);
	return $path;
}

function endsWithAllowedExtension(string $filePath, array $allowedExtensions): bool {
	$extension = pathinfo($filePath, PATHINFO_EXTENSION);

	return in_array(strtolower($extension), $allowedExtensions);
}

//////////// MAIN ////////////

// CROP ON THE FLY - PAGE
if (isset($_GET["image"])) {
	$image = urldecode($_GET['image']);
	$image = sanitizePath($image); // TO KEEP IT SAFE

	if (strpos($image, $directoryPath) !== 0) {
		header("Location: $currentUrl");
		exit();
	}

	if (!endsWithAllowedExtension($image, $allowedExtensions)) {
		header("Location: $currentUrl");
		exit();
	}

	if (!is_file($image) || !file_exists($image)) {
		header("Location: $currentUrl");
		exit();
	}

	createThumbnail($image, $thumbnailWidth, $thumbnailHeight);
}
// CATEGORY PAGE
else if (isset($_GET["category"])) {
	$category = strtolower(trim(urldecode($_GET["category"])));
	if ($category === "") {
		header("Location: $currentUrl");
		exit();
	}

	$categories = getDirectories($directoryPath);
	if (!in_array($category, $categories)) {
		header("Location: $currentUrl");
		exit();
	}

	$title = ucwords($category);

	$allImages = listAllImages($directoryPath, $allowedExtensions);
	if (empty($allImages)) {
		header("Location: $currentUrl");
		exit();
	}

	$_allImages = $allImages;
	require "layouts/category.php";
}
// HOME PAGE
else {
	$_categories = [];
	$categories = getDirectories($directoryPath);
	foreach ($categories as $category_item) {
		$categoryPath = $directoryPath . DIRECTORY_SEPARATOR . $category_item;
		$categoryImage = findFirstImage($categoryPath, $allowedExtensions);
		
		// Skip if no image found in the directory.
		if ($categoryImage === false) continue;

		$_categories[] = [
			"dir" => $category_item,
			"name" => ucwords($category_item),
			"image" => $categoryImage,
		];
	}
	require "layouts/home.php";
}
