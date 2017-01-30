<?php

namespace Package {

    trait Test
    {

        public function foreachTrue(array $values)
        {
            foreach ($values as $value) {
                $this->assertTrue($value);
            }
        }

        public function foreachFalse(array $values)
        {
            foreach ($values as $value) {
                $this->assertFalse($value);
            }
        }

        public function foreachEquals(array $values)
        {
            foreach ($values as $value) {
                $this->assertEquals($value[0], $value[1]);
            }
        }

        public function foreachNotEquals(array $values)
        {
            foreach ($values as $value) {
                $this->assertNotEquals($value[0], $value[1]);
            }
        }

        public function foreachSame(array $values)
        {
            foreach ($values as $value) {
                $this->assertSame($value[0], $value[1]);
            }
        }

        public function foreachNotSame(array $values)
        {
            foreach ($values as $value) {
                $this->assertNotSame($value[0], $value[1]);
            }
        }

        /**
         * From https://gist.github.com/VladaHejda/8826707
         *
         * @param callable $callback
         * @param string   $expectedException
         * @param null     $expectedCode
         * @param null     $expectedMessage
         */
        protected function assertException(
            callable $callback,
            $expectedException = 'Exception',
            $expectedCode = null,
            $expectedMessage = null
        ) {
            $expectedException = ltrim((string)$expectedException, '\\');
            if (!class_exists($expectedException) && !interface_exists($expectedException)) {
                $this->fail(sprintf('An exception of type "%s" does not exist.', $expectedException));
            }
            try {
                $callback();
            }
            catch (\Exception $e) {
                $class = get_class($e);
                $message = $e->getMessage();
                $code = $e->getCode();
                $errorMessage = 'Failed asserting the class of exception';
                if ($message && $code) {
                    $errorMessage .= sprintf(' (message was %s, code was %d)', $message, $code);
                } elseif ($code) {
                    $errorMessage .= sprintf(' (code was %d)', $code);
                }
                $errorMessage .= '.';
                $this->assertInstanceOf($expectedException, $e, $errorMessage);
                if ($expectedCode !== null) {
                    $this->assertEquals($expectedCode, $code, sprintf('Failed asserting code of thrown %s.', $class));
                }
                if ($expectedMessage !== null) {
                    $this->assertContains($expectedMessage, $message, sprintf('Failed asserting the message of thrown %s.', $class));
                }

                return;
            }
            $errorMessage = 'Failed asserting that exception';
            if (strtolower($expectedException) !== 'exception') {
                $errorMessage .= sprintf(' of type %s', $expectedException);
            }
            $errorMessage .= ' was thrown.';
            $this->fail($errorMessage);
        }

    }
}