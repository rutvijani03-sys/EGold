<?php

namespace NeoUnicorn\DigitalGold\Controller\Adminhtml\Request;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\RedirectFactory;
use NeoUnicorn\DigitalGold\Model\SellGoldRequestFactory;
use NeoUnicorn\DigitalGold\Model\RecordFactory;
use NeoUnicorn\DigitalGold\Model\TransactionFactory;
use Magento\Backend\App\Action\Context;

class Reject extends Action
{
    protected $recordFactory;
    protected $sellGoldRequestFactory;
    protected $resultRedirectFactory;
    protected $transactionFactory;

    public function __construct(
        Context $context,
        SellGoldRequestFactory $sellGoldRequestFactory,
        RecordFactory $recordFactory,
        TransactionFactory $transactionFactory,
        RedirectFactory $resultRedirectFactory
    ) {
        parent::__construct($context);
        $this->sellGoldRequestFactory = $sellGoldRequestFactory;
        $this->recordFactory = $recordFactory;
        $this->transactionFactory = $transactionFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $request = $this->sellGoldRequestFactory->create()->load($id);
                if ($request->getId()) {
                    if ($request->getStatus() == "Pending") {
                        $request->setStatus('Rejected'); // Update status
                        $request->save();

                        $transaction = $this->transactionFactory->create()->load($request->getTransactionId());
                        $transaction->setStatus('Rejected');
                        $transaction->setTransactionNote('Digital Gold Credit Back To Your Account ');
                        $transaction->save();

                        $record = $this->recordFactory->create();
                        $record->load($request->getCustomerId(), 'customer_id');

                        $record->setUsedGold($record->getUsedGold() - $request->getGoldQuantity());
                        $record->setRemainingGold($record->getRemainingGold() + $request->getGoldQuantity());
                        $record->save();

                        $this->messageManager->addSuccessMessage(__('Record rejected successfully.'));
                    } else {
                        $this->messageManager->addErrorMessage(__('Record already ' . $request->getStatus()));
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('Record not found.'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Error: ' . $e->getMessage()));
            }
        }

        return $this->resultRedirectFactory->create()->setPath('digitalgold/sell/index/');
    }
}
