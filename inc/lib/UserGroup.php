<?php

require_once __DIR__ . "/UserGroupMember.php";

class UserGroup extends SqlObject
{

    const GROUP_TYPE_ACCESS     = "access";
    const GROUP_TYPE_CLAN       = "clan";
    
    /**
     * Create new group instance.
     */
    function __construct($id) {
        parent::__construct("groups", "ID", $id);
    }

    /**
     * Provides the ID of this group.
     * 
     * @return int
     */
    public function getGroupID()
    {
        return $this->_getField("ID", 0, 2);
    }

    /**
     * Provides the ID of the event this group belongs to.
     * 
     * @return int
     */
    public function getEventID()
    {
        return $this->_getField("eventID", 0, 2);
    }

    /**
     * Provides the name of this group.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_getField("groupname");
    }

    /**
     * Provides the type this group is. Will only be "clan" or "access"
     * 
     * @return string
     */
    public function getType()
    {
        return $this->_getField("groupType");
    }

    /**
     * Indicates if this group is of type "access"
     * 
     * @return bool
     */
    public function isAccessType()
    {
        return $this->getType() == self::GROUP_TYPE_ACCESS;
    }

    /**
     * Provides all members of this group.
     * 
     * @return UserGroupMember[]
     */
    public function getMembers()
    {
        // First get all userIDs that are members.
        $qAllMembers = db_query(sprintf("SELECT `userID`,`access` FROM `%s_group_members` WHERE `groupID`=%d", db_prefix(), $this->getGroupID()));
        $nAllMembers = db_num($qAllMembers);

        // Verify that we got any users.
        if ($qAllMembers === false || $nAllMembers < 1) {
            return array();
        }

        $members = array();
        $userIDs = array();
        $rowsIndexedByUID = array();

        // Fetch each as User
        while ($row = db_fetch_assoc($qAllMembers)) {
            $userIDs[] = $row["userID"];
            $rowsIndexedByUID[$row["userID"]] = $row;
        }

        // Fetch the users.
        $users = UserManager::getInstance()->getUsersByID($userIDs);

        if (count($users) < 1)
            return array();

        // Create as UserGroupMember
        foreach ($users as $user) {
            $memberRow = $rowsIndexedByUID[$user->getUserID()];
            $member = new UserGroupMember($user->getUserID());
            $member->fillInfo($user->getInfo());

            // fill extra variables
            $member->setAccess($memberRow["access"]);
            $member->setGroup($this);

            $members[] = $member;
            unset($memberRow, $user, $member);
        }

        return $members;
    }

    /**
     * Provides all members of this group that has admin access.
     * 
     * @return UserGroupMember[]
     */
    public function getAdminMembers()
    {
        $members = $this->getMembers();
        if (count($members) < 1) return array();

        $admins = array();
        foreach ($members as $member) {
            if ($member->hasAdminAccess())
                $admins[] = $member;
        }

        return $admins;
    }

}
