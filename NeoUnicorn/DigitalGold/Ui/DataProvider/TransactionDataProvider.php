<?php
namespace NeoUnicorn\DigitalGold\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use NeoUnicorn\DigitalGold\Model\ResourceModel\Transaction\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\Session;

class TransactionDataProvider extends AbstractDataProvider
{
    protected $collection;
    protected $request;
    protected $session;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        Session $session, // Inject session
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->session = $session; // Store session

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        // First request: Get customer_id from the URL and store it in session
        $customerId = $this->request->getParam('customer_id');

        if ($customerId) {
            $this->session->setData('digitalgold_customer_id', $customerId);
        } else {
            // Second request: Get customer_id from session
            $customerId = $this->session->getData('digitalgold_customer_id');
        }

        if ($customerId) {
            $this->collection->addFieldToFilter('customer_id', $customerId);
        }

        $collectionData = $this->collection->toArray();

        return [
            'totalRecords' => $this->collection->getSize(),
            'items' => $collectionData['items']
        ];
    }
}
