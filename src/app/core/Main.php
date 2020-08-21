<?php


namespace app\core;

use app\core\parser\QuestionPageParser;
use app\core\parser\LetterPageParser;
use app\core\parser\QuestionListParser;
use app\models\DataCache;
use GuzzleHttp\Client;

/**
 * Class Main
 * @package app\core
 */
class Main
{
    protected $client;
    protected $dataCache;
    protected $content;
    protected $childsPid = [];

    /**
     * Main constructor.
     */
    public function __construct()
    {
        $this->client = new Client(OPTION);
        $this->content = new Content();
        $this->dataCache = new DataCache();

        $this->dataCache->flushAll();

        $document = false;
        while ($document == false) {
            $document = $this->content->getContent('uebersicht.html', $this->client);
        }

        $data = $document->find('ul.dnrg li a');
        foreach ($data as $letter) {
            $this->dataCache->cacheLink('lPage', $letter->attr('href'));
        }
    }

    /**
     * Parser's start point
     */
    public function start()
    {
        while (true) {
            foreach ($this->childsPid as $key => $pid) {
                $result = pcntl_waitpid($pid, $status, WNOHANG);
                if ($result == -1 || $result > 0) {
                    unset ($this->childsPid[$key]);
                }
            }

            if (count($this->childsPid) < PROCESS_LIMIT) {
                $linkData = $this->dataCache->getLink();
                if ($linkData == false && count($this->childsPid) == 0) {
                    exit();
                }

                switch ($pid = pcntl_fork()) {
                    case -1:
                        error_log('Failed to create child process');
                        break;

                    case 0:
                        $LetterPageParser = new LetterPageParser();
                        $QuestionListParser = new QuestionListParser();
                        $QuestionPageParser = new QuestionPageParser();
                        $link = explode('~', $linkData);
                        switch ($link[0]) {
                            case 'lPage':
                                if ($LetterPageParser->parse($link[1], $this->client) == false) {
                                    $this->dataCache->cacheLink('lPage', $link[1]);
                                }
                                break;

                            case 'qList':
                                if ($QuestionListParser->parse($link[1], $this->client) == false) {
                                    $this->dataCache->cacheLink('qList', $link[1]);
                                }
                                break;

                            case 'qPage':
                                if ($QuestionPageParser->parse($link[1], $this->client) == false) {
                                    $this->dataCache->cacheLink('qPage', $link[1]);
                                }
                                break;
                        }
                        exit();

                    default:
                        $this->childsPid[] = $pid;
                        break;
                }
            }
        }
    }
}