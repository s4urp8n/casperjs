<?php

use Zver\CasperJS;
use Zver\DirectoryWalker;

class CasperJSTest extends PHPUnit\Framework\TestCase
{

    use \Zver\Package\Test;

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
    }

    public function testIsPhantomJSInstalled()
    {
        $installed = CasperJS::isPhantomJSInstalled();

        if (!$installed) {
            $this->fail("PhantomJS is not installed. Test aborting.\n");
        }
    }

    public function testRegisterUnregisterDirs()
    {
        /**
         * Empty by default
         */
        $this->foreachSame([
                               [CasperJS::getRegisteredDirectories(), []],
                           ]);

        /**
         * Test add current folder
         */
        CasperJS::registerScriptDirectory(__DIR__);

        $this->foreachSame([
                               [CasperJS::getRegisteredDirectories(), [__DIR__]],
                           ]);

        /**
         * Test same directory isn't added
         */
        CasperJS::registerScriptDirectory(__DIR__);

        $this->foreachSame([
                               [CasperJS::getRegisteredDirectories(), [__DIR__]],
                           ]);

        /**
         * Add other directory
         */
        $otherDir = DirectoryWalker::fromCurrent()
                                   ->up()
                                   ->up()
                                   ->get();

        CasperJS::registerScriptDirectory($otherDir);

        $this->foreachSame([
                               [
                                   CasperJS::getRegisteredDirectories(),
                                   [
                                       __DIR__,
                                       $otherDir,
                                   ],
                               ],
                           ]);

        /**
         * Test unregister
         */
        CasperJS::unregisterDirectories();

        $this->foreachSame([
                               [CasperJS::getRegisteredDirectories(), []],
                           ]);
    }

    public function testRegisterException()
    {
        $this->expectException('\Exception');

        CasperJS::registerScriptDirectory(DirectoryWalker::fromCurrent()
                                                         ->enter(md5(rand(1, 9999)))
                                                         ->enter(md5(rand(1, 9999)))
                                                         ->enter(md5(rand(1, 9999)))
                                                         ->enter(md5(rand(1, 9999)))
                                                         ->enter(md5(rand(1, 9999)))
                                                         ->get());
    }

    public function testFindFileScriptName()
    {

        CasperJS::unregisterDirectories();

        $findDirectories = [
            DirectoryWalker::fromCurrent()
                           ->enter('files')
                           ->get(),
            DirectoryWalker::fromCurrent()
                           ->enter('classes/Package')
                           ->get(),
        ];

        $findFiles = ['testFind1', 'testFind2'];

        /**
         * Separate find test
         */
        foreach ($findDirectories as $findDirectory) {

            CasperJS::unregisterDirectories();
            CasperJS::registerScriptDirectory($findDirectory);

            foreach ($findFiles as $findFile) {
                $this->foreachSame([
                                       [
                                           CasperJS::findScript($findFile),
                                           $findDirectory . $findFile . '.js',
                                       ],
                                       [
                                           CasperJS::findScript($findFile . '.js'),
                                           $findDirectory . $findFile . '.js',
                                       ],
                                       [
                                           CasperJS::findScript($findDirectory . $findFile . '.js'),
                                           $findDirectory . $findFile . '.js',
                                       ],
                                   ]);
            }

        }

        /**
         * Together find test
         */
        CasperJS::unregisterDirectories();
        foreach ($findDirectories as $findDirectory) {
            CasperJS::registerScriptDirectory($findDirectory);
        }

        foreach ($findFiles as $findFile) {

            $this->foreachSame([
                                   [
                                       CasperJS::findScript($findFile),
                                       $findDirectories[0] . $findFile . '.js',
                                   ],
                                   [
                                       CasperJS::findScript($findFile . '.js'),
                                       $findDirectories[0] . $findFile . '.js',
                                   ],
                               ]);
        }

        /**
         * Unique files test
         */
        $this->foreachSame([
                               [
                                   CasperJS::findScript('testFind5'),
                                   $findDirectories[1] . 'testFind5.js',
                               ],
                               [
                                   CasperJS::findScript('testFind4'),
                                   $findDirectories[0] . 'testFind4.js',
                               ],
                           ]);
    }

    public function testFindUnexistedFile()
    {
        CasperJS::unregisterDirectories();

        /**
         * No register dirs, not esisted paths
         */
        $this->foreachFalse([
                                CasperJS::findScript(''),
                                CasperJS::findScript('notexisted'),
                                CasperJS::findScript('notexisted.js'),
                            ]);
    }

    public function testOptions()
    {

        $casper = CasperJS::init();

        $this->foreachSame([
                               [
                                   $casper->getOptions(),
                                   [],
                               ],
                               [
                                   $casper->getConsoleOptions(),
                                   [],
                               ],
                           ]);

        $options = [];

        for ($i = 1; $i < 10; $i++) {
            $casper->setOption('option' . $i, 'value' . $i);
            $casper->setConsoleOption('option' . $i, 'value' . $i);
            $options['option' . $i] = 'value' . $i;
        }

        $this->foreachSame([
                               [
                                   $options,
                                   $casper->getOptions(),
                               ],
                               [
                                   $options,
                                   $casper->getConsoleOptions(),
                               ],
                           ]);

        $casper->clearConsoleOptions();
        $this->foreachSame([
                               [
                                   $casper->getOptions(),
                                   $options,
                               ],
                               [
                                   $casper->getConsoleOptions(),
                                   [],
                               ],
                           ]);

        $casper->clearOptions();
        $this->foreachSame([
                               [
                                   $casper->getOptions(),
                                   [],
                               ],
                               [
                                   $casper->getConsoleOptions(),
                                   [],
                               ],
                           ]);

    }

    public function testNew()
    {
        $this->setExpectedException('Error');
        $casper = new CasperJS;
    }
}