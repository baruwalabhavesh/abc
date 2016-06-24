<?php

/**
 * @uses Basic query function of select , insert , update and delete
 * @param 
 * @used in pages : every page
 * 
 */
//require_once(SERVER_PATH."/classes/Xtreme.php");

class DB extends Xtreme {

    public $Conn = null;

    function __construct($para = "") {
        parent::__construct($para);

        $this->Conn = parent::GetConnection();
    }

    function Insert($array_values, $table_name) {

        $indexes = '';
        $values = '';
        $arr1 =array();
        //$Sql = "INSERT INTO $table_name SET ";
        $i = 0;
        foreach ($array_values as $index => $value) {

            //$value = $Conn->real_escape_string(trim($value));
            //$Sql .= " `$index`='$value',";
            //$Sql .='`'.$index.'`="'.$value.'",';
            $indexes .= " `$index`,";
            $values .= " ?,";
            $arr1[$i] = $value;
            $i++;
        }

        $indexes = rtrim($indexes, ',');
        $values = rtrim($values, ',');
        $Sql = "INSERT INTO $table_name ($indexes) values ($values)";
        
        //$Sql = substr($Sql, 0, strlen($Sql)-1);
        //$Sql=  $Sql.','.$arr;
        $this->Conn->Execute($Sql, $arr1);

        return $this->Conn->Insert_ID();
    }

    function Show($table_name, $where_clause = array(), $oder_clause = "") {
        $indexes = '';
        $values = '';
        $arr1 = $farray = $arr2 =array();
        $i=0;
        if (is_array($where_clause)) {
            foreach ($where_clause as $index => $value) {
                //$value = mysql_real_escape_string($value);
                $indexes .= " AND `$index`=?";
                $arr1[$i] = $value;
                $i++;
            }
        }
        $indexes = rtrim($indexes, ',');
        $Sql = "SELECT * FROM $table_name WHERE 1";
        $Sql .= $indexes;
        $Sql .= $oder_clause;
        return $this->Conn->Execute($Sql,$arr1);
    }

    function Update($array_values, $table_name, $where_clause = array()) {
        // echo "====".exit;
        $indexes = '';
        $values = '';
        $arr1 = $farray = $arr2 =array();
        $i=0;
        //$Sql = "UPDATE $table_name SET ";
        foreach ($array_values as $index => $value) {
            //$value = mysql_real_escape_string($value);
            $indexes .= " `$index`=?,";
            $arr1[$i] = $value;
            $i++;
        }
        $indexes = rtrim($indexes, ',');
        $Sql = "UPDATE $table_name SET $indexes ";
        $Sql .= " WHERE 1 ";
        
        foreach ($where_clause as $index => $value) {
            //$value = mysql_real_escape_string($value);
            $windexes .= " AND `$index`=?";
            $arr2[$i] = $value;
            $i++;
        }
        $windexes = rtrim($windexes, ',');
        $farray = array_merge($arr1,$arr2);
        $Sql .= $windexes;
        
        //echo $Sql."<hr></br>";exit;
        $this->Conn->Execute($Sql, $farray);
    }

    function Remove($table_name, $where_clause = array()) {
        $indexes = '';
        $values = '';
        $arr1 = $farray = $arr2 =array();
        $i=0;
        foreach ($where_clause as $index => $value) {
            //$value = mysql_real_escape_string($value);
            $indexes .= " AND `$index`=?";
            $arr1[$i] = $value;
            $i++;
        }
        $indexes = rtrim($indexes, ',');
        $Sql = "DELETE FROM $table_name WHERE 1 ";
        $Sql .= $indexes;
        $this->Conn->Execute($Sql,$arr1);
    }

    //--- for media management..
    function execute_query($sql) {

        return $this->Conn->Execute($sql);
    }

}

?>
