<?php

namespace Lancms;

class KioskSession
{
    /**
     * @var string
     */
    private $sqlPrefix;

    /**
     * @var string
     */
    private $sessionId;

    /**
     * @var Kiosk
     */
    private $kiosk;

    public function __construct(Kiosk $kiosk, $sessionId)
    {
        $this->kiosk = $kiosk;
        $this->sqlPrefix = db_prefix();
        $this->sessionId = $sessionId;
    }

    /**
     * Provides products in this session.
     *
     * @return KioskSessionProduct[]
     */
    public function getCartProducts(): array
    {
        $query = db_query(sprintf(
            "SELECT * FROM %s_kiosk_shopbasket WHERE sID = '%s'",
            $this->sqlPrefix,
            db_escape($this->sessionId)
        ));

        return array_map(function($row) {
            return new KioskSessionProduct(
                $this->sessionId,
                array_first($this->kiosk->getProduct($row->wareID)),
                $row->amount
            );
        }, db_fetch_all($query));
    }

    public function getTotalSumPrice(): float
    {
        return array_reduce(
            $this->getCartProducts(),
            function(float $carry, KioskSessionProduct $product) {
                return $carry + $product->getPrice();
            },
            0.0
        );
    }
}
