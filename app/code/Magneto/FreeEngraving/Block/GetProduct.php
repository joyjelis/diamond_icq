<?php
namespace Magneto\FreeEngraving\Block;

class GetProduct extends \Magento\Framework\View\Element\Template
{

    
    protected $_productRepository;
        
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        array $data = []
    ) {
        $this->_productRepository = $productRepository;
        $this->attributeSet = $attributeSet;
        parent::__construct($context, $data);
    }
    
    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }

    //Build method to get attribute set
    public function getAttributeSetName($product)
    {
        $attributeSetRepository = $this->attributeSet->get($product->getAttributeSetId());
        return $attributeSetRepository->getAttributeSetName();
    }
}
