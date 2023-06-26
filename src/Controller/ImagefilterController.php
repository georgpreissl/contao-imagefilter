<?php

namespace GeorgPreissl\Imagefilter\Controller; 


use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\Validator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse; 




class ImagefilterController
{


	public function loadAction($id,$filter): Response
	{

		// $id -> id of the image (eg. '3')
		// $filter -> name of the filter (eg. 'sepia')

		if ($id && $filter) {
			
			// get the path of the given image
			$objDbConnection = \Contao\System::getContainer()->get('database_connection');
			$arrResult = $objDbConnection->executeQuery("SELECT path FROM tl_files WHERE id = ?", [$id])->fetch();
			$strPath = $arrResult['path'];
			
			// Save the image dimensions in the variables $intImgW and $intImgH
			list($intImgW, $intImgH) = getimagesize($strPath);
			
			// Create a new image resource identifier (the source image)
			$objResImgSrc = imagecreatefromjpeg($strPath);
			
			// Create a filter instance
			$objFilter = new \FilterFun($objResImgSrc);


			if(is_callable(array($objFilter, $filter))){
				// execute the filter
			    $objFilter->$filter();
			} else {
				// throw an error
			    exit("Unable to find filter named '$filter'");
			}
				
			// Create a true color image resource identifier (the destination image)
			$objResImgDest = imagecreatetruecolor($intImgW, $intImgH);

			// Copy part of the source image
			imagecopy($objResImgDest, $objResImgSrc, 0, 0, 0, 0, $intImgW, $intImgH);

			// Return the image
			header('Content-type: image/jpeg');
			imagejpeg($objResImgDest, null, 100);
			imagedestroy($objResImgDest);
			imagedestroy($objResImgSrc);
			

		}


	}
}
