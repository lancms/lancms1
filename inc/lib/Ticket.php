<?php

class Ticket extends SqlObject {

    const TICKET_STATUS_NOTPAID = 'notpaid';
    const TICKET_STATUS_NOTUSED = 'notused';
    const TICKET_STATUS_USED    = 'used';
    const TICKET_STATUS_DELETED = 'deleted';
    
    function __construct($id) {
        parent::__construct("tickets", "ticketID", $id);
    }

    /**
     * Provides the ticket ID.
     *
     * @return int
     */
    public function getTicketID() {
        return $this->getObjectID();
    }

    /**
     * Provides ID of the event the ticket was created in.
     *
     * @return int
     */
    public function getEventID() {
        return $this->_getField("eventID", 0, 2);
    }

    /**
     * Provides the ticket type ID for this ticket.
     *
     * @return int
     */
    public function getTicketTypeID() {
        return $this->_getField("ticketType", 0, 2);
    }

    /**
     * Provides the ticket type object for this ticket.
     *
     * @return TicketType
     */
    public function getTicketType() {
        return TicketManager::getInstance()->getTicketTypeByID($this->getTicketTypeID());
    }

    /**
     * Provides the owner user ID of this ticket.
     *
     * @return int
     */
    public function getOwnerID() {
        return $this->_getField("owner", 0, 2);
    }

    /**
     * Provides the UNIX timestamp this ticket was created.
     *
     * @return int
     */
    public function getCreateTime() {
        return $this->_getField("createTime", 0, 2);
    }

    /**
     * Provides the creator user ID of this ticket.
     *
     * @return int
     */
    public function getCreator() {
        return $this->_getField("creator", 0, 2);
    }

    /**
     * Provides the user of this ticket's user ID.
     *
     * @return int
     */
    public function getUserID() {
        return $this->_getField("user", 0, 2);
    }

    /**
     * Provides the current status of this ticket, see the TICKET_STATUS_
     *
     * @return int
     */
    public function getStatus() {
        return $this->_getField("status");
    }

    /**
     * Provides if this ticket is paid.
     * 
     * @return boolean
     */
    public function isPaid() {
        return $this->_getField("paid") === 'yes' ? true : false;
    }

    /**
     * Provides the UNIX timestamp of when this ticket was marked as paid.
     * 
     * @return int
     */
    public function getPaidTime() {
        return $this->_getField("paidTime", 0, 2);
    }

    /**
     * Sets this ticket as paid and updates the database.
     */
    public function setPaid() {
        $this->_setField('paid', 'yes');
        $this->commitChanges();
    }

    /**
     * Sets a new user of this ticket, you must call commitChanges() after setting values.
     * 
     * @param int $userID
     */
    public function setUser($userID) {
        $this->_setField('user', intval($userID));
    }

    /**
     * Sets a new owner of this ticket, you must call commitChanges() after setting values.
     * 
     * @param int $userID
     */
    public function setOwner($userID) {
        $this->_setField('owner', intval($userID));
    }

}