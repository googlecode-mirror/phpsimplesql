# Introduction #

This function is a wrapper for mysql\_query. Because it is applied to the base class (simplesql\_element) and not to any specific class, you may use it on any valid simplesql element.


## Status ##

This function is finished and considered final.

## Usage syntax ##

```
$simplesql_element->query($sql);
```

## Usage Example ##

```
<?php
require_once('simplesql.class.php');
$sql = simplesql_connect("localhost", "user", "pass");
$result = $sql->query("SELECT * FROM db.table");
?>
```