<?php

namespace Lancms\Kiosk\Api;

/**
 * Interface Kiosk
 * 
 * Represents a Kiosk.
 * 
 * @author edvin
 * @package Kiosk.api
 */
interface Kiosk {

    /**
     * Provides the name of this kiosk.
     * 
     * @return name
     */
    public function getName();

    /**
     * Indicates if this kiosk is open.
     * 
     * @return bool
     */
    public function isOpen();

    /**
     * Indicates if this kiosk allows to add to a credit.
     * 
     * @return bool
     */
    public function allowCredit();

}

