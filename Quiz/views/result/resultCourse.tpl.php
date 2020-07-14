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
th{
    text-align: right;
}
td{
    vertical-align: middle !important;
}
h5{
    font-weight: bold;
}
</style>

<form method="POST" id="QuestionForm" action="#gridForm">
    <div class="row">
        <div class="col-md-12" style="position: relative" id="gridForm">
            <div class = "card">
                <div class="card-header">
                    <div class="md-form col-md-10" style="float: right">
                        <h5> لیست دانشجویان پاسخ دهنده ی آزمون درس <?=$code ?> </h5>
                    </div>
                    <div class="md-form col-md-2" style="text-align: left">
                        <input type="submit" value="تازه سازی صفحه" class="btn btn-warning" />
                    </div>                    
                </div>
                <div class="card-body">                    
                <table class="table table-hover table-sm table-responsive text-nowrap table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">کد دانشجو</th>
                            <th scope="col">نام</th>
                            <th scope="col">ساعت ورود</th>
                            <th scope="col">ساعت اتمام</th>
                            <th scope="col">پاسخ ها</th>
                            <th scope="col">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i = 0;
                            $sum = array('number'=>0,'numberTesti'=>0,'numberTashrihi'=>0,
                                         'endNumber'=>0, 'endNumberTesti'=>0, 'endNumberTashrihi'=>0,);
                            foreach($courses as $course){
                                $i++;
                                if($course['answering'] == $course["total"]){
                                    $count = '<span class="badge badge-primary">'.$course['total']." / ".$course["answering"]."</span>";
                                }
                                else{
                                    $count = '<span class="badge badge-warning">'.$course['total']." / ".$course["answering"]."</span>";
                                }
                                echo "
                                    <tr>
                                        <td scope='row'>$i</td>
                                        <td>$course[username]</td>
                                        <td>$course[firstname] $course[lastname]</td>
                                        <td>$course[start_date]</td>
                                        <td>$course[end_date]</td>
                                        <td>$count</td>
                                        <td style='text-align: center'>
                                            <a href='?exercise_answer_id=$course[id]&act=userResult' target='_blank'>
                                                <img src='/main/exercice/Quiz/views/assets/info-svgrepo-com.svg' class='icon-img'/>
                                            </a>
                                        </td>
                                    </tr>
                                ";
                            }
                            if($i == 0){
                                echo "
                                    <tr>
                                        <td scope='row' colspan='7' style='text-align: center'> در این تاریخ پاسخی ثبت نشده است!! </td>
                                    </tr>
                                ";
                            }
                            else{
                                $endNumberTesti = ($sum['endNumberTesti']>0)?'<span class="badge badge-default radius0"> '.$sum['endNumberTesti'].' </span>':'';
                                $numberTesti = ($sum['numberTesti']>0)?'<span class="badge badge-default radius0"> '.$sum['numberTesti'].' </span>':'';
                                $endNumberTashrihi = ($sum['endNumberTashrihi']>0)?'<span class="badge badge-primary radius0"> '.$sum['endNumberTashrihi'].' </span>':'';
                                $numberTashrihi = ($sum['numberTashrihi']>0)?'<span class="badge badge-primary radius0"> '.$sum['numberTashrihi'].' </span>':'';
                                $number = '<span class="badge badge-light radius0"> '.$sum['number'].' </span>';
                                $endNumber = '<span class="badge badge-light radius0"> '.$sum['endNumber'].' </span>';

                                echo "
                                    <tr style='background:#555; color: #FFF'>
                                        <td colspan='2'> مجموع </td>
                                        <td> $number $numberTashrihi $numberTesti </td>
                                        <td> $endNumber $endNumberTashrihi $endNumberTesti </td>
                                        <td colspan='4' style='text-align: left; font-size: 10px; font-weight: 100 !important'> 
                                             چندگزینه ای <span class='badge badge-default badge-sample'> </span> <br/>
                                             تشریحی <span class='badge badge-primary badge-sample'> </span>
                                        </td>
                                    </tr>
                                ";
                            }
                        ?>
                        
                    </tbody>
                </table>
                </div>
            </div>

        </div>
    </div>
</form>

<form method="GET" id="NextQuestionForm" action="">
    <input type="hidden" name="cidReq" value="<?=$_GET['cidReq']; ?>" />
    <input type="hidden" name="exerciseId" value="<?=$_GET['exerciseId']; ?>" />
</form>

<!-- <script src="/main/css/MDB/js/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="/main/css/MDB/js/popper.min.js" type="text/javascript"></script>
<script src="/main/css/MDB/alert/js/jquery-confirm.js" type="text/javascript"></script>
<script src="/main/exercice/Quiz/views/assets/MdBootstrapPersianDateTimePicker/calendar.min.js" type="text/javascript"></script>
<script src="/main/exercice/Quiz/views/assets/MdBootstrapPersianDateTimePicker/jquery.Bootstrap-PersianDateTimePicker.min.js" type="text/javascript"></script> -->
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

