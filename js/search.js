let link = !isMobile ? '/TVD (FYP)' : '/TVD (FYP)/mobile';
$('#search .search-input input').val('');
$('.search-profile > p').on('click',(e) => {
	$('.search-profile > p.selected').removeClass('selected');
	e.target.classList.add('selected');

	if (e.target.innerText == "Videos") {
		$('.search-result-css.active').hide();
		$('.search-result-css.active').removeClass('active');
		$('.search-result-video').css('display','flex');
		$('.search-result-video').addClass('active');
	}
	else if (e.target.innerText == "Users") {
		$('.search-result-css.active').hide();
		$('.search-result-css.active').removeClass('active');
		$('.search-result-user').css('display','flex');
		$('.search-result-user').addClass('active');
	}
	else if (e.target.innerText == "Hashtags") {
		$('.search-result-css.active').hide();
		$('.search-result-css.active').removeClass('active');
		$('.search-result-hashtag').css('display','flex');
		$('.search-result-hashtag').addClass('active');
	}
})
const hashtagClickHandler = (e) => {
	location = link + '/hashtag?hashtag=' + e.getAttribute('data-address');
}

const userClickHandler = (e) => {
	console.log(e);
	location = link + '/profile?user=' + e.getAttribute('data-address');
}
const postClickHandler = (e) => {
	console.log(e);
	location = link + '?video_id=' + e.getAttribute('data-video_id');
}

const addListnersSearch = () => {
	console.log("Hello")
	$('.single-user').on('click', (e) => {
		if (e.target.tagName == 'IMG') userClickHandler(e.target.parentNode);
		else if (e.target.tagName == 'P') userClickHandler(e.target.parentNode);
		else userClickHandler(e.target);
	});


	$('.single-hashtag').on('click', (e) => {
		if (e.target.tagName == 'I') hashtagClickHandler(e.target.parentNode);
		else if (e.target.tagName == 'P') hashtagClickHandler(e.target.parentNode);
		else hashtagClickHandler(e.target);
	})
	$('.single-video').on('click', (e) => {
		if (e.target.tagName == 'I') postClickHandler(e.target.parentNode);
		if (e.target.tagName == 'IMG') postClickHandler(e.target.parentNode);
		else postClickHandler(e.target);
	})
}

const fetchSearchResults = (input) => {
	$.ajax({
		url : '/TVD (FYP)/PHP/search.php',
		method : 'post',
		dataType : 'json',
		data : {
			action : 'fetch_results',
			text : input
		},
		success : function (data) {
			$('.search-result-video').html('');
			$('.search-result-hashtag').html('');
			$('.search-result-user').html('');
			if (data.posts.length == 0) {

				$('.search-result-video').css('height','calc(100% - 55px)')
				$('.search-result-video').append(`
					<div class="no-result">
						<i>🤐</i>
						<p>No Result Found</p>
					</div>
				`);
			}

			else {
				$('.search-result-video').css('height','fit-content')
			}
			for (let i = 0; i < data.posts.length; ++i) {
				$('.search-result-video').append(`
					<div class="single-video" data-video_id='${data.posts[i].post_id}'>
						<p class="fas fa-play"></p>
						<img src="/TVD (FYP)/images/thumbails/${data.posts[i].thumbail}">
					</div>`);
			}
			if (data.hashtags.length == 0) {
				$('.search-result-hashtag').css('height','calc(100% - 55px)')
				$('.search-result-hashtag').append(`
					<div class="no-result">
						<i>🤐</i>
						<p>No Result Found</p>
					</div>
				`);
			}
			else {
				$('.search-result-hashtag').css('height','fit-content')
			}
			for (let i = 0; i < data.hashtags.length; ++i) {
				$('.search-result-hashtag').append(`
					<div class="single-hashtag" data-address="${data.hashtags[i].name}">
						<i>#</i>
						<p>#${data.hashtags[i].name}</p>
					</div>`);
			}
			if (data.users.length == 0) {
				$('.search-result-user').css('height','calc(100% - 55px)')
				$('.search-result-user').append(`
					<div class="no-result">
						<i>🤐</i>
						<p>No Result Found</p>
					</div>
				`);
			}

			else {
				$('.search-result-user').css('height','fit-content')
			}
			for (let i = 0; i < data.users.length; ++i) {
				$('.search-result-user').append(`
					<div class="single-user" data-address="${data.users[i].username}">
						<img src="/TVD (FYP)/images/DP/${data.users[i].dp}">
						<p>${data.users[i].username}</p>
					</div>`);
			}
			addListnersSearch();
		}
	})
	if (input.length == 0) {
		$(".no-history").show();
		$('.search-profile').css('display','none');
		$('.search-result-css').hide();
	}
	else if ($('.search-profile').css('display') == 'none') {
		$(".no-history").hide();
		$('.search-profile').css('display','flex');
		$('.search-result-video').css('display','flex');
	}

}
$("#search .search-input input").on('keyup', (e) => {
	fetchSearchResults(e.target.value);
})
$("#search .search-input input").on('change', (e) => {
	fetchSearchResults(e.target.value);
})