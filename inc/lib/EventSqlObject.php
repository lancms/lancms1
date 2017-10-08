<?php

class EventSqlObject extends SqlObject {

    /**
     * Provides the crew eventID
     *
     * @return int
     */
    public function getEventID() {
        return $this->_getField("eventID", 2, 0);
    }

    /**
     * @param int $eventID
     */
    public function setEventID($eventID) {
        $this->_setField("eventID", $eventID);
    }

}
