<?php
require_once("model/Quiz.php");
require_once("control/Controller.php");
require_once("view/View.php");
session_start();
class Router {
public function main(QuizStorageMySQL $quizStorageSQL, AccountStorageMySQL $accountStorageSQL, AuthenticationManager $authentificationManager){

	$feedback=key_exists('feedback',$_SESSION)?$_SESSION['feedback']:null;
	$vue= new View($feedback);
	$control = new Controller($vue,$quizStorageSQL,$accountStorageSQL,$authentificationManager);
	
	$themeId = key_exists('id', $_GET)? $_GET['id']: null;
	$userId=key_exists('idUser', $_GET)? $_GET['idUser']: null;
	$action = key_exists('action', $_GET)? $_GET['action']: null;
	if ($action === null) {
		$action = ($themeId === null)? "accueil": "voir";
	}
	switch ($action) {
		case 'accueil':
			$control->showHome();
			break;
        case 'allQuiz':
            $control->showAllTheme();
            break;
        case 'saveQuiz':
            $control->saveQuizForm($_POST,$_GET['quiz_id']);
            break;
        case 'quiz':
            $control->showQuiz($_GET['quiz_id']);
            break;
        case 'listQuiz':
            $control->showAllQuizOfOneTheme($_GET['themeId']);
            break;
        case 'social':
            $control->showSocial($_POST,0);
            break;
        case 'socialDelete':
            $control->showSocial($_GET['friendId'],1);
            break;
        case 'socialCommonQuiz':
            $control->showSocialCommonQuiz($_GET['friendId']);
            break;
        case 'socialCompareQuiz':
            $control->showSocialCompareQuiz($_GET['quizId'],$_GET['friendId']);
            break;
		case 'voir':
		 	$control->showInformation($themeId);
		 	break;
		case 'nouveau':
		 	$control->createNewQuizSettings();
		 	break;
		case 'creerNouveau':
		 	$control->createNewQuiz($_POST);
		 	break;
        case 'saveEditQuiz':
            $control->saveEditQuiz($_POST);
            break;
        case 'editQuiz':
            $control->editQuiz($_GET['quizId']);
            break;
        case 'sauverQuiz':
            $control->saveNewQuiz($_POST);
            break;
		case 'account':
		 	$control->showAccount($_POST);
		 	break;
		case 'login':
			$vue->makeLoginPage();
		 	break;
		case 'inscp':
			$vue->makeUserInscription();
		 	break;
        case 'ModifyUser':
            $control->modifyUserStatut($_POST,$userId);
            break;
        case 'DeleteQuiz':
            $control->deleteQuiz($_GET['quizId']);
            break;
        case 'DeleteUserQuiz':
            $control->deleteUserQuiz($_GET['quizId']);
            break;
        case 'GestionCompte':
            $control->showGestionCompte();
            break;
		 case 'succInscp':
		 	$vue->displayInscriptionSucces();
		 	break;
		case 'connected':
		 	$control->showConnectedSession($_POST);
		 	break;
		case 'deconnected':
			$control->deconnectFromSession();
			break;
        case 'info':
            $control->showAproposPage();
            break;
        case 'VDeleteUser':
            $control->confirmeUserDeletion($userId);
            break;
        case 'DeleteUser':
            $control->deleteUserByAdmin($userId);
            break;
        case 'reccuPas':
            $vue->makeRecupPassPage();
            break;
        case 'valide':
            $control->showValideCode($_POST);
            break;
        case 'comfirmePass':
            //var_dump($_POST);
            $control->showNewPassPage($_POST);
            break;
			
		default:
			$control->showUnexpectedPage();
			break;
	}
	$vue->render();
}

    public function getQuizURL(){
        return"?action=quiz";
    }

    public function saveQuizFormURL(){
        return"?action=saveQuiz";
    }

    public function saveEditQuizURL(){
        return "?action=saveEditQuiz";
    }

    public function getModifyUserStatutURL(){
        return "?action=ModifyUser";
    }

    public function getAllQuizURL(){
        return"?action=allQuiz";
    }

    public function getVDeleteUserUrl(){
        return"?action=VDeleteUser";
    }

    public function getDeleteQuizURL(){
        return"?action=DeleteQuiz";
    }

    public function getDeleteUserQuizURL(){
        return"?action=DeleteUserQuiz";
    }

    public function getDeleteUserUrl(){
        return"?action=DeleteUser";
    }

    public function getlistQuizURL(){
        return"?action=listQuiz";
    }

    public function getSocial(){
        return"?action=social";
    }

    public function getSocialDeleteURL(){
        return"?action=socialDelete";
    }

    public function getGestionCompteURL(){
    return"?action=GestionCompte";
    }

    public function getSocialCommonQuizURL(){
        return"?action=socialCommonQuiz";
    }

    public function getSocialCompareQuizURL(){
        return"?action=socialCompareQuiz";
    }

	public function getPagePersoURL(){
		return"?action=account";
	}

	public function saveAccountChangeURL(){
        return"?action=account";
    }
	
	public function getThemeCreationURL(){	 
	 return "?action=nouveau";
	}

	public function getQuizCreationURL(){
	 return "?action=creerNouveau";
	}

    public function getQuizSaveURL(){
        return "?action=sauverQuiz";
    }

	public function getHomePageUrl(){
		return "?action=accueil";
	}

    public function getReccupMdpURL(){
        return "?action=reccuPas";
    }

    public function getValideCodeURL(){
        return "?action=valide";
    }
    public function getComfirmePassURL(){
        return "?action=comfirmePass";
    }

	public function getConnexionForm(){
		return "?action=login";
	}

	public function getInscriptionForm(){
		return "?action=inscp";
	}
	public function getSessionPage(){
		return "?action=connected";
	}

	public function getDeconnectUrl(){
		return "?action=deconnected";	
	}

	public function getEditQuizURL(){
    return "?action=editQuiz";
    }

    public  function POSTredirect($url, $feedback=null){
        $_SESSION['feedback']=$feedback;
        header("Location: " . $url, true, 303);
        exit();
    }

}
?>