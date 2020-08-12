<?php

$url = "https://www.kolesa.ru/news";
//get from https://hidemy.name/ru/proxy-list/?type=hs#list
$proxy = '95.217.120.170:8888';
//$proxyauth = 'user:password';

$res = curl_get($url, $proxy);
$start = 0;
$end = 3;
parser($url, $start, $end);

function curl_get($url, $proxy,  $referer = "https://google.com"){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; ry:38.0) Gecko/20100101 Firefox/38.0");
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function get_content($url){
    $res = curl_get($url, $GLOBALS["proxy"]);
}

function parser ($url, $start, $end){
    if ($start < $end){
        $file = curl_get($url, $GLOBALS["proxy"]);
    }
}






























