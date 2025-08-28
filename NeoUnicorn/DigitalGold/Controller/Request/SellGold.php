<?php 
namespace NeoUnicorn\DigitalGold\Controller\Request;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use NeoUnicorn\DigitalGold\Model\SellGoldRequestFactory;
use NeoUnicorn\DigitalGold\Model\RecordFactory;
use NeoUnicorn\DigitalGold\Model\TransactionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Message\ManagerInterface;

class SellGold extends Action
{
    protected $sellGoldRequestFactory;
    protected $customerSession;
    protected $messageManager;
    protected $recordFactory;
    protected $transactionFactory;
    protected $_productRepository;

    public function __construct(
        Context $context,
        SellGoldRequestFactory $sellGoldRequestFactory,
        RecordFactory $recordFactory,
        TransactionFactory $transactionFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        Session $customerSession,
        ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->sellGoldRequestFactory = $sellGoldRequestFactory;
        $this->recordFactory = $recordFactory;
        $this->transactionFactory = $transactionFactory;
        $this->_productRepository = $productRepository;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage(__('You need to log in to sell gold.'));
            return $this->resultRedirectFactory->create()->setPath('customer/account/login');
        }

        $customerId = $this->customerSession->getCustomerId();
        $goldQuantity = $this->getRequest()->getParam('gold_quantity');

        $record = $this->recordFactory->create();
        $record->load($customerId, 'customer_id');

        if ($goldQuantity <= 0 || $record->getRemainingGold() < $goldQuantity) {
            $this->messageManager->addErrorMessage(__('Invalid gold quantity.'));
            return $this->resultRedirectFactory->create()->setPath('digitalgold/sellgold');
        }

        $productId=$record->getProductId();
        $transactionnote="sell digital Gold";
        $product = $this->_productRepository->getById($record->getProductId());

        try {
            if($record->getId()){
                $record->setUsedGold($record->getUsedGold() + $goldQuantity);
                $record->setRemainingGold($record->getRemainingGold() - $goldQuantity);
            }
            $record->save();
            $price = (int)$product->getGoldPrice()*$goldQuantity/10;
            $transaction = $this->transactionFactory->create();
            $transaction
                ->setCustomerId($customerId)
                ->setProductId($productId)
                ->setAmount($price)
                ->setQuantity($goldQuantity)
                ->setOrderId()
                ->setTransactionNote($transactionnote)
                ->setAction("debit")
                ->setStatus("Pending")
                ->save();

            $sellRequest = $this->sellGoldRequestFactory->create();
            $sellRequest->setData([
                'customer_id' => $customerId,
                'gold_quantity' => $goldQuantity,
                'amount_requested' => $price, 
                'status' => 'Pending',
                'Product_id' => $productId,
                'transaction_id' => $transaction->getId()
            ]);
            $sellRequest->save();

            $this->messageManager->addSuccessMessage(__('Your sell request has been submitted successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error submitting request: ' . $e->getMessage()));
        }

        return $this->resultRedirectFactory->create()->setPath('digitalgold/transaction');
    }
}