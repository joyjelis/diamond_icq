<?php 
namespace Gemfind\Ringbuilder\Model\Config\Source;

class Defaultview implements \Magento\Framework\Option\ArrayInterface
{
 public function toOptionArray()
 {
  return [
    ['value' => 'list', 'label' => __('List View')],
    ['value' => 'grid', 'label' => __('Grid View')],
  ];
 }
}
?>