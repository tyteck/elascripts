<?php

namespace elascripts;

class AskUserIndexToUse extends UserDialog
{
    protected $wrapperObject;

    private function __construct(ElasticWrapper $wrapperObject)
    {
        $this->wrapperObject = $wrapperObject;
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function ask()
    {
        //ask user the index to use
        $this->separator("Available indices :")
            ->numberedList($this->wrapperObject->indices())
            ->simpleQuestion("Select the index to use [1-" . $this->wrapperObject->nbIndices() . "] : ")
            ->checkAnswer(
                UserDialog::_EXPECTED_RANGE,
                ['min' => 1, 'max' => $this->wrapperObject->nbIndices()]
            );

        // getting selected indexs and setting it to be used
        return $this->wrapperObject->indices()[$this->answer() - 1];
    }
}