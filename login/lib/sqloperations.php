<?php
class SqlOperations
{
	//delete value from table
	function sqldelete($table,$cond)
	{
		$sql = "delete from $table where $cond";
		//echo $sql;
		mysqli_query($connection,$sql);
		return(0);
	}
}
?>