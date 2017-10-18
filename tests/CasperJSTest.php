<?php

use Zver\CasperJS;

class CasperJSTest extends PHPUnit\Framework\TestCase
{

    use \Zver\Package\Helper;

    public static function setUpBeforeClass()
    {
        clearstatcache(true);
    }

    public static function tearDownAfterClass()
    {
        clearstatcache(true);
    }

    public function testIsCasperJSInstalled()
    {
        $installed = CasperJS::isCasperJSInstalled();

        if (!$installed) {
            $this->fail("CasperJS is not installed. Test aborting.\n");
        }

        $this->assertTrue($installed);
    }

    public function testIsPhantomJSInstalled()
    {
        $installed = CasperJS::isPhantomJSInstalled();

        if (!$installed) {
            $this->fail("PhantomJS is not installed. Test aborting.\n");
        }

        $this->assertTrue($installed);
    }

    public function testGetUrlContent()
    {

        $testData = [
            [
                'url'        => 'http://php.net/',
                'substrings' => [
                    'class',
                    'php',
                    '<html',
                    '</html',
                    '<body',
                    'nav',
                    'logo',
                    '</body',
                ],
            ],
            [
                'url'        => 'https://github.com/',
                'substrings' => [
                    'github',
                    'href',
                    '<svg',
                ],
            ],
            [
                'url'        => 'http://support.amd.com/en-us/download/desktop?os=Windows+10+-+64',
                'substrings' => [
                    'class="submitButton"',
                ],
            ],

        ];

        foreach ($testData as $test) {

            $content = CasperJS::getUrlContent($test['url'], \Zver\DirectoryWalker::fromCurrent()
                                                                                  ->enter('temp')
                                                                                  ->get());

            foreach ($test['substrings'] as $substring) {
                $this->assertContains($substring, $content);
            }

        }

    }

}