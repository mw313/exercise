<?php

class QuizDisplay{
    static public function displayQuestion(){
        Quiz::init();

        Quiz::quizManage();
        Quiz::getCurrentQuizQuestion();
                
        switch(Quiz::$status){
            case "beforeStart":
                self::displayBefore();
                return;
            case "endExam":
                self::displayEndExam();
                return;
            case "endAnswering":
                // self::displayLast();
                // return;
            case "edit":
                Quiz::getAllQuestion();
                if(Quiz::$questions[0]['type'] == 5){
                    self::displayAllDescriptive();
                }
                else{
                    self::displayAllTesti();
                }
                return;
        }        
                
        Quiz::getQuestionInfo();
        switch(Quiz::$currentQuestionInfo['type']){
            case UNIQUE_ANSWER:
                self::displayTesti();
                break;

            case FREE_ANSWER:
                self::displayDescriptive();
                break;
            
            default:
                self::displayLast();
        }
    }

    static public function displayTesti(){
        self::view("views/testi.tpl.php");
    }

    static public function displayDescriptive(){
        self::view("views/descriptive.tpl.php");
    }

    static public function displayLast(){
        self::view("views/endAnswering.tpl.php");
    }

    static public function displayEndExam(){
        self::view("views/endExam.tpl.php");
    }

    static public function displayAllDescriptive(){
        self::view("views/descriptive.all.tpl.php");
    }

    static public function displayAllTesti(){
        self::view("views/testi.all.tpl.php");
    }

    static public function displayBefore(){
        switch(Quiz::$quiz['type']){
            case UNIQUE_ANSWER:
                self::view("views/before.testi.tpl.php");
                break;
            case FREE_ANSWER:
                self::view("views/before.descriptive.tpl.php");
                break;
        }
    }

    static public function view($path, $variables){
        ob_start();
            $question = Quiz::$currentQuestionInfo;
            $question['deadline']       = Quiz::$currentQuestion['deadline_time'] - time();
            $question['order']          = Quiz::$currentQuestion['order'];
            $question['answer_id']      = Quiz::$currentQuestion['id'];
            $question['answer_text']    = Quiz::$currentQuestion['answer_text'];
            $question['answer_option']  = Quiz::$currentQuestion['answer_option'];
            $user  = $_SESSION['_user']['firstName']." ".$_SESSION['_user']['lastName'];
            $quiz  = Quiz::$quiz;
            $answers = Quiz::$questions;
            include_once($path);
        $result = ob_get_clean();

        echo $result;
    }

}