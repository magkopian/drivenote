/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

$(document).ready(function() {

	$('table#users').on('change', '#user-all', function () {
		
		if ( $('#user-all').is(':checked') ) {
			$('input[id*=user-]').prop('checked', true);
		}
		else {
			$('input[id*=user-]').prop('checked', false);
		}
		
	});

	$('.user-actions').on('click', '.button', function () {
		var users = new Array(),
			action = $(this).data('action');
		
		$.each($('input[name="users[]"]:checked'), function() {
			users.push($(this).val());
		});
		
		if ( users.length > 0 ) {
			$.ajax({
				type: 'POST',
				url: '/user-edit.php',
				data: {
					'action': action,
					'users': users
				},
				success: function (data) {
					if ( data.status != 0 ) {
						alert(data.msg);
					}
					else {
						if ( action = 'delete' ) {
							$('input[name="users[]"]:checked').parents('tr').children('td')
							.css('background-color', '#FF2200')
							.css('border-color', '#FF2200').fadeOut(600)
							.css('border-color', '#FFF');
						}
						else if ( action = 'revoke-read' ) {
							// Add reader
						}
						else if ( action = 'grant-read' ) {
							// Remove reader
						}
					}
				},
				error: function(xhr, status, error) {
					console.log(xhr.status + ' ' + error);
				},
				dataType: 'json'
			});
		}
		
		return false;
	});
	
	$('div.user-actions').width($('table#users').width()).css('text-align', 'left');
});