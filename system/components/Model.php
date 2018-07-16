<?php


namespace system\components;


abstract class Model
{

    public $errors = [];

    protected function rules() {
    return [];
    }

    protected static function modelName(){
        $classNames = explode('\\', static::class);
        $name = array_pop($classNames);

        return $name;
    }

    public function load(array $request) {
        if (isset($request[static::modelName()])) {
            $data = $request[static::modelName()];

            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
            return true;
        } else {
            return false;
        }
    }
    public function addError(string $attribute, string $string) {
        $this->errors[$attribute] = $string;
    }
    public function validate() {
        return true;
    }
}