<?php

class DB{
    private static $_instance = null;
    private $_pdo,
            $_query,
            $_error = false,
            $_results,
            $_count = 0,
            $_clause = '',
            $_pdoParams = [];

    private function __construct(){
        try{
            $this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname='. Config::get('mysql/db'),Config::get('mysql/username'),Config::get('mysql/password'));
            $this->_pdo->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
        } catch(PDOException $e){
            die ($e->getMessage());
        }
    }

    public static function getInstance(){
        if(!isset(self::$_instance)){
            self::$_instance = new DB();
        }
        return self::$_instance;
    }


    public function query($sql, $params = array()){
var_dump($this->_pdoParams);
        $this->_error = false;
        if($this->_query = $this->_pdo->prepare($sql.$this->_clause)){

            if($this->_query->execute($this->_pdoParams)){
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            } else {
                $this->_error = true;
            }
        }
        return $this;
    }

    public function action($action, $table, $where = array()){

        $value='';

        if(count($where) === 3){
            $operators = array('=','>','<','>=','<=','like');

            $field      = $where[0];
            $operator   = $where[1];
            $value      = $where[2];

            if(in_array($operator, $operators)){
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";

            }else{
                throw new RuntimeException('Unknown operator in database function : action(),only support '.print_r($operators));
            }
        }else{
            $sql = "{$action} FROM {$table}";
        }
        if($this->query($sql,array($value))->error()){
            throw new RuntimeException('Fail to execute query in database function : action(),check your statement.');
        }else{
            return $this;
        }

    }

    public function get($table, $where = array()){
        return $this->action('SELECT *', $table, $where);
    }

    public function toHtml($table, $where = array()){
        return htmlspecialchars($this->action('SELECT *', $table, $where));
    }

    public function delete($table, $where = array()){
        return $this->action('DELETE',$table,$where);
    }

    public function insert($table, $fields)
    {
        print_r($fields);
        if (isset($fields) && isset($table)) {

            $matches=$this->craftingField($fields);
            $keys = implode('`,`', $matches[1]);
            $params = implode(',:', $matches[1]);

            $binds = [];
            foreach ($matches[1] as $index => $bindkey) {
                $binds[':' . $bindkey] = $matches[2][$index];
            }

            $sql = "INSERT INTO $table (`{$keys}`) VALUES (:{$params})";
            if($this->query($sql,$binds)->error()){
                return false;
            }else{
                return true;
            }

        }else{
            throw new RuntimeException('Missing parameter in insert() from Class DB.');
        }
/*                if(isset($fields) && isset($table)){
            $keys = array_keys($fields);
            $values = '';
            $x = 1;

            $sql = "INSERT INTO user (`".implode('`,`',$keys)."`) VALUES ({$values})";
            echo $sql;
            return;
        }else{
            throw new RuntimeException('Missing parameters in database function : insert()');
        }
        return false;  */
    }

    private function craftingField($fields){
        if(is_array($fields)){
            $sortedField;
            foreach($fields as $index=>$value){
                $sortedField[1][]=$index;
                $sortedField[2][]=$value;
            }
            return $sortedField;
        }

        if(preg_match_all('/([^,]+?)=([^,]+?)(?:,|$)/', $fields, $matches)){
            return $matches;
        }

        throw new RuntimeException('Unaccepte field format in Class DB,use array(index1=>value1,index2=>value2) or string as index1=value1,index2=value2');
    }

    public function update($table,$fields,$where){

        $matches=$this->craftingField($fields);

        $params='';
        foreach($matches[1] as $val){
            $params[] = "`{$val}`=:{$val}";
        }
        $params = implode(',',$params);

        $where;


        $binds = [];
        foreach ($matches[1] as $index => $bindkey) {
            $binds[':' . $bindkey] = $matches[2][$index];
        }

        $sql="UPDATE `{$table}` SET {$params} WHERE {$where}";

       if($this->query($sql,$binds)->error()){
           throw new RuntimeException('Fail to update item in database with update() in Class DB');
       }else{
           return true;
       }
    }

    /**
    * @param string $table
    * @param array $fields   Format as: array(  0=>array(fieldOne=>value1,fieldTwo=>value2),
    *                                            1=>array(fieldOne=>value3,fieldThree=value4),
    *                                            2=>array(fieldThree=>value5))
    *
    *
    */

    public function multiUpdate($table,$fields){
        $arr = $this->fieldsArray($fields);
     
        $count=0;
        $currCount=0;

        foreach($arr as $val){
            $currCount=max(array_keys($val));
            if($count<$currCount){
                $count=$currCount;
            }
        }

        $indexStmt=implode("`,`",array_keys($arr));

        $paramStmt='';
        $duplicateStmt='';
        $i=0;
        $binds=[];

        while($i<=$count){
            $paramStmt.='(';
            foreach($arr as $key=>$val){
                if($i===0){$duplicateStmt.='`'.$key.'`=VALUES(`'.$key.'`),';}

                $paramStmt.='?,';
                          
                $binds[]=isset($val[$i])?$val[$i]:null;

            }
        $paramStmt=rtrim($paramStmt,",");
        $paramStmt.='),';
        $i++;
        }
        $paramStmt=rtrim($paramStmt,",");
        $duplicateStmt=rtrim($duplicateStmt,",");

        $sql= "INSERT INTO {$table} (`{$indexStmt}`) VALUES {$paramStmt} ON DUPLICATE KEY UPDATE {$duplicateStmt}";

        if($this->query($sql,$binds)->error()){
           throw new RuntimeException('Fail to update item in database with update() in Class DB');
       }else{
           return true;
       }

    }

    public function fieldsArray($arr){
        $output=[];
        foreach($arr as $outerindex=>$outerval){
                 foreach($outerval as $key=>$value){
                        $output[$key][$outerindex]=$value;
                        }
            }
        return $output;
    }


    public function results(){
        return $this->_results;
    }

    public function first(){
        return $this->results()[0];
    }

    public function all($column){
        $temp=array();
        if(isset($this->results()[0]->$column)){
            foreach($this->results() as $value){
                $temp[]=$value->$column;
            }
        }else{
            throw new RuntimeException('Unknown column name in database function : all().');
        }

        return $temp;
    }

    public function error(){
        return $this->_error;
    }

    public function count(){
        return $this->_count;
    }

    public function limit($limit,$offset){

        $this->_clause .= ' LIMIT ? OFFSET ?';
        $this->_pdoParams[] = $limit;
        $this->_pdoParams[] = $offset;
        return $this;
    }

    public function where($args = array()){

    //currently need a whilelist to restrict query field

        $operators = array('=','>','<','>=','<=','like','in');
        $tempArr = '';
        $pdoParam = [];

        $this->_clause .= ' WHERE ';
        if(is_array($args[0])){
            $tempArr = [];
            foreach($args as $where){
                if(in_array($where[1],$operators)){

                $tempArr[] = "{$where[0]} {$where[1]} ?";
                $this->_pdoParams[] = $where[2];
                
                } else {
                    throw new RuntimeException('Unexpected where statment.');
                }
            }
            $this->_clause .= implode(' AND ',$pdoParam);
        } else {
            if(in_array($args[1],$operators)){
                $this->_clause .= "{$args[0]} {$args[1]} ?";
                $this->_pdoParams[] = $args[2];
            } else {
                throw new RuntimeException('Unexpected where statment.');
            }
        }
        return $this;
    }


    public function whileList(){
        //create whileList from array/object,for query columns and/or tables
    }

    public function join(){
        //join tables method
    }
}

