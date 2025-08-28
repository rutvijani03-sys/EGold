<?php
namespace NeoUnicorn\DigitalGold\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use NeoUnicorn\DigitalGold\Model\ResourceModel\Record\CollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;

class RecordDataProvider extends AbstractDataProvider
{
    protected $customerRepository;
    protected $searchCriteriaBuilder;
    protected $logger;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        try {
            $items = $this->collection->getItems();
            $customerData = $this->getCustomerNames(array_unique($this->collection->getColumnValues('customer_id')));
            
            foreach ($items as $item) {
                $customerId = $item->getData('customer_id');
                $item->setData('customer_name', $customerData[$customerId] ?? 'Guest');
            }

            return [
                'totalRecords' => $this->collection->getSize(),
                'items' => array_values($this->collection->toArray()['items'])
            ];
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return [
                'totalRecords' => 0,
                'items' => []
            ];
        }
    }

    protected function getCustomerNames(array $customerIds)
    {
        if (empty($customerIds)) {
            return [];
        }

        $customerData = [];
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('entity_id', $customerIds, 'in')
            ->create();

        try {
            $customers = $this->customerRepository->getList($searchCriteria)->getItems();
            foreach ($customers as $customer) {
                $customerData[$customer->getId()] = $customer->getFirstname() . ' ' . $customer->getLastname();
            }
        } catch (\Exception $e) {
            $this->logger->error('Error loading customer names: ' . $e->getMessage());
        }

        return $customerData;
    }
}