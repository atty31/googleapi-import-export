<?php


namespace Atma\Products\Helper;

use Atma\Products\Helper\General as GeneralHelper;
use Google\Model;
use Google\Service\Sheets;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_Spreadsheet;
use Exception;
use Google_Service_Sheets_ValueRange;
use Google\Exception as GoogleException;
use Psr\Log\LoggerInterface;

class GoogleClient
{
    /**
     * @var GeneralHelper
     */
    public $generalHelper;

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * GoogleClient constructor.
     * @param General $generalHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        GeneralHelper $generalHelper,
        LoggerInterface $logger
    ){
        $this->generalHelper = $generalHelper;
        $this->logger = $logger;
    }

    /**
     * @return Google_Client
     * @throws GoogleException
     */
    public function getGoogleClient()
    {
        $client = new Google_Client();

        $client->setApplicationName('Google Sheets and PHP');
        $credentials = json_decode($this->generalHelper->getGoogleApiDetails(), true);

        $client->setAuthConfig($credentials);
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');

        return new Google_Service_Sheets($client);

    }

    /**
     * @param $service
     * @return mixed
     */
    public function readSpreadSheet($service)
    {
        try {
            $response = $service->spreadsheets_values->get($this->generalHelper->getSpreadSheetId(), $this->generalHelper->getImportSheetName().'!A:Z');
        }catch (Exception $error){
            $this->logger->error($error->getMessage());
        }
        return $response->getValues();
    }

    /**
     * @param $service
     * @return false|mixed
     */
    public function updateSpreadSheet($service, $values)
    {
        try {
            return $service->spreadsheets_values->update(
                $this->generalHelper->getSpreadSheetId(),
                $this->generalHelper->getExportSheetName().'!A2:Z',
                new Google_Service_Sheets_ValueRange([
                    'values' => $values
                ]),
                ['valueInputOption' => 'RAW']
            );
        }catch (Exception $error){
            $this->logger->error($error->getMessage());
        }

        return false;
    }
}
