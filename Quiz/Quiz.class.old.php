<?php

class Quiz{
    public static $TBL_QUIZ;
    public static $TBL_EXERCICE_QUESTION;
    public static $TBL_EXERCICES;
    public static $TBL_REPONSES;

    public static $quiz;
    public static $quizId;
    public static $quizType;
    
    public static $questions;
    public static $userId;
    public static $code;
    public static $year;
    public static $semester;

    public static $status = "";

    public static $exerciseAnswerId;
    public static $testiTime = TESTI_TIME;
    public static $tashrihiTime = DESCRIPTIVE_TIME;    
    public static $editTime = EDIT_TIME;    

    public static $currentQuestion;
    public static $currentQuestionInfo;
    
    static function init(){
        self::$quizId = $_GET['exerciseId'];
        self::$userId = $_SESSION['_user']['user_id'];
        self::$code = $_GET['cidReq'];

        # Get From DB
        if($_SESSION['eYear'] == ""){
            $query = "SELECT * FROM `current_semester`";
            $result= api_sql_query($query,__FILE__,__LINE__);
            $data = mysql_fetch_assoc($result);
            $_SESSION['eYear'] = $data['year'];
            $_SESSION['eSemester'] = $data['semester'];
            self::$year = $data['year'];
            self::$semester = $data['semester'];

        # Get From SESSION
        }else{
            self::$year = $_SESSION['eYear'];
            self::$semester = $_SESSION['eSemester'];
        }
        
    }
    
    static function tablesConfig($TBL_QUIZ, $TBL_EXERCICE_QUESTION, $TBL_EXERCICES, $TBL_REPONSES){
        self::$TBL_QUIZ = $TBL_QUIZ;
        self::$TBL_EXERCICE_QUESTION = $TBL_EXERCICE_QUESTION;
        self::$TBL_EXERCICES = $TBL_EXERCICES;
        self::$TBL_REPONSES = $TBL_REPONSES;
    }

    static function getQuizInfo(){
        $query = "SELECT `id`, `title`, `type` FROM ".self::$TBL_QUIZ." WHERE id = '{$_GET['exerciseId']}'";
        $result= api_sql_query($query,__FILE__,__LINE__);
        self::$quiz = mysql_fetch_assoc($result);        

        return self::$quiz;
    }

    static function getQuizQuestions($getOptions = false){
        $TBL_EXERCICES = self::$TBL_EXERCICES;
        $TBL_EXERCICE_QUESTION = self::$TBL_EXERCICE_QUESTION;

        // $queryOld = "SELECT {$TBL_EXERCICES}.* FROM $TBL_EXERCICE_QUESTION join $TBL_EXERCICES ON question_id = id
        //             WHERE exercice_id = '{$_GET['exerciseId']}'";
        
        $question_id = array();
        $query = "SELECT `question_id` FROM $TBL_EXERCICE_QUESTION
                    WHERE exercice_id = '{$_GET['exerciseId']}'";
        $result= api_sql_query($query,__FILE__,__LINE__);
        while($data = mysql_fetch_assoc($result)){
            $question_id[] = $data['question_id'];
        }
        $questions = implode(",", $question_id);
        $query1 = "SELECT * FROM $TBL_EXERCICES
                    WHERE id IN ($questions)";
        $result1= api_sql_query($query1,__FILE__,__LINE__);
        
        self::$questions = array();
        while($data = mysql_fetch_assoc($result1)){
            // If Question type is UNIQUE_ANSWER
            if($data['type'] == UNIQUE_ANSWER && $getOptions){
                $query2 = "SELECT * FROM ".self::$TBL_REPONSES."
                          WHERE `question_id` = '".$data['id']."'";

                $result2 = api_sql_query($query2, __FILE__, __LINE__);
                
                $data['options'] = array();
                while($data2   = mysql_fetch_assoc($result2)){
                    $data['options'][] = $data2;
                }                
            }
            if($getOptions){
                $query3 = "SELECT * FROM `dokeos_exercises`.`exercise_answer_items` 
                            WHERE `exercise_id` = '".$_GET['exerciseId']."'
                                AND `exercise_question_id` = '".$data['id']."'
                                AND `user_id` = '".self::$userId."'";

                $result3 = api_sql_query($query3, __FILE__, __LINE__);
                $data3   = mysql_fetch_assoc($result3);

                $data['response'] = $data3;
            }
            self::$questions[] = $data;
        }

        return self::$questions;
    }

    static function checkAnswerPrepare(){
        unset($_SESSION['eChecked-'.self::$code]);

        if(!$_SESSION['eChecked-'.self::$code]){
            $query = "SELECT count(*) as `number`, id FROM `dokeos_exercises`.`exercise_answer` 
                        WHERE `semester` = '".self::$semester."' 
                        AND `year` = '".self::$year."'
                        AND `user_id` = '".self::$userId."'
                        AND `exercise_id` = '".self::$quizId."'
                        AND `code` = '".self::$code."'";        

            $result = api_sql_query($query, __FILE__, __LINE__);
            $data   = mysql_fetch_assoc($result);
            $_SESSION['exerciseAnswerId-'.self::$code] = $data['id'];
            $currentDate = JalaliDate::jdate('Y-m-d H:i:s');

            if($data['number'] == 0){
                // $quizInfo = self::getQuizInfo();
                $query = "INSERT INTO `dokeos_exercises`.`exercise_answer` 
                            (`semester`, `year`, `user_id`, `exercise_id`, `code`, `start_date`, `status`)
                            VALUES ('".self::$semester."', '".self::$year."', '".self::$userId."', ".self::$quizId.", '".self::$code."', '$currentDate', '0')";
                
                $result = api_sql_query($query, __FILE__, __LINE__);
                $id     = mysql_insert_id();

                $quistions = self::getQuizQuestions(true);
                $order = 0;
                foreach($quistions as $quiz){                    
                    $options = array();
                    foreach($quiz['options'] as $option){
                        $options[] = array(
                                        'id'=>$option['id'], 
                                        // 'answer'=>mysql_escape_string($option['answer']),
                                        'answer'=>$option['answer'],
                                        'correct'=>$option['correct'],
                                        'ponderation'=>$option['ponderation'],
                                        'position'=>$option['position']
                                    );
                    }
                    if($quiz['type'] == UNIQUE_ANSWER){
                        $optionJson = base64_encode(serialize($options));
                    }
                    $order++;

                    $query2 = "INSERT INTO `dokeos_exercises`.`exercise_answer_items` 
                            (`exercise_answer_id`, `answer_option`, `answer_text`, `user_id`, `exercise_id`, `exercise_question_id`, `order`, `exercise_type`, `question`, `question_options`)
                            VALUES ('".$id."', '0', '', '".self::$userId."', '".self::$quizId."', '".$quiz['id']."', '$order', '".$quiz['type']."', '".$quiz['question']."', '$optionJson')";
                    api_sql_query($query2, __FILE__, __LINE__);
                }
                // $data   = mysql_fetch_assoc($result);
            }
            $_SESSION['eChecked-'.self::$code] = true;
        }
                
    }

    static function getCurrentQuizQuestionNew(){
        // get current answer_items record!!
        self::$exerciseAnswerId = $_SESSION['exerciseAnswerId-'.self::$code];

        $query2 = "SELECT * FROM `dokeos_exercises`.`exercise_answer_items` 
                        WHERE `exercise_answer_id` = '".self::$exerciseAnswerId."'
                            AND (
                                    (`deadline_time` > '".time()."' AND `status` = '1') 
                                    OR `status` = '0'
                                )
                        ORDER BY `id`
                        LIMIT 0, 1";
        $result2 = api_sql_query($query2, __FILE__, __LINE__);
        $data2   = mysql_fetch_assoc($result2);

        // no record!!
        if($data2['id'] != ""){
            # در صورتیکه زمان پاسخ به سوال فعلی به پایان نرسیده است
            if($data2['deadline_time'] > 0){
                self::$currentQuestion = $data2;
            }else{
                $time = time() + (($data2['exercise_type'] == '1')?self::$testiTime:self::$tashrihiTime + 1);
                $currentDate = JalaliDate::jdate('Y-m-d H:i:s');
                # هنوز آزمون شروع نشده است
                if($data2['order'] == 1 && $_GET['status'] != "start"){
                    self::$status = "beforeStart";
                    return;
                }

                # در غیر این صورت
                $query4 = "UPDATE `dokeos_exercises`.`exercise_answer_items` 
                            SET `status` = '1', 
                                deadline_time = '".$time."',
                                `start_date` = '$currentDate'
                            WHERE id = '{$data2[id]}'";
                api_sql_query($query4, __FILE__, __LINE__);

                // $data5   = mysql_fetch_assoc($result5);
                $data2['deadline_time'] = $time;
                $data2['start_date'] = $currentDate;
                self::$currentQuestion = $data2;
            }
        }
        else{
            // get current exercise record!!
            $query1 = "SELECT * FROM `dokeos_exercises`.`exercise_answer` 
            WHERE `id` = '".self::$exerciseAnswerId."'";        

            $result1 = api_sql_query($query1, __FILE__, __LINE__);
            $data1   = mysql_fetch_assoc($result1);

            // End Exam?!
            if($data1['status'] == 1 || ($data1['review_deadline_time'] > 0 && (time() > $data1['review_deadline_time']) )){
                self::$status = "endExam";
                $query2 = "UPDATE `dokeos_exercises`.`exercise_answer` 
                        SET `status` = '1'                            
                        WHERE id = '$data1[id]'";        

                $result2 = api_sql_query($query2, __FILE__, __LINE__);
                return;
            }
            // if($data1['id'] == ""){
            //     self::$status = "beforeStart";
            // }else{
                if($_GET['status'] == "edit"){
                    self::$status = "edit";
                }else{
                    self::$status = "endAnswering";
                }
            // }
        }
    }

    static function getCurrentQuizQuestion(){
        // get current exercise record!!
        $query1 = "SELECT * FROM `dokeos_exercises`.`exercise_answer` 
                        WHERE `semester` = '".self::$semester."' 
                        AND `year` = '".self::$year."'
                        AND `user_id` = '".self::$userId."'
                        AND `exercise_id` = '".self::$quizId."'
                        AND `code` = '".self::$code."'";        

        $result1 = api_sql_query($query1, __FILE__, __LINE__);
        $data1   = mysql_fetch_assoc($result1);
        self::$exerciseAnswerId = $data1['id'];
        
        // End Exam?!
        if($data1['status'] == 1 || ($data1['review_deadline_time'] > 0 && (time() > $data1['review_deadline_time']) )){
            self::$status = "endExam";
            $query2 = "UPDATE `dokeos_exercises`.`exercise_answer_items` 
                        SET `status` = '1'                            
                        WHERE id = '$data1[id]'";        

            $result2 = api_sql_query($query2, __FILE__, __LINE__);
            return;
        }

        // get current answer_items record!!
        $query2 = "SELECT * FROM `dokeos_exercises`.`exercise_answer_items` 
                        WHERE `exercise_answer_id` = '".self::$exerciseAnswerId."'
                            AND `deadline_time` > '".time()."'
                            AND `status` = '1'";
        $result2 = api_sql_query($query2, __FILE__, __LINE__);
        $data2   = mysql_fetch_assoc($result2);

        // no record!!
        if($data2['id'] != ""){
            self::$currentQuestion = $data2;
        }
        else{
            $query3 = "SELECT `id`, `order`, `exercise_type` FROM `dokeos_exercises`.`exercise_answer_items` 
                        WHERE `exercise_answer_id` = '".self::$exerciseAnswerId."'
                            AND `status` = '0'
                        ORDER BY `id`
                        LIMIT 0, 1";
            $result3 = api_sql_query($query3, __FILE__, __LINE__);
            $data3   = mysql_fetch_assoc($result3);

            // echo $data3['exercise_type'];
            // exit();
            // $time = time() + (($data1['exercise_type'] == '1')?self::$testiTime:self::$tashrihiTime + 1);
            $time = time() + (($data3['exercise_type'] == '1')?self::$testiTime:self::$tashrihiTime + 1);
            $currentDate = JalaliDate::jdate('Y-m-d H:i:s');

            if($data3['id'] != ""){
                if($data3['order'] == 1 && $_GET['status'] != "start"){
                    self::$status = "beforeStart";
                    return;
                }

                $query4 = "UPDATE `dokeos_exercises`.`exercise_answer_items` 
                            SET `status` = '1', 
                                deadline_time = '".$time."',
                                `start_date` = '$currentDate'
                            WHERE id = '{$data3[id]}'";
                api_sql_query($query4, __FILE__, __LINE__);

                $query5 = "SELECT * FROM `dokeos_exercises`.`exercise_answer_items` 
                    WHERE `id` = '{$data3[id]}'";

                $result5 = api_sql_query($query5, __FILE__, __LINE__);
                $data5   = mysql_fetch_assoc($result5);
                self::$currentQuestion = $data5;
            }
            else{
                if($_GET['status'] == "edit"){
                    self::$status = "edit";
                }else{
                    self::$status = "endAnswering";
                }
            }
        }
    }

    public function getQuestionInfoNew(){                
        $data1 = self::$currentQuestion;
        # get answers in testi questions
        $data1['type'] = $data1['exercise_type'];
        if($data1['exercise_type'] == UNIQUE_ANSWER){
            $data1['answers'] = unserialize(base64_decode($data1['question_options']));
        }

        return self::$currentQuestionInfo = $data1;
    }
    # get the question information from course database
    public function getQuestionInfo(){
        
        $query1 = "SELECT * FROM ".self::$TBL_EXERCICES."
                          WHERE `id` = '".self::$currentQuestion['exercise_question_id']."'";

        $result1 = api_sql_query($query1, __FILE__, __LINE__);
        $data1   = mysql_fetch_assoc($result1);

        # get answers in testi questions
        if($data1['type'] == UNIQUE_ANSWER){
            $query2 = "SELECT * FROM ".self::$TBL_REPONSES."
                          WHERE `question_id` = '".self::$currentQuestion['exercise_question_id']."'";

            // echo $query2;
            $result2 = api_sql_query($query2, __FILE__, __LINE__);
            
            $data1['answers'] = array();
            while($data2   = mysql_fetch_assoc($result2)){
                $data1['answers'][] = $data2;
            }            
        }

        return self::$currentQuestionInfo = $data1;
    }

    # save the student answer in to exercise_answer_items table
    static function saveAnswer(){
        if(isset($_POST['answer_id'])){
            $id            = $_POST['answer_id'];
            $answer_text   = $_POST['answer_text'];
            $answer_option = $_POST['answer_option'];
            if($answer_option == "") $answer_option = 0;
            $endDate = JalaliDate::jdate('Y-m-d H:i:s');

            $query = "UPDATE `dokeos_exercises`.`exercise_answer_items` 
                        SET  `answer_text`   = '$answer_text',
                             `answer_option` = '$answer_option',
                             `end_date`      = '$endDate',
                             `status`        = '2'
                        WHERE `id` = '$id'";
            
            $result = api_sql_query($query, __FILE__, __LINE__);
            $saveNumber = mysql_affected_rows();
            echo $saveNumber;
        }
    }

    static function saveAllNew(){
        if(isset($_POST['answer_type'])){            
            $answer_type = $_POST['answer_type'];
                        
            $endDate = JalaliDate::jdate('Y-m-d H:i:s');
            $saveNumber = 0;

            $answer_text = array();
            $answer_option = array();
            $idArray = array();

            foreach($_POST as $key=>$info){
                if(substr($key, 0, 11) == "answer_text"){
                    $str = explode("_", $key);
                    $id = $str[2];
                    $idArray[] = $id;
                    $answer_text[] = "WHEN $id THEN '$info'";

                }else if(substr($key, 0, 13) == "answer_option"){
                    $str = explode("_", $key);
                    $id = $str[2];
                    $idArray[] = $id;
                    $answer_option[] = "WHEN $id THEN '$info'";
                }
            }
            $conditionArray = array();
            if(count($answer_text)>0) $conditionArray[] = "`answer_text` = (CASE id ".implode(" ", $answer_text)." END)";
            if(count($answer_option)>0) $conditionArray[] = "`answer_option` = (CASE id ".implode(" ", $answer_option)." END)";
            $condition = implode(", ", $conditionArray);
            $ids = implode(", ", $idArray);

            $query = "UPDATE `dokeos_exercises`.`exercise_answer_items`
                        SET $condition
                        WHERE id IN($ids)";
            $result = api_sql_query($query, __FILE__, __LINE__);
            $saveNumber = mysql_affected_rows();
            
            echo $saveNumber;
        }
        
    }
    static function saveAll(){
        if(isset($_POST['answer_type'])){            
            $answer_type = $_POST['answer_type'];
                        
            $endDate = JalaliDate::jdate('Y-m-d H:i:s');
            $saveNumber = 0;

            foreach($_POST as $key=>$info){
                if(substr($key, 0, 11) == "answer_text"){
                    $str = explode("_", $key);
                    $id = $str[2];
                    $query = "UPDATE `dokeos_exercises`.`exercise_answer_items` 
                            SET `answer_text` = '$info'
                            WHERE `id` = '$id'";
                    $result = api_sql_query($query, __FILE__, __LINE__);
                    $saveNumber += mysql_affected_rows();

                }else if(substr($key, 0, 13) == "answer_option"){
                    $str = explode("_", $key);
                    $id = $str[2];
                    $query = "UPDATE `dokeos_exercises`.`exercise_answer_items` 
                            SET `answer_option` = '$info'
                            WHERE `id` = '$id'";
                    $result = api_sql_query($query, __FILE__, __LINE__);
                    $saveNumber += mysql_affected_rows();
                    
                }
            }
            
            echo $saveNumber;
        }
    }

    static function endQuiz(){
        if(isset($_POST['exercise_answer_id'])){            
            $exercise_answer_id = $_POST['exercise_answer_id'];

            $query = "UPDATE `dokeos_exercises`.`exercise_answer` 
                        SET `status` = '1'
                        WHERE `id` = '$exercise_answer_id'";
            $result = api_sql_query($query, __FILE__, __LINE__);
            $saveNumber = mysql_affected_rows();
            
            echo $saveNumber;
        }
    }

    static function getAllQuestion(){
        self::getQuizQuestions(true);
        $query = "SELECT * FROM `dokeos_exercises`.`exercise_answer` 
                        WHERE `semester` = '".self::$semester."' 
                        AND `year` = '".self::$year."'
                        AND `user_id` = '".self::$userId."'
                        AND `exercise_id` = '".self::$quizId."'
                        AND `code` = '".self::$code."'";        

        $result = api_sql_query($query, __FILE__, __LINE__);
        $data   = mysql_fetch_assoc($result);

        if($data['review_deadline_time'] < 1){
            $timeLeft = time() + self::$editTime;
            $query = "UPDATE `dokeos_exercises`.`exercise_answer`
                        SET  `review_deadline_time`   = '$timeLeft'
                        WHERE `id` = '".$data['id']."'";
            $result = api_sql_query($query, __FILE__, __LINE__);
            $data['review_deadline_time'] = $timeLeft;
        }
        $data['review_deadline_time'] = $data['review_deadline_time'] - time();
        if($data['review_deadline_time'] < 0) $data['review_deadline_time'] = 1;
        self::$quiz = $data;
        // return $data;
    }
}