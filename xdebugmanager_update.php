<?php
/**
 * Xdebug Manager for DEVSERVER
 * @version  1.7
 * @author   Laurent Abbal <laurent@abbal.com>
 * @link     https://www.easyphp.org
 */
 
if ((isset($_POST['default_enable'])) OR (isset($_POST['auto_trace'])) OR (isset($_POST['profiler_enable']))) {

	$source = dirname(dirname(__DIR__)) . '\eds-binaries\php\\' . $conf_httpserver['php_folder'] . '\php.ini';
	$phpini = file_get_contents($source);	
	
	if (isset($_POST['default_enable'])) {
				
		if ($_POST['default_enable'] == 1) {
			// Update zend_extension
			$replacement = '${1}' . '"' . $_POST['xdebug_dll'] . '"';
			$phpini = preg_replace('/^.*(zend_extension\s*=\s*).*$/m', $replacement, $phpini);	
		}
		
		if ($_POST['default_enable'] == 0) {
			// Update zend_extension
			$replacement = '#' . '${1}' . '""';
			$phpini = preg_replace('/^.*(zend_extension\s*=\s*).*$/m', $replacement, $phpini);	
		}
		
		// Update xdebug.default_enable
		$replacement = '${1}' . $_POST['default_enable'];
		$phpini = preg_replace('/^([\s|\t]*xdebug.default_enable.*\=[\s|\t]*)(.*)$/m', $replacement, $phpini);		

		// Update xdebug.trace_output_dir
		$replacement = '${1}' . dirname(dirname(__DIR__)) . '\eds-binaries\xdebug\trace$3';
		$phpini = preg_replace('/^([\s|\t]*xdebug.trace_output_dir.*\=.*\")(.*)(\".*)$/m', $replacement, $phpini);			

		// Update xdebug.profiler_output_dir
		$replacement = '${1}' . dirname(dirname(__DIR__)) . '\eds-binaries\xdebug\profiler$3';
		$phpini = preg_replace('/^([\s|\t]*xdebug.profiler_output_dir.*\=.*\")(.*)(\".*)$/m', $replacement, $phpini);		

		// Update xdebug.remote_port
		$replacement = '${1}' . '9000';
		$phpini = preg_replace('/^([\s|\t]*xdebug.remote_port.*\=[\s|\t]*)(.*)$/m', $replacement, $phpini);			
		
	} 

	if (isset($_POST['auto_trace'])) {
		
		// Update xdebug.auto_trace
		$replacement = '${1}' . $_POST['auto_trace'];
		$phpini = preg_replace('/^([\s|\t]*xdebug.auto_trace.*\=[\s|\t]*)(.*)$/m', $replacement, $phpini);			
		
	}
	
	if (isset($_POST['profiler_enable'])) {
		
		// Update xdebug.profiler_enable
		$replacement = '${1}' . $_POST['profiler_enable'];
		$phpini = preg_replace('/^([\s|\t]*xdebug.profiler_enable.*\=[\s|\t]*)(.*)$/m', $replacement, $phpini);			
		
	}

	
	// Backup old php.ini
	copy($source, dirname(dirname(__DIR__)) . '\eds-binaries\php\\' . $conf_httpserver['php_folder'] . '\php_' . @date("Y-m-d@U") . '.ini');	

	// Save new php.ini
	file_put_contents($source, $phpini);
}

// Restart http server if http server running
if ($eds_httpserver_running == 1) include('../eds-binaries/httpserver/' . $conf_httpserver['httpserver_folder'] . '/eds-app-restart.php');

header("Location: index.php#anchor_xdebugmanager");  
exit;
?>