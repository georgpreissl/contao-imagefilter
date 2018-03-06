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
 * @package    imagefilter
 * @license    MIT
 * 
 */




array_insert($GLOBALS['TL_DCA']['tl_files']['list']['operations'], 1, array
	(
		'imagefilter' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_files']['imagefilter'],
				'href'                => 'key=imagefilter',
				'icon'                => 'system/modules/imagefilter/html/filter.svg',
				'button_callback'     => array('tl_imagefilter', 'getFilterIcon')
			)
	)
);


/**
 * Class tl_imagefilter
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 */

class tl_imagefilter extends tl_files
{

	/**
	 * Return the imagefilter button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function getFilterIcon($row, $href, $label, $title, $icon, $attributes)
	{
		$this->import('BackendUser', 'User');

		if (!$this->User->isAdmin && !in_array('f5', $this->User->fop))
		{
			return '';
		}

		$strDecoded = urldecode($row['id']);

		if (is_dir(TL_ROOT . '/' . $strDecoded))
		{
			return '';
		}

		$objFile = new File($strDecoded);

		if (!in_array($objFile->extension, array('svg','jpg','jpeg')))
		{
			return '';
		}
		return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}
	
	
	
}


?>