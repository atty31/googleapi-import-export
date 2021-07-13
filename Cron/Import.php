<?php
namespace Atma\Products\Cron;

use Atma\Products\Helper\General as GeneralHelper;
use Atma\Products\Helper\GoogleClient;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;
use Exception;

class Import
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
     * @var ProductInterfaceFactory
     */
    public $productFactory;

    /**
     * @var ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * @var StockRegistryInterface
     */
    public $stockRegistry;

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * Import constructor.
     * @param GeneralHelper $generalHelper
     * @param GoogleClient $googleClient
     * @param ProductInterfaceFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param StockRegistryInterface $stockRegistry
     * @param LoggerInterface $logger
     */
    public function __construct(
        GeneralHelper $generalHelper,
        GoogleClient $googleClient,
        ProductInterfaceFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry,
        LoggerInterface $logger
    ){
        $this->generalHelper = $generalHelper;
        $this->googleClient = $googleClient;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->logger = $logger;
    }

    /**
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws StateException
     * @throws \Google\Exception
     */
    public function execute()
    {
        if (!$this->generalHelper->enableModule()){
            return;
        }
        $values = $this->googleClient->readSpreadSheet($this->googleClient->getGoogleClient());
        foreach ($values as $row) {
            $sku = $this->createProduct($row);
            if ($sku === ''){
                continue;
            }

            $this->updateStock($sku, $row[7], $row[8]);
        }
    }

    /**
     * @param $row
     * @return string
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws StateException
     */
    public function createProduct($row) : string
    {
        try {
            $this->productRepository->get((string) $row[0]);
        }catch (NoSuchEntityException $error){
            /** @var ProductInterface $product */
            $product = $this->productFactory->create();
            $product->setSku((string) $row[0]);
            $product->setTypeId((string) $row[1]);
            $product->setName((string) $row[2]);
            $product->setDescription((string) $row[3]);
            $product->setStatus((int) $row[4]);
            $product->setVisibility((bool) $row[5]);
            $product->setPrice((float) $row[6]);
            $product->setAttributeSetId(4);

            try {
                $product = $this->productRepository->save($product);
            }catch (Exception $error){
                $this->logger->error($error->getMessage());
                return '';
            }
            return $product->getSku();
        }
        return '';
    }

    /**
     * @param string $sku
     * @param bool $isInStock
     * @param int $stockQty
     * @throws NoSuchEntityException
     */
    public function updateStock(string $sku, bool $isInStock, int $stockQty)
    {
        $stockItem = $this->stockRegistry->getStockItemBySku($sku);
        $stockItem->setIsInStock($isInStock);
        $stockItem->setQty($stockQty);
        try {
            $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
        }catch (Exception $error){
            $this->logger->error($error->getMessage());
        }
    }
}
