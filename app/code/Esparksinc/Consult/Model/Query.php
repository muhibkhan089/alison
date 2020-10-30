<?php

namespace Esparksinc\Consult\Model;

use Esparksinc\Consult\Api\Data\QueryInterface;

class Query extends \Magento\Framework\Model\AbstractModel implements QueryInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'esparksinc_consult_form';

    /**
     * @var string
     */
    protected $_cacheTag = 'esparksinc_consult_form';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'esparksinc_consult_form';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Esparksinc\Consult\Model\ResourceModel\Query');
    }
    /**
     * Get EntityId.
     *
     * @return int
     */
    public function getQueryId()
    {
        return $this->getData(self::QUERY_ID);
    }

    /**
     * Set EntityId.
     */
    public function setQueryId($queryId)
    {
        return $this->setData(self::QUERY_ID, $queryId);
    }

    /**
     * Get Title.
     *
     * @return varchar
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set Title.
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function getCustomerName()
    {
        return $this->getData(self::CUSTOMER_NAME);
    }


    public function setCustomerName($customerName)
    {
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    public function getCustomerEmail()
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * Get getContent.
     *
     * @return varchar
     */
    public function getErgoId()
    {
        return $this->getData(self::ERGO_ID);
    }

    /**
     * Set Content.
     */
    public function setErgoId($ergoId)
    {
        return $this->setData(self::ERGO_ID, $ergoId);
    }

    public function getErgoName()
    {
        return $this->getData(self::ERGO_NAME);
    }


    public function setErgoName($ergoName)
    {
        return $this->setData(self::ERGO_NAME, $ergoName);
    }

    public function getErgoEmail()
    {
        return $this->getData(self::ERGO_EMAIL);
    }

    public function setErgoEmail($ergoEmail)
    {
        return $this->setData(self::ERGO_EMAIL, $ergoEmail);
    }
    /**
     * Get CreatedAt.
     *
     * @return varchar
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set CreatedAt.
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getQuery()
    {
        return $this->getData(self::QUERY);
    }

    public function setQuery($query)
    {
        return $this->setData(self::QUERY, $query);
    }
}
