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

    public function testCleanCharactersWithUnicodeCharacters()
    {
        $ical = new ICal();

        self::assertSame(
            '...',
            self::getMethod('cleanCharacters')->invokeArgs($ical, array("\xe2\x80\xa6"))
        );
    }

    public function testCleanCharactersWithEmojis()
    {
        $ical  = new ICal();
        $input = 'Test with emoji 🔴👍🏻';

        self::assertSame(
            $input,
            self::getMethod('cleanCharacters')->invokeArgs($ical, array($input))
        );
    }

    public function testCleanCharactersWithWindowsCharacters()
    {
        $ical  = new ICal();
        $input = self::getMethod('mb_chr')->invokeArgs($ical, array(133));

        self::assertSame(
            '...',
            self::getMethod('cleanCharacters')->invokeArgs($ical, array($input))
        );
    }
}
