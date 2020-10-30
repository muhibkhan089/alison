<?php
namespace Magenest\Moneris\Gateway\Http;

/**
 * Class RefundTransferFactory
 * @package Magenest\Moneris\Gateway\Http
 */
class RefundTransferFactory extends AbstractTransferFactory
{
    /**
     * @inheritdoc
     */
    public function create(array $request)
    {
        return $this->transferBuilder
            ->setMethod('POST')
            ->setBody($this->convertToXml($request))
            ->setUri($this->getUrl())
            ->build();
    }
}
