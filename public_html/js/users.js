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
		
		// From the checked users
		$.each($('input[name="users[]"]:checked'), function() {
			
			// Add to the array only those that are not already removed
			if ( $(this).parent().length ) {
				users.push($(this).val());
			}
			
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
						$(document).notify({
							'typeClass': 'error',
							'titleTxt': 'Error',
							'messageTxt': data.msg
						});
					}
					else {
						if ( action == 'delete' ) {

							// Removed not deleted users from users array
							users = users.filter( function( el ) {
								return data.notModified.indexOf(el) < 0;
							});

							// Removed deleted rows of deleted users
							$('input[name="users[]"]:checked').each(function() {	
								if ( $.inArray($(this).val(), users) != -1 ) {
									$(this).parents('tr').children('td').css('background-color', '#FF2200')
										.css('border-color', '#FFF').fadeOut(600, function () {
											$(this).remove();
									});
								}
							});
							
							// If not all selected users got deleted display warning
							if ( data.notModified.length > 0 ) {
								$(document).notify({
									'typeClass': 'warning',
									'titleTxt': 'Warning',
									'messageTxt': data.msg
								});
							}
							
						}
						else if ( action == 'revoke-access' ) {
							// Add reader
							
							// If not all selected users got modified display warning
							if ( data.notModified.length > 0 ) {
								$(document).notify({
									'typeClass': 'warning',
									'titleTxt': 'Warning',
									'messageTxt': data.msg
								});
							}
						}
						else if ( action == 'grant-read' ) {
							// Remove reader
							
							// If not all selected users got modified display warning
							if ( data.notModified.length > 0 ) {
								$(document).notify({
									'typeClass': 'warning',
									'titleTxt': 'Warning',
									'messageTxt': data.msg
								});
							}
						}
					}
				},
				error: function(xhr, status, error) {
					$(document).notify();
				},
				dataType: 'json'
			});
		}
		
		return false;
	});
	
	$('div.user-actions').width($('table#users').width()).css('text-align', 'left');
	
});