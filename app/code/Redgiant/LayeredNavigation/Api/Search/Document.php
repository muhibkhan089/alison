<?php
/**
 */

namespace Redgiant\LayeredNavigation\Api\Search;

use Magento\Framework\Api\Search\Document as SourceDocument;

/**
 * Class Document
 * @package Redgiant\LayeredNavigation\Api\Search
 */
class Document extends SourceDocument
{
    /**
     * Get Document field
     *
     * @param string $fieldName
     * @return \Magento\Framework\Api\AttributeInterface
     */
    public function getField($fieldName)
    {
        return $this->getCustomAttribute($fieldName);
    }
}
