<?php

/**
 * Image manipulator.
 *
 * @author	Zalazdi <zalazdi@gieromaniak.pl>
 * @author	Krzysztof Trzos <krzysztof.trzos@gieromaniak.pl>
 * @package Image
 * @since	1.0.0
 * @version	1.1.9, 2014-12-14
 */
class Image {

	const PNG				 = 1;
	const JPEG			 = 2;
	const GIF				 = 3;
	const GD				 = 4;
	const GD2				 = 5;
	const RESIZE_ACCURATE	 = 1;
	const RESIZE_RATIO	 = 2;
	const RESIZE_FILL		 = 3;

	/**
	 * @access	private
	 * @var		resource
	 * @since	1.0.0
	 */
	private $rImage = NULL;

	/**
	 * @access	private
	 * @var		string
	 * @since	1.0.0
	 */
	private $sType = NULL;

	/**
	 * @access	private
	 * @var		\FileManager
	 * @since	1.1.6, 2014-07-15
	 */
	private $oImageFile = NULL;

	/**
	 * @access	private
	 * @var		string
	 * @since	1.0.0
	 */
	private $sContentType = NULL;

	/**
	 * @access	private
	 * @var		string
	 * @since	1.0.0
	 */
	private $sExtension = NULL;

	/**
	 * @access	private
	 * @var		integer
	 * @since	1.0.0
	 */
	private $iWidth = NULL;

	/**
	 * @access	private
	 * @var		integer
	 * @since	1.0.0
	 */
	private $iHeight = NULL;

	/**
	 * Factor image.
	 * 
	 * @static
	 * @access	public
	 * @param	string	$sPath
	 * @param	integer	$iType
	 * @return	\Image 
	 */
	public static function factory($sPath, $iType = FALSE) {
		return new \Image($sPath, $iType);
	}

	/**
	 * Create image from path
	 *
	 * @access	public
	 * @param	string	$sPath	path to image
	 * @param	integer	$sType	image type (if null, get by extension)
	 * @return	\Image			Image class
	 * @since	1.0.0
	 * @version	1.1.9, 2014-12-14
	 */
	public function __construct($sPath = FALSE, $sType = FALSE) {
		if($sPath) {
			$sPath = str_replace(DIRECTORY_SEPARATOR, '/', $sPath);

			if(!file_exists($sPath)) {
				throw new \Plethora\Exception\Fatal\Image('Image with path "'.$sPath.'" does not exists!');
			}

			$this->oImageFile = \FileManager::factory()->prepareFileByPath($sPath);

			if(!$sType) {
				$this->sExtension	 = $this->getImageFileObject()->getExt();
				$this->sType		 = static::extensionToImageType($this->sExtension);
			} else {
				$this->sExtension	 = static::imageTypeToExtension($sType);
				$this->sType		 = $sType;
			}

			$this->sContentType = static::extensionToContentType($this->sExtension);

			switch($this->sType) {
				case self::PNG:
					$this->rImage	 = imagecreatefrompng($sPath);
					break;
				case self::JPEG:
					$this->rImage	 = imagecreatefromjpeg($sPath);
					break;
				case self::GIF:
					$this->rImage	 = imagecreatefromgif($sPath);
					break;
				case self::GD:
					$this->rImage	 = imagecreatefromgd($sPath);
					break;
				case self::GD2:
					$this->rImage	 = imagecreatefromgd2($sPath);
					break;
				default:
					throw new \Plethora\Exception\Fatal('Unknown image type!');
			}

			if(!empty($this->rImage)) {
				$this->iWidth	 = imagesx($this->rImage);
				$this->iHeight	 = imagesy($this->rImage);
			}
		}
		return $this;
	}

	/**
	 * Destroy image.
	 * 
	 * @access	public
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function __destruct() {
		if($this->rImage != null) {
			imagedestroy($this->rImage);
		}
	}

	/**
	 * Get image (to send or something).
	 * 
	 * @access	public
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function getImage() {
		if(!$this->sType) {
			$this->sType = self::PNG;
		}

		switch($this->sType) {
			case self::PNG:
				imagepng($this->rImage);
				break;
			case self::JPEG:
				imagejpeg($this->rImage);
				break;
			case self::GIF:
				imagegif($this->rImage);
				break;
			case self::GD:
				imagegd($this->rImage);
				break;
			case self::GD2:
				imagegd2($this->rImage);
				break;
		}
	}

	/**
	 * Get Base64 of image
	 *
	 * @access	public
	 * @return	string
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function getBase64() {
		ob_start();

		$this->getImage();
		$sData = ob_get_contents();

		ob_end_clean();

		return base64_encode($sData);
	}

	/**
	 * Get html code (to show directly).
	 *
	 * @access	public
	 * @return	string
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function getHtmlCode() {
		return '<img src="data:'.$this->sContentType.';base64,'.$this->getBase64().'" />';
	}

	/**
	 * Show image (adding Content-Type header).
	 *
	 * @access	public
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function showImage() {
		header('Conent-Type: '.$this->sContentType);
		$this->getImage();

		return $this;
	}

	/**
	 * Get the file extension from path
	 *
	 * @static
	 * @access	public
	 * @param	string	$sPath
	 * @return	string
	 * @since	1.0.0
	 * @version	1.1.6, 2014-07-15
	 */
	public static function getFileExtension($sPath) {
		$aExt = explode('.', $sPath);

		return end($aExt);
	}

	/**
	 * Get image file data object.
	 * 
	 * @access	public
	 * @return	\FileManager
	 * @since	1.1.6, 2014-07-15
	 * @version	1.1.6, 2014-07-15
	 */
	public function getImageFileObject() {
		return $this->oImageFile;
	}

	/**
	 * Change content type to image type.
	 * 
	 * @static
	 * @access	public
	 * @param	string	$sContentType
	 * @return	integer|FALSE
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public static function contentTypeToImageType($sContentType) {
		switch($sContentType) {
			case 'image/png':
				return self::PNG;
			case 'image/jpeg':
				return self::JPEG;
			case 'image/gif':
				return self::GIF;
			case 'image/gd':
				return self::GD;
			case 'image/gd2':
				return self::GD2;
		}

		return FALSE;
	}

	/**
	 * Change image type to content type
	 *
	 * @static
	 * @access	public
	 * @param	integer	$iType
	 * @return	string|FALSE
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public static function imageTypeToContentType($iType) {
		switch($iType) {
			case self::PNG:
				return 'image/png';
			case self::JPEG:
				return 'image/jpeg';
			case self::GIF:
				return 'image/gif';
			case self::GD:
				return 'image/gd';
			case self::GD2:
				return 'image/gd2';
		}

		return FALSE;
	}

	/**
	 * Change extention to image type
	 *
	 * @static
	 * @access	public
	 * @param	string	$sExtension
	 * @return	integer|FALSE
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public static function extensionToImageType($sExtension) {
		switch($sExtension) {
			case 'jpg':
				return self::JPEG;
			case 'jpeg':
				return self::JPEG;
			case 'png':
				return self::PNG;
			case 'gif':
				return self::GIF;
			case 'gd':
				return self::GD;
			case 'gd2':
				return self::GD2;
		}

		return FALSE;
	}

	/**
	 * Change image type to extension.
	 *
	 * @static
	 * @access	public
	 * @param	integer	$iType
	 * @return	string|FALSE
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function imageTypeToExtension($iType) {
		switch($iType) {
			case self::PNG:
				return 'png';
			case self::JPEG:
				return 'jpeg';
			case self::GIF:
				return 'gif';
			case self::GD:
				return 'gd';
			case self::GD2:
				return 'gd2';
		}

		return FALSE;
	}

	/**
	 * Change extension to content type.
	 *
	 * @static
	 * @access	public
	 * @param	string	$sExtension
	 * @return	string|FALSE
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public static function extensionToContentType($sExtension) {
		switch($sExtension) {
			case 'jpg':
				return 'image/jpeg';
			case 'jpeg':
				return 'image/jpeg';
			case 'png':
				return 'image/png';
			case 'gif':
				return 'image/gif';
			case 'gd':
				return 'image/gd';
			case 'gd2':
				return 'image/gd2';
		}

		return FALSE;
	}

	/**
	 * Change content type to extension
	 *
	 * @static
	 * @access	public
	 * @param	string	$sContentType
	 * @return	string|FALSE
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public static function contentTypeToExtension($sContentType) {
		switch($sContentType) {
			case 'image/png':
				return 'png';
			case 'image/jpeg':
				return 'jpeg';
			case 'image/gif':
				return 'gif';
			case 'image/gd':
				return 'gd';
			case 'image/gd2':
				return 'gd2';
		}

		return FALSE;
	}

	/**
	 * Get image width.
	 *
	 * @access	public
	 * @return	integer
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function getWidth() {
		return $this->iWidth;
	}

	/**
	 * Get image height.
	 *
	 * @access	public
	 * @return integer height
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function getHeight() {
		return $this->iHeight;
	}

	/**
	 * Grayscaling
	 *
	 * @access	public
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function grayScale() {
		for($x = 0; $x < $this->iWidth; $x++) {
			for($y = 0; $y < $this->iHeight; $y++) {
				$rgb	 = imagecolorat($this->rImage, $x, $y);
				$r		 = ($rgb >> 16) & 0xFF;
				$g		 = ($rgb >> 8) & 0xFF;
				$b		 = $rgb & 0xFF;
				$c		 = round(($r + $g + $b) / 3);
				$color	 = imagecolorallocate($this->rImage, $c, $c, $c);

				imagesetpixel($this->rImage, $x, $y, $color);
			}
		}

		return $this;
	}

	/**
	 * Brightening
	 *
	 * @access	public
	 * @param	integer	$iBrightness
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function brighten($iBrightness = 50) {
		for($x = 0; $x < $this->iWidth; $x++) {
			for($y = 0; $y < $this->iHeight; $y++) {
				$rgb = imagecolorat($this->rImage, $x, $y);

				$r	 = max(min($iBrightness + (($rgb >> 16) & 0xFF), 255), 0);
				$g	 = max(min($iBrightness + (($rgb >> 8) & 0xFF), 255), 0);
				$b	 = max(min($iBrightness + ($rgb & 0xFF), 255), 0);

				$color = imagecolorallocate($this->rImage, $r, $g, $b);
				imagesetpixel($this->rImage, $x, $y, $color);
			}
		}

		return $this;
	}

	/**
	 * Colorize image.
	 *
	 * @access	public
	 * @param	integer	$iRed
	 * @param	integer	$iGreen
	 * @param	integer	$iBlue
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function colorize($iRed = 50, $iGreen = 50, $iBlue = 50) {
		for($x = 0; $x < $this->iWidth; $x++) {
			for($y = 0; $y < $this->iHeight; $y++) {
				$rgb = imagecolorat($this->rImage, $x, $y);

				$r	 = max(min($iRed + (($rgb >> 16) & 0xFF), 255), 0);
				$g	 = max(min($iGreen + (($rgb >> 8) & 0xFF), 255), 0);
				$b	 = max(min($iBlue + ($rgb & 0xFF), 255), 0);

				$iColor = imagecolorallocate($this->rImage, $r, $g, $b);
				imagesetpixel($this->rImage, $x, $y, $iColor);
			}
		}

		return $this;
	}

	/**
	 * Pixelate.
	 *
	 * @access	public
	 * @param	integer	$iBlockSize
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function pixelate($iBlockSize = 1) {
		if($iBlockSize < 0) {
			$iBlockSize = 0;
		}

		for($y = 0; $y < $this->iHeight; $y += $iBlockSize + 1) {
			for($x = 0; $x < $this->iWidth; $x += $iBlockSize + 1) {
				$rgb = imagecolorsforindex($this->rImage, imagecolorat($this->rImage, $x, $y));

				$color = imagecolorclosest($this->rImage, $rgb['red'], $rgb['green'], $rgb['blue']);
				imagefilledrectangle($this->rImage, $x, $y, $x + $iBlockSize, $y + $iBlockSize, $color);
			}
		}

		return $this;
	}

	/**
	 * Blurring.
	 *
	 * @access	public
	 * @param	integer	$iDistance
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function blur($iDistance = 1) {
		$rTempImg = imagecreatetruecolor($this->iWidth, $this->iHeight);
		imagecopy($rTempImg, $this->rImage, 0, 0, 0, 0, $this->iWidth, $this->iHeight);

		$pct = 70;
		imagecopymerge($rTempImg, $this->rImage, 0, 0, 0, $iDistance, $this->iWidth - $iDistance, $this->iHeight - $iDistance, $pct);
		imagecopymerge($this->rImage, $rTempImg, 0, 0, $iDistance, 0, $this->iWidth - $iDistance, $this->iHeight, $pct);
		imagecopymerge($rTempImg, $this->rImage, 0, $iDistance, 0, 0, $this->iWidth, $this->iHeight, $pct);
		imagecopymerge($this->rImage, $rTempImg, $iDistance, 0, 0, 0, $this->iWidth, $this->iHeight, $pct);

		imagedestroy($rTempImg);

		return $this;
	}

	/**
	 * Embossing.
	 *
	 * @access	public
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function emboss() {
		$this->grayScale();

		$aEmboss = array(array(2, 0, 0), array(0, -1, 0), array(0, 0, -1));
		imageconvolution($this->rImage, $aEmboss, 1, 127);

		return $this;
	}

	/**
	 * Negating.
	 *
	 * @access	public
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function negate() {
		for($x = 0; $x < $this->iWidth; $x++) {
			for($y = 0; $y < $this->iHeight; $y++) {
				$iRGB	 = imagecolorat($this->rImage, $x, $y);
				$r		 = 0xFF - (($iRGB >> 16) & 0xFF);
				$g		 = 0xFF - (($iRGB >> 8) & 0xFF);
				$b		 = 0xFF - ($iRGB & 0xFF);
				$iColor	 = imagecolorallocate($this->rImage, $r, $g, $b);

				imagesetpixel($this->rImage, $x, $y, $iColor);
			}
		}

		return $this;
	}

	/**
	 * Change image to sepia.
	 *
	 * @access	public
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function sepia() {
		for($_x = 0; $_x < $this->iWidth; $_x++) {
			for($_y = 0; $_y < $this->iHeight; $_y++) {
				$iRGB	 = imagecolorat($this->rImage, $_x, $_y);
				$r		 = ($iRGB >> 16) & 0xFF;
				$g		 = ($iRGB >> 8) & 0xFF;
				$b		 = $iRGB & 0xFF;

				$y	 = $r * 0.299 + $g * 0.587 + $b * 0.114;
				$i	 = 0.15 * 0xFF;
				$q	 = -0.001 * 0xFF;

				$r	 = $y + 0.956 * $i + 0.621 * $q;
				$g	 = $y - 0.272 * $i - 0.647 * $q;
				$b	 = $y - 1.105 * $i + 1.702 * $q;

				if($r < 0 || $r > 0xFF) {
					$r = ($r < 0) ? 0 : 0xFF;
				}
				if($g < 0 || $g > 0xFF) {
					$g = ($g < 0) ? 0 : 0xFF;
				}
				if($b < 0 || $b > 0xFF) {
					$b = ($b < 0) ? 0 : 0xFF;
				}

				$iColor = imagecolorallocate($this->rImage, $r, $g, $b);
				imagesetpixel($this->rImage, $_x, $_y, $iColor);
			}
		}

		return $this;
	}

	/**
	 * Resize image.
	 * 
	 * Allowed is three methods:
	 * - RESIZE_RATIO: keeps the ratio
	 * - RESIZE_ACCURATE: accurate resize, doesn't keep the ratio
	 * - RESIZE_FILL: centres and fill the image with given color and alpha
	 *
	 * @access	public
	 * @param	integer	$iMaxWidth	width (RESIZE_ACCURATE) or max width (RESIZE_FILL, RESIZE_RATIO)
	 * @param	integer $iMaxHeight	height (RESIZE_ACCURATE) or max height (RESIZE_FILL, RESIZE_RATIO)
	 * @param	integer $iType		resize type
	 * @param	integer	$iRed		only in RESIZE_FILL, alpha color
	 * @param	integer	$iGreen		only in RESIZE_FILL, red color
	 * @param	integer	$iBlue		only in RESIZE_FILL, green color
	 * @param	integer	$iAlpha		only in RESIZE_FILL, blue color
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public function resize($iMaxWidth, $iMaxHeight = FALSE, $iType = FALSE, $iRed = 255, $iGreen = 255, $iBlue = 255, $iAlpha = 127) {
		if($iMaxHeight === FALSE) {
			$iMaxHeight = $iMaxWidth;
		}

		if($iType === FALSE) {
			$iType = self::RESIZE_RATIO;
		}

		if($iType == self::RESIZE_RATIO) {
			$fRatio		 = ($this->iWidth / $iMaxWidth > $this->iHeight / $iMaxHeight) ? $this->iWidth / $iMaxWidth : $this->iHeight / $iMaxHeight;
			$iNewWidth	 = ceil($this->iWidth / $fRatio);
			$iNewHeight	 = ceil($this->iHeight / $fRatio);

			$rNewImage = imagecreatetruecolor($iNewWidth, $iNewHeight);

			ImageCopyResampled($rNewImage, $this->rImage, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $this->iWidth, $this->iHeight);

			$this->iWidth	 = $iNewWidth;
			$this->iHeight	 = $iNewHeight;
			$this->rImage	 = $rNewImage;
		} elseif($iType == self::RESIZE_ACCURATE) {
			$rNewImage = imagecreatetruecolor($iMaxWidth, $iMaxHeight);

			ImageCopyResampled($rNewImage, $this->rImage, 0, 0, 0, 0, $iMaxWidth, $iMaxHeight, $this->iWidth, $this->iHeight);

			$this->iWidth	 = $iMaxWidth;
			$this->iHeight	 = $iMaxHeight;
			$this->rImage	 = $rNewImage;
		} elseif($iType == self::RESIZE_FILL) {
			$this->sType		 = self::PNG;
			$this->sContentType	 = 'image/png';
			$this->sExtension	 = 'png';

			$rNewImage	 = imagecreatetruecolor($iMaxWidth, $iMaxHeight);
			$iBackground = imagecolorallocatealpha($rNewImage, $iRed, $iGreen, $iBlue, $iAlpha);

			imagesavealpha($rNewImage, true);
			imagefill($rNewImage, 0, 0, $iBackground);

			$fRatio	 = ($this->iWidth / $iMaxWidth > $this->iHeight / $iMaxHeight) ? $this->iWidth / $iMaxWidth : $this->iHeight / $iMaxHeight;
			$iWidth	 = ceil($this->iWidth / $fRatio);
			$iHeight = ceil($this->iHeight / $fRatio);

			if($iWidth > $iHeight) {
				$iFromWidth	 = 0;
				$iFromHeight = ceil(($iMaxHeight - $iHeight) / 2);
			} else {
				$iFromWidth	 = ceil(($iMaxHeight - $iHeight) / 2);
				$iFromHeight = 0;
			}

			ImageCopyResampled($rNewImage, $this->rImage, $iFromWidth, $iFromHeight, 0, 0, $iWidth, $iHeight, $this->iWidth, $this->iHeight);
			$this->rImage = $rNewImage;
		}

		return $this;
	}

	/**
	 * Thumb creating method
	 * 
	 * @static
	 * @access	public
	 * @param	string	$sPathToImage
	 * @param	integer	$iWidth
	 * @param	integer	$iHeight
	 * @return	string					Path to miniature
	 * @since	1.0.0
	 * @version	1.1.1, 2014-07-09
	 */
	public static function getThumb($sPathToImage, $iWidth, $iHeight) {
		$aExploded	 = explode('/', $sPathToImage);
		$sImageName	 = array_pop($aExploded);
		$sPath		 = implode('/', $aExploded);

		\FileManager::prepareDir($sPath.'/min');

		if(file_exists($sPath.'/min/'.$sImageName)) {
			return '';
		}

		\Image::factory($sPathToImage)
			->resize($iWidth, $iHeight)
			->save($sPath.'/min/'.$sImageName);

		return $sPath.'/min/'.$sImageName;
	}

	/**
	 * Save the image in given path.
	 *
	 * @access	public
	 * @param	string	$sPath		path
	 * @param	integer	$iQuality	only if jpg/jpeg
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.7, 2014-07-15
	 */
	public function save($sPath, $iQuality = 100) {
		$sPath			 = str_replace('/', DIRECTORY_SEPARATOR, $sPath);
		$aExplodedPath	 = explode('.', $sPath);
		$iType			 = static::extensionToImageType(end($aExplodedPath));

		if(\FileManager::prepareDir($sPath, TRUE) === FALSE) {
			throw new \Plethora\Exception\Fatal('Can\'t create particular path ("'.$sPath.'").');
		}

		switch($iType) {
			case self::PNG:
				imagepng($this->rImage, $sPath);
				break;
			case self::JPEG:
				imagejpeg($this->rImage, $sPath, $iQuality);
				break;
			case self::GIF:
				imagegif($this->rImage, $sPath);
				break;
			case self::GD:
				imagegd($this->rImage, $sPath);
				break;
			case self::GD2:
				imagegd2($this->rImage, $sPath);
				break;
		}

		return $this;
	}

	/**
	 * Add text watermark to image
	 * 
	 * @access	public
	 * @param	string			$sText
	 * @param	string|integer	$mHorizontalPart	y position (can be integer, bottom, center or top)
	 * @param	string|integer	$mVerticalPart		x position (can be integer, left, center, right)
	 * @param	integer			$iSize				font size
	 * @param	string			$sFont				font path
	 * @param	integer			$iRed				red
	 * @param	integer			$iGreen				green
	 * @param	integer			$iBlue				blue
	 * @param	integer			$iAlpha				alpha (0-127)
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.2, 2014-07-13
	 */
	public function watermark($sText, $mHorizontalPart = 'right', $mVerticalPart = 'bottom', $iSize = 10, $sFont = 'fonts/arial.ttf', $iRed = 0, $iGreen = 0, $iBlue = 0, $iAlpha = 0) {
		$aBox	 = imagettfbbox($iSize, 0, $sFont, $sText);
		$iWidth	 = abs($aBox[4] - $aBox[0]);
		$iHeight = abs($aBox[3] - $aBox[5]);

		// check string versions
		switch($mHorizontalPart) {
			case 'right':
				$mHorizontalPart = -5;
				break;
			case 'center':
				$mHorizontalPart = round(($this->iWidth - $iWidth) / 2);
				break;
			case 'left':
				$mHorizontalPart = 5;
				break;
			default:
				throw new \Plethora\Exception\Fatal('Wrong value on $mHorizontalPart argument of watermark() function.');
		}

		switch($mVerticalPart) {
			case 'top':
				$mVerticalPart	 = 5;
				break;
			case 'top':
				$mVerticalPart	 = round(($this->iHeight - $iHeight) / 2);
				break;
			case 'bottom':
				$mVerticalPart	 = -5;
				break;
			default:
				throw new \Plethora\Exception\Fatal('Wrong value on $mHorizontalPart argument of watermark() function.');
		}

		// check integer values
		if($mHorizontalPart < 0) {
			$mHorizontalPart = $this->iWidth - $iWidth + $mHorizontalPart;
		}

		if($mVerticalPart < 0) {
			$mVerticalPart = $this->iHeight + $mVerticalPart;
		} else {
			$mVerticalPart += $iHeight;
		}

		$iColor = imagecolorallocatealpha($this->rImage, $iRed, $iGreen, $iBlue, $iAlpha);

		imagettftext($this->rImage, $iSize, 0, $mHorizontalPart, $mVerticalPart, $iColor, $sFont, $sText);

		// return
		return $this;
	}

	/**
	 * Add an image as watermark.
	 *
	 * @access	public
	 * @param	\Image			$oImage
	 * @param	string|integer	$mHorizontalPart	position (can be integer, bottom, center or top)
	 * @param	string|integer	$mVerticalPart		x position (can be integer, left, center, right)
	 * @param	integer								alpha in percent
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.2, 2014-07-13
	 */
	public function watermarkImage(Image $oImage, $mHorizontalPart = 'right', $mVerticalPart = 'bottom', $iAlpha = 100) {
		// check string versions
		if(is_string($mHorizontalPart)) {
			switch($mHorizontalPart) {
				case 'right':
					$mHorizontalPart = -5;
					break;
				case 'center':
					$mHorizontalPart = round(($this->iWidth - $oImage->iWidth) / 2);
					break;
				case 'left':
					$mHorizontalPart = 5;
					break;
				default:
					throw new \Plethora\Exception\Fatal('Wrong value on $mHorizontalPart argument of watermarkImage() function.');
			}
		}

		if(is_string($mVerticalPart)) {
			switch($mVerticalPart) {
				case 'top':
					$mVerticalPart	 = 5;
					break;
				case 'top':
					$mVerticalPart	 = round(($this->iHeight - $oImage->iHeight) / 2);
					break;
				case 'bottom':
					$mVerticalPart	 = -5;
					break;
				default:
					throw new \Plethora\Exception\Fatal('Wrong value on $mHorizontalPart argument of watermarkImage() function.');
			}
		}

		// check integer versions (if smaller than 0)
		if($mHorizontalPart < 0) {
			$mHorizontalPart = $this->iWidth - $oImage->iWidth + $mHorizontalPart;
		}

		if($mVerticalPart < 0) {
			$mVerticalPart = $this->iHeight - $oImage->iHeight + $mVerticalPart;
		}

		// create new image
		imagecopymerge($this->rImage, $oImage->rImage, $mHorizontalPart, $mVerticalPart, 0, 0, $oImage->iWidth, $oImage->iHeight, $iAlpha);

		// return
		return $this;
	}

	/**
	 * Crop image
	 * 
	 * @access	public
	 * @param	integer	$iStartX	image start x
	 * @param	integer	$iStartY	image start y
	 * @param	integer	$iWidth		crop width
	 * @param	integer	$iHeight	crop height
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.2, 2014-07-13
	 */
	public function crop($iStartX, $iStartY, $iWidth, $iHeight) {
		$oNewImage = ImageCreateTrueColor($iWidth, $iHeight);

		ImageCopyResized($oNewImage, $this->rImage, 0, 0, $iStartX, $iStartY, $iWidth, $iHeight, $iWidth, $iHeight);

		$this->iWidth	 = $iWidth;
		$this->iHeight	 = $iHeight;
		$this->rImage	 = $oNewImage;

		return $this;
	}

	/**
	 * Rotate image
	 *
	 * @access	public
	 * @param	integer	$mAngle			angle of rotation
	 * @param	boolean	$bTransparent	set true if background have to be transparent
	 * @param	integer	$iRed			red
	 * @param	integer	$iGreen			green
	 * @param	integer	$iBlue			blue
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.2, 2014-07-13
	 */
	public function rotate($mAngle = 180, $bTransparent = TRUE, $iRed = 0, $iGreen = 0, $iBlue = 0) {
		if(is_string($mAngle)) {
			switch($mAngle) {
				case 'left':
					$mAngle	 = 90;
					break;
				case 'right':
					$mAngle	 = 270;
					break;
				case 'upside':
					$mAngle	 = 180;
					break;
				default:
					throw new \Plethora\Exception\Fatal('Wrong value on $mAngle argument of rotate() function.');
			}
		}

		if($mAngle < 0 || $mAngle > 360) {
			new \Plethora\Exception\Fatal('Angle passed out of range: [0,360]');
		}

		if($mAngle == 0 || $mAngle == 360) {
			return $this;
		}

		$this->sType		 = self::PNG;
		$this->sContentType	 = 'image/png';
		$this->sExtension	 = 'png';

		$iColor = imagecolorallocate($this->rImage, $iRed, $iGreen, $iBlue);

		if($bTransparent) {
			$iColor = imagecolortransparent($this->rImage);
		}

		$rPng = imagecreatetruecolor($this->iWidth, $this->iHeight);

		imagefill($rPng, 0, 0, $iColor);
		imagecopy($rPng, $this->rImage, 0, 0, 0, 0, $this->iWidth, $this->iWidth);

		$this->rImage = imagerotate($rPng, $mAngle, $iColor, 1);

		return $this;
	}

	/**
	 * Create a border.
	 *
	 * @access	public
	 * @param	integer $iSize		size of border in pixels
	 * @param	integer	$iRed		red
	 * @param	integer	$iGreen		green
	 * @param	integer	$iBlue		blue
	 * @param	integer $iAlpha
	 * @param	integer $bInside	set true if want border inside of photo or false if want border outside of photo
	 * @return	\Image
	 * @since	1.0.0
	 * @version	1.1.2, 2014-07-13
	 */
	public function border($iSize = 1, $iRed = 0, $iGreen = 0, $iBlue = 0, $iAlpha = 0, $bInside = TRUE) {
		$iColor = imagecolorallocatealpha($this->rImage, $iRed, $iGreen, $iBlue, $iAlpha);

		if($bInside) {
			for($i = 0; $i < $iSize; ++$i) {
				imagerectangle($this->rImage, $i, $i, $this->iWidth - 1 - $i, $this->iHeight - 1 - $i, $iColor);
			}
		} else {
			$iNewWidth	 = $this->iWidth + ($iSize * 2);
			$iNewHeight	 = $this->iHeight + ($iSize * 2);
			$rNewImage	 = imagecreatetruecolor($iNewWidth, $iNewHeight);

			imagefilledrectangle($rNewImage, 0, 0, $iNewWidth, $iNewHeight, $iColor);
			imagecopyresized($rNewImage, $this->rImage, $iSize, $iSize, 0, 0, $this->iWidth, $this->iHeight, $this->iWidth, $this->iHeight);

			$this->rImage = $rNewImage;
		}

		return $this;
	}

	/**
	 * Create new image style instance.
	 * 
	 * @static
	 * @access	public
	 * @param	string	$sImgPath	path to image
	 * @param	string	$sStyle		style name
	 * @return	\Image\ImageStyle
	 * @throws	\Plethora\Exception\Fatal
	 * @since	1.1.3, 2014-07-14
	 * @version	1.1.8, 2014-11-19
	 */
	public static function stylize($sImgPath, $sStyle = NULL) {
		if(empty($sImgPath)) {
			throw new \Plethora\Exception\Fatal('Image path is empty.');
		}

		require_once __DIR__.'/Image/ImageStyle.php';

		return \Image\ImageStyle::factory($sImgPath, $sStyle);
	}

}

/**
 * CHANGELOG:
 * 1.1.9, 2014-12-14: Zwracanie wyjątku \Plethora\Exception\Fatal\Image kiedy podano ścieżkę do nieistniejącego obrazka.
 * 1.1.8, 2014-11-19: Zabezpieczenie metody ::stylize() przed wprowadzeniem pustej ścieżki do obrazka.
 * 1.1.7, 2014-07-15: Wprowadzenie zabezpieczenia w metodzie save() w przypadku, kiedy dana ścieżka nie istnieje.
 * 1.1.6, 2014-07-15: Zmiana nazwy metody "newStyle" na "stylize".
 * 1.1.6, 2014-07-15: Wprowadzenie zmiennej (wraz z jej metodami) przechowującej dane PLIKU obrazka.
 * 1.1.5, 2014-07-15: Drobne poprawki w formatowaniu kodu metody save().
 * 1.1.4, 2014-07-14: Zwracanie błędu jeśli podana ścieżka do pliku (obrazu) jest niepoprawna oraz dodanie wyrzucania błędu, jeśli typ obrazu nie zostanie zidentyfikowany poprawnie.
 * 1.1.3, 2014-07-14: Utworzenie statycznej metody ::newStyle().
 * 1.1.2, 2014-07-13: Dalsze formatowanie / modernizacja kodu.
 * 1.1.1, 2014-07-09: Dalsze formatowanie / modernizacja kodu.
 * 1.1.0, 2014-07-07: Formatowanie / modernizacja kodu.
 */