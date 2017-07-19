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
$g_dbconn = new MySQLi("127.0.0.1", "USERNAME", "PASSWORD", "DBNAME");
$g_archivepath = "/archive/path";

function site_login()
{
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
		_site_login_failres();
		return false;
	}else{
		if(($_SERVER['PHP_AUTH_USER'] == "admin") && ($_SERVER['PHP_AUTH_PW'] == "admin"))
		{
			return true;
		}else{
			_site_login_failres();
			return false;
		}
	}
}

if(!function_exists("hex2bin"))
{
	function hex2bin($hex_string)
	{
		$pos = 0;
		$result = '';
		$hex_string = trim($hex_string);
		while ($pos < strlen($hex_string)) {
			$code = hexdec(substr($hex_string, $pos, 2));
			$pos = $pos + 2;
			$result .= chr($code); 
		}
		return $result;
	}
}

?>