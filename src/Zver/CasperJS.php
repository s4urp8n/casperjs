<?php
namespace Zver {

    class CasperJS
    {

        public static function isCasperJSInstalled()
        {
            $installed = false;
            try {
                $version = StringHelper::load(@shell_exec('c222asperjs --version'))
                                       ->trimSpaces()
                                       ->toLowerCase();

                return $version->isMatch('^\d+\.\d+\.\d+$');
            }
            catch (\Exception $e) {
                $installed = false;
            }
        }

    }
}