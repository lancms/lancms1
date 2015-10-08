<?php

namespace Wannabe;

/**
 * Class Crew
 * 
 * @package Wannabe
 * @author Edvin Hultberg
 */
class Crew extends \EventSqlObject {

    function __construct($ID) {
        parent::__construct("wannabeCrews", "ID", $ID);
    }

    /**
     * Provides the crew ID.
     * 
     * @return int
     */
    public function getCrewID() {
        return $this->getObjectID();
    }

    /**
     * Provides the name of this crew.
     * 
     * @return string
     */
    public function getName() {
        return $this->_getField("crewname", "", 1);
    }

    /**
     * Set new name of this crew
     * 
     * @param string $newName
     */
    public function setName($newName) {
        $this->_setField("crewname", $newName);
    }

    public function getAdmins() {
        $group = $this->getGroup();
        if ($group instanceof UserGroup)
            return array();

        return $group->getAdminMembers();
    }

    public function getGroup() {
        if ($this->getGroupID() < 1) return null;

        return \UserGroupManager::getInstance()->getGroupByID($this->getGroupID());
    }

    /**
     * Provides the ID of the group this crew belongs to.
     * 
     * @return int
     */
    public function getGroupID() {
        return $this->_getField("groupID", 0, 2);
    }

    public function setGroupID($groupID) {
        $this->_setField("groupID", $groupID);
    }
    
}
