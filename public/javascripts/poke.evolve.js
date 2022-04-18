(function($)  {
	$.fn.wlEvolve = function (path1, path2) {
        var part1 = 2500;
        var part2 = ( part1 + 50 ) - 3000;
        var part3 = ( part2 + 800 );
        var part4 = ( part3 + 50 );
        var part5 = part4 + 50;
        var part6 = 1000;

        var image = this;
        image.fadeOut(part1 - 400, function () {
            image.attr('src', path1);
            image.fadeIn();
            image.fadeOut(part2 + 200, function () {
                image.attr('src', path2);
                image.fadeIn();
                image.fadeOut(part3, function () {
                    image.attr('src', path1);
                    image.fadeIn();
                    image.fadeOut(part4, function () {
                        image.attr('src', path2);
                        image.fadeIn();
                        image.fadeOut(part5, function () {
                            image.attr('src', path1);
                            image.fadeIn();
                            image.fadeOut(part5+100, function () {
                                image.attr('src', path2);
                                image.fadeIn();
                                image.fadeOut(0, function () {
                                    image.attr('src', path1);
                                    image.fadeIn();
                                    image.fadeOut(part6+30, function () {
                                        image.attr('src', path2);
                                        image.fadeIn();
                                    });
                                });
                            });
                        });
                    });
                });
            });
        });
    }
})(jQuery);