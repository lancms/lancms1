<?php

namespace Lancms\Kiosk\Api;

interface PurchaseResult
{

    const STATUS_SUCCESS    = "ok";
    const STATUS_FAILURE    = "error";

    /**
     * Provides the status of this result, see STATUS_* constants.
     * 
     * @return string
     */
    public function getStatus();

    /**
     * If any error occurd this will return an code of the error.
     * When there is no error this method should return null.
     * 
     * @return string|null
     */
    public function getErrorCode();

    /**
     * If any error occurd this will return a detailed message of the error.
     * When there is no error this method should return null.
     * 
     * @return string|null
     */
    public function getErrorMessage();

    /**
     * Provides the relating kiosk session.
     * 
     * @return KioskSession
     */
    public function getKioskSession();

}
