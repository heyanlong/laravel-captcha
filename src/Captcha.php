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

    protected $offset = -2;

    protected $padding = 2;

    protected $backgroundColor = [
        243,
        251,
        254
    ];

    /**
     * @var FileSystem
     */
    protected $files;

    protected $characters = '2346789abcdefghjmnpqrtuxyzABCDEFGHJMNPQRTUXYZ';

    protected $image;

    /**
     * Generates a new verification code.
     * @return string the generated verification code
     */
    public function generateVerifyCode()
    {
        $characterLength = strlen($this->characters);
        $code = '';
        for ($i = 0; $i < $this->length; ++$i) {
            if (env('APP_ENV') == 'local') {
                $code .= '1';
            } else {
                $code .= $this->characters[mt_rand(0, $characterLength - 1)];
            }

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

        $this->fonts = app('Illuminate\Filesystem\Filesystem')->files(__DIR__ . '/../assets/fonts');
        $this->fonts = array_values($this->fonts);

        if ($verifyCode != null) {
            $this->verifyCode = $verifyCode;
        } else {
            $this->verifyCode = Session::get('captcha' . $config);
            if (empty($this->verifyCode)) {
                $this->verifyCode = $this->generateVerifyCode();
                Session::put('captcha' . $config, $this->verifyCode);
            }
        }

        $this->image = imagecreate($this->width, $this->height);

        // render background color
        imagecolorallocate($this->image, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2]);
        $this->renderNoise();
        $this->renderLine();

        $font = $this->fonts[rand(0, count($this->fonts) - 1)];

        $length = strlen($this->verifyCode);
        $box = imagettfbbox(30, 0, $font, $this->verifyCode);

        $w = $box[4] - $box[0] + $this->offset * ($length - 1);
        $h = $box[1] - $box[5];
        $scale = min(($this->width - $this->padding * 2) / $w, ($this->height - $this->padding * 2) / $h);
        $x = 10;
        $y = round($this->height * 27 / 40);

        for ($i = 0; $i < $length; ++$i) {
            $fontSize = (int)(rand(26, 32) * $scale * 0.8);
            $angle = rand(-20, 20);
            $letter = $this->verifyCode[$i];
            $foreColor = imagecolorallocate($this->image, mt_rand(1, 120), mt_rand(1, 120), mt_rand(1, 120));
            $box = imagettftext($this->image, $fontSize, $angle, $x, $y, $foreColor, $font, $letter);
            $x = $box[2] + $this->offset;
        }

        ob_start();
        imagepng($this->image);
        imagedestroy($this->image);

        return ob_get_clean();

    }

    protected function renderNoise()
    {
        for ($i = 0; $i < 10; $i++) {
            $noiseColor = imagecolorallocate($this->image, mt_rand(100, 225), mt_rand(100, 225), mt_rand(100, 225));
            for ($j = 0; $j < 5; $j++) {
                imagestring($this->image, 5, mt_rand(-10, $this->width), mt_rand(-10, $this->height),
                    chr(rand(48, 122)), $noiseColor);
            }
        }
    }

    protected function renderLine()
    {
        for ($i = 0; $i < 10; $i++) {

            $x1 = rand(1, $this->width - 1);
            $y1 = rand(1, $this->height - 1);
            $x2 = rand(1, $this->width - 1);
            $y2 = rand(1, $this->height - 1);

            $lineColor = imagecolorallocate($this->image, mt_rand(0, 225), mt_rand(0, 225), mt_rand(0, 225));

            imageline($this->image, $x1, $y1, $x2, $y2, $lineColor);
        }
    }

    public function check($value, $config = 'default')
    {
        if (!Session::has('captcha' . $config)) {
            return false;
        }

        $sessionCode = Session::get('captcha' . $config);

        if (strtolower($value) == strtolower($sessionCode)) {
            Session::remove('captcha' . $config);
            return true;
        }

        return false;
    }
}