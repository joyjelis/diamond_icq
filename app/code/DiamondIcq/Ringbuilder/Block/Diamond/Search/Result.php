<?php

namespace DiamondIcq\Ringbuilder\Block\Diamond\Search;

class Result extends \Gemfind\Ringbuilder\Block\Diamond\Search\Result
{
    public function getAdditionalFilters()
    {
        $result = $this->helper->getJCOptiondata();

        return $result['data'];
    }
}
