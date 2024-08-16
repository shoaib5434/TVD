<?php
	session_start();
	if (isset($_POST)) {
		$cur_time = time();
		$extension = pathinfo($_FILES['user-video']['name'],PATHINFO_EXTENSION);
		$fileName = "VID-{$cur_time}{$_SESSION['email']}.{$extension}";
		move_uploaded_file($_FILES['user-video']['tmp_name'], '../videos/'.$fileName);
		echo json_encode(array('filename' => $fileName));
	}
?>