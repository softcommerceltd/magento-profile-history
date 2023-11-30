<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileHistory\Api;

use Magento\Framework\Exception\LocalizedException;
use SoftCommerce\Core\Model\Source\StatusInterface;

/**
 * Interface HistoryManagementInterface
 * used to manage profile history.
 */
interface HistoryManagementInterface
{
    /**
     * @return int
     */
    public function getLastCreatedHistoryId(): int;

    /**
     * @param int $profileId
     * @param string $typeId
     * @param string $status
     * @param array|string $message
     * @return int
     * @throws LocalizedException
     */
    public function create(
        int $profileId,
        string $typeId,
        string $status = StatusInterface::COMPLETE,
        array $message = []
    ): int;

    /**
     * @param array $data
     * @param int|null $entityId
     * @return void
     * @throws LocalizedException
     */
    public function update(array $data, ?int $entityId = null): void;

    /**
     * @param int|null $entityId
     * @return void
     * @throws LocalizedException
     */
    public function delete(?int $entityId = null): void;
}
