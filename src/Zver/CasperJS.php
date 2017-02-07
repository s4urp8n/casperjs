<?php
namespace Zver {

    class CasperJS
    {

        protected static $scriptDirectories = [];

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
            $realpath = @realpath($directory);

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

            foreach (static::$scriptDirectories as $directory) {

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

    }
}