<?php

//Cookie class created for transmission data from backend to frontend created for visual experiance
class Cookie
{
    public static function setCookie($name, $value){
        setcookie($name, $value, time() + 3600);
    }

    public static function getCookie($name){
        return $_COOKIE[$name];
    }

}
