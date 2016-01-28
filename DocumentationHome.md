# Introduction #

php-simplesql is designed to be as simple and straight forward as possible, but you may still occasionally need to look up functions. The following is a list of functions, both present and not yet implemented.


# The basic How to #

in order to use php-simplesql, simply do the following:

```
<?php
require_once('simplesql.class.php');
$sql = simplesql_connect($server, $user, $password);
?>
```

This will connect to your MySQL server and load/parse it into PHP.
Once this is done, you can use any of the following functions where they apply.

# List of Functions #
  * [base->query](http://code.google.com/p/phpsimplesql/wiki/FunctionBaseQuery)
  * [conn->add\_db](http://code.google.com/p/phpsimplesql/wiki/FunctionConnAdddb)
  * [conn->drop\_db](http://code.google.com/p/phpsimplesql/wiki/FunctionConnDropdb)
  * conn->num\_dbs (to be implemented)
  * conn->get\_dbs (to be implemented)
  * db->add\_table
  * db->drop\_table (to be implemented)
  * db->num\_tables (to be implemented)
  * db->get\_tables (to be implemented)
  * table->add\_row
  * table->add\_field
  * table->drop\_rows (to be implemented)
  * table->drop\_field (to be implemented)
  * table->num\_rows
  * table->num\_fields
  * table->rename\_field (to be implemented)
  * table->get\_fields (to be implemented)
  * field->search
  * field->get\_field\_flags
  * field->get\_field\_type
  * field->set\_field\_flags
  * field->set\_type (to be implemented)