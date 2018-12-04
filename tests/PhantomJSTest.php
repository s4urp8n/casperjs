<?php

use Zver\PhantomJS;

class PhantomJSTest extends PHPUnit\Framework\TestCase
{

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
                'url'        => 'https://youtube.com/',
                'substrings' => [
                    '<svg',
                    '<html',
                    '</html',
                    '<body',
                    'ytd-app',
                ],
            ],
        ];

        foreach ($testData as $test) {

            $content = PhantomJS::getPageContent($test['url']);

            foreach ($test['substrings'] as $substring) {
                $this->assertTrue(\Zver\StringHelper::load($content)
                                                    ->isContainIgnoreCase($substring),
                                  'URL=' . $test['url'] . ' is not contain SUBSTRING="' . $substring . '"');
            }

        }

    }

}