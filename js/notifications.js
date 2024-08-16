let lastNotification = -1,isNotificationUp = false,firstTime = true;
let noti_link = !isMobile ? '/TVD (FYP)/' : '/TVD (FYP)/mobile/';
const fetchAndAddNotifications = () => {
	$.ajax({
		url : "/TVD (FYP)/PHP/action.php",
		method : 'post',
		data : {
			action : 'fetch-notifications'
		},
		dataType : 'json',
		success : (data) => {
			if (!data.session) {
				clearInterval(notificationInterval)
			}
			if (data.success && data.session) {
				if (data.unread == 0) $('.sideBar > .notification > i.red').hide();
				else $('.sideBar > .notification > i.red').show();
				for (let i = 0; i < data.notifications.length && data.notifications[i].id != lastNotification; ++i) {
					if (data.notifications[i].data != undefined) {
						if (firstTime) $('#notifications > div').append(`
							${data.notifications[i].data}
						`);
						else $('#notifications > div').prepend(`
							${data.notifications[i].data}
						`);
					}
				}
				$('.single-notification').on('click',(e) => {
					console.log(e.target.classList.contains('single-notification'))
					if (e.target.classList.contains('single-notification')) location = noti_link + e.target.getAttribute('data-address');
					else if (e.target.parentNode.classList.contains('thumbail') || e.target.tagName == 'B') location = noti_link + e.target.parentNode.parentNode.getAttribute('data-address');
					else location = noti_link + e.target.parentNode.getAttribute('data-address');
				})
				lastNotification = data.notifications[0].id;
				firstTime = false;
			}
		}
	})
}
$('.sideBar > .notification').on('click', (e) => {
	if (!isNotificationUp) {
		$('.sideBar > b.notification .red').hide();
		$.ajax({
			url : '/TVD (FYP)/PHP/action.php',
			method : 'post',
			data : {action : 'mark-all-notifications-as-read'},
			dataType : 'json',
			success : (data) => {
				$('#notifications > div > div').removeClass('unread');
			}
		})
		$("#notifications").show();
		$("#notifications").css('width','330px');
		isNotificationUp = true;
	}
	else {
		$("#notifications").css('width','0px');
		$("#notifications").hide();
		isNotificationUp = false;
	}
})
let notificationInterval = setInterval(fetchAndAddNotifications,10000);
fetchAndAddNotifications();