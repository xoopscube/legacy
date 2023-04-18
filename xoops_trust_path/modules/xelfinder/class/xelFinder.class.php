<?php
class xelFinder extends elFinder {

	private $isAdmin = false;
	
	/**
	 * Constructor
	 *
	 * @param  array  elFinder and roots configurations
	 * @return void
	 * @author nao-pon
	 **/
	public function __construct($opts) {
		parent::__construct($opts);
		$this->isAdmin = $opts['isAdmin'];
		$this->commands['perm'] = ['targets' => true, 'perm' => true, 'umask' => false, 'gids' => false, 'filter' => false, 'uid' => false, 'phash' => false];
	}

	
	
	/**
	* Set perm
	*
	* @param  array  $args  command arguments
	* @return array
	* @author nao-pon
	**/
	protected function perm($args) {

		$targets = $args['targets'];
		if (!is_array($targets)) {
			$targets = [$targets];
		}
		if ($args['phash'] && is_array($args['phash']) && count($args['phash']) === count($targets)) {
			$phashes = $args['phash'];
		} else {
			$phashes = [];
		}

		if (($volume = $this->volume($targets[0])) != false) {
			if (method_exists($volume, 'savePerm')) {
				if ($volume->commandDisabled('perm')) {
					return ['error' => $this->error(self::ERROR_PERM_DENIED)];
				}

				$uid = ($this->isAdmin && is_numeric($args['uid']))? intval($args['uid']) : null;
				// @todo uid 存在するか？妥当性検査
				
				if ($args['perm'] === 'getgroups') {
					$groups = $volume->getGroups($targets[0]);
					return $groups ?: ['error' => $this->error($volume->error())];
				} else {
					$files = [];
					$errors = [];
					foreach($targets as $i => $target) {
						if (!isset($args['filter'])) $args['filter'] = '';
						$file = $volume->savePerm($target, $args['perm'], $args['umask'], $args['gids'], $args['filter'], $uid);
						if ($file) {
							if (!empty($phashes[$i])) {
								$file['alias']  = $volume->path($target);
								$file['target'] = $volume->getPath($target);
								$file['phash'] = $phashes[$i];
							}
							$files[] = $file;
						} else {
							$errors = array_merge($errors, $volume->error());
						}
					}
					$ret = [];
					if ($files) {
						$ret['changed'] = $files;
					} else {
						$ret['error'] = $this->error($errors);
					}
					return $ret;
				}
			}
		}
		return ['error' => $this->error(self::ERROR_UNKNOWN_CMD)];
	}
}