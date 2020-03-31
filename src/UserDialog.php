<?php

namespace elascripts;

class UserDialog
{
    public const _EXPECTED_STRING = 0;
    public const _EXPECTED_NUMERIC = 1;

    /** @var string $answer user answer to the question */
    protected $answer;

    private function __construct()
    {
        //code 
    }

    public static function create(...$params)
    {
        return new static(...$params);
    }

    public function separator(string $prologue = null)
    {
        echo "=================================" . PHP_EOL;
        if ($prologue) {
            echo "$prologue " . PHP_EOL;
        }
        return $this;
    }

    public function line(string $line)
    {
        echo $line . PHP_EOL;
        return $this;
    }

    public function error(string $error)
    {
        echo "ERROR : $error". PHP_EOL;
        return $this;
    }

    public function simpleQuestion(string $question, int $expectedType)
    {
        $this->answer = rtrim(readline("$question : "));

        switch ($expectedType) {
            case self::_EXPECTED_NUMERIC;
                $this->isAnswerANumeric();
                break;
            case self::_EXPECTED_STRING;
                $this->isAnswerAString();
                break;
        }
        return $this;
    }

    protected function isAnswerANumeric()
    {
        return is_numeric($this->answer());
    }

    protected function isAnswerAString()
    {
        return is_string($this->answer());
    }

    public function answer()
    {
        return $this->answer;
    }
}
