<?php

//sample usage
//$backupDatabase = new Backup_Database('db2.modwest.com', 'anonymos', 'hellotheremos', 'dbjohnnyeveryman');
//echo $backupDatabase->backupDatabase('backups', 5) ? 'OK' : 'FAILED';

// Required for the connection string
/*require_once( '../../conf.php' );

$params = @parse_url(MYACTIVERECORD_CONNECTION_STR) 
	or trigger_error("MyActiveRecord::Connection() - could not parse connection string: ".MYACTIVERECORD_CONNECTION_STR, E_USER_ERROR);
        
$backupDatabase = new Backup_Database( $params['host'], $params['user'], $params['pass'], substr( $params['path'], 1 ) );
echo $backupDatabase->backupDatabase( SERVER_DBBACKUP_ROOT, 5 ) ? 'OK' : 'FAILED';
*/

/**
 * The Backup_Database class
 * expects databasehost, username, password, database name, charset (if not uft8)
 *
 * Author: Allen Waldrop for HCd
 */
class Backup_Database {
	var $host = '';
	var $username = '';
	var $passwd = '';
	var $dbName = '';
	var $charset = '';
	
	//constructor
	function __construct($host, $username, $passwd, $dbName, $charset = 'utf8'){
		$this->host	 = $host;
		$this->username = $username;
		$this->passwd   = $passwd;
		$this->dbName   = $dbName;
		$this->charset  = $charset;
		$this->initializeDatabase();
	}
	
	//establish connection to mysql server
	protected function initializeDatabase(){
		$conn = mysql_connect($this->host, $this->username, $this->passwd);
		mysql_select_db($this->dbName, $conn);
		if (! mysql_set_charset ($this->charset, $conn))
			mysql_query('SET NAMES '.$this->charset);
	}
	
	//alias for backupTables('*') for all tables
	public function backupDatabase($outputDir = '.', $maxBackupCount = 0){
		return $this->backupTables('*',$outputDir, $maxBackupCount);
	}
	
	//backup tables or database, tables should be comma seperated or an array
	public function backupTables($tables = '*', $outputDir = '.', $maxBackupCount = 0){
		set_time_limit(600);
		ini_set('memory_limit', '512M');
		try{
			if($tables == '*'){
				$tables = array();
				$result = mysql_query('SHOW TABLES');
				while($row = mysql_fetch_row($result))
					$tables[] = $row[0];
			}
			else
				$tables = is_array($tables) ? $tables : explode(',',$tables);
			
			// Create the file
			$handle = fopen($outputDir.'/'.$this->dbName.'-'.date("Y-m-d-His", time()).'.sql','w+');
echo "Starting backup " . date("F j, Y, g:i a") . "<br />";
echo "Keeping copies of " . $maxBackupCount . " files<br />";
			
			//*** Optional, Locks in SQL to use specific database, recommended to not use this. 
			//*** Requires Database be created and use command issued before restore
			//fwrite($handle,'CREATE DATABASE IF NOT EXISTS '.$this->dbName.";\n\n");
			//fwrite($handle,'USE '.$this->dbName.";\n\n");
			foreach($tables as $table){
    			
    			//Hiding the image table, too much BLOB data
    			if($table != "images")
    			{
                    echo "<br /><br />Backing up ".$table." table...";
    
    				$result = mysql_query('SELECT * FROM '.$table);
    				$numFields = mysql_num_fields($result);
    
    				fwrite($handle, 'DROP TABLE IF EXISTS '.$table.';');
    				$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
    				fwrite($handle, "\n\n".$row2[1].";\n\n");
    
    				for ($i = 0; $i < $numFields; $i++){
    					while($row = mysql_fetch_row($result)){
    						fwrite($handle, 'INSERT INTO '.$table.' VALUES(');
    						for($j=0; $j<$numFields; $j++){
    							$row[$j] = addslashes($row[$j]);
    							$row[$j] = ereg_replace("\n","\\n",$row[$j]);
    							if (isset($row[$j]))
    								fwrite($handle,  '"'.$row[$j].'"') ;
    							else
    								fwrite($handle,  '""');
    
    							if ($j < ($numFields-1))
    								fwrite($handle,',');
    						}
    						fwrite($handle,  ");\n");
    					}
    				}
    				fwrite($handle, "\n\n\n");
    				echo " OK" . "<br />";
    			}
			}
			fclose($handle);
		}
		catch (Exception $e){
			var_dump($e->getMessage());
			return false;
		}
		if($maxBackupCount > 0) //if we should keep no more than x files
            echo "<br /><br />Pruning Files<br />";
			$this->pruneFiles($outputDir,$maxBackupCount);
		return true;
	}
	
	// Delete additional copies so we only hang on to a specified number of them
	protected function pruneFiles($outputDir, $maxFiles){
		
		if ( substr($outputDir, -1) != "/" )
            $outputDir = $outputDir."/"; 
            		
        $files = glob($outputDir . "*.sql");
        echo "Output dir is '" . $outputDir . "*.sql'<br />";
        echo "Server has " . count($files) . " files<br />";
		
		if(count($files) > $maxFiles){
			$backuplist = array();
			foreach($files as $file) //build list of date  + filename
				$backuplist[filemtime($file)] = $file;
			krsort($backuplist); //put oldest on bottom
			while (count($backuplist) > $maxFiles){
				$todelete = array_pop($backuplist);
				unlink($todelete);
			}
		}
	}
}
?>