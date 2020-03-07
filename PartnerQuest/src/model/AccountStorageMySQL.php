<?php
require_once("Account.php");
class AccountStorageMySQL{

	public $db;

    public function __construct(PDO $db){
		$this->db=$db;
    }	
    
    public function createUser(array $user){
      //try{
      $rq="INSERT INTO  users (nom,prenom,login,password,statut)VALUES(:nom,:prenom,:login,:pass,:stat)";
      $stmt=$this->db->prepare($rq);
      $stmt->execute($user);
      //return $login;
 	 //}catch(Exception $e){
 	 	//	echo $e->getMessage();
  		//}
    }
    public function readSimpleUsersOnly(){
    	$stmt=$this->db->query("SELECT * FROM users WHERE statut ='user'");
    	return $stmt->fetchAll();
    }

    public function deleteUser($id){
      $rq="DELETE FROM users where login= :a ";
      $stmt=$this->db->prepare($rq);
      $stmt->execute(array(":a"=>$id,));
    }

    public function readUser($id){
    	$stmt=$this->db->query("SELECT * FROM users WHERE login ='".$id."'");
    	$user=$stmt->fetch();
    	return new Account($user["nom"],$user["prenom"],$user["login"],$user["password"],$user["statut"]);
    }
	public function checkAuth($login, $password){
		$stmt = $this->db->query("SELECT * FROM users");
		$usersTab=$stmt->fetchAll();
		foreach ($usersTab as $user) {
			if($user["login"]==$login && password_verify($password,$user["password"])){
				$account = new Account($user["nom"],$user["prenom"],$user["login"],$user["password"],$user["statut"]);

				return $account;
			}
		}
		return null;
	}


	public function updateStat($statut,$id){
	  $rq="UPDATE users SET statut=:stat where login='".$id."'";
      $stmt=$this->db->prepare($rq);
      $stmt->execute(array(":stat" =>$statut,));
      return null;
	}

    public function updateNom($user_id,$nom){
        try {
            $rq="UPDATE users SET nom=:nom where user_id='".$user_id."'";
            $stmt=$this->db->prepare($rq);
            $stmt->execute(array(":nom" =>$nom,));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updatePrenom($user_id,$prenom){
        try {
            $rq="UPDATE users SET prenom=:prenom where user_id='".$user_id."'";
            $stmt=$this->db->prepare($rq);
            $stmt->execute(array(":prenom" =>$prenom,));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function readPicUser($log){
        $stmt=$this->db->query("SELECT img_profile FROM users WHERE login ='".$log."'");
        $pic=$stmt->fetch();
        return $pic;
    }

    public function setPicRef($pic_id,$user){
        $rq="UPDATE users SET img_profile=:pic where login='".$user."'";
        $stmt=$this->db->prepare($rq);
        $stmt->execute(array(":pic" =>$pic_id,));
        return null;
    }

    //FORGOT PASSWORD--------------


    public function updateLogin($user_id,$login){
        try {
            $rq="UPDATE users SET login=:login where user_id='".$user_id."'";
            $stmt=$this->db->prepare($rq);
            $stmt->execute(array(":login" =>$login,));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updatePassword($user_id,$password){
        try {
            $rq="UPDATE users SET password=:password where user_id='".$user_id."'";
            $stmt=$this->db->prepare($rq);
            $stmt->execute(array(":password" =>password_hash(strip_tags($password), PASSWORD_BCRYPT)));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createEmailValidationCode($login,$code){
        echo $login;
        //géneration du code de comfirmation
        $rq = "UPDATE users SET pass_confirme=:code where login='".$login."'";
        $stmt = $this->db->prepare($rq);
        $inf = array(":code" =>$code);
        $stmt->execute($inf);
    }

    public function getResetCode($login){
        $stmt = $this->db->query("SELECT (pass_confirme) FROM users  where login='".$login."'");
        return $stmt->fetch();
    }

    //END FORGOT PASSWORD--------------

}
?>