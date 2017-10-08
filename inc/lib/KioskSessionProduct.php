<?php

namespace Lancms;

class KioskSessionProduct
{
    /**
     * @var string
     */
    private $sId;

    /**
     * @var KioskProduct
     */
    private $product;

    /**
     * @var int
     */
    private $amount;

    public function __construct($sId, KioskProduct $product, int $amount = 0)
    {
        $this->sId = $sId;
        $this->product = $product;
        $this->amount = $amount;
    }

    public function getProduct(): KioskProduct
    {
        return $this->product;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getPrice(): float
    {
        return $this->product->getPrice() * $this->amount;
    }
}
