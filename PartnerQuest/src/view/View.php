<?php
declare(strict_types=1);
class View{

	private $title;
	private $content;
	private $routId;
	private $feedback;


	function __construct($feedback){
		$this->routId= new Router();
		$this->feedback=$feedback;
	}

//FUNCTIONS FOR REDIRECTION WITH A FEEDBACK MESSSAGE   
    public function displayUserConnectionSuccess(){
        $this->routId->POSTredirect($this->routId->getHomePageUrl(),"connecté !");
    }
    public function displayUpdateStatSuccess(){
        if($_POST['statut']==='admin'){
            $this->routId->POSTredirect($this->routId->getGestionCompteURL(),"un nouveau administrateur du site est ajouté !");
        }else if($_POST['statut']==='user'){
            $this->routId->POSTredirect($this->routId->getGestionCompteURL(),"Pas de modification de Statut!");
        }
    }
    public function displayUserDeletionSuccess(){
        $this->routId->POSTredirect($this->routId->getGestionCompteURL(),"utilisateur supprimée!");
    }

    public function displayAccountPage(){
        $this->routId->POSTredirect($this->routId->getPagePersoURL(),"utilisateur supprimée!");
    }

    public function displayAdmin(){
        $this->routId->POSTredirect($this->routId->getGestionCompteURL(),"Quiz supprimé!");
    }


//PASSWORD RECOVERY---------

    //First page on which the user enters his email
    public function makeRecupPassPage(){
        $this->title = "Récuperation du Compte";
        $this->content .= '<div class="pb-5 pt-5">
            <div class="card">
                    <article class="card-body">
                        <h4 class="card-title mb-4 mt-1">Récupération du mot de passe</h4>
                        <form action="'.$this->routId->getValideCodeURL().'" method=post>
                            <div class="form-group">
                                <label>Entrer votre adresse mail</label>
                                <input name="login" class="form-control" type="email" required>
                            </div> 
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">valider</button>
                            </div>
                        </form>
                    </article>
            </div>
        </div>';
    }

    //Second page on which the user enters the code received by email and his new password
    public function makeValideCodePage(){
        $this->title = "Récuperation du Compte";
        $this->content .= '<div class="pb-5 pt-5">
             <div class="card">
                     <article class="card-body">
                         <h4 class="card-title mb-4 mt-1">Code de validation</h4>
                         <form action="'.$this->routId->getComfirmePassURL().'" method=post>
                             <div class="form-group">
                                 <label>Veuillez saisir le code reçu dans votre mail</label>
                                 <input name="code" class="form-control" type="text" required>
                             </div> 
                             <div class="form-group">
                                 <label>Veuillez saisir votre mail</label>
                                 <input name="login" class="form-control" type="text" required>
                             </div>
                             <div class="form-group">
                                 <label>Veuillez saisir votre nouveau mot de passe</label>
                                 <input name="newPassword1" class="form-control" type="text" required>
                             </div>
                             <div class="form-group">
                                 <label>Veuillez confirmer votre nouveau mot de passe</label>
                                 <input name="newPassword2" class="form-control" type="text" required>
                             </div>
                             <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">valider</button>
                            </div>
                         </form>
                    </article>
            </div>
         </div>';
    }
    //END PASSWORD RECOVERY---------

//ADMIN FUNCTIONS-------
    //DELETION OF USER BY ADMIN 
    public function comfirmeDeletionUserPage($user){
        $this->title='Suppression';
        $s="<h4>Suppresion de l'utilisateur  : </h4>";
        $s.="<p><strong>Nom    :</strong>".$user->getNom()."<p>";
        $s.="<p><strong>Prenom :</strong>".$user->getPrenom()."<p>";
        $s.="<p><strong>Id     :</strong>".$user->getLogin()."<p>";
        $s.="<p><strong>Statut :</strong>".$user->getStatut()."<p>";
        $s.="<p>voulez-vous vraiment supprimer cette utilisateur?</p>";
        $s.="<form action='".$this->routId->getDeleteUserUrl()."&idUser=".$_GET['idUser']."' method='post'>";
        $s.='<input type="submit" value="Confirmer">';
        $s.='</form>';
        $this->content=$s;
    }
    
    //FUNCTION TO DISPLAY THE ADMIN VIEW 
    public function makeGestionAdminPage($tabUsers,$listQuiz){
        $this->title="Gestion des comptes";
        $a ='<div class="row">
        <div class="col">
            <h1 class="bj"> Gestion des utilisateurs </h1>
            <ul class="list-group">';

            foreach ($tabUsers as $user){
                if($user["login"]==='admin'){
                    $modStat='user';
                }else{
                    $modeStat='admin';
                }
                $a.='<li class="list-group-item">
                <p> Nom : '.$user["nom"].' '.$user["prenom"].'</p>
                <p> Mail : '.$user["login"].'</p>
                <form  action ="'.$this->routId->getModifyUserStatutURL().'&idUser='.$user["login"].'" method="post">
                <button class="btn btn-primary btn-block" type="submit" name="statut" value="'.$modeStat.'">'.$modeStat.'</button>
                </form>
                
                <form  action ="'.$this->routId->getVDeleteUserUrl().'&idUser='.$user["login"].'" method="post">
                <button class="btn btn-primary btn-block" type="submit"  value="Comfirmer"> supprimer </button>
                </form>
                </li>';
            }

        $a.='</ul>
        
        </div>
        <div class="col">
            <h1 class="bj">Suppression des Quizs</h1>
            <ul class="list-group">';
        foreach ($listQuiz as $quiz){
            $a.='<li class="list-group-item" aria-disabled="true">
<div class="float-left">
    <p><small><b>Nom : </b>'.$quiz["nom_quiz"].'</small></p>
    <p><small><b>Theme : </b>'.$quiz["nom_theme"].'</small></p>
    <p><small><b>Nombre de questions : </b>'.$quiz["nb_questions"].'</small></p>
</div>
<a href = "'.$this->routId->getDeleteQuizURL().'&amp;quizId='.$quiz["quiz_id"].'" class="btn btn-primary float-right" >Supprimer</a >
</li>';
        }
        $a.='
            </ul>
        </div>
    </div>';
        $this->content=$a;
        $_SESSION['feedback']=null;
    }

    //DISPLAY THE PRIVATE VIEW FOR EACH USER 
	public function makeAccountPage($quizOfUser,$userAnswers,$error,$pic_id){
	$this->title="mon compte";
	$this->content='<div class="row">
        <div class="col-3">
            <form action="?action=account" method="post" enctype="multipart/form-data">
            <img src="uploads/'.$pic_id.'" class="rounded" alt="Photo Profil" style="max-width: 110px;max-height:110px;">
            <input type="file" name="file" required>
            
            <button type="submit" name="submit" class="btn btn-primary btn-block">Modifier la photo</button>
            </form>
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab"
                   aria-controls="v-pills-home" aria-selected="true">Profil</a>
                <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab"
                   aria-controls="v-pills-profile" aria-selected="false">Mes Quizs</a>
                <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab"
                   aria-controls="v-pills-messages" aria-selected="false">Vos reponses</a>';
                   if($_SESSION["user"]->getStatut()=="admin"){
                       $this->content.='<a href="'.$this->routId->getGestionCompteURL().'" class="btn btn-info bj float-right">Administration</a>';
                   }
        $this->content.='</div>
        </div>
        <div class="col-9">
        
     
            <div class="tab-content" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                
                    <form  action="'.$this->routId->saveAccountChangeURL().'" method="post" encrypt="multipart/form-data">
        <div class="form-group">
            <label for="exampleFormControlInputNom">Nom</label>
            <input type="text" class="form-control" id="exampleFormControlInputNom" placeholder="'.$_SESSION["user"]->getNom().'" name="nom">
        </div>

        <div class="form-group">
            <label for="exampleFormControlInputPrenom">Prenom</label>
            <input type="text" class="form-control" id="exampleFormControlInputPrenom" placeholder="'.$_SESSION["user"]->getPrenom().'" name="prenom">
        </div>

        <div class="form-group">
            <label for="exampleFormControlInputMail">Email</label>
            <input type="email" class="form-control" id="exampleFormControlInputMail" placeholder="'.$_SESSION["user"]->getLogin().'" name="login">
        </div>

        <div class="form-group">
            <label for="exampleFormControlInputMDP1">Nouveau mot de passe</label>
            <input type="password" class="form-control" id="exampleFormControlInputMDP1" name="newpassword">
        </div>

        <div class="form-group">
            <label for="exampleFormControlInputMDP2">Mot de passe</label>
            <input required type="password" class="form-control is-invalid" id="exampleFormControlInputMDP2" name="password">
        </div>

        <button type="submit" class="btn btn-primary">VALIDER</button>
        <small class="form-text text-muted">'.$error.'</small>
    </form>

                </div>
                <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">';

	            foreach ($quizOfUser as $quiz){
	                $this->content.='<div class="card mt-2">
                        <h5 class="card-header">Theme</h5>
                        <div class="card-body">
                            <h5 class="card-title">'.$quiz["nom_quiz"].' </h5 >
                            <p class="card-text" >'.$quiz["description"].'</p >
                            <a href = "'.$this->routId->getEditQuizURL().'&amp;quizId='.$quiz["quiz_id"].'" class="btn btn-primary" >Modifier</a >
                            <a href = "'.$this->routId->getDeleteUserQuizURL().'&amp;quizId='.$quiz["quiz_id"].'" class="btn btn-primary" >Supprimer</a >
                        </div >
                    </div >';
                }

                $this->content.='</div>';
                $this->content.='<div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">';

                $this->content.='<div id="accordion">';
                $i=0;
                $d="";
                foreach ($userAnswers as $quiz){
                    $i++;
                    $this->content.='
  <div class="card">
    <div class="card-header" id="heading'.$i.'">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse'.$i.'" aria-expanded="true" aria-controls="collapse'.$i.'">
          '.$quiz["nom_theme"].' - '.$quiz["nom_quiz"].'
        </button>
      </h5>
    </div>
    <div id="collapse'.$i.'" class="collapse" aria-labelledby="heading'.$i.'" data-parent="#accordion">
    <div class="card-body">';
                    foreach ($quiz as $key => $value){//FOR ALL QUESTION/ANSWERS
                        if(is_array($value)){
                            $d=$value["date"];
                            //var_export($value);
                            $this->content.='<p class="font-weight-bold">'.$value["question"].'</p>';
                            foreach($value["reponse"] as $q){
                                $this->content.='<p>-'.$q.'</p>';
                            }
                        }
                    }
    $this->content.='
      </div>
      <div class="card-footer text-muted">'.$d.'</div>
    </div>
  </div>';
                }


                $this->content.='</div>
        </div>
    </div>';
    }

    //HOME PAGE
	public function makeFrontPage($listTheme){
		$this->title="Accueil";
		if (key_exists('feedback',$_SESSION)){	$_SESSION['feedback']=null;	}

		$this->content='<div id="carouselExampleIndicators" class="carousel slide pt-lg-5" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="skin/img/menu1.PNG" alt="First slide">
                <div class="carousel-caption d-none d-md-block">
                    <h1 class="p-3 mb-2 bg-primary text-white bj">Trouver vos points communs</h1>
                    <h4 class="p-3 mb-2 bg-primary text-white sj ">Compare tes reponses avec celles de tes amis</h4>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="skin/img/menu2.png" alt="Second slide">
                <div class="carousel-caption d-none d-md-block">
                    <h1 class="p-3 mb-2 bg-primary text-white bj">Creer des quiz</h1>
                    <h4 class=" p-3 mb-2 bg-primary text-white sj">Repond a des quiz crees par la communaute</h4>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="skin/img/menu3.png" alt="Third slide">
                <div class="carousel-caption d-none d-md-block">
                    <h1 class="p-3 mb-2 bg-primary text-white bj">Simple et interactif</h1>
                    <h4 class="p-3 mb-2 bg-primary text-white sj">Accessible depuis n\'importe quel appareil</h4>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>';

        $this->content.='<div class="populaires mt-4 mb-5">
        <h2 class="bj mb-3">Themes</h2>

        <div class="card-deck mb-3">
            <div class="card">
                <img class="card-img-top" src="skin/img/beer.jpg" alt="Card image cap">
                <div class="card-body">
                    <a href="'.$this->routId->getlistQuizURL().'&amp;themeId=1" class="btn btn-primary bj">'.$listTheme[0].'</a>
                </div>
            </div>
            <div class="card">
                <img class="card-img-top" src="skin/img/beer.jpg" alt="Card image cap">
                <div class="card-body">
                    <a href="'.$this->routId->getlistQuizURL().'&amp;themeId=2" class="btn btn-primary bj">'.$listTheme[1].'</a>
                </div>

            </div>
            <div class="card">
                <img class="card-img-top" src="skin/img/beer.jpg" alt="Card image cap">
                <div class="card-body">
                    <a href="'.$this->routId->getlistQuizURL().'&amp;themeId=3" class="btn btn-primary bj">'.$listTheme[2].'</a>
                </div>

            </div>
        </div>
        <a class="rounded dark_cerulean text-white p-2 bj" href="'.$this->routId->getAllQuizURL().'">Voir plus</a>
    </div>';
	}

    //Page that lists the available themes
	public function makeThemePage($table_theme){ //DISPLAY ALL THEMES
        $this->title="Themes";
        $a='';
        $i=1;
        $a.='<div class="card-deck pt-4">';
        foreach($table_theme as $theme){
            $a.='<div class="card" style="width: 18rem;">
            <div class="card-body">
                <h5 class="card-title bj">'.$theme["nom_theme"].'</h5>
                <a href="'.$this->routId->getlistQuizURL().'&amp;themeId='.$theme["theme_id"].'" class="btn btn-primary">Voir les Quiz</a>
            </div>
        </div>';
            if($i%5==0){
                $a.='</div>';
                $a.='<div class="card-deck pt-4">';
            }
            else{
                if($i==count($table_theme)){
                    $a.='</div>';
                }
            }
            $i++;
        }

        $a.='</div>';
        $this->content=$a;
    }
    
    //PAGE TO DISPLAYS THE AVAILABLE QUIZZES FOR A SPECIFIC THEME
    public function makeAllQuizOfOneTheme($quizOfTheme,$themeName){

        $this->title="Quiz";

        $a='<h1>Quiz disponibles pour le theme '.mb_strtoupper($themeName).'</h1>
';
        $i=1;
        $a.='<div class="card-deck pt-4">';
        foreach($quizOfTheme as $quiz){
            $a.='<div class="card" style="width: 18rem;">
            <div class="card-body">
                <h5 class="card-title bj">'.$quiz["nom_quiz"].'</h5>
                <hr>
                <p class="card-text sj">Description :</p>
                <p class="card-text">'.$quiz["description"].'</p>
                <hr>
                <p class="card-text">Ce quiz comporte '.$quiz["nb_questions"].' question(s)</p>
                <a href="'.$this->routId->getQuizURL().'&amp;quiz_id='.$quiz["quiz_id"].'" class="btn btn-primary">Voir le Quiz</a>
            </div>
        </div>';
            if($i%5==0){
                $a.='</div>';
                $a.='<div class="card-deck pt-4">';
            }
            else{
                if($i==count($quizOfTheme)){
                    $a.='</div>';
                }
            }
            $i++;
        }

        $a.='</div>';
        $this->content=$a;

    }

    public function makeInscriptionSuccess(){
	    $this->title="Inscription";
        $this->content.='<div class="card text-center">
              <div class="card-header">
                <h5 class="card-title">Inscription réussie!</h5>
                <img src="skin/img/emojiHappy.png" alt="image" width="20%" class="align-middle" />
                <p class="card-text">Et un de plus...</p>
                <a href="'.$this->routId->getHomePageUrl().'" class="btn btn-primary bj">Accueil</a>
              </div>
            </div>';
    }

    //ANSWER TO A QUIZ
    public function makeQuizView($questionAnswers_data,$quiz_id){
        $this->title = 'Quiz';
        $this->content = '<form  action="'.$this->routId->saveQuizFormURL().'&amp;quiz_id='.$quiz_id.'" method="post" encrypt="multipart/form-data">';
        $i=0;
        foreach($questionAnswers_data as $value){
            if($value["qcm"]=="off"){
                $this->content.='<div class="form-group">
            <label class="bj" for="exampleFormControlSelect'.$i.'">'.$value["question"].'</label>
            <select class="custom-select" size="3" name="'.$value["question_id"].'" id="exampleFormControlSelect'.$i.'">';

                foreach($value["reponse"] as $r){
                    $this->content .='<option>'.$r["reponse"].'</option>';
                }
                $this->content .='</select>
        </div>';
            }
            else{
                $this->content.='<div class="form-group">
            <label class="bj" for="exampleFormControlSelect'.$i.'">'.$value["question"].' (Choix multiples)</label>
            <select multiple class="form-control" name="'.$value["question_id"].'[]" id="exampleFormControlSelect'.$i.'" size="3">';

                foreach($value["reponse"] as $r){
                    $this->content .='<option>'.$r["reponse"].'</option>';
                }
                $this->content .='</select>
        </div>';
            }
        $i++;
        }
        $this->content .='<input class="btn btn-primary bj" type="submit" value="Enregistrer">';
        $this->content .= "</form>";
    }


    // VALIDATION OF SAVING ANSWERS
    public function saveQuizFormView(){
        $this->title="Reponses enregistrées";
        $this->content.='<div class="card text-center">
              <div class="card-header">
                <h5 class="card-title">Félicitation!</h5>
                <img src="skin/img/emojiHappy.png" alt="image" width="20%" class="align-middle" />
                <p class="card-text">Reponses enregistrées</p>
                <a href="'.$this->routId->getHomePageUrl().'" class="btn btn-primary bj">Accueil</a>
              </div>
            </div>';
    }


    //About page
	public function makeAproposPage(){
		$this->title="A propos";
		$this->content='<div class="row">
  <div class="col-4">
    <div class="list-group" id="list-tab" role="tablist">
      <a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home">Etudiants</a>
      <a class="list-group-item list-group-item-action" id="list-profile-list" data-toggle="list" href="#list-profile" role="tab" aria-controls="profile">Compte administrateur</a>
      <a class="list-group-item list-group-item-action" id="list-messages-list" data-toggle="list" href="#list-messages" role="tab" aria-controls="messages">Compte utilisateur</a>
    </div>
  </div>
  <div class="col-8">
    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
        <p> Alexis Leloup 21706533 </p>
        <p> Ilias El Hadri 21911226 </p>
        <p> Antoine Cornilleau 21703011</p>
        <p> Lucas Lecomte 21600370</p>
      </div>
      <div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
        <p>Mail : admin@admin.fr</p>
        <p>Mot de passe : admin</p>
      </div>
      <div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list">
        <hr>
        <p>Mail : user@user.fr</p>
        <p>Mot de passe : user</p>
      </div>
    </div>
  </div>
</div>';

	}

	//List of options available in the navigation bar
	public function MenuBare() {
		return array(
            "Social"=>$this->routId->getSocial(),
			"Ajouter un quiz" => $this->routId->getThemeCreationURL(),
            "Page Perso" => $this->routId->getPagePersoURL(),
			"Connexion"=>$this->routId->getConnexionForm(),
			"Deconnexion"=>$this->routId->getDeconnectUrl(),
			"Inscription"=>$this->routId->getInscriptionForm()
		);

	}


	//Create quiz settings
	public function makeQuizSettingsPage(QuizBuilder $fBuilder){
		if(key_exists('user', $_SESSION)){
			$data=$fBuilder->getData();
            //NOM DU THEME
			$s='<form action="'.$this->routId->getQuizCreationURL().'" method="post" encrypt="multipart/form-data">
                    <div class="form-group">
		   	            <label class="bj" for="exampleFormControlInput1">Nom du Quiz</label>';
		    $value=isset($data['Nom'])? $data['Nom']: null;
		    $s.='<input type="text" class="form-control" id="exampleFormControlInput1" name="Nom" value="'.$value .'"required>';
		    $s.='</div>';
            //FIN NOM DU THEME

            //ENVOI FICHIER IMAGE
            $value=isset($data['Imagetheme'])? $data['Imagetheme']: null;
            $s.='<div class="form-group custom-file">
                    <br>
                    <input type="file" class="custom-file-input" id="validatedCustomFile" name="Imagetheme">
                    <label class="custom-file-label" for="validatedCustomFile">Choisir une image</label>
                </div>';
            //FIN ENVOI FICHIER IMAGE

            //THEME
            $value=isset($data['Categorie'])?$data['Categorie']:null;
            $s.='<div class="form-group">
            <label class="bj"  for="exampleFormControlSelect1">Theme</label>
            <select class="custom-select" size="3" id="exampleFormControlSelect1" name="Categorie" required>
                <option>Actualite</option>
                <option>Animaux</option>
                <option>Art</option>
                <option>Auto/Motos</option>
                <option>Bandes dessinees</option>
                <option>Celebrites</option>
                <option>Cinema</option>
                <option>Cuisine</option>
                <option>Culture generale</option>
                <option>Dessins animes</option>
                <option>Enfants</option>
                <option>Geographie</option>
                <option>High Tech</option>
                <option>Histoire</option>
                <option>Humour</option>
                <option>Jeux video</option>
                <option>Langues</option>
                <option>Litterature</option>
                <option>Poesie</option>
                <option>Loisirs</option>
                <option>Manga</option>
                <option>Musique</option>
                <option>Mythologie</option>
                <option>Nature</option>
                <option>Philosophie</option>
                <option>Politique</option>
                <option>Sante</option>
                <option>Sciences</option>
                <option>WEB</option>
                <option>Societe</option>
                <option>Sport</option>
                <option>Television</option>
                <option>Personnalite</option>
                <option>Voyages</option>
            </select>
        </div>';
            //FIN THEME

            //DESCRIPTION
            $value=isset($data['Description'])?$data['Description']:null;
            $s.='<div class="form-group">
            <label class="bj"  for="exampleFormControlTextarea1">Description</label>
            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="Description" required></textarea>
            </div>';
            //FIN DESCRIPTION

            //NOMBRE QUESTIONS
		    $value=isset($data['nbQuestions'])?$data['nbQuestions']:null;

		    $s.='<div class="form-group">
            <label class="bj"  for="exampleFormControlSelect2">Nombre de questions</label>
            <select class="custom-select" size="3" id="exampleFormControlSelect2" name="nbQuestions" required>
                <option>1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
                <option>5</option>
                <option>6</option>
                <option>7</option>
                <option>8</option>
                <option>9</option>
                <option>10</option>
            </select>
           </div>';

		    $s.='<input class="btn btn-primary bj" type="submit" value="Enregistrer">';
		    $s.=' </form>';

			if($data!=null){
				//pour save
				$fBuilder->isValid();
				$error=$fBuilder->getError();
				$this->content='<h3>ajouter une nouvelle fleur</h3>';
		    	$this->title='ajouter une fleur';
		    	$this->content.=$s;
		    	if($error!=null){
			    	$this->title ='error';
			    	$this->content.="<span>".$error."</span>";
			    	}
		    }else{
		    	//pour la page d'ajout
		    	$this->content='<h3 class="bj"><u>Ajouter un nouveau Quiz</u></h3>';
		    	$this->title='ajouter Quiz';
		    	$this->content.=$s;
		    }
	    }else{
	    	//visiteur non connecter
	    	$this->makeLoginPage();
	    }
	}

	//Create the quiz(question/answers)
    public function makeQuizCreationPage(QuizBuilder $fBuilder){
        if(key_exists('user', $_SESSION)){
            $data=$fBuilder->getData();
            //NOM DU THEME

            $s='<form action="'.$this->routId->getQuizSaveURL().'" method="post" encrypt="multipart/form-data">';
            for ($i = 1; $i <= $fBuilder->getNbQuestions(); $i++) {
                $value=isset($data['Question'.$i])? $data['Question'.$i]: null;
                $s.='<div class="form-group">
		   	            <label class="bj" for="exampleFormControlInput1">Question '.$i.'</label>';
                $s.='<input type="text" class="form-control" id="exampleFormControlInput1" name="Question'.$i.'" value="'.$value.'"required>';
                $s.='</div>';

                $value=isset($data['Reponse'.$i])? $data['Reponse'.$i]: null;
                $s.='<div class="form-group">
                <label class="bj"  for="exampleFormControlTextarea'.$i.'">Reponse(s) ( chaque reponse doit commencer par un "-", 5 maximum )</label>
                <textarea class="form-control" id="exampleFormControlTextarea'.$i.'" name="Reponse'.$i.'" required></textarea>
                </div>';

                $value=isset($data['QCM_Q'.$i])? $data['QCM_Q'.$i]: null;
                $s.='<div class="form-group form-check">
                <input type="checkbox" name="QCM_Q'.$i.'" class="form-check-input" id="exampleCheck'.$i.'">
                <label class="form-check-label" for="exampleCheck'.$i.'">Question '.$i.' a choix multiples</label>
                </div>
                <hr><hr>';

            }


            //FIN NOM DU THEME

            $s.='<input class="btn btn-primary bj" type="submit" value="Enregistrer">';
            $s.=' </form>';


            if($data!=null){
                //FOR SAVE
                $fBuilder->isValid();
                $error=$fBuilder->getError();
                $this->content='<h3>Ajouter un Quiz</h3>';
                $this->title='Ajouter un Quiz';
                $this->content.=$s;
                if($error!=null){
                    $this->title ='error';
                    $this->content.="<span>".$error."</span>";
                }
            }else{
                //FOR ADD QUIZ PAGE
                $this->content='<h3 class="bj"><u>Ajouter un nouveau Quiz</u></h3>';
                $this->title='Ajouter un Quiz';
                $this->content.=$s;
            }
        }else{
            //FOR A NON LOGGED USER
            $this->makeLoginPage();
        }
    }

    //DISPLAY SAVED QUIZ SUCCED FEEDBACK
    public function makeQuizCreationSucces(){
        $this->title="Succes";
        $s ='<div>';
        $s.='<p>Votre Quiz a bien ete creer!!!</p>';
        $s.='</div>';
        $this->content=$s;
    }

    //DISPLAY PAGE TO LOGIN 
    public function makeLoginPage(){
		$s="";
		if (key_exists('wrong',$_POST)) {
			$s.='<script>alert("les informations renseignées ne sont pas valide")</script>';
		}
	   	$this->title="Connectez-vous";

        $s.='<div class="pb-5 pt-5">
    <div class="card">
        <article class="card-body">
            <a href="'.$this->routId->getInscriptionForm().'" class="float-right btn btn-outline-primary">Inscription</a>
            <h4 class="card-title mb-4 mt-1">Connexion</h4>
            <form action="'.$this->routId->getSessionPage().'" method=post>
                <div class="form-group">
                    <label>Adresse Mail</label>
                    <input name="login" class="form-control" type="email" required>
                </div> <!-- form-group// -->
                <div class="form-group">
                    <a class="float-right" href="'.$this->routId->getReccupMdpURL().'">Mot de passe oublie?</a>
                    <label>Mot de passe</label>
                    <input class="form-control" name="password" type="password" required>
                </div> <!-- form-group// -->
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Connexion</button>
                </div> <!-- form-group// -->
            </form>
        </article>
    </div>
</div>';

		$this->content=$s;


	}

    //DISPLAY TO EDIT A QUIZ
	public function makeEditQuizPage($qa,$settings,$nom_theme){
	    $this->title="Edition du Quiz";
	    $this->content='<form action="'.$this->routId->saveEditQuizURL().'" method="post" encrypt="multipart/form-data">';

        $this->content.='<div class="form-group">
  <label class="bj" for="exampleFormControlInput1">Nom du Quiz</label>
  <input type="text" class="form-control" id="exampleFormControlInput1" name="Nom" value="'.$settings["nom_quiz"].'"required>
</div>';

        $this->content.='<div class="form-group">
  <label class="bj"  for="exampleFormControlTextarea1">Description</label>
  <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="Description" required>'.$settings["description"].'</textarea>
</div>';


        $i=1;
        foreach ($qa as $value){
            $string_answers="";
            foreach ($value["reponse"] as $r){
                $string_answers.="-".$r["reponse"]."\n";
            }

            $this->content.='<div class="form-group">
<label class="bj" for="exampleFormControlInputq'.$i.'">Question '.$i.'</label>
<input type="text" class="form-control" id="exampleFormControlInputq'.$i.'" name="Question'.$i.'" value="'.$value["question"].'"required>
</div>';
            $this->content.='<div class="form-group">
  <label class="bj"  for="exampleFormControlTextarear'.$i.'">Reponse(s) ( chaque reponse doit commencer par un "-", 5 maximum )</label>
  <textarea class="form-control" id="exampleFormControlTextarear'.$i.'" name="Reponse'.$i.'" required>'.$string_answers.'</textarea>
</div>';
            if($value["qcm"]=="on"){
                $check="checked";
            }
            else{
                $check="";
            }
            $this->content.='<div class="form-group form-check">
        <input type="checkbox" name="QCM_Q'.$i.'" class="form-check-input" id="exampleCheck'.$i.'" '.$check.'>
        <label class="form-check-label" for="exampleCheck'.$i.'">Question a choix multiples</label>
    </div>';

            $i++;
        }
        $this->content.='<div class="form-group form-check">
        <input type="checkbox" name="editAnswers" class="form-check-input" id="exampleCheckFinal">
        <label class="form-check-label" for="exampleCheckFinal">Modifier les questions/reponses? <b>(ATTENTION : les reponses des utilisateurs seront supprimées)</b></label>
    </div>';
        $this->content.='<input class="btn btn-primary bj" type="submit" value="Enregistrer">';


	    $this->content.='</form>';

    }

    //DIPLAY PAGE FOR SUBSCRIBE
	public function makeUserInscription(){
		$this->title="Page d'inscription";
		$s='<div class="pb-5 pt-5">
    <div class="card">
        <article class="card-body">
            <a href="'.$this->routId->getConnexionForm().'" class="float-right btn btn-outline-primary">Connexion</a>
            <h4 class="card-title mb-4 mt-1">Inscription</h4>


            <form action="'.$this->routId->getSessionPage().'" method=post>
                <div class="form-group">
                    <label>Nom</label>
                    <input class="form-control" type="text" name ="nom" required>
                </div> <!-- form-group// -->

                <div class="form-group">
                    <label>Prenom</label>
                    <input class="form-control" type="text" name ="prenom" required>
                </div> <!-- form-group// -->

                <div class="form-group">
                    <label>Adresse Mail</label>
                    <input class="form-control" type="email" name="login" required>
                </div> <!-- form-group// -->

                <div class="form-group">
                    <label>Mot de passe</label>
                    <input class="form-control" type="password" name="password" required>
                </div> <!-- form-group// -->

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Inscription</button>
                </div> <!-- form-group// -->
            </form>
        </article>
    </div> <!-- card.// -->
</div>';
		$this->content=$s;
		$_SESSION['feedback']=null;
	}


	function makeUnknownPage(){
	    $this->title='Error';
        $this->content.='<div class="card text-center">
              <div class="card-header">
                <h5 class="card-title">Erreur</h5>
                <img src="skin/img/emojiHappy.png" alt="image" width="20%" class="align-middle" />
                <p class="card-text">Une erreur est survenue</p>
                <a href="'.$this->routId->getHomePageUrl().'" class="btn btn-primary bj">Accueil</a>
              </div>
            </div>';
    }
    //DISPLAY PAGE TO FIND A PRATNER
	function showSocialPage($friend_tchecker,$friendsList){
	    $this->title='Social';
	    $this->content='<form class="form-inline" action="'.$this->routId->getSocial().'" method="post" encrypt="multipart/form-data">
  <div class="form-group">
    <label for="FriendInput" class="bj pr-2">Ajouter un ami (saisir mail)</label>
    <input required type="text" class="form-control" id="FriendInput" name="friend_login">
    </div>
  <button type="submit" class="btn btn-primary">Valider</button>
</form>';

        if($friend_tchecker==1){//ADDED FRIEND FEEDBACK
            $this->content.='<small class="text-muted">Amis ajouté!</small>';
        }
        elseif ($friend_tchecker==-1){//FRIEND NOT FOUND FEEDBACK
            $this->content.="<small class='text-muted'>Erreur, le mail ou l'utilisateur n'existe pas ou vous etes deja amis avec.</small>";
        }
        elseif ($friend_tchecker==2){//DELETED FRIEND FEEDBACK
            $this->content.="<small class='text-muted'>Amis supprimer!</small>";
        }
        elseif ($friend_tchecker==3){//FAILURE DELETION OF FRIEND FEEDBACK
            $this->content.="<small class='text-muted'>Echec de suppression</small>";
        }

        $this->content.='<hr class="pt-2 pb-2">';
	    $this->content.='
<h5 class="bj">Vos amis :</h5>
<ul class="list-group">';
        //DISPLAY FRIENDS 
	    foreach($friendsList as $f){
            //var_export($friendsList);
            $img=($f["img_profile"]==!"")?$f["img_profile"]:"user_profil.png";
	        $this->content.='<li class="list-group-item">
    <img src="uploads/'.$img.'" alt="image" style="max-width:90px;max-height:90;" class="float-left"/>
    <p class="float-left font-weight-bold">'.$f["prenom"].' '.$f["nom"].' : '.$f["login"].'</p>
    <a href="'.$this->routId->getSocialDeleteURL().'&amp;friendId='.$f["user_id"].'" class="btn btn-primary bj float-right ml-2">Supprimer cet ami</a> 
    <a href="'.$this->routId->getSocialCommonQuizURL().'&amp;friendId='.$f["user_id"].'" class="btn btn-primary bj float-right">Comparer vos reponses</a>
  </li>';
        }

        $this->content.='</ul>';
    }
    //DISPLAY COMMON QUIZ BETWEEN USER
    function makeSocialCommonQuiz($req,$friend_id){
        $this->title="Quizs Communs";

        if($req==false){
            $this->content.='<div class="card text-center">
              <div class="card-header">
                <h5 class="card-title">Vous n\'avez aucun quiz en commun...</h5>
                <img src="skin/img/emojiSad.png" alt="image" width="20%" class="align-middle" />
                <p class="card-text">Attendez que vos amis participent a des quiz</p>
                <a href="javascript:history.go(-1)" class="btn btn-primary bj">Retour</a>
              </div>
            </div>';
        }
        else {
                $this->content = '<div class="my-3 pt-5 pl-5 bg-white rounded shadow-sm">
            <h1 class="border-bottom border-gray">Quizs Communs</h1>';
            foreach ($req as $value){
                $this->content.='<div class="media text-muted pt-3">
                <img class="bd-placeholder-img mr-2 rounded" width="32" height="32" src="skin/img/Addfriends.png" preserveAspectRatio="xMidYMid slice" focusable="false" role="img"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em"></text></img>
               
                <h3 class="d-block text-gray-dark border-bottom">'.$value["nom_quiz"].'</h3>
                <p><b>Description : </b>'.$value["description"].'</p>
                <p><b>Nombre de questions : </b>'.$value["nb_questions"].'</p>
                <a href = "'.$this->routId->getSocialCompareQuizURL().'&amp;quizId='.$value["quiz_id"].'&amp;friendId='.$friend_id.'" class="btn btn-primary bj" >Tester votre affinite!</a >
                </p>
            </div>';
            }


        $this->content.='</div>';
        }
    }

    //DISPLAY EDIT SUCCES FEEDBACK
    function makeEditSuccessPage(){
	    $this->title="Modification réussie!";
        $this->content.='<div class="card text-center">
              <div class="card-header">
                <h5 class="card-title">Félicitation!</h5>
                <img src="skin/img/emojiHappy.png" alt="image" width="20%" class="align-middle" />
                <p class="card-text">Modification réussie</p>
                <a href="'.$this->routId->getPagePersoURL().'" class="btn btn-primary bj">Retour</a>
              </div>
            </div>';
    }
    //DISPLAY EDIT PASSWORD FAIL FEEDBACK
    function makePasswordFailPage(){
        $this->title="Modification échouée!";
        $this->content.='<div class="card text-center">
              <div class="card-header">
                <h5 class="card-title">Oups...</h5>
                <img src="skin/img/emojiSad.png" alt="image" width="20%" class="align-middle" />
                <p class="card-text">La modification a échouée</p>
                <a href="'.$this->routId->getConnexionForm().'" class="btn btn-primary bj">Retour</a>
              </div>
            </div>';
    }
    //DISPLAY EDIT PASSWORD SUCCES FEEDBACK
    function makePasswordSuccessPage(){
        $this->title="Modification réussie!";
        $this->content.='<div class="card text-center">
              <div class="card-header">
                <h5 class="card-title">Félicitation!</h5>
                <img src="skin/img/emojiHappy.png" alt="image" width="20%" class="align-middle" />
                <p class="card-text">Modification réussie</p>
                <a href="'.$this->routId->getConnexionForm().'" class="btn btn-primary bj">Se connecter</a>
              </div>
            </div>';
    }
    //DISPLAY EDIT PASSWORD FAIL FEEDBACK
    function makeEditFailPage(){
        $this->title="Modification échouée!";
        $this->content.='<div class="card text-center">
              <div class="card-header">
                <h5 class="card-title">Oups...</h5>
                <img src="skin/img/emojiSad.png" alt="image" width="20%" class="align-middle" />
                <p class="card-text">La modification a échouée</p>
                <a href="'.$this->routId->getReccupMdpURL().'" class="btn btn-primary bj">Essayer de nouveau</a>
              </div>
            </div>';
    }

    //DISPLAY RESPONES OF THE USER AND HIS FRIEND
    function makeSocialCompareQuiz($user_array,$friend_array,$quiz_details,$commonAnswers,$affinity){
	    $this->title.="Affinite";

        $this->content='<div class="card text-center bj mb-2 mt-4">
<div class="card-header bj bg-primary text-white">'.$quiz_details["nom_quiz"].'</div>
<div class="card-body"><p class="card-text">'.$quiz_details["description"].'</p></div>
</div>';

	    $this->content.='<div class="row">';

	    $this->content.='<div class="col-sm-6">
            <div class="card">
                <h5 class="card-header bj">Vos reponses</h5>
                <ul class="list-group list-group-flush">';
	    foreach ($user_array as $value){
	        $this->content.='<li class="list-group-item">
                        <b>'.$value["question"].'</b>
                        <div>';
            foreach ($value["reponse"] as $answer){
                if(in_array($answer,$commonAnswers[$value["question_id"]])){
                    $this->content.='<img style=\'float:left;width:4%; margin-right:10px;\' src="skin/img/tick.png" />';
                    $this->content.='<p class="text-success">'.$answer.'</p>';
                }
                else{
                    $this->content.='<img style=\'float:left;width:4%; margin-right:10px;\' src="skin/img/cross.png" />';
                    $this->content.='<p class="text-danger">'.$answer.'</p>';
                }

            }
            $this->content.='</div>
</li>';
	    }
        $this->content.='</ul>
                    </div>
                </div>';


        $this->content.='<div class="col-sm-6">
            <div class="card">
                <h5 class="card-header bj">Reponses de votre ami</h5>
                <ul class="list-group list-group-flush">';
        foreach ($friend_array as $value){
            $this->content.='<li class="list-group-item">
                        <b>'.$value["question"].'</b>
                        <div>';
            foreach ($value["reponse"] as $answer){
                if(in_array($answer,$commonAnswers[$value["question_id"]])){
                    $this->content.='<img style=\'float:left;width:4%; margin-right:10px;\' src="skin/img/tick.png" />';
                    $this->content.='<p class="text-success">'.$answer.'</p>';
                }
                else{
                    $this->content.='<img style=\'float:left;width:4%; margin-right:10px;\' src="skin/img/cross.png" />';
                    $this->content.='<p class="text-danger">'.$answer.'</p>';
                }
            }
            $this->content.='</div>
</li>';
        }
        $this->content.='</ul>
                    </div>
                </div>';

	    $this->content.='</div>';

	    $this->content.='<div class="card mt-2 text-center mb-5">
  <div class="card-header bg-info text-white">
    <h5 class="bj">Affinite</h5>
  </div>
  <div class="card-body">';
        if($affinity==0){
            $this->content.='<p class="card-text">Etes-vous sur de le connaitre ? Je ne pense pas…</p>';
        }
	    elseif($affinity<=25){
            $this->content.='<p class="card-text">Etes-vous vraiment amis ?</p>';
        }
        elseif($affinity<=50){
            $this->content.='<p class="card-text">Vous êtes plutôt différents...</p>';
        }
        elseif($affinity<=99){
            $this->content.='<p class="card-text">Vous avez un bon feeling !</p>';
        }
        elseif($affinity==100){
            $this->content.='<p class="card-text">INCROYABLE !!!!!! Vous êtes les Meilleurs amis du monde</p>';
        }
    $this->content.='<div class="progress" style="height: 20px;">
  <div class="progress-bar progress-bar-striped" role="progressbar" style="width: '.$affinity.'%;" aria-valuenow="'.$affinity.'" aria-valuemin="0" aria-valuemax="100">'.$affinity.'%</div> 
  </div>
  <a href="javascript:history.go(-1)" class="btn btn-primary mt-3">Comparer un autre quiz</a>
</div>';

    }
    //REDIRECTION FUNCTION
	function displayInscriptionSuccess(){
		$this->routId->POSTredirect($this->routId->getConnexionForm(),"inscription réussie!, connecté vous"); 
	}


	public function render(){
		if ($this->title === null || $this->content === null) {
			$this->makeUnknownPage();
		}

?>
        <!DOCTYPE HTML>
        <html>
        <head>
            <title></title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <meta name="description" content=""/>
            <meta name="keywords" content=""/>
            <meta name="author" content=""/>

            <!-- Quiz style  -->
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
                  integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
            <link rel="stylesheet" href="skin/css/style2.css">

            <!-- JS style  -->
            <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
                    integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
                    crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
                    integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
                    crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
                    integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
                    crossorigin="anonymous"></script>
        </head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark dark_cerulean static-top mb-3">
        <div class="container">
            <a class="navbar-brand" href="?action=accueil">
                <img src="skin/img/icon.png" width="40" height="40" class="d-inline-block align-top" alt="">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
                    aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
<?php

$menu=$this->MenuBare();

if (key_exists('user',$_SESSION)){
	$menu['Connexion']=null;
	$menu['Inscription']=null;


}else{
    $menu['Ajouter un quiz']=null;
    $menu['Social']=null;
	$menu['Deconnexion']=null;
	$menu['Page Perso']=null;
}
foreach ($menu as $text => $link) {
	if($link !==null){
	echo "<li class='nav-item active'>";
	echo    "<a class='nav-link' href=".$link.">".$text."<span class='sr-only'>(current)</span></a>";
	echo "</li>";
	}
}
?>
			</ul>
		</div>
        </div>
	</nav>
	<main class="container">
<?php
echo $this->content;
?>
	</main>
    <footer class="page-footer dark_cerulean">
        <div class="text-white footer-copyright text-center py-3 sj">Projet PartnerQuest |
            <a class="text-white bj" href="?action=info"> A Propos & Credits</a>
        </div>
    </footer>
</body>
</html>
<?php
	}
}
?>