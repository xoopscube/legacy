function get_thumb_$dirname(name) {
	name = name.replace(/\.[^.]+$/, '');
	return moduleUrl+'/mailbbs/imgs/s/' + name + '.jpg';
}
