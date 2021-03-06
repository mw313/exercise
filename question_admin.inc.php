<?php
/*
    DOKEOS - elearning and course management software

    For a full list of contributors, see documentation/credits.html

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.
    See "documentation/licence.html" more details.

    Contact:
		Dokeos
		Rue des Palais 44 Paleizenstraat
		B-1030 Brussels - Belgium
		Tel. +32 (2) 211 34 56
*/


/**
*	Statement (?) administration
*	This script allows to manage the statements of questions.
* 	It is included from the script admin.php
*	@package dokeos.exercise
* 	@author Olivier Brouckaert
* 	@version $Id: question_admin.inc.php 13311 2007-09-27 08:03:12Z elixir_inter $
*/

/*
==============================================================================
		INIT SECTION
==============================================================================
*/


include_once(api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php');
include_once(api_get_path(LIBRARY_PATH).'image.lib.php');

// ALLOWED_TO_INCLUDE is defined in admin.php
if(!defined('ALLOWED_TO_INCLUDE'))
{
	exit();
}


/*********************
 * INIT QUESTION
 *********************/
if(isset($_GET['editQuestion']))
{
	$objQuestion = Question::read ($_GET['editQuestion']);
	$action = api_get_self()."?modifyQuestion=".$modifyQuestion."&editQuestion=".$objQuestion->id;
}
else
{
	$objQuestion = Question :: getInstance($_REQUEST['answerType']);
	$action = api_get_self()."?modifyQuestion=".$modifyQuestion."&newQuestion=".$newQuestion;
}

if(is_object($objQuestion))
{

	/*********************
	 * FORM STYLES
	 *********************/
	 // if you have a better way to improve the display, please inform me e.marguin@elixir-interactive.com
	$styles = '
	<style>
	div.row div.label{
		width: 10%;
	}
	div.row div.formw{
		width: 85%;
	}
	</style>
	';
	echo $styles;


	/*********************
	 * INIT FORM
	 *********************/
	$form = new FormValidator('question_admin_form','post',$action);


	/*********************
	 * FORM CREATION
	 *********************/
    # سوال چند گزینه ای
    if($_GET['answerType'] == 1 && $_GET['newQuestion'] == "yes")
    {
        $n = Unique_Answer_Question_Count;
        for($i=1; $i <= $n; $i++)
        {
           $objQuestion -> createForm ($form , $i);
    	   $objQuestion -> createAnswersForm ($form , $i);
        }
    }
    # سوال تشریحی
    elseif($_GET['answerType'] == 5 && $_GET['newQuestion'] == "yes")
    {
        $n = Free_Answer_Question_Count;
        for($i=1; $i <= $n; $i++)
        {
           $objQuestion -> createForm ($form , $i);
    	   $objQuestion -> createAnswersForm ($form , $i);
        }
    }
    else
    {
        $objQuestion -> createForm ($form);
	    $objQuestion -> createAnswersForm ($form);
    }

	//$form->addElement('submit','submitQuestion',get_lang('qustionInsert'),array("onclick"=>"alert(111)"));
    $form->addElement('submit','submitQuestion',get_lang('qustionInsert'));
    
	$renderer = $form->defaultRenderer();
	$renderer->setElementTemplate('<div class="row"><div class="label">{label}</div><div class="submitDiv">{element}</div></div>','submitQuestion');


	/**********************
	 * FORM VALIDATION
	 **********************/
	if(isset($_POST['submitQuestion']) && $form->validate())
	{
		# question
	    $objQuestion -> processCreation($form,$objExercise);
	    # answers
        # For Edit This code is necessary
	    $objQuestion -> processAnswersCreation($form,$nb_answers);
        # print_r($_SESSION);
        
	    # redirect
        $exerciseId = $objQuestion->exerciseId;
	    if($objQuestion -> type != HOT_SPOT)
	    	echo '<script type="text/javascript">window.location.href="admin.php?exerciseId='.$exerciseId.'#exerciseId'.$exerciseId.'&cidReq='.$_course['official_code'].'"</script>';
	    else
	    	echo '<script type="text/javascript">window.location.href="admin.php?hotspotadmin='.$objQuestion->id.'"</script>';
	}
	else
	{

		/******************
		 * FORM DISPLAY
		 ******************/
		echo '<h3>'.$questionName.'</h3>';


		if(!empty($pictureName)){
			echo '<img src="../document/download.php?doc_url=%2Fimages%2F'.$pictureName.'" border="0">';
		}

		if(!empty($msgErr))
		{
			Display::display_normal_message($msgErr); //main API
		}


		// display the form
		$form->display();
	}
}
?>