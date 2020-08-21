<?php


namespace app\core\parser;

use app\core\Content;
use app\models\DataCache;
use GuzzleHttp\Client;

/**
 * Class QuestionListParser.
 * Works with "question-list"-pages; cache links to question-pages.
 * @package app\core\parser
 */
class QuestionListParser implements IParser
{
    private $content;
    private $dataCache;

    /**
     * QuestionListParser constructor.
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
            $data = $document->find('.Question a');
            $questionsLinks = [];
            foreach ($data as $a) {
                $questionsLinks[] = $a->attr('href');
            }
            $questionsLinks = array_unique($questionsLinks);
            foreach ($questionsLinks as $link) {
                $this->dataCache->cacheLink('qPage', $link);
            }
            return true;
        } else {
            return false;
        }
    }
}