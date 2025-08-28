<?php

namespace NeoUnicorn\DigitalGold\Controller\Adminhtml\Request;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\RedirectFactory;
use NeoUnicorn\DigitalGold\Model\SellGoldRequestFactory;
use NeoUnicorn\DigitalGold\Model\RecordFactory;
use NeoUnicorn\DigitalGold\Model\TransactionFactory;
use Webkul\Walletsystem\Model\WalletrecordFactory;
use Webkul\Walletsystem\Model\WallettransactionFactory;
use Webkul\Walletsystem\Model\WalletUpdateData;
use Magento\Backend\App\Action\Context;

class Approve extends Action
{
    protected $recordFactory;
    protected $sellGoldRequestFactory;
    protected $resultRedirectFactory;
    protected $transactionFactory;
    /**
     * @var Webkul\Walletsystem\Model\WalletrecordFactory
     */
    protected $walletrecord;

    /**
     * @var Webkul\Walletsystem\Model\WallettransactionFactory
     */
    protected $walletTransaction;

    /**
     * @var Webkul\Walletsystem\Helper\Data
     */
    protected $walletHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Webkul\Walletsystem\Helper\Mail
     */
    protected $mailHelper;

    /**
     * @var Webkul\Walletsystem\Model\WalletUpdateData
     */
    protected $walletUpdate;

    /**
     * Initialize dependencies
     *
     * @param WalletrecordFactory                    $walletrecord
     * @param WallettransactionFactory               $transactionFactory
     * @param WebkulWalletsystemHelperData           $walletHelper
     * @param MagentoFrameworkStdlibDateTimeDateTime $date
     * @param WebkulWalletsystemHelperMail           $mailHelper
     * @param WalletUpdateData                       $walletUpdate
     */

    public function __construct(
        Context $context,
        SellGoldRequestFactory $sellGoldRequestFactory,
        RecordFactory $recordFactory,
        TransactionFactory $transactionFactory,
        RedirectFactory $resultRedirectFactory,
        WalletrecordFactory $walletrecord,
        WallettransactionFactory $wallettransactionFactory,
        \Webkul\Walletsystem\Helper\Data $walletHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Webkul\Walletsystem\Helper\Mail $mailHelper,
        WalletUpdateData $walletUpdate
    ) {
        parent::__construct($context);
        $this->sellGoldRequestFactory = $sellGoldRequestFactory;
        $this->recordFactory = $recordFactory;
        $this->transactionFactory = $transactionFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->walletrecord = $walletrecord;
        $this->walletTransaction = $wallettransactionFactory;
        $this->walletHelper = $walletHelper;
        $this->date = $date;
        $this->mailHelper = $mailHelper;
        $this->walletUpdate = $walletUpdate;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $request = $this->sellGoldRequestFactory->create()->load($id);
                if ($request->getId()) {
                    if ($request->getStatus() == "Pending") {
                        $request->setStatus('Approved'); // Update status
                        $request->save();

                        $transaction = $this->transactionFactory->create()->load($request->getTransactionId());
                        $transaction->setStatus('Approved');
                        $transaction->setTransactionNote('Credited to your Wallet');
                        $transaction->save();

                        $successCounter = 0;
                        $params = $this->getRequest()->getParams();
                        $walletTransaction  = $this->walletTransaction->create();
                        $currencycode = $this->walletHelper->getBaseCurrencyCode();
                        $params['customerid'] = $request->getCustomerId();
                        $params['curr_code'] = $currencycode;
                        $params['walletamount'] = $request->getAmountRequested();
                        $params['curr_amount'] = $request->getAmountRequested();
                        $params['sender_id'] = 0;
                        $params['sender_type'] = $walletTransaction::ADMIN_TRANSFER_TYPE;
                        $params['order_id'] = 0;
                        $params['status'] = $walletTransaction::WALLET_TRANS_STATE_APPROVE;
                        $params['increment_id'] = '';
                        $params['walletactiontype'] = 'credit';
                        $customerId = $params['customerid'];
                        $totalAmount = 0;
                        $remainingAmount = 0;
                        $params['walletnote'] = 'Digital gold amount added to wallet';
                        $walletRecordModel = $this->walletUpdate->getRecordByCustomerId($request->getCustomerId());
                        if ($walletRecordModel != '' && $walletRecordModel->getEntityId()) {
                            $remainingAmount = $walletRecordModel->getRemainingAmount();
                        }
                        $result = $this->walletUpdate->creditAmount($request->getCustomerId(), $params);

                        $this->messageManager->addSuccessMessage(__('Record approved successfully.'));
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
