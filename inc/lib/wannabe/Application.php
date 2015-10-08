<?php

namespace Wannabe;

/**
 * Class Application
 * 
 * @package Wannabe
 * @author Edvin Hultberg
 */
class Application {

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

}
