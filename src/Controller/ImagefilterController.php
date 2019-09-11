<?php

namespace GeorgPreissl\Imagefilter; 


use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse; 




class ImagefilterController extends Controller
{

	/**
	 * @Route("/test")
	 */
	public function loadAction($id,$filter): Response
	{

		if ($id && $filter) {

			// return new JsonResponse("test"); 
			
			// get the path of the given image
			$db = \Contao\System::getContainer()->get('database_connection');
			$result = $db->executeQuery("SELECT path FROM tl_files WHERE id = ?", [$id])->fetch();
			$path = $result['path'];

			// Save the image dimensions in the variables $picW and $picH
			list($picW, $picH) = getimagesize($path);

			// Create a new image resource identifier (the source image)
			$resImgSrc = imagecreatefromjpeg($path);

			// Create a filter instance
			$objFilter = new \FilterFun($resImgSrc);


			if(is_callable(array($objFilter, $filter))){
			    $objFilter->$filter();
			} else {
			    $objFilter->sepia();
			}
				
			// Create a true color image resource identifier (the destination image)
			$resImgDest = imagecreatetruecolor($picW, $picH);

			// Copy part of the source image
			imagecopy($resImgDest, $resImgSrc, 0, 0, 0, 0, $picW, $picH);

			// Return the image
			header('Content-type: image/jpeg');
			imagejpeg($resImgDest, null, 100);
			imagedestroy($resImgDest);
			imagedestroy($resImgSrc);
			

		}


	}
}
