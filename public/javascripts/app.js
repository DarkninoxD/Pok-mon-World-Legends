$(document).ready (function () {
	$('[title]').tooltip({
		container: 'body',
		html: true
	});

	$('#example').dataTable({
		"bStateSave": true,
		"paging": false,
		"oLanguage": {
			"sUrl": "portugues.txt",
			"sLoadingRecords": "Carregando..."
		},
		searching: false,
		ordering: true,
		info: false,
		"columnDefs": [{
			"targets": 'no-sort',
			"orderable": false,
		}]
	});
	
	$('#example2').dataTable({
		"bStateSave": true,
		"paging": true,
		"oLanguage": {
			"sUrl": "portugues.txt",
			"sLoadingRecords": "Carregando..."
		}
	});
	
	$('#to_top').click(function () {
		$("html, body").animate({
			scrollTop: 0
		}, 600);
	});

	$('input[type="radio"]:visible').iCheck({
		radioClass: 'iradio_square-blue',
	});

	$(".colorbox-privacy").colorbox({
		width: "800",
		height: "600",
		iframe: true
	});

	$(".colorbox-rules").colorbox({
		width: "800",
		height: "600",
		iframe: true
	});

	$(".colorbox-terms").colorbox({
		width: "800",
		height: "600",
		iframe: true
	});

	if (window.history.replaceState) window.history.replaceState(null, null, window.location.href);

    refresh = setTimeout(function () { location.href = './'; }, 1200000);
});