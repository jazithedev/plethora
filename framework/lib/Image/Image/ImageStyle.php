<?php

namespace Image;

defined('PATH_ROOT') OR die('No direct script access.');

/**
 * Using image styles.
 * 
 * @package	Form
 * @author	Krzysztof Trzos <krzysztof.trzos@gieromaniak.pl>
 * @since	2.9.4, 2014-07-13
 * @version	1.1.0, 2014-07-15
 */
class ImageStyle {

	/**
	 * @access	private
	 * @var		\Image
	 * @since	1.0.0, 2014-07-13
	 */
	private $oImage = NULL;

	/**
	 * @access	private
	 * @var		array
	 * @since	1.0.0, 2014-07-13
	 */
	private static $aImageStyles = NULL;

	/**
	 * Factory new image style.
	 * 
	 * @static
	 * @access	public
	 * @param	string	$sPath
	 * @return	\Image\ImageStyle
	 * @since	1.0.0, 2014-07-13
	 * @version	1.1.0, 2014-07-15
	 */
	public static function factory($sPath, $sStyle = NULL) {
		if($sStyle === NULL) {
			new ImageStyle($sPath);
		} else {
			$oImageStyle = new ImageStyle($sPath, $sStyle);
			
			return $oImageStyle->apply($sStyle);
		}
	}

	/**
	 * Constructor.
	 * 
	 * @access	public
	 * @param	string	$sPath
	 * @since	1.0.1, 2014-07-14
	 * @version	1.1.0, 2014-07-15
	 */
	public function __construct($sPath) {
		$this->oImage = \Image::factory($sPath);
	}

	/**
	 * Get image instance.
	 * 
	 * @access	public
	 * @return	\Image
	 * @since	1.0.1, 2014-07-14
	 * @version	1.0.1, 2014-07-14
	 */
	public function &getImage() {
		return $this->oImage;
	}

	/**
	 * Apply style to particular image.
	 * 
	 * @static
	 * @access	public
	 * @param	string	$sStyleName
	 * @since	1.0.0, 2014-07-13
	 * @version	1.1.0, 2014-07-15
	 */
	public function apply($sStyleName) {
		if(static::$aImageStyles === NULL) {
			static::$aImageStyles = \Plethora\Config::get('image_styles');
		}

		$aStyles = \Plethora\Helper\Arrays::get(static::$aImageStyles, $sStyleName);

		if($aStyles === NULL) {
			throw new \Plethora\Exception\Fatal('Image style "'.$sStyleName.'" do not exists.');
		}

		$oImage				 = $this->getImage();
		$oImageFile			 = $oImage->getImageFileObject();
		$sStyledImagePath	 = 'uploads/image_styles/'.$sStyleName.'/'.$oImageFile->getName().'.'.$oImageFile->getExt();

		if(!file_exists($sStyledImagePath)) {
			foreach($aStyles as $aStyle) {
				$oImage = call_user_func_array(array($oImage, $aStyle[0]), $aStyle[1]);
			}

			$oImage->save($sStyledImagePath);
		}

		return $sStyledImagePath;
	}

}

/**
 * CHANGELOG:
 * 1.1.0, 2014-07-15: Utworzenie pierwszej, działającej wersji klasy stylującej obrazki.
 * 1.0.1, 2014-07-14: Dalsze rozwijanie funkcjonalności klasy.
 * 1.0.0, 2014-07-13: Utworzenie pliku.
 */