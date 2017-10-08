<?php

namespace Lancms;

use SqlObject;

class KioskProduct extends SqlObject
{
    /**
     * @param int $id
     */
    public function __construct($id = -1)
    {
        parent::__construct('kiosk_wares', 'ID', $id);
    }

    public function getId(): int
    {
        return $this->_getField('ID', 0, 2);
    }

    public function getName(): string
    {
        return $this->_getField('name');
    }

    public function setName(string $name): void
    {
        $this->_setField('name', $name);
    }

    public function getPrice(): float
    {
        return (float) $this->_getField('price', -1, 2);
    }

    public function setPrice(float $price): void
    {
        $this->_setField('price', $price);
    }
}
