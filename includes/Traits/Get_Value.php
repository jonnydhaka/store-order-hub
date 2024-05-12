<?php

namespace Wppool\Orderhub\Traits;

/**
 * Error handler trait
 */
trait Get_Value
{

    /**
     * Holds the errors
     *
     * @var array
     */
    public $errors = [];

    /**
     * Check if the form has error
     *
     * @param  string  $key
     *
     * @return boolean
     */
    public function has_error($key)
    {
        return isset($this->errors[$key]) ? true : false;
    }

    /**
     * Get the error by key
     *
     * @param  key $key
     *
     * @return string | false
     */
    public function get_error($key)
    {
        if (isset($this->errors[$key])) {
            return $this->errors[$key];
        }

        return false;
    }

    public static function get_domain()
    {
        return  site_url();
        $protocols = array('http://', 'https://', 'http://www.', 'https://www.', 'www.');
        return str_replace($protocols, '', site_url());
    }
    public static function base64UrlEncode($text)
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }
}
