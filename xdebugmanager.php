<?php
/**
 * Xdebug Manager for DEVSERVER
 * @version  1.7
 * @author   Laurent Abbal <laurent@abbal.com>
 * @link     https://www.easyphp.org
 */

$source = dirname(dirname(__DIR__)) . '\eds-binaries\php\\' . $conf_httpserver['php_folder'] . '\php.ini';

$phpini = file_get_contents($source);

preg_match('/^(.*)zend_extension.*$/m', $phpini, $xdebug_settings['zend_extension']);
preg_match('/^[\s|\t]*xdebug.default_enable.*\=[\s|\t]*(.*)$/m', $phpini, $xdebug_settings['default_enable']);
preg_match('/^[\s|\t]*xdebug.trace_output_dir.*\=[\s|\t]*(.*)$/m', $phpini, $xdebug_settings['trace_output_dir']);
preg_match('/^[\s|\t]*xdebug.profiler_output_dir.*\=[\s|\t]*(.*)$/m', $phpini, $xdebug_settings['profiler_output_dir']);
preg_match('/^[\s|\t]*xdebug.auto_trace.*\=[\s|\t]*(.*)$/m', $phpini, $xdebug_settings['auto_trace']);
preg_match('/^[\s|\t]*xdebug.profiler_enable.*\=[\s|\t]*(.*)$/m', $phpini, $xdebug_settings['profiler_enable']);

if (($xdebug_settings['default_enable'][1] == 0) OR (trim($xdebug_settings['zend_extension'][1]) == "#")) {
	?>

	<form method="post" action="<?php echo 'http://' . $_SERVER["HTTP_HOST"] . '/index.php' ?>" style="float:right;padding:0px;margin:0px;cursor:pointer">
		<input type="hidden" name="default_enable" value="1" />
		<input type="hidden" name="xdebug_dll" value="<?php echo $php_settings['xdebug_dll']; ?>" />
		<input type="hidden" name="action[request][0][type]" value="include" />
		<input type="hidden" name="action[request][0][value]" value="../eds-modules/xdebugmanager/xdebugmanager_update.php" />
		<input type="submit" value="start" onclick="delay();" alt="start" title="start" class="btn btn-primary btn-sm" />
	</form>
	
	<?php
}

if (($xdebug_settings['default_enable'][1] == 1) AND (trim($xdebug_settings['zend_extension'][1]) == "")) {
	?>

	<form method="post" action="<?php echo 'http://' . $_SERVER["HTTP_HOST"] . '/index.php' ?>" style="float:right;padding:0px;margin:0px;cursor:pointer">
		<input type="hidden" name="default_enable" value="0" />
		<input type="hidden" name="auto_trace" value="0" />
		<input type="hidden" name="profiler_enable" value="0" />
		<input type="hidden" name="action[request][0][type]" value="include" />
		<input type="hidden" name="action[request][0][value]" value="../eds-modules/xdebugmanager/xdebugmanager_update.php" />
		<input type="submit" value="stop" onclick="delay();" alt="stop" title="stop" class="btn btn-danger btn-sm" />
	</form>
	
	<?php
	$trace_dir = trim($xdebug_settings['trace_output_dir'][1]);
	$profiler_dir = trim($xdebug_settings['profiler_output_dir'][1]);

	$weeds = array('.', '..');
	$trace_files = array_diff(scandir(str_replace('"', '', $trace_dir)), $weeds); 
	$profiler_files = array_diff(scandir(str_replace('"', '', $profiler_dir)), $weeds); 

	$sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");

	// Trace dir size
	$trace_dir_size = 0;
	foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(str_replace('"', '', $trace_dir))) as $files){
		$trace_dir_size+=$files->getSize();
	}
	if ($trace_dir_size == 0) {
		$trace_dir_size = '0 Bytes';
	} else {
		$trace_dir_size = round($trace_dir_size/pow(1024, ($i = floor(log($trace_dir_size, 1024)))), $i > 1 ? 2 : 0) . $sizes[$i];
	}

	// Profiler dir size
	$profiler_dir_size = 0;
	foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(str_replace('"', '', $profiler_dir))) as $files){
		$profiler_dir_size+=$files->getSize();
	}
	if ($profiler_dir_size == 0) {
		$profiler_dir_size = '0 Bytes';
	} else {
		$profiler_dir_size = round($profiler_dir_size/pow(1024, ($i = floor(log($profiler_dir_size, 1024)))), $i > 1 ? 2 : 0) . $sizes[$i];
	}		
	
	?>
	<div  class="col-sm-12">
		<br />
		<table class="table table-hover table-condensed" style="padding:20px;">
			<tr>
				<td><b style="color:#555">Trace</b></td>
				<td>
					<?php
					// TRACE
					if ($xdebug_settings['auto_trace'][1] == 0) {
						?>
						<form method="post" action="<?php echo 'http://' . $_SERVER["HTTP_HOST"] . '/index.php' ?>" style="float:left;padding:0px;margin:0px;">
							<input type="hidden" name="auto_trace" value="1" />
							<input type="hidden" name="action[request][0][type]" value="include" />
							<input type="hidden" name="action[request][0][value]" value="../eds-modules/xdebugmanager/xdebugmanager_update.php" />
							<input type="submit" value="<?php echo $module_i18n[$lang]['start']; ?>" onclick="delay();" alt="<?php echo $module_i18n[$lang]['start']; ?>" title="<?php echo $module_i18n[$lang]['start']; ?>" class="btn btn-info btn-xs" style="width:60px;opacity:0.5;" />
						</form>
						<?php
					} elseif ($xdebug_settings['auto_trace'][1] == 1) {
						?>
						<form method="post" action="<?php echo 'http://' . $_SERVER["HTTP_HOST"] . '/index.php' ?>" style="float:left;padding:0px;margin:0px;">
							<input type="hidden" name="auto_trace" value="0" />
							<input type="hidden" name="action[request][0][type]" value="include" />
							<input type="hidden" name="action[request][0][value]" value="../eds-modules/xdebugmanager/xdebugmanager_update.php" />
							<input type="submit" value="<?php echo $module_i18n[$lang]['stop']; ?>" onclick="delay();" alt="<?php echo $module_i18n[$lang]['stop']; ?>" title="<?php echo $module_i18n[$lang]['stop']; ?>" class="btn btn-info btn-xs" style="width:60px;" />
						</form>
						<?php
					}
					?>
				</td>
				<td><span class="glyphicon glyphicon-folder-open small" style="color:silver;padding-right:0px;" aria-hidden="true"></span></td>		
				<td style="width:60%">
					<form method="post" action="http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" class="form-inline" role="form" name="trace_dir">
						<input type="hidden" name="action[request][0][type]" value="exe" />
						<input type="hidden" name="action[request][0][value]" value="<?php echo urlencode('explorer ' . str_replace('"', '', $trace_dir)) ?>" />
						<a type="submit" role="submit" class="xdebug_list_directory" data-toggle="tooltip" data-placement="top" title="Explore Directory" onclick="delay();document.forms['trace_dir'].submit()"><samp class="small"><?php echo wordwrap(str_replace('"', '', $trace_dir), 80, "<br />", true) ?></samp></a>
					</form>					
				</td>
				<td style="color:#555;width:20%;text-align:right;" class="small">
					<?php echo count($trace_files) . " " . $module_i18n[$lang]['files'] . " / " . $trace_dir_size; ?>
				</td>
			</tr>
			
			<tr>
				<td><b style="color:#555">Profiler</b></td>
				<td>
					<?php
					// PROFILER
					if ($xdebug_settings['profiler_enable'][1] == 0) {
						?>
						<form method="post" action="<?php echo 'http://' . $_SERVER["HTTP_HOST"] . '/index.php' ?>" style="float:left;padding:0px;margin:0px;">
							<input type="hidden" name="profiler_enable" value="1" />
							<input type="hidden" name="action[request][0][type]" value="include" />
							<input type="hidden" name="action[request][0][value]" value="../eds-modules/xdebugmanager/xdebugmanager_update.php" />
							<input type="submit" value="<?php echo $module_i18n[$lang]['start']; ?>" onclick="delay();" alt="<?php echo $module_i18n[$lang]['start']; ?>" title="<?php echo $module_i18n[$lang]['start']; ?>"  class="btn btn-info btn-xs" style="width:60px;opacity:0.5;" />
						</form>
						<?php
					} elseif ($xdebug_settings['profiler_enable'][1] == 1) {
						?>
						<form method="post" action="<?php echo 'http://' . $_SERVER["HTTP_HOST"] . '/index.php' ?>" style="float:left;padding:0px;margin:0px;">
							<input type="hidden" name="profiler_enable" value="0" />
							<input type="hidden" name="action[request][0][type]" value="include" />
							<input type="hidden" name="action[request][0][value]" value="../eds-modules/xdebugmanager/xdebugmanager_update.php" />
							<input type="submit" value="<?php echo $module_i18n[$lang]['stop']; ?>" onclick="delay();" alt="<?php echo $module_i18n[$lang]['stop']; ?>" title="<?php echo $module_i18n[$lang]['stop']; ?>"  class="btn btn-info btn-xs" style="width:60px;" />
						</form>	
						<?php
					}
					?>
				</td>
				<td><span class="glyphicon glyphicon-folder-open small" style="color:silver;padding-right:0px;" aria-hidden="true"></span></td>	
				<td style="width:60%">
					<form method="post" action="http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" class="form-inline" role="form" name="profiler_dir">
						<input type="hidden" name="action[request][0][type]" value="exe" />
						<input type="hidden" name="action[request][0][value]" value="<?php echo urlencode('explorer ' . str_replace('"', '', $profiler_dir)) ?>" />
						<a type="submit" role="submit" class="xdebug_list_directory" data-toggle="tooltip" data-placement="top" title="Explore Directory" onclick="delay();document.forms['profiler_dir'].submit()"><samp class="small"><?php echo wordwrap(str_replace('"', '', $profiler_dir), 80, "<br />", true) ?></samp></a>
					</form>					
				</td>
				<td style="color:#555;width:20%;text-align:right;" class="small">
					<?php echo count($profiler_files) . " " . $module_i18n[$lang]['files'] . " / " . $profiler_dir_size; ?>
				</td>
			</tr>
		</table>
		
	</div>
	<?php
}
?>