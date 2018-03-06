<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 *
 * The TYPOlight webCMS is an accessible web content management system that 
 * specializes in accessibility and generates W3C-compliant HTML code. It 
 * provides a wide range of functionality to develop professional websites 
 * including a built-in search engine, form generator, file and user manager, 
 * CSS engine, multi-language support and many more. For more information and 
 * additional TYPOlight applications like the TYPOlight MVC Framework please 
 * visit the project website http://www.typolight.org.
 *
 * 
 *
 * @author     Georg Preissl <http://www.georg-preissl.at> 
 * @package    imagecrop
 * @license    MIT
 * 
 */

class ImageFilter extends Backend
{

	public function filterImage(DataContainer $dc)
	{
		
		// load the filter class
		require 'filters/Filter.php';


		// filter-form has been submitted, so lets filter the image

		if (strlen($this->Input->get('token')) && $this->Input->get('token') == $this->Session->get('tl_imagecrop'))
		{			

			$strPath = TL_ROOT.'/'.$this->Input->get('id');

			// get width and height of the original image
			list($width, $height) = getimagesize($strPath);

			// create a new image resource identifier (the source image)
			$resImgSrc = imagecreatefromjpeg($strPath);

			// get the filter parameter
			$strFilter = $this->Input->get('filter');

			// create a filter class instance and call the filter method
			$objFilter = new Filter($resImgSrc);
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
			$this->redirect($this->Environment->script.'?do=files');
		}
			


		
		// Setup the form

		// create and set the token (e.g. '8d7cfa67389c2df17e192965f7121793')
		$strToken = md5(uniqid('', true));
		$this->Session->set('tl_imagecrop', $strToken);


		if (TL_MODE == 'BE')
		{
			$GLOBALS['TL_JAVASCRIPT'][] = 'https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js'; 
			$GLOBALS['TL_CSS'][] = 'system/modules/imagefilter/html/css/bootstrap.css'; 
			$GLOBALS['TL_CSS'][] = 'system/modules/imagefilter/html/css/imagefilter.css'; 

			$this->Template = new BackendTemplate('be_imagefilter');
			$this->Template->back = $this->Environment->base . preg_replace('/&(amp;)?(id|key|submit|imagecrop|token)=[^&]*/', '', $this->Environment->request);
			$this->Template->imageSrc = $dc->id;
			$this->Template->inputDo = $this->Input->get('do');
			$this->Template->inputKey = $this->Input->get('key');
			$this->Template->inputId = $this->Input->get('id');
			$this->Template->token = $strToken;
			$this->Template->messages = $this->getMessages();
			$this->Template->filters = array_slice(get_class_methods('Filter'), 2); 
			$this->Template->formAction = ampersand($this->Environment->script, ENCODE_AMPERSANDS);

			$strHtml .= $this->Template->parse();
		}

		return $strHtml;

	}
	
}


?>