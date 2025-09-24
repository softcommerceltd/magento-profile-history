<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileHistory\Model\Profile\PostProcessor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use SoftCommerce\Core\Framework\DataStorageInterfaceFactory;
use SoftCommerce\Core\Framework\MessageCollectorInterfaceFactory;
use SoftCommerce\Core\Framework\MessageStorage\StatusPredictionInterface;
use SoftCommerce\Core\Framework\MessageStorageInterfaceFactory;
use SoftCommerce\Profile\Model\ServiceAbstract\ProcessorInterface;
use SoftCommerce\Profile\Model\ServiceAbstract\Service;
use SoftCommerce\ProfileHistory\Api\HistoryManagementInterface;
use SoftCommerce\ProfileSchedule\Model\Config\ScheduleConfigInterface;
use SoftCommerce\ProfileSchedule\Model\Config\ScheduleConfigInterfaceFactory;
/**
 * @inheritDoc
 */
class History extends Service implements ProcessorInterface
{
    private const BATCH_LIMIT = 50;

    /**
     * @var ScheduleConfigInterface|null
     */
    private ?ScheduleConfigInterface $scheduleConfig = null;

    /**
     * @param HistoryManagementInterface $historyManagement
     * @param ScheduleConfigInterfaceFactory $scheduleConfigFactory
     * @param StatusPredictionInterface $statusPrediction
     * @param DataStorageInterfaceFactory $dataStorageFactory
     * @param MessageCollectorInterfaceFactory $messageCollectorFactory
     * @param MessageStorageInterfaceFactory $messageStorageFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        private HistoryManagementInterface $historyManagement,
        private ScheduleConfigInterfaceFactory $scheduleConfigFactory,
        private StatusPredictionInterface $statusPrediction,
        DataStorageInterfaceFactory $dataStorageFactory,
        MessageCollectorInterfaceFactory $messageCollectorFactory,
        MessageStorageInterfaceFactory $messageStorageFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        parent::__construct(
            $dataStorageFactory,
            $messageCollectorFactory,
            $messageStorageFactory,
            $searchCriteriaBuilder,
            $data
        );
    }

    /**
     * @inheritDoc
     */
    public function execute(): void
    {
        if (!$this->getConfig()->isActiveHistory()) {
            return;
        }

        foreach (array_chunk($this->getContext()->getMessageStorage()->getData(), self::BATCH_LIMIT) as $batch) {
            $this->historyManagement->create(
                $this->getContext()->getProfileId(),
                $this->getContext()->getTypeId(),
                $this->statusPrediction->execute($batch),
                $batch
            );
        }
    }

    /**
     * @return ScheduleConfigInterface
     * @throws LocalizedException
     */
    private function getConfig(): ScheduleConfigInterface
    {
        if (null === $this->scheduleConfig) {
            $this->scheduleConfig = $this->scheduleConfigFactory->create(
                ['profileId' => $this->getContext()->getProfileId()]
            );
        }
        return $this->scheduleConfig;
    }
}
