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

    public function testIsInstalled()
    {
        $installed = \Zver\CasperJS::isCasperJSInstalled();

        if (!$installed) {
            $this->fail("CasperJS is not installed. Test aborting.\n");
        }
    }

}