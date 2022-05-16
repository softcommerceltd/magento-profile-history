<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileHistory\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use SoftCommerce\ProfileHistory\Api\Data\HistoryInterface;
use SoftCommerce\ProfileHistory\Api\Data\HistorySearchResultsInterface;

/**
 * Interface HistoryRepositoryInterface
 * used to provide profile history data.
 */
interface HistoryRepositoryInterface
{
    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return HistorySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $entityId
     * @param string|null $field
     * @return HistoryInterface
     * @throws NoSuchEntityException
     */
    public function get($entityId, ?string $field = null);

    /**
     * @param HistoryInterface $history
     * @return HistoryInterface
     * @throws CouldNotSaveException
     */
    public function save(HistoryInterface $history);

    /**
     * @param HistoryInterface $history
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(HistoryInterface $history);

    /**
     * @param $entityId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($entityId);
}
