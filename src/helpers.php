<?php

if (!function_exists('captcha_src')) {
    /**
     * @param $config
     * @return bool
     */
    function captcha_src($config = 'default')
    {
        return '/captcha/' . $config . '?v=' . time();
    }
}

if (!function_exists('captcha_check')) {
    /**
     * @param $value
     * @param $config
     * @return bool
     */
    function captcha_check($value, $config = 'default')
    {
        return app('captcha')->check($value, $config);
    }
}