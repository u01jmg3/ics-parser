<?php

use ICal\ICal;
use PHPUnit\Framework\TestCase;

class KeyValueTest extends TestCase
{
    // phpcs:disable Generic.Arrays.DisallowLongArraySyntax
    // phpcs:disable Squiz.Commenting.FunctionComment

    public function testBoundaryCharactersInsideQuotes()
    {
        $checks = array(
            0 => 'ATTENDEE',
            1 => array(
                0 => 'mailto:julien@ag.com',
                1 => array(
                    'PARTSTAT' => 'TENTATIVE',
                    'CN' => 'ju: @ag.com = Ju ; ',
                ),
            ),
        );

        $this->assertLines(
            'ATTENDEE;PARTSTAT=TENTATIVE;CN="ju: @ag.com = Ju ; ":mailto:julien@ag.com',
            $checks
        );
    }

    public function testUtf8Characters()
    {
        $checks = array(
            0 => 'ATTENDEE',
            1 => array(
                0 => 'mailto:juëǯ@ag.com',
                1 => array(
                    'PARTSTAT' => 'TENTATIVE',
                    'CN'       => 'juëǯĻ',
                ),
            ),
        );

        $this->assertLines(
            'ATTENDEE;PARTSTAT=TENTATIVE;CN=juëǯĻ:mailto:juëǯ@ag.com',
            $checks
        );

        $checks = array(
            0 => 'SUMMARY',
            1 => ' I love emojis 😀😁😁 ë, ǯ, Ļ',
        );

        $this->assertLines(
            'SUMMARY: I love emojis 😀😁😁 ë, ǯ, Ļ',
            $checks
        );
    }

    public function testParametersOfKeysWithMultipleValues()
    {
        $checks = array(
            0 => 'ATTENDEE',
            1 => array(
                0 => 'mailto:jsmith@example.com',
                1 => array(
                    'DELEGATED-TO' => array(
                        0 => 'mailto:jdoe@example.com',
                        1 => 'mailto:jqpublic@example.com',
                    ),
                ),
            ),
        );

        $this->assertLines(
            'ATTENDEE;DELEGATED-TO="mailto:jdoe@example.com","mailto:jqpublic@example.com":mailto:jsmith@example.com',
            $checks
        );
    }

    private function assertLines($lines, array $checks)
    {
        $ical = new ICal();

        self::assertSame($ical->keyValueFromString($lines), $checks);
    }
}
