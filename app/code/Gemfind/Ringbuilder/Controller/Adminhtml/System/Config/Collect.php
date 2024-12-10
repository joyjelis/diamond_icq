<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Gemfind\Ringbuilder\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Gemfind\Ringbuilder\Helper\Data;

class Collect extends Action
{

    protected $resultJsonFactory;

    /**
     * @var Data
     */
    protected $helper;
	
	/**
     * @var RingBuilder 
     */
	protected $ringblock;
	
	/**
     * @var FileSystem 
     */
	protected $filesystem;
	
	/**
     * @var DirectoryList 
     */
	protected $directoryList;
	
	/**
     * @var CsvProcessor 
     */
	protected $csvProcessor;
	
	/** @var \Magento\Framework\Url */
	protected $urlHelper;
    
	/**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Data $helper,
		\Gemfind\Ringbuilder\Block\Settings\Ringfilter $ringblock,
		\Magento\Framework\File\Csv $csvProcessor,
		\Magento\Framework\App\Filesystem\DirectoryList $directoryList,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Url $urlHelper
    )
    {
		$this->ringblock = $ringblock;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
		$this->filesystem = $filesystem;  
		$this->directoryList = $directoryList;
		$this->csvProcessor = $csvProcessor;
		$this->urlHelper = $urlHelper;

        parent::__construct($context);
    }

    /**
     * Collect relations data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
		$result = $this->resultJsonFactory->create();	
        try {
			
            $this->generatefeed();
			
        } catch (\Exception $e) {
             return $result->setData(['success' => $e->getMessage()]);
        }
		
        /** @var \Magento\Framework\Controller\Result\Json $result */
        

        return $result->setData(['success' => true]);
    }

    public function generatefeed(){
		
		$request = array();
		$shapeValue =  $collection = $metal =  [];
        $shapesContent = $collectionContent = $metalContent = $itemperpage = $filtermode = '';
        // Convert the Shapes list into gemfind form

        if (array_key_exists('ring_shape', $request)) {
            $shapesContent = $request["ring_shape"];
        }
        // Convert the Ring_Collection list into gemfind form

        if (array_key_exists('ring_collection', $request)) {
            $collectionContent = $request["ring_collection"];
        }
        // Convert the Ring_Metal list into gemfind form

        if (array_key_exists('ring_metal', $request)) {
            $metalContent = $request["ring_metal"];
        }
        // Convert the SettingID list into gemfind form

        if(isset($request['settingid'])){
            $settingid = $request['settingid'];
        } else {
            $settingid = '';
        }
        // Convert the SettingID list into gemfind form

        if (array_key_exists('caratvalue', $request)) {
            $caratvalueContent = $request["caratvalue"];
        }


        if(isset($request['orderby']) && $request['orderby'] == "cost-h-l"){
            $orderby = 'cost';
            $direction = 'desc';
        } else {
            $orderby = 'cost';
            $direction = 'asc';
        }

        if (array_key_exists('filtermode', $request)) {
            $filtermode = $request["filtermode"];
        }
/*        echo "<pre>";
        print_r($request);
        exit;*/
        // Create the request array to sumbit to gemfind
        $requestData = [
            'shapes' => $shapesContent,
            'ring_metal' => $metalContent,
            'ring_collection' => $collectionContent,
            'price_from' => 0,
            'price_to' => '',
            'page_number' => 1,
            'page_size' => 1000,
            'sort_by' => $orderby,
            'sort_direction' => $direction,
            'settingId' => $settingid,
            'filtermode' => $filtermode,
        ];
		
		 $filters = $this->ringblock->getRingFilters();
		 
		 $collectionsOptions = (array) $filters['collections'];
		 //
		// echo "<pre>";print_r($filters);die;
			$data = array();
		 foreach ($collectionsOptions as $options) :
				$data[] = array('name' =>$options->collectionName, 'data' => $this->helper->sendRingRequest(array('ring_collection'=>$options->collectionName, 'page_number' => 1, 'page_size'=>1000)));
		 endforeach;
			//print_r($data);die;
		 foreach($data as $csvdata){
			
			$ringdata = array();
			$ringdata['data'][] = array('id', 'title', 'description', 'price', 'condition', 'link', 'availability', 'image_link', 'brand' );
			$csvname = $csvdata['name'];
			$ringdata['name'] = $csvname; 
			$rings = $csvdata['data']['rings'];
			
			foreach($rings as $ring){
				
				$sku = "";
				if($ring->currencySymbol == 'US$'){
					$symbol = '$';
				} else {
					$symbol = $ring->currencySymbol;
				}
				
				$sku = "SKU#".$this->helper->getRingById($ring->priceSettingId)['ringData']['styleNumber'];
				//if($ring->settingId == '2679608'){
					//print_r($this->helper->getRingById($ring->settingId)['ringData']);
				//}
				$ringdata['data'][] = array($sku, $ring->name, $this->helper->getRingById($ring->priceSettingId)['ringData']['description'], $symbol.$ring->cost, 'new', $this->getRingViewUrl($ring->priceSettingId,$ring->name), 'in stock', $ring->imageUrl, $this->helper->getDealerName());
			}
			//print_r($ringdata);
			$this->writeToCsv($ringdata);
		 }die;
	}
	
	public function writeToCsv($data){
		$fileDirectoryPath = $this->directoryList->getRoot();
		$path = $this->helper->getCsvPath();
		$filePath =  $fileDirectoryPath . '/' . $path;
		if(!is_dir($filePath))
			mkdir($filePath, 0777, true);
		$fileName = $data['name'].'.csv';
		$filePath =  $fileDirectoryPath . '/' . $path . $fileName;

		$this->csvProcessor
			->setEnclosure('"')
			->setDelimiter(',')
			->saveData($filePath, $data['data']);

		return true;
	}
	/**
     * @return string
     */
    public function getRingViewUrl($sku,$name)
    {   
    
        $metaltype = '14k-white-gold-metaltype-';
        $name = strtolower(str_replace(' ', '-', $name));
        $sku = '-sku-'.str_replace(' ', '-', $sku);
        return $this->urlHelper->getUrl('ringbuilder/settings/view', ['path' => $metaltype.$name.$sku, '_secure' => true]);
        
    }
}
?>