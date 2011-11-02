
(function($) {
	wpWordCount = {

		settings : {
			strip : /<[a-zA-Z\/][^<>]*>/g, // strip HTML tags
			clean : /[0-9.(),;:!?%#$¿'"_+=\\/-]+/g, // regexp to remove punctuation, etc.
			count : /\S\s+/g // counting regexp
		},

		block : 0,

		wc : function(tx) {
			var t = this, w = $('.word-count'), tc = 0;

			if ( t.block )
				return;

			t.block = 1;

			setTimeout( function() {
				if ( tx ) {
					tx = tx.replace(/ +/g, ' ').replace(/<br[ \/]*> /gi, '');
					tx = tx.replace(/<[a-zA-Z\/][^<>]*>/g, '').replace(/&nbsp;|&#160;/gi, ' ');
					tx.replace(/./g, function(){tc++;});
				}
				w.html(tc.toString());

				setTimeout( function() { t.block = 0; }, 2000 );
			}, 1 );
		}
	}

	$(document).bind( 'wpcountwords', function(e, txt) {
		wpWordCount.wc(txt);
	});
}(jQuery));
