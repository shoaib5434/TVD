let videoBatch = [],allnextVideos = [];
let oldVideos = [];
let curVideo = null,nextVideo = null,previousVideo = null,isPlaying = true,videoIndex = 0;
let oldVideosCount = 0,globalMail,isVideoMuted = true;


let URLHashtag = new URLSearchParams(window.location.search).get('hashtag');
let URLUser = new URLSearchParams(window.location.search).get('user');
let startIndex = new URLSearchParams(window.location.search).get('index');
let URLVideoID = new URLSearchParams(window.location.search).get('video_id');

let addShowMoreEvent = () => {
	$('.see-more').off('click');
	$('.see-more').on('click', (e) => {
		e.preventDefault();
		$('.cur .post-data').html(`
			${curVideo.caption}
			<br>
			<a class="see-less">show less</a>
		`);
		for (let i = 0; i < $('.cur .post-data a').length; ++i) {
			let mobileHashtagLink = $('.cur .post-data a')[i].href;
			let indexOf = mobileHashtagLink.indexOf('/profile') != -1 ? mobileHashtagLink.indexOf('/profile') : mobileHashtagLink.indexOf('/hashtag');
			mobileHashtagLink = mobileHashtagLink.slice(0,indexOf + 1) + "mobile" + mobileHashtagLink.slice(indexOf);
			$('.cur .post-data a')[i].href = mobileHashtagLink;
		}
		$('.see-less').off('click');
		$('.see-less').on('click', (e) => {
			e.preventDefault();
			$('.cur .post-data')[0];
			$('.cur .post-data')[0].innerHTML = $('.cur .post-data')[0].innerHTML.substr(0,150);
			$('.cur .post-data')[0].innerHTML += `<a class="see-more">...see more</a>`;
			addShowMoreEvent()
		})
	})
}

const loadVideoInNext = () => {

	// Load Video Into Player
	$('.next video')[0].src = `/TVD (FYP)/videos/` + nextVideo.url;
	$('.next video')[0].load();
	$('.next video')[0].pause();

	// Add User's DP
	$('.next .user-details img').attr('src','/TVD (FYP)/images/DP/' + nextVideo.dp);
	$('.next .post-buttons div:nth-child(1) i').text(nextVideo.likes);
	$('.next .post-buttons div:nth-child(2) i').text(nextVideo.total_comments);

	//Check If Video Is Liked By User
	if (nextVideo.isLikedByUser) $('.next .post-buttons div:first-child p').addClass('liked');
	else $('.next .post-buttons div:first-child p').removeClass('liked');

	// Add Username & Name
	$('.next .user-details a').text(nextVideo.user_name);

	//Add Video Caption
	$('.next .post-data').html(`
		${nextVideo.caption}
	`);
	let postText = $('.next .post-data')[0].innerHTML;
	if (postText.length >= 150) {
		$('.next .post-data')[0].innerHTML = $('.next .post-data')[0].innerHTML.substr(0,150);
		$('.next .post-data')[0].innerHTML += `<a href="" class="see-more"> ...see more</a>`;
	}
	addShowMoreEvent();

	for (let i = 0; i < $('.next .post-data a').length; ++i) {
		let mobileHashtagLink = $('.next .post-data a')[i].href;
		let indexOf = mobileHashtagLink.indexOf('/profile') != -1 ? mobileHashtagLink.indexOf('/profile') : mobileHashtagLink.indexOf('/hashtag');
		mobileHashtagLink = mobileHashtagLink.slice(0,indexOf + 1) + "mobile" + mobileHashtagLink.slice(indexOf);
		$('.next .post-data a')[i].href = mobileHashtagLink;
	}

}
const loadVideoInPrevious = () => {
	// Load Video Into Player
	$('.prev video')[0].src = `/TVD (FYP)/videos/` + previousVideo.url;
	$('.prev video')[0].load();
	$('.prev video')[0].pause();

	// Add User's DP
	$('.prev .user-details img').attr('src','/TVD (FYP)/images/DP/' + previousVideo.dp);
	$('.prev .post-buttons div:nth-child(1) i').text(previousVideo.likes);
	$('.prev .post-buttons div:nth-child(2) i').text(nextVideo.total_comments);

	//Check If Video Is Liked By User
	if (previousVideo.isLikedByUser) $('.prev .post-buttons div:first-child p').addClass('liked');
	else $('.prev .post-buttons div:first-child p').removeClass('liked');

	// Add Username & Name
	$('.prev .user-details a').text(previousVideo.user_name);

	//Add Video Caption
	$('.prev .post-data').html(`
		${previousVideo.caption}
	`);
	let postText = $('.prev .post-data')[0].innerHTML;
	if (postText.length >= 150) {
		$('.prev .post-data')[0].innerHTML = $('.prev .post-data')[0].innerHTML.substr(0,150);
		$('.prev .post-data')[0].innerHTML += `<a href="" class="see-more"> ...see more</a>`;
	}
	addShowMoreEvent();

	for (let i = 0; i < $('.prev .post-data a').length; ++i) {
		let mobileHashtagLink = $('.prev .post-data a')[i].href;
		let indexOf = mobileHashtagLink.indexOf('/profile') != -1 ? mobileHashtagLink.indexOf('/profile') : mobileHashtagLink.indexOf('/hashtag');
		mobileHashtagLink = mobileHashtagLink.slice(0,indexOf + 1) + "mobile" + mobileHashtagLink.slice(indexOf);
		$('.prev .post-data a')[i].href = mobileHashtagLink;
	}
}

const loadVideoInCur = () => {
	// Load Video Into Player
	$('.cur video')[0].src = `/TVD (FYP)/videos/` + curVideo.url;
	$('.cur video')[0].load();

	// Add User's DP
	$('.cur .user-details img').attr('src','/TVD (FYP)/images/DP/' + curVideo.dp);
	$('.cur .post-buttons div:nth-child(1) i').text(curVideo.likes);
	$('.cur .post-buttons div:nth-child(2) i').text(curVideo.total_comments);

	//Check If Video Is Liked By User
	if (curVideo.isLikedByUser) $('.cur .post-buttons div:first-child p').addClass('liked');
	else $('.cur .post-buttons div:first-child p').removeClass('liked');

	// Add Username & Name
	$('.cur .user-details a').text(curVideo.user_name);

	//Add Video Caption
	$('.cur .post-data').html(`
		${curVideo.caption}
	`);
	let postText = $('.cur .post-data')[0].innerHTML;
	if (postText.length >= 150) {
		$('.cur .post-data')[0].innerHTML = $('.cur .post-data')[0].innerHTML.substr(0,150);
		$('.cur .post-data')[0].innerHTML += `<a href="" class="see-more"> ...see more</a>`;
	}
	addShowMoreEvent();

	for (let i = 0; i < $('.cur .post-data a').length; ++i) {
		let mobileHashtagLink = $('.cur .post-data a')[i].href;
		let indexOf = mobileHashtagLink.indexOf('/profile') != -1 ? mobileHashtagLink.indexOf('/profile') : mobileHashtagLink.indexOf('/hashtag');
		mobileHashtagLink = mobileHashtagLink.slice(0,indexOf + 1) + "mobile" + mobileHashtagLink.slice(indexOf);
		$('.cur .post-data a')[i].href = mobileHashtagLink;
	}

	//Load The Next Video
	++videoIndex;
	nextVideo = videoBatch.data[1];
	loadVideoInNext();
}

// Load Next Videos

const loadVideoBatch = (isBootUp) => {

	let fetchOBJ = {
		action : 'fetch-video-batch'
	};
	if (URLUser != null) fetchOBJ['user_id'] = URLUser;
	if (URLHashtag != null) fetchOBJ['hashtag'] = URLHashtag;
	if (startIndex != null) fetchOBJ['start'] = startIndex;
	if (URLVideoID != null) fetchOBJ['video_id'] = URLVideoID;
	$.ajax({
		url : '/TVD (FYP)/PHP/action.php',
		method : 'post',
		data : fetchOBJ,
		dataType : 'json',
		success : function (data) {
			startIndex = data.lastIndex + 1;
			videoBatch = data;
			videoIndex = 0;
			if (isBootUp) {
				curVideo = videoBatch.data[0];
				loadVideoInCur();
			}
			else {
				nextVideo = videoBatch.data[0];
				loadVideoInNext();
			}
		}
	});
}

const playNext = () => {
	$('.cur').css('top','-100%');
	$('.next').css('top','0');
	let lastCur = $('.cur')[0];
	let lastNext = $('.next')[0];
	let lastPrevious = $('.prev')[0];

	$('.next video')[0].play()
	$('.cur video')[0].autoplay = false;
	$('.cur video')[0].pause();
	/* Change Styles */
	lastCur.classList.remove('cur');
	lastCur.classList.add('prev');
	lastNext.classList.remove('next');
	lastNext.classList.add('cur');
	lastPrevious.classList.remove('prev');
	lastPrevious.classList.add('next');
	lastPrevious.style.display = 'none';
	lastPrevious.style.top = '100%';

	$('.cur video')[0].muted = isVideoMuted; 

	setTimeout(() => {
		lastPrevious.style.display = 'block';
	},1000);
	if (previousVideo != null) oldVideos.push(previousVideo);
	previousVideo = curVideo;
	curVideo = nextVideo;
	if (allnextVideos.length != 0) {
		nextVideo = allnextVideos[allnextVideos.length - 1];
		allnextVideos.pop();
		loadVideoInNext();
	}
	else {
		if (videoIndex >= 4) {
			loadVideoBatch(false);
		}
		else {
			++videoIndex;
			nextVideo = videoBatch.data[videoIndex];
			loadVideoInNext();
		}
	}
	isPlaying = true;
	/* Change Styles */
}

const playPrevious = () => {
	$('.cur').css('top','100%');
	$('.prev').css('top','0');
	let lastCur = $('.cur')[0];
	let lastNext = $('.next')[0];
	let lastPrevious = $('.prev')[0];

	$('.prev video')[0].play()
	$('.cur video')[0].autoplay = false;
	$('.cur video')[0].pause();

	lastCur.classList.remove('cur');
	lastCur.classList.add('next');
	lastNext.classList.remove('next');
	lastNext.classList.add('prev');
	lastPrevious.classList.remove('prev');
	lastPrevious.classList.add('cur');
	lastNext.style.display = 'none';
	lastNext.style.top = '-100%';
	setTimeout(() => {
		lastNext.style.display = 'block';
	},1000);
	if (nextVideo != null) allnextVideos.push(nextVideo);
	nextVideo = curVideo;
	curVideo = previousVideo;
	if (oldVideos.length != 0) {
		previousVideo = oldVideos[oldVideos.length - 1];
		oldVideos.pop();
	}
	else {
		previousVideo = null;
	}
	if (previousVideo != null) loadVideoInPrevious();
	isPlaying = true;
}

const bootUp = () => {
	loadVideoBatch(true);
	// $('.cur .video-area video')[0].muted = false;
}

bootUp()

// $('.next .video-area video').on('click', () => {
// 	if (isPlaying) {
// 		$('.next .video-area video')[0].pause();
// 		isPlaying = false;
// 	}
// 	else {
// 		$('.next .video-area video')[0].play();
// 		isPlaying = true;
// 	}
// })
// $('.prev .video-area video').on('click', () => {
// 	if (isPlaying) {
// 		$('.prev .video-area video')[0].pause();
// 		isPlaying = false;
// 	}
// 	else {
// 		$('.prev .video-area video')[0].play();
// 		isPlaying = true;
// 	}
// })

$('#commentsBox .commentsClose').on('click', () => {
	$('#commentsBox').hide();
	$('#shadow').hide();
});
$('p.fas.fa-comment').on('click', () => {
	$('#commentsBox').show();
	$('#shadow').show();

	$('#commentsBox .comments-list').html('');

	$.ajax({
		url : '/TVD (FYP)/PHP/action.php',
		data : {
			'action' : 'get-comments',
			post_id : curVideo.post_id
		},
		dataType : 'json',
		method : 'post',
		success : function (data) {
			for (let i = 0; i < data.data.length; ++i) {
				$('#commentsBox .comments-list').append(
					`
						<div class="single-comment">
							<div class="single-comment-header">
								<img src="/TVD (FYP)/images/DP/${data.data[i].dp}">
								<b>${data.data[i].name}</b>
								<p>${getTimeDiff(data[data[i].time_added])}</p>
							</div>
							<div class="single-comment-body">
								<p class="comment-text">
									${data.data[i].comment_text}
								</p>
							</div>
						</div>
					`
				);
			}

		}
	})

})

$(document).on('keyup',(event) => {
	if (event.keyCode == 40) {
		playNext();
	}
})
$(document).on('keyup',(event) => {
	if (event.keyCode == 38 && previousVideo != null) {
		playPrevious();
	}
})

$('#commentsBox .write-comment').on('submit', (event) => {
	event.preventDefault();
	let commentText = $('#comment-input-text').val();
	if (isLogin && commentText.length > 0) {
		// console.log(curVideo.post_id);
		$('#comment-input-text').val('');
		$.ajax({
			url : '/TVD (FYP)/PHP/action.php',
			data : {
				action : 'add-comment',
				videoid : curVideo.post_id,
				commenttext : commentText
			},
			dataType :'json',
			method : 'post',
			success : (data) => {
				$('.cur .fa-comment + i').text(parseInt($('.cur .fa-comment + i')[0].textContent) + 1);
				$('#commentsBox .comments-list').append(`
					<div class="single-comment">
						<div class="single-comment-header">
							<img src="/TVD (FYP)/images/DP/${curUser.dp}">
							<b>${curUser.name}</b>
							<p>Today</p>
						</div>
						<div class="single-comment-body">
							<p class="comment-text">
								${commentText}
							</p>
						</div>
					</div>
				`);
			}
		})
	}
	else if (!isLogin) {
		$('#login').show(300);
		$('#shadow').show();
	}
})

$('.fa-heart').on('click', () => {
	if (!isLogin) {
		$('#login').show(300);
		$('#shadow').show();
	}
	else {
		if (curVideo.isLikedByUser) {
			$('.cur .fa-heart').removeClass('liked');
			console.log(parseInt($('.cur .fa-heart + i')[0].textContent));
			$('.cur .fa-heart + i').text(parseInt($('.cur .fa-heart + i')[0].textContent) - 1);
			$.ajax({
				url : '/TVD (FYP)/PHP/action.php',
				data : {
					post_id : curVideo.post_id,
					action : 'unlike-video'
				},
				method : 'post',
				dataType : 'json',success : function (data) {
					
				}
			})
			curVideo.isLikedByUser = false;
		}
		else {
			$('.cur .fa-heart').addClass('liked');
			$('.cur .fa-heart + i').text(parseInt($('.cur .fa-heart + i')[0].textContent) + 1);
			$.ajax({
				url : '/TVD (FYP)/PHP/action.php',
				data : {
					post_id : curVideo.post_id,
					action : 'like-video'
				},
				method : 'post',
				dataType : 'json',success : function (data) {
					
				}
			})
			curVideo.isLikedByUser = true;
		}
	}
})
$('#mail-verification button.resend').on('click',(event) => {
	event.preventDefault()
	// Resend Ajax Request
	console.log(globalMail)
	$.ajax({
		url : '/TVD (FYP)/PHP/action.php',
		method : 'post',
		dataType : 'json',
		data : {
			action : 'resend-code',
			mail : globalMail
		},
		success : function(data) {
			if (data.success) {
				$('#mail-verification button.resend').attr('disabled','');
				$('#mail-verification button.resend').text('Resent!');
				$('#mail-verification button.resend').css('color','green');
			}
		}
	})

})

$('#mail-verification form').on('submit',(event) => {
	event.preventDefault()
	// Resend Ajax Request

	$.ajax({
		url : '/TVD (FYP)/PHP/action.php',
		method : 'post',
		dataType : 'json',
		data : {
			action : 'verify-code',
			mail : globalMail,
			code : $('#mail-verification form input[name="code"]').val()
		},
		success : function(data) {
			if (data.success) {
				if (data.success && data.verified) {
					$('#mail-verification').hide();
					$('#shadow').hide();
					location.reload();
				}
				else if (!data.verified) {
					$('#mail-verification .warning').show();
					$('#mail-verification .warning p').html('<b> Code Is Incorrect Try Again</b>')
					$('#mail-verification .warning').css('top','158px')
					$('#mail-verification form button').removeClass('getting')
				}
			}
		}
	})

})

$("#login form").on("submit", () => {

	event.preventDefault()
	$('#login .body form button').addClass('getting');
	console.log($('#login .body form input[name="email"]').val());
	$('#login .body form input[name="email"]').attr('disabled','disabled');
	$.ajax({
		url : '/TVD (FYP)/PHP/action.php',
		method : 'post',
		dataType : 'json',
		data : {
			action : 'user-login',
			mail : $("#login form input[name='email']").val(),
			password : $("#login form input[name='password']").val()
		},
		success : function (data) {

			$('#login form input[name="email"]').removeAttr('disabled');
			$('#login form button').removeClass('getting');
			if (!data.error && data.session) {
				$('#login').hide()
				$('#mail-verification').show();
				$('#mail-verification p.description b').text($("#login form input[name='email']").val());
			}
			else if (!data.error && data.password) {
				$('#login .warning').show();
				$('#login .warning p').html('<b> Email Is Not registered</b>')
				$('#login .warning').css('top','108px')
			}
			else if (!data.error && data.email) {
				$('#login .warning').show();
				$('#login .warning p').html('<b> Incorrect Password</b>')
				$('#login .warning').css('top','108px')
			}
		}
	})
})

