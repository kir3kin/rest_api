<?php

require_once "db.php";
require_once "request.php";
require_once "config.php";
require_once "resolve.php";

class Controller
{

	private $db;
	private $request;

	public function __construct()
	{
		$this->db = new Database(Config::DB_HOST, Config::DB_USER, Config::DB_PASSWORD, Config::DB_NAME, Config::DB_PREFIX);
		$this->request = new Request();
	}

	public function index()
	{
		$resource = $this->request->resource;
		$get = $this->request->get;

		if ($resource === -1) Resolve::getError(400,'incorrect value');
		if (!empty($resource)) {

			$table_name = $resource[0];
			if (isset($resource[1])) {
				if ((int)$resource[1] >= 1) {
					$where_id = "`id`='" . $resource[1] . "'";
				} else Resolve::getError(400, 'incorrect value');
			}

			if (isset($resource[2])) Resolve::getError(400,'excess data');

			$fields = '*';
			$order_by = 'id';
			$sort = "false";//asc
			$limit = 10;
			$offset = 0;
			$meta = "true";

			if (!empty($get)) {
				if (isset($get['fields'])) {
					if (!empty($get['fields'])) {
						$fields = $get['fields'];
					} else Resolve::getError(400, 'empty value');
				}

				if ((isset($get['value']) || isset($get['between'])) && !isset($get['where'])) Resolve::getError(400, 'you need to use operator "where" with operators "value" OR "between"');

				if (isset($get['where'])) {
					if (empty($get['where'])) Resolve::getError(400, 'empty value');

					if (isset($get['between']) && isset($get['value'])) Resolve::getError(400, 'you can use only one of this operators "value" OR "between" at the same time');

					if (!isset($get['value']) && !isset($get['between'])) Resolve::getError(400, 'you cannot use operator "where" without operators "value" OR "between"');

					if (isset($where_id)) Resolve::getError(400, 'you cannot use operator "where" and the second value of main request at the same time');

					if (isset($get['between'])) {
						 if (empty($get['between'])) Resolve::getError(400, 'empty value');
						$bet_ween = explode(',', $get['between']);
						if (count($bet_ween) !== 2) Resolve::getError(400, 'incorrect value');
						$where_id = "`" . $get['where'] . "` BETWEEN '" . $bet_ween[0] . "' AND '" . $bet_ween[1] . "'";
					}

					if (isset($get['value'])) {
						if (empty($get['value'])) Resolve::getError(400, 'empty value');
						if ($get['where'] === 'players_id') {
							$where_id = "`" . $get['where'] . "` LIKE '%," . $get['value'] . "' OR `" . $get['where'] . "` LIKE '" . $get['value'] . ",%' OR `" . $get['where'] . "` LIKE '%," . $get['value'] . ",%'";
						} else {
							$where_id = "`" . $get['where'] . "`='" . $get['value'] . "'";
						}
					}
				}

				if (isset($get['order_by'])) {
					if (empty($get['order_by'])) Resolve::getError(400, 'empty value');
					$order_by = $get['order_by'];
				}

				if (isset($get['sort'])) {
					if (empty($get['sort'])) Resolve::getError(400, 'empty value');
					if (($get['sort'] !== "true") && ($get['sort'] !== "false")) Resolve::getError(400, 'incorrect value');
					$sort = $get['sort'];
				}

				if (isset($get['limit'])) {
					if (empty($get['limit'])) Resolve::getError(400, 'empty value');
					if ((int) $get['limit'] <= 0) Resolve::getError(400, 'incorrect value');
					$limit = $get['limit'];
				}

				if (isset($get['offset'])) {
					if (empty($get['offset'])) Resolve::getError(400, 'empty value');
					if ((int)$get['offset'] <= 0) Resolve::getError(400, 'incorrect value');
					$offset = $get['offset'];
				}

				if (isset($get['meta'])) {
					if (empty($get['meta'])) Resolve::getError(400, 'empty value');
					if (($get['meta'] !== "true") && ($get['meta'] !== "false")) Resolve::getError(400, 'incorrect value');
					$meta = $get['meta'];
				}
			}

			switch ($this->request->method) {
				case 'GET': {
					$result = $this->db->read($table_name, $fields, $where_id, $order_by, $sort, $limit, $offset, $meta);// return request data
					if (!$result) Resolve::getError(404);
				}; break;
				case 'POST': {
					if ($where_id) Resolve::getError(400, "excess data(second value of main request)");
					$result = $this->db->create($table_name, $this->request->post);// return last insert id
					if (!$result) Resolve::getError(400);
					$result = "id_last_record=" . $result;
				}; break;
				case 'PUT': {
					if (!$where_id) Resolve::getError(400, "need data(second value of main request)");
					$result = $this->db->update($table_name, $this->request->put, $where_id);
					if (!$result) Resolve::getError(400);
				}; break;
				case 'DELETE': {
					$result = $this->db->delete($table_name, $where_id);
					if (!$result) Resolve::getError(400);
				}; break;
				default: {
					Resolve::getError(400);
				}; break;
			}
			echo Resolve::getAnswer($result);
		} else Resolve::getError(400, 'empty request');
	}
}
?>