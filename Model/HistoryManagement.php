<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileHistory\Model;

use Magento\Framework\Serialize\SerializerInterface;
use SoftCommerce\Core\Model\Source\StatusInterface;
use SoftCommerce\ProfileHistory\Api\Data\HistoryInterface;
use SoftCommerce\ProfileHistory\Api\HistoryManagementInterface;

/**
 * @inhertidoc
 */
class HistoryManagement implements HistoryManagementInterface
{
    /**
     * @var int|null
     */
    private ?int $lastInsertId = null;

    /**
     * @var ResourceModel\History
     */
    private ResourceModel\History $resource;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param ResourceModel\History $resource
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ResourceModel\History $resource,
        SerializerInterface $serializer
    ) {
        $this->resource = $resource;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function getLastCreatedHistoryId(): int
    {
        return (int) $this->lastInsertId;
    }

    /**
     * @inheritDoc
     */
    public function create(
        int $profileId,
        string $typeId,
        string $status = StatusInterface::COMPLETE,
        array $message = []
    ): int {
        try {
            $message = $this->serializer->serialize(is_array($message) ? $message : [$message]);
        } catch (\InvalidArgumentException $e) {
            $message = __('Could not serialize message. Error: %1', $e->getMessage());
        }

        $requestData = [
            HistoryInterface::PARENT_ID => $profileId,
            HistoryInterface::TYPE_ID => $typeId,
            HistoryInterface::STATUS => $status,
            HistoryInterface::MESSAGE => $message
        ];

        $this->resource->insert($requestData);

        $this->lastInsertId = (int) $this->resource->getConnection()->lastInsertId(
            HistoryInterface::DB_TABLE_NAME,
            HistoryInterface::ENTITY_ID
        );

        return $this->lastInsertId;
    }

    /**
     * @inheritDoc
     */
    public function update(array $data, ?int $entityId = null): void
    {
        $this->resource->update(
            $data,
            [HistoryInterface::ENTITY_ID . ' = ?' => $entityId ?: $this->getLastCreatedHistoryId()]
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(?int $entityId = null): void
    {
        $this->resource->remove(
            [HistoryInterface::ENTITY_ID . ' = ?' => $entityId ?: $this->getLastCreatedHistoryId()]
        );
    }
}
