<?php
/**
 * Copyright © Byte8 Ltd (formerly Soft Commerce). All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace SoftCommerce\ProfileHistory\Controller\Adminhtml\ProfileHistory;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * @inheritDoc
 */
class Index extends Action
{
    /**
     * @inheritDoc
     */
    public const ADMIN_RESOURCE = 'SoftCommerce_ProfileHistory::manage';

    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Action\Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('grid');
            return $resultForward;
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SoftCommerce_ProfileHistory::profile_history');
        $resultPage->getConfig()->getTitle()->prepend(__('Profile History'));
        $resultPage->addBreadcrumb(__('Profile History'), __('Profile History'));

        return $resultPage;
    }
}
