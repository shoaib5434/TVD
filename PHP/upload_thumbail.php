<?php
	// print_r($_FILES);
    $unique = uniqid().time();
	$fileName = "{$unique}.png";
	move_uploaded_file($_FILES['image']['tmp_name'], '../images/thumbails/'.$fileName);
	// echo json_encode(array('filename' => $fileName));

    echo json_encode([
    	'success' => true,
    	'file' => $fileName
    ]);

?>