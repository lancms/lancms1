<?php

class UserGroupMember extends User
{

    protected $_access;
    protected $_group;

    /**
     * Provides the access this member has.
     * 
     * @return string
     */
    public function getAccess()
    {
        return $this->_access;
    }

    /**
     * Provides the group this member is a member of.
     * 
     * @return UserGroup
     */
    public function getGroup()
    {
        return $this->_group;
    }

    public function setAccess($access)
    {
        $this->_access = $access;
    }

    public function setGroup($group)
    {
        $this->_group = $group;
    }

    /**
     * Indicates if this user has admin access.
     * 
     * @return bool
     */
    public function hasAdminAccess()
    {
        return $this->_access == "Admin";
    }

    /**
     * Indicates if this user has write access.
     * 
     * @return bool
     */
    public function hasWriteAccess()
    {
        return $this->hasAdminAccess() || $this->_access == "Write";
    }

    /**
     * Indicates if this user has read access.
     * 
     * @return bool
     */
    public function hasReadAccess()
    {
        return $this->_access == "Read";
    }

    /**
     * Indicates if this user has any access at all.
     * 
     * @return bool
     */
    public function hasAccess()
    {
        return $this->_access != "No";
    }
    
}
