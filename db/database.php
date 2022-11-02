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

    public function checkUsername($username){
        $stmt = $this->db->prepare("select * from user where username =  ?");
        $stmt->bind_param("s",$username);
        $stmt->execute();
        if(sizeof($stmt->get_result()->fetch_all(MYSQLI_ASSOC))!= 0){
            return false;
        }
        return true;
    }

    public function registerUser($username,$password,$firstname,$lastname){
        if($this->checkUsername($username)){
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
            echo "name_check_ok";
        } else {
            echo "name_check_fail";
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
    public function insertFantaBoulder($name,$grade,$date,$img,$userId){
        $query = "INSERT INTO `fantaboulder` ( `name`, `grade`, `date`, `img`,`user_id`) VALUES (?,?,?,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssi",$name,$grade,$date,$img,$userId);
        $stmt->execute();
        return $stmt->insert_id;        //return $this->insertTracciatura($idBoulder,$userId);
    }

    public function getFantaBouldersAndTracciatore(){
        $query = "select u.username as tracciatore,f.* from fantaboulder f join user u on (u.id = f.user_id)";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    }

    public function removeFantaBoulder($fantaId){
        $query = "DELETE FROM `fantaboulder` WHERE `fantaboulder`.`id` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i",$fantaId);
        $stmt->execute();
    }

    public function getFantaboulder($id){
        $query = "select * from fantaboulder where id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i",$id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function promoveFantaboulderToBoulder($fantaboulderId){
        $fantaboulders = $this->getFantaboulder($fantaboulderId);
        if(sizeof($fantaboulders)==0){
            return;
        }
        $fantaboulder = $fantaboulders[0];
        $newBoulderId = $this->insertBoulder($fantaboulder["name"],$fantaboulder["grade"],$fantaboulder["date"],false,$fantaboulder["img"]);
        $this->insertTracciatura($newBoulderId,$fantaboulder["user_id"]);
        $this->removeFantaBoulder($fantaboulderId);
    }

    public function updateFirstname($firstname,$id){
        $sql = "update user set first_name='${firstname}' where id=${id}";
        return mysqli_query($this->db,$sql);
    }
    public function updateLastname($lastname,$id){
        $sql = "update user set last_name='${lastname}' where id=${id}";
        return mysqli_query($this->db,$sql);
    }

    public function updateUsername($username,$id){
        $sql = "update user set username='${username}' where id=${id}";
        return mysqli_query($this->db,$sql);
    }

    public function insertComment($text,$rating,$grade,$user_id){
        $sql = NULL;
        $stmt = NULL;

        if(strcmp($text,'""')==0){
            $sql = "insert into comment (text,rating,grade,user_id) value (null,?,?,?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("isi",$rating,$grade,$user_id);
        } else {
            $sql = "insert into comment (text,rating,grade,user_id) value (?,?,?,?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("sisi",$text,$rating,$grade,$user_id);
        }
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function insertCompletedBoulder($commentId,$date,$user_id,$boulderId,$numOfTries){
        $sql = "insert into completed_boulder(comment_id,date,user_id,boulder_id,number_of_tries) value (?,?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isiii",$commentId,$date,$user_id,$boulderId,$numOfTries);
        return $stmt->execute();

    }

    public function deleteComment($id){
        $sql = "delete from comment where id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i",$id);
        $stmt->execute();
    }
}