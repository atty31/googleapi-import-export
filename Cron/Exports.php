<?php


namespace Atma\Products\Cron;

use Atma\Products\Helper\General as GeneralHelper;
use Atma\Products\Helper\GoogleClient;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;

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
     * @var CollectionFactory
     */
    public $productCollection;

    /**
     * @var StockRegistryInterface
     */
    public $stock;

    /**
     * Exports constructor.
     * @param GeneralHelper $generalHelper
     * @param GoogleClient $googleClient
     * @param CollectionFactory $productCollection
     * @param StockRegistryInterface $stock
     */
    public function __construct(
        GeneralHelper $generalHelper,
        GoogleClient $googleClient,
        CollectionFactory $productCollection,
        StockRegistryInterface $stock
    ) {
        $this->generalHelper = $generalHelper;
        $this->googleClient = $googleClient;
        $this->productCollection = $productCollection;
        $this->stock = $stock;
    }

    /**
     * Run cronjob
     * @throws \Google\Exception
     */
    public function execute()
    {
        if (!$this->generalHelper->enableModule()){
            return;
        }

        $productCollection = $this->productCollection->create();
        $productCollection->addAttributeToSelect('*');
        $productData = [];

        /** @var Product $product */
        foreach ($productCollection as $product){
            $productStock = $this->getStockStatus($product->getId());
            $productData[] = [
                (string) $product->getSku(),
                (string) $product->getTypeId(),
                (string) $product->getName(),
                (string) $product->getDescription(),
                (int) $product->getStatus(),
                (int) $product->getVisibility(),
                (float) $product->getPrice(),
                (int) $productStock->getIsInStock(),
                (int) $productStock->getQty()
            ];
        }
        $this->googleClient->updateSpreadSheet($this->googleClient->getGoogleClient(), $productData);
    }

    /**
     * Get product stock data
     * @param int $productId
     * @return StockItemInterface
     */
    public function getStockStatus(int $productId)
    {
        return $this->stock->getStockItem($productId);
    }
}
