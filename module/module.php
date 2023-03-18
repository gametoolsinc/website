<?php

abstract class Module {
    abstract function __construct(Webpage $webpage);

    abstract function place();
}