// Check Login 
let isSignup = false,isLogin = false,options = false,isSearchUp = false;
let curUser = {};


$('#options .setting-logo').on('click', () => {
	window.location = '/TVD (FYP)/settings/profile';
})

$('.site-logo').on('click', () => {
	window.location = '/TVD (FYP)/';
})

$('.sideBar > b.search').on('click', () => {
	if (!isSearchUp) {
		$('.sideBar > b').css('font-size','20px')
		$("#search").show();
		$("#search").css('width','330px');
		isSearchUp = true;
	}
	else {
		$('.sideBar > b').css('font-size','17px')
		$("#search").css('width','0px');
		$("#search").hide();
		isSearchUp = false;
	}
})

$('.shadow-down').on('click',() => {
	if (!options) {
		options = true;
		$('#options').show();
		$('.shadow-down').show();
		$('.shadow-down i').removeClass('fa-caret-down');
		$('.shadow-down i').addClass('fa-caret-up');
	}
	else {
		options = false;
		$('#options').hide();
		$('.shadow-down i').removeClass('fa-caret-up');
		$('.shadow-down i').addClass('fa-caret-down');
	}
});

$('#options .logout').on('click', () => {
	$.ajax({
		url : '/TVD (FYP)/PHP/logout.php',
		method : 'post',
		dataType : 'json',
		success : function (data) {
			if (data.success) {
				location.reload()
				console.log("HII")
			}
		}
	})
});


function checkForSession(clearNotification = false) {
	$.ajax({
		url : '/TVD (FYP)/PHP/action.php',
		method : 'post',
		data : {
			action : 'check-for-session',
		},
		dataType : 'json',
		success : function (data) {
			if (data.session) {
				$('#header .header-buttons .header-login').hide();
				$('#header .header-buttons .header-signup').hide();
				$('#header .header-buttons .header-upload').show();
				$('#header .header-buttons > div').show();
				$('.header-user-icon').attr('src','/TVD (FYP)/images/DP/' + data.user_dp);
				curUser.dp = data.user_dp;
				curUser.name = data.user_name;
				curUser.username = data.user_username;
				isLogin = true;
			}
			if (data.session && !data.verified) {
				$('#mail-verification').show()
				$('#shadow').show()
				$('#mail-verification p.description b').text(data.message);
			}
			if (!data.session || !data.verified) {
				$('.sideBar > b.notification').hide();
				$('.sideBar > a:nth-child(2)').hide();
				$('.sideBar > a:nth-child(3)').hide();
			}
			if (!data.session) {
				$('#notLoggedIn').show();
			}
		}
	})
}
$.ajax({
	url : '/TVD (FYP)/PHP/action.php',
	data : {
		action : 'get-top-accounts'
	},
	method : 'post',
	dataType : 'json',
	success : function(data) {
		for (let i in data.data) {
			console.log(i)
			$('.sideBar .sug-acc').append(`
				<div class="account" data-username='${data.data[i].username}'>
					<img src="/TVD (FYP)/images/DP/${data.data[i].dp}" class="top-center">
					<a href="/TVD (FYP)/profile?user=${data.data[i].username}">${data.data[i].username}</a>
				</div>
			`);
		}
		$('.account > img').on('click', (e) => {
			window.location = '/TVD (FYP)/profile?user=' + e.target.parentNode.getAttribute('data-username');
		})
	}
})
$('.trend').on('click',e => {
	window.location = '/TVD (FYP)/hashtag?hash=' + e.target.innerText.substr(1);
})

const getTimeDiff = (a) => {
	let secs = new Date() - new Date(a);
	secs /= 1000;
	if (secs >= (365 * 24 * 60 * 60)) {
		return parseInt(secs / (365 * 24 * 60 * 60)) + 'y';
	}
	if (secs >= (7 * 24 * 60 * 60)) {
		return parseInt(secs / (7 * 24 * 60 * 60)) + 'w';
	}
	if (secs >= (24 * 60 * 60)) {
		return parseInt(secs / (24 * 60 * 60)) + 'd';
	}
	if (secs >= (60 * 60)) {
		return `${parseInt(secs / (60 * 60))} h`;
	}
	if (secs >= 60) {
		return `${parseInt(secs / (60))} m`;
	}
	else {
		return parseInt(secs) + 's';
	}
}