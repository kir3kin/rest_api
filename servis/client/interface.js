(function($) {
	$(document).ready(function() {

		$('#start').bind('click', function() {
			$.ajax({
				url: 'http://rest.loc/players/1',
				type: 'get',
				dataType: 'json',
				// data: {time_start: 147128, time_end: 14688, players_id: "1,4", winner_id: 1, match_log: "sdfsdf"},
				success: function(data) {
					console.log(data);
				},
				error: function(data, error, errorInfo) {
					// console.log(data);
					// console.log(error);
					console.error(errorInfo);
				},
			});
		});
	});
}(jQuery));