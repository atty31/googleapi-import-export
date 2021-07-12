<?php


namespace Atma\Products\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class General extends AbstractHelper
{
    CONST ENABLE_MODULE = 'atma/google_client/enable';
    CONST GOOGLE_API = 'atma/google_client/google_api_details';
    CONST GOOGLE_SPREADSHEET_ID = 'atma/google_client/spreadsheet_id';

    /**
     * @return bool
     */
    public function enableModule() : bool
    {
        return (bool) $this->scopeConfig->getValue(self::ENABLE_MODULE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getGoogleApiDetails() : string
    {
        return trim($this->scopeConfig->getValue(self::GOOGLE_API, ScopeInterface::SCOPE_STORE));
    }

    /**
     * @return string
     */
    public function getSpreadSheetId() : string
    {
        return trim($this->scopeConfig->getValue(self::GOOGLE_SPREADSHEET_ID, ScopeInterface::SCOPE_STORE));
    }
}
