<?php

namespace Iovista\Blog\Model\ResourceModel\Post\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    /**
     * Aggregations
     * 
     * @var \Magento\Framework\Search\AggregationInterface
     */
    protected $_aggregations;


    /**
     * @return \Magento\Framework\Search\AggregationInterface
     */
    public function getAggregations()
    {
        return $this->_aggregations;
    }

    /**
     * @param \Magento\Framework\Search\AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->_aggregations = $aggregations;
    }


    /**
     * Retrieve all ids for collection
     * Backward compatibility with EAV collection
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * Get items
     *
     * @return \Magento\Framework\Api\ExtensibleDataInterface[]
     */
    public function getItems()
    {
        $items = parent::getItems();
        $result = [];
        foreach ($items as $item) {
            // Ensure getCustomAttributes returns an array, not null
            if ($item instanceof \Magento\Framework\View\Element\UiComponent\DataProvider\Document) {
                // Check if getCustomAttributes returns null or empty
                $customAttributes = $item->getCustomAttributes();
                if ($customAttributes === null || !is_array($customAttributes)) {
                    // Re-initialize the document with data to ensure getCustomAttributes works
                    $data = $item->getData();
                    $document = $this->_entityFactory->create(\Magento\Framework\View\Element\UiComponent\DataProvider\Document::class);
                    $document->setData($data);
                    $result[] = $document;
                } else {
                    $result[] = $item;
                }
            } else {
                // Convert to Document if not already
                $document = $this->_entityFactory->create(\Magento\Framework\View\Element\UiComponent\DataProvider\Document::class);
                $document->setData($item->getData());
                $result[] = $document;
            }
        }
        return $result;
    }

}