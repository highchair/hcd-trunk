<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Import CSV file</title>
	</head>
<body>

<?php

/********************************************************************/
/* Code at [url=http://legend.ws/blog/tips-tricks/csv-php-mysql-import/]http://legend.ws/blog/tips-tricks/csv-php-mysql-import/[/url]
/* Edit the entries below to reflect the appropriate values
/********************************************************************/
$databasehost = "####";
$databasename = "####";
$databasetable = "####";
$databaseusername ="####";
$databasepassword = "####";
$fieldseparator = ",";
$lineseparator = "\n";

/********************************************************************/
/* This code assumes that the order of the CSV file matches the order of the columns in the database
/* BE SURE they match up before running this script!

/* AND DON'T FORGET to set up a primary key (id) field in the new DB table!

/********************************************************************/
$csvfile = "####";

/********************************************************************************************/
/* Would you like to add an ampty field at the beginning of these records?
/* This is useful if you have a table with the first field being an auto_increment integer
/* and the csv file does not have such as empty field before the records.
/* Set 1 for yes and 0 for no. ATTENTION: don't set to 1 if you are not sure.
/* This can dump data in the wrong fields if this extra field does not exist in the table
/********************************************************************************************/
$addauto = 1;
/********************************************************************************************/
/* Would you like to save the mysql queries in a file? If yes set $save to 1.
/* Permission on the file should be set to 777. Either upload a sample file through ftp and
/* change the permissions, or execute at the prompt: touch output.sql && chmod 777 output.sql
/********************************************************************************************/
$save = 1;
$outputfile = "output.sql";
/* If exporting directly to the DB does not work, try running the output query in Navicat
/********************************************************************************************/


if( !file_exists($csvfile) ) {
	echo "File not found. Make sure you specified the correct path.\n";
	exit;
}

$file = fopen( $csvfile,"r" );

if( !$file ) {
	echo "Error opening data file.\n";
	exit;
}

$size = filesize( $csvfile );

if( ! $size ) {
	echo "File is empty.\n";
	exit;
}

$csvcontent = fread( $file,$size );

fclose( $file );

$con = @mysql_connect( $databasehost,$databaseusername,$databasepassword ) or die( mysql_error() );
@mysql_select_db( $databasename ) or die( mysql_error() );

$lines = 0;
$queries = "";
$linearray = array();

foreach( split( $lineseparator,$csvcontent ) as $line ) {

	$lines++;

	$line = trim( $line," \t" );
	
	$line = str_replace( "\r","",$line );
	
	/************************************************************************************************************
	This line escapes the special character. remove it if entries are already escaped in the csv file
	************************************************************************************************************/
	$line = str_replace( "'","\'",$line );
	/***********************************************************************************************************/
	
	$linearray = explode( $fieldseparator,$line );
	
	$linemysql = implode( "','",$linearray );
	
	if( $addauto )
		$query = "insert into $databasetable values('','$linemysql');";
	else
		$query = "insert into $databasetable values('$linemysql');";
	
	$queries .= $query . "\n";

	@mysql_query( $query );
}

@mysql_close( $con );

if( $save ) {
	
	if( ! is_writable($outputfile) ) {
		echo "File is not writable, check permissions.\n";
	}
	
	else {
		$file2 = fopen( $outputfile,"w" );
		
		if( ! $file2 ) {
			echo "Error writing to the output file.\n";
		}
		else {
			fwrite( $file2,$queries );
			fclose( $file2 );
		}
	}
	
}

echo "Found a total of $lines records in this csv file.\n";

?>

</body>
</html>