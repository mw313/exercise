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

    static function quizManage(){
        unset($_SESSION['eChecked-'.self::$code.'-'.self::$quizId]);

        if(!$_SESSION['eChecked-'.self::$code.'-'.self::$quizId]){
            $query = "SELECT count(*) as `number`, id, `exercise_type` as `type` FROM `dokeos_exercises`.`exercise_answer` 
                        WHERE `semester` = '".self::$semester."' 
                        AND `year` = '".self::$year."'
                        AND `user_id` = '".self::$userId."'
                        AND `exercise_id` = '".self::$quizId."'
                        AND `code` = '".self::$code."'";        

            $result = api_sql_query($query, __FILE__, __LINE__);
            $data   = mysql_fetch_assoc($result);
            $_SESSION['exerciseAnswerId-'.self::$code.'-'.self::$quizId] = $data['id'];
            $currentDate = JalaliDate::jdate('Y-m-d H:i:s');

            if($data['number'] == 0){
                $quistions = self::randomize(self::getQuizQuestions());
                $type = $quistions[0]['type'];
                
                $query = "INSERT INTO `dokeos_exercises`.`exercise_answer` 
                            (`semester`, `year`, `user_id`, `exercise_id`, `code`, `start_date`, `status`, `exercise_type`, `ip`)
                            VALUES ('".self::$semester."', '".self::$year."', '".self::$userId."', ".self::$quizId.", '".self::$code."', '$currentDate', '0', '$type', '$_SERVER[REMOTE_ADDR]')";
                
                $result = api_sql_query($query, __FILE__, __LINE__);
                $id     = mysql_insert_id();
                self::$quiz = array('id'=>$id, 'type'=>$type);

                $order = 0;
                $query2 = "INSERT INTO `dokeos_exercises`.`exercise_answer_items` 
                            (`exercise_answer_id`, `answer_option`, `answer_text`, `user_id`, `exercise_id`, `exercise_question_id`, `order`, `exercise_type`, `question`, `question_options`) VALUES ";
                $insert = array();
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
                        $optionJson = self::encode($options);
                    }
                    $order++;
                    $insert[] = "('".$id."', '0', '', '".self::$userId."', '".self::$quizId."', '".$quiz['id']."', '$order', '".$quiz['type']."', '".$quiz['question']."', '$optionJson')";
                    // $query2 = "INSERT INTO `dokeos_exercises`.`exercise_answer_items` 
                    //         (`exercise_answer_id`, `answer_option`, `answer_text`, `user_id`, `exercise_id`, `exercise_question_id`, `order`, `exercise_type`, `question`, `question_options`)
                    //         VALUES ('".$id."', '0', '', '".self::$userId."', '".self::$quizId."', '".$quiz['id']."', '$order', '".$quiz['type']."', '".$quiz['question']."', '$optionJson')";
                    
                }
                $insertItems = implode(", ", $insert);
                $query2 = $query2.$insertItems;
                api_sql_query($query2, __FILE__, __LINE__);
            }else{
                self::$quiz = $data;
                $_SESSION['quiz'.self::$code.'-'.self::$quizId] = self::$quiz;
            }
            $_SESSION['eChecked-'.self::$code.'-'.self::$quizId] = true;
        }else{
            self::$quiz = $_SESSION['quiz'.self::$code.'-'.self::$quizId];
        }
                
    }

    # get Questions from its tables at first or get from temp
    static function getQuizQuestions(){
        $TBL_EXERCICES = self::$TBL_EXERCICES;
        $TBL_EXERCICE_QUESTION = self::$TBL_EXERCICE_QUESTION;

        $sql = "SELECT * FROM `dokeos_exercises`.`quiz_temp` WHERE code = '".self::$code."' AND `quiz_id` = '".self::$quizId."'";
        $data = self::queryData($sql);

        if($data['id'] != ""){
            self::$questions = self::decode($data['quiz_text'], true);
        }else{
            $query1 = "SELECT {$TBL_EXERCICES}.* FROM $TBL_EXERCICE_QUESTION join $TBL_EXERCICES ON question_id = id
                        WHERE exercice_id = '{$_GET['exerciseId']}'";            
            $result1= api_sql_query($query1,__FILE__,__LINE__);
            self::$questions = array();
            while($data1 = mysql_fetch_assoc($result1)){
                if($data1['type'] == UNIQUE_ANSWER){
                    $query2 = "SELECT * FROM ".self::$TBL_REPONSES."
                              WHERE `question_id` = '".$data1['id']."'";
    
                    $result2 = api_sql_query($query2, __FILE__, __LINE__);
                    
                    $data1['options'] = array();
                    while($data2 = mysql_fetch_assoc($result2)){
                        // $data2['answer'] = self::strip_word_html($data2['answer']);
                        $data1['options'][] = $data2;
                    }
                }
                // $data1['question'] = self::strip_word_html($data1['question']);
                self::$questions[] = $data1;
            }
            
            $questionsTemp = self::encode(self::$questions);

            $sql = "INSERT INTO `dokeos_exercises`.`quiz_temp`(`code`, `quiz_id`, `quiz_text`, `date`) VALUES ('".self::$code."', '".self::$quizId."', '$questionsTemp', '')";
            api_sql_query($sql, __FILE__, __LINE__);
        }

        return self::$questions;
    }

    static function getCurrentQuizQuestion(){
        // get current answer_items record!!
        self::$exerciseAnswerId = $_SESSION['exerciseAnswerId-'.self::$code.'-'.self::$quizId];
        // echo self::$exerciseAnswerId;

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
        }else{
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
            if($data1['id'] == ""){
                self::$status = "beforeStart";
            }else{
                if($_GET['status'] == "edit"){
                    self::$status = "edit";
                }else{
                    self::$status = "endAnswering";
                }
            }
        }
    }    

    # get the question information from course database
    public function getQuestionInfo(){                
        $data1 = self::$currentQuestion;
        # get answers in testi questions
        $data1['type'] = $data1['exercise_type'];
        if($data1['exercise_type'] == UNIQUE_ANSWER){
            $data1['answers'] = unserialize(base64_decode($data1['question_options']));
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

    static function saveAll(){
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

    static function endQuiz(){
        if(isset($_POST['exercise_answer_id'])){            
            $exercise_answer_id = $_POST['exercise_answer_id'];
            $currentDate = JalaliDate::jdate('Y-m-d H:i:s');

            $query = "UPDATE `dokeos_exercises`.`exercise_answer` 
                        SET `status` = '1', 
                            `end_date` = '$currentDate'
                        WHERE `id` = '$exercise_answer_id'";
            $result = api_sql_query($query, __FILE__, __LINE__);
            $saveNumber = mysql_affected_rows();
            
            echo $saveNumber;
        }
    }

    static function getAllQuestion(){
        // self::getQuizQuestions();
        $query = "SELECT * FROM `dokeos_exercises`.`exercise_answer` 
                        WHERE `semester` = '".self::$semester."' 
                        AND `year` = '".self::$year."'
                        AND `user_id` = '".self::$userId."'
                        AND `exercise_id` = '".self::$quizId."'
                        AND `code` = '".self::$code."'";

        $result = api_sql_query($query, __FILE__, __LINE__);
        $data   = mysql_fetch_assoc($result);

        self::getEditedQuestion($data['id']);

        if($data['review_deadline_time'] < 1){
            if(self::$questions[0]['exercise_type'] == UNIQUE_ANSWER){
                self::$editTime = EDIT_TIME_TESTI;
            }else{
                self::$editTime = EDIT_TIME_DESCRIPTIVE;
            }            
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

    static function getEditedQuestion($exercise_answer_id){
        $query = "SELECT * FROM `dokeos_exercises`.`exercise_answer_items` 
                        WHERE `user_id` = '".self::$userId."'
                        AND `exercise_answer_id` = '$exercise_answer_id'
                        ORDER BY `order`";        

        $result = api_sql_query($query, __FILE__, __LINE__);
        $questions = array();
        while($data = mysql_fetch_assoc($result)){
            $data['options'] = self::decode($data['question_options']);
            $data['type'] = $data['exercise_type'];
            $questions[] = $data;
        }
        self::$questions = $questions;
    }

    static function queryData($sql){
        $result = api_sql_query($sql, __FILE__, __LINE__);        
        $data = mysql_fetch_assoc($result);

        return $data;
    }
    
    static function encode($array){
        // json_encode($array, JSON_UNESCAPED_UNICODE);
        return base64_encode(serialize($array));
    }

    static function decode($str){
        return unserialize(base64_decode($str));
    }

    static function randomize($array){
        shuffle($array);        
        return $array;
    }

    static function tablesConfig($TBL_QUIZ, $TBL_EXERCICE_QUESTION, $TBL_EXERCICES, $TBL_REPONSES){
        self::$TBL_QUIZ = $TBL_QUIZ;
        self::$TBL_EXERCICE_QUESTION = $TBL_EXERCICE_QUESTION;
        self::$TBL_EXERCICES = $TBL_EXERCICES;
        self::$TBL_REPONSES = $TBL_REPONSES;
    }

    // static function strip_word_html($text, $allowed_tags = '<b><i><sup><sub><em><strong><u><br><p><div><img>')
    static function strip_word_html($text, $allowed_tags = '<b><i><sup><sub><em><p><div><img>')
    {
        mb_regex_encoding('UTF-8');
        //replace MS special characters first
        $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
        $replace = array('\'', '\'', '"', '"', '-');
        $text = preg_replace($search, $replace, $text);
        //make sure _all_ html entities are converted to the plain ascii equivalents - it appears
        //in some MS headers, some html entities are encoded and some aren't
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        //try to strip out any C style comments first, since these, embedded in html comments, seem to
        //prevent strip_tags from removing html comments (MS Word introduced combination)
        if(mb_stripos($text, '/*') !== FALSE){
            $text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
        }
        //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be
        //'<1' becomes '< 1'(note: somewhat application specific)
        $text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text);
        $text = strip_tags($text, $allowed_tags);
        //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one
        $text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text);
        //strip out inline css and simplify style tags
        $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
        $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
        $text = preg_replace($search, $replace, $text);
        //on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears
        //that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains
        //some MS Style Definitions - this last bit gets rid of any leftover comments */
        $num_matches = preg_match_all("/\<!--/u", $text, $matches);
        if($num_matches){
              $text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
        }
        return $text;
    }
}