<?php

namespace NeoUnicorn\DigitalGold\Block;

use Magento\Framework\View\Element\Template\Context;

class Link extends \Magento\Framework\View\Element\Template
{
    protected $_helper;

    public function __construct(
        Context $context,
    ) {
        parent::__construct($context);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {

        if ($this->_request->getModuleName() == "digitalgold") {
            $html = '<li class="nav item current">';
            $html .= '<strong>' . $this->escapeHtml($this->getLabel()) . '</strong>';
            $html .= '</li>';
        } else {
            $html = "<li class='nav item'>";
            $html .= '<a href=' . $this->getUrl($this->getPath()) . ' >' .
                $this->escapeHtml($this->getLabel()) . '</a>';
            $html .= '</li>';
        }

        return $html;
    }
}
