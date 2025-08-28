<?php

namespace NeoUnicorn\DigitalGold\Controller\Adminhtml\Sell;

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
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('NeoUnicorn_DigitalGold::digitalgold'); // ✅ Matches menu.xml
        $resultPage->getConfig()->getTitle()->prepend(__('Digital Gold Sell Records'));

        return $resultPage;
    }
}

