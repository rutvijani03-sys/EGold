<?php
namespace NeoUnicorn\DigitalGold\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class RequestActions extends Column
{
    /** @var UrlInterface */
    protected $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['entity_id'])) {
                    $item[$this->getData('name')] = [
                        'approve' => [
                            'href' => $this->urlBuilder->getUrl(
                                'digitalgold/request/approve',
                                ['id' => $item['entity_id']]
                            ),
                            'label' => __('Approve'),
                            'confirm' => [
                                'title' => __('Approve Request'),
                                'message' => __('Are you sure you want to approve this request?')
                            ]
                        ],
                        'reject' => [
                            'href' => $this->urlBuilder->getUrl(
                                'digitalgold/request/reject',
                                ['id' => $item['entity_id']]
                            ),
                            'label' => __('Reject'),
                            'confirm' => [
                                'title' => __('Reject Request'),
                                'message' => __('Are you sure you want to reject this request?')
                            ]
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
