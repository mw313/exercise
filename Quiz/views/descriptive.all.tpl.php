<link rel="stylesheet" href="/main/css/MDB/css/bootstrap.min.css">
<link rel="stylesheet" href="/main/css/MDB/font/iconfont/material-icons.css">
<link rel="stylesheet" href="/main/css/MDB/css/mdb.min.css">
<link rel="stylesheet" href="/main/css/MDB/alert/css/jquery-confirm.css">
<link rel="stylesheet" href="/main/css/MDB/font/sahel/style.css">
<link rel="stylesheet" href="/main/exercice/Quiz/views/assets/styleNew.css">

<script src="/main/css/MDB/js/jquery-3.2.1.min.js" type="text/javascript"></script>

<?php
    // print_r(Quiz::$questions);
?>

<form method="POST" id="QuestionForm" action="">
<div class="row">
    <div class="col-md-12" style="position: relative">
        <div class = "card">
            <div class="card-header">
                بازبینی پاسخ ها
                <div class="deadline" data-type="<?=$question['type']; ?>" style="display: none;">
                    زمان باقی مانده: <span><?=$quiz['review_deadline_time']; ?></span>
                </div>
            </div>            
            <div class="card-body">
                <input type="hidden" name="exercise_answer_id" id="exercise_answer_id" value="<?=Quiz::$questions[0]['exercise_answer_id']; ?>" />
                <input type="hidden" name="answer_type" id="answer_type" value="<?=Quiz::$questions[0]['type']; ?>" />
                <?php 
                    $i = 0;
                    foreach(Quiz::$questions as $question) { ?>
                        <div class="question"> سوال<?=++$i ?>: <?=$question['question'] ?> </div>
                        <input type="hidden"  id="id_<?=$i ?>" value="<?=$question['id'] ?>" />
                        <textarea class="editor" id="answer_text_<?=$i ?>" name="answer_text_<?=$i ?>"><?=$question['answer_text'] ?></textarea>
                        <br/>
                <?php } ?>
                <input type="hidden" name="count" id="count" value="<?=$i ?>" />
            </div>
            <div class="card-footer text-left">
                <button type="button" id="saveItems" onclick="saveAll()" class="btn btn-cyan"> ویرایش پاسخ ها </button>
                <button type="button" id="next" onclick="endQuiz()" class="btn btn-deep-orange" style="display: none"> اتمام آزمون </button>
            </div>
        </div>

        <div class="loading-container" style="position: fixed; padding-top: 15%;">
            <div class="lds-ellipsis">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="loading-text"> در حال ذخیره کردن پاسخ ها </div>
        </div>
    </div>
</div>
</form>

<form method="GET" id="NextQuestionForm" action="">
    <input type="hidden" name="cidReq" value="<?=$_GET['cidReq']; ?>" />
    <input type="hidden" name="exerciseId" value="<?=$_GET['exerciseId']; ?>" />
</form>

<script src="/main/css/MDB/js/popper.min.js" type="text/javascript"></script>
<!-- <script src="/main/css/MDB/js/bootstrap.min.js" type="text/javascript"></script> -->
<!-- <script src="/main/css/MDB/js/mdb.min.js" type="text/javascript"></script> -->
<script src="/main/css/MDB/alert/js/jquery-confirm.js" type="text/javascript"></script>
<script src="/main/inc/lib/tinymce_5.2.1/tinymce/js/tinymce/tinymce.min.js" type="text/javascript"></script>
<script src="/main/exercice/Quiz/views/assets/functionsNew.js" type="text/javascript"></script>
<script>
    $(function(){
        // initTinyMCE('.editor');
        editFormTiming();
    });
</script>