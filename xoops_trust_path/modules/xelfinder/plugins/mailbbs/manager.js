function get_thumb_$dirname(name) {
	name = name.replace(/\.[^.]+$/, '');
	return moduleUrl.replace(rootUrl+'/', '') + '/mailbbs/imgs/s/' + name + '.jpg';
}
