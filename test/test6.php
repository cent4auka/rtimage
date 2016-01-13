<?php
/**
 * @author Leonid Gushchin aka Centurion 11.01.2016
 */

require_once __DIR__ . '/../vendor/autoload.php';

$in = __DIR__ . '/in/test1.jpg';
$out = __DIR__ . '/out/test/6.jpg';

(new \ResizeTool\Image($in))
	->resize(400, 400)
	->setBackgroudColor(0xFFFF00)
	->cropCenter(100, 200)
	->cropCenter(200, 200)
	->save($out);