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
*	File containing the UNIQUE_ANSWER class.
*	@package dokeos.exercise
* 	@author Eric Marguin
* 	@version $Id: admin.php 10680 2007-01-11 21:26:23Z pcool $
*/


if(!class_exists('UniqueAnswer')):

/**
	CLASS UNIQUE_ANSWER
 *
 *	This class allows to instantiate an object of type UNIQUE_ANSWER (MULTIPLE CHOICE, UNIQUE ANSWER),
 *	extending the class question
 *
 *	@author Eric Marguin
 *	@package dokeos.exercise
 **/

class UniqueAnswer extends Question {

	static $typePicture = 'mcua.gif';
	static $explanationLangVar = 'UniqueSelect';

	/**
	 * Constructor
	 */
	function UniqueAnswer(){
		$this -> type = UNIQUE_ANSWER;
	}

	/**
	 * function which redifines Question::createAnswersForm
	 * @param the formvalidator instance
	 * @param the answers number to display
	 */
	function createAnswersForm ($form , $i = "no") 
    {
        
        $suffix = "";
        if($i!="no")
        $suffix = "_".$i;

		global $fck_attribute;

		$fck_attribute = array();
		$fck_attribute['Width'] = '348px';
		$fck_attribute['Height'] = '100px';
		$fck_attribute['ToolbarSet'] = 'Test';
		// $fck_attribute['Config']['IMUploadPath'] = 'upload/test/';
		// $fck_attribute['Config']['FlashUploadPath'] = 'upload/test/';

		$nb_answers = isset($_POST['nb_answers']) ? $_POST['nb_answers'] : 4;
		$nb_answers += (isset($_POST['lessAnswers']) ? -1 : (isset($_POST['moreAnswers']) ? 1 : 0));

		$html='
		<div class="row">
			<div class="label">
			'.get_lang('Answers').'
			</div>
			<div class="formw">
				<table class="data_table">
					<tr style="text-align: center;">
						<th>
							'.get_lang('Number').'
						</th>
						<th>
							'.get_lang('True').'
						</th>
						<th>
							'.get_lang('Answer').'
						</th>
						<!--<th>
							'.get_lang('Comment').'
						</th>-->
						<th>
							'.get_lang('Weighting').'
						</th>
						
					</tr>';
		$form -> addElement ('html', $html);

		$defaults = array();
		$correct = 0;
		if(!empty($this -> id))
		{
			$answer = new Answer($this -> id);
			$answer -> read();
			if(count($answer->nbrAnswers)>0 && !$form->isSubmitted())
			{
				$nb_answers = $answer->nbrAnswers;
			}
		}

		$form -> addElement('hidden', 'nb_answers'.$suffix , $nb_answers);

		for($i = 1 ; $i <= $nb_answers ; ++$i)
		{
			$form -> addElement ('html', '<tr>');
			if(is_object($answer))
			{
				if($answer -> correct[$i])
				{
					$correct = $i;
				}
				$defaults['answer['.$i.']'] = $answer -> answer[$i];
				$defaults['comment['.$i.']'] = $answer -> comment[$i];
				$defaults['weighting['.$i.']'] = $answer -> weighting[$i];
			}
			
			$renderer = & $form->defaultRenderer();
			$renderer->setElementTemplate('<td align="center"><!-- BEGIN error --><span class="form_error">{error}</span><!-- END error --><br/>{element}</td>');
			
			$answer_number=$form->addElement('text', null,null,'value="'.$i.'"');
			$answer_number->freeze();
			
			$form->addElement('radio', 'correct'.$suffix, null, null, $i, 'style="margin-left: 0em;"');
			if($_GET['type'] == "editor")
				$form->addElement('html_editor', 'answer['.$i.']'.$suffix,null, 'style="vertical-align:middle"');
			else
            	$form->addElement('textarea', 'answer'.$suffix.'['.$i.']', null, 'cols="40" style="margin:2px auto; display:inline-block;"');
            $form -> setDefaults(array('answer'.$suffix.'['.$i.']' => "پاسخ".$i));
            
			//$form->addRule('answer'.$suffix.'['.$i.']', get_lang('ThisFieldIsRequired'), 'required');
			//$form->addElement('html_editor', 'comment['.$i.']'.$suffix,null, 'style="vertical-align:middle"');
			$form->addElement('text', 'weighting'.$suffix.'['.$i.']',null, 'style="vertical-align:middle;margin-left: 0em;" size="5" value="0"');
			$form -> addElement ('html', '</tr>');
		}
		$form -> addElement ('html', '</table>');

		if($i == "no")
        {
            $form->addElement('submit', 'lessAnswers', get_lang('LessAnswer'));
        	$form->addElement('submit', 'moreAnswers', get_lang('PlusAnswer'));
        	$renderer->setElementTemplate('{element}&nbsp;','lessAnswers');
        	$renderer->setElementTemplate('{element}','moreAnswers');
        }
        
		$form -> addElement ('html', '</div></div></div>');
		
		//We check the first radio button to be sure a radio button will be check 
		if($correct==0){
			$correct=1;
		}
		$defaults['correct'] = $correct;
		$form -> setDefaults($defaults);

		$form->setConstants(array('nb_answers' => $nb_answers));

	}


	/**
	 * abstract function which creates the form to create / edit the answers of the question
	 * @param the formvalidator instance
	 * @param the answers number to display
	 */
	function processAnswersCreation($form) 
    {

		if ($_GET['newQuestion'] == "yes")
        return;
        
        $questionWeighting = $nbrGoodAnswers = 0;

		$correct = $form -> getSubmitValue('correct');
		$objAnswer = new Answer($this->id);
		$nb_answers = $form -> getSubmitValue('nb_answers');

		for($i=1 ; $i <= $nb_answers ; $i++)
        {
        	$answer = trim($form -> getSubmitValue('answer['.$i.']'));
            $comment = trim($form -> getSubmitValue('comment['.$i.']'));
            $weighting = trim($form -> getSubmitValue('weighting['.$i.']'));

        	$goodAnswer= ($correct == $i) ? true : false;

        	if($goodAnswer)
        	{
        		$nbrGoodAnswers++;
        		$weighting = abs($weighting);
        		if($weighting > 0)
                {
                    $questionWeighting += $weighting;
                }
        	}

        	$objAnswer -> createAnswer($answer,$goodAnswer,$comment,$weighting,$i);

        }

    	// saves the answers into the data base
        $objAnswer -> save();

        // sets the total weighting of the question
        $this -> updateWeighting($questionWeighting);
        $this -> save();
	}
    
    function MyProcessAnswersCreation($form , $j) 
    {
        $type = $_POST['answerType'];
        if($_GET['newQuestion'] == "yes" && $type == 1)
        {
            $n = Unique_Answer_Question_Count;
            
            $questionWeighting = $nbrGoodAnswers = 0;
            $suffix = "_".$j;
            $correct = $form -> getSubmitValue('correct'.$suffix);
    		$objAnswer = new Answer($this->id);
    		$nb_answers = $form -> getSubmitValue('nb_answers'.$suffix);
            # echo "Total Numbers: ".$nb_answers."<br/>";
            # echo "correct: ".$correct."<br/>";
    
    		for($i=1 ; $i <= $nb_answers ; $i++)
            {
            	$answer = trim($form -> getSubmitValue('answer'.$suffix.'['.$i.']'));
                # $answer = trim($_REQUEST['answer['.$i.']']);
                $comment   = trim($form -> getSubmitValue('comment'.$suffix.'['.$i.']'));
                $weighting = trim($form -> getSubmitValue('weighting'.$suffix.'['.$i.']'));
                # $weighting = $_REQUEST['weighting['.$i.']'.$suffix];
                # $weighting = trim($_POST['weighting['.$i.']'.$suffix]);
                # print_r($_REQUEST);
    
            	$goodAnswer = ($correct == $i) ? true : false;
    
            	if($goodAnswer)
            	{
            		$nbrGoodAnswers++;
            		$weighting = abs($weighting);
            		if($weighting > 0)
                    {
                        $questionWeighting += $weighting;
                    }
            	}
                # echo "Info: answer[$i]$suffix is ".$answer." & "." & ".$weighting."<br/>";
    
            	$objAnswer -> createAnswer($answer,$goodAnswer,$comment,$weighting,$i);
            }
        
        	# saves the answers into the data base
            $objAnswer -> save();
    
            # sets the total weighting of the question
            $this -> updateWeighting($questionWeighting);
            $this -> save();
            # exit();                            
        }
    }
}
endif;
?>