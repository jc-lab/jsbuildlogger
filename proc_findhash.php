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

$stmt_findhash = $g_dbconn->prepare("SELECT * FROM `buildlog_hashs` INNER JOIN `buildlogs` ON `buildlogs`.`idx`=`buildlog_hashs`.`build_idx` WHERE `hash`=?");

$input_filehashbin = hex2bin($_POST['filehash']);
$stmt_findhash->bind_param("s", $input_filehashbin);
$stmt_findhash->execute();
$dbresult = $stmt_findhash->get_result();
$dbrow = $dbresult->fetch_array();
if($dbrow)
{
	$resultjson = array(
		"found" => true,
		"build_idx" => $dbrow['build_idx'],
		"filename" => $dbrow['filename'],
		"archivefile" => $dbrow['archivefile']
	);
}else{
	$resultjson = array(
		"found" => false
	);
}
echo json_encode($resultjson, JSON_UNESCAPED_UNICODE);
?>