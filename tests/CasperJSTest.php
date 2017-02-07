<?php

use Zver\CasperJS;
use Zver\DirectoryWalker;

class CasperJSTest extends PHPUnit\Framework\TestCase
{

    use Package\Test;

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
        $this->assertException(function () {
            CasperJS::registerScriptDirectory(DirectoryWalker::fromCurrent()
                                                             ->enter(md5(rand(1, 9999)))
                                                             ->enter(md5(rand(1, 9999)))
                                                             ->enter(md5(rand(1, 9999)))
                                                             ->enter(md5(rand(1, 9999)))
                                                             ->enter(md5(rand(1, 9999)))
                                                             ->get());
        });
    }

    public function testFindFileScriptName()
    {

        /**
         * Full path
         */
        $this->foreachSame([
                               [CasperJS::findScript(packageTestFile('testFind.js')), packageTestFile('testFind.js')],
                               [
                                   CasperJS::findScript(\Zver\StringHelper::load(packageTestFile('testFind.js'))
                                                                          ->removeEnding('.js')
                                                                          ->get()),
                                   packageTestFile('testFind.js'),
                               ],
                           ]);

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
                                   ]);
            }

        }

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
}