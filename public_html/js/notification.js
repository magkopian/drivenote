/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

(function ($) {
	$.fn.notify = function ( options ) {
		var settings, shade, close, title, message, notification, closeNotification;
			
		settings = $.extend({
			'notificationClass': 'notification modal',
			'shadeClass': 'shade',
			'typeClass': 'error',
			'closeClass': 'button-close',
			'titleTxt': 'Error',
			'messageTxt': 'An unexpected error has been occurred, please try again later. If the error persists contact the administrator.',
			'closeTxt': 'Close',
			'notificationHeight': '45px'
		}, options);
		
		closeNotification = function ( notification, shade ) {
			notification.fadeOut( 300, function () {
				$(this).remove();
			});
			
			shade.fadeOut( 300, function () {
				$(this).remove();
			});
		};
		
		
		shade = $('<div />', {
			'class': settings.shadeClass
		}).on('click', function () {
			closeNotification(notification, shade);
			return false;
		});
		
		
		close = $('<a />', {
			'text': settings.closeTxt,
			'class': settings.closeClass,
			'href': '#'
		}).on('click', function () {
			closeNotification(notification, shade);
			return false;
		});
		
		title = $('<h4 />', {
			'text': settings.titleTxt
		});
		
		message = $('<p />', {
			'html': settings.messageTxt
		});
		
		notification = $('<div />', {
			'class': settings.notificationClass + ' ' + settings.typeClass
		}).append(title, message, close);
		
		$('body').prepend(notification, shade);
		
		var autoHeight = notification.height();
		notification.height(0).animate({
			'height': autoHeight
		}, 200 );
		
	};
})(jQuery);