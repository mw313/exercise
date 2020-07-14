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
</style>
<form method="POST" id="QuestionForm" action="#gridForm">
    <div class="row">
        <div class="col-md-12" style="position: relative" id="gridForm">
            <div class = "card">
                <div class="card-header">
                    <div class="md-form col-md-3" style="float: right">
                        <input type="text" name="date" id="date" class="form-control" value="<?=$_POST['date'] ?>" placeholder="تاریخ برگزاری آزمون" data-mddatetimepicker="true" data-placement="left" />                        
                        <!-- <label for="date" class="">تاریخ برگزاری آزمون</label> -->
                    </div>
                    <div class="md-form col-md-3" style="float: right">
                        <select name="time" id="time" class="form-control" style="height: auto">
                            <option value="00:00:00">همه</option>
                            <option value="07:50:00">08:00</option>
                            <option value="09:50:00">10:00</option>
                            <option value="11:50:00">12:00</option>
                            <option value="13:50:00">14:00</option>
                        </select>
                        <script>
                            $("#time").val('<?=$_POST["time"]?>');
                        </script>
                    </div>
                    <div class="md-form col-md-6" style="float: right; text-align: left">
                        <input type="submit" value="نمایش لیست دروس" class="btn btn-info" />
                        <input type="submit" value="بارگذاری مجدد صفحه" class="btn btn-warning" />
                    </div>
                </div>
                <div class="card-body">                    
                <table class="table table-hover table-sm table-responsive text-nowrap table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">کد درس</th>
                            <th scope="col">پاسخ های ثبت شده</th>
                            <th scope="col">اتمام یافته</th>
                            <th scope="col">اولین پاسخ ثبت شده</th>
                            <th scope="col">آخرین فرد وارد شده</th>
                            <th scope="col">نوع آزمون</th>
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
                                $sum['number'] = $sum['number'] + $course['number'];
                                $sum['endNumber'] = $sum['endNumber'] + $course['endNumber'];
                                
                                if($course['exercise_type'] == 1){
                                    $sum['endNumberTesti'] = $sum['endNumberTesti'] + $course['endNumber'];
                                    $sum['numberTesti'] = $sum['numberTesti'] + $course['number'];
                                    $type = '<span class="badge badge-default"> چندگزینه ای </span>';
                                }
                                else{
                                    $sum['endNumberTashrihi'] = $sum['endNumberTashrihi'] + $course['endNumber'];
                                    $sum['numberTashrihi'] = $sum['numberTashrihi'] + $course['number'];
                                    $type = '<span class="badge badge-primary"> تشریحی </span>';
                                }
                                echo "
                                    <tr>
                                        <td scope='row'>$i</td>
                                        <td>$course[code]</td>
                                        <td>$course[number]</td>
                                        <td>$course[endNumber]</td>
                                        <td>$course[firstDate]</td>
                                        <td>$course[lastDate]</td>
                                        <td>$type</td>
                                        <td style='text-align: center'>
                                            <a href='?code=$course[code]&exercise_id=$course[exercise_id]&act=courseResult' target='_blank'><img src='/main/exercice/Quiz/views/assets/info-svgrepo-com.svg' class='icon-img'/></a>
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
