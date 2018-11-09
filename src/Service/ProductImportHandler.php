<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Service;

use Locastic\SyliusRitamIntegrationPlugin\Factory\ChannelPricingFromRitamFactoryInterface;
use Locastic\SyliusRitamIntegrationPlugin\Factory\ProductFromRitamFactoryInterface;
use Locastic\SyliusRitamIntegrationPlugin\Repository\ProductRepositoryInterface;
use Locastic\SyliusRitamIntegrationPlugin\Repository\TaxonRepositoryInterface;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Resource\Factory\FactoryInterface;

class ProductImportHandler
{
    /**
     * @var ProductFromRitamFactoryInterface
     */
    private $productFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ChannelPricingFromRitamFactoryInterface
     */
    private $channelPricingFactory;

    /**
     * @var ProductTaxonImportHandler
     */
    private $productTaxonImportHandler;

    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * @var FactoryInterface
     */
    private $productTaxonFactory;

    /**
     * @var string
     */
    private $taxonCode;

    /**
     * ProductImportHandler constructor.
     * @param ProductFromRitamFactoryInterface $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ChannelPricingFromRitamFactoryInterface $channelPricingFactory
     * @param ProductTaxonImportHandler $productTaxonImportHandler
     * @param TaxonRepositoryInterface $taxonRepository
     * @param FactoryInterface $productTaxonFactory
     * @param string $taxonCode
     */
    public function __construct(
        ProductFromRitamFactoryInterface $productFactory,
        ProductRepositoryInterface $productRepository,
        ChannelPricingFromRitamFactoryInterface $channelPricingFactory,
        ProductTaxonImportHandler $productTaxonImportHandler,
        TaxonRepositoryInterface $taxonRepository,
        FactoryInterface $productTaxonFactory,
        $taxonCode
    ) {
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->channelPricingFactory = $channelPricingFactory;
        $this->productTaxonImportHandler = $productTaxonImportHandler;
        $this->taxonRepository = $taxonRepository;
        $this->productTaxonFactory = $productTaxonFactory;
        $this->taxonCode = $taxonCode;
    }


    public function importProducts($ritamProducts)
    {
        $importedProductCount = 0;
        $batchSize = 100;

        $this->productRepository->disableSqlLogger();
        foreach ($ritamProducts as $ritamProduct) {

            if (!isset($ritamProduct->item_code)) {
                continue;
            }

            $product = $this->productRepository->findOneByCode($ritamProduct->item_code);

            if (!is_null($product)) {
                continue;
            }

            $channelPricing = $this->channelPricingFactory->createFromRitam($ritamProduct);

            $product = $this->productFactory->createWithChannelPricing($ritamProduct, $channelPricing);

            $product = $this->productTaxonImportHandler->importTaxons($ritamProduct, $product);


            if ($product->getProductTaxons()->count() == 0) {

                $taxon = $this->taxonRepository->findOneByCode($this->taxonCode);

                if ($taxon instanceof Taxon) {

                    $productTaxon = $this->productTaxonFactory->createNew();
                    $productTaxon->setTaxon($taxon);
                    $productTaxon->setProduct($product);

                    $product->addProductTaxon($productTaxon);
                    $product->setMainTaxon($taxon);
                }
            }

            $this->productRepository->persist($product);

            // bulk insert - flush after every $batchSize persists
            if (($importedProductCount % $batchSize) === 0) {
                $this->productRepository->savePersisted();
            }

            $importedProductCount++;
        }

        // save remaining
        $this->productRepository->savePersisted();

        return $importedProductCount;
    }
}