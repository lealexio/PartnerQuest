<?php
include_once 'Quiz.php';



class QuizStorageMySQL{
    
    public $db;

    public function __construct(PDO $db){
		
		$this->db=$db;
    }

    public function getUserIdFromLogin($login){
        $stmt = $this->db->query("SELECT user_id FROM users WHERE login='".$login."'");
        $tab=$stmt->fetch();
        return $tab["user_id"];
    }

    public function getCommonQuiz($user_id,$friend_id){
        try {


            $stmt = $this->db->query("SELECT DISTINCT quiz_id FROM `user_reponse` WHERE user_id='".$user_id."' AND quiz_id IN(SELECT quiz_id FROM `user_reponse` WHERE user_id='".$friend_id."')");
            $tabQID=$stmt->fetchAll();

            $tmp="";
            $i=0;
            foreach ($tabQID as $quiz_id){
                $i++;
                $tmp.="quiz_id='";
                $tmp.=$quiz_id["quiz_id"];
                $tmp.="'";
                if($i < sizeof($tabQID)){
                    $tmp.=" OR ";
                }
            }

            $stmt = $this->db->query("SELECT * FROM `quiz` WHERE ".$tmp);
            $tabCommonQuiz=$stmt->fetchAll();

            return $tabCommonQuiz;

        } catch (Exception $e) {
            return false;
        }
    }

    public function read($id){
			$stmt = $this->db->query("SELECT * FROM theme where id='".$id."'");            
            $tab=$stmt->fetch();
            $name = $tab['nom'];
            $nbQuestion = $tab['nb_quest'];
            $idUser=$tab['id_user'];
            $theme=new Quiz($name,$nbQuestion,$idUser);
        return $theme;
    }

    public function getFriendsFromLogin($userLogin){
        echo $userLogin;
        $stmt = $this->db->query("SELECT users.nom,users.prenom,users.login,users.user_id,users.img_profile FROM users INNER JOIN friends ON users.user_id=friends.friend_id WHERE friends.user_id='".$userLogin."'");
        return $stmt->fetchAll();
    }

    public function deleteFriend($post,$userId){
        echo $post;
        echo $userId;
        try {
            $this->db->query("DELETE FROM friends WHERE user_id=".$userId." AND friend_id=".$post);
            echo"OK";
            return true;
        } catch (Exception $e) {
            return false;
        }

    }

    public function addFriend($post){
        $stmt = $this->db->query("SELECT user_id FROM users WHERE login='".$post["friend_login"]."'");
        $tmp=$stmt->fetch();
        if($tmp==false){
            return false;
        }
        else{
            $userId=$this->getUserIdFromLogin($_SESSION["user"]->getLogin());

            $stmt = $this->db->query("SELECT * FROM friends WHERE user_id='".$userId."' AND friend_id='".$tmp["user_id"]."'");
            $tmp3=$stmt->fetch();

            if($tmp3==false){
                $rq = "INSERT INTO friends (user_id,friend_id)VALUES(?,?)";
                $stmt = $this->db->prepare($rq);
                $stmt->execute(array($userId, $tmp["user_id"]));
                return true;
            }
            else{
                return false;
            }
        }
    }

    public function getCompareQuiz($quiz_id,$userId,$friend_id){//RETURN ANSWERS OF USER AND FRIEND
        $stmt = $this->db->query("SELECT * FROM user_reponse INNER JOIN reponse INNER JOIN question ON user_reponse.reponse_id = reponse.reponse_id AND user_reponse.question_id=question.question_id WHERE (user_reponse.quiz_id='".$quiz_id."' AND user_reponse.user_id='".$userId."') OR (user_reponse.quiz_id='".$quiz_id."' AND user_reponse.user_id='".$friend_id."')");
        $req=$stmt->fetchAll();
        //var_export($req);

        $stackOfQuiz=array();
        foreach ($req as $value){
            if(!array_key_exists($value["question_id"], $stackOfQuiz)){
                $tmp3=array();
                $tmp3["question_id"]=$value["question_id"];
                $tmp3["reponse"]=array();
                $tmp3["question"]=$value["question"];
                $stackOfQuiz[$value["question_id"]]=$tmp3;
            }
        }

        $friend_array=$stackOfQuiz;
        $user_array=$stackOfQuiz;


        foreach ($req as $value){
            if($value["user_id"]==$userId){
                $answertmp=$user_array[$value["question_id"]]["reponse"];
                array_push($answertmp,$value["reponse"]);
                $user_array[$value["question_id"]]["reponse"]=$answertmp;
            }
            else{
                $answertmp=$friend_array[$value["question_id"]]["reponse"];
                array_push($answertmp,$value["reponse"]);
                $friend_array[$value["question_id"]]["reponse"]=$answertmp;
            }
        }

        //var_export($user_array);
        //var_export($friend_array);

        $stmt = $this->db->query("SELECT nom_quiz, description, nb_questions FROM quiz WHERE quiz_id='".$quiz_id."'");
        $quiz_details=$stmt->fetch();

        return array($user_array,$friend_array,$quiz_details);

    }



    public function readAllAnswersOfUser($userId){
        $stmt = $this->db->query("SELECT * FROM user_reponse INNER JOIN reponse INNER JOIN question ON user_reponse.reponse_id = reponse.reponse_id AND user_reponse.question_id=question.question_id WHERE user_id='".$userId."'");
        $tmp=$stmt->fetchAll();


        //ARRAY CLEANING
        $stackOfAnswer=array();
        foreach ($tmp as $value){
            $tmp2=array();
            $tmp2["question_id"]=$value["question_id"];
            $tmp2["reponse_id"]=$value["reponse_id"];
            $tmp2["date"]=$value["date"];
            $tmp2["reponse"]=$value["reponse"];
            $tmp2["question"]=$value["question"];
            $tmp2["quiz_id"]=$value["quiz_id"];
            $stackOfAnswer[$value["reponse_id"]]=$tmp2;
        }
        //ARRAY CLEANING END---------


        //ARRAY CONTAINS QUESTIONS
        $stackOfQuiz=array();
        foreach ($stackOfAnswer as $value){
            if(!array_key_exists($value["question_id"], $stackOfQuiz)){
                $tmp3=array();
                $tmp3["question_id"]=$value["question_id"];
                $tmp3["reponse"]=array();
                $tmp3["date"]=$value["date"];
                $tmp3["question"]=$value["question"];
                $tmp3["quiz_id"]=$value["quiz_id"];
                $stackOfQuiz[$value["question_id"]]=$tmp3;
            }

        }

        $final=array();
        foreach ($stackOfAnswer as $value){
            array_push($stackOfQuiz[$value["question_id"]]["reponse"], $value["reponse"]);
            if(!array_key_exists($value["quiz_id"], $final)){
                $final[$value["quiz_id"]]=array();
            }
        }

        foreach($stackOfQuiz as $value){
            array_push($final[$value["quiz_id"]],$value);
        }

        foreach($final as $key => $value) {
            //echo "------------";
            $stmt = $this->db->query("SELECT * FROM quiz WHERE quiz_id='".$key."'");
            $tmp=$stmt->fetch();
            //var_export($tmp);
            $final[$key]["description"]=$tmp["description"];
            $final[$key]["nom_quiz"]=$tmp["nom_quiz"];

            $stmt = $this->db->query("SELECT nom_theme FROM theme WHERE theme_id='".$tmp["theme_id"]."'");
            $tmp=$stmt->fetch();
            $final[$key]["nom_theme"]=$tmp["nom_theme"];
        }

        //var_export($final);
        return $final;

    }

    //RETOURNE LE NOM DE TOUT LES THEMES
    public function readAllTheme(){
        $stmt = $this->db->query("SELECT * FROM theme");
        $xxx=$stmt->fetchAll();
        $ret=array();
        foreach($xxx as $ar){
            array_push($ret,$ar["nom_theme"]);
        }
        return $ret;
    }

    //RETOURNE LA TABLE THEME
    public function readTheme(){
        $stmt = $this->db->query("SELECT * FROM theme");
        return $stmt->fetchAll();
    }

    //RETOURNE LES QUIZ D'UN USER
    public function readQuizOfUser($id){
        $stmt = $this->db->query("SELECT * FROM quiz WHERE user_id='".$id."'");
        return $stmt->fetchAll();
    }

    //RETOURNE LES QUIZS D'UN THEME
    public function readQuizFromTheme($theme_id){
        $stmt = $this->db->query("SELECT * FROM quiz WHERE theme_id='".$theme_id."'");
        return $stmt->fetchAll();
    }


    //RETOURNE LE NOM D'UN THEME DEPUIS ID
    public function readThemeName($theme_id){
        $stmt = $this->db->query("SELECT nom_theme FROM theme WHERE theme_id='".$theme_id."'");
        $tampon=$stmt->fetch();
        return $tampon['nom_theme'];
    }

    //RETOURNE UN QUIZ DEPUIS L'ID D'UN QUIZ
    public function readQuizFromQuizId($quiz_id){
        $stmt = $this->db->query("SELECT * FROM quiz WHERE quiz_id='".$quiz_id."'");
        $tampon=$stmt->fetch();
        return $tampon;
    }

    //RETOURNE LES QUESTIONS DEPUIS L'ID D'UN QUIZ
    public function readQuestionFromQuizId($quiz_id){
        $stmt = $this->db->query("SELECT * FROM question WHERE quiz_id='".$quiz_id."'");
        $tampon=$stmt->fetchAll();
        return $tampon;
    }

    //RETOURNE LES REPONSES DEPUIS L'ID D'UN QUIZ
    public function readAnswerFromQuizId($quiz_id){
        $stmt = $this->db->query("SELECT * FROM reponse WHERE quiz_id='".$quiz_id."'");
        $tampon=$stmt->fetchAll();
        return $tampon;
    }

    //RETOURNE TOUT LES QUIZS
    public function readAllQuiz(){
        $stmt = $this->db->query("SELECT * FROM quiz INNER JOIN theme WHERE quiz.theme_id=theme.theme_id");
        $tampon=$stmt->fetchAll();
        return $tampon;
    }

    //RETOURNE LES CLES QUESTION/REPONSE DEPUIS UN ID DE QUIZ
    public function readQuestionAnswerFromQuizId($quiz_id){
        $stmt = $this->db->query("SELECT * FROM (question INNER JOIN reponse ON question.quiz_id = reponse.quiz_id) WHERE question.quiz_id='".$quiz_id."' AND question.question_id=reponse.question_id");
        $tampon=$stmt->fetchAll();
        $semiCleanArray=array();
        foreach($tampon as $value){
            $tmp=array();
            $tmp["question_id"]=$value["question_id"];
            $tmp["question"]=$value["question"];
            $tmp["quiz_id"]=$value["quiz_id"];
            $tmp["qcm"]=$value["qcm"];
            $tmp["reponse"]=$value["reponse"];
            $tmp["reponse_id"]=$value["reponse_id"];
            array_push($semiCleanArray,$tmp);
        }

        $questions_parcourues=array();
        $CleanArray=array();
        $tmp=array();
        foreach($semiCleanArray as $value){
            if(in_array($value["question_id"], $questions_parcourues)){//SI LA QUESTION EXISTE DEJA
                $tmp2=$CleanArray[$value["question_id"]]["reponse"];
                $tmp2[$value["reponse_id"]]=array("reponse" => $value["reponse"],"reponse_id" => $value["reponse_id"]);
                $CleanArray[$value["question_id"]]["reponse"]=$tmp2;
            }
            else{
                array_push($questions_parcourues,$value["question_id"]);
                $tmp["question_id"]=$value["question_id"];
                $tmp["question"]=$value["question"];
                $tmp["quiz_id"]=$value["quiz_id"];
                $tmp["qcm"]=$value["qcm"];
                $tmp["reponse"]=array($value["reponse_id"] => array("reponse" => $value["reponse"],"reponse_id" => $value["reponse_id"]));
                $CleanArray[$value["question_id"]]=$tmp;
            }
        }
        return $CleanArray;
    }

    //STOCKE UN QUIZ DANS LA BDD
    public function createQuizz(Quiz $quiz){

        //On obtient l'ID de l'user
        $id_user=$this->getUserIdFromLogin($_SESSION['user']->getLogin());



        //TABLE THEME--------------
        //On compte combien de fois apparait le theme
        $stmt = $this->db->query("SELECT COUNT(nom_theme) FROM theme WHERE nom_theme='".$quiz->getCategorie()."'");
        $nb_themes=$stmt->fetch();
        if($nb_themes[0]==0){ //SI le theme n'existe pas on le creer
            $rq="INSERT INTO theme (theme_id,nom_theme)VALUES(?,?)";
            $stmt=$this->db->prepare($rq);
            $stmt->execute(array("",$quiz->getCategorie()));
        }

        //THEME_ID
        $stmt = $this->db->query("SELECT theme_id FROM theme WHERE nom_theme='".$quiz->getCategorie()."'");
        $theme_id=$stmt->fetch()[0];

        //TABLE QUIZ---------------

        //GENERATION DE L'ID DU QUIZ
        $stmt = $this->db->query("SELECT quiz_id FROM quiz");
        $qids=$stmt->fetchAll();
        $id_quiz=self::generate_id($qids);

        //INSERTION DANS LA TABLE
        $rq="INSERT INTO quiz (quiz_id,theme_id,user_id,description,nb_questions,nom_quiz)VALUES(?,?,?,?,?,?)";
        $stmt=$this->db->prepare($rq);
        $stmt->execute(array($id_quiz,$theme_id,$id_user,$quiz->getDescription(),$quiz->getNbQuest(),$quiz->getNom()));

        var_export($quiz->getQuestionsAnswers());
        //TABLE QUESTION-----------
        foreach($quiz->getQuestionsAnswers() as $qa){
            //GENERATION DE L'ID DE LA QUESTION
            $stmt = $this->db->query("SELECT question_id FROM question");
            $ids=$stmt->fetchAll();
            $id_question=self::generate_id($ids);
            //INSERTION DE L'ID DE LA QUESTION
            $rq="INSERT INTO question (question_id,question,quiz_id,qcm)VALUES(?,?,?,?)";
            $stmt=$this->db->prepare($rq);
            $stmt->execute(array($id_question,$qa['Question'],$id_quiz,$qa['QCM']));

            foreach($qa['Reponse'] as $r){
                $rq="INSERT INTO reponse (reponse_id,question_id,reponse,quiz_id)VALUES(?,?,?,?)";
                $stmt=$this->db->prepare($rq);
                if($r!=""){
                    $r = str_replace("\n","",$r);
                    $r = str_replace("\r","",$r);
                    $r = str_replace("\t","",$r);
                    $stmt->execute(array("",$id_question,$r,$id_quiz));
                }
            }
        }
    }

    //STOCKE LES REPONSES D'UN USER
    public function stockUserQuizAnswer($quizAnswer,$userLogin,$date,$quiz_id){
        $stmt = $this->db->query("SELECT user_id FROM users WHERE login='".$userLogin."'");
        $userId=$stmt->fetch()["user_id"];

        try {//IF THE USER ALREADY ANSWERED, DELETE LAST ANSWERS
            $rq = $this->db->query("DELETE FROM user_reponse WHERE user_id='".$userId."' AND quiz_id='".$quiz_id."'");
            $xxx=$stmt->fetch();
        } catch (Exception $e) {
            //THERE IS NOT ANSWERS FOR THE SAMER QUIZ
        }

        $test=$this->readQuestionAnswerFromQuizId($quiz_id);

        foreach($test as $q){

            $qId=$q["question_id"]; //qID est lid de la question
            if($q["qcm"]=="on"){//SI C'EST UN QCM
                foreach($quizAnswer[$qId] as $k){

                    $stmt = $this->db->query("SELECT reponse_id FROM reponse WHERE reponse='".$k."' AND question_id='".$qId."'");
                    $rId = $stmt->fetch();

                    $rq = "INSERT INTO user_reponse (user_id,question_id,reponse_id,quiz_id,date)VALUES(?,?,?,?,?)";
                    $stmt = $this->db->prepare($rq);
                    $stmt->execute(array($userId, $qId, $rId["reponse_id"], $quiz_id, $date));
                }

            }
            else{
                $stmt = $this->db->query("SELECT reponse_id FROM reponse WHERE reponse='".$quizAnswer[$qId]."' AND question_id='".$qId."'");
                $rId = $stmt->fetch();

                $rq = "INSERT INTO user_reponse (user_id,question_id,reponse_id,quiz_id,date)VALUES(?,?,?,?,?)";
                $stmt = $this->db->prepare($rq);
                $stmt->execute(array($userId, $qId, $rId["reponse_id"], $quiz_id, $date));
            }
        }

    }

    public function updateNomDescriptionQuiz($quiz_id,$nom,$description){
        try {
            $rq="UPDATE quiz SET nom_quiz=:nom , description=:description where quiz_id='".$quiz_id."'";
            $stmt=$this->db->prepare($rq);
            $stmt->execute(array(":nom" =>$nom,":description" =>$description));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteQuiz($quiz_id){
        try{
            $this->db->query("DELETE FROM question WHERE quiz_id='".$quiz_id."'");
            $this->db->query("DELETE FROM reponse WHERE quiz_id='".$quiz_id."'");
            $this->db->query("DELETE FROM user_reponse WHERE quiz_id='".$quiz_id."'");
            $this->db->query("DELETE FROM quiz WHERE quiz_id='".$quiz_id."'");
            return true;
        }catch (Exception $e) {
            return false;
        }
    }



    static private function generate_id($ids) {
        do {

            $id = bin2hex(openssl_random_pseudo_bytes(8));

        } while (is_numeric($id[0]) || in_array($id, $ids));

        return $id;
    }
}
