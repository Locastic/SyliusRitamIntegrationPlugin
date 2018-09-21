<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface as BaseProductVariantTranslationInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

/**
 * @method ProductVariantTranslationInterface|TranslationInterface getTranslation(?string $locale = null)
 */
class ProductVariant extends BaseProductVariant implements ProductVariantInterface
{

}