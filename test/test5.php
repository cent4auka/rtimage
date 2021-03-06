<?php
/**
 * @author Leonid Gushchin aka Centurion 11.01.2016
 */

require_once __DIR__ . '/../vendor/autoload.php';

$in = __DIR__ . '/in/test1.jpg';
$out = __DIR__ . '/out/test/5.jpg';

(new \ResizeTool\Image($in))
	->resize(100, 100)
	->cropCenter(300, 300)
	->save($out);