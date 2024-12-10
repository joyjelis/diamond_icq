<?php declare(strict_types=1);

namespace DiamondIcq\OpenGraph\Service;

/**
 * Format locale as default
 * @see \Magento\Framework\View\Page\Config::__construct()
 *
 * Examples:
 *  en_US -> en
 *  zh_Hant_TW -> zh
 */
class DefaultLocaleFormatter implements LocaleFormatterInterface
{
    /**
     * @inheritDoc
     */
    public function format(string $locale): string
    {
        return strstr($locale, '_', true);
    }
}
