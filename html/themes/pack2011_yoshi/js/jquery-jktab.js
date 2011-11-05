/**
* MediaQuery MultiDevice Template ver 0.1É¿
* @Author: funnythingz
* @Url:    http://hiropo.co.uk
* 
* jquery-jktab.js
*/
(function($){
	$.fn.jktab = function(config){
		var defaults = {
		}
		var options=$.extend(defaults, config);
		return this.each(function(i){
			var self = $(this);
			var menu = $('.tabIndexMenu li', self);
			var col = $('.tabIndexCol', self);
			var showCol = function(eq){
				menu.removeClass('cur');
				menu.eq(eq).addClass('cur');
				col.stop(true, true).fadeOut();
				col.eq(eq).stop(true, true).fadeIn();
			}
			menu.css({
				display: 'block',
				float: 'left',
				whiteSpace: 'nowrap'
			});
			showCol(0);
			for( var i = 0, L = menu.length; i < L; i++ ){
				menu.eq(i).bind('click', i, function(e){
					showCol(e.data);
				});
			}
		});
	};
})(jQuery);