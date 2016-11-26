<?php

/**
 * Represents an object that is fetched from a database.
 * Objects extending must always contract parent with table name, IDfield and the ID of the object.
 * 
 * A object can easily be updated in the database by using _setField then calling commitChanges()
 * 
 * NOTE! Do not create a method to fetch the ID of the object that do not just return the value of getObjectID().
 *       When a new object is inserted the insert_id is set on getObjectID.
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
     * @param $table string The sql prefix will be added as prefix.
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
		
		return $this;
    }

    public function getInfo() {
        return $this->_info;
    }

    /**
     * Updates the object in the database.
     * Only changes set with setters will be updated.
     *
     * If the object ID is -1 then data in the info array will be used in an INSERT statement.
     */
    public function commitChanges() {
        // Insert into the database if the ID is -1.
        $insertMode = false;
        if ($this->getObjectID() == -1) {
            $insertMode = true;
        }

        $data = array();
        foreach ($this->_info as $k=>$value) {
            if ($insertMode == true || (array_key_exists($k, $this->_orgInfo) == false || $value != $this->_orgInfo[$k])) {
                $data[$k] = $value;
            }
        }

        if (count($data) < 1) {
            return false;
        }

        if ($insertMode) {
            // Format the string for insert.
            $dataFormattedSql = array();
            foreach ($data as $key=>$dataLine) {
                $dataFormattedSql[$key] = (is_numeric($dataLine) ? "" : "'") . $dataLine . (is_numeric($dataLine) ? "" : "'");
            }

            $query = sprintf("INSERT INTO %s (%s)VALUES(%s)", $this->_table, implode(",", array_keys($dataFormattedSql)), implode(",", $dataFormattedSql));

            // Set new ID
            $this->_objectID = db_insert_id();
        } else {
            // Format the string for update.
            $dataFormattedSql = array();
            foreach ($data as $key=>$dataLine) {
                $dataFormattedSql[$key] = $key . "=" . (is_numeric($dataLine) ? "" : "'") . $dataLine . (is_numeric($dataLine) ? "" : "'");
            }

            $query = sprintf("UPDATE %s SET %s WHERE %s = %s", $this->_table, implode(",", $dataFormattedSql), $this->_idField, $this->getObjectID());
        }

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
