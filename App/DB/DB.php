<?php namespace App\DB;

use App\Config\Config;


class DB {

    /**
     * The database array containing all relevant
     * data such as table name.
     *
     * @var $dbData
     */
    private $dbData;
    /**
     * The database instance.
     *
     * @var $databaseConnection
     */
    private $dbConnection;

    /**
     * Singleton instance
     *
     * @var $instance
     */
    private static $instance = null;

    public function __construct() {
        $config = new Config();
        $this->dbData = $config->getDBArray();
        $this->dbConnection = $this->connect();
    }

    /**
     * Instantiates a new connection  for the Database class
     *
     * @return  $con_db  object
     */
    public function connect(){
        $data = $this->dbData;
        $db = $data['DB_NAME'];
        $con_db = new \PDO("mysql:host=127.0.0.1;dbname=$db", $data['DB_USER'], $data['DB_PASS']);
        $con_db->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);

        return $con_db;
    }

    /**
     * Returns a specific record from given table
     *
     * @param  string  $id
     * @param  string  $table
     * @return  array  $view
     */
    public function getRecordByID($id, $table){
        $id = addslashes(htmlspecialchars($id));
        try{
            $stmt = $this->dbConnection->prepare("SELECT * FROM  $table  " .
                " WHERE id =:id LIMIT 1");
            $stmt->bindParam(':id',$id,\PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll();

            return $result;
        }catch(\PDOException $e){
            die('Error!: '.$e->getMessage());
        }
    }



    /**
     * Returns specific record obtained by value
     *
     * @param  	string  $val
     * @param  	string  $table
     * @param  	string  $field1
     * @param  	string  $field2
     */
    public function getRecordByVal($table, $field1 = null, $field2 = null, $val, $amount = null){
        $val=addslashes(htmlspecialchars($val));
        try{
            $stmt = $this->dbConnection->prepare("SELECT * FROM $table " .
                " WHERE ".$field1." =:val or ".$field2." =:val ORDER BY id DESC LIMIT " . $amount); //.($page*10).", 10"
            $stmt->bindParam(':val',$val,\PDO::PARAM_STR,12);
            $stmt->execute();
            $result = $stmt->fetchAll();

            return $result;
        }catch(PDOException $e){
            die('Error!: '.$e->getMessage());
        }
    }


    /**
     * Returns amount of records of a given table
     *
     * @param  string  $table
     * @return  array  $number
     */
    public function getPageCount($table){
        $stmt = $this->dbConnection->prepare("SELECT COUNT(*) AS total FROM $table ");
        $stmt->execute();
        $number = $stmt->fetchAll();

        return $number;
    }



    /**
     * Inserts a single record into a given table
     *
     * @param  string  $table
     * @param  array  $fields
     * @return  array  mixed
     */

    public function insert($table, array $fields){
        if(count($fields)){
            $keys = array_keys($fields);
            $sql = "INSERT INTO {$table} (".implode(',', $keys).") ";
            $pholder = $this->getPlaceHolder($fields);
            $sql .= " VALUES({$pholder}) ";
            try{
                $stmt = $this->dbConnection->prepare($sql);
                $pos = 1;

                if(count($fields)){
                    foreach ($fields as $param) {
                        $stmt->bindValue($pos, $param);
                        $pos++;
                    }
                }

                $post = $stmt->execute();
                if($post){
                    return  'Post sent';
                }
            }catch(PDOException $e){
                die('Error!: '.$e->getMessage());
            }
        }
    }


    public static function getInstance() {
        if(!isset(self::$instance)) {
            self::$instance = new Database;
        }
        return self::$instance;
    }

    /**
     * Creates placeholders for db query
     *
     * @param  array  $fields
     * @return  array  string
     */
    public function getPlaceHolder($fields = array()){
        $pholder = str_repeat ( "?, " , count($fields)-1 );

        return $pholder."?";
    }


    /**
     * Returns a list obtained from a given table
     *
     * @param  	int  	$page
     * @param  	string  $table
     * @return  array  	$con_db
     */
    public function paginate($page,$table, $amount){
        $page=htmlspecialchars($page);
        $stmt = $this->dbConnection->prepare("SELECT * FROM $table ORDER BY id ASC LIMIT ".($page * $amount).", $amount");
        $stmt->execute();
        $result = $stmt -> fetchAll();
//        this section of the function is not being used yet
//        if(!$disp){
//            $this->lim_reached = true;
//            $page--;
//            header("location:$table?page=$page");
//        }

        return $result;
    }

}

