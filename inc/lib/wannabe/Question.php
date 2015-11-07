<?php

namespace Wannabe;

/**
 * Class Question
 * 
 * @package Wannabe
 * @author Edvin Hultberg
 */
class Question extends \SqlObject {
    
    const QUESTION_TYPE_TEXT     = "text"; // Normal textarea.
    const QUESTION_TYPE_CHECKBOX = "checkbox"; // Checkbox
    const QUESTION_TYPE_SELECT   = "select"; // Select box

    protected $_responses;

    /**
     * Question constructor.
     * 
     * @param int $questionID
     */
    public function __construct($questionID) {
        parent::__construct("wannabeQuestions", "ID", $questionID);
        $this->_responses = null;
    }

    /**
     * Provides the question ID.
     * 
     * @return int
     */
    public function getQuestionID() {
        return $this->getObjectID();
    }

    /**
     * Provides the question data/string.
     * 
     * @return string
     */
    public function getQuestionData() {
        return $this->_getField("question", "", 1);
    }

    /**
     * Set new question data/string.
     * 
     * @param string $questionData
     */
    public function setQuestionData($questionData) {
        $this->_setField("question", $questionData);
    }

    /**
     * Provides the question eventID
     * 
     * @return int
     */
    public function getQuestionEventID() {
        return $this->_getField("eventID", 0, 2);
    }

    /**
     * @param int $questionEventID
     */
    public function setQuestionEventID($questionEventID) {
        $this->_setField("eventID", $questionEventID);
    }

    /**
     * Provides the question type. See the QUESTION_TYPE_* constants in this class.
     * 
     * @return int
     */
    public function getQuestionType() {
        return $this->_getField("questionType", "text", 1);
    }

    /**
     * @param int $questionType
     */
    public function setQuestionType($questionType) {
        $this->_setField("questionType", $questionType);
    }

    /**
     * Provides the programmatic name of this question.
     *
     * @return string|null
     */
    public function getProgrammaticName()
    {
        return $this->_getField("progName");
    }

    /**
     * Set the programmatic name of this question.
     *
     * @param string $progName
     */
    public function setProgrammaticName($progName)
    {
        $this->_setField("progName", $progName);
    }

}
