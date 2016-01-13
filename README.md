rtimage
-------

Resize tool image

Installing via Composer <span id="installing-via-composer"></span>
-----------------------

If you do not already have Composer installed, you may do so by following the instructions at
[getcomposer.org](https://getcomposer.org/download/). On Linux you'll run the following commands:

```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

Please refer to the [Composer Documentation](https://getcomposer.org/doc/) if you encounter any
problems or want to learn more about Composer usage.

If you had Composer already installed before, make sure you use an up to date version. You can update Composer
by running `composer self-update`.

With Composer installed, you can install Yii by running the following commands under a Web-accessible folder:

```bash
composer require "cent4auka/rtimage:~1.0"
```

Usage
-----

```php
$image = new \ResizeTool\Image($filePath);
$image->resize(100, 100);
$image->save();

// or
 
$image = new \ResizeTool\Image($originFilePath);
$image->resize(100, 100)
	->save($resultFilePath);

```

Methods
-------

```php
$image = new \ResizeTool\Image($filePath);

// methods of image:
$image->setBackgroudColor(0xFFFFFF)
$image->resize(100, 100);
$image->crop(400, 0, 300, 300)
$image->cropCenter(300, 300)

$image->save();

```
