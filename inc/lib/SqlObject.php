<?php

/**
 * Represents an object that is fetched from a database.
 * Objects extending must always contract parent with table name, IDfield and the ID of the object.
 * 
 * A object can easily be updated in the database by using _setField then calling commitChanges()
 *
 * @author edvin
 */
class SqlObject {
	
	protected $_info;
    protected $_orgInfo;
    protected $_table;
    protected $_idField;
    protected $_objectID;

    /**
     * Construct new sqlobject.
     *
     * For classes extending this class they must give
     * what table, primary ID field and the ID of the object.
     * This information is required to make commitChanges() work.
     *
     * @param $table string sql prefix will be added as prefix.
     * @param $idField
     * @param $objectID
     */
    protected function __construct($table, $idField, $objectID) {
        global $sql_prefix;

        $this->_info = array();
        $this->_orgInfo = array();
        $this->_table = $sql_prefix . "_" . $table;
        $this->_idField = $idField;
        $this->_objectID = $objectID;
    }

    /**
     * Handle sleep for sqlobject.
     *
     * @return array
     */
    function __sleep() {
        return array('_info', '_orgInfo', '_table', '_idField', '_objectID');
    }

    function __wakeup() { }

    /**
     * Fill the info array from sql result.
     *
     * @param $arr
     */
    public function fillInfo($arr) {
        $this->_info = $arr;
        $this->_orgInfo = $arr;
    }

    /**
     * Updates the object in the database.
     * Only changes set with setters will be updated.
     */
    public function commitChanges() {
        $toUpdate = array();
        foreach ($this->_info as $k=>$value) {
            if (array_key_exists($k, $this->_orgInfo) == false || $value != $this->_orgInfo[$k]) {
                $toUpdate[] = $k . "='" . $value . "'";
            }
        }

        if (count($toUpdate) < 1) {
            return;
        }

        $query = sprintf("UPDATE %s SET %s WHERE %s = %s", $this->_table, implode(",", $toUpdate), $this->_idField, $this->getObjectID());
        $result = db_query($query);

        if ($result === false)
        	return false;

        return true;
    }

    /**
     * Provides the ID object set in constructor.
     * Should return int if setup correctly.
     *
     * @return int|mixed
     */
    public function getObjectID() {
        return $this->_objectID;
    }

    /**
     * @return int
     */
    public function getCacheObjectID() {
        return $this->getObjectID();
    }

    /**
     * Provides an "field" from the info array, will return null if field
     * is not found in array.
     *
     * @param string $name
     * @param mixed $default Default value to return if $name is not found.
     * @param int $dataValue 1=String,2=Int,3=Bool
     * @return null|mixed
     */
    protected function _getField($name, $default = null, $dataValue = 1) {
        if (isset($this->_info[$name]) == false) {
            return $default;
        }

        $value = $this->_info[$name];

        switch ($dataValue) {
            case 1:
                $value = strval($value);
                break;

            case 2:
                $value = intval($value);
                break;

            case 3:
                $value = intval($value) == 1 ? true : false;
                break;

            default: break;
        }

        return $value;
    }

    /**
     * Set a value in the info array, orgInfo is not updated as it will indicate what commitChanges() should update.
     *
     * @param string $name
     * @param mixed $value
     */
    protected function _setField($name, $value) {
        $this->_info[$name] = $value;
    }

}
