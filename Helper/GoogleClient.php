<?php


namespace Atma\Products\Helper;

use Atma\Products\Helper\General as GeneralHelper;
use Google\Model;
use Google\Service\Sheets;
use Google_Client;
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
        LoggerInterface $logger,
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
        $spreadsheetId = $this->generalHelper->getSpreadSheetId();
        $client = new Google_Client();

        $client->setApplicationName('Google Sheets and PHP');
        $credentials = json_decode($this->generalHelper->getGoogleApiDetails(), true);

        $client->setAuthConfig($credentials);
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');

        return $client;

    }
    public function readSpreadSheet(Google_Client $client)
    {
        $service = new Google_Service_Sheets($client);
        try {
            $response = $service->spreadsheets_values->get($spreadsheetId, 'Import!A:Z');
        }catch (Exception $error){
            $this->logger->error($error->getMessage());
        }
        return $response->getValues();
    }

    /**
     * @param Google_Client $client
     * @return mixed
     */
    public function updateSpreadSheet(Google_Client $client)
    {
        $values = [
            ['col 1', 'col 2', 'col 3'],
            ['col 1', 'col 2']
        ];

        $body = new Google_Service_Sheets_ValueRange([
            "values" => $values
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];
        try {
            $updateSheet = $service->spreadsheets_values->update($spreadsheetId, 'Export!A2:Z', $body, $params);
        }catch (Exception $error){
            $this->logger->error($error->getMessage());
        }
        return $updateSheet;
    }
}
