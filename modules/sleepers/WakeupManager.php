<?php

/**
 * WakeupManager
 * @author edvin
 */
class WakeupManager {

    protected $_eventID;
    protected $_sleeperTable;
    protected $_sleepers;

    function __construct($eventID, $sleepersTable) {
        $this->_eventID = $eventID;
        $this->_sleeperTable = $sleepersTable;
        $this->_sleepers = array();
    }

    /**
     * Provides array containing sleepers that have wakeup set on them.
     * Array looks like:
     * array(
     *   array('ID' => [userid], 'name' => [name], 'wakeup_time' => [timestamp])
     * )
     *
     * Wakeup_time is a timestamp of when the user should be woken up.
     *
     * @return array
     */
    public function getSleepers() {
        return $this->_sleepers;
    }

    /**
     * Indicates if $userID has wakeup time set.
     *
     * @param $userID
     * @return bool
     */
    public function hasUserWakeup($userID) {
        return ($this->getWakeupTime($userID) > 0);
    }

    /**
     * Provides the time a user has requested wakeup at.
     *
     * @param $userID
     * @return null
     */
    public function getWakeupTime($userID) {
        if (count($this->_sleepers) > 0) {
            foreach ($this->_sleepers as $sleeper) {
                if ($sleeper['ID'] == $userID)
                    return $sleeper['wakeup_time'];

                unset($sleeper);
            }
        }

        return null;
    }

    /**
     * Remove wakeup time on user, will simply set it to zero.
     *
     * @param $userID
     * @return bool false if user it not sleeping and true on success.
     */
    public function removeWakeup($userID) {
        $ret = false;

        $getUserSleeping = db_query(sprintf("SELECT wakeupTimestamp FROM %s WHERE eventID=%s AND userID=%s", $this->_sleeperTable, $this->_eventID, $userID));
        if (db_num($getUserSleeping) > 0) {
            $row = db_fetch($getUserSleeping);
            if ($row->wakeupTimestamp > 0) {
                db_query(sprintf("UPDATE %s SET wakeupTimestamp=0 WHERE eventID=%s AND userID=%s", $this->_sleeperTable, $this->_eventID, $userID));
                $ret = true;
            }
            unset($row);
        }
        unset($getUserSleeping);

        // Remove locally.
        $this->_rmSleeper($userID);

        return $ret;
    }

    /**
     * Set wakeup time on user
     *
     * @param $userID
     * @param $timestamp int unix timestamp
     * @return bool false if user it not sleeping and true on success.
     */
    public function setWakeup($userID, $timestamp) {
        $ret = false;

        $getUserSleeping = db_query(sprintf("SELECT wakeupTimestamp FROM %s WHERE eventID=%s AND userID=%s", $this->_sleeperTable, $this->_eventID, $userID));
        if (db_num($getUserSleeping) > 0) {
            $row = db_fetch($getUserSleeping);
            if ($row->wakeupTimestamp < 1) {
                db_query(sprintf("UPDATE %s SET wakeupTimestamp=%s WHERE eventID=%s AND userID=%s", $this->_sleeperTable, $timestamp, $this->_eventID, $userID));
                $ret = true;
            }
            unset($row);
        }
        unset($getUserSleeping);

        // Set locally.
        $this->_addSleeper($userID, '', $timestamp);

        return $ret;
    }

    /**
     * Add sleeper to array internally.
     *
     * @param $userID
     * @param $username
     * @param $wakeUpAt
     */
    protected function _addSleeper($userID, $username, $wakeUpAt) {
        array_push($this->_sleepers, array('ID' => $userID, 'name' => $username, 'wakeup_time' => $wakeUpAt));
    }

    /**
     * Removes a sleeper internally.
     *
     * @param $userID
     * @return bool
     */
    protected function _rmSleeper($userID) {
        if (count($this->_sleepers) > 0) {
            foreach ($this->_sleepers as $k=>$sleeper) {
                if ($sleeper['ID'] == $userID)
                    unset($sleeper[$k]);
            }
        }

        return true;
    }

    /**
     * Indicates if its time to wakeup this user.
     *
     * @param $userID
     * @return bool
     */
    public function isWakeupTime($userID) {
        $wakeupTime = $this->getWakeupTime($userID);
        if ($wakeupTime != null && $wakeupTime > 0) {
            return (time() > $wakeupTime);
        }

        return false;
    }

    /**
     * Determine if sleeper needs wakeup.
     *
     * @param $row
     */
    public function filterSleeper($row) {
        if (isset($row->wakeupTimestamp) && $row->wakeupTimestamp > 0) {
            $this->_addSleeper($row->ID, $row->nick, $row->wakeupTimestamp);
        }

        unset($queryResult);
    }

}