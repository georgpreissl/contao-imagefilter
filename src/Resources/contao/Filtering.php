<?php

namespace GeorgPreissl\Imagefilter;

use Contao\DataContainer;
use Contao\Backend;
use Contao\BackendUser;
use Contao\BackendTemplate;
use Contao\File;

class Filtering extends Backend
{

	public function filterImage(DataContainer $dc)
	{




		


		// filter-form has been submitted, so lets filter the image

		if (strlen($this->Input->get('token')) && $this->Input->get('token') == $this->Session->get('tl_imagefilter'))
		{			
			// load the filter class
			require 'filters/FilterFun.php';

			// var_dump($this->Input->get('filter'));
			$strPath = TL_ROOT.'/'.$this->Input->get('id');

			// get width and height of the original image
			list($width, $height) = getimagesize($strPath);

			// create a new image resource identifier (the source image)
			$resImgSrc = imagecreatefromjpeg($strPath);

			// get the filter parameter
			$strFilter = $this->Input->get('filter');

			// create a filter class instance and call the filter method
			$objFilter = new \FilterFun($resImgSrc);
			if(is_callable(array($objFilter, $strFilter))){
			    $objFilter->$strFilter();
			}else{
			    $objFilter->sepia();
			}
 			
 			// create a true color image resource identifier (the destination image)
			$resImgDest = imagecreatetruecolor($width, $height);

 			// copy part of the source image
			imagecopy($resImgDest, $resImgSrc, 0, 0, 0, 0, $width, $height);

				
			$objFile = new File($this->Input->get('id'));
			
			$error = false;
			if ($this->Input->get('UpdateImage') == $GLOBALS['TL_LANG']['MSC']['imagefilterUpdateImage'])
			{
				// overwrite the image

				// Get the cached thumbnail to destroy it
				$_height = ($objFile->height < 70) ? $objFile->height : 70;
				$_width = (($objFile->width * $_height / $objFile->height) > 400) ? 90 : '';
				$thumbnail = $this->getImage($this->Input->get('id'),$_width, $_height);
				
				$strCacheName = 'system/html/' . $objFile->filename . '-' . substr(md5('-w' . $objFile->width . '-h' . $objFile->height . '-' .urldecode($this->Input->get('id'))), 0, 8) . '.' . $objFile->extension;
				imagejpeg($resImgDest, TL_ROOT.'/'.$this->Input->get('id'));
				imagedestroy($resImgDest);
				$thumbnail = new File($thumbnail);
				$thumbnail->delete();

			} else {
				// create a copy of the image

				// build the new path
				$strNewPath = $objFile->dirname.'/'.$objFile->filename.'_'.$strFilter.'_'.time().'.'.$objFile->extension;
				
				// create a JPEG file from the image resource 
				imagejpeg($resImgDest, $strNewPath);

				// frees any memory associated with the image resource
				imagedestroy($resImgDest);
			}

			if (!$error) 
			{
				// create a log entry
				$this->log('A filter has been applied to the image "'.$this->Input->get('id').'"', 'imagefilter filterImage()', TL_FILES);
			}
			// go back to the file list
			// $this->redirect($this->Environment->script.'?do=files');
			$this->redirect('contao?do=files');

		}
			


		
		// Setup the form

		// create and set the token (e.g. '8d7cfa67389c2df17e192965f7121793')
		$strToken = md5(uniqid('', true));
		$this->Session->set('tl_imagefilter', $strToken);


		if (TL_MODE == 'BE')
		{
			$GLOBALS['TL_JAVASCRIPT'][] = 'https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js'; 
			$GLOBALS['TL_CSS'][] = 'bundles/georgpreisslimagefilter/css/bootstrap.css|static'; 
			$GLOBALS['TL_CSS'][] = 'bundles/georgpreisslimagefilter/css/imagefilter.css|static'; 


			$this->Template = new BackendTemplate('be_imagefilter');
			$this->Template->back = $this->Environment->base . preg_replace('/&(amp;)?(id|key|submit|imagecrop|token)=[^&]*/', '', $this->Environment->request);
			$this->Template->imageSrc = $dc->id;
			$this->Template->inputDo = $this->Input->get('do');
			$this->Template->inputKey = $this->Input->get('key');
			$this->Template->inputId = $this->Input->get('id');
			$this->Template->token = $strToken;

			// get the id of the image
			$objId = $this->Database->prepare("SELECT id FROM tl_files WHERE path = ?")->limit(1)->execute($dc->id);
			$id = $objId->id;
			$this->Template->id = $id;
			
			$this->Template->messages = $this->getMessages();
			$this->Template->filters = array_slice(get_class_methods('FilterFun'), 2); 
			// $this->Template->formAction = ampersand($this->Environment->script);
			$this->Template->formAction = "contao?";

			$strHtml .= $this->Template->parse();
		}

		return $strHtml;

	}
	
}


?>