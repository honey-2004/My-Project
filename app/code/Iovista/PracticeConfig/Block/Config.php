<?php
namespace Iovista\PracticeConfig\Block;

use Magento\Framework\View\Element\Template;
use Iovista\PracticeConfig\Helper\Data;

class Config extends Template
{
    protected $helper;

    public function __construct(
        Template\Context $context,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    public function canShow()
    {
        return $this->helper->isEnabled();
    }

    public function getPracticeText()
    {
        return $this->helper->getPracticeText();
    }
}