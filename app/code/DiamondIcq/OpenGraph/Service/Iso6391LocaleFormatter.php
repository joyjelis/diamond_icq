<?php declare(strict_types=1);

namespace DiamondIcq\OpenGraph\Service;

/**
 * Format locale to ISO 639-1
 * https://www.w3schools.com/Tags/ref_language_codes.asp
 *
 * Examples:
 *  en_US -> en-US
 *  zh_Hant_TW -> zh-Hant-TW
 */
class Iso6391LocaleFormatter implements LocaleFormatterInterface
{
    /**
     * @inheritDoc
     */
    public function format(string $locale): string
    {
        return str_replace('_', '-', $locale);
    }
}
