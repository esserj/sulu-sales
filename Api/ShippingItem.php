<?php

namespace Sulu\Bundle\Sales\ShippingBundle\Api;

use JMS\Serializer\Annotation\VirtualProperty;
use Sulu\Bundle\Sales\CoreBundle\Api\Item;
use Sulu\Component\Rest\ApiWrapper;
use JMS\Serializer\Annotation\SerializedName;
use Sulu\Bundle\Sales\ShippingBundle\Entity\ShippingItem as ShippingItemEntity;

/**
 * Describes an item of a shipping
 * @package Sulu\Bundle\Sales\ShippingBundle\Api
 */
class ShippingItem extends ApiWrapper
{
    /**
     * @param ShippingItemEntity $entity
     * @param string $locale
     */
    public function __construct(ShippingItemEntity $entity, $locale) {
        $this->entity = $entity;
        $this->locale = $locale;
    }

    /**
     * Returns the id
     * @return int
     * @VirtualProperty
     * @SerializedName("id")
     */
    public function getId()
    {
        return $this->entity->getId();
    }

    /**
     * Set quantity
     *
     * @param float $quantity
     * @return ShippingItem
     */
    public function setQuantity($quantity)
    {
        $this->entity->setQuantity($quantity);

        return $this;
    }

    /**
     * Get quantity
     *
     * @return float
     * @VirtualProperty
     * @SerializedName("quantity")
     */
    public function getQuantity()
    {
        return $this->entity->getQuantity();
    }

    /**
     * Set note
     *
     * @param string $note
     * @return ShippingItem
     */
    public function setNote($note)
    {
        $this->entity->setNote($note);

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     * @VirtualProperty
     * @SerializedName("note")
     */
    public function getNote()
    {
        return $this->entity->getNote();
    }

    /**
     * Set shipping
     *
     * @param \Sulu\Bundle\Sales\ShippingBundle\Entity\Shipping $shipping
     * @return ShippingItem
     */
    public function setShipping(\Sulu\Bundle\Sales\ShippingBundle\Entity\Shipping $shipping)
    {
        $this->entity->setShipping($shipping);

        return $this;
    }

//    /**
//     * Get Shipping
//     * @return \Sulu\Bundle\Sales\ShippingBundle\Entity\Shipping
//     */
//    public function getShipping()
//    {
//        return $this->entity->getShipping();
//    }

    /**
     * Set item
     *
     * @param \Sulu\Bundle\Sales\CoreBundle\Entity\Item $item
     * @return ShippingItem
     */
    public function setItem(\Sulu\Bundle\Sales\CoreBundle\Entity\Item $item)
    {
        $this->entity->setItem($item);

        return $this;
    }

    /**
     * Get item
     *
     * @return \Sulu\Bundle\Sales\CoreBundle\Entity\Item
     * @VirtualProperty
     * @SerializedName("item")
     */
    public function getItem()
    {
        return new Item($this->entity->getItem(), $this->locale);
    }
}
