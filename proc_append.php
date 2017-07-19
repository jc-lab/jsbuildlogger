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

$stmt_insertlog = $g_dbconn->prepare("INSERT INTO `buildlogs` (`time`, `archivefile`) VALUE (UTC_TIMESTAMP(), ?)");
$stmt_inserthash = $g_dbconn->prepare("INSERT INTO `buildlog_hashs` (`hash`, `build_idx`, `filename`) VALUE (?, ?, ?)");

$g_dbconn->begin_transaction();

$archivename = hash('sha256', time().".".mt_rand().".".mt_rand()).".zip";
$archivepath = $g_archivepath."/".$archivename;

$workresult = false;
do {
	$zip = new ZipArchive();
	$fileinfos = array();
	
	$res = $zip->open($archivepath, ZipArchive::CREATE);
	if ($res!==TRUE)
	{
		echo "ZIP Open Error";
		break;
	}
	
	for($i=0; $i<count($_FILES['uploadfile']['name']); $i++)
	{
		$filehash = hash_file('sha256', $_FILES['uploadfile']['tmp_name'][$i], true);
		$res = $zip->addFromString($_FILES['uploadfile']['name'][$i], file_get_contents($_FILES['uploadfile']['tmp_name'][$i]));
		$fileinfos[] = array(
			'filename' => $_FILES['uploadfile']['name'][$i],
			'filehash' => $filehash
		);
	}
	
	$zip->close();
	
	for($i=0; $i<count($_FILES['uploadfile']['name']); $i++)
	{
		unlink($_FILES['uploadfile']['tmp_name'][$i]);
	}
	
	$stmt_insertlog->bind_param("s", $archivename);
	if(!$stmt_insertlog->execute())
	{
		echo "DB Error #2\n".$stmt_insertlog->error;
		break;
	}
	
	$buildlogidx = $stmt_insertlog->insert_id;
	$_tmprst = true;
	for($i=0; $i<count($fileinfos); $i++)
	{
		$stmt_inserthash->bind_param("sis", $fileinfos[$i]['filehash'], $buildlogidx, $fileinfos[$i]['filename']);
		if(!$stmt_inserthash->execute())
		{
			echo "DB Error #3\n".$stmt_inserthash->error;
			$_tmprst = false;
			break;
		}
	}
	if(!$_tmprst)
		break;
	
	echo "Success";
	
	$workresult = true;
} while(false);
if($workresult)
{
	$g_dbconn->commit();
}else{
	$g_dbconn->rollback();
}
?>