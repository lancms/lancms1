<?php

/**
 * Represents a ticket that is attatched to a user and owner. TicketType represents a ticket that can be ordered.
 *
 * @author edvin
 */
class Ticket extends SqlObject {

    const TICKET_STATUS_NOTPAID = 'notpaid';
    const TICKET_STATUS_NOTUSED = 'notused';
    const TICKET_STATUS_USED    = 'used';
    const TICKET_STATUS_ARRIVED = 'arrived';
    const TICKET_STATUS_DELETED = 'deleted';

    protected $_seat;

    function __construct($id) {
        $this->_seat = -1; // Initial value, used to determine if there is a runtime cached seat object.
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
     * Provides tthe md5 ID of this ticket.
     *
     * @return string
     */
    public function getMd5ID() {
        return $this->_getField("md5_ID", "INVALID_tID", 1);
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
     * Provides the owner user object of this ticket.
     *
     * @return User|null
     */
    public function getOwner() {
        return UserManager::getInstance()->getUserByID($this->getOwnerID());
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
    public function getCreatorID() {
        return $this->_getField("creator", 0, 2);
    }

    /**
     * Provides the creator user object of this ticket.
     *
     * @return User|null
     */
    public function getCreator() {
        return UserManager::getInstance()->getUserByID($this->getCreatorID());
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
     * Provides the user user object of this ticket.
     *
     * @return User|null
     */
    public function getUser() {
        return UserManager::getInstance()->getUserByID($this->getUserID());
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
     * Provides the seat object for this ticket if any.
     *
     * @return TicketSeat|null
     */
    public function getSeat() {
        global $sql_prefix;

        if ($this->_seat != -1) {
            return $this->_seat;
        }

        $this->_seat = null;
        $result = db_query(sprintf("SELECT * FROM %s_seatReg_seatings WHERE ticketID = %d", $sql_prefix, $this->getTicketID()));
        if (db_num($result) > 0) {
            $row = db_fetch_assoc($result);
            $this->_seat = new TicketSeat($row['seatingID']);
            $this->_seat->fillInfo($row);
        }

        return $this->_seat;
    }

    /**
     * Indicates if this ticket has a seat on the seatmap.
     *
     * @see getSeat()
     * @return bool
     */
    public function hasSeat() {
        return ($this->getSeat() instanceof TicketSeat) ? true : false;
    }

    /**
     * Indicates if this ticket can be seated.
     *
     * @see getSeat()
     * @return bool
     */
    public function canSeat() {
        /*

            if ticket.isPaid:
                return true

            else:
                canSeat = true
                loop user.tickets
                    if ticket.isPaid is False AND ticket.hasSeat is True:
                        canSeat = false

                return canSeat

        */

        if ($this->isPaid()) {
            // This ticket is paid, user can seat it.
            return true;
        }

        $canSeat = true;

        // Fetch all tickets on user.
        $tickets = TicketManager::getInstance()->getTicketsOfUser($this->getUserID());

        if (is_array($tickets) && count($tickets) > 0) {
            foreach ($tickets as $ticket) {
                if (!$ticket->isPaid() && $ticket->hasSeat()) {
                    $canSeat = false;
                }
            }
        }

        return $canSeat;
    }

    /**
     * Sets this ticket as used and updated the database.
     */
    public function setUsed() {
        $this->_setField('status', self::TICKET_STATUS_USED);
        $this->commitChanges();
    }

    /**
     * Set this ticket as deleted.
     * Will also commit changes.
     */
    public function setDeleted() {
        $this->_setField('status', self::TICKET_STATUS_DELETED);
        $this->commitChanges();
    }

    /**
     * Sets this ticket as paid and updates the database.
     */
    public function setPaid() {
        $this->_setField('paid', 'yes');
        $this->commitChanges();
    }

    /**
     * Sets this ticket as unpaid and updates the database.
     */
    public function setUnPaid() {
        $this->_setField('paid', 'no');
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

    /**
     * Remove any seat this ticket has.
     *
     * @return bool
     */
    public function removeSeat() {
        if ($this->getSeat() instanceof TicketSeat) {
            $this->getSeat()->deleteSeat();
            return true;
        }

        return false;
    }

    public function setIsArrived()
    {
        $this->_setField('status', self::TICKET_STATUS_ARRIVED);
    }

    public function hasArrived(): bool
    {
        return $this->_getField('status') === self::TICKET_STATUS_ARRIVED;
    }

    /**
     * Deletes this ticket.
     *
     * @return bool
     */
    public function deleteTicket() {
        // Delete seat if any.
        $this->removeSeat();

        // Delete the ticket from database.
        $res = db_query(sprintf("DELETE FROM %s_tickets WHERE ticketID = %d AND eventID = %d", db_prefix(), $this->getTicketID(), $this->getEventID()));
        if ($res !== true) {
            return false;
        }

        return true;
    }

}
