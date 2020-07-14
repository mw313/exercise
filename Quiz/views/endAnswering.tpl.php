<link rel="stylesheet" href="/main/css/MDB/css/bootstrap.min.css">
<link rel="stylesheet" href="/main/css/MDB/font/iconfont/material-icons.css">
<link rel="stylesheet" href="/main/css/MDB/css/mdb.min.css">
<link rel="stylesheet" href="/main/css/MDB/alert/css/jquery-confirm.css">
<link rel="stylesheet" href="/main/css/MDB/font/sahel/style.css">
<link rel="stylesheet" href="/main/exercice/Quiz/views/assets/style.css">

<div class="row">
    <div class="col-md-12" style="position: relative">
        <div class = "card">
            <div class="card-header">
                پایان سوالات
            </div>
            <div class="card-body">
                دانشجوی محترم «<?=$user ?>» <br/>
                خسته نباشید <br/>
                با کلیک بر روی دکمه ی ادامه به شما فرصت داده می شود تا پاسخ های خود را ویرایش نمایید. <br/>
                بعد از اتمام ویرایش بر روی دکمه ی تایید کلیک نمایید تا پاسخ های شما ثبت نهایی گردد. <br/>
                در صورت پایان یافتن فرصت ویرایش، پاسخ های شما به صورت خودکار تایید می گردد.             
            </div>
            <div class="card-footer" style="text-align: left">
                <form method="GET" id="NextQuestionForm" action="">
                    <input type="hidden" name="exerciseId" value="<?=$_GET['exerciseId']; ?>" />
                    <input type="hidden" name="cidReq" value="<?=$_GET['cidReq']; ?>" />
                    <input type="hidden" name="status" value="edit" />
                    <button type="button" id="save" onclick="nextAnswer()" class="btn btn-cyan next-btn"> ادامه </button>
                </form>
            </div>
        </div>                

    </div>
</div>

<script src="/main/css/MDB/js/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="/main/css/MDB/alert/js/jquery-confirm.js" type="text/javascript"></script>
<script src="/main/exercice/Quiz/views/assets/functionsNew.js" type="text/javascript"></script>