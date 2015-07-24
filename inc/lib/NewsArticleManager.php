<?php

require_once __DIR__ . "/NewsArticle.php";

/**
 * Class NewsArticleManger
 *
 * @author edvin
 */
class NewsArticleManger {

    protected static $_instance;

    protected $_articles;

    function __construct() {
        $this->_articles = null; // Initialize runtime cache
    }

    /**
     * @return NewsArticleManger
     */
    public static function getInstance() {
        if (self::$_instance === null)
            self::$_instance = new self();

        return self::$_instance;
    }

    /**
     * Provides all news articles from the database.
     *
     * @param int|null $eventID If null, then current event is used.
     * @param bool $mustBeActive
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return NewsArticle[]
     */
    public function getArticles($eventID=null, $mustBeActive=false, $order="ID DESC", $limit=-1, $offset=0) {
        global $sql_prefix,$sessioninfo;

        if ($eventID === null || $eventID < 1) {
            $eventID = $sessioninfo->eventID;
        }

        if ($this->_articles === null) {
            // Refresh cache
            $query = "SELECT ID,header,eventID,content,createTime,active,global FROM " . $sql_prefix . "_news";

            // Apply eventID filter, we must include global and requested eventID articles.
            $query .= " WHERE (global='yes' OR eventID=1) OR (eventID=$eventID)";

            if ($mustBeActive == true) {
                $query .= " AND active='yes'";
            }

            if (strlen(trim($order)) > 0) {
                $query .= " ORDER BY $order";
            }

            if ($limit > 0) {
                $query .= "LIMIT " . ($offset > 0 ? $offset : 0) . ", " . $limit;
            }

            $result = db_query($query);
            if (db_num($result) > 0) {
                while ($row = db_fetch_assoc($result)) {
                    $articleObject = new NewsArticle($row["ID"]);
                    $articleObject->fillInfo($row);
                    $this->_articles[] = $articleObject;
                    unset($row);
                }
            }

            unset($result);
        }

        return $this->_articles;
    }

    /**
     * Provides a single article instance.
     *
     * @param int $articleID
     * @return NewsArticle|null
     */
    public function getArticle($articleID) {
        global $sql_prefix, $sessioninfo;

        if (intval($articleID) < 1)
            return null;

        $query = sprintf("SELECT ID,header,eventID,content,createTime,active,global FROM %s_news WHERE ID=%s AND ((global='yes' OR eventID=1) OR eventID=%s)",
            $sql_prefix, $articleID, $sessioninfo->eventID);
        $result = db_query($query);
        if ($result == false || db_num($result) < 1)
            return null;

        $row = db_fetch_assoc($result);

        $articleObject = new NewsArticle($articleID);
        $articleObject->fillInfo($row);

        unset($row, $result);

        return $articleObject;
    }

    /**
     * Creates an article and inserts the row into the database.
     *
     * @param $header
     * @param $content
     * @param null $eventID
     * @param bool|true $isActive
     * @param bool|false $isGlobal
     * @return null|NewsArticle
     */
    public function createArticle($header, $content, $eventID=null, $isActive=true, $isGlobal=false) {
        global $sql_prefix,$sessioninfo;

        if ($eventID === null || $eventID < 1) {
            $eventID = $sessioninfo->eventID;
        }

        // Send eventID through intval
        $eventID = intval($eventID);

        // "Convert" active and global to database values for enum
        $active = "no";
        $global = "no";

        if ($isActive == true)
            $active = "yes";

        if ($isGlobal == true)
            $global = "yes";

        $query = sprintf(
            "INSERT INTO %s_news(header,eventID,content,createTime,active,global)VALUES('%s', %s, '%s', %s, '%s', '%s')",
            $sql_prefix, db_escape($header), db_escape($eventID), db_escape($content), time(), $active, $global
        );

        $result = db_query($query);
        if ($result == false) {
            return null;
        }

        $newID = db_insert_id();
        if ($newID < 1)
            return null;

        log_add("news", "addArticle", "0", "0", $sessioninfo->userID, $eventID);
        return $this->getArticle($newID);
    }

    /**
     * Delete an article.
     *
     * @param NewsArticle $article
     * @return bool
     */
    public function deleteArticle(NewsArticle $article) {
        global $sql_prefix,$sessioninfo;

        $query = sprintf("DELETE FROM %s_news WHERE ID=%s AND ((global='yes' OR eventID=1) OR eventID=%s)", $sql_prefix, $article->getArticleID(), $sessioninfo->eventID);
        db_query($query);

        log_add("news", "rmArticle", "0", "0", $sessioninfo->userID, $sessioninfo->eventID);
        return true;
    }

}
