<?php
class Hdinstaller_Plugin extends Ethna_Plugin
{
    /**
     *  プラグインのインスタンスをレジストリから消す
     *
     *  @access private
     *  @param  string  $type   プラグインの種類
     *  @param  string  $name   プラグインの名前
     */
    function _loadPluginDirList()
    {
        $this->_dirlist[] = $this->controller->getDirectory('plugin');

        // include_path から検索
        $include_path_list = explode(PATH_SEPARATOR, get_include_path());

        // Communiy based libraries
        $extlib_dir = implode(DIRECTORY_SEPARATOR, array('Curaga', 'extlib', 'Plugin'));
        // Ethna bandle
        $class_dir = implode(DIRECTORY_SEPARATOR, array('Curaga', 'class', 'Plugin'));
        foreach ($include_path_list as $include_path) {
            if (is_dir($include_path . DIRECTORY_SEPARATOR . $extlib_dir)) {
                $this->_dirlist[] = $include_path . DIRECTORY_SEPARATOR . $extlib_dir;
            }
            if (is_dir($include_path . DIRECTORY_SEPARATOR . $class_dir)) {
                $this->_dirlist[] = $include_path . DIRECTORY_SEPARATOR . $class_dir;
            }
        }
    }
}
