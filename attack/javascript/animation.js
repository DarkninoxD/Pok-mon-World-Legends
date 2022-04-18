(function($)  {
	$.fn.wlAnimate = function(d, img = '') {
        let e = this;
        if (d === 'shake') {
            e.effect('shake');
        } else if (d === 'quick_atk') {
            e.effect('puff');
            setTimeout (function () {
                e.show('500');
            }, 1000);
        } else if (d === 'bounce') {
            e.effect('bounce');
        } else if (d === 'explode') {
            e.effect( 'explode' );
        } else if (d === 'minimize') {
            let w, h;
            w = e.width (); 
            h = e.height ();

            e.animate ({
                width: '0',
                height: '0'
            }, 1000, function () {
                e.width(w);
                e.height(h);
            });
        } else if (d === 'catch') {
            e.attr('src', img);
            e.addClass('shake-pokeball');
            setTimeout (function () {
                e.removeClass('shake-pokeball');
            }, 2000);
        } else if (d === 'not-catch') {
            let original = e.attr('src');
            e.attr('src', img);
            e.addClass('shake-pokeball');
            setTimeout (function () {
                e.removeClass('shake-pokeball');
                e.attr('src', original);
            }, 1300);
        } 
    }
})(jQuery);