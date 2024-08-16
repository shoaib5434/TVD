<?php
	session_start();
	$cur_time = time();
	$fileName = "VID-{$cur_time}{$_SESSION['email']}";
	echo $fileName;
?>