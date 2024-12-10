<?php

namespace Customer\Sell\Model;

use Customer\Sell\Api\Data\SellInterface;
use Customer\Sell\Api\SellRepositoryInterface;
use Magento\Framework\Exception\NotFoundException;

class SellRepository implements SellRepositoryInterface
{
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Customer\Sell\Model\SellFactory $sellFactory,
        \Customer\Sell\Helper\Data $helper
    ) {
        $this->eventManager = $eventManager;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->sellFactory = $sellFactory;
        $this->helper = $helper;
    }
    /**
     * Save Sell details
     * @param \Customer\Sell\Api\Data\SellInterface $sell
     * @return \Customer\Sell\Api\Data\SellInterface $sell
     */
    public function save(SellInterface $sell)
    {
        $error = false;

        if ($sell->getCustomerId()) {
            $customer = $this->customerRepository->getById($sell->getCustomerId());
            if ((int)$customer->getId()) {
                // validate customer name
                $customerName = $customer->getFirstName() . " " . $customer->getLastName();
                if (stripos($sell->getName(), $customerName) === false) {
                    throw new NotFoundException(__('Customer name does not matched'));
                }

                // validate customer email
                if ($sell->getEmail() != $customer->getEmail()) {
                    throw new NotFoundException(__('Customer email does not matched'));
                }

                if ($defaultBilling = $customer->getDefaultBilling()) {
                    $address = $this->addressRepository->getById($defaultBilling);
                    if ($address) {
                        $sell->setMobile($address->getTelephone());
                    }
                } elseif ($defaultShipping = $customer->getDefaultBilling()) {
                    $address = $this->addressRepository->getById($defaultShipping);
                    if ($address) {
                        $sell->setMobile($address->getTelephone());
                    }
                }
            }
        }

        $_name = trim($sell->getName());
        if (!empty($_name)) {
            $sell->setName(preg_replace('/[^a-z0-9 ]/i', '', $_name));
        }

        $_email = trim($sell->getEmail());
        if (!empty($_email)) {
            $sell->setEmail($_email);
        }

        $_mobile = trim($sell->getMobile());
        if (!empty($_mobile)) {
            $sell->setMobile(preg_replace('/[^0-9+]/', '', $_mobile));
        }
        
        if ($sell->getDescription()) {
            $_text = trim($sell->getDescription());
            $_text = strip_tags($_text);
            $sell->setDescription($_text);
        }

        if ($sell->getCertificateRemark()) {
            $_text = trim($sell->getCertificateRemark());
            $_text = strip_tags($_text);
            $sell->setCertificateRemark($_text);
        }
        
        if (!$sell->getJewelleryType()) {
            $error = "Jewellery type is required.";
        } elseif (empty($_name)) {
            $error = "Name is required.";
        } elseif (empty($_mobile) && empty($_email)) {
            $error = "Mobile number or email address is required.";
        } elseif (!empty($_email)) {
            if (!filter_var($_email, FILTER_VALIDATE_EMAIL)) {
                $error = "Email address is invalid.";
            }
        }

        if ($error) {
            throw new \Magento\Framework\Webapi\Exception(__($error));
        }

        $imageslist = [];
        $images = $sell->getItemImages();
        if (!empty($images)) {
            foreach ($images as $file) {
                $image = (object)["data" => $file];
                $imageslist[] = $this->helper->SaveBase64images($image, 'sell/dimages/', 0);
            }
        }
        $images = $sell->getCertificateImages();
        if (!empty($images)) {
            foreach ($images as $file) {
                $image = (object)["data" => $file];
                $imageslist[] = $this->helper->SaveBase64images($image, 'sell/dimages/', 0);
                $sell->setCertificate(1);
            }
        }

        $sell->setImage(implode(',', $imageslist));

        $sell->getResource()->save($sell);

        $data = $this->sellFactory->create()->load($sell->getSellId());

        $data->setStatus("New Trade Request");

        $this->eventManager->dispatch('sell_diamond_email', ['sell' => $data]);

        return $data;
    }

    public function getList()
    {
        return null;
    }

    public function getSellById($id)
    {
        $sell = $this->sellFactory->create()->load($id);
        return $sell;
    }
}
