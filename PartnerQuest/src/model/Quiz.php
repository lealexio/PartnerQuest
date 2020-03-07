<?php
class Quiz{
	private $nom;
	private $nb_quest;
	private $id_user;
	private $categorie;
	private $description;
	private $picture;
	private $questionsAnswers;


	function __construct($iduser,$nom,$nbquest,$categorie,$description,$pic,$questionsAnswers){
		$this->nom=$nom;
		$this->nb_quest=$nbquest;
		$this->categorie=$categorie;
		$this->description=$description;
		$this->picture=$pic;
		$this->id_user=$iduser;
		$this->questionsAnswers=$questionsAnswers;
	}

	public function getQuestionsAnswers(){
		return $this->questionsAnswers;
	}

	public function getNom(){
		return $this->nom;
	}
	public function getNbQuest(){
		return $this->nb_quest;
	}
	public function getIdUser(){
		return $this->idUser;
	}

	public function getCategorie()
	{
		return $this->categorie;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function getPicture()
	{
		return $this->picture;
	}


}
?>
