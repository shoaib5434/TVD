let isLogin = false,username = "default";
let curUser = {};
function checkForSession() {
	let promiseObj = new Promise((resolve,reject) => {
		resolve($.ajax({
			url : '/TVD (FYP)/PHP/action.php',
			method : 'post',
			data : {
				action : 'check-for-session',
			},
			dataType : 'json',
			success : function (data) {
				console.log(data);
				if (data.session) {
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
			}
		}))
	});
	return promiseObj;
}

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