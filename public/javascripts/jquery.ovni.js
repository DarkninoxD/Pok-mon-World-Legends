(function($){
	$.fn.ovni = function(speed, trigger = true){
		var b = speed, c = trigger;
		/* speed = velocidade da animação,
		trigger = (boolean) se vai animar ou não */
		if(c === true){
			this.css({position: 'fixed'}); // posição fixa

			var rh = Math.floor(($(window).height()-30) * Math.random()); // random height
			var rw = Math.floor(($(window).width()-30) * Math.random()); // random width

			this.animate({
				top: rh, 
				left: rw
			}, (b*650), function(){
				$(this).ovni(b, c)
			});
		}
	}
})(jQuery);