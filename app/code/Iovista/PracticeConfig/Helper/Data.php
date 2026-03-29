<?php
namespace Iovista\PracticeConfig\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLE = 'practice_section/general/enable';
    const XML_PATH_TEXT   = 'practice_section/general/practice_text';

    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getPracticeText($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TEXT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
