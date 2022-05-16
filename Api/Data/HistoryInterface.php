<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileHistory\Api\Data;

/**
 * Interface HistoryInterface used
 * to manage profile history data.
 */
interface HistoryInterface
{
    public const DB_TABLE_NAME = 'softcommerce_profile_history';

    public const ENTITY_ID = 'entity_id';
    public const PARENT_ID = 'parent_id';
    public const TYPE_ID = 'type_id';
    public const STATUS = 'status';
    public const MESSAGE = 'message';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * @return int
     */
    public function getEntityId(): int;

    /**
     * @return int
     */
    public function getParentId(): int;

    /**
     * @param int $parentId
     * @return $this
     */
    public function setParentId(int $parentId);

    /**
     * @return string|null
     */
    public function getTypeId(): ?string;

    /**
     * @param string $typeId
     * @return $this
     */
    public function setTypeId(string $typeId);

    /**
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status);

    /**
     * @return array
     */
    public function getMessage(): array;

    /**
     * @param array|string|mixed $message
     * @return $this
     */
    public function setMessage($message);

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string $date
     * @return $this
     */
    public function setCreatedAt(string $date);

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * @param string $date
     * @return $this
     */
    public function setUpdatedAt(string $date);
}
