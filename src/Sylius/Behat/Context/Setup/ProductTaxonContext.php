<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductTaxonContext implements Context
{
    public function __construct(
        private FactoryInterface $productTaxonFactory,
        private ObjectManager $objectManager,
    ) {
    }

    /**
     * @Given /^I assigned (this product) to ("[^"]+" taxon)$/
     * @Given /^(it|this product) (belongs to "[^"]+")$/
     * @Given /^(this product) is in ("[^"]+" taxon) at (\d)(?:st|nd|rd|th) position$/
     * @Given the product :product belongs to taxon :taxon
     */
    public function itBelongsTo(ProductInterface $product, TaxonInterface $taxon, $position = null)
    {
        $productTaxon = $this->createProductTaxon($taxon, $product, (int) $position - 1);
        $product->addProductTaxon($productTaxon);

        $this->objectManager->persist($product);
        $this->objectManager->flush();
    }

    /**
     * @Given the product :product has a main taxon :taxon
     * @Given /^(this product) has a main (taxon "[^"]+")$/
     */
    public function productHasMainTaxon(ProductInterface $product, TaxonInterface $taxon): void
    {
        $product->setMainTaxon($taxon);
        $this->objectManager->flush();
    }

    /**
     * @param int|null $position
     *
     * @return ProductTaxonInterface
     */
    private function createProductTaxon(TaxonInterface $taxon, ProductInterface $product, $position = null)
    {
        /** @var ProductTaxonInterface $productTaxon */
        $productTaxon = $this->productTaxonFactory->createNew();
        $productTaxon->setProduct($product);
        $productTaxon->setTaxon($taxon);

        if (null !== $position) {
            $productTaxon->setPosition($position);
        }

        return $productTaxon;
    }
}
