<?php
class Account{
	private $nom;
	private $prenom;
	private $login;
	private $password;
	private $statut;

	public function __construct($nom,$prenom,$log,$pass,$stat){
		$this->nom=$nom;
		$this->prenom=$prenom;
		$this->login=$log;
		$this->password=$pass;
		$this->statut=$stat;
	}

	public function getNom(){
		return $this->nom;
	}
	public function getPrenom(){
		return $this->prenom;
	}
	public function getLogin(){
		return $this->login;
	}
	public function getStatut(){
		return $this->statut;
	}

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }




}

?>