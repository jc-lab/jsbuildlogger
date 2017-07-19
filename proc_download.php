<?php
/*
MIT License

Copyright (c) 2017 JC-Lab

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
*/
require_once("./config.php");

function mb_basename($path) { return end(explode('/',$path)); } 
function utf2euc($str) { return iconv("UTF-8","cp949//IGNORE", $str); }
function is_ie() {
	if(!isset($_SERVER['HTTP_USER_AGENT']))return false;
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) return true; // IE8
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 6.1') !== false) return true; // IE11
	return false;
}


$input_buildidx = intval($_GET['build_idx']);

$stmt_select = $g_dbconn->prepare("SELECT * FROM `buildlogs` WHERE `idx`=?");

$stmt_select->bind_param("i", $input_buildidx);
$stmt_select->execute();
$dbresult = $stmt_select->get_result();
$dbrow = $dbresult->fetch_array();
if($dbrow)
{
	$filepath = $g_archivepath.'/'.$dbrow['archivefile'];
	if(!file_exists($filepath))
	{
		echo "<h1>File not found</h1>";
	}else{
		$filesize = filesize($filepath);
		$filename = mb_basename($filepath);
		if( is_ie() ) $filename = utf2euc($filename);
		
		header("Pragma: public");
		header("Expires: 0");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: $filesize");
		
		readfile($filepath);
	}
}else{
	echo "<h1>Not found</h1>";
}
?>