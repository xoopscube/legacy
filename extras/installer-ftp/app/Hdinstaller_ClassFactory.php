<?php
class Hdinstaller_ClassFactory extends Ethna_ClassFactory
{
    /**
     *  指定されたクラスから想定されるファイルをincludeする
     *
     *  @access protected
     */
    function _include($class_name)
    {
        $file = sprintf("%s.%s", $class_name, $this->controller->getExt('php'));
        if (file_exists_ex($file)) {
            include_once $file;
            return true;
        }

        if (preg_match('/^(\w+?)_(.*)/', $class_name, $match)) {
            // try ethna app style
            // App_Foo_Bar_Baz -> Foo/Bar/App_Foo_Bar_Baz.php
            $tmp = explode("_", $match[2]);
            $tmp[count($tmp)-1] = $class_name;
            $file = sprintf('%s.%s',
                            implode(DIRECTORY_SEPARATOR, $tmp),
                            $this->controller->getExt('php'));
            if (file_exists_ex($file)) {
                include_once $file;
                return true;
            }

            // try ethna app & pear mixed style
            // App_Foo_Bar_Baz -> Foo/Bar/Baz.php
            $file = sprintf('%s.%s',
                            str_replace('_', DIRECTORY_SEPARATOR, $match[2]),
                            $this->controller->getExt('php'));
            if (file_exists_ex($file)) {
                include_once $file;
                return true;
            }

            // try ethna master style
            // Ethna_Foo_Bar -> class/Ethna/Foo/Ethna_Foo_Bar.php
            array_unshift($tmp, 'Curaga', 'class');
            $file = sprintf('%s.%s',
                            implode(DIRECTORY_SEPARATOR, $tmp),
                            $this->controller->getExt('php'));
            if (file_exists_ex($file)) {
                include_once $file;
                return true;
            }

            // try pear style
            // Foo_Bar_Baz -> Foo/Bar/Baz.php
            $file = sprintf('%s.%s',
                            str_replace('_', DIRECTORY_SEPARATOR, $class_name),
                            $this->controller->getExt('php'));
            if (file_exists_ex($file)) {
                include_once $file;
                return true;
            }
        }
        return false;
    }
}
