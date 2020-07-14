<link rel="stylesheet" href="/main/css/MDB/css/bootstrap.min.css">
<link rel="stylesheet" href="/main/css/MDB/font/iconfont/material-icons.css">
<link rel="stylesheet" href="/main/css/MDB/css/mdb.min.css">
<link rel="stylesheet" href="/main/css/MDB/alert/css/jquery-confirm.css">
<link rel="stylesheet" href="/main/css/MDB/font/sahel/style.css">
<link rel="stylesheet" href="/main/exercice/Quiz/views/assets/styleNew.css">

<form method="POST" id="QuestionForm" action="">
<div class="row">
    <div class="col-md-12" style="position: relative">
        <div class = "card">
            <div class="card-header">
                سوال: <?=$question['order']; ?>
                <div class="deadline" data-type="<?=$question['type']; ?>" style="display: none;">
                    زمان باقی مانده: <span><?=$question['deadline']; ?></span>
                </div>
            </div>
            <div class="card-body">
                <?=$question['question']; ?>
            </div>
        </div>
        <div class = "card">
            <div class="card-body">
                <input type="hidden" name="answer_id" id="answer_id" value="<?=$question['answer_id']; ?>" />
                <textarea class="editor" id="answer_text" name="answer_text" rows="10"></textarea>
            </div>
            <div class="card-footer text-left">
                <button type="button" id="save" onclick="saveAnswer()" class="btn btn-cyan"> ثبت پاسخ </button>
                <button type="button" id="next" onclick="nextAnswer()" class="btn btn-deep-orange" style="display: none"> سوال بعد </button>
            </div>
        </div>        

        <div class="loading-container">
            <div class="lds-ellipsis">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="loading-text"> در حال ذخیره کردن پاسخ </div>
        </div>

    </div>
</div>
</form>

<form method="GET" id="NextQuestionForm" action="">
    <input type="hidden" name="cidReq" value="<?=$_GET['cidReq']; ?>" />
    <input type="hidden" name="exerciseId" value="<?=$_GET['exerciseId']; ?>" />
</form>

<script src="/main/css/MDB/js/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="/main/css/MDB/js/popper.min.js" type="text/javascript"></script>
<!-- <script src="/main/css/MDB/js/bootstrap.min.js" type="text/javascript"></script> -->
<!-- <script src="/main/css/MDB/js/mdb.min.js" type="text/javascript"></script> -->
<script src="/main/css/MDB/alert/js/jquery-confirm.js" type="text/javascript"></script>
<script src="/main/inc/lib/tinymce_5.2.1/tinymce/js/tinymce/tinymce.min.js" type="text/javascript"></script>
<script src="/main/exercice/Quiz/views/assets/functionsNew.js" type="text/javascript"></script>
<script>
    $(function(){
        // initTinyMCE('#answer_text');
        timing();
    });
</script>