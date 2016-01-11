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
		$_path,
		$_mime,
		$_width,
		$_height;

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
	 * Set background color
	 * @param int $color
	 * @return static
	 */
	public function fillBackgroud($color = 0xFFFFFF)
	{
		imagefill($this->_resource, 0, 0, $color);

		return $this;
	}

	/**
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
	 * Crop image
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
		imagecopyresampled($newResource, $this->_resource, 0, 0, $x, $y,
			$width, $height, $width, $height);

		$this->_width = $width;
		$this->_height = $height;
		$this->_resource = $newResource;

		$this->_resource = $newResource;

		return $this;
	}

	/**
	 * @param $path
	 * @return static
	 * @throws Exception\FileNotSavedException
	 */
	public function save($path)
	{
		$this->checkPath($path);
		if (!imagejpeg($this->_resource, $path, 85)) {
			throw new Exception\FileNotSavedException("Cant save image to {$path}");
		}

		return $this;
	}

	/**
	 * check file dir existing and create it
	 *
	 * @param $path
	 * @throws Exception\DirectoryNotSavedException
	 */
	public function checkPath($path)
	{
		$path = dirname($path);
		if (!file_exists($path)) {
			if (!mkdir($path, 0775)) { // @todo: change file permissions to optional
				throw new Exception\DirectoryNotSavedException($path);
			}
		}
	}
}