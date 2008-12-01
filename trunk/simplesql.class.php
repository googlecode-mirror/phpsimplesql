<?php
/*
SimpleSQL by Philip Weaver
Use and redistribute as pleased
Please Include this header.
SimpleSQL v0.2
-------------------------------
Usage (Basic lookup):
$sql = simplesql_connect($server, $user, $pass);
$res = $sql->database->table->field->value;
foreach ($res as $cur_res) {
  echo $cur_res['field'];
}
-------------------------------
Works (tested):
  field->search
  table->num_rows
  table->num_fields
  base->query
  conn->__destruct
  conn->__construct
  field->__get
  field->__set
  field->get_field_flags
  conn->drop_db
  conn->add_db
  db->add_table
  table->add_field
  field->get_field_type
  field->set_field_flags
  table->add_row
Should work (untested):

Does not work (unfinished):

To be implemented (unstarted):
  table->rename_field
  table->drop_rows
  table->drop_field
  conn->num_dbs
  db->num_tables
  conn->get_dbs
  db->get_tables
  table->get_fields
  field->set_type
*/
function simplesql_connect($server, $user, $password) {
  $link = new simplesql_connection($server, $user, $password);
  return $link;
}
function simplesql_disconnect($link) {
  unset($link);
  return true;
}
Class simplesql_element {
  public $__link;
  function query($sql) {
    $result = mysql_query($sql, $this->__link);
    return $result;
  }
  function kill($what) {
    $what= NULL;
  }
}
Class simplesql_connection extends simplesql_element {
  function __construct($server, $user, $password) {
    $this->__link = mysql_connect($server, $user, $password);
    $db_list = mysql_list_dbs($this->__link);
    while ($db = mysql_fetch_object($db_list)) {
      $this->{$db->Database} = new simplesql_db($this->__link, $db->Database);
      $table_list = mysql_list_tables($db->Database, $this->__link);
      while ($table = mysql_fetch_object($table_list)) {
        $tmp = "Tables_in_".$db->Database;
        $this->{$db->Database}->{$table->$tmp} = new simplesql_table($this->__link, $db->Database, $table->$tmp);
        $field_list = mysql_list_fields($db->Database, $table->$tmp, $this->__link);
        for ($i = 0; $i < mysql_num_fields($field_list); $i++) {
          $field = mysql_field_name($field_list, $i);
          $this->{$db->Database}->{$table->$tmp}->{$field} = new simplesql_field($this->__link, $db->Database, $table->$tmp, $field);
        }
      }
    }
  }
  function __destruct() {
    mysql_close($this->__link);
  }
  function __get($name) {
    return "SimpleSQL Error: Database '$name' not found.";
  }
  function add_db($name) {
    $this->query("CREATE DATABASE ".$name);
    $this->{$name} = new simplesql_db($this->__link, $name);
  }
  function num_dbs() {
  }
  function get_dbs() {
  }
  function drop_db($db) {
    if(is_object($this->$db)) {
      $this->query("DROP DATABASE ".$db);
      unset($this->$db);
    } else {
      return false;
    }
  }
}
Class simplesql_db extends simplesql_element {
  private $__db;
  function __construct($link, $db) {
    $this->__link = $link;
    $this->__db = $db;
  }
  function __get($name) {
    return "SimpleSQL Error: Table '$name' not found in Database '".$this->__db."'.";
  }
  function num_tables() {
  }
  function add_table($name, $row_name, $row_type, $row_is_notnull=true, $row_is_unsigned=false, $row_is_autoinc=false, $row_is_primarykey=false, $row_default="") {
    if ($name != "" || $row_name != "" || $row_type != "") {
      $sql = "CREATE TABLE ".$this->__db.".".$name." (";
      $sql .= $row_name . " " . $row_type;
      if (substr($row_type, 0, 3) == "INT" || substr($row_type, 0, 7) == "TINYINT" || substr($row_type, 0, 9) == "MEDIUMINT" || substr($row_type, 0, 6) == "BIGINT") {
        if ($row_is_unsigned) {
          $sql .= " UNSIGNED";
        }
        if ($row_is_notnull) {
          $sql .= " NOT NULL";
        }
        if ($row_is_autoinc) {
          $sql .= " AUTO_INCREMENT";
        }
      } else {
        if ($row_is_notnull) {
          $sql .= " NOT NULL";
        }
      }
      if ($row_is_primarykey) {
        $sql .= " PRIMARY KEY";
      }
      if ($row_default != "") {
        $sql .= " DEFAULT '".$row_default."'";
      }
      $sql .= ")";
      $this->query($sql);
      $this->$name = new simplesql_table($this->__link, $this->__db, $name);
    } else {
      return false;
    }
  }
  function get_tables() {
  }
  function drop_table($name) {
  }
}
Class simplesql_table extends simplesql_element {
  private $__db;
  private $__table;
  function __construct($link, $db, $table) {
    $this->__link = $link;
    $this->__db = $db;
    $this->__table = $table;
  }
  function __get($name) {
    return "SimpleSQL Error: Field '$name' not found in Table '".$this->__db.".".$this->__table."'.";
  }
  function add_row($array) {
    $sql = "INSERT INTO ".$this->__db.".".$this->__table." (";
    $cols = array_keys($array);
    for ($i = 0; $i < count($cols); $i++) {
      if ($i != 0) {
        $sql .= ",";
      }
      $sql .= $cols[$i];
    }
    $sql .= ") VALUES(";
    for ($i = 0; $i < count($cols); $i++) {
      if ($i != 0) {
        $sql .= ",";
      }
      $sql .= "'".$array[$cols[$i]]."'";
    }
    $sql .= ")";
    echo $sql;
    $this->query($sql);
  }
  function add_field($name, $type="VARCHAR(150)", $default="", $is_not_null=true, $is_unsigned=false, $is_autoincrement=false, $is_primary=false) {
    if ($name != "") {
      $sql = "ALTER TABLE ".$this->__db.".".$this->__table." ADD COLUMN ".$name." ".$type."";
      if ($is_autoincrement || $is_unsigned) {
        if (substr($type, 0, 3) == "INT" || substr($type, 0, 7) == "TINYINT" || substr($type, 0, 9) == "MEDIUMINT" || substr($type, 0, 6) == "BIGINT") {
          if ($is_unsigned) {
            $sql .= " UNSIGNED";
          }
          if ($is_not_null) {
            $sql .=  " NOT NULL";
          }
          if ($is_autoincrement) {
            $sql .= " AUTO_INCREMENT";
          }
        } else {
          return false;
        }
      } else {
        if ($is_not_null) {
          $sql .=  " NOT NULL";
        }
      }
      if ($default != "") {
        $sql .= " DEFAULT '".$default."'";
      }
      $this->query($sql);
      $this->{$name} = new simplesql_field($this->__link, $this->__db, $this->__table, $name);
      if ($is_primary) {
        $this->query("ALTER TABLE ".$this->__db.".".$this->__table." DROP PRIMARY KEY, ADD PRIMARY KEY (".$name.")");
      }
      return true;
    } else {
      return false;
    }
  }
  function get_fields() {
  }
  function num_rows() {
    return mysql_num_rows($this->query("SELECT * FROM ".$this->__db.".".$this->__table));
  }
  function num_fields() {
    return mysql_num_fields($this->query("SELECT * FROM ".$this->__db.".".$this->__table));
  }
  function drop_rows($what, $is_what) {
  }
  function drop_field($what) {
  }
  function rename_field($what, $to_what) {
  }
}
Class simplesql_field extends simplesql_element {
  private $__db;
  private $__table;
  private $__field;
  function __construct($link, $db, $table, $field) {
    $this->__link = $link;
    $this->__db = $db;
    $this->__table = $table;
    $this->__field = $field;
  }
  function __get($name) {
    return $this->search($name);
  }
  function __set($name, $value) {
    $this->set($name, $value);
  }
  function get_field_flags($assoc=true) {
    $result = $this->query("SELECT ".$this->__field." FROM ".$this->__db.".".$this->__table);
    $flags = mysql_field_flags($result, 0);
    $flags = explode(' ', $flags);
    if ($assoc) {
      $tmp = array();
      foreach($flags as $flag) {
        if ($flag == "not_null") {
          $tmp['not_null'] = true;
        }
        if ($flag == "unsigned") {
          $tmp['unsigned'] = true;
        }
        if ($flag == "auto_increment") {
          $tmp['auto_inc'] = true;
        }
        if ($flag == "primary_key") {
          $tmp['primary'] = true;
        }
      }
      if ($tmp['not_null'] == "") {
        $tmp['not_null'] = false;
      }
      if ($tmp['unsigned'] == "") {
        $tmp['unsigned'] = false;
      }
      if ($tmp['auto_inc'] == "") {
        $tmp['auto_inc'] = false;
      }
      if ($tmp['primary'] == "") {
        $tmp['primary'] = false;
      }
      return $tmp;
    } else {
      return $flags;
    }
  }
  function get_field_type() {
    $result = $this->query("DESCRIBE ".$this->__db.".".$this->__table);
    $f_temp = mysql_fetch_assoc($result);
    while ($f_temp['Field'] != $this->__field) {
      $f_temp = mysql_fetch_assoc($result);
    }
    $type = explode(" ", $f_temp['Type']);
    $type = strtoupper($type[0]);
    return $type;
  }
  function set_type($type) {
  }
  function set_field_flags($is_not_null=true, $is_unsigned=false, $is_autoincrement=false, $is_primary=false) {
    $sql = "ALTER TABLE `".$this->__db."`.`".$this->__table."` CHANGE `".$this->__field."` `".$this->__field . "` " . $this->get_field_type();
    if ($is_unsigned) {
      $sql .= " UNSIGNED";
    }
    if ($is_not_null) {
      $sql .=  " NOT NULL";
    } else {
      $sql .=  " NULL";
    }
    if ($is_autoincrement) {
      $sql .= " AUTO_INCREMENT";
    }
    $this->query($sql);
    if ($is_primary) {
      $sql = "ALTER TABLE `".$this->__db."`.`".$this->__table."` DROP PRIMARY KEY, ADD PRIMARY KEY (".$this->__field.")";
      $this->query($sql);
    }
  }
  function make_primary() {
    $curr_props = $this->get_field_flags(true);
    $this->set_field_flags($curr_props['not_null'], $curr_props['unsigned'], $curr_props['auto_inc'], true);
  }
  function set($old_val, $new_val) {
    $result = $this->query('UPDATE '.$this->__db.'.'.$this->__table.' SET '.$this->__field."='".$new_val."' WHERE ".$this->__field."='".$old_val."'");
  }
  function search($what) {
    $result = $this->query('SELECT * FROM '.$this->__db.'.'.$this->__table.' WHERE '.$this->__field."='".$what."'");
    $tmp = array();
    while ($res = mysql_fetch_assoc($result)) {
      $tmp[] = $res;
    }
    if (count($tmp) != 0) {
      return $tmp;
    } else {
      return false;
    }
  }
}
$sql = simplesql_connect("localhost", "root", "root");
$new_row['name'] = 'test';
$sql->simplesql_test->test->add_row($new_row);
echo mysql_error();
?>