<?php

abstract class GameDataManagement {
    protected array $data;

    abstract protected function __construct(Tool $tool);

    abstract public function getGameSpecificWords();

    protected function openFile($path){
        $json_string = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $path);
        return json_decode($json_string, true);
    }
}