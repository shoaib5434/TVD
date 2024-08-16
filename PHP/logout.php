<?php
	session_start();
	session_destroy();

	setcookie('LOGIN_SESSION','',time() - 2 * 60 * 60);

	echo (json_encode(array('success' => true)));
?>