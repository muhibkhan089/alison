<?php
namespace Magenest\Moneris\Gateway\Validator;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class TransactionCaptureValidator
 * @package Magenest\Moneris\Gateway\Validator
 */
class TransactionCaptureValidator extends AbstractResponseValidator
{
    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $response = SubjectReader::readResponse($validationSubject);
        $amount = SubjectReader::readAmount($validationSubject);

        $errorMessages = [];
        $validationResult = $this->validateErrors($response)
            && $this->validateTotalAmount($response, $amount)
            && $this->validateTransactionId($response)
            && $this->validateResponseCode($response)
            && $this->validateResponseMessage($response);

        if (!$validationResult && $this->validateResponseMessage($response)) {
            throw new LocalizedException(__($response[AbstractResponseValidator::RESPONSE_MESSAGE]));
        }

        return $this->createResult($validationResult, $errorMessages);
    }
}
