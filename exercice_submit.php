<?php

// ini_set('display_errors', 1);
// error_reporting(E_ERROR);

include_once('Quiz/config.php');
include_once('Quiz/Quiz.class.php');
include_once('Quiz/QuizDisplay.class.php');
include_once('Quiz/JalaliDate.class.php');


define('UNIQUE_ANSWER',		1);
define('MULTIPLE_ANSWER',	2);
define('FILL_IN_BLANKS',	3);
define('MATCHING',			4);
define('FREE_ANSWER', 		5);
define('HOT_SPOT', 			6);
define('HOT_SPOT_ORDER', 	7);

$language_file='exercice';

include_once('../inc/global.inc.php');
api_protect_course_script(true);

$TBL_EXERCICE_QUESTION = Database::get_course_table(TABLE_QUIZ_TEST_QUESTION);
$TBL_QUIZ              = Database::get_course_table(TABLE_QUIZ_TEST);
$TBL_EXERCICES         = Database::get_course_table(TABLE_QUIZ_QUESTION);
$TBL_REPONSES          = Database::get_course_table(TABLE_QUIZ_ANSWER);

Quiz::tablesConfig($TBL_QUIZ, $TBL_EXERCICE_QUESTION, $TBL_EXERCICES, $TBL_REPONSES);


Display::display_header();

QuizDisplay::displayQuestion();

Display::display_footer();


?>