<?php

namespace elascripts;

use Illuminate\Support\Collection;

class Curly
{

    protected $url;
    protected $query;
    protected $queryLength = 0;
    protected $curlHandler;
    protected $data;

    private function __construct(string $url)
    {
        $this->url = $url;
    }

    public static function init(...$params)
    {
        return new static(...$params);
    }

    public function setQuery(string $query)
    {
        $this->query = $query;
        $this->setQueryLength();
        return $this;
    }

    public function query()
    {
        return $this->query;
    }

    protected function setQueryLength()
    {
        $this->queryLength = strlen($this->query);
    }

    public function run()
    {
        $this->curlHandler = curl_init($this->url);
        curl_setopt($this->curlHandler, CURLOPT_TIMEOUT, 5);
        curl_setopt($this->curlHandler, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $this->curlHandler,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json',
                'Content-Length: ' . $this->queryLength
            ]
        );
        curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, $this->query());
        curl_setopt($this->curlHandler, CURLOPT_CUSTOMREQUEST, 'GET');

        $this->data = curl_exec($this->curlHandler);

        curl_close($this->curlHandler);
        return $this;
    }

    public function get()
    {
        return $this->data;
    }
}
