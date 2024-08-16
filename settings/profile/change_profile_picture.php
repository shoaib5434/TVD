<?php

include '../../PHP/database_connection.php';

session_start();

$connect = connect('tvd_user');

$username = $connect -> prepare("SELECT username FROM users where id = {$_SESSION['id']}");
$username -> execute();
$username = $username -> fetch(PDO::FETCH_ASSOC);
$username = $username['username'];

$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

$fileName = $_SESSION['id'].$username.'.'.$ext;
move_uploaded_file($_FILES['image']['tmp_name'], '../../images/DP/'. $fileName);
$connect = connect('tvd_user');

$connect -> prepare("UPDATE users SET dp = '{$fileName}' WHERE id = {$_SESSION['id']}") -> execute();

echo json_encode(array('success' => true,'fileName' => $fileName));

?>