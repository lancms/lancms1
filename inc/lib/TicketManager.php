<?php

require __DIR__ . "/Ticket.php";
require __DIR__ . "/TicketType.php";

class TicketManager {

	protected static $_instance;

    protected $_ticketTypes;

    function __construct() {
        $this->_ticketTypes = array(); // Initialize runtime cache of ticketTypes.
    }

	/**
	 * @return TicketManager
	 */
	public static function getInstance() {
		if (self::$_instance == null)
			self::$_instance = new self();

		return self::$_instance;
	}

	/**
	 * Provides an array of tickets on a user. Will search fields "user" and "owner"
	 * 
	 * @param int $userID
	 * @param int $eventID If null then active event is used.
	 * @return Ticket[]
	 */
	public function getTicketsOfUser($userID, $eventID=null) {
		global $sessioninfo, $sql_prefix;

		if ($eventID == null) {
			$eventID = $sessioninfo->eventID;
		}

		$result = db_query(sprintf("SELECT * FROM `%s_tickets` WHERE `eventID` = %d AND (`user` = %d OR `owner` = %d)", $sql_prefix, $eventID, $userID, $userID));
		$num   = db_num($result);

		$tickets = array();
		if ($num > 0) {
			$i = 0;
			while ($row = db_fetch_assoc($result)) {
				$tickets[$i] = new Ticket($row['ticketID']);
				$tickets[$i]->fillInfo($row);

				$i++;
			}
		}

		return $tickets;
	}

    /**
     * Provides the ticket type by ID.
     * 
     * @param int $ticketTypeID
     * @param int $eventID If null then active event is used.
     * @return TicketType
     */
    public function getTicketTypeByID($ticketTypeID, $eventID=null) {
        global $sessioninfo, $sql_prefix;

        if (intval($ticketTypeID) < 1)
            return null;

        if (array_key_exists($ticketTypeID, $this->_ticketTypes)) {
            return $this->_ticketTypes[$ticketTypeID];
        }

        if ($eventID == null) {
            $eventID = $sessioninfo->eventID;
        }

        $result = db_query(sprintf("SELECT * FROM `%s_ticketTypes` WHERE `eventID` = %d AND ticketTypeID = %d", $sql_prefix, $eventID, $ticketTypeID));
        $num   = db_num($result);

        $ticketType = null;
        if ($num > 0) {
            $row = db_fetch_assoc($result);
            $ticketType = new TicketType($row['ticketTypeID']);
            $ticketType->fillInfo($row); 

            // Store to runtime cache
            $this->_ticketTypes[$row['ticketTypeID']] = $ticketType;
        }

        return $ticketType;
    }
	
}