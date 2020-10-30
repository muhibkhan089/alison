<?php
namespace Magenest\Moneris\Gateway\Http;

/**
 * Class TransferFactory
 */
class TransferFactory extends AbstractTransferFactory
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
