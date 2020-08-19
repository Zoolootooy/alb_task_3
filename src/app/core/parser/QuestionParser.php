<?php


namespace app\core\parser;

use app\core\Content;
use app\models\DataCache;
use GuzzleHttp\Client;

class QuestionParser implements IParser
{
    private $content;
    private $dataCache;

    /**
     * QuestionParser constructor.
     */
    public function __construct()
    {
        $this->content = new Content();
        $this->dataCache = new DataCache();
    }

    public function parse($uri, Client $client)
    {
        $document = $this->content->getContent($uri, $client);
        if ($document != false) {
            $data = $document->find('.Question a');
            $questionsLinks = [];
            foreach ($data as $a) {
                $questionsLinks[] = $a->attr('href');
            }
            $questionsLinks = array_unique($questionsLinks);
            foreach ($questionsLinks as $link) {
                $this->dataCache->cacheLink('a', $link);
            }
            return true;
        } else {
            return false;
        }
    }
}