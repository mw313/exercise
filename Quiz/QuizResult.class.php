<?php
ini_set('display_errors', 1);
error_reporting(E_ERROR);
class QuizResult{
    static public function display(){
        Quiz::init();

        // Quiz::quizManage();
        // Quiz::getCurrentQuizQuestion();
        $route = $_GET['act'];

        switch($route){
            case "courseResult":
                self::displayCourse();
                return;
            case "userResult":
                self::displayUserResponse();
                return;
            case "endExam":
            default:
                self::displayDate();
        }
    }

    static public function displayDate(){
        $courses = self::getCoursesResultByDate();
        self::view("views/result/resultDate.tpl.php", compact('courses'));
    }

    static public function displayCourse(){
        $courses = self::getCoursesUsersResult();
        $code = $_REQUEST['code'];
        self::view("views/result/resultCourse.tpl.php", compact('courses', 'code'));
    }

    static public function displayUserResponse(){
        $info = self::getUserResponse();
        $code = $_REQUEST['code'];
        $responses = $info['responses'];
        $totalInfo = $info['totalInfo'];
        self::view("views/result/resultUser.tpl.php", compact('responses', 'totalInfo'));
    }

    static public function getCoursesResultByDate(){
        $date = str_replace("/", "-", $_POST['date']);
        $time = $_POST['time'];
        switch($time){
            case "00:00:00":
                $endStart = "23:59:59";
            break;
            case "07:50:00":
                $endStart = "09:50:00";
            break;
            case "09:50:00":
                $endStart = "11:50:00";
            break;
            case "11:50:00":
                $endStart = "13:50:00";
            break;
            case "13:50:00":
                $endStart = "15:50:00";
            break;
        }

        $query = "SELECT *,
                            count(*) as `number`,
                            sum(if(`review_deadline_time` > '0', 1, 0)) AS `endNumber`,
                            min(`start_date`) AS `firstDate`,
                            max(`start_date`) AS `lastDate`
                        FROM `dokeos_exercises`.`exercise_answer`
                        WHERE `start_date` > '$date $time' AND `start_date` < '$date $endStart'
                        GROUP BY `code`, `exercise_type`
                        ORDER BY `id`";
        // echo $query."<br/>";
        $result = api_sql_query($query, __FILE__, __LINE__);
        $courses = array();
        while($data = mysql_fetch_assoc($result)){
            $data['firstDate'] = self::justTime($data['firstDate']);
            $data['lastDate'] = self::justTime($data['lastDate']);
            $courses[] = $data;
        }

        return $courses;
    }

    static public function getCoursesUsersResult(){
        $code = $_REQUEST['code'];
        $exericse_id = $_REQUEST['exercise_id'];
        $date = str_replace("/", "-", $_POST['date']);

        $query = "SELECT `user`.`firstname`, `user`.`lastname`, `user`.`username`, `user`.`firstname`, `exercise_answer`.*
                        FROM `dokeos_exercises`.`exercise_answer`
                             join `dokeos_main`.`user` using(`user_id`)
                        WHERE 
                            `code` = '$code' AND
                            `exercise_id` = '$exericse_id'
                            /* AND `start_date` > '$date 00:00:00' AND `start_date` < '$date 23:59:59' */
                        ORDER BY `id`";
        
        $result = api_sql_query($query, __FILE__, __LINE__);
        $courses = array();
        $excIds = array();
        while($data = mysql_fetch_assoc($result)){
            $data['start_date'] = self::justTime($data['start_date']);
            $data['end_date'] = self::justTime($data['end_date']);
            $excIds[] = $data['id'];
            $courses[] = $data;
        }

        $excIds = implode(',', $excIds);
        $query2 = "SELECT 
                            sum(if(`deadline_time` > 0, 1, 0)) AS `answering`, 
                            count(*) AS `total`, 
                            `exercise_answer_id`
                        FROM `dokeos_exercises`.`exercise_answer_items`
                        WHERE 
                            `exercise_answer_id` IN ($excIds)
                        GROUP BY `exercise_answer_id`";
        $result2 = api_sql_query($query2, __FILE__, __LINE__);
        $count = array();
        $total = array();
        while($data2 = mysql_fetch_assoc($result2)){
            $count[$data2['exercise_answer_id']] = $data2['answering'];
            $total[$data2['exercise_answer_id']] = $data2['total'];
        }

        foreach($courses as $key=>$course){
            $courses[$key]['answering'] = $count[$course['id']];
            $courses[$key]['total'] = $total[$course['id']];
        }

        return $courses;
    }

    static public function getUserResponse(){
        $exercise_answer_id = $_REQUEST['exercise_answer_id'];

        $query = "SELECT *
                        FROM `dokeos_exercises`.`exercise_answer_items`
                        WHERE
                            `exercise_answer_id` = '$exercise_answer_id'
                        ORDER BY `id`";
        
        $result = api_sql_query($query, __FILE__, __LINE__);
        $responses = array();
        while($data = mysql_fetch_assoc($result)){
            $data['start_date'] = self::justTime($data['start_date']);
            $data['end_date'] = self::justTime($data['end_date']);
            $responses[] = $data;
        }

        $query = "SELECT `user`.`firstname`, `user`.`lastname`, `user`.`username`, `user`.`firstname`, `exercise_answer`.*
                        FROM `dokeos_exercises`.`exercise_answer`
                             join `dokeos_main`.`user` using(`user_id`)
                        WHERE 
                            `id` = '$exercise_answer_id'
                        ORDER BY `id`";
        
        $result = api_sql_query($query, __FILE__, __LINE__);
        $totalInfo = mysql_fetch_assoc($result);

        $query = "SELECT `PersonID` FROM `educ_ikvu`.`studentspecs` WHERE `StNo` = '$totalInfo[username]'";
        $result = api_sql_query($query, __FILE__, __LINE__);
        $personInfo = mysql_fetch_assoc($result);        

        $query = "SELECT * FROM `photo_ikvu`.`photos` WHERE `PersonID` = '$personInfo[PersonID]'";
        $result = api_sql_query($query, __FILE__, __LINE__);
        $photoInfo = mysql_fetch_assoc($result);
        $totalInfo['photo'] = $photoInfo['photo'];
        
        return array('responses'=>$responses, 'totalInfo'=>$totalInfo);
    }

    static public function justTime($dateTime){
        $str = explode(" ", $dateTime);
        return $str[1];
    }

    static public function justDate($dateTime){
        $str = explode(" ", $dateTime);
        return str_replace("-", "/", $str[0]);
    }

    static public function view($path, $variables){
        ob_start();
            foreach($variables as $key=>$val){
                $$key = $val;
            }
            include_once($path);
        $result = ob_get_clean();

        echo $result;
    }

}