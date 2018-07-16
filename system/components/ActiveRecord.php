<?php


namespace system\components;

use PDO;

abstract class ActiveRecord extends Model
{

    protected static function tableName()
    {
        return static::modelName();
    }

    private static function tableColumns()
    {
        $db = App::$current->connection;
        $q = $db->prepare("DESCRIBE" . static::tableName());
        $q->execute();

        return $q->fetchAll(PDO::FETCH_COLUMN);
    }

    private static function find(array $params) {
        $db = App::$current->connection;

        $queryString = "select * from `".static::tableName()."`";
        if(!is_null($params) && count($params) > 0) {
            $queryString .= " where ";
            $keys = array_keys($params);

            for ($i = 0; $i < count($params); $i++) {
                if ($i == 0) {
                    $pair = "{$keys[$i]}=:{$keys[$i]}";
                } else {
                    $pair = " and {$keys[$i]}=:{$keys[$i]}";
                }
                $queryString .=$pair;
            }
        }

        $query = $db->prepare($queryString);

        if(!is_null($params) && count($params) > 0) {
            foreach ($params as $key => $val) {
                $type = static::paramType($val);
                $query->bindParam($key, $val, $type);
            }
        }
        $query->execute();

        return $query;
    }
    private static function paramType($value) {
        switch (true) {
            case is_int($value):
                $type = PDO::PARAM_INT;
                break;

            case is_bool($value):
                $type = PDO::PARAM_BOOL;
                break;

            case is_null($value):
                $type = PDO::PARAM_NULL;
                break;

            default:
                $type = PDO::PARAM_STR;
        }
        return $type;
    }
    public static function findOne(array $params = []) {
        $sth = static::find($params);
        $sth->setFetchMode(PDO::FETCH_CLASS, static::class);

        $model = $sth->fetch();
        return $model;
    }
    public static function findAll(array $params = []) {
        return static::find($params)->fetchAll(PDO::FETCH_CLASS, static::class);
    }
    public function findById(int $id) {
        return static::findOne(['id' => $id]);
    }
    public function save() {
        $db = App::$current->connection;

        $tableColumns = static::tableColumns();
        array_shift($tableColumns);

        $columns = [];
        $values = [];

        foreach ($tableColumns as $col) {
            $columns[] = "`{$col}`";
            $values[] = ":{$col}";
        }
        if(isset($this->id)) {
            $queryString = "update `".static::tableName(). "` set UPDATES where 
            `id`={$this->id};";
            $updates = [];

            foreach ($columns as $i => $item) {
                $updates[] = "{$columns[$i]} = {$values[$i]}";
            }

            $queryString = str_replace('UPDATES', implode(',',$updates),$queryString);
        } else {
            $queryString = "insert into `".static::tableName()."` (COLUMNS) values (KEYS);";
       $queryString = str_replace('COLUMNS'.implode(',', $columns),$queryString);
       $queryString = str_replace('KEYS'.implode(',', $values),$queryString);
        }
        $query = $db->prepare($queryString);

        foreach ($tableColumns as $property) {
            $newValue = (isset($this->$property)) ? $this->$property : null;
        $newType = static::paramType($newValue);

        $query->bindValue(':'.$property, $newValue, $newType);
        }

        $result = $query->execute();

        if(!isset($this->id)) {
            if($result) {
                $this->id = $db->lastInsertId();
            }
        }
        return $result;
    }
    public function delete() {
        if (isset($this->id)) {
            $db = App::$current->connection;

            $queryString = "delete from `" . static::tableName()."` where id=:id";
            $query = $db->prepare($queryString);
            $query->bindValue(':id',$this->id, static::paramType($this->id));

            return $query->execute();
        } else {
            return false;
        }
    }
}