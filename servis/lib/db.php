<?php

class Database
{

	private $mysqli;
	private $prefix;

	public function __construct($db_host, $db_user, $db_password, $db_name, $prefix)
	{
		$this->mysqli = @new mysqli($db_host, $db_user, $db_password, $db_name);
		if ($this->mysqli->connect_errno) exit("Ошибка соединения с базой данных");
		$this->prefix = $prefix;
		$this->mysqli->set_charset("utf8");
	}

	public function read($table_name, $fields, $where, $order_by, $sort, $limit, $offset, $meta)
	{
		if (!$this->changeRating()) return false;
		$table_name = $this->getTableName($table_name);
		if (!$order_by) $order_by = "ORDER BY `id`";
		else {
			$order_by = "ORDER BY `$order_by`";
		}
		if ($sort === "true") $order_by .= " DESC";
		if ($limit) $limit_e = "LIMIT {$offset},{$limit}";
		if ($where) $query = "SELECT $fields FROM $table_name WHERE $where $order_by $limit_e";
		else $query = "SELECT $fields FROM $table_name $order_by $limit_e";
		$result_set = $this->mysqli->query($query);
		if (!$result_set) return false;
		while ($row = $result_set->fetch_assoc()) {
			$data[] = $row;
		}
		$result_set->close();

		if ($meta === "true") {
			//info block
			$query_total = "SELECT COUNT(id) FROM `" . $table_name . "`";
			$total = $this->mysqli->query($query_total);
			$totals = $total->fetch_row();
			$data_old = $data;
			unset($data);
			$data['data'] = $data_old;
			$data['meta'] = array(
				"total" => $totals[0],
				"offset" => $offset,
				"limit" => $limit,
				"sort" => $sort,
			);
			$total->close();
		}
		return $data;
	}

	public function create($table_name, $new_values)
	{
		$table_name = $this->getTableName($table_name);

		$query = "INSERT INTO $table_name (";
		foreach ($new_values as $field => $value) $query .= "`".$field."`,";
		$query = substr($query, 0 , -1);
		$query .= ") VALUES (";
		foreach ($new_values as $value) $query .= "'".addslashes($value)."',";
		$query = substr($query, 0, -1);
		$query .= ")";

		$this->mysqli->begin_transaction();
		if (!$this->mysqli->query($query)) {
			$this->mysqli->rollback();
			return false;
		}
		$data = $this->mysqli->insert_id;
		if (!$this->changeRating()) {
			$this->mysqli->rollback();
			return false;
		} else $this->mysqli->commit();

		// if (!$this->mysqli->query($query)) return false;
		// $data = $this->mysqli->insert_id;
		// if (!$this->changeRating()) return false;
		return $data;
	}

	public function update($table_name, $upd_fields, $where, $is_rating = false)
	{
		$table_name = $this->getTableName($table_name);

		$check = $this->mysqli->query("SELECT * FROM `$table_name` WHERE $where");
		if (!$check || !$check->num_rows) return false;

		$query = "UPDATE $table_name SET ";
		foreach ($upd_fields as $field => $value) {
			if (($field === "id") && (strpos($where, "id") !== false)) return false;
			$query .= "`$field` = '".addslashes($value)."',";
		}
		$query = substr($query, 0, -1);
		$query .= " WHERE $where";

		$this->mysqli->begin_transaction();
		if (!$this->mysqli->query($query)) {
			$this->mysqli->rollback();
			return false;
		}
		if (!$is_rating) {
			if (!$this->changeRating()) {
				$this->mysqli->rollback();
				return false;
			} else $this->mysqli->commit();
		} else $this->mysqli->commit();

		// if (!$this->mysqli->query($query)) return false;
		// if (!$is_rating) {
		// 	if (!$this->changeRating()) return false;
		// }
		return true;
	}

	public function delete($table_name, $where){
		$table_name = $this->getTableName($table_name);
		$query = "DELETE FROM $table_name";
		if ($where) {
			$query .= " WHERE $where";
			$check = $this->mysqli->query("SELECT * FROM `$table_name` WHERE $where");
			if (!$check || !$check->num_rows) return false;
		} else return false;// запрет на удаление всех записей


		$this->mysqli->begin_transaction();
		if (!$this->mysqli->query($query)) {
			$this->mysqli->rollback();
			return false;
		}
		if (!$this->changeRating()) {
			$this->mysqli->rollback();
			return false;
		} else $this->mysqli->commit();


		// if (!$this->mysqli->query($query)) return false;
		// if (!$this->changeRating()) return false;
		return true;
	}


	public function changeRating()
	{
		$players = $this->mysqli->query("SELECT * FROM `mur_players`");
		if (!$players) return false;
		while ($row = $players->fetch_assoc()) {
			$data_players[] = $row;
		}

		$winners = $this->mysqli->query("SELECT `winner_id` FROM `mur_matches` WHERE `winner_id` > '0'");
		while ($row = $winners->fetch_assoc()) {
			$winners_id[] = $row['winner_id'];
		}

		$pat = $this->mysqli->query("SELECT `players_id` FROM `mur_matches` WHERE `winner_id`='0'");
		while ($row = $pat->fetch_assoc()) {
			$players = explode(",", $row['players_id']);
			foreach ($players as $player) {
				$pats_id[] = $player;
			}
		}

		foreach ($data_players as $data_player) {
			$default_rating = false;

			if (empty($data_player['default_rating'])) {
				$default_rating = true;
				$new_rating = (int)$data_player['rating'];
			} else $new_rating = (int)$data_player['default_rating'];

			foreach ($winners_id as $winner_id) {
				if ((int)$data_player['id'] === (int)$winner_id) {
					$new_rating += 2;
				}
			}
			foreach ($pats_id as $pat_id) {
				if ((int)$data_player['id'] === (int)$pat_id) {
					$new_rating++;
				}
			}

			$upd_fields = array('rating' => $new_rating);
			if ($default_rating) {
				$upd_fields['default_rating'] = (int)$data_player['rating'];
			}

			$res = $this->update("players", $upd_fields, "`id`='" . $data_player['id'] . "'", true);
			if (!$res) return false;
		}
		return true;
	}

	private function getTableName($table_name)
	{
		return $this->prefix.$table_name;
	}

	public function __destruct() {
		if (($this->mysqli) && (!$this->mysqli->connect_errno)) $this->mysqli->close();
	}
}
?>