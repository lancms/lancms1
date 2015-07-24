<?php

/**
 * Represents a ticket type in the CMS. Contains the price and type of ticket.
 * 
 * @author edvin
 */
class TicketType extends SqlObject {

    const TICKET_TYPE_ONSITECOMPUTER = 'onsite-computer';
    const TICKET_TYPE_ONSITEVISITOR  = 'onsite-visitor';
    const TICKET_TYPE_PREPAID        = 'prepaid';
    const TICKET_TYPE_PREORDER       = 'preorder';
    const TICKET_TYPE_RESELLER       = 'reseller';
    
    function __construct($id) {
        parent::__construct("ticketTypes", "ticketTypeID", $id);
    }

    /**
     * Provides the ticket type ID.
     *
     * @return int
     */
    public function getTicketTypeID() {
        return $this->getObjectID();
    }

    /**
     * Provides ID of the event the ticket type belongs to
     *
     * @return int
     */
    public function getEventID() {
        return $this->_getField("eventID", 0, 2);
    }

    /**
     * Provides the name of this ticket type.
     *
     * @return string
     */
    public function getName() {
        return $this->_getField("name");
    }

    /**
     * Sets new name of this ticket type.
     *
     * @param string $newName
     */
    public function setName($newName) {
        $this->_setField("name", $newName);
    }

    /**
     * Provides the type of ticket type, see TICKET_TYPE_* constats in class.
     *
     * @return string
     */
    public function getType() {
        return $this->_getField("type");
    }

    /**
     * Provides the price of this ticket type.
     * 
     * @return int
     */
    public function getPrice() {
        return $this->_getField("price", 0, 2);
    }

    /**
     * Set new price for ticket type.
     * 
     * @param int $newPrice
     */
    public function setPrice($newPrice) {
        $this->_setField("price", $newPrice);
    }

    /**
     * Indicates if this ticket type is active/enabled.
     * 
     * @return boolean
     */
    public function isEnabled() {
        return $this->_getField("active", false, 3);
    }

    /**
     * Provides the max available tickets.
     * 
     * @return int
     */
    public function getMaxTickets() {
        return $this->_getField("maxTickets", 0, 2);
    }

    /**
     * Set new maxTickets for ticket type.
     * 
     * @param int $arg
     */
    public function setMaxTickets($arg) {
        $this->_setField("maxTickets", $arg);
    }
    
}