<?php
namespace Magneto\TransferMethods\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Country implements ArrayInterface
{
    /**
     * Countries
     *
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    protected $_countryCollection;

    protected $_countryFactory;

    /**
     * Options array
     *
     * @var array
     */
    protected $_options;

    /**
     * @param \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection
     */
    public function __construct(
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection) {
        $this->_countryCollection = $countryCollection;
        $this->_countryFactory = $countryFactory;
    }

    public function toOptionArray()
    {
        $result = $this->getOptions();
        
        return $result;
    }

    public function getOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_countryCollection->loadData();
        }

        $cnt = $this->_options->getData();

        $options = array();
        foreach ($cnt as $key => $value) {
            $cid = $value['country_id'];
            $name = $this->getCountryname($cid);

            $options[] = [
                 'value' => $cid,
                 'label' => $name,
             ];
        }

        return $options;
    }

    public function getCountryname($countryCode){    
        $country = $this->_countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }
}