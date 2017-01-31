<?php

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
        $installed = \Zver\CasperJS::isCasperJSInstalled();

        if (!$installed) {
            $this->fail("CasperJS is not installed. Test aborting.\n");
        }
    }

    public function testIsPhantomJSInstalled()
    {
        $installed = \Zver\CasperJS::isPhantomJSInstalled();

        if (!$installed) {
            $this->fail("PhantomJS is not installed. Test aborting.\n");
        }
    }

}