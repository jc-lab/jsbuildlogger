<!--
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
--><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>무제 문서</title>
<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
<script type="text/javascript" language="javascript" src="sha256.js"></script>
<script>
var g_page_uploadfile_idx = 0;
var g_page_uploadfile_dats = [];

$(document).ready(function () {
	var obj = $("#dropzone");
	
	obj.on('dragenter', function (e) {
		e.stopPropagation();
		e.preventDefault();
		$(this).css('border', '2px solid #5272A0');
	});

	obj.on('dragleave', function (e) {
		e.stopPropagation();
		e.preventDefault();
		$(this).css('border', '2px dotted #8296C2');
	});

	obj.on('dragover', function (e) {
		e.stopPropagation();
		e.preventDefault();
	});

	obj.on('drop', function (e) {
		e.preventDefault();
		$(this).css('border', '2px dotted #8296C2');

		var files = e.originalEvent.dataTransfer.files;
		if(files.length < 1)
			return;

		page_uploadfile_onDrag(files, obj);
	});
});

function arrayBuffer_to_hex(buffer)
{
 	return Array.prototype.map.call(new Uint8Array(buffer), x => ('00' + x.toString(16)).slice(-2)).join('');
}

function page_uploadfile_onDrag(files, obj)
{
	for (var fi = 0; fi < files.length; fi++)
	{
		var reader = new FileReader();
		reader.file = files[fi];
		reader.onload = function(e)
		{
			var ofile = e.target.file;
			var data = e.target.result;
			var data_u8arr;
			var filehash;
			
			var odat;
			
			data_u8arr = new Uint8Array(data);
			filehash = arrayBuffer_to_hex(sha256(data_u8arr));
			
			odat = {
				idx: g_page_uploadfile_idx,
				filename: ofile.name,
				filesize: ofile.size,
				filehash: filehash,
				file: ofile
			};
			g_page_uploadfile_idx++;
			g_page_uploadfile_dats[odat.idx] = odat;
			
			$("#tbluploadfilelist > tbody:last").append(
				'<tr data-idx="' + odat.idx + '">' + 
				'	<td class="filename">' + odat.filename + '</td>' + 
				'	<td class="filesize">' + odat.filesize + '</td>' + 
				'	<td class="filehash">' + odat.filehash + '</td>' + 
				'	<td><input type="button" value="REMOVE" onclick="page_uploadfile_remove(' + odat.idx + ')" /></td>' + 
				'	<td class="findhash_state">&nbsp;</td>' + 
				'</tr>');
			page_findhash(odat.idx);
		};
		reader.readAsArrayBuffer(files[fi]);
	}
}

function page_findhash(idx)
{
	var odat = g_page_uploadfile_dats[idx];
	var otr = $("#tbluploadfilelist > tbody > tr[data-idx='" + idx + "']");
	$.ajax({
		url: "./proc_findhash.php",
		method:'post',
		data: {
			filehash: odat.filehash
		},
		enctype: "application/x-www-form-urlencoded",
		dataType: 'json',
		success:function(res)
		{
			if(res.found == true)
			{
				otr.find(".findhash_state").html('found(build_idx: ' + res.build_idx + '):<br />\narchive(<a href="./proc_download.php?build_idx=' + res.build_idx + '">' + res.archivefile + '</a>)<br />\nfile(' + res.filename + ')');
			}else{
				otr.find(".findhash_state").html('Not found in database.');
			}
			
		}
	});
}

function page_uploadfile_remove(idx)
{
	var odat = g_page_uploadfile_dats[idx];
	var otr = $("#tbluploadfilelist > tbody > tr[data-idx='" + idx + "']");
	otr.remove();
	g_page_uploadfile_dats[idx] = null;
}

function page_uploadfile_doUpload()
{
	var formData = new FormData();
	
	for(var i=0; i < g_page_uploadfile_dats.length; i++)
	{
		var odat = g_page_uploadfile_dats[i];
		if(odat == null)
			continue;
		formData.append("uploadfile[]", odat.file);
	}
	
	$.ajax({
		url: "./proc_append.php",
		method:'post',
		data: formData,
		enctype: "multipart/form-data",
		processData:false,
		contentType:false,
		success:function(res){
			alert(res);
			location.reload();
		}
	});
}
</script>
<style>
#dropzone
{
	border:2px dotted #3292A2;
	width:250px;
	height:250px;
	color:#92AAB0;
	text-align:center;
	font-size:24px;
	padding-top:12px;
	margin-top:10px;
}

#tbluploadfilelist thead .filename {
	min-width: 100px;
}
#tbluploadfilelist thead .filesize {
	min-width: 100px;
}
#tbluploadfilelist thead .filehash {
	min-width: 100px;
}
#tbluploadfilelist thead .btn_remove {
}
</style>
</head>

<body>

<div id="dropzone">Drag & Drop Files Here</div> 

<table id="tbluploadfilelist" class="table">
	<thead>
	    <tr>
    		<th class="filename">File Name</th>
    		<th class="filesize">File Size</th>
    		<th class="filehash">Hash</th>
            <th class="btn_remove">Remove</th>
            <th class="findhash_state">State</th>
    	</tr>
    </thead>
    <tbody>
    </tbody>
</table>

<input type="button" value="UPLOAD" onclick="page_uploadfile_doUpload()" />

</body>
</html>
