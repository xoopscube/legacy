<?php
class xelFinder extends elFinder {

	/**
	 * Constructor
	 *
	 * @param  array  elFinder and roots configurations
	 * @return void
	 * @author nao-pon
	 **/
	public function __construct($opts) {
		parent::__construct($opts);
		$this->commands['perm'] = array('target' => true, 'perm' => true, 'umask' => false, 'gids' => false);
	}

	/**
	* Set perm
	*
	* @param  array  $args  command arguments
	* @return array
	* @author nao-pon
	**/
	protected function perm($args) {

		$target = $args['target'];

		if (($volume = $this->volume($target)) != false) {
			if (method_exists($volume, 'savePerm')) {
				if ($volume->commandDisabled('perm')) {
					return array('error' => $this->error(self::ERROR_PERM_DENIED));
				}

				if ($args['perm'] === 'getgroups') {
					$groups = $volume->getGroups($target);
					return $groups? $groups : array('error' => $this->error($volume->error()));
				} else {
					$file = $volume->savePerm($target, $args['perm'], $args['umask'], $args['gids']);
					return $file? array('changed' => array($file)) : array('error' => $this->error($volume->error()));
				}
			}
		}
		return array('error' => $this->error(self::ERROR_UNKNOWN_CMD));
	}
}