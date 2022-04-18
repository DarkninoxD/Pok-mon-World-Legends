(function($){
	$.fn.wlOrientation = function (get) {
		let data = '';

		$(this).children('a').each(function () {
			data = $(this).data('orientation');

			if (data == get) {
				$(this).addClass('selected');
				return;
			}
		});
	}
})(jQuery);