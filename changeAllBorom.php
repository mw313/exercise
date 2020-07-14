<?php

/**
 * This code added by Mahdi Wosughi
 * For change the grade of all questions 
 */

include('../inc/global.inc.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-rtl.css" />
    <script src="../Bootstrap/js/jquery.js"></script>
    <script src="../Bootstrap/js/bootstrap.min.js"></script>  
    <style>
        *{
            font-family: tahoma;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-sd-3">
        <?php
        
            $con = mysqli_connect("mysqlserver","root","qwer$#@!QWER","dokeos_main");
            mysqli_query($con, "SET NAMES 'utf8'");
            $sql1="SELECT * FROM course";
            $query1 = mysqli_query($con, $sql1);
            $i = 0;
            while($data = mysqli_fetch_assoc($query1))
            {
                mysqli_select_db($con, $data['db_name']) or die(mysqli_error());
                $i++;
                $sql2   = "UPDATE `".$data['db_name']."`.`quiz_question` SET `ponderation` = '4'";
                echo $sql2;
                $query2 = mysqli_query($con, $sql2) or die(mysql_error());
                $num = mysqli_num_rows($query2);
                if($num > 0)
                {
                    echo "<div class='alert alert-success'> $i) سوالات درس «".$data['code']."-".$data['title']."» ویرایش گردید!! </div>";
                }
                else
                {
                    echo "<div class='alert alert-warning'> $i) سوالات درس «".$data['code']."-".$data['db_name']."» ویرایش نشد!! ".mysqli_error()."</div>";
                }
                
            }
            
            mysqli_select_db($con, "dokeos_stats");
            $sql3   = "UPDATE `dokeos_stats`.`track_e_exercices` SET `exe_weighting` = '24' WHERE `exe_result` = '0'";
            $query3 = mysqli_query($con, $sql3);
            $num3 = mysqli_num_rows($query3);
            if($num3 > 0)
            {
                echo "<div class='alert alert-info'> پاسخ های آزمون ها ویرایش گردید!! </div>";
            }
            else
            {
                echo "<div class='alert alert-warning'> پاسخ های آزمون ها ویرایش نشد!! </div>";
            }
        
        ?>
        </div>
    </div>
</div>
</body>
</html>