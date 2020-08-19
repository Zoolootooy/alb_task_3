<?php


namespace app\core\parser;


use app\core\Content;
use GuzzleHttp\Client;

interface IParser
{
    public function parse($uri, Client  $client);
}