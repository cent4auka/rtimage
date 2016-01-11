<?php
/**
 * @author Leonid Gushchin aka Centurion 11.01.2016
 */

require_once __DIR__ . '/../vendor/autoload.php';

$in = __DIR__ . '/in/test1.jpg';
$out = __DIR__ . '/out/test3.jpg';

(new \ResizeTool\Image($in))
	->resize(0, 300)
	->crop(200, 0, 300, 300)
	->save($out);