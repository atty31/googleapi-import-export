<?php


namespace Atma\Products\Cron;

use \Atma\Products\Helper\General as GeneralHelper;
use \Atma\Products\Helper\GoogleClient;

class Exports
{
    /**
     * @var GeneralHelper
     */
    public $generalHelper;

    /**
     * @var GoogleClient
     */
    public $googleClient;

    /**
     * Exports constructor.
     * @param GeneralHelper $generalHelper
     * @param GoogleClient $googleClient
     */
    public function __construct(
        GeneralHelper $generalHelper,
        GoogleClient $googleClient
    ) {
        $this->generalHelper = $generalHelper;
        $this->googleClient = $googleClient;
    }

    public function execute()
    {
        if (!$this->generalHelper->enableModule()){
            return;
        }
        $response = $this->googleClient->getGoogleClient();
    }
}
