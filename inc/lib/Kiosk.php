<?php

namespace Lancms;
use Lancms\KioskProduct;

class Kiosk
{
    /**
     * @var string
     */
    private $sqlPrefix;

    /**
     * @var KioskProduct[]|null
     */
    private $products = null;

    public function __construct()
    {
        $this->sqlPrefix = db_prefix();
    }

    private function fetchAllProducts(): void
    {
        if (is_null($this->products)) {
            $qProducts = db_query(sprintf("SELECT * FROM %s_kiosk_wares", $this->sqlPrefix));

            while($product = db_fetch_assoc($qProducts)) {
                $id = (int) $product['ID'];
                $this->products[$id] = $this->createProduct($product);
            }
        }
    }

    public function search($query): array
    {
        $qProducts = db_query('SELECT * FROM ' . db_prefix() . '_kiosk_wares WHERE name LIKE \'%' . db_escape($query) . '%\'');

        return array_map([ $this, 'createProduct' ], db_fetch_all($qProducts));
    }

    /**
     * Provides a product.
     *
     * @param int[] ...$ids
     *
     * @return KioskProduct[]
     */
    public function getProduct(...$ids): array
    {
        $this->fetchAllProducts();

        return array_filter(array_map(function($id) {
            return $this->products[intval($id)] ?? null;
        }, $ids));
    }

    private function createProduct($definition): KioskProduct
    {
        if (!is_array($definition)) $definition = (array) $definition;

        $instance = new KioskProduct($definition['ID']);
        $instance->fillInfo($definition);

        return $instance;
    }
}
