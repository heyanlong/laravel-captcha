<?php namespace Heyanlong\Captcha;

use Session;
use Illuminate\Routing\Controller;

/**
 * Class CaptchaController
 * @package Heyanlong\Captcha
 */
class CaptchaController extends Controller
{
    /**
     * @param Captcha $captcha
     * @param string $config
     * @return string
     */
    public function getCaptcha(Captcha $captcha, $config = 'default')
    {
        if (request('refresh')) {
            Session::put('captcha' . $config, $captcha->generateVerifyCode());
            return response()->json([
                'url' => '/captcha/' . $config . '?v=' . time()
            ]);
        }
        return response($captcha->create($config), 200, ['Content-Type' => 'image/png']);
    }
}