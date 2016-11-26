<?php

/**
 * Represents a user in lancms.
 * 
 * @author edvin
 */
class User extends SqlObject {

    protected $_tickets;
    
    function __construct($id) {
        $this->_tickets = null;

        parent::__construct("users", "ID", $id);
    }

    /**
     * Provides the user ID
     * 
     * @return int
     */
    public function getUserID() {
        return $this->getObjectID();
    }

    /**
     * Provides the nick of this user.
     * 
     * @return string
     */
    public function getNick() {
        return $this->_getField('nick');
    }

    /**
     * Provides the email address of this user.
     * 
     * @return string
     */
    public function getEmail() {
        return $this->_getField('EMail');
    }

    /**
     * Provides this users first name. Use getFullName() to get both first and last name in one string.
     * 
     * @see getFullName()
     * @return string
     */
    public function getFirstName() {
        return $this->_getField('firstName');
    }

    /**
     * Provides this users last name. Use getFullName() to get both first and last name in one string.
     * 
     * @see getFullName()
     * @return string
     */
    public function getLastName() {
        return $this->_getField('lastName');
    }

    /**
     * Provides the full name of this user, will join getFirstName() and getLastName() together.
     * 
     * @see getFirstName()
     * @see getLastName()
     * @return string
     */
    public function getFullName() {
        return sprintf("%s %s", $this->getFirstName(), $this->getLastName());
    }

    /**
     * Provides the post number (zip-code) of this user. Do not treat this variable as a int as
     * norwegian postcodes can start with zero.
     * 
     * @return string
     */
    public function getPostNumber() {
        return $this->_getField('postNumber');
    }

    /**
     * Provides the post district of this user.
     * 
     * @return string
     */
    public function getPostPlace() {
        return $this->_getField('postPlace');
    }

    /**
     * Provides this users street address.
     * 
     * @return string
     */
    public function getStreetAddress() {
        return $this->_getField('street');
    }

    /**
     * Provides this users street address number 2.
     * 
     * @return string
     */
    public function getStreetAddress2() {
        return $this->_getField('street2');
    }

    /**
     * Provides the UNIX timestamp of when this user is registered.
     * 
     * @return int
     */
    public function getRegisterTime() {
        return $this->_getField('registerTime', 0, 2);
    }

    /**
     * Provides the IP address of this user when it was created.
     * 
     * @return string
     */
    public function getRegisterIP() {
        return $this->_getField('registerIP');
    }

    /**
     * Indicates if this user has its user info verified.
     * 
     * @return bool
     */
    public function isUserInfoVerified() {
        return $this->_getField('userInfoVerified', false, 3);
    }

    /**
     * Provides the gender of this user. Male, Female or Other.
     * 
     * @return string
     */
    public function getGender() {
        return $this->_getField('gender');
    }

    /**
     * Provides mobile phone number of this user.
     * 
     * @return string
     */
    public function getCellPhone() {
        return $this->_getField('cellphone');
    }

    /**
     * Provides the birthday year of this user.
     * 
     * @return int
     */
    public function getBirthYear() {
        return $this->_getField('birthYear');
    }

    /**
     * Provides the birthday month of this user.
     * 
     * @return int
     */
    public function getBirthMonth() {
        return $this->_getField('birthMonth');
    }

    /**
     * Provides the birthday day of this user.
     * 
     * @return int
     */
    public function getBirthDay() {
        return $this->_getField('birthDay');
    }

    /**
     * Provides the birthday timestamp.
     * 
     * @return int
     */
    public function getBirthdayTimestamp() {
        return mktime(0, 0, 0, $this->getBirthMonth(), $this->getBirthDay(), $this->getBirthYear());
    }

    /**
     * Provides the current age of this user.
     *
     * @return int
     */
    public function getAge() {
        $tz  = new DateTimeZone('Europe/Oslo');
        $then = new DateTime("@" . $this->getBirthdayTimestamp(), $tz);
        return $then->diff(new DateTime("now", $tz))->y;
    }

    /**
     * Indicates if this user is verified.
     * 
     * @return bool
     */
    public function isEmailConfirmed() {
        return $this->_getField('EMailConfirmed', false, 3);
    }

    /**
     * Provides the email verification code.
     * 
     * @return string
     */
    public function getEmailVerifyCode() {
        return $this->_getField('EMailVerifyCode');
    }

    /**
     * Provides all groups this user is a member of, only for current event.
     * 
     * @return UserGroup[]
     */
    public function getGroups()
    {
        return UserGroupManager::getInstance()->getUserGroups($this->getUserID());
    }

    /**
     * @return UserGroup[]
     */
    public function getGroupsWhereAdmin()
    {
        return UserGroupManager::getInstance()->getUserIsAdminGroups($this->getUserID());
    }

    /**
     * Provides all groups this user is a member of, only for current event.
     * 
     * @return UserGroup[]
     */
    public function isAdminOfGroup(UserGroup $group)
    {
        $adminOf = $this->getGroupsWhereAdmin();
        if (count($adminOf) > 0) {
            foreach ($adminOf as $group) {
                if ($adminOf->getGroupID() == $group->getGroupID())
                    return true;
            }
        }

        return false;
    }
    
    /**
     * Indicates if this user is a crew.
     *
     * @param int|null $eventId 
     *
     * @return boolean 
     */
    public function isCrew($eventId = null)
    {
        return is_user_crew($this->getUserID(), $eventId);
    }

    /**
     * Provides the tickets of this user in an array of Ticket objects.
     * 
     * @return Ticket[]
     */
    public function getTickets() {
        global $sql_prefix;

        if ($this->_tickets == null) {
            $this->_tickets = TicketManager::getInstance()->getTicketsOfUser($this->getUserID());
        }

        return $this->_tickets;
    }

    /**
     * Adds a tickettype to this user i.e. creates a ticket in the _tickets table.
     * 
     * <p>See validateAddTicketType to handle max amount of tickets an user can order, ment for "ticketorder" module.</p>
     * 
     * @see validateAddTicketType()
     * @param TicketType $ticketType The ticket type to add.
     * @param int $amount Amount to add.
     * @param User|null $creator If an moderator has added this ticket, send the mods User object.
     * @return array Array of the new tickets md5 ID.
     */
    public function addTicketType(TicketType $ticketType, $amount = 1, $creator=null) {
        global $sessioninfo, $sql_prefix, $maxTicketsPrUser;

        if (($creator instanceof User) == false) {
            $creator = $this;
        }

        $insertIDs = array();

        for ($i=0; $i < $amount; $i++) { 
            db_query(sprintf("INSERT INTO %s_tickets(`md5_ID`, `ticketType`, `eventID`, `owner`, `createTime`, `creator`, `user`)
                VALUES('%s', %d, %d, %d, %d, %d, %d)",
                $sql_prefix,
                md5(rand() . time() . $this->getUserID()),
                $ticketType->getTicketTypeID(),
                $sessioninfo->eventID,
                $this->getUserID(),
                time(),
                $creator->getUserID(),
                $this->getUserID()));

            // Find md5 ID from ticket ID
            $qTicketMd5ID = db_query(sprintf("SELECT `md5_ID` FROM %s_tickets WHERE `ticketID`=%s", $sql_prefix, db_insert_id()));
            if (db_num($qTicketMd5ID) < 0) continue;

            $rows = db_fetch_assoc($qTicketMd5ID);
            $insertIDs[] = $rows["md5_ID"];
        }        

        return $insertIDs;
    }

    /**
     * Adds a tickettype to user and checks if user has ordered the maximum allowed then calls addTicketType().
     * 
     * @see addTicketType
     * @param TicketType $ticketType The ticket type to add.
     * @param int $amount Amount to add.
     * @param User|null $creator If an moderator has added this ticket, send the mods User object.
     * @return bool False if amount is reached, true on success.
     */
    public function validateAddTicketType(TicketType $ticketType, $amount = 1, $creator=null) {
        global $sessioninfo, $sql_prefix, $maxTicketsPrUser;

        // Validate that user has not ordered too much.
        $hasAmount = 0;
        $canAmount = (isset($maxTicketsPrUser) && is_numeric($maxTicketsPrUser) ? intval($maxTicketsPrUser) : 1);
        $tickets = $this->getTickets();
        if (count($tickets) > 0) {
            foreach ($tickets as $value) {
                if ($value->getTicketTypeID() == $ticketType->getTicketTypeID()) {
                    $hasAmount++;
                }
            }
        }

        if ($hasAmount >= $canAmount)  
            return false;

        // Call Addtickettype too do the rest
        $insertIDs = $this->addTicketType($ticketType, $amount, $crator);
        return $insertIDs;
    }

    /**
     * Will compare this User object to another User object.
     * 
     * @return bool
     */
    public function equals(User $user) {
        return ($user->getUserID() == $this->getUserID());
    }

    //========================================================================
    // SETTERS

    /**
     * Set the post number (zip-code) of this user. Do not treat this variable as a int as
     * norwegian postcodes can start with zero.
     * 
     * @param string $arg
     */
    public function setPostNumber($arg) {
        return $this->_setField('postNumber', $arg);
    }

    /**
     * Set this users street address.
     * 
     * @param string $arg
     */
    public function setStreetAddress($arg) {
        $this->_setField('street', $arg);
    }

    /**
     * Set the gender of this user. Male, Female or Other
     * 
     * @param string $arg
     */
    public function setGender($arg) {
        $this->_setField('gender', $arg);
    }

    /**
     * Set the mobile phone number of this user.
     * 
     * @param string $arg
     */
    public function setCellPhone($arg) {
        $this->_setField('cellphone', $arg);
    }

    /**
     * Set the birthday year of this user.
     * 
     * @param int $arg
     */
    public function setBirthYear($arg) {
        $this->_setField('birthYear', $arg);
    }

    /**
     * Set the birthday month of this user.
     * 
     * @param int $arg
     */
    public function setBirthMonth($arg) {
        $this->_setField('birthMonth', $arg);
    }

    /**
     * Set the birthday day of this user.
     * 
     * @param int $arg
     */
    public function setBirthDay($arg) {
        $this->_setField('birthDay', $arg);
    }

    /**
     * Sets new email verification code.
     * 
     * @param boolean $arg
     */
    public function setEmailConfirmed($arg) {
        $this->_setField('EMailConfirmed', $arg == true ? 1 : 0);
    }

    /**
     * Sets new email verification code.
     * 
     * @param string $arg
     */
    public function setEmailVerifyCode($arg) {
        $this->_setField('EMailVerifyCode', $arg);
    }

}
