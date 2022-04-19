<?php
/*
 * Custom Extend Module to change the SKU of the warranty product before the order is placed
 * the SKU was previously WARRANTY-1, now we build a custom sku based on the API response.
 * in this example, EXTEND-<WARRANTY_ID_FROM_API>
 *
 * */

namespace Extend\CustomWarrantySku\Observer\Frontend\Sales;

class OrderPlaceBefore implements \Magento\Framework\Event\ObserverInterface
{
    protected \Magento\Catalog\Model\ProductRepository $_productRepository;
    private \Magento\Framework\Data\Form\FormKey $formKey;

    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Data\Form\FormKey $formKey
    ){
        $this->_productRepository = $productRepository;
        $this->formKey = $formKey;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $order = $observer->getEvent()->getOrder();
        $items = $order->getItems();
        foreach($items as $item){
            if ($item->getSku() == 'WARRANTY-1'){
                $productOptions = $item->getProductOptions();
                $warranty_id = $productOptions['warranty_id'];
                $warranty_cost = $productOptions['info_buyRequest']['price'];
                $item->setSku('EXTEND-'.$warranty_id.'-'.$warranty_cost);
                $order->save();
            }
        }
    }
}
