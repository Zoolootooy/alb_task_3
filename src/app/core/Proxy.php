<?php


namespace app\core;

use app\models\DataCache;
use GuzzleHttp\Client;
use function GuzzleHttp\json_decode;

class Proxy
{
    private $client;
    private $dataCache;

    public function __construct()
    {
        $this->client = new Client(OPTION);
        $this->dataCache = new DataCache();
    }


    public function newProxyList()
    {
        $config = file_get_contents(__DIR__ .'/../config/proxy.json');
        $proxyList = false;
        while ($proxyList == false) {
            $proxyList = Content::getProxyContent($config, $this->client);
        }

        foreach ($proxyList as $proxy) {
            $this->dataCache->cacheProxy($proxy['ip'] . ':' . $proxy['port']);
        }
    }

    /**
     * @param string $ip
     * @param string $port
     * @return bool
     */
    public function checkProxy($ip, $port)
    {
        $errorNumber = '';
        $errorMessage = '';
        $timeout = 10;
        if (@fsockopen($ip, $port, $errorNumber, $errorMessage, $timeout)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getProxy()
    {
        $proxiesExit = $this->dataCache->getProxy();
        if ($proxiesExit == false) {
            self::newProxyList();
            $proxiesExit = $this->dataCache->getProxy();
        }
        $proxy = explode(':', $proxiesExit);
        while (self::checkProxy($proxy['ip'], $proxy['port'])){
            if ($proxiesExit == false){
                self::newProxyList();
                $proxiesExit = $this->dataCache->getProxy();
            }
            $proxy = explode(':', $proxiesExit);
        }
        return implode(':', $proxy);
    }

    /**
     * @param string $proxy
     */
    public function pushProxy($proxy)
    {
        $this->dataCache->cacheProxy($proxy);
    }
}