<?php
namespace Zver {

    class CasperJS
    {

        public static function isCasperJSInstalled()
        {
            $version = StringHelper::load(@shell_exec('casperjs --version'))
                                   ->trimSpaces()
                                   ->toLowerCase();

            return $version->isMatch('^\d+\.\d+\.\d+$');
        }

        public static function isPhantomJSInstalled()
        {
            $version = StringHelper::load(@shell_exec('phantomjs -v'))
                                   ->trimSpaces()
                                   ->toLowerCase();

            return $version->isMatch('^\d+\.\d+\.\d+$');
        }

    }
}