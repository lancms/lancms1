<?php

namespace Wannabe;

/**
 * Class AdminComment
 *
 * @package Wannabe
 * @author Edvin Hultberg
 */
class AdminComment extends \SqlObject {

    const COMMENT_TYPE_UNKNOWN = -1; // Legacy.
    const COMMENT_TYPE_ADMINPREF = 1; // Admin preference.
    const COMMENT_TYPE_COMMENT = 2; // Normal text comment.

    function __construct($ID) {
        parent::__construct("wannabeComment", "ID", $ID);
    }

    public function getCommentID() {
        return $this->getObjectID();
    }

    /**
     * Provides the object of the crew.
     *
     * @return \Wannabe\Crew
     */
    public function getCrew() {
        if ($this->getCrewID() < 1) return null;
        return Manager::getInstance()->getCrewByID($this->getCrewID());
    }

    /**
     * Provides the ID of the crew.
     *
     * @return int
     */
    public function getCrewID() {
        return $this->_getField("crewID", 0, 2);
    }

    /**
     * Set the ID of the crew.
     *
     * @param int $arg
     */
    public function setCrewID($arg) {
        $this->_setField("crewID", $arg);
    }

    /**
     * Provides the comment itself
     *
     * @return string
     */
    public function getComment() {
        return $this->_getField("comment", "", 1);
    }

    /**
     * Set the comment data
     *
     * @param string $arg
     */
    public function setComment($arg) {
        $this->_setField("comment", $arg);
    }

    /**
     * Provides the approval rate for this comment.
     *
     * case "0":
     *   $content .= lang("Nothing selected");
     *   break;
     * case "1":
     *   $content .= lang("Of course!");
     *   break;
     * case "2":
     *   $content .= lang("Sure");
     *   break;
     * case "3":
     *   $content .= lang("Probably");
     *   break;
     * case "4":
     *   $content .= lang("I'd rather not");
     *   break;
     * case "5":
     *   $content .= lang("Not at all");
     *   break;
     * default:
     *   $content .= lang("Unknown option");
     *   break;
     *
     * @return int
     */
    public function getApproval() {
        return $this->_getField("approval", 0, 2);
    }

    /**
     * Set the approval
     *
     * @param int $arg
     */
    public function setApproval($arg) {
        $this->_setField("approval", $arg);
    }

    /**
     * Provides the object of the user ID this comment is on.
     *
     * @return \User
     */
    public function getApplicationUser() {
        if ($this->getApplicationUserID() < 1) return null;
        return \UserManager::getInstance()->getUserByID($this->getApplicationUserID());
    }

    /**
     * Provides the user ID this comment is on.
     *
     * @return int
     */
    public function getApplicationUserID() {
        return $this->_getField("userID", 0, 2);
    }

    /**
     * Set the application user ID
     *
     * @param int $arg
     */
    public function setApplicationUserID($arg) {
        $this->_setField("userID", $arg);
    }

    /**
     * Provides the object of the author of this comment.
     *
     * @return \User
     */
    public function getAdminUser() {
        if ($this->getAdminUserID() < 1) return null;
        return \UserManager::getInstance()->getUserByID($this->getAdminUserID());
    }

    /**
     * Provides the author of this comment.
     *
     * @return int
     */
    public function getAdminUserID() {
        return $this->_getField("adminID", 0, 2);
    }

    /**
     * Set the admin user ID
     *
     * @param int $arg
     */
    public function setAdminUserID($arg) {
        $this->_setField("adminID", $arg);
    }

    /**
     * Provides the comment type
     *
     * @return int
     */
    public function getCommentType() {
        return $this->_getField("commentType", 0, 2);
    }

    /**
     * Set the comment type
     *
     * @param int $arg
     */
    public function setCommentType($arg) {
        $this->_setField("commentType", $arg);
    }

    /**
     * Provides the UNIX timestamp of when this comment was created
     *
     * @return int
     */
    public function getCreatedTime() {
        return $this->_getField("createdTime", 0, 2);
    }

    /**
     * Set the created time
     *
     * @param int $arg
     */
    public function setCreatedTime($arg) {
        $this->_setField("createdTime", $arg);
    }

}
