<?php

class UserGroupManager
{

    protected static $_instance;

    protected $_runtimeGroups;

    function __construct() {
        $this->_runtimeGroups = array(); // Initialize runtime cache of groups.
    }

    /**
     * @return UserGroupManager
     */
    public static function getInstance() {
        if (self::$_instance == null)
            self::$_instance = new self();

        return self::$_instance;
    }

    /**
     * Provides all access groups of the provided event.
     *
     * @param int|null $eventID If null the current event is used.
     * @return UserGroup[]
     * @throws Exception
     */
    public function getEventGroups($eventID = null)
    {
        global $sessioninfo;

        if ($eventID == null) {
            $eventID = $sessioninfo->eventID;
        }

        if (intval($eventID) < 1) {
            throw new Exception("Argument must be an int.");
        }

        $getEventGroupIDs = db_query(sprintf("SELECT `ID` FROM `%s_groups` WHERE `eventID` = %d", db_prefix(), $eventID));
        if (db_num($getEventGroupIDs) < 1) {
            return array();
        }

        $groupIDs = array();
        while ($row = db_fetch_assoc($getEventGroupIDs)) {
            $groupIDs[] = $row["ID"];
        }

        return $this->getGroupsByID($groupIDs);
    }

    /**
     * Provides group instances from an array of IDs.
     * 
     * @param array $groupIDs
     * @return UserGroup[]
     * @throws Exception
     */
    public function getGroupsByID(array $groupIDs)
    {
        if (!is_array($groupIDs))
            throw new Exception("Array of group IDs is not an array...");

        $groupIDs = array_map("intval", $groupIDs);
        $groupIDs = array_filter($groupIDs);

        if (count($groupIDs) < 1)
            throw new Exception("Array of group IDs must contain something..");

        $groups = array();

        // Check if we have already fetched some of the groups.
        if (is_array($this->_runtimeGroups) && count($this->_runtimeGroups) > 0) {
            foreach ($groupIDs as $key => $value) {
                if (isset($this->_runtimeGroups[$key])) {
                    $groups[] = $this->_runtimeGroups[$key];
                    unset($groupIDs[$key]);
                }
            }
        }

        // Is there any groups to fetch?
        if (count($groupIDs) > 0) {
            // create query
            $qGroups = db_query(sprintf("SELECT `ID`,`eventID`,`groupname`,`groupType` FROM `%s_groups` WHERE `ID` IN (%s)", db_prefix(), implode(",", $groupIDs)));
            $nGroups = db_num($qGroups);

            if ($qGroups != false && $nGroups > 0) {
                while ($row = db_fetch_assoc($qGroups)) {
                    $group = new UserGroup($row["ID"]);
                    $group->fillInfo($row);

                    $groups[] = $group;
                    $this->_runtimeGroups[$row["ID"]] = $group;
                }
            }
        }

        return $groups;
    }

    /**
     * Provides a single group by ID.
     * 
     * @see getGroupsByID()
     * @param int $groupID
     * @return UserGroup
     * @throws Exception
     */
    public function getGroupByID($groupID)
    {
        $groups = $this->getGroupsByID(array($groupID));
        return (isset($groups[0]) ? $groups[0] : null);
    }

    /**
     * Provides all groups of an user.
     * 
     * @param int $userID
     * @param int|null $eventID
     * @return UserGroup[]
     */
    public function getUserGroups($userID, $eventID=null)
    {
        global $sessioninfo;
        if ($eventID === null) {
            $eventID = $sessioninfo->eventID;
        }

        $query = "SELECT `groupID` FROM `" . db_prefix() . "_group_members` WHERE `userID` = " . intval($userID);
        $result = db_query($query);

        if (db_num($result) < 1) {
            return array();
        }

        // The groups into an array of ids
        $groupIDs = array();
        while ($row = db_fetch_assoc($result)) {
            $groupIDs[] = $row["groupID"];
        }

        // Fetch groups
        $groups = $this->getGroupsByID($groupIDs);
        if (count($groups) < 1)
            return array();

        // Filter out eventIDs?
        if (intval($eventID) > 0) {
            foreach ($groups as $key => $group) {
                if ($group->getEventID() != $eventID)
                    unset($groups[$key]);
            }
        }

        return $groups;
    }

    /**
     * Provides all groups of an user where access is admin.
     * 
     * @param int $userID
     * @param int|null $eventID
     * @return UserGroup[]
     */
    public function getUserIsAdminGroups($userID)
    {
        global $sessioninfo;
        $eventID = $sessioninfo->eventID;

        $query = "SELECT `groupID` FROM `" . db_prefix() . "_group_members` WHERE `userID` = " . intval($userID) . " AND `access` = 'Admin'";
        $result = db_query($query);

        if (db_num($result) < 1) {
            return array();
        }

        // The groups into an array of ids
        $groupIDs = array();
        while ($row = db_fetch_assoc($result)) {
            $groupIDs[] = $row["groupID"];
        }

        // Fetch groups
        $groups = $this->getGroupsByID($groupIDs);
        if (count($groups) < 1)
            return array();

        // Filter out eventIDs?
        if (intval($eventID) > 0) {
            foreach ($groups as $key => $group) {
                if ($group->getEventID() != $eventID)
                    unset($groups[$key]);
            }
        }

        return $groups;
    }

    public function getCrewsOfGroups(array $groups)
    {
        global $sessioninfo;
        $eventID = $sessioninfo->eventID;

        $crewsOfEvent = \Wannabe\Manager::getInstance()->getCrews(array($eventID));

        $crews = array();

        if (count($crewsOfEvent) < 1) {
            return $crews;
        }

        foreach ($crewsOfEvent as $crew) {
            foreach ($groups as $group) {
                if ($crew->getGroupID() == $group->getGroupID()) {
                    $crews[] = $crew;
                }
            }
        }

        return $crews;
    }

}
