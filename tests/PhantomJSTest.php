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
                ],
            ],
        ];

        foreach ($testData as $test) {

            $content = PhantomJS::getPageContent($test['url'],
                                                 1920,
                                                 1080,
                                                 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36',
                                                 10);

            foreach ($test['substrings'] as $substring) {
                $this->assertTrue(\Zver\StringHelper::load($content)
                                                    ->isContainIgnoreCase($substring),
                                  'URL=' . $test['url'] . ' is not contain SUBSTRING="' . $substring . '"');
            }

        }

    }

}