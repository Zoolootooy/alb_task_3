<?php

namespace app\models;

use app\core\Database;

class Data
{
    private $conn;

    /**
     * Data constructor.
     */
    public function __construct()
    {
        //Connection to DB
        $this->conn = new Database();
    }

    /**
     * Checking the question for existence
     * @param string $question
     * @return int|null
     */
    public function checkQueE($question)
    {
        return $executeQuery = $this->conn->query("SELECT id FROM question WHERE text = ?", [$question])[0]['id'];
    }

    /**
     * Saving the question
     * @param string $question
     * @return bool|int
     */
    public function saveQue($question)
    {
        $id = self::checkQueE($question);

        if ($id == null) {
            $executeQuery = $this->conn->query("INSERT INTO question (text) VALUES (?)", [$question]);
            if ($executeQuery) {
                return $this->conn->lastInsertId();
            }
            return false;
        } else {
            return $id;
        }
    }

    /**
     * Checking the answer for existence
     * @param string $answer
     * @return int|null
     */
    public function checkAnsE($answer)
    {
        return $executeQuery = $this->conn->query("SELECT id FROM answer WHERE text = ?", [$answer])[0]['id'];
    }

    /**
     * Saving the answer
     * @param string $answer
     * @param int $length
     * @return bool|int
     */
    public function saveAns($answer, $length)
    {
        $id = self::checkAnsE($answer);

        if ($id == null) {
            $executeQuery = $this->conn->query("INSERT INTO answer (text, length) VALUES (?,?)", [$answer, $length]);
            if ($executeQuery) {
                return $this->conn->lastInsertId();
            }
            return false;
        } else {
            return $id;
        }
    }

    /**
     * Checking a question for an answer
     * @param int $idQue
     * @param int $idAns
     * @return int|null
     */
    public function checkAnsQueE($idQue, $idAns)
    {
        return $executeQuery = $this->conn->query("SELECT id FROM answerQuestion WHERE idQuestion=? and idAnswer=?",
            [$idQue, $idAns])[0]['id'];
    }

    /**
     * Saving answer on the question
     * @param int $idQue
     * @param int $idAns
     */
    public function saveAnsQue($idQue, $idAns)
    {
        if (self::checkAnsQueE($idQue, $idAns) == null) {
            $this->conn->query("INSERT INTO answerQuestion (idQuestion, idAnswer) VALUES (?,?)", [$idQue, $idAns]);
        }
    }
}