<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package UndoCartAction
 */

namespace Magneto\UndoCartAction\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\CartFactory as CustomerCart;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magneto\UndoCartAction\Helper\Data;

class RestoreItem extends \Magento\Framework\App\Action\Action {

	/**
	 * @var StoreManagerInterface
	 */
	private $storeManager;

	/**
	 * @var ProductRepositoryInterface
	 */
	private $productRepository;

	/**
	 * @var Session
	 */
	private $helper;

	public function __construct(
		Context $context,
		StoreManagerInterface $storeManager,
		ProductRepositoryInterface $productRepository,
		CustomerCart $cart,
		Data $helper
	) {
		$this->productRepository = $productRepository;
		$this->storeManager = $storeManager;
		$this->cart = $cart;
		$this->helper = $helper;
		parent::__construct($context);
	}

	public function execute() {
		$restore_id = $this->getRequest()->getParam('restore_id');
		$items = $this->helper->getRestoreItems();
		foreach ($items as $productId => $info) {
			if ($productId == $restore_id) {
				$this->helper->RemoveItemFromSession($productId);
				$this->restoreCartItem($productId, $info);
				$this->messageManager->addSuccessMessage(__("Successfully Restored"));
			}
		}

		$resultRedirect = $this->resultRedirectFactory->create();
		$resultRedirect->setPath('checkout/cart');
		return $resultRedirect;
	}

	public function restoreCartItem($productId, $qty) {
		$cart = $this->cart->create();
		$product = $this->initProduct($productId);

		$buy_request['qty'] = $qty['qty'];
		$buy_request['buy_request'] = $qty['buy_request'];

		if (isset($buy_request['buy_request']['options'])) {
			$buy_request = ['product' => $productId, 'qty' => $buy_request['qty'], 'options' => $buy_request['buy_request']['options']];
		}

		$request = new \Magento\Framework\DataObject();
		$request->setData($buy_request);

		try {
			$cart->addProduct($product, $request);
			$cart->getQuote()->setTotalsCollectedFlag(false);
			$cart->save();
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Initialize product instance from request data
	 *
	 * @return \Magento\Catalog\Model\Product|false
	 */
	protected function initProduct($productId) {
		if ($productId) {
			$storeId = $this->storeManager->getStore()->getId();
			try {
				return $this->productRepository->getById($productId, false, $storeId);
			} catch (NoSuchEntityException $e) {
				return false;
			}
		}

		return false;
	}
}