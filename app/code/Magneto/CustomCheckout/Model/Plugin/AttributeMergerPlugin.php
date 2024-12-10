<?php

namespace Magneto\CustomCheckout\Model\Plugin;

class AttributeMergerPlugin
{
    public function afterMerge(\Magento\Checkout\Block\Checkout\AttributeMerger $subject, $result)
    {
        if (array_key_exists('country_id', $result)) {
            $result['country_id']['additionalClasses'] = 'custom_country placeholder1';
        }

        if (array_key_exists('city', $result)) {
            $result['city']['additionalClasses'] = 'custom_city placeholder1';
        }

        if (array_key_exists('postcode', $result)) {
            $result['postcode']['additionalClasses'] = 'custom_postcode placeholder1';
        }

        if (array_key_exists('telephone', $result)) {
            $result['telephone']['additionalClasses'] = 'custom_telephone placeholder1';
        }

        if (array_key_exists('firstname', $result)) {
            $result['firstname']['additionalClasses'] = 'custom_firstname placeholder1';
        }

        if (array_key_exists('lastname', $result)) {
            $result['lastname']['additionalClasses'] = 'custom_lastname placeholder1';
        }

        if (array_key_exists('street', $result)) {
            $result['street']['children'][0]['config']['additionalClasses'] = 'custom_street_0 placeholder1';
            $result['street']['children'][1]['config']['additionalClasses'] = 'custom_street_1 placeholder1';
        }

        return $result;
    }
}