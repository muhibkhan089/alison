<?php
namespace Magenest\Moneris\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class CompAmountDataBuilder
 *
 * @package Magenest\Moneris\Gateway\Request
 */
class CompAmountDataBuilder extends AbstractDataBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        return [
            self::REPLACE_KEY => [
                self::COMP_AMOUNT => sprintf('%.2F', SubjectReader::readAmount($buildSubject))
            ]
        ];
    }
}
