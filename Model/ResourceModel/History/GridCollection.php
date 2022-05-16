<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileHistory\Model\ResourceModel\History;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use SoftCommerce\Profile\Api\Data\ProfileInterface;
use SoftCommerce\ProfileHistory\Api\Data\HistoryInterface;
use SoftCommerce\ProfileHistory\Model\ResourceModel;
use Psr\Log\LoggerInterface;

/**
 * @inheritDoc
 */
class GridCollection extends SearchResult
{
    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param string $mainTable
     * @param string|null $resourceModel
     * @throws LocalizedException
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        string $mainTable = HistoryInterface::DB_TABLE_NAME,
        ?string $resourceModel = ResourceModel\History::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @inheritDoc
     */
    protected $_map = [
        'fields' => [
            HistoryInterface::ENTITY_ID => 'main_table.' . HistoryInterface::ENTITY_ID,
            HistoryInterface::PARENT_ID => 'main_table.' . HistoryInterface::PARENT_ID,
            ProfileInterface::TYPE_ID => 'spe_tb.' . ProfileInterface::TYPE_ID,
        ]
    ];

    /**
     * @inheritDoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['spe_tb' => $this->getTable(ProfileInterface::DB_TABLE_NAME)],
            'spe_tb.' . ProfileInterface::ENTITY_ID . ' = main_table.' . HistoryInterface::PARENT_ID,
            ProfileInterface::TYPE_ID
        );

        return $this;
    }
}
