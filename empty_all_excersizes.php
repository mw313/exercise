<!DOCTYPE html>
<html>
<head>
    <meta charset="utf8" />
    <title>حذف سوالات آزمون ها</title>
</head>
<body style="direction: rtl; font-family: tahoma; font-size: 12px; padding: 20px 15px;">
<?php

/**
 * @author Mahdi Wosughi
 * @copyright 2016
 */

$c = mysql_connect("mysqlserver", "root", "qwer$#@!QWER") or die(mysql_error());
mysql_select_db("dokeos_main", $c);

$session7 = array();
$order0 = "select * from `session_rel_course` where id_session = '7'";
$q = mysql_query($order0) or die(mysql_error());
while($d = mysql_fetch_assoc($q))
{
    $session7[] = $d['course_code'];
}
$session7s = implode(",", $session7);

$order = "select * from course where code not in ($session7s)";
$q = mysql_query($order) or die(mysql_error());
while($d = mysql_fetch_assoc($q))
{
    mysql_select_db($d['db_name'], $c);
    
    $order2 = "DELETE FROM ".$d['db_name'].".quiz_question";
    //echo($order2."<br/>");
    mysql_query($order2) or die(mysql_error());
    if(mysql_affected_rows() > 0)
    {
        echo "اطلاعات جدول quiz_question درس ".$d['code']." حذف شد! <br/>";
    }
    
    $order3 = "DELETE FROM ".$d['db_name'].".quiz_rel_question";
    //echo($order3."<br/><br/>");
    mysql_query($order3) or die(mysql_error());
    if(mysql_affected_rows() > 0)
    {
        echo "اطلاعات جدول quiz_rel_question درس ".$d['code']." حذف شد! <br/>";
    }
    
    echo "<br/>";
}


?>
</body>
</html>