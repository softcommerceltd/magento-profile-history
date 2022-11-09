<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileHistory\Cron\Backend;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use SoftCommerce\ProfileHistory\Api\Data\HistoryInterface;

/**
 * Class HistoryCleanup used to
 * clean-up profile history.
 */
class HistoryCleanup
{
    private const HISTORY_LIFETIME = 1209600;
    private const SECONDS_IN_DAY = 86400;
    private const XML_PATH_HISTORY_LIFETIME = 'softcommerce_profile/profile_config/history_lifetime';

    /**
     * @var AdapterInterface
     */
    private AdapterInterface $connection;

    /**
     * @var DateTime
     */
    private DateTime $dateTime;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param DateTime $dateTime
     * @param ResourceConnection $resource
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        DateTime $dateTime,
        ResourceConnection $resource,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->dateTime = $dateTime;
        $this->connection = $resource->getConnection();
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $this->connection->delete(
            $this->connection->getTableName(HistoryInterface::DB_TABLE_NAME),
            [
                HistoryInterface::CREATED_AT . ' < ?' => $this->connection->formatDate(
                    $this->dateTime->gmtTimestamp() - $this->getHistoryLifetime()
                )
            ]
        );
    }

    /**
     * @return int
     */
    private function getHistoryLifetime(): int
    {
        if ($historyLifetime = $this->scopeConfig->getValue(self::XML_PATH_HISTORY_LIFETIME)) {
            $historyLifetime = $historyLifetime * self::SECONDS_IN_DAY;
        } else {
            $historyLifetime = self::HISTORY_LIFETIME;
        }

        return $historyLifetime;
    }
}
