<?php


class Autoload
{
    const INDEX_FILE = 'class_index.php';


    protected static $instance;
    protected $root_dir;

    protected function __construct()
    {
        $file = CACHE_DIR . Autoload::INDEX_FILE;

        if (/* @filemtime ($file) && */
        is_readable($file)) {
            $this->index = include($file);
        } else {
            $this->generateIndex();
        }
    }

    public function generateIndex()
    {

        $classes = array_merge($this->getClassesFromDir('core/classes/'), $this->getClassesFromDir('core/controller/'));

        ksort($classes);
        $content = '<?php return ' . var_export($classes, true) . '; ?>';

        $filename = CACHE_DIR . Autoload::INDEX_FILE;
        $filename_tmp = tempnam(dirname($filename), basename($filename . '.'));
        if ($filename_tmp !== false && file_put_contents($filename_tmp, $content) !== false) {
            if (!rename($filename_tmp, $filename)) {
                unlink($filename_tmp);
            } else {
                chmod($filename, 0666);
            }

        } // $filename_tmp couldn't be written. $filename should be there anyway (even if outdated), no need to die.
        else {
            throw new Exception('Cannot write temporary file ' . $filename_tmp);
        }
        $this->index = $classes;

    }

    protected function getClassesFromDir($path)
    {
        $classes = [];
        $root_dir = ROOT_DIR;

        foreach (scandir($root_dir . $path) as $file) {
            if ($file[0] != '.') {

                if (is_dir($root_dir . $path . $file)) {
                    $classes = array_merge($classes, $this->getClassesFromDir($path . $file . '/'));
                } elseif (substr($file, -4) == '.php') {
                    $info = pathinfo($file);
                    $fileName = $info['filename'];
                    $classes[$fileName] = $path . $file;

                }
            }
        }
        return $classes;
    }

    public static function getInstance()
    {
        if (!Autoload::$instance) {
            Autoload::$instance = new Autoload();
        }
        return Autoload::$instance;
    }

    public function load($classname)
    {

        $class_dir = ROOT_DIR;
        if (!isset($this->index[$classname])) {
            $this->generateIndex();
        }

        if (isset($this->index[$classname])) {
            $class_dir = ROOT_DIR;
            require_once($class_dir . $this->index[$classname]);
        }
    }


}
