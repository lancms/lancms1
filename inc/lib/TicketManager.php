<?php

require __DIR__ . "/Ticket.php";
require __DIR__ . "/TicketType.php";
require __DIR__ . "/TicketSeat.php";

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
     * Provides an ticket by ID.
     *
     * @see getTickets()
     * @param int $ticketID
     * @param int $eventID If null then active event is used.
     * @return Ticket
     */
    public function getTicket($ticketID, $eventID=null) {
        $res = $this->getTickets(array($ticketID), $eventID);
        return count($res) > 0 ? $res[0] : null;
    }

    /**
     * Provides an array of tickets by ID in the array provided.
     *
     * @param array $ticketIDs Array of IDs.
     * @param int $eventID If null then active event is used.
     * @return Ticket[]
     */
    public function getTickets($ticketIDs, $eventID=null) {
        global $sessioninfo, $sql_prefix;

        if (is_array($ticketIDs) == false || count($ticketIDs) < 1)
            return array();

        if ($eventID == null) {
            $eventID = $sessioninfo->eventID;
        }

        $result = db_query(sprintf("SELECT * FROM `%s_tickets` WHERE `eventID` = %d AND `ticketID` IN (%s)", $sql_prefix, $eventID, implode(",", $ticketIDs)));
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
     * Provides an array of tickets by md5 ID in the array provided.
     *
     * @param array $ticketMD5s Array of md5.
     * @param int $eventID If null then active event is used.
     * @return Ticket[]
     */
    public function getTicketsByMD5($ticketMD5s, $eventID=null) {
        global $sessioninfo, $sql_prefix;

        if (is_array($ticketMD5s) == false || count($ticketMD5s) < 1)
            return array();

        if ($eventID == null) {
            $eventID = $sessioninfo->eventID;
        }

        $result = db_query(sprintf("SELECT * FROM `%s_tickets` WHERE `eventID` = %d AND `md5_ID` IN ('%s')", $sql_prefix, $eventID, implode("','", $ticketMD5s)));
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
     * Provides an array of tickets by order reference.
     *
     * @param string $orderReference the order reference
     * @param int $eventID If null then active event is used.
     * @return Ticket[]
     */
    public function getTicketsByOrderReference($orderReference, $eventID=null) {
        global $sessioninfo, $sql_prefix;

        if (empty($orderReference))
            return array();

        if ($eventID == null) {
            $eventID = $sessioninfo->eventID;
        }

        $result = db_query(sprintf("SELECT * FROM `%s_tickets` WHERE `orderReference` = '%s'",
            $sql_prefix, $eventID, db_escape($orderReference)));
        $num = db_num($result);

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
     * Shorthand for fetching a single ticket by md5 ID.
     *
     * @see getTicketsByMD5()
     * @param string $ticketMD5
     * @param int|null $eventID
     * @return Ticket|null
     */
    public function getTicketByMD5($ticketMD5, $eventID=null) {
        $result = $this->getTicketsByMD5(array($ticketMD5), $eventID);
        return (is_array($result) && count($result) > 0 ? $result[0] : null);
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

    /**
     * Provides the ticket types
     *
     * @param array|null $ids Filter by IDs.
     * @param int $eventID If null then active event is used.
     * @return TicketType
     */
    public function getTicketTypes($ids = null, $eventID=null) {
        global $sessioninfo, $sql_prefix;

        if ($eventID == null) {
            $eventID = $sessioninfo->eventID;
        }

        $query = sprintf('SELECT * FROM `%s_ticketTypes` WHERE `eventID` = %d', $sql_prefix, $eventID);

        if ((is_array($ids)) && (count($ids) > 0)) {
            $query .= sprintf(' AND ticketTypeID IN (%s)', db_escape(implode(',', $ids)));
        }

        $result = db_query($query);

        $ticketTypes = array();
        if (db_num($result) > 0) {
            while($row = db_fetch_assoc($result)) {
                $ticketTypes[] = (new TicketType($row['ticketTypeID']))->fillInfo($row);
            }
        }

        return $ticketTypes;
    }

    /**
     * @param int $userID
     * @param int $eventID
     * @param string $ticketID
     * @param int $timestamp
     * @param int $externalTime
     * @param string $externalRef
     * @param string $status
     * @param TicketType $ticketType
     * @param int $price
     * @param int $amount Default 1
     */
    public function logTicketPurchase($userID, $eventID, $ticketID, $timestamp, $externalTime, $externalRef, $status, TicketType $ticketType, $price, $amount=1, $externalRef2 = '') {
        // Integers
        $userID = intval($userID);
        $eventID = intval($eventID);
        $timestamp = intval($timestamp);
        $externalTime = intval($externalTime);
        $amount = intval($amount);

        // Strings, misc.
        $externalRef = db_escape($externalRef);
        $status = db_escape($status);
        $price = db_escape($price);
        $ticketID = db_escape($ticketID);

        $query = sprintf("INSERT INTO `%s` (`ticketType`, `eventID`, `userID`, `ticketID`, `timestamp`, `externalTime`, `externalRef`, `externalRef2`, `price`, `amount`, `status`)
VALUES (%d, %d, %d, '%s', %d, %d, '%s', '%s', %d, '%s');", db_prefix() . "_ticketLogs", $ticketType->getTicketTypeID(), $eventID, $userID, $ticketID,
            $timestamp, $externalTime, $externalRef, $price, $amount, $status, db_escape($externalRef2));

        db_query($query);
    }

}
