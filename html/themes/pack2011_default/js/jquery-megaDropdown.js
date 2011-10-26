/**
* MediaQuery MultiDevice Template ver 0.1ƒÀ
* @Author: funnythingz
* @Url:    http://hiropo.co.uk
* 
* jquery-megaDropdown.js
*/
(function($){
	$.fn.megaDropdown = function(config){
		var defaults = {
			effectSpeed: 250
		}
		var options = $.extend(defaults, config);
		return this.each(function(i){
			var self = $(this);
			var contents = $('.gnaviChildCol', self);
			var bgClass = 'megaDropdownOver';
			
			contents.hide();
			self.bind('mouseenter mouseleave', i, function(e){
				if( $(window).width() > 581 ){
					if( e.type === 'mouseenter' ){
						contents.stop(true, true).fadeIn(options.effectSpeed);
						self.addClass(bgClass);
					}
					else if( e.type === 'mouseleave' ){
						contents.hide();
						self.removeClass(bgClass);
					}
				}
			});
		});
	};
})(jQuery);