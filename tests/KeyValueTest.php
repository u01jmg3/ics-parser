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

    public function testEventSummaryWithQuotes()
    {
        $checks = array(
            0 => 'SUMMARY',
            1 => 'Test Event with "Quotation Marks" in the Title',
        );

        // This ensures the quotes in the property value (after the colon)
        // are kept literal and not stripped like parameter quotes.
        $this->assertLines(
            'SUMMARY:Test Event with "Quotation Marks" in the Title',
            $checks
        );
    }

    public function testPropertyWithParametersAndQuotedValue()
    {
        // A more complex case: Parameters (where quotes are stripped)
        // AND a Value (where quotes are kept) on the same line.
        $checks = array(
            0 => 'DESCRIPTION',
            1 => array(
                0 => 'He said "Hello World"',
                1 => array(
                    'ALTREP' => 'cid:part1.msg.de@example.org',
                ),
            ),
        );

        $this->assertLines(
            'DESCRIPTION;ALTREP="cid:part1.msg.de@example.org":He said "Hello World"',
            $checks
        );
    }

    public function testEscapedCharactersInValue()
    {
        $checks = array(
            0 => 'DESCRIPTION',
            1 => 'Meeting\; at the "Café", see you there.',
        );

        // Verifies the backslash escapes the semicolon so it isn't treated as a parameter delimiter
        $this->assertLines(
            'DESCRIPTION:Meeting\; at the "Café", see you there.',
            $checks
        );
    }

    public function testEmptyPropertyDescription()
    {
        $checks = array(
            0 => 'X-WR-CALDESC',
            1 => '',
        );

        // Ensure empty values don't cause errors
        $this->assertLines(
            'X-WR-CALDESC:',
            $checks
        );
    }

    private function assertLines($lines, array $checks)
    {
        $ical = new ICal();

        self::assertSame($ical->keyValueFromString($lines), $checks);
    }
}
