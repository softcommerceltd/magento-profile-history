<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileHistory\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use SoftCommerce\Core\Model\ResourceModel\AbstractResource;
use SoftCommerce\Core\Model\Source\StatusInterface;
use SoftCommerce\ProfileHistory\Api\Data\HistoryInterface;

/**
 * @inheritDoc
 */
class History extends AbstractResource
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(HistoryInterface::DB_TABLE_NAME, HistoryInterface::ENTITY_ID);
    }

    /**
     * @param string|int $profileId
     * @param string|array $actionCode
     * @param string $cols
     * @return array
     * @throws LocalizedException
     */
    public function getByActionCode($profileId, $actionCode, $cols = '*')
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), $cols)
            ->where('profile_id = ?', $profileId)
            ->where('action_code in (?)', is_array($actionCode) ? $actionCode : [$actionCode]);

        return $adapter->fetchAll($select);
    }

    /**
     * @param $profileId
     * @param $actionCode
     * @return string
     * @throws LocalizedException
     */
    public function getIsHistoryExist($profileId, $actionCode)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), HistoryInterface::ENTITY_ID)
            ->where(HistoryInterface::PROFILE_ID . ' = ?', $profileId)
            ->where(HistoryInterface::ACTION_CODE . ' in (?)', is_array($actionCode) ? $actionCode : [$actionCode])
            ->where(HistoryInterface::STATUS. ' in (?)', [StatusInterface::SUCCESS, StatusInterface::COMPLETE])
            ->limit(1);

        return $adapter->fetchOne($select);
    }

    /**
     * @param array $data
     * @throws LocalizedException
     */
    public function addRecord(array $data)
    {
        $this->getConnection()
            ->insertMultiple($this->getMainTable(), $data);
    }

    /**
     * @param null $profileId
     * @return string
     * @throws LocalizedException
     */
    public function getLastProcessedAt($profileId = null)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), [HistoryInterface::PROCESSED_AT])
            ->where(HistoryInterface::STATUS. ' != ?', StatusInterface::PROCESSING);

        if (null !== $profileId) {
            $select->where(HistoryInterface::PROFILE_ID.' = ?', $profileId);
        }

        $select->order(HistoryInterface::PROCESSED_AT.' '.Select::SQL_DESC);
        return $adapter->fetchOne($select);
    }

    /**
     * @param $table
     * @return $this
     */
    public function truncate($table)
    {
        if ($this->getConnection()->getTransactionLevel() > 0) {
            $this->getConnection()->delete($table);
        } else {
            $this->getConnection()->truncateTable($table);
        }
        return $this;
    }
}
