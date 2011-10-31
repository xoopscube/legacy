function code_outline(id)
{
	if(navigator.appVersion.match(/MSIE\s*(6\.|5\.5)/)){
		if(document.getElementById(id+"_img")) {
			document.getElementById(id+"_img").style.height="1.2em";
			document.getElementById(id+"_img").style.verticalAlign="bottom";
		}
	}
	var dotimage = "&middot;&middot;&middot;";
	var vis = document.getElementById(id).style.display;
	if (vis=="none") {
		disp = '';
		disp2 = 'none';
		ch = '-';
	} else {
		disp = 'none';
		disp2 = '';
		ch = '+';
	}
	if (document.getElementById(id)) document.getElementById(id).style.display = disp;
	if (document.getElementById(id+"n")) document.getElementById(id+"n").style.display = disp;
	if (document.getElementById(id+"o")) document.getElementById(id+"o").style.display = disp;
	if (document.getElementById(id+"_img")) document.getElementById(id+"_img").style.display = disp2;
	if (document.getElementById(id+"a")) document.getElementById(id+"a").innerHTML = ch;
	if (vis=="none") {
		if (document.getElementById(id+"_img")) document.getElementById(id+"_img").innerHTML = '';
	} else {
		if (document.getElementById(id+"_img")) document.getElementById(id+"_img").innerHTML=dotimage;
	}

}

function code_classname(id,num,disp,cname)
{
	var ch = '';
	var dotimage = '';

	if (disp=="") {
		ch = '-';
		dotimage = '';
	} else {
		ch = '+';
		dotimage = "&middot;&middot;&middot;";
	}

	for (var i=num; i>0; i--) {
		if (document.getElementById(id+"_"+i)) {
			if (document.getElementById(id+"_"+i).className == cname) {
				if (document.getElementById(id+"_"+i).className == 'code_block' && navigator.appVersion.match(/MSIE\s*(6\.|5\.5)/)) {
					document.getElementById(id+"_"+i+"_img").style.height = "1.2em";
					document.getElementById(id+"_"+i+"_img").style.verticalAlign = "bottom";
				}
				if (document.getElementById(id+"_"+i+"o")) {
					if (document.getElementById(id+"_"+i)) {
						document.getElementById(id+"_"+i).style.display = disp;
						if (document.getElementById(id+"_"+i).className == 'code_block' && document.getElementById(id+"_"+i+"_img"))
							document.getElementById(id+"_"+i+"_img").innerHTML=dotimage;
					}
				}
				if (document.getElementById(id+"_"+i+"n")) document.getElementById(id+"_"+i+"n").style.display = disp;
				if (document.getElementById(id+"_"+i+"o")) document.getElementById(id+"_"+i+"o").style.display = disp;
				if (document.getElementById(id+"_"+i+"a")) document.getElementById(id+"_"+i+"a").innerHTML = ch;
			}	
		}
	}
}
