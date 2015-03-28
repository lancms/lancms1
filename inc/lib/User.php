<?php

/**
 * Represents a user in lancms.
 * 
 * @author edvin
 */
class User extends SqlObject {
    
    function __construct($id) {
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
     * Provides the gender of this user. Male or Female.
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
     * Provides the borthday timestamp.
     * 
     * @return int
     */
    public function getBirthdayTimestamp() {
        return mktime(0, 0, 0, $this->getBirthMonth(), $this->getBirthDay(), $this->getBirthYear());
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
     * Will compare this User object to another User object.
     * 
     * @return bool
     */
    public function equals(User $user) {
        return ($user->getUserID() == $this->getUserID());
    }

}