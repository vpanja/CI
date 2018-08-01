<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class GoogleApi
{
    public function __construct()
    {
        require_once APPPATH.'third_party/google-api/src/Google/autoload.php';
    }
}