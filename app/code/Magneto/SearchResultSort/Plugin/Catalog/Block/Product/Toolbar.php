<?php
namespace Magneto\SearchResultSort\Plugin\Catalog\Block\Product;

class Toolbar
{
    public function afterGetCurrentDirection(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, $result) {

        $controller = $subject->getRequest()->getControllerName();
        $action     = $subject->getRequest()->getActionName();
        $route      = $subject->getRequest()->getRouteName();
        if ($controller == 'result' && $action == 'index' && $route == 'catalogsearch') {
          $result = 'asc';
        } 
        
        return $result;
    }
}