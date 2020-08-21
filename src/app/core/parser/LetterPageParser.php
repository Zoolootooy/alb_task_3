<?php


namespace app\core\parser;

use app\core\Content;
use app\models\DataCache;
use GuzzleHttp\Client;

/**
 * Class LetterPageParser.
 * Works with letters-pages, "seite"-pages; caches included links.
 * @package app\core\parser
 */
class LetterPageParser implements IParser
{
    private $content;
    private $dataCache;

    /**
     * LetterPageParser constructor.
     */
    public function __construct()
    {
        $this->content = new Content();
        $this->dataCache = new DataCache();
    }

    /**
     * @param string $uri
     * @param Client $client
     * @return bool
     */
    public function parse($uri, Client $client)
    {
        $document = $this->content->getContent($uri, $client);
        if ($document != false) {
            $data = $document->find('ul.dnrg li a');

            foreach ($data as $a) {
                $this->dataCache->cacheLink('qList', $a->attr('href'));
            }
            return true;
        } else {
            return false;
        }
    }
}