<?php

namespace Zver {

    use Zver\Package\Helper;

    class CasperJS
    {

        use Helper;

        public static function getScriptsDirectory($script = null)
        {
            $scriptPath = DirectoryWalker::fromCurrent()
                                         ->upUntil('src')
                                         ->up()
                                         ->enter('files')
                                         ->get();

            if (!is_null($script)) {
                $scriptPath .= $script;
            }

            return $scriptPath;
        }

        public static function isCasperJSInstalled()
        {
            return StringHelper::load(Common::executeInSystem('casperjs --version'))
                               ->trimSpaces()
                               ->toLowerCase()
                               ->isMatch('\d+\.\d+\.\d+');
        }

        public static function isPhantomJSInstalled()
        {
            return StringHelper::load(Common::executeInSystem('phantomjs -v'))
                               ->trimSpaces()
                               ->toLowerCase()
                               ->isMatch('\d+\.\d+\.\d+');
        }

        public static function executeScript($scriptPath, $arguments = [], $options = [])
        {
            $command = 'casperjs ' . escapeshellarg($scriptPath) . ' ' . implode(' ', $arguments) . ' ' . implode(' ', $options);

            return Common::executeInSystem($command);
        }

        protected static function getDefaultConsoleOptions($tempDirectory)
        {
            return [
                '--ignore-ssl-errors=true',
                '--ssl-protocol=any',
                '--disk-cache=false',
                '--web-security=false',
                '--output-encoding=utf8',
                '--cookies-file="' . $tempDirectory . DIRECTORY_SEPARATOR . 'cookie.txt"',
                '--disk-cache-path="' . $tempDirectory . DIRECTORY_SEPARATOR . 'cache"',
                '--local-storage-path="' . $tempDirectory . DIRECTORY_SEPARATOR . 'local"',
                '--offline-storage-path="' . $tempDirectory . DIRECTORY_SEPARATOR . 'offline"',
            ];
        }

        public static function getUrlContent(
            $url,
            $tempDirectory
        ) {

            $arguments = [
                escapeshellarg($url),
            ];

            return static::executeScript(static::getScriptsDirectory('getUrlContent.js'), $arguments, static::getDefaultConsoleOptions($tempDirectory));
        }

    }
}