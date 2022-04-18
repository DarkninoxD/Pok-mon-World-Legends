<?php
ini_set('max_execution_time', 300);
require_once('../includes/resources/config.php');

# Query to recive table names 
$query = DB::exQuery("SHOW TABLE STATUS WHERE Name='gebruikers'");
$sql_backup = '';
# Whileloop to loop trough every table 
while($row = $query->fetch_assoc()) {
	if ($row['Name'] == "gebruikers") {
		# Show sql query to rebuild the query
		$sql = 'SHOW CREATE TABLE ' . $row['Name'];
		# Exucte error or give a error
		$query2 = DB::exQuery($sql);

		# Create sql
		$sql_backup .= "\r\n#Create table " . $row['Name'] . "\r\n\r\n";
		$out = $query2->fetch_assoc();

		$sql_backup .= $out['Create Table'] . ";\r\n\r\n";
		$sql_backup .= "#Dump data\r\n\r\n";

		# SQL code to select everything for table
		$sql = 'SELECT * FROM ' . $row['Name']; 
		$out = DB::exQuery($sql);
		$sql_code = '';

		# Loop trough the colloms
		while($code = $out->fetch_assoc()) {
			$sql_code .= "INSERT INTO ".$row['Name']." SET ";
			$count = 0;
			foreach($code as $insert => $value) {
				if ($count != 0) $sql_code.= ", `".$insert."`='".addslashes($value)."'";
				else $sql_code.= "`".$insert."`='".addslashes($value)."'";
				++$count;
			}
			$sql_code = substr($sql_code, 0, -1);
			$sql_code .=  ";\r\n";
		}
		$sql_backup.= $sql_code;
		$table = $row['Name'];
	}
}
#Datum bepalen voor de naam van de backup
$datum = date("dmY - His");
$fp = fopen("private/backup/" . $table . '_' . $datum . ".sql", "w");
fwrite($fp, $sql_backup);
fclose($fp);

# Query to recive table names 
$query = DB::exQuery("SHOW TABLE STATUS WHERE Name='gebruikers_item'");
$sql_backup = '';
# Whileloop to loop trough every table 
while($row = $query->fetch_assoc()) {
	if ($row['Name'] == "gebruikers_item") {
		# Show sql query to rebuild the query
		$sql = 'SHOW CREATE TABLE ' . $row['Name'];
		# Exucte error or give a error
		$query2 = DB::exQuery($sql);

		# Create sql
		$sql_backup .= "\r\n#Create table " . $row['Name'] . "\r\n\r\n";
		$out = $query2->fetch_assoc();

		$sql_backup .= $out['Create Table'] . ";\r\n\r\n";
		$sql_backup .= "#Dump data\r\n\r\n";

		# SQL code to select everything for table
		$sql = 'SELECT * FROM ' . $row['Name']; 
		$out = DB::exQuery($sql);
		$sql_code = '';

		# Loop trough the colloms
		while($code = $out->fetch_assoc()) {
			$sql_code .= "INSERT INTO ".$row['Name']." SET ";
			$count = 0;
			foreach($code as $insert => $value) {
				if ($count != 0) $sql_code.= ", `".$insert."`='".addslashes($value)."'";
				else $sql_code.= "`".$insert."`='".addslashes($value)."'";
				++$count;
			}
			$sql_code = substr($sql_code, 0, -1);
			$sql_code .=  ";\r\n";
		}
		$sql_backup.= $sql_code;
		$table = $row['Name'];
	}
}
#Datum bepalen voor de naam van de backup
$datum = date("dmY - His");
$fp = fopen("private/backup/" . $table . '_' . $datum . ".sql", "w");
fwrite($fp, $sql_backup);
fclose($fp);

# Query to recive table names 
$query = DB::exQuery("SHOW TABLE STATUS WHERE Name='gebruikers_tmhm'");
$sql_backup = '';
# Whileloop to loop trough every table 
while($row = $query->fetch_assoc()) {
	if ($row['Name'] == "gebruikers_tmhm") {
		# Show sql query to rebuild the query
		$sql = 'SHOW CREATE TABLE ' . $row['Name'];
		# Exucte error or give a error
		$query2 = DB::exQuery($sql);

		# Create sql
		$sql_backup .= "\r\n#Create table " . $row['Name'] . "\r\n\r\n";
		$out = $query2->fetch_assoc();

		$sql_backup .= $out['Create Table'] . ";\r\n\r\n";
		$sql_backup .= "#Dump data\r\n\r\n";

		# SQL code to select everything for table
		$sql = 'SELECT * FROM ' . $row['Name']; 
		$out = DB::exQuery($sql);
		$sql_code = '';

		# Loop trough the colloms
		while($code = $out->fetch_assoc()) {
			$sql_code .= "INSERT INTO ".$row['Name']." SET ";
			$count = 0;
			foreach($code as $insert => $value) {
				if ($count != 0) $sql_code.= ", `".$insert."`='".addslashes($value)."'";
				else $sql_code.= "`".$insert."`='".addslashes($value)."'";
				++$count;
			}
			$sql_code = substr($sql_code, 0, -1);
			$sql_code .=  ";\r\n";
		}
		$sql_backup.= $sql_code;
		$table = $row['Name'];
	}
}
#Datum bepalen voor de naam van de backup
$datum = date("dmY - His");
$fp = fopen("private/backup/" . $table . '_' . $datum . ".sql", "w");
fwrite($fp, $sql_backup);
fclose($fp);

# Query to recive table names 
$query = DB::exQuery("SHOW TABLE STATUS WHERE Name='gebruikers_badges'");
$sql_backup = '';
# Whileloop to loop trough every table 
while($row = $query->fetch_assoc()) {
	if ($row['Name'] == "gebruikers_badges") {
		# Show sql query to rebuild the query
		$sql = 'SHOW CREATE TABLE ' . $row['Name'];
		# Exucte error or give a error
		$query2 = DB::exQuery($sql);

		# Create sql
		$sql_backup .= "\r\n#Create table " . $row['Name'] . "\r\n\r\n";
		$out = $query2->fetch_assoc();

		$sql_backup .= $out['Create Table'] . ";\r\n\r\n";
		$sql_backup .= "#Dump data\r\n\r\n";

		# SQL code to select everything for table
		$sql = 'SELECT * FROM ' . $row['Name']; 
		$out = DB::exQuery($sql);
		$sql_code = '';

		# Loop trough the colloms
		while($code = $out->fetch_assoc()) {
			$sql_code .= "INSERT INTO ".$row['Name']." SET ";
			$count = 0;
			foreach($code as $insert => $value) {
				if ($count != 0) $sql_code.= ", `".$insert."`='".addslashes($value)."'";
				else $sql_code.= "`".$insert."`='".addslashes($value)."'";
				++$count;
			}
			$sql_code = substr($sql_code, 0, -1);
			$sql_code .=  ";\r\n";
		}
		$sql_backup.= $sql_code;
		$table = $row['Name'];
	}
}
#Datum bepalen voor de naam van de backup
$datum = date("dmY - His");
$fp = fopen("private/backup/" . $table . '_' . $datum . ".sql", "w");
fwrite($fp, $sql_backup);
fclose($fp);

# Query to recive table names 
$query = DB::exQuery("SHOW TABLE STATUS WHERE Name='pokemon_speler'");
$sql_backup = '';
# Whileloop to loop trough every table 
while($row = $query->fetch_assoc()) {
	if ($row['Name'] == "pokemon_speler") {
		# Show sql query to rebuild the query
		$sql = 'SHOW CREATE TABLE ' . $row['Name'];
		# Exucte error or give a error
		$query2 = DB::exQuery($sql);

		# Create sql
		$sql_backup .= "\r\n#Create table " . $row['Name'] . "\r\n\r\n";
		$out = $query2->fetch_assoc();

		$sql_backup .= $out['Create Table'] . ";\r\n\r\n";
		$sql_backup .= "#Dump data\r\n\r\n";

		# SQL code to select everything for table
		$sql = 'SELECT * FROM ' . $row['Name']; 
		$out = DB::exQuery($sql);
		$sql_code = '';

		# Loop trough the colloms
		while($code = $out->fetch_assoc()) {
			$sql_code .= "INSERT INTO ".$row['Name']." SET ";
			$count = 0;
			foreach($code as $insert => $value) {
				if ($count != 0) $sql_code.= ", `".$insert."`='".addslashes($value)."'";
				else $sql_code.= "`".$insert."`='".addslashes($value)."'";
				++$count;
			}
			$sql_code = substr($sql_code, 0, -1);
			$sql_code .=  ";\r\n";
		}
		$sql_backup.= $sql_code;
		$table = $row['Name'];
	}
}
#Datum bepalen voor de naam van de backup
$datum = date("dmY - His");
$fp = fopen("private/backup/" . $table . '_' . $datum . ".sql", "w");
fwrite($fp, $sql_backup);
fclose($fp);

#Tijd opslaan van wanneer deze file is uitevoerd
DB::exQuery("UPDATE `cron` SET `tijd`='".date("Y-m-d H:i:s")."' WHERE `soort`='backup'");
?>
