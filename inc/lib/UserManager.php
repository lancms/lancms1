<?php

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
     * Provides multiple users by array of IDs
     *
     * @param array $userIDs
     * @return User[]|null
     */
    public function getUsersByID($userIDs) {
        if (!is_array($userIDs))
            return null;

        $userIDs = array_map("intval", $userIDs);
        $userIDs = array_filter($userIDs);

        if (count($userIDs) < 1)
            return array();

        $users = null;
        $result = db_query(sprintf("SELECT * FROM `%s_users` WHERE `ID` IN (%s)", db_prefix(), implode(",", $userIDs)));
        if (db_num($result) > 0) {
            while ($row = db_fetch_assoc($result)) {
                $user = new User($row['ID']);
                $user->fillInfo($row);

                $this->_users[$row['ID']] = $user;
                $users[] = $user;
            }
        }

        return $users;
    }

    /**
     * Provides an user from the database by ID. Will return null if the user is not found or the
     * argument is not an int over zero.
     *
     * @param int $userID
     * @return User|null
     */
    public function getUserByID($userID) {
        $users = $this->getUsersByID(array($userID));
        return isset($users[0]) ? $users[0] : null;
    }

    /**
     * Provides online user objects.
     *
     * @return User
     * @throws Exception
     */
    public function getOnlineUser() {
        global $sessioninfo;

        $user = $this->getUserByID($sessioninfo->userID);

        if (!$user instanceof User) {
            throw new Exception("No user found in session.");
        }

        return $user;
    }

    /**
     * Provides an user from the database by username or nick.
     *
     * @param string $userNick
     * @return User|null
     */
    public function getUserByNick($userNick) {
        global $sql_prefix;

        if (strlen(trim($userNick)) < 1)
            return null;

        $user = null;
        $result = db_query(sprintf("SELECT * FROM `%s_users` WHERE `nick`='%s'", $sql_prefix, db_escape($userNick)));
        if (db_num($result) > 0) {
            $row = db_fetch_assoc($result);
            $user = new User($row['ID']);
            $user->fillInfo($row);

            $this->_users[$row['ID']] = $user;
        }

        return $user;
    }

    /**
     * Indicates if a user exists in the database by email.
     *
     * @param string $userEmail The email
     * @return User|null
     */
    public function userExistsByEmail($userEmail) {
        global $sql_prefix;

        if (strlen(trim($userEmail)) < 1)
            return false;

        // FIXME: Should `EMailConfirmed` = 1 be in the where statement?
        $result = db_query(sprintf("SELECT * FROM `%s_users` WHERE `EMail`='%s'", $sql_prefix, db_escape($userEmail)));
        if (db_num($result) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Provides an user from the database by email verification code. If null is returned the code might be invalid.
     *
     * @param string $userCode
     * @return User|null
     */
    public function getUserByVerificationCode($userCode) {
        global $sql_prefix;

        if (strlen(trim($userCode)) < 1)
            return null;

        $user = null;
        $result = db_query(sprintf("SELECT * FROM `%s_users` WHERE `EMailVerifyCode`='%s'", $sql_prefix, $userCode));
        if (db_num($result) > 0) {
            $row = db_fetch_assoc($result);
            $user = new User($row['ID']);
            $user->fillInfo($row);

            $this->_users[$row['ID']] = $user;
        }

        return $user;
    }

    /**
     * Creates an user into the database, returns the User object if the user was created otherwise null.
     *
     * @param string $nick
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function createUser($nick, $firstName, $lastName, $email, $password) {
        global $sql_prefix;

        $data = array(
            'nick' => $nick,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'EMail' => $email,
            'password' => md5($password),
            'registerIP' => getUserIP(),
            'registerTime' => time(),
            'EMailConfirmed' => 0
        );

        $q = "INSERT INTO `" . $sql_prefix . "_users`(`" . implode("`,`", array_keys($data)) . "`)VALUES(";

        // Insert values
        $values = array();
        foreach ($data as $key => $value) {
            $value = db_escape($value);

            if (is_numeric($value)) {
                $values[] = $value;
            } else {
                $values[] = "'" . $value . "'";
            }
        }

        $q .= implode(",", $values) . ")";
        $result = db_query($q);
        $lastInsertID = db_insert_id();

        if ($lastInsertID < 1)
            return false;

        return $this->getUserByID($lastInsertID);
    }

    /**
     * @param User $user
     * @return boolean
     */
    public function sendEmailVerification(User $user) {
        $code = md5(rand(1,10000) * time());

        $user->setEmailVerifyCode($code);
        $user->setEmailConfirmed(false);
        $user->commitChanges();

        $url = getUrlBase();
        $verifyUrl = $url . "/index.php?module=register&action=verifymail&verifycode=" . $code;

        $message = sprintf("Hello %s

You, or someone has created an account on %s with this e-mail address.

To verify your account please open the web address under (or click it)
%s

Sent by LANCMS to %s because the user %s was created on the site %s at %s.",
            $user->getFullName(),
            $url,
            $verifyUrl,
            $user->getEmail(),
            $user->getNick(),
            $url,
            date("d.m.Y H:i:s", $user->getRegisterTime()));

        mail($user->getEmail(), "Verify your user account", $message);
        return true;
    }

    /**
     * Save form data in session.
     *
     * @param string $formName The unique identifier to this form.
     * @param array $data This array will be serialized into session.
     */
    public function saveFormInSession($formName, array $data) {
        $_SESSION[$formName] = serialize($data);
    }

    /**
     * Provides the saved form data if any of form name.
     * Returns an empty array if form name is not found in session.
     *
     * @param string $formName The unique identifier to this form.
     * @return array
     */
    public function getFormInSession($formName) {
        if (isset($_SESSION[$formName])) {
            return unserialize($_SESSION[$formName]);
        }

        return array();
    }

    /**
     * Will remove the saved form data from session by form name.
     * This method is void.
     *
     * @param string $formName The unique identifier to this form.
     */
    public function resetFormInSession($formName) {
        if (isset($_SESSION[$formName])) {
            unset($_SESSION[$formName]);
        }
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
