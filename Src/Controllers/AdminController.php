<?php
include_once "Core/Controller.php";


class AdminController extends Controller
{
    function __construct($params)
    {
       parent::__construct($params);
    }
    public function run($ctx)
    {
        if(!isset($_SESSION["Admin"])){
            header('Location: /Default');
        }
        if(!isset($_GET["action"])){
            $this->displayAdmin();
        }else{
            $action = $_GET["action"];
            switch($action){
                case 'displayCreateSubject':
                    return $this->displayCreateSubject();
                case 'createSubject':
                    return $this->createSubject();
                case 'displayDeleteSubject':
                    return $this->displayDeleteSubject();
                case 'deleteSubject':
                    return $this->deleteSubject();
                case 'displayDeleteQuestion':
                    return $this->displayDeleteQuestion();
                case 'deleteQuestion':
                    return $this->deleteQuestion();
                case 'displayDeleteResponse':
                    return $this->displayDeleteResponse();
                case 'deleteResponse':
                    return $this->deleteResponse();
                case 'displayUsers':
                    return $this->displayUsers();
                case 'banUser':
                    return $this->banUser();
                case 'validateQuestion':
                    return $this->validateQuestion();
                case 'validateResponse':
                    return $this->validateResponse();
                case 'createStatut':
                    return $this->createStatut();
                case 'displayCreateStatut':
                    return $this->displayCreateStatut();
                case 'deleteStatut':
                    return $this->deleteStatut();
                case 'displayDeleteStatut':
                    return $this->displayDeleteStatut();
                case 'changeRightsUser':
                    return $this->changeRightsUser();
                case 'createCategory':
                    return $this->createCategory();
                case 'displayCreateCategory':
                    return $this->displayCreateCategory();
                case 'deleteCategory':
                    return $this->deleteCategory();
                case 'displayDeleteCategory':
                    return $this->displayDeleteCategory();

            }
        }
    }

    public function createCategory()
    {
        if(isset($_POST["name"]) == false || strlen($_POST["name"]) <= 0)
        {
            header("Location: /Admin?action=displayCreateCategory&info=NameError");
            return;
        }
        $storage = Engine::Instance()->Persistence("DatabaseStorage");
        $cat = new Category($storage);
        $cat->setName(Utils::MakeTextSafe($_POST["name"]));
        $storage->persist($cat);
        $storage->flush();
        header('Location: /Admin?info=CategoryCreated');
    }

    public function displayCreateCategory()
    {
        $info = "";
        if(isset($_GET["info"]))
        {
            if($_GET["info"] === "NameError")
            {
                $info = "Nom invalide.";
            }
        }
        $data = Utils::SessionVariables();
        $data["info"] = $info;
        $view = new View("createCategory", $data);
        $view->setTitle("Cr�er une cat�gorie");
        $view->show();
    }

    public function deleteCategory()
    {
        if(isset($_GET["categoryId"]) == false)
        {
            header("Location: /Admin?info=Null");
            return;
        }
        $storage = Engine::Instance()->Persistence("DatabaseStorage");
        $cat = new Category($storage, $_GET["categoryId"]);
        $storage->remove($cat);
        $storage->flush();
        header("Location: /Admin?info=CategoryDeleted");
    }

    public function displayDeleteCategory()
    {
        $data = Utils::SessionVariables();
        $storage = Engine::Instance()->Persistence("DatabaseStorage");
        $cats = Null;
        $storage->findAll("Category", $cats);
        $data["categories"] = array();
        foreach ($cats as $cat)
        {
            array_push($data["categories"], get_object_vars($cat));
        }
        $view = new View("deleteCategory", $data);
        $view->setTitle("Supprimer une cat�gorie");
        $view->show();
    }

    public function changeRightsUser()
    {

        if(isset($_GET["userId"]) == false)
        {
            header("Location: /Admin?info=Null");
            return;
        }
        $storage = Engine::Instance()->Persistence("DatabaseStorage");
        $user = new User($storage, $_GET["userId"]);
        $user = $storage->find($user);
        echo "VAL: ".intval($user->isadmin);
        if(intval($user->isadmin) == 1)
        {
            $user->setIsAdmin("0");
        }
        else {
            $user->setIsAdmin("1");
        }
        $storage->persist($user, $state = StorageState::ToUpdate);
        $storage->flush();
        header("Location: /Admin?info=UserRightsChanged");
    }

    public function deleteStatut()
    {
        if(isset($_GET["statutId"]) == false)
        {
            header("Location: /Admin?info=Null");
            return;
        }
        $storage = Engine::Instance()->Persistence("DatabaseStorage");
        $statut = new Status($storage, $_GET["statutId"]);
        $storage->remove($statut);
        $storage->flush();
        header("Location: /Admin?info=StatutDeleted");
    }


    public function displayDeleteStatut()
    {
        $data = Utils::SessionVariables();
        $storage = Engine::Instance()->Persistence("DatabaseStorage");
        $status = Null;
        $storage->findAll("Status", $status);
        $data["status"] = array();
        foreach ($status as $statut)
        {
            array_push($data["status"], get_object_vars($statut));
        }
        $view = new View("deleteStatut", $data);
        $view->setTitle("Supprimer un statut");
        $view->show();
    }

    public function createStatut()
    {
        if(isset($_POST["name"]) == false || strlen($_POST["name"]) <= 0)
        {
            header("Location: /Admin?action=displayCreateStatut&info=NameError");
            return;
        }
        $storage = Engine::Instance()->Persistence("DatabaseStorage");
        $statut = new Status($storage);
        $statut->setName(Utils::MakeTextSafe($_POST["name"]));
        $storage->persist($statut);
        $storage->flush();
        header('Location: /Admin?info=StatutCreated');
    }

    public function displayCreateStatut()
    {
        $info = "";
        if(isset($_GET["info"]))
        {
            if($_GET["info"] === "NameError")
            {
                $info = "Nom invalide.";
            }
        }
        $data = Utils::SessionVariables();
        $data["info"] = $info;
        $view = new View("createStatut", $data);
        $view->setTitle("Cr�er un statut");
        $view->show();
    }
    
    public function displayAdmin(){
        $data = Utils::SessionVariables();
        $questions = NULL;
        $condition = "reported = '1'";
        $storage = Engine::Instance()->Persistence("DatabaseStorage");
        $storage->findAll("Question",$questions,$condition);
        $data["questions"] = array();
        foreach ($questions as $question) {
            $responsevalues = get_object_vars($question);
            $subject = new Subject($storage);
            $subject = $question->Subject();
            $responsevalues["subject_id"] = $subject->Id();
            array_push($data["questions"],$responsevalues);
        }

        $responses = NULL;
        $condition = "reported = '1'";
        $storage->findAll("Response",$responses,$condition);
        $data["responses"] = array();
        foreach ($responses as $response) {
            $vars = get_object_vars($response);
            $question = new Question($storage, $response->QuestionId());
            $question = $storage->find($question);
            $vars["subject_id"] = $question->SubjectId();
            array_push($data["responses"],$vars);
        }

        $services = Null;
        $condition = "reported = '1'";
        $storage->findAll("Service",$services,$condition);
        $data["services"] = array();
        foreach($services as $service)
        {
            array_push($data["services"], get_object_vars($service));
        }

        $info = $_GET["info"];
        if($info === "NULL"){
            $info = "";
        }
        if($info === "CategoryCreated"){
            $info = "La cat�gorie a bien �t� cr��.";
        }
        if($info === "SubjectCreated"){
            $info = "Le sujet a bien �t� cr��.";
        }
        if($info === "UserRightsChanged"){
            $info = "Les droits de l'utilisateur ont bien �t� chang�s.";
        }
        if($info === "StatutCreated"){
            $info = "Le statut a bien �t� cr��.";
        }
        if($info === "ErrorCreationSubject"){
            $info = "Erreur cr�ation : nom de sujet invalide.";
        }
        if($info === "SubjectDeleted"){
            $info = "Le sujet a bien �t� supprim�.";
        }
        if($info === "StatutDeleted"){
            $info = "Le statut a bien �t� supprim�.";
        }
        if($info === "QuestionDeleted"){
            $info = "La question a bien �t� supprim�e.";
        }
        if($info === "ResponseDeleted"){
            $info = "La r�ponse a bien �t� supprim�e.";
        }
        if($info === "CategoryDeleted"){
            $info = "La cat�gorie a bien �t� supprim�e.";
        }
        if($info === "UserBanned"){
            $info = "L'utilisateur a bien �t� banni.";
        }

        $data["info"] = $info;
        $view = new View("admin",$data);
        $view->setTitle("Administration");
        $view->show();
    }

    public function displayCreateSubject(){
        $data = Utils::SessionVariables();
        $view = new View("createSubject",$data);
        $view->setTitle("createSubject");
        $view->show();
    }

    public function createSubject(){
        if(isset($_POST['name'])){
                $name = $_POST['name'];
            if($name !== ""){
                $storage = Engine::Instance()->Persistence("DatabaseStorage");
                $subject = new Subject($storage);
                $subject->SetName($name);
                $storage->persist($subject);
                $storage->flush();
                header('Location: /Admin&info=SubjectCreated');
            }else{
                header('Location: /Admin&info=ErrorCreationSubject');
            }
        }else{
            header('Location: /Admin&info=ErrorCreationSubject');
        }
    }

    public function displayDeleteSubject(){
        $data = Utils::SessionVariables();
        $subjects = NULL;
        $storage = Engine::Instance()->Persistence("DatabaseStorage")->findAll("Subject",$subjects);
        $data["subjects"] = array();
        foreach ($subjects as $subject) {
            array_push($data["subjects"],get_object_vars($subject));
        }
        $view = new View("deleteSubject",$data);
        $view->setTitle("Suppression de sujet");
        $view->show();
    }

    public function deleteSubject(){
        if(isset($_GET['subjectId'])){
            $subject_id = $_GET['subjectId'];
            $storage = Engine::Instance()->Persistence("DatabaseStorage");
            $subject = new Subject($storage, $subject_id);
            $subject = $storage->find($subject);
            $storage->remove($subject);
            $storage->flush();
            header('Location: /Admin&info=SubjectDeleted');
        }else{
            header('Location: /Admin&info=NULL');
        }
    }

    public function displayDeleteQuestion(){
        $data = Utils::SessionVariables();
        $questions = NULL;
        $storage = Engine::Instance()->Persistence("DatabaseStorage")->findAll("Question",$questions);
        $data["questions"] = array();
        foreach ($questions as $question) {
            array_push($data["questions"],get_object_vars($question));
        }
        $view = new View("deleteQuestion",$data);
        $view->setTitle("Suppression de Question");
        $view->show();
    }

    public function deleteQuestion(){
        if(isset($_GET['questionId'])){
            $question_id = $_GET['questionId'];
            $storage = Engine::Instance()->Persistence("DatabaseStorage");
            $question = new Question($storage, $question_id);
            $subject = $storage->find($question);
            $storage->remove($question);
            $storage->flush();
            header('Location: /Admin&info=QuestionDeleted');
        }else{
            header('Location: /Admin&info=NULL');
        }
    }

    public function displayDeleteResponse(){
        $data = Utils::SessionVariables();
        $subject_id = $_GET['subjectId'];
        $question_id = $_GET['questionId'];
        $response_id = $_GET['responseId'];
        $subjects = NULL;
        $storage = Engine::Instance()->Persistence("DatabaseStorage")->findAll("Subject",$subjects);
        $data["subjects"] = array();
        foreach ($subjects as $subject) {
            array_push($data["subjects"],get_object_vars($subject));
        }

        $questions = NULL;
        $condition = "subject_id = ".$subject_id;
        $storage = Engine::Instance()->Persistence("DatabaseStorage")->findAll("Question",$questions,$condition);
        $data["questions"] = array();
        foreach ($questions as $question) {
            array_push($data["questions"],get_object_vars($question));
        }

        $responses = NULL;
        $condition = "question_id = ".$question_id;
        $storage = Engine::Instance()->Persistence("DatabaseStorage")->findAll("Response",$responses,$condition);
        $data["responses"] = array();
        foreach ($responses as $response) {
            array_push($data["responses"],get_object_vars($response));
        }

        $data["subject_id"] = $subject_id;
        $data["question_id"] = $question_id;
        $data["response_id"] = $response_id;
        $view = new View("deleteResponse",$data);
        $view->setTitle("Suppression de r�ponse");
        $view->show();
    }

    public function deleteResponse(){
        if(isset($_GET['responseId'])){
            $id_question = Null;
            $id_subject = Null;
            $question = Null;

            $response_id = $_GET['responseId'];
            $storage = Engine::Instance()->Persistence("DatabaseStorage");
            $response = new Response($storage, $response_id);
            $response = $storage->find($response);
            $id_question = $response->QuestionId();
            $question = new Question($storage, $response->QuestionId());
            $id_subject = $question->SubjectId();
            $storage->remove($response);
            $storage->flush();
            header('Location: /Question?action=displayQuestionContent&subjectId='.$id_subject.'&questionId='.$id_question.'&info=ResponseDeleted');
        }else{
            header('Location: /Admin&info=NULL');
        }
    }

    public function displayUsers(){
        $data = Utils::SessionVariables();
        $users = NULL;
        $condition = "isbanned = 0";
        $storage = Engine::Instance()->Persistence("DatabaseStorage");
        $storage->findAll("User",$users,$condition);
        $data["users"] = array();
        foreach ($users as $user) {
            $vars = get_object_vars($user);
            $vars["is_admin"] = false;
            $vars["is_user"] = true;
            if($vars["isadmin"] == 1)
            {
                $vars["is_admin"] = true;
                $vars["is_user"] = false;
            }
            array_push($data["users"], $vars);
        }
        $view = new View("manageUsers",$data);
        $view->setTitle("Administrer les utilisateurs");
        $view->show();

    }

    public function banUser(){
        $data = Utils::SessionVariables();
        $user_id = $_GET['userId'];
        $storage = Engine::Instance()->Persistence("DatabaseStorage");
        $user = new User($storage, $user_id);
        $user = $storage->find($user);
        $user->setIsbanned(1);
        $storage->persist($user, $state = StorageState::ToUpdate);
        $storage->flush();
        header('Location: /Admin&info=UserBanned');
    }

    public function validateQuestion(){
        $data = Utils::SessionVariables();
        if(isset($_GET['questionId']) && isset($_GET['subjectId'])){
            $subject_id =  $_GET['subjectId'];
            $question_id = $_GET['questionId'];
            $storage = Engine::Instance()->Persistence("DatabaseStorage");
            $question = new Question($storage, $question_id);
            $question = $storage->find($question);
            $question->setReported(2);
            $storage->persist($question, $state = StorageState::ToUpdate);
            $storage->flush();
            header('Location: /Question?action=displayQuestionContent&subjectId='.$subject_id.'&questionId='.$question_id.'&info=QuestionValidated');
        }else{
            header('Location: /Default');
        }
    }

    public function validateResponse(){
        $data = Utils::SessionVariables();
        if(isset($_GET['responseId']) && isset($_GET['questionId']) && isset($_GET['subjectId'])){
            $subject_id =  $_GET['subjectId'];
            $question_id = $_GET['questionId'];
            $response_id = $_GET['responseId'];
            $storage = Engine::Instance()->Persistence("DatabaseStorage");
            $response = new Response($storage, $response_id);
            $response = $storage->find($response);
            $response->setReported(2);
            $storage->persist($response, $state = StorageState::ToUpdate);
            $storage->flush();
            header('Location: /Question?action=displayQuestionContent&subjectId='.$subject_id.'&questionId='.$question_id.'&info=ResponseValidated');
        }else{
            header('Location: /Default');
        }
    }
}