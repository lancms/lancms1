<?php

namespace Wannabe;

/**
 * Class QuestionResponse
 * 
 * @package Wannabe
 * @author Edvin Hultberg
 */
class QuestionResponse extends \SqlObject {

    function __construct($ID) {
        parent::__construct("wannabeResponse", "ID", $ID);
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
     * @return \Wannabe\Question
     */
    public function getQuestion() {
        if ($this->getQuestionID() < 1) return null;
        return Manager::getInstance()->getQuestionByID($this->getQuestionID());
    }

    /**
     * Provides the ID of the question this response is for.
     * 
     * @return int
     */
    public function getQuestionID() {
        return $this->_getField("questionID", -1, 2);
    }

    /**
     * Set the ID of the question this response is for.
     * 
     * @param int $arg
     */
    public function setQuestionID($arg) {
        $this->_setField("questionID", $arg);
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
     * Provides a question type friendly response.
     *
     * @return string
     */
    public function getResponse() {
        switch ($this->getQuestion()->getQuestionType()) {
            case Question::QUESTION_TYPE_TEXT:
            case Question::QUESTION_TYPE_SELECT:
                return $this->getResponseData();

            case Question::QUESTION_TYPE_CHECKBOX:
                return (strlen(trim($this->getResponseData())) > 0 ? _("Yes") : _("No"));

            default: return "UNKNOWN RESPONSE";
        }
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
     * Indicates if this response has data.
     *
     * @return bool
     */
    public function hasResponse()
    {
        return strlen($this->getResponse()) > 0;
    }

    /**
     * Indicates if this response is for allergies.
     *
     * @return bool
     */
    public function isAllergicResponse()
    {
        $question = $this->getQuestion();

        if (!$question instanceof Question) {
            return false;
        }

        return ($question->getProgrammaticName() == "allergies");
    }

}
