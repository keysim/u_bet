<?php
/**
* DB interactions
**/
class Model
{
    private $table;
    protected static $_pdo = null;

    function __construct($table)
    {
        $user = "root";
        $password = "";
        $database = "bet";
        $host = "localhost";

        if (self::$_pdo === null) {
            try {
                $this->db = new PDO('mysql:host='.$host.';dbname='.$database, $user, $password);
            } catch (PDOException $e) {
                die(json_encode(array("error"=> $e->getMessage())));
            }
        }

        $this->table = strtolower($table);
    }

    function add($data)
    {
        foreach ($data as $key => $value) {
            $keys[] = $key;
            $values[] = $value;
        }
        $strSQL = "INSERT INTO " . $this->table . " (";
        foreach ($keys as $ky => $k)
            $strSQL .= $k . ",";
        $strSQL = substr($strSQL, 0, -1) . ") VALUES(";
        foreach ($values as $vl => $v)
            $strSQL .= "?,";
        $strSQL = substr($strSQL, 0, -1) . ")";
        $query = $this->db->prepare($strSQL);
        if ($query->execute($values))
            return $this->db->lastInsertId();
        return false;
    }

    function update($id, $data)
    {
        foreach ($data as $key => $value) {
            $keys[] = $key;
            $values[] = $value;
        }
        $strSQL = "UPDATE " . $this->table . " SET ";
        foreach ($data as $key => $value) {
            $strSQL .= $key . "=?,";
        }
        $strSQL = substr($strSQL, 0, -1) . " WHERE id = ?";
        $values[] = $id;
        $query = $this->db->prepare($strSQL);
        if ($query->execute($values)) {
            if($query->rowCount() <= 0)
                return false;
            return true;
        }
        return false;
    }

    function delete($id)
    {
        $strSQL = "DELETE FROM " . $this->table . " WHERE id = ?";
        $query = $this->db->prepare($strSQL);
        if ($query->execute(array($id))) {
            if($query->rowCount() <= 0)
                return false;
            return true;
        }
        return false;
    }

    function get($columns = null, $where = null, $one = false)
    {
        $rows = "";
        $clause = "";
        $values = [];
        if ($columns == '*')
            $columns = ["*"];

        foreach ($columns as $column)
            $rows .= (($clause == "") ? "" : ", ") . $column;

        if (!is_null($where) && is_array($where)) {
            foreach ($where as $k => $v) {
                $clause .= (($clause == "") ? "" : "AND ") . $k . " = ? ";
                $values[] = $v;
            }
            $clause = " WHERE " . $clause;
        }
        $strSQL = "SELECT " . $rows . " FROM " . $this->table . $clause;
        $query = $this->db->prepare($strSQL);
        if(!$query->execute(@$values))
            return ["error"=> "Can't execute request wrong db probably."];
        if($one)
            return $query->fetch(PDO::FETCH_ASSOC);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function maskFields($data, $mask) {
        $result = [];
        for($i = 0; $i < count($mask); $i++){
            if(!isset($data[$mask[$i]]) || $data[$mask[$i]] === "")
                return ["error" => "Field '" . $mask[$i] . "' missing or empty."];
            $result[$mask[$i]] = $data[$mask[$i]];
        }
        return $result;
    }
}