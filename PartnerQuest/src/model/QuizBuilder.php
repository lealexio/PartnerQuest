<?php
require_once ("model/Quiz.php");
class QuizBuilder{
	private $data;
    private $settings;
	private $error;

	function __construct($settings=null,$data=null){
        $this->settings=$settings;
		$this->data=$data;
		$this->error=null;
	}

	function getData(){
		return $this->data;
	}

    function getSettings(){
        return $this->settings;
    }

	function getError(){
		return $this->error;
	}
	

	function getNbQuestions(){
	    return htmlspecialchars($this->settings['nbQuestions']);
    }

	function createQuiz(){
        $QA = array();
        for ($i = 1; $i <= htmlspecialchars($this->settings['nbQuestions']); $i++) {
            $stack = array();
            $q=$this->data["Question".$i];
            $stack["Question"]=$q;

            $r=explode("-",$this->data["Reponse".$i]);

            $stack["Reponse"]=$r;

            if(array_key_exists("QCM_Q".$i,$this->data)){
                $qcm=$this->data["QCM_Q".$i];
                $stack["QCM"]=$qcm;
            }
            else{
                $stack["QCM"]="off";
            }
            $QA["Q".$i]=$stack;
        }
        echo"XXXXXXX\n";
        var_export($QA);
        $quiz = new Quiz($_SESSION['user']->getLogin(),htmlspecialchars($this->settings['Nom']),htmlspecialchars($this->settings['nbQuestions']),htmlspecialchars($this->settings['Categorie']),htmlspecialchars($this->settings['Description']),htmlspecialchars($this->settings['Imagetheme']),$QA);
        return $quiz;
    }

	function isValid(){
		if($this->data['Nom']===""){
			$this->error="Le nom saisie".htmlspecialchars($this->data['Nom'])." n'est pas valide";
		}
		if(strlen($this->data['nbQuestions'])<0){
       		$this->error="Le nombre de question que vous avez entrer est trop petit, veuillez le modifier";
        }
        if($this->data['Categorie']===""){
            $this->error="La categorie saisie".htmlspecialchars($this->data['Categorie'])." n'est pas valide";
        }
        if($this->data['Description']===""){
            $this->error="La description saisie".htmlspecialchars($this->data['Description'])." n'est pas valide";
        }
	}

    function isValidSettings(){
        if($this->data['Nom']===""){
            $this->error="Le nom saisie".htmlspecialchars($this->data['Nom'])." n'est pas valide";
        }
        if(((int)$this->data['nbQuestions'])>5 or $this->data['nbQuestions']===""){
            $this->error="Le nombre de question que vous avez entrer est incorrecte, veuillez le modifier";
        }
        if($this->data['Categorie']===""){
            $this->error="La categorie saisie".htmlspecialchars($this->data['Categorie'])." n'est pas valide";
        }
        if($this->data['Description']===""){
            $this->error="La description saisie".htmlspecialchars($this->data['Description'])." n'est pas valide";
        }
    }

}

?>