function get_thumb_$dirname(name, file) {
	var tmb = file.url.replace(name, file.simg);
	return tmb.replace(rootUrl+'/', '');
}
