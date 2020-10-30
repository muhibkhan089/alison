<?php
namespace Magenest\Moneris\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class AmountDataBuilder
 *
 * @package Magenest\Moneris\Gateway\Request
 */
class AmountDataBuilder extends AbstractDataBuilder implements BuilderInterface
{

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        return [
            self::REPLACE_KEY => [
                self::AMOUNT => sprintf('%.2F', SubjectReader::readAmount($buildSubject))
            ]
        ];
    }
}
