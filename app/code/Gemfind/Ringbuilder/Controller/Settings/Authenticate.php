<?php

/**
 * @copyright Copyright (c) 2018 www.Gemfind.com
 */

namespace Gemfind\Ringbuilder\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Authenticate extends Action {
    

    /**
     * @var \Gemfind\Ringbuilder\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */    
    protected $resultJsonFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * ResultDropHint constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Gemfind\Ringbuilder\Helper\Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    
    public function __construct(
    Context $context, \Gemfind\Ringbuilder\Helper\Data $helper, \Magento\Store\Model\StoreManagerInterface $storeManager, JsonFactory $resultJsonFactory
    ) {
        $this->helper = $helper;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->helper->getDealerAuthapi(),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => $this->helper->getApiTimeout(),
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => '{"DealerID": "'.$this->helper->getUsername().'", "DealerPass": "'.$data['password'].'"}',
          CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "cache-control: no-cache"
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $data = array('status' => 0, 'msg' => $err );
            $result->setData(['output' => $data]);
            return $result;
        } else {
          if($response == '"User successfully authenticated."'){
            $data = array('status' => 1, 'msg' => 'User successfully authenticated.' );
            $result->setData(['output' => $data]);
            return $result;       
          }
          if($response == '"User not authenticated."'){
            $data = array('status' => 2, 'msg' => 'User not authenticated.' );
            $result->setData(['output' => $data]);
            return $result;       
          }
          if($response == '"User not found!"'){
            $data = array('status' => 2, 'msg' => 'User not found!' );
            $result->setData(['output' => $data]);
            return $result;       
          }          
        }
        }
        $message = __('Something went wrong, please try again later');
        $data = array('status' => 0, 'msg' => $message );
        $result->setData(['output' => $data]);
        return $result;
    }

}
