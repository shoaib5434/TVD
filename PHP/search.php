<?php

session_start();
include 'database_connection.php';
$connect = connect('tvd_user');
if (isset($_POST)) {
	if ($_POST['action'] == 'fetch_results') {
		try {
			$users;
			$query = "select name,username,dp,count(*) as followers from users left join followings on followings.followed_to = id where name like '%{$_POST['text']}%' or username like '%{$_POST['text']}%' group by followings.followed_to ORDER BY followers DESC";
			$users = $connect -> prepare($query);
			$users -> execute();
			$users = $users -> fetchAll(PDO::FETCH_ASSOC);
			$hashtags;
			$query = "select name,id,count(*) as vid_count from tvd_content.hashtag join tvd_content.hashtag_videos on hashtag_videos.hashtag_id = id where name like '%{$_POST['text']}%' group by hashtag_videos.hashtag_id order by vid_count desc; ";
			$hashtags = $connect -> prepare($query);
			$hashtags -> execute();
			$hashtags = $hashtags -> fetchAll(PDO::FETCH_ASSOC);
			$posts;
			$query = "select thumbail,post_id from tvd_content.posts where caption like '%{$_POST['text']}%' order by likes desc";
			$posts = $connect -> prepare($query);
			$posts -> execute();
			$posts = $posts -> fetchAll(PDO::FETCH_ASSOC);
			echo json_encode([
				'success' => true,
				'posts' => $posts,
				'hashtags' => $hashtags,
				'users' => $users
			]);
		}
		catch (PDOException $e) {
			echo json_encode([
				'success' => false,
				'message' => $e -> getMessage()
			]);
		}
	}
}
$connect = null;
?>