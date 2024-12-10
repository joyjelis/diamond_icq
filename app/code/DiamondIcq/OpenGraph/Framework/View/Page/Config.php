<?php declare(strict_types=1);

namespace DiamondIcq\OpenGraph\Framework\View\Page;

use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\View\Page\Config as MagentoConfig;

/**
 * Add getter for localeResolver
 */
class Config extends MagentoConfig
{
    /**
     * Get localeResolver
     *
     * @return ResolverInterface
     */
    public function getLocaleResolver(): ResolverInterface
    {
        return $this->localeResolver;
    }
}
