<?php
namespace Zver {

    class CasperJS
    {

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

        public static function getCasperJsCommand($scriptPath, $arguments = [], $options = [])
        {
            return sprintf('casperjs %s "%s" %s', implode(' ', $options), $scriptPath, implode(' ', $arguments));
        }

        public static function executeScript($scriptPath, $arguments = [], $options = [])
        {
            return Common::executeInSystem(static::getCasperJsCommand($scriptPath, $arguments, $options));
        }

        protected static function getSSLConsoleOptions()
        {
            return [
                '--ignore-ssl-errors=true',
                '--ssl-protocol=any',
            ];
        }

        public static function getUrlContent($url, $width = 1920, $height = 1280, $userAgent = '')
        {

            $arguments = [
                escapeshellarg($url),
                $width,
                $height,
                escapeshellarg($userAgent),
            ];

            return static::executeScript(Common::getPackageFilePath('getUrlContent.js'), $arguments, static::getSSLConsoleOptions());
        }

    }
}