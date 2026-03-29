<?php

namespace Iovista\ContactUs\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
    ) {
        parent::__construct($context);
    }

    public function getAllowedFileTypes()
    {
        return ".png, .jpg, .jpeg";
    }

    public function getMaxFileSize()
    {
        return 2;
    }
}