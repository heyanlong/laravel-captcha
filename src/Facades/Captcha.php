<?php namespace Heyanlong\Captcha\Facades;
use Illuminate\Support\Facades\Facade;
/**
 * @see \Heyanlong\Captcha
 */
class Captcha extends Facade {
    /**
     * @return string
     */
    protected static function getFacadeAccessor() { return 'captcha'; }
}