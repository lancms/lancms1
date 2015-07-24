<?php

/**
 * Class NewsArticle
 *
 * @author edvin
 */
class NewsArticle extends SqlObject {

    function __construct($articleID=-1) {
        parent::__construct("news", "ID", $articleID);
    }

    /**
     * Provides the article ID
     *
     * @return int
     */
    public function getArticleID() {
        return $this->getObjectID();
    }

    /**
     * Provides the header or title.
     *
     * @return string
     */
    public function getHeader() {
        return $this->_getField("header", "", 1);
    }

    /**
     * @param string $arg
     */
    public function setHeader($arg) {
        $this->_setField("header", $arg);
    }

    /**
     * Provides the eventID for this article.
     *
     * @return int
     */
    public function getEventID() {
        return $this->_getField("eventID", 0, 2);
    }

    /**
     * @param int $arg
     */
    public function setEventID($arg) {
        $this->_setField("eventID", $arg);
    }

    /**
     * Provides the content of this article.
     *
     * @return string
     */
    public function getContent() {
        return $this->_getField("content", "", 1);
    }

    /**
     * @param string $arg
     */
    public function setContent($arg) {
        $this->_setField("content", $arg);
    }

    /**
     * Provides the time this article was created as a UNIX timestamp.
     *
     * @return int
     */
    public function getCreateTime() {
        return $this->_getField("createTime", 0, 2);
    }

    /**
     * @param int $arg
     */
    public function setCreateTime($arg) {
        $this->_setField("createTime", $arg);
    }

    /**
     * Indicates if this article is enabled or active.
     *
     * @return boolean
     */
    public function isActive() {
        return ($this->_getField("active", "no", 1) == "no" ? false : true);
    }

    /**
     * @param boolean $arg
     */
    public function setIsActive($arg) {
        $this->_setField("active", $arg == true ? "yes" : "no");
    }

    /**
     * Indicates if this article is global between events.
     *
     * @return int
     */
    public function isGlobal() {
        return ($this->_getField("global", "no", 1) == "no" ? false : true);
    }

    /**
     * @param boolean $arg
     */
    public function setIsGlobal($arg) {
        $this->_setField("global", $arg == true ? "yes" : "no");
    }

}
