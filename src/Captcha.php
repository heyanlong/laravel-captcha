<?php namespace Heyanlong\Captcha;

use Session;
use Illuminate\Filesystem\Filesystem;

/**
 * Laravel 5 Captcha package
 *
 * @copyright Copyright (c) 2015 heyanlong
 * @version 1.x
 * @author Yanlong He
 * @contact yanlong_he@163.com
 * @web http://www.hyl.pw
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class Captcha
{
    protected $length = 4;

    protected $width = 120;

    protected $height = 36;

    protected $fonts = [];

    protected $verifyCode = '';

    /**
     * @var FileSystem
     */
    protected $files;

    protected $characters = '2346789abcdefghjmnpqrtuxyzABCDEFGHJMNPQRTUXYZ';

    /**
     * Generates a new verification code.
     * @return string the generated verification code
     */
    public function generateVerifyCode()
    {
        $characterLength = strlen($this->characters);
        $code = '';
        for ($i = 0; $i < $this->length; ++$i) {
            $code .= $this->characters[mt_rand(0, $characterLength - 1)];
        }
        return $code;
    }

    
    /**
     * Create captcha image
     * @param string $config
     * @param null $verifyCode
     * @return string
     */
    public function create($config = 'default', $verifyCode = null)
    {
        $this->fonts = $this->files->files(__DIR__ . '/../assets/fonts');
        $this->fonts = array_values($this->fonts);

        if ($verifyCode != null) {
            $this->verifyCode = $verifyCode;
        } else {
            $this->verifyCode = $this->generateVerifyCode();
            Session::put('captcha' . $config, $this->verifyCode);
        }

        $image = imagecreate($this->width, $this->height);

        ob_start();
        imagepng($image);
        imagedestroy($image);

        return ob_get_clean();

    }
}

var_dump((new Captcha())->create());