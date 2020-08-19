<?php

namespace app\models;

class DataCache
{
    private $redis;
    private $config;

    /**
     * DataCache constructor.
     */
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->config = require __DIR__ . '/../config/redis_config.php';
    }

    /**
     * @param $proxy
     */
    public function cacheProxy($proxy)
    {
        $this->redis->connect($this->config['host'], $this->config['port']);
        $this->redis->rPush('proxy', $proxy);
    }

    /**
     * @return mixed
     */
    public function getProxy()
    {
        $this->redis->connect($this->config['host'], $this->config['port']);
        return $this->redis->lPop('proxy');
    }

    /**
     * @param $type
     * @param $link
     */
    public function cacheLink($type, $link)
    {
        $typeLink = $type . '>' . $link;
        $this->redis->connect($this->config['host'], $this->config['port']);
        $this->redis->rPush('link', $typeLink);
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        $this->redis->connect($this->config['host'], $this->config['port']);
        return $this->redis->rPop('link');
    }

    /**
     * @param $name
     */
    public function show($name)
    {
        $this->redis->connect($this->config['host'], $this->config['port']);
        print_r($this->redis->lRange($name, 0, -1));
    }

    /**
     * Flush all
     */
    public function flushAll()
    {
        $this->redis->connect($this->config['host'], $this->config['port']);
        $this->redis->flushAll();
    }


}