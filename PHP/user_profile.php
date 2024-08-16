<?php

session_start();
include 'database_connection.php';
$connect = connect('tvd_user');

if (isset($_POST)) {

	if ($_POST['action'] == 'get-user-info') {
		try {
			$user_found = true;
			$query = "SELECT dp,username,id,name,description FROM users WHERE";
			if (isset($_POST['username'])) $query .= " username = '{$_POST['username']}'";
			else if (isset($_SESSION['id'])) $query .= " id = {$_SESSION['id']}";
			else $user_found = false;
			if ($user_found) {
				$query = $connect -> prepare($query);
				$query -> execute();
				$result = $query -> fetch(PDO::FETCH_ASSOC);
			}
			if (!$user_found || $query -> rowCount() == 0) {
				echo json_encode(
					[
						'success' => true,
						'user_found' => false
					]
				);
			}

			else {
				$user_id = intval($result['id']);

				$follower_count = $connect -> prepare("SELECT count(*) FROM followings WHERE followed_to = {$user_id}");
				$follower_count -> execute();
				$follower_count = $follower_count -> fetch(PDO::FETCH_NUM);
				$follower_count = $follower_count[0];

				$following_count = $connect -> prepare("SELECT count(*) FROM followings WHERE followed_by = {$user_id}");
				$following_count -> execute();
				$following_count = $following_count -> fetch(PDO::FETCH_NUM);
				$following_count = $following_count[0];

				$isFollowedByUser = false;
				if (isset($_SESSION['id']) && $_SESSION['id'] != $user_id) {
					$isFollowedByUser = $connect -> prepare("SELECT followed_by FROM followings where followed_to = {$user_id} AND followed_by = {$_SESSION['id']}");
					$isFollowedByUser -> execute();
					// echo $isFollowedByUser -> rowCount();
					$isFollowedByUser = $isFollowedByUser -> rowCount() > 0;
				}

				$connect_content = connect('tvd_content');
				$videos_count = $connect_content -> prepare("SELECT count(*) FROM posts WHERE user_id = {$user_id}");
				$videos_count -> execute();
				$videos_count = $videos_count -> fetch(PDO::FETCH_NUM);
				$videos_count = $videos_count[0];

				echo json_encode(
					[
						'success' => true,
						'user_found' => true,
						'username' => $result['username'],
						'name' => $result['name'],
						'dp' => $result['dp'],
						'id' => $result['id'],
						'description' => $result['description'],
						'follower_count' => $follower_count,
						'following_count' => $following_count,
						'videos_count' => $videos_count,
						'isFollowedByUser' => $isFollowedByUser
					]
				);
			}
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

	if ($_POST['action'] == 'delete-video') {
		try {
			$connect_content = connect('tvd_content');
			$videoFileName = $connect_content -> prepare("SELECT videofile,thumbail FROM posts where post_id = {$_POST['video_id']}");
			$videoFileName -> execute();
			$videoFileName = $videoFileName -> fetch(PDO::FETCH_ASSOC);
			$thumbailFile = $videoFileName['thumbail'];
			$videoFileName = $videoFileName['videofile'];
			$query = "
				DELETE FROM posts WHERE post_id = {$_POST['video_id']};
			";
			$connect_content -> prepare($query) -> execute();
			$query = "
				DELETE FROM comments WHERE post_id = {$_POST['video_id']};
			";
			$connect_content -> prepare($query) -> execute();
			$query = "
				DELETE FROM liked_videos WHERE post_id = {$_POST['video_id']};
			";
			$connect_content -> prepare($query) -> execute();
			$query = "
				DELETE FROM video_mentions WHERE video_id = {$_POST['video_id']};
			";
			$connect_content -> prepare($query) -> execute();
			$query = "
				DELETE FROM hashtag_videos WHERE video_id = {$_POST['video_id']};
			";
			$connect_content -> prepare($query) -> execute();
			unlink('../videos/' . $videoFileName);
			unlink('../images/thumbails/' . $thumbailFile);
			echo json_encode([
				'success' => true,
				'message' => "Done"
			]);
		}
		catch (PDOException $e) {
			echo json_encode([
				'success' => false,
				'message' => $e -> getMessage()
			]);
		}
	}

	if ($_POST['action'] == 'get-user-videos') {
		try {
			$connect_content = connect('tvd_content');
			$user_id = $connect -> prepare("SELECT id FROM users WHERE username = '{$_POST['username']}'");
			$user_id -> execute();
			$user_id = $user_id -> fetch(PDO::FETCH_NUM);
			$user_id = $user_id[0];
			$query = $connect_content -> prepare("SELECT thumbail,post_id FROM posts WHERE user_id = {$user_id} LIMIT {$_POST['start']}, {$_POST['limit']}");
			$query -> execute();
			$videos = $query -> fetchAll(PDO::FETCH_ASSOC);

			$connect_content = $connect = null;

			echo json_encode(
				[
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

}

?>