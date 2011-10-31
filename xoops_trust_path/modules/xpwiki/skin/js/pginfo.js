function xpwiki_parm_desc (id, mode) {
	var base = $(id).getElementsByTagName("input");
	mode = (mode)? false : true;
	for (var i = 0; i < base.length; i++ ) {
		base[i].disabled = mode;
	}
}

function xpwiki_pginfo_setradio(sw) {
	if (sw == 'eu1') {
		$('_eaid1').checked = true;
	}
	if (sw == 'eu2') {
		if (! $('_eaid3').checked) $('_eaid2').checked = true;
	}

	if (sw == 'eg1') {
		$('_egid1').checked = true;
	}
	if (sw == 'eg2') {
		if (! $('_egid3').checked) $('_egid2').checked = true;
	}

	if (sw == 'vu1') {
		$('_vaid1').checked = true;
	}
	if (sw == 'vu2') {
		if (! $('_vaid3').checked) $('_vaid2').checked = true;
	}

	if (sw == 'vg1') {
		$('_vgid1').checked = true;
	}
	if (sw == 'vg2') {
		if (! $('_vgid3').checked) $('_vgid2').checked = true;
	}
	
	if (sw == 'eg3') {
		$('_egid3').checked = true;
		xpwiki_pginfo_setradio('eu2');
	}
	if (sw == 'eu3') {
		$('_eaid3').checked = true;
		xpwiki_pginfo_setradio('eg2');
	}

	if (sw == 'vg3') {
		$('_vgid3').checked = true;
		xpwiki_pginfo_setradio('vu2');
	}
	if (sw == 'vu3') {
		$('_vaid3').checked = true;
		xpwiki_pginfo_setradio('vg2');
	}

}