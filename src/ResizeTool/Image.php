<?php
/**
 * @author Leonid Gushchin aka Centurion 11.01.2016
 */
namespace ResizeTool;

use ResizeTool\Exception;

/**
 * Class Image
 * @package ResizeTool
 */
class Image
{

	protected
		/**
		 * @var resource
		 */
		$_resource,
		/**
		 * @var string
		 */
		$_path,
		/**
		 * @var string e.g "image/jpeg"
		 */
		$_mime,
		/**
		 * @var int
		 */
		$_width,
		/**
		 * @var int
		 */
		$_height,
		/**
		 * @var int
		 */
		$_backgroundColor = 0xFFFFFF;

	/**
	 * Create new image object
	 * @param string $path
	 * @throws Exception\FileNotFoundException
	 * @throws Exception\InvalidFileException
	 * @throws Exception\InvalidMimeTypeException
	 */
	public function __construct($path)
	{
		$this->_path = $path;

		if (!file_exists($this->_path)) {
			throw new Exception\FileNotFoundException("File `{$this->_path}` not found");
		}

		if (!($size = getimagesize($this->_path))) {
			throw new Exception\InvalidFileException("Invalid file `{$this->_path}`");
		}

		$this->_width = $size[0];
		$this->_height = $size[1];
		$this->_mime = $size['mime'];
		if ($this->_mime != 'image/jpeg') {
			throw new Exception\InvalidMimeTypeException("Invalid mime type `{$this->_path}`");
		}
		$this->_resource = imagecreatefromjpeg($this->_path);
	}

	/**
	 * @return string
	 */
	public function getMime()
	{
		return $this->_mime;
	}

	/**
	 * @return int
	 */
	public function getWidth()
	{
		return $this->_width;
	}

	/**
	 * @return int
	 */
	public function getHeight()
	{
		return $this->_height;
	}

	/**
	 * Resize image and return $this
	 * @param int $width
	 * @param int $height
	 * @return static
	 */
	public function resize($width = 0, $height = 0)
	{
		$width = (int)$width;
		$height = (int)$height;

		if (0 == $width) {
			$width = ceil($this->_width * $height / $this->_height);
			$height = (int)($this->_height * $width / $this->_width);
			$width = (int)$width;
		} elseif (0 == $height) {
			$height = intval($this->_height * $width / $this->_width);
		} else {
			if ($height / $this->_height > $width / $this->_width) {
				$height = (int)($this->_height * $width / $this->_width);
			} else {
				$width = intval($this->_width * $height / $this->_height);
			}
		}

		$newResource = imagecreatetruecolor($width, $height);
		imagealphablending($newResource, false);
		imagesavealpha($newResource, true);
		imagecopyresampled($newResource, $this->_resource, 0, 0, 0, 0,
			$width, $height, $this->_width, $this->_height);

		$this->_width = $width;
		$this->_height = $height;
		$this->_resource = $newResource;

		return $this;
	}

	/**
	 * Set background color of image and return $this
	 * @param int $color
	 * @return static
	 */
	public function setBackgroudColor($color = 0xFFFFFF)
	{
		$this->_backgroundColor = $color;

		return $this;
	}

	/**
	 * Crop center part of image and return $this
	 * @param int $width
	 * @param int $height
	 * @return static
	 */
	public function cropCenter($width, $height)
	{
		$x = (int)(($this->_width - $width) / 2);
		$y = (int)(($this->_height - $height) / 2);
		return $this->crop($x, $y, $width, $height);
	}

	/**
	 * Crop image and return $this
	 * @param $x
	 * @param $y
	 * @param int $width
	 * @param int $height
	 * @return static
	 */
	public function crop($x, $y, $width, $height)
	{
		$x = (int)$x;
		$y = (int)$y;
		$width = (int)$width;
		$height = (int)$height;

		$newResource = imagecreatetruecolor($width, $height);
		imagealphablending($newResource, false);
		imagesavealpha($newResource, true);
		imagefill($newResource, 0, 0, $this->_backgroundColor);

		if ($x >= 0) {
			$dst_x = 0;
			$src_x = $x;
		} else {
			$dst_x = -$x;
			$src_x = 0;
		}
		if ($y >= 0) {
			$dst_y = 0;
			$src_y = $y;
		} else {
			$dst_y = -$y;
			$src_y = 0;
		}
		$dst_w = $src_w = (int)(min($width, $this->_width));
		$dst_h = $src_h = (int)(min($height, $this->_height));

		imagecopyresampled($newResource, $this->_resource, $dst_x, $dst_y, $src_x, $src_y,
			$dst_w, $dst_h, $src_w, $src_h);

		$this->_width = $width;
		$this->_height = $height;
		$this->_resource = $newResource;

		return $this;
	}

	/**
	 * Save image into $path and return $this
	 * @param $path
	 * @param int $fileMode
	 * @param int $dirMode
	 * @param int $quality
	 * @return static
	 * @throws Exception\DirectoryNotSavedException
	 * @throws Exception\FileNotSavedException
	 */
	public function save($path, $fileMode = 0644, $dirMode = 0755, $quality = 95)
	{
		$this->checkPath($path, $dirMode);
		if (!imagejpeg($this->_resource, $path, $quality)) {
			throw new Exception\FileNotSavedException("Cant save image to {$path}");
		}

		chmod($path, $fileMode);

		return $this;
	}

	/**
	 * Check file dir existing and create it
	 *
	 * @param $path
	 * @param int $mode
	 * @throws Exception\DirectoryNotSavedException
	 */
	protected function checkPath($path, $mode = 0755)
	{
		$path = dirname($path);
		if (!file_exists($path)) {
			if (!mkdir($path, $mode, true)) {
				throw new Exception\DirectoryNotSavedException($path);
			}
		}
	}
}