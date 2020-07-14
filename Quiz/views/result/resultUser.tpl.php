<link rel="stylesheet" href="/main/css/MDB/css/bootstrap.min.css">
<link rel="stylesheet" href="/main/css/MDB/font/sahel/style.css">
<link rel="stylesheet" href="/main/exercice/Quiz/views/assets/style.css">

<link href="/main/exercice/Quiz/views/assets/MD.BootstrapPersianDateTimePicker-1.6.4/Content/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="/main/exercice/Quiz/views/assets/MD.BootstrapPersianDateTimePicker-1.6.4/Content/bootstrap-theme.min.css" />
<link rel="stylesheet" href="/main/exercice/Quiz/views/assets/MD.BootstrapPersianDateTimePicker-1.6.4/Content/MdBootstrapPersianDateTimePicker/jquery.Bootstrap-PersianDateTimePicker.css" />

<script src="/main/exercice/Quiz/views/assets/MD.BootstrapPersianDateTimePicker-1.6.4/Scripts/jquery-2.1.4.js" type="text/javascript"></script>
<script src="/main/exercice/Quiz/views/assets/MD.BootstrapPersianDateTimePicker-1.6.4/Scripts/bootstrap.min.js" type="text/javascript"></script>

<style>
.form-control {
	display: block;
	width: 100%;
	/* height: calc(1.5em + 0.75rem + 2px); */
	padding: .375rem .75rem;
	font-size: 1.4rem;
	font-weight: 400;
	line-height: 1.5;
	color: #495057;
	background-color: #fff !important;
	background-clip: padding-box;
	border: 1px solid #ced4da !important;
	border-radius: .25rem !important;
	transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out !important;
    text-align: left;
}
h5{
    font-weight: bold;
    vertical-align: top;
    margin: 10px 5px;
}
</style>
<?php
// print_r($totalInfo);
?>
<form method="POST" id="QuestionForm" action="#gridForm">
    <div class="row">
        <div class="col-md-12" style="position: relative" id="gridForm">
            <div class = "card">
                <div class="card-header">
                    <div class="md-form col-md-10" style="float: right">
                        <div class="row">
                            <div class="col-md-2 right">
                                <img src="data:image/jpeg;base64,<?=base64_encode($totalInfo['photo']) ?>" class="avatar">
                            </div>
                            <div class="col-md-5 right info">
                                <h5> <?=$totalInfo['firstname'].' '.$totalInfo['lastname'] ?> </h5>
                                <h5> <?=$totalInfo['username'] ?> </h5>
                                <div><span><?=$totalInfo['ip'] ?></span></div>
                            </div>
                            <div class="col-md-5 right info">
                                <div>تاریخ: <span> <?=QuizResult::justDate($totalInfo['start_date']) ?> </span> </div>
                                <div>شروع آزمون:<span> <?=QuizResult::justTime($totalInfo['start_date']) ?> </span></div>
                                <div>پایان آزمون:<span> <?=QuizResult::justTime($totalInfo['end_date']) ?> </span></div>
                                <div>وضعیت: </div>
                                <div>امتیاز: <span id="score"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="md-form col-md-2" style="text-align: left">
                        <input type="submit" value="تازه سازی صفحه" class="btn btn-warning" />
                    </div>
                </div>
                <div class="card-body">
                <?php
                    $i = 0;
                    $score = 0;
                    foreach($responses as $question) { ?>
                        <div class="question font-dark">سوال<?=++$i ?>: <?=$question['question'] ?></div>
                        <?php if($question['exercise_type'] == "5"){ ?>
                            <div class="font-light"><?=$question['answer_text'] ?> </div>
                        <?php }elseif($question['exercise_type'] == "1"){ 
                                $questions = Quiz::decode($question['question_options']);
                                
                                foreach($questions as $answer){
                                    $img = "<img class='check-img' src='/main/exercice/Quiz/views/assets/svg/blank-square.svg' />";
                                    if($question['answer_option'] == $answer['id']){
                                        if($answer['correct'] == 1){
                                            $score++;
                                            $bg = "bg-green";
                                        }
                                        else{
                                            $bg = "bg-orange";
                                        }
                                        
                                        $img = "<img class='check-img $bg' src='/main/exercice/Quiz/views/assets/svg/check-box.svg' />";
                                    }

                                    $class = "";
                                    if($answer['correct'] == 1){
                                        $class = "badge badge-primary";
                                    }

                                    echo '
                                        <div class="form-check">
                                            <div class="check-div">
                                                '.$img.'
                                            </div>
                                            <div class="answer-div '.$class.'" >'.$answer['answer'].'</div>
                                        </div> 
                                    ';
                                }
                        ?>
                            <div class="font-light"><?=$question['answer_text'] ?> </div>
                        <?php } ?>
                        <br/>
                <?php } ?>
                <script>
                    $('#score').html('<?=$score ?>');
                </script>
                </div>
            </div>

        </div>
    </div>
</form>

<form method="GET" id="NextQuestionForm" action="">
    <input type="hidden" name="cidReq" value="<?=$_GET['cidReq']; ?>" />
    <input type="hidden" name="exerciseId" value="<?=$_GET['exerciseId']; ?>" />
</form>

<script type="text/javascript">
    $('#input1').change(function() {
        var $this = $(this),
            value = $this.val();
        alert(value);
    });
    $('#textbox1').change(function () {
        var $this = $(this),
            value = $this.val();
        alert(value);
    });
</script>
<script src="/main/exercice/Quiz/views/assets/MD.BootstrapPersianDateTimePicker-1.6.4/Scripts/MdBootstrapPersianDateTimePicker/calendar.js" type="text/javascript"></script>
<script src="/main/exercice/Quiz/views/assets/MD.BootstrapPersianDateTimePicker-1.6.4/Scripts/MdBootstrapPersianDateTimePicker/jquery.Bootstrap-PersianDateTimePicker.js" type="text/javascript"></script>

