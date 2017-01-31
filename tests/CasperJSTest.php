<?php

use Zver\CasperJS;

class CasperJSTest extends PHPUnit\Framework\TestCase
{

    use Package\Test;

    public static function setUpBeforeClass()
    {

    }

    public static function tearDownAfterClass()
    {

    }

    public function testIsCasperJSInstalled()
    {
        $installed = CasperJS::isCasperJSInstalled();

        if (!$installed) {
            $this->fail("CasperJS is not installed. Test aborting.\n");
        }
    }

    public function testIsPhantomJSInstalled()
    {
        $installed = CasperJS::isPhantomJSInstalled();

        if (!$installed) {
            $this->fail("PhantomJS is not installed. Test aborting.\n");
        }
    }

    public function testFindFile()
    {
        $this->foreachFalse([
                                CasperJS::findScript(''),
                                CasperJS::findScript('notexisted'),
                                CasperJS::findScript('notexisted.js'),
                            ]);
    }
}