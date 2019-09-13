<?php


if ($_GET['image'] && $_GET['filter']) {
	
	require '../filters/Filter.php';

	$strPath = $_GET['image'];
	$strFilter = $_GET['filter'];

	$strPath = "../../../../".$strPath;
	list($width, $height) = getimagesize($strPath);

	// Create a new image resource identifier (the source image)
	$resImgSrc = imagecreatefromjpeg($strPath);

	// Create a Filter instance
	$objFilter = new Filter($resImgSrc);


	if(is_callable(array($objFilter, $strFilter))){
	    $objFilter->$strFilter();
	}else{
	    $objFilter->sepia();
	}
		
	// Create a true color image resource identifier (the destination image)
	$resImgDest = imagecreatetruecolor($width, $height);

	// Copy part of the source image
	imagecopy($resImgDest, $resImgSrc, 0, 0, 0, 0, $width, $height);

	// return the image
	header('Content-type: image/jpeg');
	imagejpeg($resImgDest, null, 100);
	imagedestroy($resImgDest);
	imagedestroy($resImgSrc);


}

?>

