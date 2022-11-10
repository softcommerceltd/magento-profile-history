<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileHistory\Controller\Adminhtml\ProfileHistory;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use SoftCommerce\ProfileHistory\Api\Data\HistoryInterface;
use SoftCommerce\ProfileHistory\Model\ResourceModel;

/**
 * @inheritDoc
 */
class MassDelete extends Action implements HttpPostActionInterface
{
    /**
     * @var ResourceModel\History\CollectionFactory
     */
    private ResourceModel\History\CollectionFactory $collectionFactory;

    /**
     * @var Filter
     */
    private Filter $filter;

    /**
     * @var ResourceModel\History
     */
    private ResourceModel\History $resource;

    /**
     * @param ResourceModel\History $resource
     * @param ResourceModel\History\CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param Context $context
     */
    public function __construct(
        ResourceModel\History $resource,
        ResourceModel\History\CollectionFactory $collectionFactory,
        Filter $filter,
        Context $context
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (!$this->canExecute()) {
            $this->messageManager->addErrorMessage(__('Could not process given request.'));
            return $resultRedirect->setPath($this->getComponentRefererUrl());
        }

        try {
            $this->processDelete();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    private function processDelete(): void
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        if (!$ids = $collection->getAllIds()) {
            throw new LocalizedException(
                __('Could not retrieve history ID(s) from request data.')
            );
        }

        $result = $this->resource->remove(
            [
                HistoryInterface::ENTITY_ID . ' IN (?)' => $ids
            ]
        );

        if ($result > 0) {
            $this->messageManager->addSuccessMessage(
                __(
                    'Selected histories have been deleted. Effected IDs: %1',
                    implode(', ', $ids)
                )
            );
        }
    }

    /**
     * Return component referer url
     *
     * @return string
     */
    private function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl() ?: $this->_redirect->getRefererUrl();
    }

    /**
     * @return bool
     */
    private function canExecute(): bool
    {
        return $this->_formKeyValidator->validate($this->getRequest()) && $this->getRequest()->isPost();
    }
}
