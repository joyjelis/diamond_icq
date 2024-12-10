<?php
namespace Magneto\CartAttributes\Plugin\Checkout\Model;

use Magento\Checkout\Model\Session as CheckoutSession;

class DefaultConfigProvider
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * Constructor
     *
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        CheckoutSession $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        array $result
    ) {
        // $items = $result['totalsData']['items'];
        /*foreach ($items as $index => $item) {
            $quoteItem = $this->checkoutSession->getQuote()->getItemById($item['item_id']);
            $result['quoteItemData'][$index]['engraving_font'] = __('Engraving Font');
            $result['quoteItemData'][$index]['engraving_text'] = __('Engraving Text');
        }*/
        // $result['quoteItemData']['engraving_font1'] = __('Engraving Font');
        // $result['quoteItemData']['engraving_text1'] = __('Engraving Text');
        return $result;
    }
}