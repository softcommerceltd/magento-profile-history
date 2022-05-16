<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileHistory\Model;

use Magento\Framework\DataObject\IdentityInterface;
use SoftCommerce\Core\Model\AbstractModel;
use SoftCommerce\ProfileHistory\Api\Data\HistoryInterface;
use SoftCommerce\ProfileHistory\Model\ResourceModel;

/**
 * @inheritDoc
 */
class History extends AbstractModel implements HistoryInterface, IdentityInterface
{
    const CACHE_TAG = 'softcommerce_profile_history';

    /**
     * @inheritDoc
     */
    protected $_cacheTag = 'softcommerce_profile_history';

    /**
     * @inheritDoc
     */
    protected $_eventPrefix = 'softcommerce_profile_history';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\History::class);
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritDoc
     */
    public function getEntityId(): int
    {
        return (int) $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function getParentId(): int
    {
        return (int) $this->getData(self::PARENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setParentId(int $parentId)
    {
        $this->setData(self::PARENT_ID, $parentId);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTypeId(): ?string
    {
        return $this->getData(self::TYPE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTypeId(string $typeId)
    {
        $this->setData(self::TYPE_ID, $typeId);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(string $status)
    {
        $this->setData(self::STATUS, $status);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): array
    {
        return $this->getDataSerialized(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage($message)
    {
        $this->setDataSerialized(self::MESSAGE, is_array($message) ? $message : [$message]);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $date)
    {
        $this->setData(self::CREATED_AT, $date);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(string $date)
    {
        $this->setData(self::UPDATED_AT, $date);
        return $this;
    }
}
