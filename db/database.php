<?php 

class DtabaseHelper {
    private $db;

    public function __construct($servername,$username,$password,$dbname){
        $this->db =  new mysqli($servername,$username,$password,$dbname);
        if($this->db->connect_error){
            die("connection failed ".$this->db->connect_error);
        }
    }

    public function getFromTable($tableName){
        $stmt = $this->db->prepare("select * from ".$tableName);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getTables(){
        $stmt = $this->db->prepare("SELECT table_name FROM information_schema.tables WHERE table_schema = 'climbing_app_db'");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function dumpDbToJson(){
        $json = array();
        $tables = $this->getTables();
        foreach($tables as $row){
            foreach($row as $tablename){
                $json[$tablename] = $this->getFromTable($tablename);
            }
        }
        return json_encode($json,JSON_UNESCAPED_SLASHES);
    }

    public function checkUser($username,$password){
        $stmt = $this->db->prepare("select * from user where username= ? and password = ?");
        $stmt->bind_param("ss",$username,$password);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function registerUser($username,$password,$firstname,$lastname){
        $stmt = $this->db->prepare("select * from user where username =  ?");
        $stmt->bind_param("s",$username);
        $stmt->execute();
        if(sizeof($stmt->get_result()->fetch_all(MYSQLI_ASSOC))!= 0){
            echo "registration failed";
        } else {
            $stmt = $this->db->prepare("insert into user(username,password,first_name,last_name) values (?,?,?,?)");
            $stmt->bind_param("ssss",$username,$password,$firstname,$lastname);
            if( $stmt->execute()){
                echo "registration success";
            }
        }
    }

    //todo: da testare
    public function checkBoulderName($name){
        $stmt = $this->db->prepare("select * from boulder where name = ?");
        $stmt->bind_param("s",$name);
        $stmt->execute();
        if(sizeof($stmt->get_result()->fetch_all(MYSQLI_ASSOC))== 0){
            echo "booulder name already taken";
        } else {
            echo "boulder name available";
        }
    }

   

    public function insertBoulder($name,$grade,$date,$isofficial,$img){
        $query = "INSERT INTO `boulder` ( `name`, `grade`, `date`, `is_official`, `img`) VALUES (?,?,?,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssis",$name,$grade,$date,$isofficial,$img);
         $stmt->execute();
        return $stmt->insert_id;
    }

    public function insertTracciatura($idboulder,$iduser){
        $query = "INSERT INTO `tracciatura_boulder` ( `boulder_id`, `user_id`) VALUES ( ?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii",$idboulder,$iduser);
        $stmt->execute();
        return $stmt->insert_id;
    }
    public function insertFantaBoulder($name,$grade,$date,$isofficial,$img,$userId){
        $idBoulder = $this->insertBoulder($name,$grade,$date,$isofficial,$img);
        return $this->insertTracciatura($idBoulder,$userId);
    }
}