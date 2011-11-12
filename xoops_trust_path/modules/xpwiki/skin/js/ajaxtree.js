function rawurlencode(str) {
  try {
    return encodeURIComponent(str)
          .replace(/!/g,  "%21")
          .replace(/'/g,  "%27")
          .replace(/\(/g, "%28")
          .replace(/\)/g, "%29")
          .replace(/\*/g, "%2A")
          .replace(/~/g,  "%7E");
  } catch(e) {
    return escape(str)
          .replace(/\+/g, "%2B")
          .replace(/\//g, "%2F")
          .replace(/@/g,  "%40");
  }
}

XpWiki.domInitFunctions.push(function() {
	var body = document.getElementsByTagName('body')[0];
	if (this.useSelector) {
		var x = body.querySelectorAll('div.xpwiki_ajaxtree');
		x.snapshotLength = x.length;
	} else {
		var x = document.evaluate('descendant::div[@class="xpwiki_ajaxtree"]', body, null, 6, null);
	}
	var n = 0;
	for (var i = 0; i < x.snapshotLength; i++) {
		var obj = (this.useSelector)? x[i] : x.snapshotItem(i);

		if (typeof(obj.onclick) != 'function') {
		
			obj.onclick = function(event) {
			  var baseUrl = XpWikiModuleUrl + "/" + this.id.replace('_ajaxtree', '') + "/skin/loader.php?src=";
			
			  var event   = event || window.event;
			  var element = event.target || event.srcElement;
			
			  if (element.nodeName == "LI") {
			    var li = element;
			    var ul = li.getElementsByTagName("ul")[0];
			
			    if (li.className == "expanded") {
			      li.className = "collapsed";
			      ul.style.display = "none";
			    } else if (li.className == "collapsed") {
			      if (ul) {
			        li.className = "expanded";
			        ul.style.display = "block";
			      } else {
			        var req = window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
			        if (req) {
			          var a    = li.getElementsByTagName("a")[0];
			          var name = rawurlencode(a.title).replace(/%/g, "%25");
			          var url  = baseUrl + 'ajaxtree_' + name + ".pcache.html&charset=" + XpWikiCharSet;
			
			          req.onreadystatechange = function() {
			            if (req.readyState == 4 && req.status == 200) {
			              li.className = "expanded";
			              li.innerHTML += req.responseText;
			            }
			          }
			          req.open("GET", url, true);
			          req.setRequestHeader("If-Modified-Since", "Thu, 01 Jan 1970 00:00:00 GMT");
			          req.send("");
			        }
			      }
			    }
			  }
			};
		}
	}

});
