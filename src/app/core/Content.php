<?php


namespace app\core;

use DiDom\Document;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use app\models\DataCache;
use function GuzzleHttp\json_decode;

class Content
{
    private $proxy;

    public function __construct()
    {
        $this->proxy = new Proxy();
    }

    /**
     * @param $link
     * @param Client $client
     * @return Document|false
     */
    public function getContent($link, Client $client)
    {
        $proxy = $this->proxy->getProxy();
        try {
            $content = $client->get($link, ['proxy' => $proxy])->getBody()->getContents();
            $this->proxy->pushProxy($proxy);
            error_log("[" . date("j F Y G:i:s") . "]Successfully parse: " . ROOT . $link . "\n", 3,
                __DIR__ . "/../logs/logfile.log");
            return new Document($content);
        } catch (RequestException $e) {
            error_log("[" . date("j F Y G:i:s") . "] Failed to parse: " . ROOT . $link . "\n", 3,
                __DIR__ . "/../logs/logfile.log");
            return false;
        }
    }

    /**
     * @param $link
     * @param Client $client
     * @return false|string\
     */
    public static function getProxyContent($link, Client $client)
    {
        try {
            $content = json_decode($link, true);
            error_log("[" . date("j F Y G:i:s") . "] Proxy list successfully received \n", 3,
                __DIR__ . "/../logs/logfile.log");
            return $content;
        } catch (RequestException $e) {
            error_log("[" . date("j F Y G:i:s") . "] Failed to get proxy list \n", 3, __DIR__ . "/../logs/logfile.log");
            sleep(20);
            return false;
        }
    }
}