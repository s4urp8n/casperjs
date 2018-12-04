<?php

namespace Zver {

    use Zver\Package\Helper;

    class PhantomJS
    {

        protected static function getPhantomJSExecutable()
        {
            $executable = DirectoryWalker::fromCurrent()
                                         ->upUntil('src')
                                         ->enter('phantomjs')
                                         ->get('phantomjs');

            if (Common::isWindowsOS()) {
                $executable .= '.exe';
            }

            return $executable;
        }

        protected static function getScriptPath($script = null)
        {
            $scriptPath = DirectoryWalker::fromCurrent()
                                         ->upUntil('src')
                                         ->up()
                                         ->enter('scripts')
                                         ->get($script);


            return StringHelper::load($scriptPath)
                               ->ensureEndingIs('.js')
                               ->get();
        }

        protected static function getDefaultConsoleOptions()
        {
            return [
                '--ignore-ssl-errors=true',
                '--ssl-protocol=any',
                '--disk-cache=false',
                '--web-security=false',
                '--load-images=false',
                '--output-encoding=utf8',
            ];
        }

        public static function executeScript($script, $arguments = [], $options = [])
        {

            /**
             * executable
             */
            $commands = [escapeshellarg(static::getPhantomJSExecutable())];

            /**
             * default options
             */
            foreach (self::getDefaultConsoleOptions() as $key => $value) {
                $commands[] = $value;
            }

            /**
             * options
             */
            foreach ($options as $key => $value) {
                $commands[] = $value;
            }

            /**
             * script
             */
            $commands[] = escapeshellarg(static::getScriptPath($script));

            /**
             * arguments
             */
            foreach ($arguments as $key => $value) {
                $commands[] = escapeshellarg($value);
            }

            /**
             * join in command and execute
             */
            $command = implode(' ', $commands);

            return shell_exec($command);

        }

        /***
         * @param $url URL to open
         * @param int $w Browser window width
         * @param int $h Browser window height
         * @param string $userAgent
         * @param int $timeout Timeout in seconds
         * @param null $proxy 23.23.23.23:2020
         * @return null|string
         */
        public static function getPageContent(
            $url,
            $w = 1920,
            $h = 1080,
            $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36',
            $timeout = 5,
            $proxy = null
        ) {

            /**
             * arguments
             */
            $arguments = [
                $url,
                $w,
                $h,
                $userAgent,
                $timeout,
            ];

            /**
             * options
             */
            $options = [];

            if (!empty($proxy)) {
                $options = ['--proxy="' . $proxy . '"'];
            }

            return static::executeScript('getPageContent', $arguments, $options);
        }

    }
}