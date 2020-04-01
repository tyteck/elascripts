<?php

namespace elascripts;

class UserDialog
{
    public const _EXPECTED_STRING = 0;
    public const _EXPECTED_NUMERIC = 1;
    public const _EXPECTED_RANGE = 2;

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

    public function separator(string $message = null)
    {
        echo "=================================" . PHP_EOL;
        if ($message !== null) {
            echo "$message " . PHP_EOL;
        }
        return $this;
    }

    public function line(string $message)
    {
        echo $message . PHP_EOL;
        return $this;
    }

    public function error(string $message)
    {
        echo "ERROR : $message" . PHP_EOL;
        return $this;
    }

    public function warning(string $message)
    {
        echo "Warning : $message" . PHP_EOL;
        return $this;
    }

    public function simpleQuestion(string $question)
    {
        $this->answer = rtrim(readline("$question : "));
        return $this;
    }

    public function numberedList(array $list)
    {
        $i = 1;
        foreach ($list as $item) {
            $this->line("$i -- $item : ");
            $i++;
        }
        return $this;
    }

    public function unorderedList(array $list)
    {
        foreach ($list as $item) {
            if (is_array($item)) {
                $this->line("-- ".implode(' - ', $item) );
            } else {
                $this->line("-- $item");
            }
        }
        return $this;
    }

    public function checkAnswer(int $checkType, array $attributes = [])
    {
        switch ($checkType) {
            case self::_EXPECTED_NUMERIC;
                $this->isAnswerANumeric();
                break;
            case self::_EXPECTED_STRING;
                $this->isAnswerAString();
                break;
            case self::_EXPECTED_RANGE;
                $this->isAnswerAValidRange($attributes);
                break;
        }
        return $this;
    }

    protected function isAnswerAValidRange(array $attributes)
    {
        if (
            !isset($attributes['min']) ||
            !is_numeric($attributes['min']) ||
            !isset($attributes['max']) ||
            !is_numeric($attributes['max'])
        ) {
            throw new \InvalidArgumentException("A range must be numeric. It should have a 'min' value and a 'max' one.");
        }

        if ($attributes['min'] <= $this->answer() && $this->answer() <= $attributes['max']) {
            return true;
        }

        throw new \InvalidArgumentException("Your answer {" . $this->answer() . "} should have been between a numeric [" . $attributes['min'] . "-" . $attributes['max'] . "]");
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
