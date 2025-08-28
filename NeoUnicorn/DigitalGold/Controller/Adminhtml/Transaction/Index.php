<?php
namespace NeoUnicorn\DigitalGold\Controller\Adminhtml\Transaction;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $customerId = $this->getRequest()->getParam('customer_id');
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('NeoUnicorn_DigitalGold::digitalgold');
        $resultPage->getConfig()->getTitle()->prepend(
            __('Transactions for Customer ID: %1', $customerId)
        );
        
        return $resultPage;
    }
}