<?php

/**
 * Represents an seat that has been seated and that exists in the table seatReg_seatings
 * 
 * @author edvin
 */
class TicketSeat extends SqlObject {
    
    function __construct($id) {
        parent::__construct("seatReg_seatings", "seatID", $id);
    }

    public function getSeatID() {
        return $this->getObjectID();
    }

    public function getEventID() {
        return $this->_getField('eventID', 0, 2);
    }

    public function getSeatX() {
        return $this->_getField('seatX', 0, 2);
    }

    public function getSeatY() {
        return $this->_getField('seatY', 0, 2);
    }

    public function getTicketID() {
        return $this->_getField('ticketID', 0, 2);
    }

    public function getTicket() {
        return TicketManager::getInstance()->getTicket($this->getTicketID());
    }

    /**
     * Deletes the seat in the database.
     */
    public function deleteSeat() {
        db_query(sprintf("DELETE FROM %s_seatReg_seatings WHERE seatingID = %d AND eventID = %d AND ticketID = %d", db_prefix(), $this->getSeatID(), $this->getEventID(), $this->getTicketID()));
        return true;
    }

}
