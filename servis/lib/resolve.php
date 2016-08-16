<?php
abstract class Resolve
{

	static function getError($code, $errorInfo = '')
	{
		self::getHeader($code);
		if ($errorInfo) {
			self::getHeader("JSON");
			$resolve = "ErrorInfo: " . $errorInfo;
			die(self::encode($resolve));
		}
		die();
	}

	public static function getAnswer($result)
	{
		self::getHeader(200);
		self::getHeader("JSON");
		return self::encode($result);
	}

	private static function getHeader($code)
	{
		switch ($code) {
			case 404: {
				header("HTTP/1.1 404 Not Found");
			}; break;
			case 201: {
				header("HTTP/1.1 201 Created");
			}; break;
			case 206: {
				header("HTTP/1.1 204 No Content");
			}; break;
			case 400: {
				header("HTTP/1.1 400 Bad Request");
			}; break;
			case 200: {
				header("HTTP/1.1 200 OK");
			}; break;
			case "JSON": {
				header("Content-Type:application/json");
			}; break;
			default: {
				header("HTTP/1.1 400 Bad Request");
			}
		}
	}

	private static function encode($data)
	{
		return json_encode($data);
	}

}
?>