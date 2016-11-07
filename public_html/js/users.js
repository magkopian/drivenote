/************************************************\
 * Copyright (c) 2016 Manolis Agkopian          *
 * See the file LICENCE for copying permission. *
\************************************************/

$(document).ready(function () {

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

		if ( $.inArray(action, [
				'delete',
				'revoke-access',
				'grant-read',
				'revoke-membership',
				'grant-membership'
			]) == -1 ) {
			return;
		}

		// From the checked users
		$.each($('input[name="users[]"]:checked'), function () {

			// Add to the array only those that are not already removed
			if ( $(this).parent().length ) {
				users.push($(this).val());
			}

		});

		if ( users.length > 0 ) {

			$('a[data-action="' + action + '"]').addClass('disabled');

			$.ajax({
				type: 'POST',
				url: '/user-edit.php',
				data: {
					'action': action,
					'users': users
				},
				success: function ( data ) {
					if ( data.status != 0 ) {
						$(document).notify({
							'typeClass': 'error',
							'titleTxt': 'Error',
							'messageTxt': data.msg
						});
					}
					else {
						// Removed not deleted users from users array
						users = users.filter(function ( el ) {
							return data.notModified.indexOf(el) < 0;
						});

						$('input[name="users[]"]:checked').each(function () {

							if ( $.inArray($(this).val(), users) != -1 ) {

								if ( action == 'delete' ) {
									$(this).parents('tr').children('td').css('background-color', '#f88').css('border-color', '#fff').fadeOut(600, function () {
										$(this).parents('tr').remove();
										$('.odd').removeClass('odd');
										$('.even').removeClass('even');
										$('table tbody tr:nth-child(odd)').addClass('odd');
										$('table tbody tr:nth-child(even)').addClass('even');
									});
								}
								else if ( action == 'revoke-access' ) {
									$(this).parents('tr').children('td.role').html('');
								}
								else if ( action == 'grant-read' ) {
									$(this).parents('tr').children('td.role').html('Reader');
								}
								else if ( action == 'revoke-membership' ) {
									$(this).parents('tr').children('td.member').removeClass('table-success').addClass('table-error').html('No');
								}
								else if ( action == 'grant-membership' ) {
									$(this).parents('tr').children('td.member').removeClass('table-error').addClass('table-success').html('Yes');
								}

							}

						});

						// If not all selected users got modified display warning
						if ( data.notModified.length > 0 ) {
							$(document).notify({
								'typeClass': 'warning',
								'titleTxt': 'Warning',
								'messageTxt': data.msg
							});
						}

						$('a[data-action="' + action + '"]').removeClass('disabled');

					}
				},
				error: function ( xhr, status, error ) {
					$(document).notify();
				},
				dataType: 'json'
			});
		}

		return false;
	});

});
