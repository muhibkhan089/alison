<?php
namespace Magenest\Moneris\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magenest\Moneris\Model\Adminhtml\Source\Environment;
use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class AbstractDataBuilder
 * @package Magenest\Moneris\Gateway\Request
 */
abstract class AbstractDataBuilder implements BuilderInterface
{
    /**@#+
     * List Transaction Type
     * @const
     */
    const PRE_AUTH_CAPTURE = 'completion';

    /**
     * Capture
     */
    const PURCHASE = 'purchase';

    /**
     * Transaction type: Void
     */
    const VOID = 'purchasecorrection';

    /**
     * Transaction type: Authorize
     */
    const AUTHORIZE = 'preauth';

    /**
     * Transaction Type: Refund
     */
    const REFUND = 'refund';

    /**
     * Need Replace it in TransferFactory
     */
    const REPLACE_KEY = 'replace_key';

    /**
     * Amount
     */
    const AMOUNT = 'amount';

    /**
     * Comp Amount
     */
    const COMP_AMOUNT = 'comp_amount';

    /**
     * Moneris CC Vault
     */
    const CC_VAULT_CODE = 'moneris_cc_vault';

    /**
     * Vault Capture
     */
    const VAULT_CAPTURE = 'res_purchase_cc';

    /**
     * Vault Authorize
     */
    const VAULT_AUTHORIZE = 'res_preauth_cc';

    /**
     * Get Key
     */
    const GET_KEY = 'get_key';
}
