<?php declare(strict_types=1);

namespace DiamondIcq\OpenGraph\Service;

/**
 * Interface LocaleFormatterInterface
 */
interface LocaleFormatterInterface
{
    /**
     * @param string $locale
     *
     * @return string
     */
    public function format(string $locale): string;
}
