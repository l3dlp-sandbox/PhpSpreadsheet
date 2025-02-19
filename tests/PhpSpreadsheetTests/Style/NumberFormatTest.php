<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\NumberFormatter;
use PHPUnit\Framework\TestCase;

class NumberFormatTest extends TestCase
{
    protected function setUp(): void
    {
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(',');
    }

    protected function tearDown(): void
    {
        StringHelper::setCurrencyCode(null);
        StringHelper::setDecimalSeparator(null);
        StringHelper::setThousandsSeparator(null);
    }

    /**
     * @param null|bool|float|int|string $args string to be formatted
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerNumberFormat')]
    public function testFormatValueWithMask(mixed $expectedResult, mixed ...$args): void
    {
        $result = NumberFormat::toFormattedString(...$args);
        self::assertSame($expectedResult, $result);
    }

    public static function providerNumberFormat(): array
    {
        return require 'tests/data/Style/NumberFormat.php';
    }

    /**
     * @param null|bool|float|int|string $args string to be formatted
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerNumberFormatFractions')]
    public function testFormatValueWithMaskFraction(mixed $expectedResult, mixed ...$args): void
    {
        $result = NumberFormat::toFormattedString(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerNumberFormatFractions(): array
    {
        return require 'tests/data/Style/NumberFormatFractions.php';
    }

    /**
     * @param null|bool|float|int|string $args string to be formatted
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerNumberFormatDates')]
    public function testFormatValueWithMaskDate(mixed $expectedResult, mixed ...$args): void
    {
        $result = NumberFormat::toFormattedString(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerNumberFormatDates(): array
    {
        return require 'tests/data/Style/NumberFormatDates.php';
    }

    public function testCurrencyCode(): void
    {
        // "Currency symbol" replaces $ in some cases, not in others
        $cur = StringHelper::getCurrencyCode();
        StringHelper::setCurrencyCode('€');
        $fmt1 = '#,##0.000\ [$]';
        $rslt = NumberFormat::toFormattedString(12345.679, $fmt1);
        self::assertEquals($rslt, '12,345.679 €');
        $fmt2 = '$ #,##0.000';
        $rslt = NumberFormat::toFormattedString(12345.679, $fmt2);
        self::assertEquals($rslt, '$ 12,345.679');
        StringHelper::setCurrencyCode($cur);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerNoScientific')]
    public function testNoScientific(string $expectedResult, string $numericString): void
    {
        $result = NumberFormatter::floatStringConvertScientific($numericString);
        self::assertSame($expectedResult, $result);
    }

    public static function providerNoScientific(): array
    {
        return [
            'large number' => ['92' . str_repeat('0', 16), '9.2E+17'],
            'no decimal portion' => ['16', '1.6E1'],
            'retain decimal 0 if supplied in string' => ['16.0', '1.60E1'],
            'exponent 0' => ['2.3', '2.3E0'],
            'whole and decimal' => ['16.5', '1.65E1'],
            'plus signs' => ['165000', '+1.65E+5'],
            'e2 one decimal' => ['489.7', '4.897E2'],
            'e2 no decimal' => ['-489', '-4.89E2'],
            'e2 fill units position' => ['480', '4.8E+2'],
            'no scientific notation' => ['3.14159', '3.14159'],
            'non-zero in first decimal' => ['0.165', '1.65E-1'],
            'one leading zero in decimal' => ['0.0165', '1.65E-2'],
            'four leading zeros in decimal' => ['-0.0000165', '-1.65E-5'],
            'small number' => ['0.' . str_repeat('0', 16) . '1', '1E-17'],
            'very small number' => ['0.' . str_repeat('0', 69) . '1', '1E-70'],
        ];
    }
}
