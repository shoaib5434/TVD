<?php

session_start();
include 'database_connection.php';
$connect = connect('tvd_user');
if (isset($_POST)) {

	if ($_POST['action'] == 'verify-user') {
		$isLoggedIn = true;
		$username = '';
		$isUserFollowing = false;
		if (!isset($_SESSION['id'])) $isLoggedIn = false;
		else {
			$query = "
				SELECT username FROM users WHERE id = {$_SESSION['id']}
			";
			$result = $connect -> prepare($query);
			$result -> execute();
			$result = $result -> fetch(PDO::FETCH_ASSOC);
			if (isset($_POST['username']) && $_POST['username'] != $result['username']) {
				$isLoggedIn = false;
			}
			else if (!isset($_POST['username'])) $username = $result['username']; 
			$tmp = false;
			if (isset($_POST['username'])) {
				$tmp = $connect -> prepare("SELECT followed_to from followings where followed_to = (SELECT id from users where username = '{$_POST['username']}') and followed_by = {$_SESSION['id']}");
				$tmp -> execute();
				$tmp = $tmp -> rowCount() > 0;
			}
			if ($isLoggedIn || $tmp) $isUserFollowing = true;
		}
		echo json_encode([
			'info' => [
				'isloggedIn' => $isLoggedIn,
				'isUserFollowing' => $isUserFollowing
			],
			'username' => $username,
		]);
	}

	if ($_POST['action'] == 'fetch-followers-and-followings') {
		try {
			$result = $connect -> prepare("SELECT users.id,users.dp,users.username from followings join users on followed_to = users.id where followed_by = (SELECT id from users where username = '{$_POST['username']}') group by id");
			$result  -> execute();
			$followings = $result -> fetchAll(PDO::FETCH_ASSOC);


			$result = $connect -> prepare("SELECT users.id,users.dp,users.username from followings join users on followed_by = users.id where followed_to = (SELECT id from users where username = '{$_POST['username']}') group by id");
			$result -> execute();
			$followers = $result -> fetchAll(PDO::FETCH_ASSOC);

			echo json_encode([
				'success' => true,
				'followers' => $followers,
				'followings' => $followings
			]);
		}
		catch (PDOException $e) {
			echo json_encode([
				'success' => false,
				'message' => $e -> getMessage()
			]);
		}
	}

	if ($_POST['action'] == 'fetch-liked-videos') {
		$videos = $connect -> prepare("SELECT thumbail,post_id from tvd_content.posts where post_id in (SELECT post_id from tvd_content.liked_videos where user_id = {$_SESSION['id']})");
		$videos -> execute();
		$videos = $videos -> fetchAll(PDO::FETCH_ASSOC);
		echo json_encode([
			'success' => false,
			'videos' => $videos
		]);
	}

	if ($_POST['action'] == 'get-video-info') {
		$query = "
			SELECT * from posts WHERE post_id = {$_POST['video_id']}
		";
		$connect_content = connect('tvd_content');
		$result = $connect_content -> prepare($query);
		$result -> execute();
		$result = $result -> fetch(PDO::FETCH_ASSOC);
		// echo "Hello";
		echo json_encode([
			'success' => true,
			'data' => $result
		]);
	}

	if ($_POST['action'] == 'update-post') {
		$query = "DELETE FROM video_mentions WHERE video_id = {$_POST['video_id']}";
		$connect_content = connect('tvd_content');
		$connect_content -> prepare($query) -> execute();
		$query = "DELETE FROM hashtag_videos WHERE video_id = {$_POST['video_id']}";
		$connect_content -> prepare($query) -> execute();
		$query = "UPDATE posts SET caption = '{$_POST['caption']}' WHERE post_id = {$_POST['video_id']}";
		$connect_content -> prepare($query) -> execute();
		for ($i = 1; $i < count($_POST['mentioned_users']); ++$i) {
			$username = substr($_POST['mentioned_users'][$i],1,strlen($_POST['mentioned_users'][$i]) - 1);
			$result = $connect -> prepare("SELECT id from users where username = '{$username}'");
			$result -> execute();
			if ($result -> rowCount() > 0) {
				$result = $result -> fetch(PDO::FETCH_ASSOC);
				$connect_content -> prepare("INSERT INTO video_mentions(video_id,user_id) VALUES({$_POST['video_id']},{$result['id']})") -> execute();
			}
		}
		for ($i = 1; $i < count($_POST['hashtags']); ++$i) {
			$username = substr($_POST['hashtags'][$i],1,strlen($_POST['hashtags'][$i]) - 1);
			$result = $connect_content -> prepare("SELECT id from hashtag where name = '{$username}'");
			$result -> execute();
			if ($result -> rowCount() > 0) {
				$result = $result -> fetch(PDO::FETCH_ASSOC);
				$connect_content -> prepare("INSERT INTO hashtag_videos(video_id,hashtag_id) VALUES({$_POST['video_id']},{$result['id']})") -> execute();
			}
			else {
				$hashtag = substr($_POST['hashtags'][$i],1,strlen($_POST['hashtags'][$i]) - 1);
				$connect_content -> prepare("INSERT INTO hashtag(name,started_on,last_uploaded) VALUES ('{$hashtag}',date('d/m/y'),date('d/m/y'))") -> execute();
				$result = $connect_content -> prepare("SELECT id from hashtag where name = '{$hashtag}'");
				$result -> execute();
				if ($result -> rowCount() > 0) {
					$result = $result -> fetch(PDO::FETCH_ASSOC);
					$connect_content -> prepare("INSERT INTO hashtag_videos(video_id,hashtag_id) VALUES({$_POST['video_id']},{$result['id']})") -> execute();
				}
			}
		}
		$connect_content -> prepare($query) -> execute();
		echo json_encode(['success' => true]);
	}

	// Save User Info

	if ($_POST['action'] == 'save-user-info') {
		require('./verify_mail.php');

		try {
			$session = '';
			for ($i = 0; $i < 4; ++$i) {
				$session .= chr(rand(65,122));
			}
			$result = $connect -> prepare("SELECT email FROM users WHERE email = :mail");
			$result -> bindParam("mail",$_POST['user-email']);
			$result -> execute();

			if ($result -> rowCount() > 0) {
				echo json_encode(array(
					'success' => true,
					'duplicateMail' => true,
					'message' => 'Duplicate Email'
				));
			}
			else {
				$code = 12333;
				$sth = $connect -> prepare("
					INSERT INTO users (email,password) VALUES (:email,:password)
				");

				$sth -> bindParam('email',$_POST['user-email']);
				$sth -> bindParam('password',$_POST['user-password']);
				$sth -> execute();

				$sth = $connect -> prepare("
					INSERT INTO logins(session_id,gmail,code) VALUES ('{$session}',:email,{$code})
				");
				$sth -> bindParam('email',$_POST['user-email']);

				$sth -> execute();
				setcookie("LOGIN_SESSION",$session,time() + 15 * 24 * 60 * 60);
				echo json_encode(array(
					'success' => true,
					'duplicateMail' => false,
					'message' => 'Email Verification Has Benn Sent!'
				));
			}
		}
		catch (PDOException $err) {
			echo json_encode(array(
				'success' => false,
				'message' => $err->getMessage()
			));
		}
	}

	if ($_POST['action'] == 'follow-unfollow') {
		$result = [
			'success' => false,
			'isLoggedIn' => false,
			'message' => 'You\'re Not Logged In'
		];
		if (isset($_SESSION['email'])) {
			$result['isLoggedIn'] = true;
			if ($_POST['current'] == 'followed' && $_SESSION['id'] != $_POST['user_id']) {
				$connect -> prepare("DELETE from followings WHERE followed_to = {$_POST['user_id']} and followed_by = {$_SESSION['id']}") -> execute();
				$result['success'] = true;
				$result['message'] = 'Unfollowed';
			}
			else if ($_POST['current'] == 'unfollowed' && $_SESSION['id'] != $_POST['user_id']) {
				$connect -> prepare("INSERT INTO followings (followed_to,followed_by) VALUES ({$_POST['user_id']},{$_SESSION['id']})") -> execute();
				$connect -> prepare("INSERT INTO tvd_content.notification (_to,user_id,cat_index,time) VALUES ({$_POST['user_id']},{$_SESSION['id']},0,date('d/m/y'))") -> execute();
				$result['success'] = true;
				$result['message'] = 'Followed';
			}
		}
		echo json_encode($result);
	}

	if ($_POST['action'] == 'fetch-notifications') {
		if (isset($_SESSION['id'])) {
			$allNotification = [];
			$debug = "";
			$query = "SELECT tvd_content.notification.cat_index,tvd_content.notification.read,tvd_content.notification.id as noti_id,tvd_content.notification.post_id,tvd_user.users.id,tvd_user.users.dp,tvd_user.users.username FROM tvd_content.notification JOIN tvd_user.users ON tvd_content.notification.user_id = tvd_user.users.id WHERE ";
			if (isset($_SESSION['id'])) $query .= " _to = {$_SESSION['id']}";
			$query .= " GROUP BY tvd_content.notification.id ORDER BY tvd_content.notification.id DESC LIMIT 10";
			$debug .= $query . "\n";
			$result = $connect -> prepare($query);
			$result -> execute();
			$result = $result -> fetchAll(PDO::FETCH_ASSOC);
			$query .= count($result) ."\n";
			$countRead = 0;
			for ($i = 0; $i < count($result); ++$i) {
				$notification = [];
				$isread = ($result[$i]['read'] == 1) ? "" : " unread";
				$countRead += ($result[$i]['read'] == 0);
				if ($result[$i]['cat_index'] == 0) {
					$notification['data'] = "

						<div class=\"single-notification {$isread}\" data-address='/profile?user={$result[$i]['username']}'>
							<img class=\"icon\" src=\"/TVD (FYP)/images/DP/{$result[$i]['dp']}\">
							<p><b>{$result[$i]['username']}</b> has started following you.</p>
						</div>
					";
				}
				else if ($result[$i]['cat_index'] == 1) {
					$sub_res = $connect -> prepare("SELECT thumbail,post_id from tvd_content.posts where post_id = {$result[$i]['post_id']}");
					$sub_res -> execute();
					if ($sub_res -> rowCount() > 0) {
						$sub_res = $sub_res -> fetch(PDO::FETCH_ASSOC);
						$notification['data'] = "
							<div class=\"single-notification {$isread}\" data-address='?video_id={$result[$i]['post_id']}'>
								<img class=\"icon\" src=\"/TVD (FYP)/images/DP/{$result[$i]['dp']}\">
								<p class='thumbail-noti'><b>{$result[$i]['username']}</b> liked your video.</p>
								<div class='thumbail'> <img src='/TVD (FYP)/images/thumbails/{$sub_res['thumbail']}'> </div>
							</div>
						";
					}
				}
				$notification['id'] = $result[$i]['noti_id'];
				array_push($allNotification,$notification);
			}
			echo json_encode([
				'success' => true,
				'notifications' => $allNotification,
				'debug' => $debug,
				'unread' => $countRead,'session' => true
			]);
		}
		else {
			echo json_encode([
				'success' => true,
				'session' => false
			]);
		}
	}

	if ($_POST['action'] == 'mark-all-notifications-as-read') {
		$connect -> prepare("UPDATE tvd_content.notification set notification.read = 1 where _to = {$_SESSION['id']}")-> execute();
		echo json_encode(['success' =>true]);
	}

	// Resend Mail

	if ($_POST['action'] == 'resend-code') {
		require('./verify_mail.php');
		$mail;
		if (isset($_POST['mail'])) $mail = $_POST['mail'];
		else $mail = $_SESSION['email'];
		$code = send_email($mail);
		try {
			$connect -> prepare("
				UPDATE logins SET code = {$code} WHERE gmail = '{$mail}' AND session_id = '{$_COOKIE['LOGIN_SESSION']}'
			") -> execute();

			echo json_encode(array(
				'success' => true,
				'message' => 'Email Verification Has Benn Sent!'
			));
		}
		catch (PDOException $err) {
			echo json_encode(array(
				'success' => false,
				'message' => $err->getMessage()
			));
		}
	}

	// Verify Code

	if ($_POST['action'] == 'verify-code') {
		try {
			$mail;
			if (isset($_POST['mail'])) $mail = $_POST['mail'];
			else $mail = $_SESSION['email'];
			$result = $connect -> prepare("
				SELECT session_id FROM logins WHERE gmail = '{$mail}' AND code = {$_POST['code']} AND verified = 0
			");
			$result -> execute();
			$ret = array(
				'success' => true,
				'verified' => true
			);
			if ($result->rowCount() > 0) {
				$ret['verified'] = true;
				$connect -> prepare("UPDATE logins SET verified = true WHERE code = {$_POST['code']} AND gmail = '{$mail}'") -> execute();
				$connect -> prepare("UPDATE users SET verified = true WHERE email ='{$mail}'") -> execute();

				if (!isset($_SESSION['email'])) $_SESSION['email'] = $mail;
			}
			else $ret['verified'] = false;
			echo json_encode($ret);
		}
		catch (PDOException $err) {
			echo json_encode(array(
				'success' => false,
				'message' => $err->getMessage()
			));
		}
	}

	if ($_POST['action'] == 'add-username') {
		$ret = array(
			'success' => true,
			'uniqueUsername' => true,
			'message' => 'None'
		);
		try {
			$result = $connect -> prepare("SELECT username FROM users WHERE username = :username");

			$result -> bindParam("username",$_POST['username']);
			$result -> execute();
			if ($result -> rowCount() > 0) {
				$ret['uniqueUsername'] = false;
			}
			else {
				$new = $connect -> prepare("UPDATE users SET username = :username , name = :name WHERE email = :email");
				$new -> bindParam("username",$_POST['username']);
				$new -> bindParam("name",$_POST['name']);
				$new -> bindParam("email",$_POST['mail']);
				$new -> execute();
			}
		}
		catch (PDOException $err) {
			$ret['success'] = false;
			$ret['uniqueUsername'] = false;
			$ret['message'] = $err->getMessage();
		}
		echo json_encode($ret);
	}

	if ($_POST['action'] == 'check-for-session') {
		$ret = [
			'session' => false,
			'verified' => false,
			'message' => 'None'
		];
		$flag = false;
		if (isset($_SESSION['email'])) {
			$ret['session'] = true;
			$session_id = $_COOKIE['LOGIN_SESSION'];
			$dp = $connect -> prepare("SELECT id,dp,name,username from users where email = '{$_SESSION['email']}'");
			$dp -> execute();
			$dp = $dp -> fetch(PDO::FETCH_ASSOC);
			$name = $dp['name'];
			$username = $dp['username'];
			$dp = $dp['dp'];
			$flag = true;
		}
		else if (isset($_COOKIE['LOGIN_SESSION'])) {
			$flag = true;
			$ret['session'] = true;
			$session_id = $_COOKIE['LOGIN_SESSION'];
			$mail = $connect -> prepare("SELECT gmail FROM logins WHERE session_id = '{$_COOKIE['LOGIN_SESSION']}'");
			$mail -> execute();
			$mail = $mail -> fetch(PDO::FETCH_ASSOC);
			$mail = $mail['gmail'];
			$id = $connect -> prepare("SELECT id,dp from users where email = '{$mail}'");
			$id -> execute();
			$id = $id -> fetch(PDO::FETCH_ASSOC);
			$dp = $id['dp'];
			$id = $id['id'];
			$_SESSION['email'] = $mail;
			$_SESSION['id'] = $id;
		}
		else {
			$ret['session'] = false;
			$ret['verified'] = false;
		}
		if ($flag) {
			$mail = $connect -> prepare("SELECT verified FROM logins WHERE session_id = '{$session_id}'");
			$mail -> execute();
			$mail = $mail -> fetch(PDO::FETCH_ASSOC);

			$ret['verified'] = $mail['verified'];
			$ret['message'] = $_SESSION['email'];
			$ret['id'] = $_SESSION['id'];
			$ret['user_dp'] = $dp;
			$ret['user_name'] = $name;
			$ret['user_username'] = $username;
		}

		echo json_encode($ret);
	}
	if ($_POST['action'] == 'user-login') {

		$ret = [
			'error' => false,
			'session' => false,
			'verified' => false,
			'email' => false,
			'password' => false,
			'message' => 'None'
		];

		try {
			$result = $connect -> prepare("SELECT username,id FROM users WHERE email = :email AND password = :password");
			$result -> bindParam("email",$_POST['mail']);
			$result -> bindParam("password",$_POST['password']);
			$result -> execute();

			if ($result -> rowCount() > 0) {
				// require('./verify_mail.php');
				$code = 12345;
				$session = '';
				$result = $result -> fetch(PDO::FETCH_ASSOC);
				$id = $result['id'];
				for ($i = 0; $i < 4; ++$i) {
					$session .= chr(rand(65,122));
				}
				$result = $connect -> prepare("INSERT INTO logins(session_id,code,gmail) VALUES('{$session}',$code,:email)");
				$result -> bindParam("email",$_POST['mail']);
				$result -> execute();
				setcookie('LOGIN_SESSION',$session,time() + 15 * 24 * 60 * 60);
				$_SESSION['email'] = $_POST['mail'];
				$_SESSION['id'] = $id;
				$ret['session'] = true;
			}
			else {
				$result = $connect -> prepare("SELECT username FROM users WHERE email = :email");
				$result -> bindParam("email",$_POST['mail']);

				$result -> execute();

				if ($result -> rowCount() > 0) {
					$ret['email'] = true;
				}
				else {
					$ret['password'] = true;
				}
			}
		}
		catch (PDOException $err) {
			$ret['error'] = true;
			$ret['message'] = $err -> getMessage();
		}
		echo json_encode($ret);
	}

	if ($_POST['action'] == 'post-upload') {
		$ret = [];
		$connect_content = connect('tvd_content');
		try {
			$user_id;
			$result = $connect -> prepare("SELECT id FROM users WHERE email = '{$_SESSION['email']}'");
			$result -> execute();
			$result = $result -> fetch(PDO::FETCH_ASSOC);
			$user_id = $result['id'];
			// print_r($_POST['users_mentioned']);
			$result = $connect_content -> prepare("INSERT INTO posts(user_id,comments,caption,mode,videofile,upload_date,thumbail) VALUES
				({$user_id},{$_POST['comments']},'{$_POST['caption']}','{$_POST['mode']}','{$_POST['videofile']}',date('d/m/y'),'{$_POST['thumbail']}')
			");
			for ($i = 0; $i < count($_POST['videos']); ++$i) {
				unlink("../videos/{$_POST['videos'][$i]}");
			}
			$result -> execute();
			for ($i = 1; $i < count($_POST['users_mentioned']); ++$i) {
				$username = substr($_POST['users_mentioned'][$i],1,strlen($_POST['users_mentioned'][$i]) - 1);
				$_mentioned_user = $connect -> prepare("SELECT id from users where username = '{$username}'");
				$_mentioned_user -> execute();
				if ($_mentioned_user -> rowCount() > 0) {
					$_mentioned_user = $_mentioned_user -> fetch(PDO::FETCH_ASSOC);
					// print_r($_mentioned_user);
					$_mentioned_user = $_mentioned_user['id'];
					$connect_content -> prepare("INSERT INTO video_mentions VALUES((SELECT post_id from posts WHERE user_id = {$user_id} AND videofile = '{$_POST['videofile']}'), {$_mentioned_user})") -> execute();
				}
			}
			for ($i = 1; $i < count($_POST['hashtags']); ++$i) {
				$hashtag = substr($_POST['hashtags'][$i],1,strlen($_POST['hashtags'][$i]) - 1);
				$hashtag = $connect_content -> prepare("SELECT id from hashtag where name = '{$hashtag}'");
				$hashtag -> execute();
				if ($hashtag -> rowCount() > 0) {
					$hashtag = $hashtag -> fetch(PDO::FETCH_ASSOC);
					$hashtag = $hashtag['id'];
					$connect_content -> prepare("INSERT INTO hashtag_videos(video_id,hashtag_id) VALUES((SELECT post_id from posts WHERE user_id = {$user_id} AND videofile = '{$_POST['videofile']}'), {$hashtag})") -> execute();
				}
				else {
					$hashtag = substr($_POST['hashtags'][$i],1,strlen($_POST['hashtags'][$i]) - 1);
					$connect_content -> prepare("INSERT INTO hashtag(name,started_on,last_uploaded) VALUES ('{$hashtag}',date('d/m/y'),date('d/m/y'))") -> execute();
					$hashtag = substr($_POST['hashtags'][$i],1,strlen($_POST['hashtags'][$i]) - 1);
					$hashtag = $connect_content -> prepare("SELECT id from hashtag where name = '{$hashtag}'");
					$hashtag -> execute();
					$hashtag = $hashtag -> fetch(PDO::FETCH_ASSOC);
					$hashtag = $hashtag['id'];
					$connect_content -> prepare("INSERT INTO hashtag_videos(video_id,hashtag_id) VALUES((SELECT post_id from posts WHERE user_id = {$user_id} AND videofile = '{$_POST['videofile']}'), {$hashtag})") -> execute();
				}
			}
			$ret['success'] = true;
		}
		catch (PDOException $err) {
			$ret['message'] = $err -> getMessage();
			$ret['success'] = false;
		}
		echo json_encode($ret);
		$connect_content = null;
	}

	if ($_POST['action'] == 'fetch-video-batch') {
		$video_batch = [];
		$lastIndex = 0;
		$connect_content = connect('tvd_content');
		$debug = "";
		try {
			if (isset($_POST['video_id'])) {
				$result = $connect_content -> prepare("SELECT * from posts WHERE post_id = {$_POST['video_id']}");
				$result -> execute();
				$result = $result -> fetchAll(PDO::FETCH_ASSOC);
				// print_r($result);
				returnVideos($video_batch,$connect,$result,0,$connect_content);
			}
			$query = "SELECT * FROM posts";
			if (isset($_POST['user_id'])) {
				$query .= " WHERE user_id = {$_POST['user_id']}";
				if (isset($_POST['video_id'])) $query .= " AND post_id != {$_POST['video_id']}";
			}
			else if (isset($_POST['hashtag'])) {
				$query .= " WHERE post_id IN (SELECT video_id FROM hashtag_videos WHERE hashtag_id = {$_POST['hashtag']})";
				if (isset($_POST['video_id'])) $query .= " AND post_id != {$_POST['video_id']}";
			}
			else if (isset($_POST['video_id'])) {
				$query .= " WHERE post_id != {$_POST['video_id']}";
			}
			$query .= " LIMIT";
			if (isset($_POST['start'])) {
				$query .= " {$_POST['start']},";
				$lastIndex = $_POST['start'];
			}
			if (isset($_POST['video_id'])) $query .= " 4";
			else $query .= " 5";
			$result = $connect_content -> prepare($query);
			$result -> execute();
			$debug .= $query;
			$result = $result -> fetchAll(PDO::FETCH_ASSOC);

			$i = 0;
			$limit = (isset($_POST['video_id']) ? 4 : 5);
			$debug .= " - {$limit} - " . count($result);
			$flag = true;
			while (count($result) < $limit) {
				$flag = false;
				$query = "SELECT * FROM posts";
				if (isset($_POST['user_id'])) {
					$query .= " WHERE user_id = {$_POST['user_id']}";				}
				else if (isset($_POST['hashtag'])) {
					$query .= " WHERE post_id IN (SELECT video_id FROM hashtag_videos WHERE hashtag_id = {$_POST['hashtag']})";
				}
				$lastIndex = 0;
				$query .= " LIMIT {$limit}";
				$next_res = $connect_content -> prepare($query);
				$next_res -> execute();
				$next_res = $next_res -> fetchAll(PDO::FETCH_ASSOC);
				$i = 0;
				while (count($next_res) > $i && count($result) < $limit) {
					array_push($result,$next_res[$i]);
					$i++;
				}
				$lastIndex = $i - 1;
			}
			if ($flag) $lastIndex += 4;
			$i = 0;
			while ($i < $limit) {
				returnVideos($video_batch,$connect,$result,$i,$connect_content);
				++$i;
			}

			echo json_encode(array(
				'success' => true,
				'data' => $video_batch,
				'lastIndex' => $lastIndex
			));
		}
		catch (PDOException $err) {
			echo json_encode(array(
				'success' => false,
				'data' => $err -> getMessage(),
				'debug' => $debug
			));
		}
	}

	if ($_POST['action'] == 'like-video') {
		$ret = [];
		try {
			$_POST['post_id'] = intval($_POST['post_id']);
			// var_dump($_POST);
			$connect_content = connect('tvd_content');
			$connect_content -> prepare("INSERT INTO liked_videos(user_id,post_id) VALUES({$_SESSION['id']},{$_POST['post_id']})") -> execute();
			$query = "INSERT INTO notification(user_id,_to,cat_index,time,post_id) VALUES({$_SESSION['id']},(SELECT user_id from posts where post_id = {$_POST['post_id']}),1,date('d/m/y'),{$_POST['post_id']})";
			$connect_content -> prepare($query) -> execute();
			$likes = $connect_content -> prepare("SELECT likes FROM posts WHERE post_id = {$_POST['post_id']}");
			$likes -> execute();
			$likes = $likes -> fetch()[0];
			$connect_content -> prepare("UPDATE posts SET likes = {$likes} + 1 where post_id = {$_POST['post_id']}") -> execute();
			$ret['success'] = true;
		}
		catch(PDOException $err) {
			$ret['success'] = false;
			$ret['message'] = $err -> getMessage();
		}
		echo json_encode($ret);
	}
	if ($_POST['action'] == 'getUserInfo') {
		$info = array(
			'dp' => false,
			'session' => false
		);
		if (isset($_SESSION['email'])) {
			$data = $connect -> prepare("SELECT dp,username,name,description from users WHERE id = {$_SESSION['id']}");
			$data -> execute();
			$data = $data -> fetch(PDO::FETCH_ASSOC);
			$info['name'] = $data['name'];
			$info['username'] = $data['username'];
			$info['description'] = $data['description'];
			$info['dp'] = $data['dp'];
			$info['session'] = true;
		}
		echo json_encode($info);
	}
	if ($_POST['action'] == 'update-name') {
		$info = array(
			'session' => false
		);
		if (isset($_SESSION['email'])) {
			$query = "UPDATE users SET {$_POST['column-name']} = '{$_POST['column-value']}' WHERE id = {$_SESSION['id']}";
			$data = $connect -> prepare($query);
			$data -> execute();
			$info['session'] = true;
		}
		echo json_encode($info);
	}
	if ($_POST['action'] == 'unlike-video') {
		$ret = [];
		try {
			$_POST['post_id'] = intval($_POST['post_id']);
			// var_dump($_POST);
			$connect_content = connect('tvd_content');
			$connect_content -> prepare("DELETE FROM liked_videos WHERE post_id = {$_POST['post_id']} AND user_id = {$_SESSION['id']}") -> execute();
			$likes = $connect_content -> prepare("SELECT likes FROM posts WHERE post_id = {$_POST['post_id']}");
			$likes -> execute();
			$likes = $likes -> fetch()[0];
			$connect_content -> prepare("UPDATE posts SET likes = $likes - 1 where post_id = {$_POST['post_id']}") -> execute();
			$ret['success'] = true;
		}
		catch(PDOException $err) {
			$ret['success'] = false;
			$ret['message'] = $err -> getMessage();
		}
		echo json_encode($ret);
	}
	if ($_POST['action'] == 'add-comment') {
		try {
			$connect_content = connect('tvd_content');
			// echo $_POST['videoid'];
			$connect_content -> prepare("INSERT into comments(post_id,user_id,comment_text,time_added) values ({$_POST['videoid']},{$_SESSION['id']},'{$_POST["commenttext"]}',date('d/m/y'))") -> execute();
			echo json_encode([
				'success' => true,
				'message' => 'Added'
			]);
		}
		catch(PDOException $e) {
			echo json_encode([
				'success' => true,
				'message' => $e -> getMessage()
			]);
		}
	}
	if ($_POST['action'] == 'get-comments') {
		try {
			$connect_content = connect('tvd_content');
			// echo $_POST['videoid'];
			$data = $connect_content -> prepare("SELECT user_id,comment_text,time_added from comments where comments.post_id = {$_POST['post_id']}");
			$data -> execute();
			$data = $data -> fetchAll(PDO::FETCH_ASSOC);
			for ($i = 0; $i < count($data); ++$i) {
				$userdata = $connect -> prepare("SELECT dp,name from users where id = {$data[$i]['user_id']}");
				$userdata -> execute();
				$userdata = $userdata -> fetch(PDO::FETCH_ASSOC);
				$data[$i]['dp'] = $userdata['dp'];
				$data[$i]['name'] = $userdata['name'];
			}
			echo json_encode([
				'success' => true,
				'message' => 'Added',
				'data' => $data
			]);
		}
		catch(PDOException $e) {
			echo json_encode([
				'success' => true,
				'message' => $e -> getMessage()
			]);
		}
	}

	if ($_POST['action'] == 'get-hashtag-videos') {
		try {
			$connect_content = connect('tvd_content');
			$hashtag = '';
			if ($_POST['hashtag'] == 'default') {
				$hashtag = $connect_content -> prepare("SELECT name,MAX(videos_count) FROM `hashtag` LIMIT 1;");
				$hashtag -> execute();
				$hashtag = $hashtag -> fetch(PDO::FETCH_ASSOC);
				$hashtag = $hashtag['name'];
			}
			$hashtag = $_POST['hashtag'];
			$hashtag_name = $hashtag;
			$hashtag_id = $connect_content -> prepare("SELECT id FROM hashtag WHERE name = '{$hashtag}'");
			$hashtag_id -> execute();
			// print_r($hashtag_id);
			$hashtag_id = $hashtag_id -> fetch(PDO::FETCH_NUM);
			$hashtag_id = $hashtag_id[0];
			$query = $connect_content -> prepare("SELECT thumbail,post_id FROM posts JOIN hashtag_videos on posts.post_id = hashtag_videos.video_id WHERE hashtag_id = {$hashtag_id} LIMIT {$_POST['start']}, {$_POST['limit']}");
			$query -> execute();
			$videos = $query -> fetchAll(PDO::FETCH_ASSOC);

			$connect_content = $connect = null;

			echo json_encode(
				[
					'hashtag' => '#' . $hashtag_name,
					'id' => $hashtag_id,
					'data' => $videos,
					'success' => true
				]
			);
		}
		catch (PDOException $e) {
			echo json_encode(
				[
					'success' => true,
					'message' => $e -> getMessage()
				]
			);
		}
	}
	if ($_POST['action'] == 'get-top-accounts') {
		$query = "select count(*),u.id,u.username,u.dp,u.name from users u join followings f on f.followed_to = u.id GROUP by f.followed_to ORDER by COUNT(*) DESC";
		$result = $connect -> prepare($query);
		$result -> execute();
		$result = $result -> fetchAll(PDO::FETCH_ASSOC);
		echo json_encode(array(
			'success' => true,
			'data' => $result
		));
	}

	$connect = null;
}


function returnVideos(&$video_batch,$connect,$result,$i,$connect_content) {
	$userInfo = $connect -> prepare("SELECT * FROM users WHERE id = {$result[$i]['user_id']}");
	$userInfo -> execute();
	$userInfo = $userInfo -> fetch(PDO::FETCH_ASSOC);
	$isFollowed = false;
	$isLiked = false;
	if (isset($_SESSION['email'])) {
		$isLiked = $connect_content -> prepare("SELECT post_id FROM liked_videos WHERE post_id = {$result[$i]['post_id']} and user_id = {$_SESSION['id']}");
		$isLiked -> execute();
		if ($isLiked -> rowCount() > 0) $isLiked = true;
		else $isLiked = false;
	}
	if (isset($_SESSION['id'])) {
		$isFollowed = $connect -> prepare("SELECT followed_by FROM followings WHERE followed_to = {$userInfo['id']} and followed_by = {$_SESSION['id']}");
		$isFollowed -> execute();
		if ($isFollowed -> rowCount() > 0) $isFollowed = true;
		else $isFollowed = false;
	}
	$_mentioned_user = $connect_content -> prepare("SELECT user_id from video_mentions where video_id = {$result[$i]['post_id']} LIMIT 5");
	$_mentioned_user -> execute();
	$_users = [];
	$_mentioned_user = $_mentioned_user -> fetchAll(PDO::FETCH_ASSOC);
	// print_r($_mentioned_user);
	$comment_count = $connect_content -> prepare("SELECT count(*) from comments where post_id = {$result[$i]['post_id']}");
	$comment_count -> execute();
	$comment_count = $comment_count -> fetch(PDO::FETCH_NUM)[0];
	for ($k = 0; $k < count($_mentioned_user); ++$k) {
		// echo "Hello";
		$user = $connect -> prepare("SELECT id,name,dp,username from users where id = {$_mentioned_user[$k]['user_id']}");
		$user -> execute();
		$user = $user -> fetch(PDO::FETCH_ASSOC);
		array_push($_users,$user);
	}
	$obj = array(
		'url' => $result[$i]['videofile'],
		'post_id' => $result[$i]['post_id'],
		'caption' => $result[$i]['caption'],
		'coments_allowed' => $result[$i]['comments'],
		'date' => $result[$i]['upload_date'],
		'user_name' => $userInfo['username'],
		'name' => $userInfo['name'],
		'email' => $userInfo['email'],
		'likes' => $result[$i]['likes'],
		'isLikedByUser' => $isLiked,
		'isFollowedByUser' => $isFollowed,
		'user_id' => $userInfo['id'],
		'dp' => $userInfo['dp'],
		'mentioned_users' => $_users,
		'total_comments' => $comment_count
	);
	++$i;
	array_push($video_batch,$obj);
}

?>