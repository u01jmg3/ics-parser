<?php

use ICal\ICal;
use PHPUnit\Framework\TestCase;

class CleanCharacterTest extends TestCase
{
    // phpcs:disable Generic.Arrays.DisallowLongArraySyntax
    // phpcs:disable Squiz.Commenting.FunctionComment

    protected static function getMethod($name)
    {
        $class  = new ReflectionClass(ICal::class);
        $method = $class->getMethod($name);

        // < PHP 8.1.0
        $method->setAccessible(true);

        return $method;
    }

    public function testCleanCharacters()
    {
        $ical  = new ICal();
        $input = 'Test with emoji 🔴👍🏻';

        self::assertSame(
            self::getMethod('cleanCharacters')->invokeArgs($ical, array($input)),
            $input
        );
    }
}
