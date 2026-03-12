<?php
/**
 * Copyright © Byte8 Ltd (formerly Soft Commerce). All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileHistory\Model\Profile\PostProcessor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use SoftCommerce\Core\Framework\DataStorageInterfaceFactory;
use SoftCommerce\Core\Framework\MessageCollectorInterfaceFactory;
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
     * @param DataStorageInterfaceFactory $dataStorageFactory
     * @param MessageCollectorInterfaceFactory $messageCollectorFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        private readonly HistoryManagementInterface $historyManagement,
        private readonly ScheduleConfigInterfaceFactory $scheduleConfigFactory,
        DataStorageInterfaceFactory $dataStorageFactory,
        MessageCollectorInterfaceFactory $messageCollectorFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        parent::__construct(
            $dataStorageFactory,
            $messageCollectorFactory,
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

        $messageCollector = $this->getContext()->getMessageCollector();
        $messages = $messageCollector->getMessages();
        $overallStatus = $messageCollector->getOverallStatus();

        foreach (array_chunk($messages, self::BATCH_LIMIT) as $batch) {
            $this->historyManagement->create(
                $this->getContext()->getProfileId(),
                $this->getContext()->getTypeId(),
                $overallStatus,
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
