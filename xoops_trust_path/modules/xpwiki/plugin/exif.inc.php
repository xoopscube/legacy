<?php
class xpwiki_plugin_exif extends xpwiki_plugin {
	function plugin_exif_inline() {
		$args = func_get_args();
		$body = array_pop($args);
		if (isset($args[0])) {
			$file = $args[0];
			$filename = $this->get_filename($file);
			$exif_data = $this->func->get_exif_data($filename);
			$exif_tags = '';
			if ($exif_data){
				$exif_tags = $exif_data['title'];
				foreach($exif_data as $key => $value){
					if ($key != "title") $exif_tags .= "<br />$key: $value";
				}
			}
			return $exif_tags;
		}
		return 'error';
	}
	function plugin_exif_convert() {
		$args = func_get_args();
		if (isset($args[0])) {
			$file = $args[0];
			$filename = $this->get_filename($file);
			$exif_data = $this->func->get_exif_data($filename);
			$exif_tags = '';
			if ($exif_data){
				$exif_tags = '<h5>' . $exif_data['title'] . '</h5>';
				$exif_tags .= '<dl>';
				foreach($exif_data as $key => $value){
					if ($key != "title") $exif_tags .= "<dt>$key</dt><dd>$value</dd>";
				}
				$exif_tags .= '</dl>';
			}
			return '<div>' . $exif_tags . '</div>';
		}
	}

	function get_filename($var) {
		$page = ($this->root->vars['page'] === '#RenderMode')? $this->root->render_attach : $this->root->vars['page'];

		if (preg_match('#^(.+)/([^/]+)$#', $var, $matches)) {
			if ($matches[1] == '.' || $matches[1] == '..') {
				$matches[1] .= '/'; // Restore relative paths
			}

			// ページIDでの指定
			if (preg_match('/^#(\d+)$/', $matches[1], $arg)) {
				$matches[1] = $this->func->get_name_by_pgid($arg[1]);
			}

			$name = $matches[2];
			$page = $this->func->get_fullname($this->func->strip_bracket($matches[1]), $page); // strip is a compat
		} else {
			$name = $var;
		}
		return $this->cont['UPLOAD_DIR'] . $this->func->encode($page) . '_' . $this->func->encode($name);
	}
}
