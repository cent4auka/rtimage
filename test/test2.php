<?php
/**
 * @author Leonid Gushchin aka Centurion 11.01.2016
 */

require_once __DIR__ . '/../vendor/autoload.php';

$in = __DIR__ . '/in/test2.jpg';
$out = __DIR__ . '/out/test/2.jpg';

(new \ResizeTool\Image($in))
	->crop(400, 0, 300, 300)
	->save($out);