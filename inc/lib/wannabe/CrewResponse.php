<?php

namespace Wannabe;

/**
 * Class CrewResponse
 * 
 * @package Wannabe
 * @author Edvin Hultberg
 */
class CrewResponse extends \SqlObject {

    function __construct() {
        parent::__construct("wannabeCrewResponse", null, -1);
    }

    /**
     * Provides the response ID.
     * 
     * @return int
     */
    public function getResponseID() {
        return $this->getObjectID();
    }

    /**
     * Provides the object of the question this response is for.
     * 
     * @return \Wannabe\Crew
     */
    public function getCrew() {
        if ($this->getCrewID() < 1) return null;
        return Manager::getInstance()->getCrewByID($this->getCrewID());
    }

    /**
     * Provides the ID of the question this response is for.
     * 
     * @return int
     */
    public function getCrewID() {
        return $this->_getField("crewID", -1, 2);
    }

    /**
     * Set the ID of the crew this response is for.
     * 
     * @param int $arg
     */
    public function setCrewID($arg) {
        $this->_setField("crewID", $arg);
    }

    /**
     * Provides the object of the user this response is by.
     * 
     * @return \User
     */
    public function getUser() {
        if ($this->getUserID() < 1) return null;
        return \UserManager::getInstance()->getUserByID($this->getUserID());
    }

    /**
     * Provides the ID of the user this response is by.
     * 
     * @return int
     */
    public function getUserID() {
        return $this->_getField("userID", -1, 2);
    }

    /**
     * Set the ID of the user this response is by.
     * 
     * @param int $arg
     */
    public function setUserID($arg) {
        $this->_setField("userID", $arg);
    }

    /**
     * Provides the response data of user.
     * 
     * @return string
     */
    public function getResponseData() {
        return $this->_getField("response", "", 1);
    }

    /**
     * Set new response data, remeber to call commitChanges() to save the changes.
     * 
     * @param string $arg
     */
    public function setResponseData($arg) {
        $this->_setField("response", $arg);
    }

    /**
     * Updates the object in the database.
     * Only changes set with setters will be updated.
     *
     * If the object ID is -1 then data in the info array will be used in an INSERT statement.
     */
    public function commitChanges() {
        // Insert into the database if the ID is -1.
        $insertMode = false;
        if ($this->getObjectID() == -1) {
            $insertMode = true;
        }

        $data = array();
        foreach ($this->_info as $k=>$value) {
            if ($insertMode == true || (array_key_exists($k, $this->_orgInfo) == false || $value != $this->_orgInfo[$k])) {
                $data[$k] = $value;
            }
        }

        if (count($data) < 1) {
            return false;
        }

        if ($insertMode) {
            // Format the string for insert.
            $dataFormattedSql = array();
            foreach ($data as $key=>$dataLine) {
                $dataFormattedSql[$key] = (is_numeric($dataLine) ? "" : "'") . $dataLine . (is_numeric($dataLine) ? "" : "'");
            }

            $query = sprintf("INSERT INTO %s (%s)VALUES(%s)", $this->_table, implode(",", array_keys($dataFormattedSql)), implode(",", $dataFormattedSql));
        } else {
            // Format the string for update.
            $dataFormattedSql = array();
            foreach ($data as $key=>$dataLine) {
                $dataFormattedSql[$key] = $key . "=" . (is_numeric($dataLine) ? "" : "'") . $dataLine . (is_numeric($dataLine) ? "" : "'");
            }

            $query = sprintf("UPDATE %s SET %s WHERE %s = %s AND %s = %s",
                $this->_table,
                implode(",", $dataFormattedSql),
                "userID", $this->getUserID(),
                "crewID", $this->getCrewID());
        }

        $result = db_query($query);
        if ($result === false)
            return false;

        return true;
    }

}
