<?php declare(strict_types=1);

namespace DiamondIcq\OpenGraph\Model;

/**
 * Model class for alternate links
 */
class AlternateLink
{
    /**
     * @var string
     */
    protected string $href = '';

    /**
     * @var string
     */
    protected string $hreflang = '';

    /**
     * AlternateLink constructor.
     *
     * @param string $href
     * @param string $hreflang
     */
    public function __construct(
        string $href = '',
        string $hreflang = ''
    ) {
        $this->href = $href;
        $this->hreflang = $hreflang;
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @return string
     */
    public function getHreflang(): string
    {
        return $this->hreflang;
    }
}
