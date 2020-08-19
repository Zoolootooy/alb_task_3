<?php


namespace app\core\parser;

use app\core\Content;
use app\models\Data;
use GuzzleHttp\Client;

class AnswerParser implements IParser
{

    private $content;
    private $model;

    /**
     * AnswerParser constructor.
     */
    public function __construct()
    {
        $this->content = new Content();
        $this->model = new Data();
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
            $question = $document->find('#HeaderString')[0]->text();
            $idQue = $this->model->saveQue($question);
            $answers = $document->find('.Answer a');
            $lengths = $document->find('tbody tr .Length');
            $answersArray = [];
            foreach ($answers as $answer) {
                $answersArray [] = $answer->text();
            }
            $i = 0;
            foreach ($lengths as $length) {
                $idAns = $this->model->saveAns($answersArray[$i], $length->text());
                $this->model->saveAnsQue($idQue, $idAns);
                $i++;
            }
            return true;
        } else {
            return false;
        }
    }
}