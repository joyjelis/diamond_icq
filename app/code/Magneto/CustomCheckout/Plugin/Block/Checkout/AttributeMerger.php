<?php
namespace Magneto\CustomCheckout\Plugin\Block\Checkout;

/**
 * Class AttributeMerger
 * @package RH\Helloworld\Plugin\Block\Checkout\AttributeMerger
 */
class AttributeMerger
{
    /**
     * @param \Magento\Checkout\Block\Checkout\AttributeMerger $subject
     * @param $result
     * @return mixed
     */
    public function afterMerge(
        \Magento\Checkout\Block\Checkout\AttributeMerger $subject,
        $result
    ) {

        //$result['email']['config']['template'] = 'Magento_Checkout/form/field';
        
        $result['firstname']['config']['templates'] = 'Magento_Ui/form/field';
        $result['lastname']['config']['templates'] = 'Magento_Ui/form/field';
        $result['city']['config']['templates'] = 'Magento_Ui/form/field';
        $result['postcode']['config']['templates'] = 'Magento_Ui/form/field';
        $result['telephone']['config']['templates'] = 'Magento_Ui/form/field';
        $result['country_id']['config']['templates'] = 'Magento_Ui/form/field';
        $result['region_id']['config']['templates'] = 'Magento_Ui/form/field';
        
        $result['street']['component'] = 'Magento_Ui/js/form/components/group';
        //$result['street']['config']['template'] = 'Magento_Checkout\group\group';
        $result['street']['children'][0]['config']['templates'] = 'Magento_Ui/form/field';
        $result['street']['children'][1]['config']['templates'] = 'Magento_Ui/form/field';

        $result['firstname']['config']['templates'] = 'Magento_Ui/form/element/input';
        $result['lastname']['config']['templates'] = 'Magento_Ui/form/element/input';
        $result['city']['config']['templates'] = 'Magento_Ui/form/element/input';
        $result['postcode']['config']['templates'] = 'Magento_Ui/form/element/input';
        $result['telephone']['config']['templates'] = 'Magento_Ui/form/element/input';
        
        $result['street']['children'][0]['config']['templates'] = 'Magento_Ui/form/element/input';
        $result['street']['children'][1]['config']['templates'] = 'Magento_Ui/form/element/input';
        
        return $result;
    }
}
