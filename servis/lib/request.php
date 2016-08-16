<?php
class Request {

	private $server = array();

	public $resource = array();
	public $method = "";
	public $post = array();
	public $get = array();
	public $put = array();

	public function __construct() {
		$this->server = $this->clean($_SERVER);
		$this->get = $this->clean($_GET);
		$this->post = $this->clean($_POST);
		$this->put = $this->getPUT();
		$this->method = $this->getMethod();
		$this->resource = $this->getResource();
	}

	private function clean($data) {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				unset($data[$key]);
				$data[$this->clean($key)] = $this->clean($value);
			}
		} else {
			$data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
		}
		return $data;
	}

	private function getPUT()
	{
		if($this->getMethod() === 'PUT') {
			$request = file_get_contents('php://input');
			$exploded = explode('&', $request);
			$putData = array();

			foreach($exploded as $pair) {
				$item = explode('=', $pair);
				if(count($item) == 2) {
					$putData[urldecode($item[0])] = urldecode($item[1]);
				}
			}
			return $this->clean($putData);
		}
		else return array();
	}

	private function getMethod()
	{
		return $this->server["REQUEST_METHOD"];
	}

	private function getResource()
	{
		$data = $this->server["REQUEST_URI"];
		if (preg_match('/\/[-_\.]+/i', $data)) return -1;
		preg_match_all('/\/(\w+)/i', $data, $match);
		return $match[1];
	}
}
?>