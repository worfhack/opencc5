<?php


    class Autoload
    {
        const INDEX_FILE = 'class_index.php';


        protected static $instance;
        protected        $root_dir;

        protected function __construct ()
        {
            $file = CACHE_DIR . Autoload::INDEX_FILE;
            $this->generateIndex ();

            if (/* @filemtime ($file) && */ is_readable ($file) )
            {
                $this->index = include ($file);
            }
            else
            {
                $this->generateIndex ();
            }
        }

        public function generateIndex ()
        {

            $classes = array_merge ($this->getClassesFromDir ('core/classes/'), $this->getClassesFromDir ('core/controller/')  );

            ksort ($classes);
            $content = '<?php return ' . var_export ($classes , true) . '; ?>';

            $filename = CACHE_DIR . Autoload::INDEX_FILE;

            $filename_tmp = tempnam (dirname ($filename) , basename ($filename . '.'));

            if ( $filename_tmp !== false && file_put_contents ($filename_tmp , $content) !== false )
            {


                if ( !@rename ($filename_tmp , $filename) )
                {
                    unlink ($filename_tmp);
                }
                else
                {

                    @chmod ($filename , 0666);
                }

            }
            // $filename_tmp couldn't be written. $filename should be there anyway (even if outdated), no need to die.
            else
            {
                Tools::error_log ('Cannot write temporary file ' . $filename_tmp);
            }
            $this->index = $classes;
            //die();
            //_KERNEL_DATAS_DIR_
        }

        protected function getClassesFromDir ($path , $host_mode = false)
        {
            $classes = [];
            //        	$root_dir = $this->root_dir;
            $root_dir = ROOT_DIR;
    
            foreach (scandir ($root_dir . $path) as $file)
            {
                if ( $file[0] != '.' )
                {
                    if ( is_dir ($root_dir . $path . $file) )
                    {
                        $classes = array_merge ($classes , $this->getClassesFromDir ($path . $file . '/' , $host_mode));
                    }
                    elseif ( substr ($file , -4) == '.php' )
                    {
                        $content = file_get_contents ($root_dir . $path . $file);
                       // d($root_dir . $path . $file);
                        $namespacePattern = '[\\a-z0-9_]*[\\]';
                        $pattern = '#\W((abstract\s+)?class|interface|trait)\s+(?P<classname>' . basename ($file , '.php') . '(?:Core)?)' . '(?:\s+extends\s+' . $namespacePattern . '[a-z][a-z0-9_]*)?(?:\s+implements\s+' . $namespacePattern . '[a-z][\\a-z0-9_]*(?:\s*,\s*' . $namespacePattern . '[a-z][\\a-z0-9_]*)*)?\s*\{#i';
                        //DONT LOAD CLASS WITH NAMESPACE - PSR4 autoloaded from composer
                        $usesNamespace = false;
                        foreach (token_get_all ($content) as $token)
                        {
                            if ( $token[0] === T_NAMESPACE )
                            {
                                $usesNamespace = true;
                                break;
                            }
                        }
                        if ( !$usesNamespace && preg_match ($pattern , $content , $m) )
                        {
                            $classes[$m['classname']] = ['path'     => $path . $file ,
                                                         'type'     => trim ($m[1]) ,
                                                         'override' => $host_mode];
                            if ( substr ($m['classname'] , -4) == 'Core' )
                            {
                                $classes[substr ($m['classname'] , 0 , -4)] = ['path'     => '' ,
                                                                               'type'     => $classes[$m['classname']]['type'] ,
                                                                               'override' => $host_mode];
                            }
                        }else
                        {

                        }
                    }
                }
            }
            return $classes;
        }

        public static function getInstance ()
        {
            if ( !Autoload::$instance )
            {
                Autoload::$instance = new Autoload();
            }
            return Autoload::$instance;
        }

        public function load ($classname)
        {

            $class_dir = ROOT_DIR;
            if ( !isset($this->index[$classname]) )
            {

                $this->generateIndex ();
            }

            //$this->generateIndex ();
            // regenerate the class index if the requested file doesn't exists
            /* if ( (!isset($this->index[$classname]) && $this->index[$classname]['path'] &&
                     !is_file ($this->root_dir . $this->index[$classname]['path'])) || (isset($this->index[$classname . 'Core']) && $this->index[$classname . 'Core']['path'] && !is_file ($this->root_dir . $this->index[$classname . 'Core']['path'])) ) {

                 //$this->generateIndex ();
             }*/
            // If $classname has not core suffix (E.g. Shop, Product)
            if ( substr ($classname , -4) != 'Core' )
            {                 // If requested class does not exist, load associated core class
                if ( isset($this->index[$classname]) && !$this->index[$classname]['path'] )
                {

                    require_once ($class_dir . $this->index[$classname . 'Core']['path']);
                    if ( $this->index[$classname . 'Core']['type'] != 'interface' )
                    {
                        eval($this->index[$classname . 'Core']['type'] . ' ' . $classname . ' extends ' . $classname . 'Core {}');
                    }
                }
                else
                {
                    // request a non Core Class load the associated Core class if exists
                    if ( isset($this->index[$classname . 'Core']) )
                    {
                        require_once ($this->root_dir . $this->index[$classname . 'Core']['path']);
                    }
                    if ( isset($this->index[$classname]) )
                    {


                        $class_dir = ROOT_DIR;

                        require_once ($class_dir . $this->index[$classname]['path']);
                    }
                }
            }
            // Call directly ProductCore, ShopCore class
            elseif ( isset($this->index[$classname]['path']) && $this->index[$classname]['path'] )
            {
                require_once ($this->root_dir . $this->index[$classname]['path']);
            }
        }


    }
