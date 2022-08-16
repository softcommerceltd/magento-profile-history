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
use Magento\Framework\Controller\ResultInterface;
use SoftCommerce\ProfileHistory\Api\HistoryRepositoryInterface;

/**
 * @inheritDoc
 */
class Delete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'SoftCommerce_ProfileHistory::manage';

    /**
     * @var HistoryRepositoryInterface
     */
    private $repository;

    /**
     * @param HistoryRepositoryInterface $repository
     * @param Context $context
     */
    public function __construct(
        HistoryRepositoryInterface $repository,
        Context $context
    ) {
        $this->repository = $repository;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->canExecute()) {
            $this->messageManager->addErrorMessage(__('Could not process given request.'));
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        if (!$id = $this->getRequest()->getParam('id')) {
            $this->messageManager->addErrorMessage(__('Could not retrieve history ID from request data.'));
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        try {
            $this->repository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('The history with ID %1 has been deleted.', $id));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath($this->_redirect->getRefererUrl());
    }

    /**
     * @return bool
     */
    private function canExecute(): bool
    {
        return $this->_formKeyValidator->validate($this->getRequest()) && $this->getRequest()->isPost();
    }
}
