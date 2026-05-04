<?php
class ControllerDownloadDownload extends Controller {

	public function index() {
		// we have get data in array from url i.e. file_desc and serialization file_path
		if ( !$this->securefiledownload->downloadFile($this->request->get,'admin') ) {
			header("Location: " . HTTPS_CATALOG);
		}
	}

	// TO download the file on local server when the sql debug progiling is in Parameter and if it checks it matches with value defined in config
	public function debugSql()
	{
		//Will run only if parameter and its value will match
		if(isset($this->request->get['download_sql']) && $this->request->get['download_sql'] == DEBUG_SQL_PROFILE)
		{
			$filename = DIR_DLOAD.'sql-debug-profiling.csv';
			
			//Unlink file after page loads and the file gets download

			if (file_exists($filename)) {	       	
				
			    header('Content-Description: File Transfer');
			    header('Content-Type: application/csv');
			    header('Content-Disposition: attachment; filename="'.basename($filename).'"');
			    header('Expires: 0');
			    header('Cache-Control: must-revalidate');
			    header('Pragma: public');
			    header('Content-Length: ' . filesize($filename));
			    readfile($filename);
			    ob_clean();
				flush();
			    //exit();
			    unlink($filename);
			}
		}
	}
}
