<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefrontPlugin\Sylius\Transformer;

use BitBag\SyliusVueStorefrontPlugin\Document\Attribute;
use BitBag\SyliusVueStorefrontPlugin\Document\Product;
use BitBag\SyliusVueStorefrontPlugin\Sylius\Transformer\SyliusProduct\ImagesToMediaGalleryTransformerInterface;
use BitBag\SyliusVueStorefrontPlugin\Sylius\Transformer\SyliusProduct\InventoryToStockTransformerInterface;
use BitBag\SyliusVueStorefrontPlugin\Sylius\Transformer\SyliusProduct\ProductAssociationsToLinksTransformerInterface;
use BitBag\SyliusVueStorefrontPlugin\Sylius\Transformer\SyliusProduct\ProductDetailsTransformerInterface;
use BitBag\SyliusVueStorefrontPlugin\Sylius\Transformer\SyliusProduct\ProductOptionsToConfigurableOptionsTransformerInterface;
use BitBag\SyliusVueStorefrontPlugin\Sylius\Transformer\SyliusProduct\ProductVariantsToConfigurableChildrenTransformerInterface;
use BitBag\SyliusVueStorefrontPlugin\Sylius\Transformer\SyliusProduct\TaxonsToCategoriesTransformerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;

final class SyliusProductAttributeTransformer implements SyliusProductAttributeTransformerInterface
{
    /** @var ProductDetailsTransformerInterface */
    private $productDetailsTransformer;

    /** @var InventoryToStockTransformerInterface */
    private $inventoryTransformer;

    /** @var ImagesToMediaGalleryTransformerInterface */
    private $imagesTransformer;

    /** @var TaxonsToCategoriesTransformerInterface */
    private $taxonsTransformer;

    /** @var ProductVariantsToConfigurableChildrenTransformerInterface */
    private $productVariantsTransformer;

    /** @var ProductOptionsToConfigurableOptionsTransformerInterface */
    private $productOptionsTransformer;

    /** @var ProductAssociationsToLinksTransformerInterface */
    private $productAssociationsTransformer;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var string */
    private $syliusChannel;

    public function __construct(
        ProductDetailsTransformerInterface $productDetailsTransformer,
        InventoryToStockTransformerInterface $inventoryTransformer,
        ImagesToMediaGalleryTransformerInterface $imagesTransformer,
        TaxonsToCategoriesTransformerInterface $taxonsTransformer,
        ProductVariantsToConfigurableChildrenTransformerInterface $productVariantsTransformer,
        ProductOptionsToConfigurableOptionsTransformerInterface $productOptionsTransformer,
        ProductAssociationsToLinksTransformerInterface $productAssociationsTransformer,
        ChannelRepositoryInterface $channelRepository,
        string $syliusChannel
    ) {
        $this->productDetailsTransformer = $productDetailsTransformer;
        $this->inventoryTransformer = $inventoryTransformer;
        $this->imagesTransformer = $imagesTransformer;
        $this->taxonsTransformer = $taxonsTransformer;
        $this->productVariantsTransformer = $productVariantsTransformer;
        $this->productOptionsTransformer = $productOptionsTransformer;
        $this->productAssociationsTransformer = $productAssociationsTransformer;
        $this->channelRepository = $channelRepository;
        $this->syliusChannel = $syliusChannel;
    }

    public function transform(ProductAttributeInterface $syliusProductAttribute): Attribute
    {
        $importedChannel = $this->channelRepository->findOneByCode($this->syliusChannel);

        if (false === $syliusProduct->getChannels()->contains($importedChannel)) {
            return null;
        }

        $details = $this->productDetailsTransformer->transform($syliusProduct);
        $stock = $this->inventoryTransformer->transform($syliusProduct->getVariants()->first());
        $mediaGallery = $this->imagesTransformer->transform($syliusProduct->getImages());
        $category = $this->taxonsTransformer->transform($syliusProduct->getTaxons());
        $configurableChildren = $this->productVariantsTransformer->transform($syliusProduct->getVariants());
        $configurableOptions = $this->productOptionsTransformer->transform($syliusProduct->getOptions());
        $productLinks = $this->productAssociationsTransformer->transform($syliusProduct->getAssociations());
        $price = new Product\Price();
        $stockItem = new Product\StockItem();

        return new Attribute(
            $syliusProduct->getId(),
            $details,
            $stock,
            $category,
            $mediaGallery,
            $configurableChildren,
            $configurableOptions,
            $productLinks,
            $price,
            $stockItem
        );
    }
}