<?php
namespace Zver {

    class CasperJS
    {

        protected static $scriptDirectories = [];
        protected $options = [];
        protected $clientScripts = [];
        protected $requires = [];
        protected $consoleOptions = [];
        protected $viewportWidth = 1920;
        protected $viewportHeight = 1280;
        protected $loadImages = false;
        protected $loadPlugins = false;

        public function enableImages()
        {
            $this->loadImages = true;

            return $this;
        }

        public function enablePlugins()
        {
            $this->loadPlugins = true;

            return $this;
        }

        protected function __construct()
        {

        }

        public static function init()
        {
            return new static;
        }

        public function setClientScript($script)
        {

            $found = static::findScript($script);

            if ($found !== false && !in_array($found, $this->clientScripts)) {
                $this->clientScripts[] = $found;
            }

            return $this;
        }

        public function getClientScripts()
        {
            return $this->clientScripts;
        }

        public function clearClientScripts()
        {
            $this->clientScripts = [];

            return $this;
        }

        public function setOption($option, $value)
        {
            $this->options[$option] = $value;

            return $this;
        }

        public function setRequire($package)
        {
            $this->requires[] = $package;

            return $this;
        }

        public function getRequires()
        {
            return $this->requires;
        }

        public function getOptions()
        {
            return $this->options;
        }

        public function setConsoleOption($option, $value)
        {
            $this->consoleOptions[$option] = $value;

            return $this;
        }

        public function getConsoleOptions()
        {
            return $this->consoleOptions;
        }

        public function clearConsoleOptions()
        {
            $this->consoleOptions = [];

            return $this;
        }

        public function clearRequires()
        {
            $this->requires = [];

            return $this;
        }

        public function clearOptions()
        {
            $this->options = [];

            return $this;
        }

        public static function isCasperJSInstalled()
        {
            return StringHelper::load(@shell_exec('casperjs --version'))
                               ->trimSpaces()
                               ->toLowerCase()
                               ->isMatch('^\d+\.\d+\.\d+$');
        }

        public static function isPhantomJSInstalled()
        {
            return StringHelper::load(@shell_exec('phantomjs -v'))
                               ->trimSpaces()
                               ->toLowerCase()
                               ->isMatch('^\d+\.\d+\.\d+$');
        }

        public static function unregisterDirectories()
        {
            static::$scriptDirectories = [];
        }

        public static function getRegisteredDirectories()
        {
            return static::$scriptDirectories;
        }

        public static function registerScriptDirectory($directory)
        {
            $realpath = realpath($directory);

            if (file_exists($realpath) && is_dir($realpath)) {
                if (!in_array($directory, static::$scriptDirectories)) {
                    static::$scriptDirectories[] = $directory;
                }
            } else {
                throw new \Exception('Directory "' . $directory . '" is not exists');
            }
        }

        public static function findScript($scriptName)
        {
            $scriptFile = StringHelper::load(Common::replaceSlashesToPlatformSlashes($scriptName))
                                      ->ensureEndingIs('.js');

            /**
             * Full path to script
             */
            if (file_exists($scriptFile->get())) {
                return $scriptFile->get();
            }

            $scriptFile->removeBeginning(DIRECTORY_SEPARATOR);

            $testFile = '';

            foreach (static::getRegisteredDirectories() as $directory) {

                $dir = StringHelper::load(Common::replaceSlashesToPlatformSlashes($directory))
                                   ->ensureEndingIs(DIRECTORY_SEPARATOR);

                $testFile = $dir->getClone()
                                ->append($scriptFile)
                                ->get();

                if (file_exists($testFile)) {
                    return $testFile;
                }
            }

            return false;
        }

        public function ignoreSSLErrors()
        {
            return $this->setConsoleOption('ignore-ssl-errors', 'true')
                        ->setConsoleOption('ssl-protocol', 'any');
        }

    }
}