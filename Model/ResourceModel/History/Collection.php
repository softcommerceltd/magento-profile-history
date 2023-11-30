<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileHistory\Model\ResourceModel\History;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;
use SoftCommerce\Core\Model\Source\StatusInterface;
use SoftCommerce\Profile\Api\Data\ProfileInterface;
use SoftCommerce\ProfileHistory\Api\Data\HistoryInterface;
use SoftCommerce\ProfileHistory\Model\History;
use SoftCommerce\ProfileHistory\Model\ResourceModel;

/**
 * @inheritDoc
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = HistoryInterface::ENTITY_ID;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @array
     */
    private const FULLTEXT_SEARCH_FIELDS = [
        HistoryInterface::TYPE_ID,
        HistoryInterface::STATUS,
        HistoryInterface::MESSAGE
    ];

    /**
     * @param RequestInterface $request
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        RequestInterface $request,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(History::class, ResourceModel\History::class);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function addFullTextFilter(string $value)
    {
        $fields = self::FULLTEXT_SEARCH_FIELDS;
        $whereCondition = '';
        foreach ($fields as $key => $field) {
            $field = 'main_table.' . $field;
            $condition = $this->_getConditionSql(
                $this->getConnection()->quoteIdentifier($field),
                ['like' => "%$value%"]
            );
            $whereCondition .= ($key === 0 ? '' : ' OR ') . $condition;
        }
        if ($whereCondition) {
            $this->getSelect()->where($whereCondition);
        }

        return $this;
    }

    /**
     * @param $profileId
     * @return $this
     */
    public function addProfileFilter($profileId)
    {
        $this->addFieldToFilter(HistoryInterface::PARENT_ID, (int) $profileId);
        return $this;
    }

    /**
     * @return $this
     */
    public function addPendingFilter()
    {
        $this->addFieldToFilter(HistoryInterface::STATUS, ['eq' => StatusInterface::PENDING]);
        return $this;
    }

    /**
     * @param $typeId
     * @return $this
     */
    public function excludeTypeIdFromFilter($typeId)
    {
        $this->addFieldToFilter(HistoryInterface::TYPE_ID, ['neq' => $typeId]);
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        if ($profileId = $this->request->getParam(ProfileInterface::PROFILE_ID)) {
            $this->getSelect()->where(HistoryInterface::PARENT_ID . ' = ?', $profileId);
        }

        return $this;
    }
}
