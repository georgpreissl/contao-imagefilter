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
	 * @Route("/test/")
	 */
	public function loadAction($path, $filter): Response
	{



		$objArticleModel = \ArticleModel::findByPk(2);
		$title = $objArticleModel->title;    	
		return new JsonResponse($title); 

		/*

		if ($strPath && $strFilter) {
			
			require '../Resources/contao/filters/FilterFun.php';

			$strPath = "../../../../".$strPath;
			list($picW, $picH) = getimagesize($strPath);

			// Create a new image resource identifier (the source image)
			$resImgSrc = imagecreatefromjpeg($strPath);

			// Create a Filter instance
			$objFilter = new FilterFun($resImgSrc);


			if(is_callable(array($objFilter, $strFilter))){
			    $objFilter->$strFilter();
			} else {
			    $objFilter->sepia();
			}
				
			// Create a true color image resource identifier (the destination image)
			$resImgDest = imagecreatetruecolor($picW, $picH);

			// Copy part of the source image
			imagecopy($resImgDest, $resImgSrc, 0, 0, 0, 0, $picW, $picH);

			// return the image
			header('Content-type: image/jpeg');
			imagejpeg($resImgDest, null, 100);
			imagedestroy($resImgDest);
			imagedestroy($resImgSrc);


		}

		*/
		/*


        $objResponse = new Response($strBuffer);
        $objResponse->headers->set('Content-Type', 'text/html; charset=UTF-8');
        return $objResponse;

		

		*/







	}
}
