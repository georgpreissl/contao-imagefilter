<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');



use Contao\File;




array_insert($GLOBALS['TL_DCA']['tl_files']['list']['operations'], 1, array
	(
		'imagefilter' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_files']['imagefilter'],
				'href'                => 'key=imagefilter',
				'icon'                => 'bundles/georgpreisslimagefilter/icons/filter.svg',
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