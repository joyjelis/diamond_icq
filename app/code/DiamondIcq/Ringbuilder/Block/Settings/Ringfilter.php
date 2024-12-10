<?php

namespace DiamondIcq\Ringbuilder\Block\Settings;

class Ringfilter extends \Gemfind\Ringbuilder\Block\Settings\Ringfilter
{

    /**
     * @return mixed
     */
    public function getRingFilters()
    {
        return $this->_helper->getRingFilters();
    }
}
