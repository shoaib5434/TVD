<?php

function connect($database,$user = 'root',$pass = '') {
	$pdo = new PDO("mysql:dbname=".$database.";host=localhost",$user,$pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    return $pdo;
}

?>