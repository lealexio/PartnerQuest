<?php 
class UserBuilder {
	private $nom;
	private $prenom;
	private $mail;
	private $password;
	private $statut;
	private $data;
	private $error;

	public function __construct(array $data,$error=null){
		$this->nom=strip_tags($data["nom"]);
		$this->prenom=strip_tags($data["prenom"]);
		$this->mail=strip_tags($data["login"]);
		$this->password=password_hash(strip_tags($data["password"]), PASSWORD_BCRYPT);
		$this->statut='user';
		$this->data = $data;
		$this->error=$error;
	}

	public function isValid(){
		if ($this->nom === "" || $this->prenom === "" || $this->mail === "" || $this->password === "") {
			$this->error="champs  invalide";
		}
	}
	public function createUserRQ(){
		$tab=array(':nom' 	=>$this->nom,
				   ':prenom'=>$this->prenom,
				   ':login'	=>$this->mail,
				   ':pass'	=>$this->password,
				   ':stat'	=>$this->statut,
					);
		return $tab;
	}

	public function getUserName(){
		return $this->nom;
	}
    public function getStatut(){
        return $this->statut;
    }
	public function getUserFirstName(){
		return $this->prenom;
	}
	public function getUserEmail(){
		return $this->mail;
	}
	public function getUserPass(){
		return $this->password;
	}
	public function getError(){
		return $this->error;
	}
}
?>