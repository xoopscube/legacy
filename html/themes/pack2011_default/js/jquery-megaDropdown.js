/**
* MediaQuery MultiDevice Template ver 0.1����
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

	//Calculate width of all ul's
	$.fn.calcSubWidth = function() {
		rowWidth = 0;
		//Calculate row
		$(this).find("ul").each(function() {
			rowWidth += $(this).width();
		});
	};

	function megaHoverOver(){
		$(this).find(".sub").stop().fadeTo('fast', 1).show();

		if ( $(this).find(".row").length > 0 ) { //If row exists...
			var biggestRow = 0;
			//Calculate each row
			$(this).find(".row").each(function() {
				$(this).calcSubWidth();
				//Find biggest row
				if(rowWidth > biggestRow) {
					biggestRow = rowWidth;
				}
			});
			//Set width
			$(this).find(".sub").css({'width' :biggestRow});
			$(this).find(".row:last").css({'margin':'0'});

		} else { //If row does not exist...

			$(this).calcSubWidth();
			//Set Width
			$(this).find(".sub").css({'width' : rowWidth});

		}
	}

	function megaHoverOut(){
		$(this).find(".sub").stop().fadeTo('fast', 0, function() {
			$(this).hide();
		});
	}

	$(document).ready(function() {

		var config = {
			 sensitivity: 2, // number = sensitivity threshold (must be 1 or higher)
			 interval: 100, // number = milliseconds for onMouseOver polling interval
			 over: megaHoverOver, // function = onMouseOver callback (REQUIRED)
			 timeout: 500, // number = milliseconds delay before onMouseOut
			 out: megaHoverOut // function = onMouseOut callback (REQUIRED)
		};

		$("ul#topnav li .sub").css({'opacity':'0'});
		$("ul#topnav li").hoverIntent(config);

	});
})(jQuery);
