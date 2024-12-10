<?php

namespace Customer\Sell\Model;

use Customer\Sell\Model\ResourceModel\Sell as ResourceModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Sell extends AbstractModel implements IdentityInterface
{
    const PENDING_STATUS = 1;
    const QUALIFY_STATUS = 2;
    const NOT_QUALIFY_STATUS = 3;
    const NEW_OFFER_STATUS = 4;
    const ADMIN_OFFER_CONSIGNMENT_STATUS = 5;
    const CUSTOMER_ACCEPTED_OFFER_STATUS = 6;
    const PAID_STATUS = 7;
    const COMPLETED_STATUS = 8;
    const CLOSED_STATUS = 9;
    const CUSTOMER_NOT_ACCEPTED_OFFER_STATUS = 10;
    const CUSTOMER_PAID_FOR_SHIPPING_STATUS = 11;
    const INSPECTION_NOT_PASSED_STATUS = 12;
    const CUSTOMER_BOOKED_AN_APPOINTMENT_STATUS = 13;
    const CUSTOMER_ACCEPTED_CONSIGNMENT_STATUS = 15;
    const CUSTOMER_NOT_ACCEPTED_CONSIGMENT_STATUS = 16;
    const CUSTOMER_CANCELLED_APPOINTMENT_STATUS = 18;
    const CUSTOMER_RESCHEDULED_APPOINTMENT_STATUS = 19;
    const CUSTOMER_REQUESTED_PICK_UP_STATUS = 20;
    const CUSTOMER_PICK_UP_GENERATED_STATUS = 22;
    const CUSTOMER_ASKED_FOR_RETURN_SHIPPING_PAYMENT_LINK_STATUS = 21;

    const NEW_TRADE_REQUEST_LABEL = 'New Trade Request';
    const PENDING_LABEL = 'Pending';
    const QUALIFY_LABEL = 'Qualify';
    const NOT_QUALIFY_LABEL = 'Not Qualify';
    const NEW_OFFER_LABEL = 'New Offer';
    const ADMIN_OFFER_CONSIGNMENT_LABEL = 'Admin Offer Consignment';
    const CUSTOMER_ACCEPTED_OFFER_LABEL = 'Customer Accepted Offer';
    const PAID_LABEL = 'Paid';
    const COMPLETED_LABEL = 'Complete';
    const CLOSED_LABEL = 'Closed';
    const CUSTOMER_NOT_ACCEPTED_OFFER_LABEL = 'Customer Not Accepted Offer';
    const CUSTOMER_PAID_FOR_SHIPPING_LABEL = 'Customer Paid For Shipping';
    const INSPECTION_NOT_PASSED_LABEL = 'Inspection Not Passed';
    const CUSTOMER_BOOKED_AN_APPOINTMENT_STATUS_LABEL = 'Customer Booked an Appointment';
    const CUSTOMER_ACCEPTED_CONSIGNMENT_LABEL = 'Customer Accepted Consignment';
    const CUSTOMER_NOT_ACCEPTED_CONSIGMENT_LABEL = 'Customer Not Accepted Consignment';
    const CUSTOMER_CANCELLED_APPOINTMENT_LABEL = 'Customer cancelled appointment';
    const CUSTOMER_RESCHEDULED_APPOINTMENT_LABEL = 'Customer Rescheduled Appointment';
    const CUSTOMER_REQUESTED_PICK_UP_LABEL = 'Customer Requested Pick Up';
    const CUSTOMER_ASKED_FOR_RETURN_SHIPPING_PAYMENT_LINK_LABEL = 'Customer asked for Return shipping payment link';
    const CUSTOMER_PICK_UP_GENERATED_LABEL = "Admin Generated Pick Up Request";

    const CACHE_TAG = 'customer_sell_customer_sell';

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'customer_sell';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Return a unique id for the model.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getNextStep($status)
    {
        $nextstep = [
            self::PENDING_STATUS => "After inspection of your item(s), we will contact you and let you know whether your product is qualified or not.",
            self::QUALIFY_STATUS => "Congratulations. After inspecting your item(s) we are happy to inform you that your item(s) is qualified.",
            self::NOT_QUALIFY_STATUS => "After inspection of your product, we will contact you and let you know whether your product is qualified or not.",
            self::NEW_OFFER_STATUS => "We have sent you a new offer to purchase your old jewellery. We would like to take this opportunity to offer you an even higher offer for your old jewellery.",
            self::CUSTOMER_BOOKED_AN_APPOINTMENT_STATUS => "After inspection of your item(s), We will contact you and let you know whether your product is qualified or not.",
            self::PAID_STATUS => "Thank you for submitting your request to book an appointment for Store pickup. Kindly see your email within 1-2 days to confirm your appointment. You can contact our Diamond Specialist for more details on Whatsapp at +85266887759.",
            self::CUSTOMER_NOT_ACCEPTED_OFFER_STATUS => "We noticed that you have declined our offer to purchase your old jewellery. We would like to take this opportunity to offer you an even higher offer for your old jewellery. Kindly see your email within 2-3 days to receive new offer.",
            self::CUSTOMER_PICK_UP_GENERATED_STATUS => "We will be shipping your jewellery back to you once you pay the return shipping fees. You can contact our Diamond Specialist for more details on Whatsapp at +85266887759.",
            self::CUSTOMER_PAID_FOR_SHIPPING_STATUS => "Thank you for submiting your request to book appointment for Store pickup. Kindly see your email within 1-2 days to confirm your appointment. You can contact our Diamond Specialist for more details on Whatsapp at +85266887759.",
            self::CUSTOMER_ASKED_FOR_RETURN_SHIPPING_PAYMENT_LINK_STATUS => "After careful inspection with your trade in item(s), we have found a defect with your trade in item(s). We wil be shipping your jewellery back to you once you pay the return shipping fees. You can contact our Diamond Specialist for more details on Whatsapp at +85266887759.",
            self::ADMIN_OFFER_CONSIGNMENT_STATUS => "After inspecting your item(s), we will contact you and let you know whether your item(s) is qualified.",
            self::CUSTOMER_REQUESTED_PICK_UP_STATUS => "Thanks, we have received your pickup request and will inform you through Email once it is scheduled.",
            self::CUSTOMER_ACCEPTED_OFFER_STATUS => self::CUSTOMER_ACCEPTED_OFFER_LABEL,
            self::COMPLETED_STATUS => "Thank you, Your Online Trade submission has been received. Kindly see your email for completing the shipping process. We will get back to you via email : contact@diamondicq.com.",
            self::CLOSED_STATUS => "Need to add Next Step",
            self::INSPECTION_NOT_PASSED_STATUS => "After careful inspection with your trade in product, we have found a defect with your trade in item(s). We wil be shipping your jewellery back to you once you pay the return shipping fees. You can contact our Diamond Specialist for more details on Whatsapp at +85266887759.",
            self::CUSTOMER_ACCEPTED_CONSIGNMENT_STATUS => "Thanks for Accepting Consignment offer, Our team will contact you.",
            self::CUSTOMER_NOT_ACCEPTED_CONSIGMENT_STATUS => "Not Happy with our Consignment Offer, Please feel free to connect with our Diamond Specialist.",
            self::CUSTOMER_CANCELLED_APPOINTMENT_STATUS => "After inspection of your item(s), We will contact you and let you know whether your product is qualified or not.",
            self::CUSTOMER_RESCHEDULED_APPOINTMENT_STATUS => "After inspection of your item(s), We will contact you and let you know whether your product is qualified or not.",
        ];

        return isset($nextstep[$status]) ? $nextstep[$status] : "";
    }

    public function getStatusOptions()
    {
        return [
            self::PENDING_STATUS => self::PENDING_LABEL,
            self::QUALIFY_STATUS => self::QUALIFY_LABEL,
            self::NOT_QUALIFY_STATUS => self::NOT_QUALIFY_LABEL,
            self::NEW_OFFER_STATUS => self::NEW_OFFER_LABEL,
            self::ADMIN_OFFER_CONSIGNMENT_STATUS => self::ADMIN_OFFER_CONSIGNMENT_LABEL,
            self::CUSTOMER_ACCEPTED_OFFER_STATUS => self::CUSTOMER_ACCEPTED_OFFER_LABEL,
            self::PAID_STATUS => self::PAID_LABEL,
            self::COMPLETED_STATUS => self::COMPLETED_LABEL,
            self::CLOSED_STATUS => self::CLOSED_LABEL,
            self::CUSTOMER_NOT_ACCEPTED_OFFER_STATUS => self::CUSTOMER_NOT_ACCEPTED_OFFER_LABEL,
            self::CUSTOMER_PAID_FOR_SHIPPING_STATUS => self::CUSTOMER_PAID_FOR_SHIPPING_LABEL,
            self::INSPECTION_NOT_PASSED_STATUS => self::INSPECTION_NOT_PASSED_LABEL,
            self::CUSTOMER_BOOKED_AN_APPOINTMENT_STATUS => self::CUSTOMER_BOOKED_AN_APPOINTMENT_STATUS_LABEL,
            self::CUSTOMER_ACCEPTED_CONSIGNMENT_STATUS => self::CUSTOMER_ACCEPTED_CONSIGNMENT_LABEL,
            self::CUSTOMER_NOT_ACCEPTED_CONSIGMENT_STATUS => self::CUSTOMER_NOT_ACCEPTED_CONSIGMENT_LABEL,
            self::CUSTOMER_CANCELLED_APPOINTMENT_STATUS => self::CUSTOMER_CANCELLED_APPOINTMENT_LABEL,
            self::CUSTOMER_RESCHEDULED_APPOINTMENT_STATUS => self::CUSTOMER_RESCHEDULED_APPOINTMENT_LABEL,
            self::CUSTOMER_REQUESTED_PICK_UP_STATUS => self::CUSTOMER_REQUESTED_PICK_UP_LABEL,
            self::CUSTOMER_ASKED_FOR_RETURN_SHIPPING_PAYMENT_LINK_STATUS => self::CUSTOMER_ASKED_FOR_RETURN_SHIPPING_PAYMENT_LINK_LABEL,
            self::CUSTOMER_PICK_UP_GENERATED_STATUS => self::CUSTOMER_PICK_UP_GENERATED_LABEL,
        ];
    }
}
