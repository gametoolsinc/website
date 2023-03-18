<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/tool.php");

$tool = new Tool($_GET['id']);
include($_SERVER['DOCUMENT_ROOT'] . $tool->getApplication()->getPhpUrl());
