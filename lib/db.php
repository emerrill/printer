<?php

/**
 * Execute arbitrary SQL with no needed return
 *
 * @uses $CONFIG
 * @uses $DB
 * @param string $sql The sql to be executed
 * @return bool
 */
function execute_sql($sql) 
{
    global $CONFIG, $DB;
    
    $result = $DB->Execute($sql);
    
    if ($result) {
        return true;
    } else {
        return false;
    }
}


/**
 * Returns a single row from the database as an object
 *
 * @uses $CONFIG
 * @param string $table The table the row is to be selected from
 * @param string $field1 The field to make compare against value1
 * @param string $value1 The value to compare field1 to
 * @param string $field2 The field to make compare against value2
 * @param string $value2 The value to compare field2 to
 * @param string $field3 The field to make compare against value3
 * @param string $value3 The value to compare field3 to
 * @param string $fields Which fields of the row to return
 * @return object
 */
function get_record($table, $field1, $value1, $field2='', $value2='', $field3='', $value3='', $field4='', $value4='', $fields='*')
{
    global $CONFIG;

    $sql = 'SELECT '.$fields.' FROM '. $CONFIG->prefix . $table .' WHERE '. $field1 .' = \''. $value1 .'\'';

    if ($field2) {
        $sql .= ' AND '. $field2 .' = \''. $value2 .'\'';
        if ($field3) {
            $sql .= ' AND '. $field3 .' = \''. $value3 .'\'';
            if ($field4) {
				$sql .= ' AND '. $field4 .' = \''. $value4 .'\'';
			}
        }
    }

    return get_record_sql($sql);
}


/**
 * Returns a single row for the database as an object, but with a specified WHERE clause
 *
 * @uses $CONFIG
 * @param string $table The table the row is to be selected from
 * @param string $where The WHERE clause (without the 'WHERE')
 * @param string $fields Which fields of the row to return
 * @return bool
 */
function get_record_where($table, $where, $fields='*', $sort = NULL, $limitnum = NULL, $limitfrom = NULL)
{
    global $CONFIG;

    $sql = 'SELECT '.$fields.' FROM '. $CONFIG->prefix . $table .' WHERE '. $where;

    if ($sort) {
        $sql .= ' ORDER BY '. $sort;
    }
    
    if ($limitnum) {
        $sql .= ' LIMIT '.$limitnum;
        if ($limitfrom) {
            $sql .= ' OFFSET '.$limitfrom;
        }
    }

    return get_record_sql($sql);
}


/**
 * Returns a single row for the database as an object, but with specified SQL
 *
 * @uses $CONFIG
 * @param string $sql The SQL to execute
 * @param string $ignoremultiple Ignore if multiple rows are 
 * @return bool
 */
function get_record_sql($sql, $ignoremultiple = true)
{
    global $CONFIG, $DB;
    
    if ($ignoremultiple || !$CONFIG->debug) {
        $limit = ' LIMIT 1';
    } else {
        $limit = '';
    }
    
    
    if (!$rs = $DB->Execute($sql . $limit)) {
        if ($CONFIG->debug) {    // Debugging mode - print checks
            print($DB->ErrorMsg() . '<br /><br />'. $sql . $limit );
        }

        return false;
    }

    if (!$recordcount = $rs->RecordCount()) {
        return false;                 // Found no records
    }

    if ($recordcount == 1) {          // Found one record
        return (object)$rs->fields;

    } else {                          // Error: found more than one record
        print 'Error:  Turn off debugging to hide this error.';
        print $sql . $limit;
        if ($records = $rs->GetAssoc(true)) {
            print 'Found more than one record in get_record_sql !';
            print_r($records);
        } else {
            print 'Very strange error in get_record_sql !';
            print_r($rs);
        }
        //print_continue("$CFG->wwwroot/$CFG->admin/config.php");
    }
}


function get_field($table, $field, $field1, $value1, $field2='', $value2='', $field3='', $value3='', $field4='', $value4='')
{
    global $CONFIG;

    $sql = 'SELECT '.$field.' FROM '. $CONFIG->prefix . $table .' WHERE '. $field1 .' = \''. $value1 .'\'';

    if ($field2) {
        $sql .= ' AND '. $field2 .' = \''. $value2 .'\'';
        if ($field3) {
            $sql .= ' AND '. $field3 .' = \''. $value3 .'\'';
            if ($field4) {
				$sql .= ' AND '. $field4 .' = \''. $value4 .'\'';
			}
        }
    }

    return get_field_sql($sql);
}

function get_field_sql($sql) {

    global $DB;


    $rs = $DB->Execute($sql);
    if (!$rs) {
        return false;
    }

    if ( $rs->RecordCount() == 1 ) {
        return $rs->fields[0];
    } else {
        return false;
    }
}


function get_record_count($table, $field1, $value1, $field2='', $value2='', $field3='', $value3='', $field4='', $value4='')
{
    global $CONFIG;

    $sql = 'SELECT COUNT(*) FROM '. $CONFIG->prefix . $table .' WHERE '. $field1 .' = \''. $value1 .'\'';

    if ($field2) {
        $sql .= ' AND '. $field2 .' = \''. $value2 .'\'';
        if ($field3) {
            $sql .= ' AND '. $field3 .' = \''. $value3 .'\'';
            if ($field4) {
				$sql .= ' AND '. $field4 .' = \''. $value4 .'\'';
			}
        }
    }

    return get_field_sql($sql);
}

function get_record_count_where($table, $where)
{
    global $CONFIG;

    $sql = 'SELECT COUNT(*) FROM '. $CONFIG->prefix . $table .' WHERE '. $where;


    return get_field_sql($sql);
}

/**
 * Returns a single row for the database as an object, but with specified SQL
 *
 * @uses $CONFIG
 * @param string $table The table the row is to be selected from
 * @param string $field 
 * @param string $value 
 * @param string $sort 
 * @param string $fields 
 * @param string $limitfrom 
 * @param string $limitnum 
 * @return bool
 */
function get_records($table, $field='', $value='', $sort='', $fields='*', $limitfrom='', $limitnum='')
{
    $where = '';
    
    if ($field) {
        $where = $field .' = \''. $value .'\'';
    }

    return get_records_where($table, $where, $sort, $fields, $limitfrom, $limitnum);
}


function get_records_where($table, $where='', $sort='', $fields='*', $limitfrom='', $limitnum='')
{
    global $CONFIG;

    $sql = 'SELECT '.$fields.' FROM '. $CONFIG->prefix . $table;
    
    if ($where) {
        $sql .= ' WHERE '. $where;
    }

    if ($sort) {
        $sql .= ' ORDER BY '. $sort;
    }
    
    if ($limitnum) {
        $sql .= ' LIMIT '.$limitnum;
        if ($limitfrom) {
            $sql .= ' OFFSET '.$limitfrom;
        }
    }

    

    return get_records_sql($sql);
}


function get_records_sql($sql)
{
    global $CONFIG, $DB;
    
    if (!$rs = $DB->Execute($sql)) {
        if ($CONFIG->debug) {    // Debugging mode - print checks
            print($DB->ErrorMsg() . '<br /><br />'. $sql);
        }

        return false;
    }

    if ( $rs->RecordCount() > 0 ) {
        if ($records = $rs->GetAssoc(true)) {
            foreach ($records as $key => $record) {
                $objects[$key] = (object) $record;
            }
            return $objects;
        } else {
            return false;
        }
    } else {
        return false;
    }
}


function insert_record($table, $dataobject, $returnid=true, $primarykey='id')
{
    global $DB, $CONFIG;

    if (empty($DB)) {
        return false;
    }


/// In Moodle we always use auto-numbering fields for the primary key
/// so let's unset it now before it causes any trouble later
    unset($dataobject->{$primarykey});

/// Get an empty recordset. Cache for multiple inserts.
    if (!$rs = $DB->Execute('SELECT * FROM '. $CONFIG->prefix . $table .' WHERE '. $primarykey  .' = \'-1\'')) {
        return false;
    }


/// Get the correct SQL from adoDB
    if (!$insertSQL = $DB->GetInsertSQL($rs, (array)$dataobject, true)) {
        return false;
    }


/// Run the SQL statement
    if (!$rs = $DB->Execute($insertSQL)) {
        if ($CONFIG->debug) {    // Debugging mode - print checks
            print($DB->ErrorMsg() . '<br /><br />'. $insertSQL);
        }

        return false;
    }


/// If a return ID is not needed then just return true now (but not in MSSQL DBs, where we may have some pending tasks)
    if (!$returnid) {
        return true;
    }

/// We already know the record PK if it's been passed explicitly,
/// or if we've retrieved it from a sequence (Postgres and Oracle).
    if (!empty($dataobject->{$primarykey})) {
        return $dataobject->{$primarykey};
    }

/// This only gets triggered with MySQL and MSQL databases
/// however we have some postgres fallback in case we failed
/// to find the sequence.
    $id = $DB->Insert_ID();


    return (integer)$id;
}


function update_record($table, $dataobject, $primarykey = 'id')
{

    global $DB, $CONFIG;

    if (! isset($dataobject->$primarykey) ) {
        return false;
    }


    // Determine all the fields in the table
    if (!$columns = $DB->MetaColumns($CONFIG->prefix . $table)) {
        return false;
    }
    $data = (array)$dataobject;


    // Pull out data matching these fields
    $ddd = array();
    foreach ($columns as $column) {
        if ($column->name <> $primarykey and isset($data[$column->name]) ) {
            $ddd[$column->name] = $data[$column->name];
        }
    }

    // Construct SQL queries
    $numddd = count($ddd);
    $count = 0;
    $update = '';

/// Only if we have fields to be updated (this will prevent both wrong updates + 
/// updates of only LOBs in Oracle
    if ($numddd) {
        foreach ($ddd as $key => $value) {
            $count++;
            $update .= $key .' = \''. $value .'\'';   // All incoming data is already quoted
            if ($count < $numddd) {
                $update .= ', ';
            }
        }

        if (!$rs = $DB->Execute('UPDATE '. $CONFIG->prefix . $table .' SET '. $update .' WHERE '.$primarykey.' = \''. $dataobject->$primarykey .'\'')) {
            if ($CONFIG->debug) {    // Debugging mode - print checks
                print($DB->ErrorMsg() . '<br /><br />'.'UPDATE '. $CONFIG->prefix . $table .' SET '. $update .' WHERE '.$primarykey.' = \''. $dataobject->$primarykey .'\'');
            }
    
            return false;
        }
    }


    return true;
}


function delete_record($table, $field1, $value1, $field2='', $value2='', $field3='', $value3='') {
    global $CONFIG;

    $sql = 'DELETE FROM '. $CONFIG->prefix . $table .' WHERE '. $field1 .' = \''. $value1 .'\'';


    if ($field2) {
        $sql .= ' AND '. $field2 .' = \''. $value2 .'\'';
        if ($field3) {
            $sql .= ' AND '. $field3 .' = \''. $value3 .'\'';
        }
    }
    
    return delete_records_sql($sql);
}

function delete_record_object($table, $dataobject) {
    global $CONFIG;

    if (! isset($dataobject->id) ) {
        return false;
    }
    
    $sql = 'DELETE FROM '. $CONFIG->prefix . $table .' WHERE id = \''.$dataobject->id.'\' LIMIT 1';
    
    return delete_records_sql($sql);
}	

function delete_records($table, $field='', $value='', $sort='', $limitfrom='', $limitnum='') {
    $where = '';
    
    if ($field) {
        $where = $field .' = \''. $value .'\'';
    }

    return delete_records_where($table, $where, $sort, $limitfrom, $limitnum);
}

function delete_records_where($table, $where='', $sort='', $limitfrom='', $limitnum='')
{
    global $CONFIG;

    $sql = 'DELETE FROM '. $CONFIG->prefix . $table;
    
    if ($where) {
        $sql .= ' WHERE '. $where;
    }

    if ($sort) {
        $sql .= ' ORDER BY '. $sort;
    }
    
    if ($limitnum) {
        $sql .= ' LIMIT '.$limitnum;
        if ($limitfrom) {
            $sql .= ' OFFSET '.$limitfrom;
        }
    }

    

    return delete_records_sql($sql);
}

function delete_records_sql($sql) {
    global $DB, $CONFIG;

    if (!$rs = $DB->Execute($sql)) {
        if ($CONFIG->debug) {    // Debugging mode - print checks
            print($DB->ErrorMsg() . '<br /><br />'. $sql);
        }

        return false;
    }
    
    return true;
}




?>