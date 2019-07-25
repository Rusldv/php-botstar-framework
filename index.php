<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/botstar/config.class.php';
    	require_once $_SERVER['DOCUMENT_ROOT'].'/botstar/route.class.php';
    	require_once $_SERVER['DOCUMENT_ROOT'].'/botstar/datawelcome.class.php';
    	$config = new \botstar\Config();
    	$config->encoding = "UTF-8"; // Данное свойство отправляет header с кодировкой
    	//$config->cors = '*';
    	$config->pdoDriver = "sqlite";
    	//$config->pdoDBFile = "test.data";
    	print $config->dsn;	
