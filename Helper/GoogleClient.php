<?php


namespace Atma\Products\Helper;

use Atma\Products\Helper\General as GeneralHelper;
use Google_Service_Analytics;
use Google_Service_Sheets;
use Google_Client;
use Google_Service_Sheets_Spreadsheet;
use Exception;

class GoogleClient
{
    /**
     * @var GeneralHelper
     */
    public $generalHelper;

    public function __construct(
        GeneralHelper $generalHelper
    ){
        $this->generalHelper = $generalHelper;
    }

    public function getGoogleClient()
    {
        $client = new Google_Client();

        $client->setApplicationName('Google Sheets and PHP');
        $credentials = json_decode($this->generalHelper->getGoogleApiDetails(), true);

        $client->setAuthConfig($credentials);
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $service = new Google_Service_Sheets($client);
        try {
            $response = $service->spreadsheets_values->get($this->generalHelper->getSpreadSheetId(), 'Import!A:Z');
        }catch (Exception $error){
            var_dump($error->getMessage()); exit();
        }
        var_dump($response->getValues());

    }
}
