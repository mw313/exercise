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
                شیوه برگزاری آزمون آنلاین
            </div>
            <div class="card-body">
                <center> به نام خدا </center>
                دانشجوی محترم «<?=$user ?>» <br/>
                با سلام <br/>
                به اطلاع می رساند که مهلت پاسخگویی هر سوال تستی، <?=(TESTI_TIME) ?> ثانیه می باشد. <br/>
                با پایان یافتن مهلت پاسخگویی هر سوال، پاسخ شما به صورت خودکار ذخیره می گردد. <br/>
                در انتها جهت ویرایش پاسخ ها <?=(EDIT_TIME_TESTI/60) ?> دقیقه فرصت وجود دارد. <br/>
                لطفا جهت پیشگیری از بروز هرگونه مشکلی، زمان خود را تنظیم نموده و تا انتهای آزمون، از اتصال اینترنت خود اطمینان حاصل نمایید. <br/>
                موفق باشید.
            </div>
            <div class="card-footer" style="text-align: left">
                <form method="GET" id="NextQuestionForm" action="">
                    <input type="hidden" name="exerciseId" value="<?=$_GET['exerciseId']; ?>" />
                    <input type="hidden" name="cidReq" value="<?=$_GET['cidReq']; ?>" />
                    <input type="hidden" name="status" value="start" />
                    <button type="button" id="save" onclick="nextAnswer()" class="btn btn-cyan next-btn"> شروع آزمون </button>
                </form>
            </div>
        </div>                

    </div>
</div>

<script src="/main/css/MDB/js/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="/main/css/MDB/alert/js/jquery-confirm.js" type="text/javascript"></script>
<script src="/main/exercice/Quiz/views/assets/functionsNew.js" type="text/javascript"></script>