<?php


namespace app\core;

use app\core\parser\AnswerParser;
use app\core\parser\LetterParser;
use app\core\parser\QuestionParser;
use app\models\DataCache;
use GuzzleHttp\Client;

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
            $this->dataCache->cacheLink('l', $letter->attr('href'));
        }
    }

    /**
     * Parser's start point
     */
    public function start()
    {
        while (true) {
            foreach ($this->childsPid as $key => $pid) {
                $res = pcntl_waitpid($pid, $status, WNOHANG);
                if ($res == -1 || $res > 0) {
                    unset ($this->childsPid[$key]);
                }
            }
            if (count($this->childsPid) < LIMIT) {
                $linkData = $this->dataCache->getLink();
                if ($linkData == false && count($this->childsPid) == 0) {
                    exit();
                }
                switch ($pid = pcntl_fork()) {
                    case -1:
                        error_log('Failed to create child process');
                        break;
                    case 0:
                        $letterParser = new LetterParser();
                        $questionParser = new QuestionParser();
                        $answerParser = new AnswerParser();
                        $link = explode('>', $linkData);
                        switch ($link[0]) {
                            case 'l':
                                if ($letterParser->parse($link[1], $this->client) == false) {
                                    $this->dataCache->cacheLink('l', $link[1]);
                                }
                                break;

                            case 'q':
                                if ($questionParser->parse($link[1], $this->client) == false) {
                                    $this->dataCache->cacheLink('q', $link[1]);
                                }
                                break;

                            case 'a':
                                if ($answerParser->parse($link[1], $this->client) == false) {
                                    $this->dataCache->cacheLink('a', $link[1]);
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