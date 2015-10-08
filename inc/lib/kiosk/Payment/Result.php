<?php

namespace Lancms\Kiosk\Payment;

use Lancms\Kiosk\Api\PurchaseResult;
use Lancms\Kiosk\Api\KioskSession;

class Result implements PurchaseResult
{

    protected $_status;
    protected $_errorCode;
    protected $_errorMessage;
    protected $_session;

    public function __construct($status, $errorCode, $errorMessage, KioskSession $session)
    {
        $this->_status = $status;
        $this->_errorCode = $errorCode;
        $this->_errorMessage = $errorMessage;
        $this->_session = $session;
    }

    /**
     * Provides the status of this result, see STATUS_* constants.
     * 
     * @return string
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * If any error occurd this will return an code of the error.
     * When there is no error this method should return null.
     * 
     * @return string|null
     */
    public function getErrorCode()
    {
        return $this->_errorCode;
    }

    /**
     * If any error occurd this will return a detailed message of the error.
     * When there is no error this method should return null.
     * 
     * @return string|null
     */
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }

    /**
     * Provides the relating kiosk session.
     * 
     * @return KioskSession
     */
    public function getKioskSession()
    {
        return $this->_session;
    }

}
