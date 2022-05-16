<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileHistory\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use SoftCommerce\ProfileHistory\Api\Data\HistoryInterface;
use SoftCommerce\ProfileHistory\Api\Data\HistorySearchResultsInterface;
use SoftCommerce\ProfileHistory\Api\Data\HistorySearchResultsInterfaceFactory;
use SoftCommerce\ProfileHistory\Api\HistoryRepositoryInterface;
use SoftCommerce\ProfileHistory\Model\ResourceModel;

/**
 * @inheritDoc
 */
class HistoryRepository implements HistoryRepositoryInterface
{
    /**
     * @var ResourceModel\History
     */
    private $resource;

    /**
     * @var HistoryFactory
     */
    private $historyFactory;

    /**
     * @var ResourceModel\History\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var HistorySearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ResourceModel\History $resource
     * @param ResourceModel\History\CollectionFactory $collectionFactory
     * @param HistoryFactory $historyFactory
     * @param HistorySearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        CollectionProcessorInterface $collectionProcessor,
        ResourceModel\History $resource,
        ResourceModel\History\CollectionFactory $collectionFactory,
        HistoryFactory $historyFactory,
        HistorySearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->historyFactory = $historyFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var ResourceModel\History\Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var HistorySearchResultsInterface $searchResults */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * @inheritDoc
     */
    public function get($entityId, ?string $field = null)
    {
        /** @var HistoryInterface|History $history */
        $history = $this->historyFactory->create();
        $this->resource->load($history, $entityId, $field);
        if (!$history->getId()) {
            throw new NoSuchEntityException(__('The history with ID "%1" doesn\'t exist.', $entityId));
        }

        return $history;
    }

    /**
     * @inheritDoc
     */
    public function save(HistoryInterface $history)
    {
        try {
            $this->resource->save($history);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $history;
    }

    /**
     * @inheritDoc
     */
    public function delete(HistoryInterface $history)
    {
        try {
            $this->resource->delete($history);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($entityId)
    {
        return $this->delete($this->get($entityId));
    }
}
