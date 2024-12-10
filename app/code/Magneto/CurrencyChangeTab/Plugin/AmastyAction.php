<?php
namespace Magneto\CurrencyChangeTab\Plugin;

use Amasty\GeoipRedirect\Plugin\Action;

class AmastyAction extends Action
{
    protected function _setNewCurrencyIfExist($country, $scopeStores, $currentStoreId)
    {
        return $this;
    }
}
