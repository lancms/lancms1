<?php

require __DIR__ . "/User.php";

class UserManager {

    protected static $_instance;

    protected $_users;

    function __construct() {
        $this->_users = array(); // Initialize runtime cache of ticketTypes.
    }

    /**
     * @return UserManager
     */
    public static function getInstance() {
        if (self::$_instance == null)
            self::$_instance = new self();

        return self::$_instance;
    }

    /**
     * Provides an user from the database by ID. Will return null if the user is not found or the
     * argument is not an int over zero.
     * 
     * @param int $userID
     * @return User|null
     */
    public function getUserByID($userID) {
        global $sql_prefix;

        if (intval($userID) < 1)
            return null;

        if (array_key_exists($userID, $this->_users)) {
            return $this->_users[$userID];
        }

        $user = null;
        $result = db_query(sprintf("SELECT * FROM `%s_users` WHERE `ID`=%d", $sql_prefix, $userID));
        if (db_num($result) > 0) {
            $row = db_fetch_assoc($result);
            $user = new User($row['ID']);
            $user->fillInfo($row);

            $this->_users[$row['ID']] = $user;
        }

        return $user;
    }

    /**
     * Provides an array of users matching $str
     * 
     * @param string $str
     * @return array
     */
    public function searchUsers($str) {
        return user_find($str);
    }

}