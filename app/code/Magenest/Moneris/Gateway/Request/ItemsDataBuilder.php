<?php
namespace Magenest\Moneris\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class ItemsDataBuilder
 * @package Magenest\Moneris\Gateway\Request
 */
class ItemsDataBuilder extends AbstractDataBuilder implements BuilderInterface
{
    const ITEMS = 'items';
    const SKU = 'product_code';
    const QUANTITY = 'quantity';
    const UNIT_COST = 'extended_amount';
    const NAME = 'name';

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);

        $order = $paymentDO->getOrder();

        return [
            self::REPLACE_KEY => [
                CustomerDataBuilder::CUSTOMER => [
                    self::ITEMS => $this->prepareItems($order->getItems())
                ]
            ]
        ];
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface[]|null $items
     * @return array
     */
    private function prepareItems($items)
    {
        $result = [];

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($items as $item) {
            $result[] = [
                self::SKU => $item->getSku(),
                self::QUANTITY => number_format($item->getQtyOrdered(), 0),
                self::UNIT_COST => number_format($item->getBasePrice(), 2),
                self::NAME => $item->getName()
            ];
        }

        return $result;
    }
}
