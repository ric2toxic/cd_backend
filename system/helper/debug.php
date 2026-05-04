<?php
class Debug {
	static $log = array();
	
	// For checking the sql and execution time of queries
	public static function triggerSql($type, $data, &$caller, $duration, $server) {
		if(!isset(self::$log[$type])) 
			self::$log[$type] = array();
		if(!isset(self::$log[$type.'_func'])) 
			self::$log[$type.'_func'] = array();

		self::$log[$type][] = $data;
		self::$log[$type.'_func'][] = array($caller[0]['file'], $caller[0]['line'], 
		    (!empty($caller[1]['class']) ? ' '.$caller[1]['class'].'::'.$caller[1]['function'] : ''), (!empty($caller[2]['class']) ? ' '.$caller[2]['class'].'::'.$caller[2]['function'] : ''), (!empty($caller[3]['class']) ? ' '.$caller[3]['class'].'::'.$caller[3]['function'] : '') );
		self::$log['duration'][] = $duration;
		self::$log['server'][] = $server;
	}
	
	//For Taking the Oputput of the files
	public static function output() {

		if(isset($_REQUEST['profiling']) && $_REQUEST['profiling'] == DEBUG_SQL_PROFILE){
			$sql_num = count(self::$log['sql']);
			//To display how much queries are executed on the particular page
			//echo '<tr><td colspan="3">SQL Queries Executed: ' . $sql_num . '<br/>';
			self::generateSqlProfiling($sql_num);
			
		}
	}

	// while with every page download a csv will be generated if page is having the GET parameter "debug_profiling" and its value should match
	/*
	* $sqlData SqlData Count
	*/
	public static function generateSqlProfiling($sqlData)
	{

		$file_name = 'sql-debug-profiling.csv';
		$filepath = DIR_DLOAD.$file_name;
		$fp = fopen($filepath, 'w');
		
		if(empty($sqlData))
		{
			$data = array("No Data Found.");
			fputcsv($fp, $data);
		} else {
			$csvFileData = array();

			for($i = 0; $i < $sqlData; $i++) {

				$time = abs(round(self::$log['duration'][$i], 6));
		        $sr_no = $i+1;
			    $time = abs(round(self::$log['duration'][$i], 6));
			    $sql  = self::$log['sql'][$i];
			    $path = self::$log['sql_func'][$i][0].' ('.self::$log['sql_func'][$i][1].')';
			    $function = self::$log['sql_func'][$i][2] ? self::$log['sql_func'][$i][2] : '';
			    $path_function = $path . ' ' . $function;
			    $sub_origin_path = self::$log['sql_func'][$i][3] ? self::$log['sql_func'][$i][3] : '';
			    $origin_path = self::$log['sql_func'][$i][4] ? self::$log['sql_func'][$i][4] : '';
			    $hostname = self::$log['server'][$i];

				if($i==0)
			    {
		            //For headers
					$csvFileData[0][0] = 'Sr. No.';
					$csvFileData[0][1] = 'Execution Time';
					$csvFileData[0][2] = 'Queries Executed';
					$csvFileData[0][3] = 'SQL Path';
					$csvFileData[0][4] = 'Sub Origin path';
					$csvFileData[0][5] = 'Origin path';
					$csvFileData[0][6] = 'Server';
				}

			    $csvFileData[$i+1][0] = $sr_no;
			    $csvFileData[$i+1][1] = $time;
			    $csvFileData[$i+1][2] = $sql;
			    $csvFileData[$i+1][3] = $path_function;
			    $csvFileData[$i+1][4] = $sub_origin_path;
			    $csvFileData[$i+1][5] = $origin_path;
			    $csvFileData[$i+1][6] = $hostname;
			
			}
			
			foreach ($csvFileData as $new_value) {
				fputcsv($fp, $new_value);
			}
		}
		fclose($fp);
	}
}
?>