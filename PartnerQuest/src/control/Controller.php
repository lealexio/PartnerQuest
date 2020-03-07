<?php
declare(strict_types=1);
require_once("model/QuizBuilder.php");
require_once("model/UserBuilder.php");
require_once("model/AccountStorageMySQL.php");
require_once("model/Account.php");

class Controller{

	private $view;
	private $accountStorageSQL;
	private $authentificationManager;
	private $quizStorageSQL;
	private $themeBuilder;
	
	function __construct(View $view, QuizStorageMySQL $quizStorageSQL, AccountStorageMySQL $accountStorageSQL, AuthenticationManager $authentificationManager){
		$this->view=$view;
		$this->quizStorageSQL = $quizStorageSQL;
		$this->accountStorageSQL=$accountStorageSQL;
		$this->authentificationManager=$authentificationManager;
	}
	
	public function showHome(){
		$this->view->makeFrontPage($this->quizStorageSQL->readAllTheme());
	}

	public function showSocial($post, $rqtype){
		//$rqtype 0 == ADD FRIEND OR NOTHING
		//$rqtype 1 == DELETE FRIEND
		$userLogin=$_SESSION["user"]->getLogin();
		$userId=$this->quizStorageSQL->getUserIdFromLogin($userLogin);
		$friendList=$this->quizStorageSQL->getFriendsFromLogin($userId);

		if($rqtype==0){
			if((empty($post) OR $post["friend_login"]==$userLogin)AND $rqtype==0){//If no friend request is made
				$this->view->showSocialPage(0,$friendList);
			}
			elseif($this->quizStorageSQL->addFriend($post)==true){//The friend request worked
				$this->view->showSocialPage(1,$friendList);
			}
			else{//The friend request failed
				$this->view->showSocialPage(-1,$friendList);
			}
		}
		elseif ($rqtype==1){//Delete request
			if($post=="" OR $post==null){//If there is an error
				echo"FUCK";
				$this->view->showSocialPage(0,$friendList);
			}else{
				if($this->quizStorageSQL->deleteFriend($post,$userId)==true){//The friend request worked
					$this->view->showSocialPage(2,$friendList);
				}
				else{//The friend request failed
					$this->view->showSocialPage(3,$friendList);
				}

			}
		}

	}

	public function showSocialCommonQuiz($friend_id){
		$userId=$this->quizStorageSQL->getUserIdFromLogin($_SESSION["user"]->getLogin());
		$req=$this->quizStorageSQL->getCommonQuiz($userId,$friend_id);

		if($req==false){//IF THERE IS NO COMMON QUIZ
			$this->view->makeSocialCommonQuiz(false,$friend_id);
		}
		else{
			$this->view->makeSocialCommonQuiz($req,$friend_id);
		}
	}

	public function showSocialCompareQuiz($quiz_id,$friend_id){
		$userId=$this->quizStorageSQL->getUserIdFromLogin($_SESSION["user"]->getLogin());
		$tmp=$this->quizStorageSQL->getCompareQuiz($quiz_id,$userId,$friend_id);
        $user_array=$tmp[0];
        $friend_array=$tmp[1];
        $quiz_details=$tmp[2];

        //var_export($user_array);
        //var_export($friend_array);
        //var_export($quiz_details);


		//$quiz_details["nb_questions"]

		$affinity=array();

        foreach ($user_array as $value){
            $allFriendAnswers=$friend_array[$value["question_id"]]["reponse"];
            $commonAnswers[$value["question_id"]]=array_intersect($value["reponse"],$allFriendAnswers); //REPONSES EN COMMUN

            $nbCommonAnswers=count($commonAnswers[$value["question_id"]]);
            $nbAnswers=max(count($allFriendAnswers),count($value["reponse"]));
            array_push($affinity,$nbCommonAnswers/$nbAnswers);
        }
        $affinity=intval((array_sum($affinity)/count($affinity))*100);
		$this->view->makeSocialCompareQuiz($user_array,$friend_array,$quiz_details,$commonAnswers,$affinity);
	}


	public function showGestionCompte(){
		if($this->authentificationManager->isAdminConnected()){
			$tabUsers=$this->accountStorageSQL->readSimpleUsersOnly();
			$listQuiz=$this->quizStorageSQL->readAllQuiz();

			$this->view->makeGestionAdminPage($tabUsers,$listQuiz);
		}
		else{
			$this->view->makeUnknownPage();
		}
	}

	public function deleteQuiz($quiz_id){
		if($this->quizStorageSQL->deleteQuiz($quiz_id)){//If the old quiz has been deleted, we create a new one
			$this->view->displayAdmin();
		}
		else{//Else if the old quiz hasn't been deleted -> error
			$this->view->makeEditFailPage();
		}
	}

	public function deleteUserQuiz($quiz_id){
		if($this->quizStorageSQL->deleteQuiz($quiz_id)){//If the old quiz has been deleted, we create a new one
			$this->view->displayAccountPage();
		}
		else{//Else if the old quiz hasn't been deleted -> error
			$this->view->makeEditFailPage();
		}
	}

	public function modifyUserStatut($data,$id){
		$this->accountStorageSQL->updateStat($data['statut'],$id);
		$this->view->displayUpdateStatSuccess();
	}

	public function showAccount($post){
		$error="";
		$userId=$this->quizStorageSQL->getUserIdFromLogin($_SESSION["user"]->getLogin());
		$quizOfUser=$this->quizStorageSQL->readQuizOfUser($userId);
		$userAnswers=$this->quizStorageSQL->readAllAnswersOfUser($userId);

		var_export($post);
		//ACCOUNT MODIFICATIONS
		if(!empty($post) AND array_key_exists ( "password",$post )){
			$update=$this->authentificationManager->updateUser($userId,$post);
			if($update==null){
				$error="Le mot de passe est incorrecte!";
			}
			elseif($update==false){
				$error="Erreur";
			}
			elseif($update==true){
				$error="Informations modifiées";
			}

		}
		//--------------------

		//USER PICTURE
		$pic_id=null;
		if(key_exists('file',$_FILES)){ //USER UPLOAD UNE PIC


			include_once("uploadPic.php");
			$pic_id=$picNameNew;//unique
			$this->accountStorageSQL->setPicRef($pic_id,$_SESSION['user']->getLogin());
		}else{//USER A UNE PIC

			$pic=$this->accountStorageSQL->readPicUser($_SESSION['user']->getLogin());
			$pic_id=$pic["img_profile"];
			//var_dump($pic);
			if($pic_id===null){ //PIC PAR DEFAUT
				$pic_id="user_profil.png";//img pad defaut
			}
		}
		//------------
		echo "PICID".$pic_id;
		echo $error;
		$this->view->makeAccountPage($quizOfUser,$userAnswers,$error,$pic_id);
	}

	public function createNewQuizSettings(){ //AFFICHE LA PAGE DE PARAMETRES DU QUIZ (nouveau)
		$this->view->makeQuizSettingsPage(new QuizBuilder());
	}

	public function createNewQuiz(array $settings){ //RECUPERE LES PARAMETRES ET AFFICHE LA PARTIE 2 (sauvernouveau)
    	$quizBuilder = new QuizBuilder($settings,null);
		$quizBuilder->isValidSettings(); //ON VERIFIE LES PARAMETRES DU QUIZ AVANT DE LE PASSER EN SESSION
        if($quizBuilder->getError()==null){
			$_SESSION['quizSettings'] = $settings;

			$this->view->makeQuizCreationPage($quizBuilder);
        }else{
           	$this->view->makeUnknownPage();
           }                    
    }

    public function saveNewQuiz(array $data){
		$fBuilder = new QuizBuilder($_SESSION['quizSettings'],$data);

		var_export($_SESSION['quizSettings']);
		var_export($data);
		$quiz=$fBuilder->createQuiz();
		$this->quizStorageSQL->createQuizz($quiz);
		$this->view->makeQuizCreationSucces();
	}

	public function showAllTheme(){
		$this->view->makeThemePage($this->quizStorageSQL->readTheme());
	}

	public function showAllQuizOfOneTheme($theme_id){
		$quizOfTheme=$this->quizStorageSQL->readQuizFromTheme($theme_id);
		$themeName=$this->quizStorageSQL->readThemeName($theme_id);
		$this->view->makeAllQuizOfOneTheme($quizOfTheme,$themeName);
	}

	public function showQuiz($quiz_id){//Affiche un quiz
		if(key_exists('user', $_SESSION)) {
			//$quiz_data=$this->themesStore->readQuizFromQuizId($quiz_id);
			//$question_data=$this->themesStore->readQuestionFromQuizId($quiz_id);
			//$answers_data=$this->themesStore->readAnswerFromQuizId($quiz_id);
			$questionAnswers_data = $this->quizStorageSQL->readQuestionAnswerFromQuizId($quiz_id);

			$this->view->makeQuizView($questionAnswers_data, $quiz_id);
		}
		else{
			$this->view->makeLoginPage();
		}
	}

	public function editQuiz($quiz_id){
		$qa=$this->quizStorageSQL->readQuestionAnswerFromQuizId($quiz_id);
		$settings=$this->quizStorageSQL->readQuizFromQuizId($quiz_id);
		$nom_theme=$this->quizStorageSQL->readThemeName($settings["theme_id"]);

		$_SESSION['editQuizSettings'] = $settings;
		$_SESSION['editQuizId'] = $quiz_id;
		$_SESSION['editQuizNomTheme'] = $nom_theme;

		$this->view->makeEditQuizPage($qa,$settings,$nom_theme);
	}

	public function saveEditQuiz($post){
		$lastSettings=$_SESSION['editQuizSettings'];
		$quiz_id=$_SESSION['editQuizId'];
		$nom_theme=$_SESSION['editQuizNomTheme'];

		if(array_key_exists("editAnswers",$post)){//If we recreate a quiz
			if($this->quizStorageSQL->deleteQuiz($quiz_id)){//If the old quiz has been deleted, we create a new one
				$quizSettings=array();
				$quizSettings["Nom"]=$post["Nom"];
				$quizSettings["Imagetheme"]="";
				$quizSettings["Categorie"]=$nom_theme;
				$quizSettings["Description"]=$post["Description"];
				$quizSettings["nbQuestions"]=$lastSettings["nb_questions"];

				$fBuilder = new QuizBuilder($quizSettings,$post);
				$quiz=$fBuilder->createQuiz();
				$this->quizStorageSQL->createQuizz($quiz);

				$this->view->makeEditSuccessPage();
			}
			else{//Else if the old quiz hasn't been deleted -> error
				$this->view->makeEditFailPage();
			}

		}
		else{//If the user just changes the name and description
			if($this->quizStorageSQL->updateNomDescriptionQuiz($quiz_id,$post["Nom"],$post["Description"])){
				$this->view->makeEditSuccessPage();
			}
			else{
				$this->view->makeEditFailPage();
			}
		}
	}

	public function saveQuizForm($quizAnswer,$quiz_id){
		date_default_timezone_set('Europe/Paris');
		$d=date("d-m-y-H-i-s");
		$user_login=$_SESSION["user"]->getLogin();

		try {
			$this->quizStorageSQL->stockUserQuizAnswer($quizAnswer,$user_login,$d,$quiz_id);
			$this->view->saveQuizFormView();
		} catch (Exception $e) {
			$this->view->makeUnknownPage();
		}
	}



    public function showConnectedSession(array $data){
    	if(key_exists('nom',$data)){//si c'est une inscription
    		//condition pour savoir que le POST vient du formulaire de l'inscription
    		//creation d'user dans la base
	    	$userBuilder = new UserBuilder($data);
	    	$userBuilder->isValid();

	    	if($userBuilder->getError()!==null){
	    		//si les forms ne sont pas remplies || condition de validité dans la fonction appeler 
	    		$this->view->makeLoginPage();
	    	}else{
	    	session_destroy();
			session_start();
	    	$this->accountStorageSQL->createUser($userBuilder->createUserRQ());
			$this->view->makeInscriptionSuccess();
	    	}

	    }else{
	    	//ici la variable $data contient le login et le password envoyer par l'user
	    	if ($this->authentificationManager->connectUser($data['login'], $data['password'])) {
	    		$this->view->displayUserConnectionSuccess();
	    	}else{
	    		$_POST['wrong']=true;
	    		$this->view->makeLoginPage();
	    	}
	    }
    }


	public function confirmeUserDeletion($idUser){
		$this->view->comfirmeDeletionUserPage($this->accountStorageSQL->readUser($idUser));
	}

	public function deleteUserByAdmin($idUser){
		if ($idUser!=null) {
			$this->accountStorageSQL->deleteUser($idUser);
			$this->view->displayUserDeletionSuccess();
		} else {
			//id non existant
		}
	}

    public function deconnectFromSession(){
    	session_destroy();
    	header("Location: index.php");
    }
    public function showUnexpectedPage(){
    	$this->view->makeUnknownPage();
    }

   public function showAproposPage(){
   		$this->view->makeAproposPage();
   }

   	//FORGOT PASSWORD-------------
	public function showValideCode($data){
		$code= bin2hex(openssl_random_pseudo_bytes(3));
		$this->accountStorageSQL->createEmailValidationCode($data["login"],$code);
		echo $code;

		$to = "".$data["login"];
		$subject = 'Changement de mot de passe';
		$message = 'Bonjour, voici le code afin de modifier votre  mot de passe : '.$code;
		$headers = 'From: 21706533@etu.unicaen.fr';

		mail($to, $subject, $message, $headers);

		$this->view->makeValideCodePage();
	}


	public function showNewPassPage($data){
		$generated_code = $this->accountStorageSQL->getResetCode($data['login']);
		if($data['code']===$generated_code["pass_confirme"]){
			$user_id=$this->quizStorageSQL->getUserIdFromLogin($data["login"]);
			if($data['newPassword1']==$data['newPassword2']){
				$this->accountStorageSQL->updatePassword($user_id,$data['newPassword1']);
				$this->view->makePasswordSuccessPage();
			}
			else{
				$this->view->makePasswordFailPage();
			}
		}else{
			$this->view->makePasswordFailPage();
		}
	}
	//END FORGOT PASSWORD-------------

}
?>
