<?php declare(strict_types=1);

namespace DiamondIcq\OpenGraph\Plugin;

use DiamondIcq\OpenGraph\Framework\View\Page\Config;
use DiamondIcq\OpenGraph\Service\LocaleFormatterInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\View\Page\Config as MagentoConfig;

/**
 * Format html lang value
 */
class PageConfigPlugin
{
    /**
     * @var ResolverInterface
     */
    protected ResolverInterface $localeResolver;

    /**
     * @var LocaleFormatterInterface
     */
    protected LocaleFormatterInterface $localeFormatter;

    /**
     * PageConfigPlugin constructor.
     *
     * @param ResolverInterface $localeResolver
     * @param LocaleFormatterInterface $localeFormatter
     */
    public function __construct(
        ResolverInterface $localeResolver,
        LocaleFormatterInterface $localeFormatter
    ) {
        $this->localeResolver = $localeResolver;
        $this->localeFormatter = $localeFormatter;
    }

    /**
     * Format html lang value
     *
     * @param Config $subject
     * @param string $elementType
     * @param string $attribute
     * @param mixed $value
     *
     * @return array
     */
    public function beforeSetElementAttribute(
        Config $subject,
        string $elementType,
        string $attribute,
        $value
    ): array {
        if ($elementType === MagentoConfig::ELEMENT_TYPE_HTML
            && $attribute === MagentoConfig::HTML_ATTRIBUTE_LANG
        ) {
            $value = $this->localeFormatter->format(
                $subject->getLocaleResolver()->getLocale()
            );
        }
        return [$elementType, $attribute, $value];
    }
}
