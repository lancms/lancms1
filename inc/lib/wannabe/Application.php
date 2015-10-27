<?php

namespace Wannabe;

/**
 * Class Application
 * 
 * @package Wannabe
 * @author Edvin Hultberg
 */
class Application {

    const APPLICATION_STATUS_WAITING  = "waiting";
    const APPLICATION_STATUS_FINISHED = "finished";

    /**
     * @var QuestionResponse[]
     */
    protected $_responses;

    /**
     * @var CrewResponse[]
     */
    protected $_crewResponses;

    /**
     * @var AdminComment[]
     */
    protected $_comments;

    /**
     * @var \User
     */
    protected $_user;

    /**
     * See APPLICATION_STATUS_*
     *
     * @var string
     */
    protected $_applicationStatus;

    /**
     * Application constructor.
     * @param \User $user
     * @param CrewResponse[] $crewResponses
     * @param QuestionResponse[] $responses
     * @param AdminComment[] $comments
     */
    public function __construct(\User $user=null, array $crewResponses=array(), array $responses=array(), array $comments=array()) {
        $this->_user = $user;
        $this->_responses = $responses;
        $this->_crewResponses = $crewResponses;
        $this->_comments = $comments;
    }

    /**
     * @return QuestionResponse[]
     */
    public function getResponses() {
        return $this->_responses;
    }

    /**
     * @return CrewResponse[]
     */
    public function getCrewResponses() {
        return $this->_crewResponses;
    }

    /**
     * @return \User
     */
    public function getUser() {
        return $this->_user;
    }

    /**
     * @param QuestionResponse[] $responses
     */
    public function setResponses($responses) {
        $this->_responses = $responses;
    }

    /**
     * @param CrewResponse[] $crewResponses
     */
    public function setCrewResponses($crewResponses) {
        $this->_crewResponses = $crewResponses;
    }

    /**
     * @param \User $user
     */
    public function setUser($user) {
        $this->_user = $user;
    }

    /**
     * @return AdminComment[]
     */
    public function getComments() {
        return $this->_comments;
    }

    /**
     * @param AdminComment[] $comments
     */
    public function setComments($comments) {
        $this->_comments = $comments;
    }

    /**
     * @param Question $question
     * @return null|QuestionResponse
     */
    public function getResponseForQuestion(Question $question) {
        if (count($this->getResponses()) > 0) {
            foreach ($this->getResponses() as $response) {
                if ($question->getQuestionID() == $response->getQuestionID()) {
                    return $response;
                }
            }
        }

        return null;
    }

    /**
     * @param Crew $crew
     * @return int
     */
    public function getCrewPreferenceScore(Crew $crew) {
        if (count($this->getCrewResponses()) > 0) {
            foreach ($this->getCrewResponses() as $response) {
                if ($response->getCrewID() == $crew->getCrewID()) {
                    return intval($response->getResponseData());
                }
            }
        }

        return 0;
    }

    /**
     * @param Crew $crew
     * @return float
     */
    public function getAverageScoreFromCrew(Crew $crew) {
        $totalCommentsForCrew = 0;
        $totalScoreForCrew = 0;
        foreach ($this->getComments() as $comment) {
            if ($comment->getCrewID() == $crew->getCrewID()) {
                $totalCommentsForCrew++;
                $totalScoreForCrew += $comment->getApproval();
            }
        }

        if ($totalCommentsForCrew < 1 || $totalScoreForCrew < 1) return 0;

        return floor($totalScoreForCrew / $totalCommentsForCrew);
    }

    /**
     * Shorthand method to check if an application is finished.
     *
     * @return bool
     */
    public function isFinished()
    {
        return $this->getStatus() == self::APPLICATION_STATUS_FINISHED;
    }

    /**
     * Provides the current status of this application.
     * An application is finished when the applicant is in a group.
     *
     * See APPLICATION_STATUS_*
     *
     * @return string
     */
    public function getStatus()
    {
        if (is_null($this->_applicationStatus)) {
            $this->_resolveApplicationStatus();
        }

        return $this->_applicationStatus;
    }

    /**
     * Internal method to resolve the status of this application.
     */
    protected function _resolveApplicationStatus()
    {
        // An application is finished if a user is in a group.
        $userGroups = $this->getUser()->getGroups();

        if (count($userGroups) > 0) {
            $this->_applicationStatus = self::APPLICATION_STATUS_FINISHED;
        } else {
            $this->_applicationStatus = self::APPLICATION_STATUS_WAITING;
        }
    }

}
