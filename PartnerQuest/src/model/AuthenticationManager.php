<?php
require_once('AccountStorageMySQL.php');
class AuthenticationManager 
{
	private $accountStorageMySQL;

	function __construct(AccountStorageMySQL $accountStub)
	{
		$this->accountStorageMySQL=$accountStub;
	}

    public function updateUser($userId,$post){

        $account=$this->accountStorageMySQL->checkAuth($_SESSION["user"]->getLogin(), $post["password"]);
        if ($account!=null ){//IF THE PASSWORD IS CORRECT
            try {
                if($post["nom"]!=null){
                    $this->accountStorageMySQL->updateNom($userId,$post["nom"]);
                    $_SESSION["user"]->setNom($post["nom"]);
                }
                if($post["prenom"]!=null){
                    $this->accountStorageMySQL->updatePrenom($userId,$post["prenom"]);
                    $_SESSION["user"]->setPrenom($post["prenom"]);
                }
                if($post["login"]!=null){
                    $this->accountStorageMySQL->updateLogin($userId,$post["login"]);
                    $_SESSION["user"]->setLogin($post["login"]);
                }
                if($post["newpassword"]!=null){
                    $this->accountStorageMySQL->updatePassword($userId,$post["newpassword"]);
                }
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        else{
            return null;
        }
    }

	public function connectUser($login, $password){
		$account=$this->accountStorageMySQL->checkAuth($login, $password);
		if ($account==null ){
			return false;
		}else{
			$_SESSION['user']=$account;	
			return true;
		}
	}

	public function isUserConnected(){
		if (key_exists('user',$_SESSION)) {
			return true;
		}else{
			return false;
		}
	}

	public function isAdminConnected(){
		if (key_exists('user',$_SESSION)) {
			$account=$_SESSION['user'];
			if ($account->getStatut()==="admin"){
				return true;
			}else{
				return flase;
			}
		}
	}

	public function getUserName(){
		try{
			$account=$_SESSION['user'];
			return $account->getNom();
		}catch (Exception $e) {
    		echo 'Exception reçue : vous n\'êtes pas connécté' ;
		}
		
	}

	public function disconnectUser(){
		session_destroy();
	}
}
?>