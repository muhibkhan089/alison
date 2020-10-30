<?php

namespace Esparksinc\Consult\Api\Data;

interface QueryInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const QUERY_ID = 'query_id';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_EMAIL = 'customer_email';
    const ERGO_ID = 'ergo_id';
    const ERGO_NAME = 'ergo_name';
    const ERGO_EMAIL = 'ergo_email';
    const QUERY = 'query';
    const CREATED_AT = 'created_at';

   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getQueryId();

   /**
    * Set EntityId.
    */
    public function setQueryId($queryId);

   /**
    * Get Title.
    *
    * @return varchar
    */
    public function getCustomerId();

   /**
    * Set Title.
    */
    public function setCustomerId($customerId);
    
    public function getCustomerName();

    public function setCustomerName($customerName);

    public function getCustomerEmail();

    public function setCustomerEmail($customerEmail);

   /**
    * Get Content.
    *
    * @return varchar
    */
    public function getErgoId();

   /**
    * Set Content.
    */
    public function setErgoId($ergoId);

    public function getErgoName();

    public function setErgoName($ergoName);

    public function getErgoEmail();

    public function setErgoEmail($ergoEmail);
   /**
    * Get CreatedAt.
    *
    * @return varchar
    */
    public function getCreatedAt();

   /**
    * Set CreatedAt.
    */
    public function setCreatedAt($createdAt);

    public function getQuery();

    public function setQuery($query);


}
