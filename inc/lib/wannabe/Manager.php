<?php

namespace Wannabe;

use LanException\InvalidArgumentException;
use LanException\SQLException;

require_once __DIR__ . "/Question.php";
require_once __DIR__ . "/QuestionResponse.php";
require_once __DIR__ . "/Crew.php";
require_once __DIR__ . "/CrewResponse.php";
require_once __DIR__ . "/AdminComment.php";
require_once __DIR__ . "/Application.php";

/**
 * Class Manager, the manager for Wannabe
 * 
 * @package Wannabe
 * @author Edvin Hultberg
 */
class Manager {

    protected static $_instance;
    protected $_questions;

    protected function __construct() {
        $this->_questions = array();
    }

    /**
     * Provides the current singleton instance of the WannabeManager
     * 
     * @return Manager
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Provides questions from the database. This method will by default fetch ALL questions from the database
     * if no filter is defined.
     *
     * @param array $eventIDs Apply a filter of eventIDs (optional).
     * @return \Wannabe\Question[]
     * @throws SQLException
     */
    public function getQuestions($eventIDs=array()) {
        // Allow to filter on event, if it's -1 then get all.
        $query = "SELECT `ID`,`eventID`,`question`,`questionType`,`questionOrder` FROM `" . db_prefix() . "_wannabeQuestions`";

        // sanatize our array
        $eventIDs = array_map("intval", $eventIDs);

        // Remove all false or zero from it.
        $eventIDs = array_filter($eventIDs);

        // Apply filter if it do contain something.
        if (count($eventIDs) > 0) {
            $query .= " WHERE `eventID` IN(" . implode(",", $eventIDs) . ")";
        }

        // Do query
        $getQuestions = db_query($query);

        // Did query fail?
        if ($getQuestions === false) {
            throw new SQLException("Query for wannabe questions failed! " . $query);
        }

        // Check for some data...
        if (db_num($getQuestions) < 1) {
            return array();
        }

        $questionObjects = array();
        while ($row = db_fetch_assoc($getQuestions)) {
            $questionObject = new Question($row["ID"]);
            $questionObject->fillInfo($row);

            $questionObjects[] = $questionObject;
            $this->_questions[$row["ID"]] = $questionObject;
            unset($questionObject);
        }

        return $questionObjects;
    }

    /**
     * Provides a single question from the database by ID.
     * 
     * @param int $questionID The ID of the question to get.
     * @param array $eventIDs Apply a filter of eventIDs (optional).
     * @return \Wannabe\Question|null
     * @throws InvalidArgumentException
     * @throws SQLException
     */
    public function getQuestionByID($questionID, $eventIDs=array()) {
        $eventIDs = array_map("intval", $eventIDs);
        $eventIDs = array_filter($eventIDs);

        // Is the ID in runtime cache?
        if (isset($this->_questions[$questionID]) == true) {
            $question = $this->_questions[$questionID];

            // Match event filter.
            if (count($eventIDs) > 0) {
                if (in_array($question->getQuestionEventID(), $eventIDs) == true) {
                    return $question;
                }
            } else {
                return $question;
            }
        }

        // Allow to filter on event, if it's -1 then get all.
        $query = "";

        // Apply filter if it do contain something.
        if (intval($questionID) < 1) {
            throw new InvalidArgumentException("Invalid argument given to method, expected an int over zero.");
        }

        $query = sprintf("SELECT `ID`,`eventID`,`question`,`questionType`,`questionOrder` FROM `" . db_prefix() . "_wannabeQuestions` WHERE `ID`=%s", $questionID);

        // Apply event filer if set and valid.
        if (count($eventIDs) > 0) {
            $query .= " AND `eventID` IN(" . implode(",", $eventIDs) . ")";
        }

        // Do query
        $getQuestion = db_query($query);

        // Did query fail?
        if ($getQuestion === false) {
            throw new SQLException("Query for wannabe question failed! " . $query);
        }

        // Check for some data...
        if (db_num($getQuestion) < 1) {
            return null;
        }

        $row = db_fetch_assoc($getQuestion);
        $questionObject = new Question($row["ID"]);
        $questionObject->fillInfo($row);

        $this->_questions[$row["ID"]] = $questionObject;
        return $questionObject;
    }

    /**
     * Provides all responses made by user.
     *
     * If userID is left as null then current user is used.
     *
     * @param int|null $userID
     * @return QuestionResponse[]
     * @throws SQLException
     */
    public function getQuestionResponsesByUser($userID=null) {
        global $sessioninfo;
        if ($userID === null) {
            $userID = $sessioninfo->userID;
        }

        $query = sprintf("SELECT `ID`, `questionID`, `response`, `userID` FROM `%s_wannabeResponse` WHERE `userID`=%s", db_prefix(), $userID);
        $response = db_query($query);
        if ($response === false) {
            throw new SQLException("MySQL query failed!" . $query);
        }

        if (db_num($response) < 1) {
            return array();
        }

        $responses = array();
        while ($row = db_fetch_assoc($response)) {
            $responseObject = new QuestionResponse($row["ID"]);
            $responseObject->fillInfo($row);

            $responses[$row["ID"]] = $responseObject;
            unset($responseObject);
        }

        unset($response, $query);

        return $responses;
    }

    /**
     * Provides an array of all question types.
     * 
     * @return array An array of strings.
     */
    public function getQuestionTypes() {
        return array(Question::QUESTION_TYPE_TEXT, Question::QUESTION_TYPE_CHECKBOX, Question::QUESTION_TYPE_SELECT);
    }

    /**
     * Delete question from the database.
     * 
     * @param \Wannabe\Question $question
     * @return boolean Always return true.
     */
    public function deleteQuestion(Question $question) {
        db_query("DELETE FROM `" . db_prefix() . "_wannabeQuestions` WHERE `ID`=" . $question->getQuestionID());

        // Log this action.
        $log['ID'] = $question->getQuestionID();
        $log['eventID'] = $question->getQuestionEventID();
        log_add ("wannabeadmin", "rmWannabeQuestion", serialize($log));

        return true;
    }

    /**
     * Provides crews from the database. This method will by default fetch ALL crews from the database
     * if no filter is defined.
     * 
     * @param array $eventIDs Apply a filter of eventIDs (optional).
     * @return \Wannabe\Crew[]
     * @throws SQLException
     */
    public function getCrews($eventIDs=array()) {
        // Allow to filter on event, if it's -1 then get all.
        $query = "SELECT `ID`,`eventID`,`description`,`crewname`,`groupID` FROM `" . db_prefix() . "_wannabeCrews`";

        // sanatize our array
        $eventIDs = array_map("intval", $eventIDs);

        // Remove all false or zero from it.
        $eventIDs = array_filter($eventIDs);

        // Apply filter if it do contain something.
        if (count($eventIDs) > 0) {
            $query .= " WHERE `eventID` IN(" . implode(",", $eventIDs) . ")";
        }

        // Do query
        $getCrews = db_query($query);

        // Did query fail?
        if ($getCrews === false) {
            throw new SQLException("Query for wannabe crews failed! " . $query);
        }

        // Check for some data...
        if (db_num($getCrews) < 1) {
            return array();
        }

        $crewsObjects = array();
        while ($row = db_fetch_assoc($getCrews)) {
            $crewObject = new Crew($row["ID"]);
            $crewObject->fillInfo($row);

            $crewsObjects[] = $crewObject;
            unset($crewObject);
        }

        return $crewsObjects;
    }

    /**
     * @param $eventIDs
     * @return array
     * @throws SQLException
     */
    public function getCrewsIDArray($eventIDs) {
        $crews = $this->getCrews($eventIDs);
        $ret = array();

        if (count($crews) > 0) {
            foreach ($crews as $crew) {
                $ret[] = $crew->getCrewID();
            }
        }

        return $ret;
    }

    /**
     * Provides a single crew from the database by ID.
     * 
     * @param int $crewID The ID of the crew to get.
     * @param array $eventIDs Apply a filter of eventIDs (optional).
     * @return \Wannabe\Crew|null
     * @throws SQLException
     */
    public function getCrewByID($crewID, $eventIDs=array()) {
        // Allow to filter on event, if it's -1 then get all.
        $query = "";

        // Apply filter if it do contain something.
        if (intval($crewID) < 1) {
            throw new \InvalidArgumentException("Invalid argument given to method, expected an int over zero.");
        }

        $query = sprintf("SELECT `ID`,`eventID`,`crewname`,`description`,`groupID` FROM `" . db_prefix() . "_wannabeCrews` WHERE `ID`=%s", $crewID);

        // Apply event filer if set and valid.
        $eventIDs = array_map("intval", $eventIDs);
        $eventIDs = array_filter($eventIDs);
        if (count($eventIDs) > 0) {
            $query .= " AND `eventID` IN(" . implode(",", $eventIDs) . ")";
        }

        // Do query
        $getCrew = db_query($query);

        // Did query fail?
        if ($getCrew === false) {
            throw new SQLException("Query for wannabe crew failed! " . $query);
        }

        // Check for some data...
        if (db_num($getCrew) < 1) {
            return null;
        }

        $row = db_fetch_assoc($getCrew);
        $crewObject = new Crew($row["ID"]);
        $crewObject->fillInfo($row);

        return $crewObject;
    }

    /**
     * Provides all crew responses made by user.
     *
     * If userID or/and eventID is left as null then current user and/or event is used.
     *
     * @param int|null $userID
     * @param array $crewIDs
     * @return CrewResponse[]
     * @throws SQLException
     */
    public function getCrewResponsesByUser($userID=null, $crewIDs=array()) {
        global $sessioninfo;
        if ($userID === null) {
            $userID = $sessioninfo->userID;
        }

        if (count($crewIDs) < 1) return array();

        $query = sprintf("SELECT `userID`, `crewID`, `response` FROM `%s_wannabeCrewResponse` WHERE `userID`=%s AND `crewID` IN(%s)", db_prefix(), $userID, implode(", ", $crewIDs));
        $response = db_query($query);
        if ($response === false) {
            throw new SQLException("MySQL query failed!" . $query);
        }

        if (db_num($response) < 1) {
            return array();
        }

        $responses = array();
        while ($row = db_fetch_assoc($response)) {
            $responseObject = new CrewResponse();
            $responseObject->fillInfo($row);

            $responses[] = $responseObject;
            unset($responseObject, $row);
        }

        unset($query, $response);

        return $responses;
    }

    /**
     * Delete a crew from the database.
     * 
     * @param \Wannabe\Crew $crew
     * @return boolean Always return true.
     */
    public function deleteCrew(Crew $crew) {
        db_query("DELETE FROM `" . db_prefix() . "_wannabeCrews` WHERE `ID`=" . $crew->getCrewID());

        // Log this action.
        $log['ID'] = $crew->getCrewID();
        $log['eventID'] = $crew->getEventID();
        log_add ("wannabeadmin", "rmWannabeCrew", serialize($log));

        return true;
    }

    /**
     * @param int|null $eventID
     * @return Application[]
     * @throws SQLException
     */
    public function getApplications($eventID=null) {
        global $sessioninfo;
        if ($eventID === null) {
            $eventID = $sessioninfo->eventID;
        }

        $query = "SELECT DISTINCT `userID` FROM `%s_wannabeResponse` AS res JOIN `%s_wannabeQuestions` AS que ON res.`questionID` = que.`ID` WHERE que.`eventID` = %s";

        $qApplicationUsers = db_query(sprintf($query, db_prefix(), db_prefix(), $eventID));
        if (db_num($qApplicationUsers) < 1) {
            return array();
        }

        $applications = array();
        while ($row = db_fetch_assoc($qApplicationUsers)) {
            $userID = $row["userID"];
            $applications[$userID] = $this->getApplication($userID, $eventID);
        }
        return $applications;
    }

    /**
     * @param int|null $userID
     * @param int|null $eventID
     * @return Application
     * @throws SQLException
     */
    public function getApplication($userID=null, $eventID=null) {
        global $sessioninfo;
        if ($userID === null) {
            $userID = $sessioninfo->userID;
        }
        if ($eventID === null) {
            $eventID = $sessioninfo->eventID;
        }

        $crewIDs = $this->getCrewsIDArray(array($eventID));

        // Create initial object
        $application = new Application(\UserManager::getInstance()->getUserByID($userID), array(), array());

        // Fetch all question responses.
        $application->setResponses($this->getQuestionResponsesByUser($userID));

        // Fetch all crew responses
        $application->setCrewResponses($this->getCrewResponsesByUser($userID, $crewIDs));

        // Fetch comments
        $application->setComments($this->getApplicationComments($userID, $crewIDs));

        return $application;
    }

    /**
     * @param int $userID
     * @param array $crewIDs
     * @return AdminComment[]
     */
    public function getApplicationComments($userID, $crewIDs) {
        global $sessioninfo;

        $query = "SELECT `ID`,`crewID`,`comment`,`approval`,`userID`,`adminID`,`createdTime`,`commentType` FROM `" . db_prefix() . "_wannabeComment`";
        $query .= " WHERE `userID`=" . $userID . "";
        $query .= " AND `crewID` IN(" . implode(",", $crewIDs) . ")";

        $qComments = db_query($query);
        if (db_num($qComments) < 1) {
            return array();
        }

        $comments = array();
        while ($row = db_fetch_assoc($qComments)) {
            $commentObject = new AdminComment($row["ID"]);
            $commentObject->fillInfo($row);

            $comments[] = $commentObject;
            unset($commentObject, $row);
        }

        unset($qComments, $query);
        return $comments;
    }

    public function createApplicationComment($userID, $crewID, $comment, $approval, $adminID, $type = -1, $createdTime = 0) {
        global $sessioninfo;

        db_query(sprintf(
            "INSERT INTO `%s_wannabeComment`(`crewID`,`comment`,`approval`,`userID`,`adminID`,`createdTime`,`commentType`)VALUES(%d, '%s', %d, %d, %d, %d, %d)",
            db_prefix(),
            $crewID,
            db_escape($comment),
            $approval,
            $userID,
            $adminID,
            ($createdTime > 0 ? $createdTime : time()),
            ($type > 0 && $type < 3 ? $type : -1)
        ));

    }

    /**
     * @param $int
     * @return string
     */
    public function getScoreLabel($int) {
        switch($int) {
            case "0":
                return lang("Nothing selected");
            case "1":
                return lang("Of course!");
            case "2":
                return lang("Sure");
            case "3":
                return lang("Probably");
            case "4":
                return lang("I'd rather not");
            case "5":
                return lang("Not at all");
            default:
                return lang("Unknown option");
        } // End switch
    }

}
