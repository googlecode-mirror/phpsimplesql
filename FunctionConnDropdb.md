# Introduction #

This function allows you to remove a database from your MySQL server using simplesql. It must be used from the simplesql\_connection class.

## Status ##

This function is finished but could be changed if needed.

## Usage syntax ##

```
$simplesql_connection->drop_db($name);
```

## Usage Example ##

```
<?php
require_once('simplesql.class.php');
$sql = simplesql_connect("localhost", "user", "pass");
$sql->drop_db("test");
?>
```