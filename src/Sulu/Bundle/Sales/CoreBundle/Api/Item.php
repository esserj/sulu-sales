<?php

namespace Sulu\Bundle\Sales\CoreBundle\Api;

use DateTime;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Exclude;
use Sulu\Bundle\ContactBundle\Entity\AccountInterface;
use Sulu\Bundle\PricingBundle\Pricing\Exceptions\PriceFormatterException;
use Sulu\Bundle\ProductBundle\Entity\Addon;
use Sulu\Bundle\ProductBundle\Entity\ProductInterface;
use Sulu\Bundle\ProductBundle\Product\ProductFactoryInterface;
use Sulu\Bundle\ProductBundle\Api\ApiProductInterface;
use Sulu\Bundle\Sales\CoreBundle\Entity\ItemAttribute;
use Sulu\Bundle\Sales\CoreBundle\Entity\ItemAttributeInterface;
use Sulu\Bundle\PricingBundle\Pricing\PriceFormatter;
use Sulu\Bundle\Sales\CoreBundle\Entity\ItemInterface;
use Sulu\Component\Rest\ApiWrapper;
use Sulu\Component\Security\Authentication\UserInterface;
use Sulu\Bundle\ProductBundle\Api\Product;
use Sulu\Bundle\Sales\CoreBundle\Entity\ItemInterface as Entity;
use Sulu\Bundle\PricingBundle\Pricing\CalculableBulkPriceItemInterface;
use Sulu\Bundle\PricingBundle\Pricing\CalculablePriceGroupItemInterface;
use Sulu\Bundle\Sales\CoreBundle\Entity\OrderAddressInterface as OrderAddressEntity;

/**
 * The item class which will be exported to the API
 */
class Item extends ApiWrapper implements
    ApiItemInterface,
    CalculableBulkPriceItemInterface,
    CalculablePriceGroupItemInterface
{
    /**
     * indicates if prices have been changed
     *
     * @var bool
     */
    protected $priceChanged = false;

    /**
     * price value before change
     *
     * @var float
     */
    protected $priceChangeFrom;

    /**
     * price value after change (current price)
     *
     * @var float
     */
    protected $priceChangeTo;

    /**
     * Temporary storage for product api entity
     *
     * @Exclude
     *
     * @var Product
     */
    protected $tempProduct;

    /**
     * @Exclude
     *
     * @var ProductFactoryInterface
     */
    protected $productFactory;

    /**
     * @Exclude
     *
     * @var PriceFormatter
     */
    protected $priceFormatter;

    /**
     * @param Entity $item The item to wrap
     * @param string $locale The locale of this item
     * @param ProductFactoryInterface $productFactory
     * @param PriceFormatter $priceFormatter
     * @param string $currency
     */
    public function __construct(
        Entity $item,
        $locale,
        ProductFactoryInterface $productFactory,
        PriceFormatter $priceFormatter,
        $currency = 'EUR'
    ) {
        $this->entity = $item;
        $this->locale = $locale;
        $this->currency = $currency;
        $this->productFactory = $productFactory;
        $this->priceFormatter = $priceFormatter;
    }

    /**
     * Returns the id of the entity
     *
     * @VirtualProperty
     * @SerializedName("id")
     * @Groups({"Default","cart"})
     *
     * @return int
     */
    public function getId()
    {
        return $this->entity->getId();
    }

    /**
     * @VirtualProperty
     * @SerializedName("name")
     *
     * @return string
     */
    public function getName()
    {
        return $this->entity->getName();
    }

    /**
     * @param string $name
     *
     * @return Item
     */
    public function setName($name)
    {
        $this->entity->setName($name);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("number")
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->entity->getNumber();
    }

    /**
     * @param string $number
     *
     * @return Item
     */
    public function setNumber($number)
    {
        $this->entity->setNumber($number);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("created")
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->entity->getCreated();
    }

    /**
     * @param DateTime $created
     *
     * @return Item
     */
    public function setCreated(DateTime $created)
    {
        $this->entity->setCreated($created);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("deliveryDate")
     *
     * @return DateTime
     */
    public function getDeliveryDate()
    {
        return $this->entity->getDeliveryDate();
    }

    /**
     * @param DateTime $deliveryDate
     *
     * @return Item
     */
    public function setDeliveryDate(DateTime $deliveryDate = null)
    {
        $this->entity->setDeliveryDate($deliveryDate);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("changed")
     *
     * @return DateTime
     */
    public function getChanged()
    {
        return $this->entity->getChanged();
    }

    /**
     * @param DateTime $changed
     *
     * @return Item
     */
    public function setChanged(DateTime $changed)
    {
        $this->entity->setChanged($changed);

        return $this;
    }

    /**
     * Set changer
     *
     * @param UserInterface $changer
     *
     * @return Item
     */
    public function setChanger(UserInterface $changer = null)
    {
        $this->entity->setChanger($changer);

        return $this;
    }

    /**
     * Get changer
     *
     * @VirtualProperty
     * @SerializedName("changer")
     *
     * @return UserInterface
     */
    public function getChanger()
    {
        // just return id
        $changer = $this->entity->getChanger();

        return !$changer ? null : array(
            'id' => $changer->getId()
        );
    }

    /**
     * Set creator
     *
     * @param UserInterface $creator
     *
     * @return Item
     */
    public function setCreator(UserInterface $creator = null)
    {
        $this->entity->setCreator($creator);

        return $this;
    }

    /**
     * Get creator
     *
     * @VirtualProperty
     * @SerializedName("creator")
     *
     * @return UserInterface
     */
    public function getCreator()
    {
        // just return id
        $creator = $this->entity->getCreator();

        return !$creator ? null : array(
            'id' => $creator->getId()
        );
    }

    /**
     * @param float
     *
     * @return Item
     */
    public function setQuantity($quantity)
    {
        $this->entity->setQuantity($quantity);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("quantity")
     * @Groups({"Default","cart"})
     *
     * @return float
     */
    public function getQuantity()
    {
        return $this->entity->getQuantity();
    }

    /**
     * @param string
     *
     * @return Item
     */
    public function setQuantityUnit($quantityUnit)
    {
        $this->entity->setQuantityUnit($quantityUnit);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("quantityUnit")
     *
     * @return string
     */
    public function getQuantityUnit()
    {
        return $this->entity->getQuantityUnit();
    }

    /**
     * @param bool
     *
     * @return Item
     */
    public function setUseProductsPrice($useProductsPrice)
    {
        $this->entity->setUseProductsPrice($useProductsPrice);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("useProductsPrice")
     *
     * @return bool
     */
    public function getUseProductsPrice()
    {
        return $this->entity->getUseProductsPrice();
    }

    /**
     * @param float
     *
     * @return Item
     */
    public function setTax($tax)
    {
        $this->entity->setTax($tax);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("tax")
     *
     * @return float
     */
    public function getTax()
    {
        return $this->entity->getTax();
    }

    /**
     * @param float
     *
     * @return Item
     */
    public function setPrice($value)
    {
        $this->entity->setPrice($value);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("price")
     * @Groups({"Default","cart"})
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->entity->getPrice();
    }

    /**
     * @VirtualProperty
     * @SerializedName("priceFormatted")
     * @Groups({"Default","cart"})
     *
     * @param string $locale
     *
     * @throws PriceFormatterException
     *
     * @return string
     */
    public function getPriceFormatted($locale = null)
    {
        return $this->priceFormatter->format((float)$this->entity->getPrice(), null, $locale);
    }

    /**
     * @VirtualProperty
     * @SerializedName("unitPrice")
     * @Groups({"Default","cart"})
     *
     * @return float
     */
    public function getUnitPrice()
    {
        $product = $this->getProduct();
        if ($product === null) {
            return null;
        }
        $orderContentRatio = $product->getOrderContentRatio();
        $price = $this->getPrice();

        if ($orderContentRatio) {
            return round($price / $orderContentRatio, 2);
        }

        return $price;
    }

    /**
     * @VirtualProperty
     * @SerializedName("unitPriceFormatted")
     * @Groups({"Default","cart"})
     *
     * @param string $locale
     *
     * @throws PriceFormatterException
     *
     * @return string
     */
    public function getUnitPriceFormatted($locale = null)
    {
        return $this->priceFormatter->format((float)$this->getUnitPrice(), null, $locale);
    }

    /**
     * @VirtualProperty
     * @SerializedName("totalNetPriceFormatted")
     * @Groups({"Default","cart"})
     *
     * @param string $locale
     *
     * @throws PriceFormatterException
     *
     * @return string
     */
    public function getTotalNetPriceFormatted($locale = null)
    {
        return $this->priceFormatter->format((float)$this->entity->getTotalNetPrice(), null, $locale);
    }

    /**
     * Get total net price of an item
     *
     * @VirtualProperty
     * @SerializedName("totalNetPrice")
     * @Groups({"Default","cart"})
     *
     * @return float
     */
    public function getTotalNetPrice()
    {
        return $this->entity->getTotalNetPrice();
    }

    /**
     * Set total net price of an item
     *
     * @param float $price
     *
     * @return Item
     */
    public function setTotalNetPrice($price)
    {
        $this->entity->setTotalNetPrice($price);

        return $this;
    }

    /**
     * @param float
     *
     * @return Item
     */
    public function setDiscount($value)
    {
        $this->entity->setDiscount($value);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("discount")
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->entity->getDiscount();
    }

    /**
     * @param string
     *
     * @return Item
     */
    public function setDescription($value)
    {
        $this->entity->setDescription($value);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("description")
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->entity->getDescription();
    }

    /**
     * @param float
     *
     * @return Item
     */
    public function setWeight($value)
    {
        $this->entity->setWeight($value);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("weight")
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->entity->getWeight();
    }

    /**
     * @param float
     *
     * @return Item
     */
    public function setWidth($value)
    {
        $this->entity->setWidth($value);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("width")
     *
     * @return float
     */
    public function getWidth()
    {
        return $this->entity->getWidth();
    }

    /**
     * @param float
     *
     * @return Item
     */
    public function setHeight($value)
    {
        $this->entity->setHeight($value);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("height")
     *
     * @return float
     */
    public function getHeight()
    {
        return $this->entity->getHeight();
    }

    /**
     * @param float
     *
     * @return Item
     */
    public function setLength($value)
    {
        $this->entity->setWeight($value);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("length")
     *
     * @return float
     */
    public function getLength()
    {
        return $this->entity->getLength();
    }

    /**
     * @param int
     *
     * @return Item
     */
    public function setBitmaskStatus($status)
    {
        $this->entity->setBitmaskStatus($status);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("bitmaskStatus")
     *
     * @return int
     */
    public function getBitmaskStatus()
    {
        return $this->entity->getBitmaskStatus();
    }

    /**
     * @param AccountInterface
     *
     * @return Item
     */
    public function setSupplier($value)
    {
        $this->entity->setSupplier($value);

        return $this;
    }

    /**
     * @return AccountInterface
     */
    public function getSupplier()
    {
        return $this->entity->getSupplier();
    }

    /**
     * @param string
     *
     * @return Item
     */
    public function setSupplierName($value)
    {
        $this->entity->setSupplierName($value);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("supplierName")
     *
     * @return float
     */
    public function getSupplierName()
    {
        return $this->entity->getSupplierName();
    }

    /**
     * @param ItemAttributeInterface $value
     *
     * @return $this
     */
    public function addAttribute(ItemAttributeInterface $value)
    {
        $this->entity->addAttribute($value);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("attributes")
     *
     * @return ItemAttribute
     */
    public function getAttributes()
    {
        return $this->entity->getAttributes();
    }

    /**
     * @param ProductInterface|ApiProductInterface $product
     *
     * @return Item
     */
    public function setProduct($product)
    {
        $productEntity = $product;
        // if api-product - temporarily save
        if ($product instanceof ApiProductInterface) {
            $this->tempProduct = $product;
            $productEntity = $product->getEntity();
        }
        $this->entity->setProduct($productEntity);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("product")
     * @Groups({"Default","cart"})
     *
     * @return ApiProductInterface
     */
    public function getProduct()
    {
        if ($this->tempProduct) {
            return $this->tempProduct;
        }

        $product = $this->entity->getProduct();
        if ($product) {
            return $this->productFactory->createApiEntity($product, $this->locale);
        }

        return null;
    }

    /**
     * Set deliveryAddress
     *
     * @param OrderAddressEntity $deliveryAddress
     *
     * @return Item
     */
    public function setDeliveryAddress(OrderAddressEntity $deliveryAddress = null)
    {
        $this->entity->setDeliveryAddress($deliveryAddress);

        return $this;
    }

    /**
     * Get deliveryAddress
     *
     * @VirtualProperty
     * @SerializedName("deliveryAddress")
     * @Groups({"Default","cart"})
     *
     * @return OrderAddress $deliveryAddress
     */
    public function getDeliveryAddress()
    {
        $address = $this->entity->getDeliveryAddress();
        if ($address) {
            return new OrderAddress($address);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     * TODO: default-price EUR?
     */
    public function getCalcProduct()
    {
        if ($this->getProduct()) {
            return $this->getProduct()->getEntity();
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getCalcQuantity()
    {
        return $this->getQuantity();
    }

    /**
     * {@inheritDoc}
     */
    public function getCalcPriceGroup()
    {
        $supplier = $this->getSupplier();
        if ($supplier) {
            return $supplier->getId();
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getCalcDiscount()
    {
        return $this->getDiscount();
    }

    /**
     * {@inheritDoc}
     */
    public function getCalcCurrencyCode()
    {
        return $this->currency;
    }

    /**
     * {@inheritDoc}
     */
    public function getCalcPriceGroupContent()
    {
        $supplier = $this->getSupplier();
        if ($supplier) {
            return array(
                'id' => $supplier->getId(),
                'name' => $supplier->getName(),
            );
        }

        return null;
    }

    /**
     * Get price changes
     *
     * @VirtualProperty
     * @SerializedName("priceChange")
     * @Groups({"Default","cart"})
     *
     * @return array|null
     */
    public function getPriceChange()
    {
        if ($this->priceChanged) {
            return array(
                'from' => $this->priceChangeFrom,
                'to' => $this->priceChangeTo
            );
        }

        return null;
    }

    /**
     * Set changed price (from, to) to an item
     *
     * @param float $from
     * @param float $to
     */
    public function setPriceChange($from, $to)
    {
        $this->priceChanged = true;
        $this->priceChangeFrom = $from;
        $this->priceChangeTo = $to;
    }

    /**
     * @VirtualProperty
     * @SerializedName("costCentre")
     * @Groups({"Default","cart"})
     *
     * @return string
     */
    public function getCostCentre()
    {
        return $this->entity->getCostCentre();
    }

    /**
     * @param string $costCentre
     *
     * @return self
     */
    public function setCostCentre($costCentre)
    {
        $this->entity->setCostCentre($costCentre);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("isRecurringPrice")
     * @Groups({"Default","cart"})
     *
     * @return string
     */
    public function isRecurringPrice()
    {
        return $this->entity->isRecurringPrice();
    }

    /**
     * @param bool $isRecurringPrice
     *
     * @return bool
     */
    public function setIsRecurringPrice($isRecurringPrice)
    {
        return $this->entity->setIsRecurringPrice($isRecurringPrice);
    }

    /**
     * {@inheritdoc}
     *
     * @VirtualProperty
     * @SerializedName("type")
     * @Groups({"Default","cart"})
     */
    public function getType()
    {
        return $this->entity->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->entity->setType($type);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddon()
    {
        return $this->entity->getAddon();
    }

    /**
     * {@inheritdoc}
     */
    public function setAddon(Addon $addon = null)
    {
        $this->entity->setAddon($addon);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->entity->getParent();
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(ItemInterface $parent = null)
    {
        $this->entity->setParent($parent);

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("parentId")
     * @Groups({"Default","cart"})
     *
     * {@inheritdoc}
     */
    public function getParentId()
    {
        if ($this->entity->getParent()) {
            return $this->entity->getParent()->getId();
        }

        return null;
    }
}
