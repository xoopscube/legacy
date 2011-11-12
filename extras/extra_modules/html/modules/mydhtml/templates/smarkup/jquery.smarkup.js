// ----------------------------------------------------------------------------
// SmartMarkUP - Universal Markup Editor and Engine
// markItUp! Universal MarkUp Engine, JQuery plugin
// v 1.0
// Licensed under GPL license.
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Joseph Woods & PHPCow.com
// http://www.phpcow.com/smartmarkup
// ----------------------------------------------------------------------------
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
// ----------------------------------------------------------------------------
(function($) {
	
	$.fn.sMarkUp = function(conf, height) {
		SMarkUp.bind(this.get(), conf, height);
	};
	
	$.sMarkUp = function(settings) {
		SMarkUp.insert(settings);
	};
	
	$.sMarkUpGetInstance = function(selector) {
		return SMarkUp.getInstance(selector.replace(/^#/g, ''));
	};
	
	$.sMarkUpGetInstanceByName = function(name) {
		return SMarkUp.getInstanceByName(name);
	};
	
	$.sMarkUpRemove = function(selector) {
		if (!selector) {
			selector = 'textarea';
		}
		$(selector).each(
			function() {
				$.sMarkUpGetInstance(this.id).remove();
			}
		);
	};
	
})(jQuery);