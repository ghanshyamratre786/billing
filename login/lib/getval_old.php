<?php
class Comman {
	
	
function backup_tables($host,$user,$pass,$name,$tables = '*')
{
	$link = mysql_connect($host,$user,$pass);
	mysql_select_db($name,$link);
	
	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = mysqli_query($connection,'SHOW TABLES');
		while($row = mysqli_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	
	//cycle through
	foreach($tables as $table)
	{
		$result = mysqli_query($connection,'SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);
		
		$return.= 'DROP TABLE '.$table.';';
		$row2 = mysqli_fetch_row(mysqli_query($connection,'SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysqli_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	
	//save file
	$handle = fopen('backup/db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);
}

// To encrypt data based on key //
function encrypt($string, $key = ENCRYPTION_KEY)
{
	$result = '';
	for($i=0; $i<strlen($string); $i++)
	{
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
	}
	return base64_encode($result);
}

function caretbookopen($connection,$date) {
	$openbal = $this->getvalfield($connection,"caretcompanyopen","sum(openbalc)","1=1");
	$opendate = strtotime($this->getvalfield($connection,"company_setting","crtopendate","1=1"));
	$fromdatestr=strtotime($date);
	
	if($fromdatestr > $opendate) {
	//echo "hi"; die;	
$fromdate = $this->getvalfield($connection,"company_setting","crtopendate","1=1");
$date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
//echo $date; die;

 $totalrec = $this->getvalfield($connection,"carretentry","sum(qty)","billdate between '$date' and '$date'  && is_sup=0"); 

 $othercaretin=  $this->getvalfield($connection,"othr_crt","sum(qty)","indate between '$date' and '$date'  && process='in'"); 

$purchaseinsup =  $this->getvalfield($connection,"purchaseentrydetail as A left join m_unit as B on A.unitid=B.unitid left join purchaseentry as C on A.purchaseid=C.purchaseid","sum(qty)","C.purchasedate between '$date' and '$date' && isstockable=1"); 

$totaldisc = $this->getvalfield($connection,"othr_crt","sum(qty)","indate between '$date' and '$date'  && process='out'");

$totalpayment = $this->getvalfield($connection,"saleenetry as A left join m_unit as B on A.unitid=B.unitid","sum(qty)","billdate between '$fromdate' and '$date' && isstockable=1") + $this->getvalfield($connection,"loaderentry as A left join m_unit as B on A.unitid=B.unitid left join loading as C on A.lodingid=C.lodingid","sum(qty)","C.loaddate between '$date' and '$date' && isstockable=1");

$suppret = $this->getvalfield($connection,"carretentry","sum(qty)","billdate between '$date' and '$date'  && is_sup=1");

$totexpense=0;
$totincome=0;
$totpayment=0;
	return $openbal + $totalrec + $totincome + $purchaseinsup - $totpayment - $totexpense - $suppret;

}
else
{

return $openbal;
}
	
	
}

function cashbookopen($connection,$date) {
$openbal = $this->getvalfield($connection,"company_setting","openbal","1=1");
$opendate = strtotime($this->getvalfield($connection,"company_setting","opendate","1=1"));
$fromdatestr=strtotime($date);

if($fromdatestr > $opendate) {
$fromdate = $this->getvalfield($connection,"company_setting","opendate","1=1");
$date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
$totalrec = $this->getvalfield($connection,"payment","sum(paid_amt)","paymentdate between '$fromdate' and '$date'  && type='sale'");
$totincome = $this->getvalfield($connection,"other_income","sum(amount)","transdate between '$fromdate' and '$date'");
$totpayment = $this->getvalfield($connection,"payment","sum(paid_amt)","paymentdate between '$fromdate' and '$date' && type='purchase'");
$totexpense = $this->getvalfield($connection,"other_expense","sum(amount)","transdate between '$fromdate' and '$date'");

	return $openbal + $totalrec + $totincome - $totpayment - $totexpense;

}
else
{

return $openbal;
}
}


function checkuser() {
	//$serialkey =  readfile("../../../php/test.txt"); 
	//echo $serialkey; die;
	
	//if($serialkey !="BKMKP5") {  echo "hacked..... Call to 8871181890"; die;  }
}


// To decrypt data based on key //
function decrypt($string, $key = ENCRYPTION_KEY)
{
	$result = '';
	$string = base64_decode($string);
	
	for($i=0; $i<strlen($string); $i++)
	{
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)-ord($keychar));
		$result.=$char;
	}
	return $result;
}


function get_billno($tblname,$tblpkey)
{
	$maxid = $this ->getvalfield($connection,$tblname,"max($tblpkey)","1=1");	
	
	
	$id = $maxid + 1;
	$strlen = strlen($id);
	if($strlen == 1)
	$id = '00000'.$id;
	else if($strlen == 2)
	$id = '0000'.$id;
	else if($strlen == 3)
	$id = '000'.$id;
	else if($strlen == 4)
	$id = '00'.$id;
	else if($strlen == 5)
	$id = '0'.$id;
	else if($strlen == 6)
	$id = $id;
	return $id;
	
}

// get Token no. //
function getTokencode($associationid)
{
	$sql = "select tokenno from issue_token where associationid = '$associationid' order by tokenno desc";
	//echo $sql,"<br>";
	$getvalue = mysqli_query($connection,$sql);
	$getval = mysqli_fetch_row($getvalue);
		
	$asdigit = $this->genNDigitCode("",$associationid,2);
	//echo "--".$getval[0]."--","<br>";
	if($getval[0] != "")
	$lastOrderCode = substr($getval[0], -5);
	else
	$lastOrderCode = 0;
	
	//echo $lastOrderCode,"<br>";
	$orderCode = intval($lastOrderCode) + 1;
	
	//echo $orderCode,"<br>";
	
	$orderDigit = $this->genNDigitCode("",$orderCode,5);
		
	return $asdigit . $orderDigit;
}


function InsertLogin($memberid, $emuid, $associationid, $asuid, $membertype, $username)
{
	$password = $this->getmixedno(8);
	$sqlquery = "insert into login(memberid, emuid, associationid, asuid, membertype, username, password, enable) values('$memberid', '$emuid', '$associationid', '$asuid', '$membertype', '$username', '$password', 1)";
	mysqli_query($connection,$sqlquery);
	return $password;
}

function genNDigitCode($joinchar, $id, $num)
{
	$digit = strlen($id);
	$zeronum = "";
	for($i=$digit; $i<$num;  $i++)
	$zeronum .= "0";
	return $joinchar . $zeronum . $id;
}
function InsertLog($connection,$pagename, $module, $submodule, $tablename, $tablekey, $keyvalue, $action)
{
	$sessionid = $this->getvalfield($connection,"m_session","session_name","status='1'");
	$userid = $_SESSION['userid'];
	$usertype = $_SESSION['usertype'];
	$activitydatetime  = date('Y-m-d H:m:s');
	
	$sqlquery = "insert into activitylogreport(userid, usertype, module, submodule, pagename, primarykeyid ,tablename, activitydatetime, action) values('$userid', '$usertype', '$module', '$submodule',  '$pagename', '$keyvalue','$tablename', '$activitydatetime', '$action')";
	//echo $sqlquery;die;
	mysqli_query($connection,$sqlquery);
}
//Insert into log history in CA admin //
function insertLoginLogout($userid ,$usertype,$process,$sessionid,$ipaddress)
{
	$date = date("Y-m-d H:i:s");
    $sql = "insert into  loginlogoutreport set userid = '$userid' ,usertype = '$usertype' ,process = '$process' ,sessionid = '$sessionid',loginlogouttime = '$date'  ,ipaddress = '$ipaddress', createdate = '$date'";
	mysqli_query($connection,$sql);
	//echo $sql;die;
}
function sendsms($smsuname,$msg_token,$smssender,$msg,$mobile)
{
	//echo "Called";
	//http://loginsms.trinitysolutions.pw/api/send_transactional_sms.php?username=u2377&msg_token=dgy4ws&sender_id=PALITP&message=hello%20nipesh&mobile=9770131555
	$request = ""; //initialize the request variable
	$param["username"] = $smsuname; //this is the username of our TM4B account
	$param["msg_token"] = $msg_token; //this is the password of our TM4B account
	$param["sender_id"] =$smssender;//this is our sender 
	$param["message"] = $msg; //this is the message that we want to send
	$param["mobile"] = $mobile; //these are the recipients of the message
			
	foreach($param as $key=>$val) //traverse through each member of the param array
	{ 
		$request.= $key."=".urlencode($val); //we have to urlencode the values
		$request.= "&"; //append the ampersand (&) sign after each paramter/value pair
	}
	$request = substr($request, 0, strlen($request)-1); //remove the final ampersand sign from the request
	
	//die;
	//First prepare the info that relates to the connection
	$host = "loginsms.trinitysolutions.pw";
	$script = "/api/send_transactional_sms.php";
	$request_length = strlen($request);
	$method = "POST"; // must be POST if sending multiple messages
	if ($method == "GET") 
	{
	  $script .= "?$request";
	}
	
	//echo $host; die;
	//Now comes the header which we are going to post. 
	$header = "$method $script HTTP/1.1\r\n";
	$header .= "Host: $host\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: $request_length\r\n";
	$header .= "Connection: close\r\n\r\n";
	$header .= "$request\r\n";
	
	//echo $header; die;
	//Now we open up the connection
	$socket = @fsockopen($host, 80, $errno, $errstr); 
	if ($socket) //if its open, then...
	{ 
	  fputs($socket, $header); // send the details over
	  while(!feof($socket))
	  {
		$output[] = fgets($socket); //get the results 
	  }
	  fclose($socket); 
	} 
}

function sendsmsold($request)
{
	//First prepare the info that relates to the connection
	$host = "sms.reliableindya.info";
	$script = "/web2sms.php";
	$request_length = strlen($request);
	$method = "POST"; // must be POST if sending multiple messages
	if ($method == "GET") 
	{
	  $script .= "?$request";
	}
	 
	//Now comes the header which we are going to post. 
	$header = "$method $script HTTP/1.1\r\n";
	$header .= "Host: $host\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: $request_length\r\n";
	$header .= "Connection: close\r\n\r\n";
	$header .= "$request\r\n";
	//Now we open up the connection
	$socket = @fsockopen($host, 80, $errno, $errstr); 
	if ($socket) //if its open, then...
	{ 
	  fputs($socket, $header); // send the details over
	  while(!feof($socket))
	  {
		$output[] = fgets($socket); //get the results 
	  }
	  fclose($socket); 
	} 
}

// mail content //
function getEmailContent($content,$loginbtn,$loginurl)
{
	$urlOfOurSite1 = "http://www.fullonwms.com/fullonwms/";
	$mc = "<html><body><table width='400' align='center' cellpadding='0' cellspacing='0' style='border-top:5px solid #0079d6;border-left:5px solid #0079d6; border-right:5px solid #50b2fd; border-bottom:5px solid #50b2fd'><tr><td><table width='500' cellpadding='5' cellspacing='5'><tr><td align='center'><img src='".$urlOfOurSite1."images/maillogo2.jpg' width='500' height='130'></td></tr><tr><td style='text-align:justify; color:#024c84; font-size:16px'><span style='color:#024c84'><strong>Dear member,</strong></span><br><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
	$mc .= $content;
	
	$logincontent = "<br /><strong>Regards,<br>FullOnWMS team</strong></td></tr></table></td></tr></table></body></html>";
	if($loginbtn=="Y")
	{
		$mc .= "<center><a href='".$urlOfOurSite1.$loginurl."' target='_blank' style='text-decoration:none'><span style='background-color:#0079d6; width:230px; height:30px; color:#FFF; font-weight:bold; padding:15px; padding-bottom:10px; padding-top:10px; font-size:22px'>Login</span></a></center>";
	}
	
	$mc .= "<br /><br /><br /><strong>Regards,<br>FullOnWMS team</strong></td></tr></table></td></tr></table></body></html>";
	return $mc;
}
// change number into word format //
function numtowords($num)
{
	$ones = array(
	1 => "one",
	2 => "two",
	3 => "three",
	4 => "four",
	5 => "five",
	6 => "six",
	7 => "seven",
	8 => "eight",
	9 => "nine",
	10 => "ten",
	11 => "eleven",
	12 => "twelve",
	13 => "thirteen",
	14 => "fourteen",
	15 => "fifteen",
	16 => "sixteen",
	17 => "seventeen",
	18 => "eighteen",
	19 => "nineteen"
	);
	$tens = array(
	2 => "twenty",
	3 => "thirty",
	4 => "forty",
	5 => "fifty",
	6 => "sixty",
	7 => "seventy",
	8 => "eighty",
	9 => "ninety"
	);
	$hundreds = array(
	"hundred",
	"thousand",
	"million",
	"billion",
	"trillion",
	"quadrillion"
	); //limit t quadrillion
	$num = number_format($num,2,".",",");
	$num_arr = explode(".",$num);
	$wholenum = $num_arr[0];
	$decnum = $num_arr[1];
	$whole_arr = array_reverse(explode(",",$wholenum));
	krsort($whole_arr);
	$rettxt = "";
	foreach($whole_arr as $key => $i)
	{
		if($i < 20)
		{
			$rettxt .= $ones[$i];
		}
		elseif($i < 100)
		{
			$rettxt .= $tens[substr($i,0,1)];
			$rettxt .= " ".$ones[substr($i,1,1)];
		}
		else
		{
			$rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0];
			$rettxt .= " ".$tens[substr($i,1,1)];
			$rettxt .= " ".$ones[substr($i,2,1)];
		}
		
		if($key > 0)
		{
			$rettxt .= " ".$hundreds[$key]." ";
		}
	}
	if($decnum > 0)
	{
		$rettxt .= " and ";
		if($decnum < 20)
		{
			$rettxt .= $ones[$decnum];
		}
		elseif($decnum < 100)
		{
			$rettxt .= $tens[substr($decnum,0,1)];
			$rettxt .= " ".$ones[substr($decnum,1,1)];
		}
	}
	return $rettxt;
} 

// return present page url //
function curPageURL()
{
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") 
	$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	else
	$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	return $pageURL;
}

// trial for pagination //
function startPagination($page_query, $data_in_a_page)
{
	$getrow = mysqli_query($connection,$page_query);
	$count = mysqli_num_rows($getrow);
	
	$page_for_site = "";
	
	$page=1;
	if(isset($_REQUEST['page']))
	$page = $_REQUEST['page'];
	
	if($count > $data_in_a_page)
	{
		$cnt = ceil($count / $data_in_a_page);
		
		$page_for_site .= "<div style='float:left; padding-top:3px; color:#c0f;'>Page $page of $cnt &nbsp;&nbsp;&nbsp;</div>";
		
		for($i = 1; $i<= $cnt; $i++)
		{
			$class = " class='pagination' ";
			if($i == $page)
			$class = " class='pagination-current' ";
			
			$pu = $this->curPageURL();
			$cm = explode("/",$pu);
			$n = count($cm);
			$curl = $cm[$n-1];
			
			$qm_avail = strpos($curl,"?");
			if($qm_avail == "")
			$page_for_site .= "<a href='?page=$i' $class>$i</a>";

			else
			{
				$page_avail = strpos($curl,"page=");
				if($page_avail != "")
				{
					$pagevalue = $_REQUEST['page'];
					$past_page = "page=$pagevalue";
					$finalurl = str_replace($past_page,"page=$i",$curl);
					$page_for_site .= "<a href='$finalurl' $class>$i</a>";
				}
				else
				$page_for_site .= "<a href='$curl&page=$i' $class>$i</a>";
			}
		}
		$page_for_site .= "<div style='clear:both'></div>";
	}
	echo $page_for_site;
}

function get_client_ip() 
{
      $ipaddress = '';
      if (getenv('HTTP_CLIENT_IP'))
          $ipaddress = getenv('HTTP_CLIENT_IP');
      else if(getenv('HTTP_X_FORWARDED_FOR'))
          $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
      else if(getenv('HTTP_X_FORWARDED'))
          $ipaddress = getenv('HTTP_X_FORWARDED');
      else if(getenv('HTTP_FORWARDED_FOR'))
          $ipaddress = getenv('HTTP_FORWARDED_FOR');
      else if(getenv('HTTP_FORWARDED'))
          $ipaddress = getenv('HTTP_FORWARDED');
      else if(getenv('REMOTE_ADDR'))
          $ipaddress = getenv('REMOTE_ADDR');
      else
          $ipaddress = 'UNKNOWN';

      return $ipaddress; 
}

// get image in particular size. if you writ only width then it returns in ratio of height. and you can set width and height //
function convert_image($fname,$path,$wid,$hei)
{
	$wid = intval($wid); 
	$hei = intval($hei); 
	//$fname = $sname;
	$sname = "$path$fname";
	//echo $sname;
	//header('Content-type: image/jpeg,image/gif,image/png');
	//image size
	list($width, $height) = getimagesize($sname);
	
	if($hei == "")
	{
		if($width < $wid)
		{
			$wid = $width;
			$hei = $height;
		}
		else
		{
			$percent = $wid/$width;  
			$wid = $wid;
			$hei = round ($height * $percent);
		}
	}
	
	//$wid=469;
	//$hei=290;
	$thumb = imagecreatetruecolor($wid,$hei);
	//image type
	$type=exif_imagetype($sname);
	//check image type
	switch($type)
	{
	case 2:
	$source = imagecreatefromjpeg($sname);
	break;
	case 3:
	$source = imagecreatefrompng($sname);
	break;
	case 1:
	$source = imagecreatefromgif($sname);
	break;
	}
	// Resize
	imagecopyresized($thumb, $source, 0, 0, 0, 0,$wid,$hei, $width, $height);
	//echo "converted";
	//else
	//echo "not converted";
	// source filename
	$file = basename($sname);
	//destiantion file path
	//$path="uploaded/flashgallery/";
	$dname=$path.$fname;
	//display on browser
	//imagejpeg($thumb);
	//store into file path
	imagejpeg($thumb,$dname);
}

// for get mixed no. like password etc. //
function getmixedno($totalchar)
{
	$abc= array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
	$mixedno = "";
	for($i=0; $i<=$totalchar; $i++)
	{
		$mixedno .= $abc[rand(0,35)];
	}
	return $mixedno;
}


// get total no. of rows //
function getTotalNum($table,$where)
{
	$sql = "select * from $table where $where";
	//echo $sql;
	$getvalue = mysqli_query($connection,$sql);
	$getval = mysql_num_rows($getvalue);

	return $getval;
}

// get value from any condition //
function getvalfield($connection,$table,$field,$where)
{
	$sql = mysqli_query($connection,"set names utf-8 ");
	$sql = "select $field from $table where $where";
	//echo $sql;
	
	//die;
	$getvalue = mysqli_query($connection,$sql);
	$getval = mysqli_fetch_row($getvalue);

	return $getval[0];
}

// get date format (01 march 2012) from 2012-03-01 //
function dateformat($date)
{
	if($date != "0000-00-00")
	{
	$ndate = explode("-",$date);
	$year = $ndate[0];
	$day = $ndate[2];
	$month = intval($ndate[1])-1;
	$montharr = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	$month1 = $montharr[$month];
	
	
	return $day . " " . $month1 . " " . $year;
	}
	else
	return "";
}

// get date format (01-03-2012) from (2012-03-01) //
function dateformatindia($date)
{
	$ndate = explode("-",$date);
	$year = $ndate[0];
	$day = $ndate[2];
	$month = $ndate[1];
	
	if($date == "0000-00-00" || $date =="")
	return "";
	else
	return $day . "-" . $month . "-" . $year;
	
}

// get date format (01-03-2012) from (2012-03-01 23:12:04) //
function dateFullToIndia($date,$full)
{
	$fdate = explode(" ",$date);
	
	$ndate = explode("-",$fdate[0]);
	$year = $ndate[0];
	$day = $ndate[2];
	$month = $ndate[1];
	
	$time = explode(":",$fdate[1]);
	$hour = $time[0];
	$minute = $time[1];
	$second = $time[2];
	if($hour > 12)
	{
		$h = $hour-12;
		if($h < 10)
		$h = "0" . $h;
		$fulltime = $h . ":" . $minute . ":" . $second . " PM";
	}
	else
	$fulltime = $hour . ":" . $minute . ":" . $second . " AM";
	
	
	if($full == "full")
	return $day . "-" . $month . "-" . $year . " " . $fdate[1];
	else if($full == "fullindia")
	return $day . "-" . $month . "-" . $year . " " . $fulltime;
	else if($full == "time")
	return $fulltime;
	else
	return $day . "-" . $month . "-" . $year;
}

// get date format (2012-03-01) from (01-03-2012) //
function dateformatusa($date)
{
	if($date !='') {
	$ndate = explode("-",$date);
	$year = $ndate[2];
	$day = $ndate[0];
	$month = $ndate[1];
	
	return $year . "-" . $month . "-" . $day;
	}
	else
	return '';
}

// get value if you know the primary key value //
function getvalMultiple($table,$field,$where)
{
	$sql = "select $field from $table where $where";
	//echo $sql;
	$getvalue = mysqli_query($connection,$sql);
	$getval="";
	while($row = mysqli_fetch_row($getvalue))
	{
		if($getval == "")
		$getval = $row[0];
		else
		$getval .= ",". $row[0];
	}
	return $getval;
}


function getcode($connection,$tablename,$tablepkey,$cond)
{
	$num =  $this->getvalfield($connection,$tablename,"max($tablepkey)","$cond");
	
	
	//if($num == NULL)
	//$num = 0;
    ++$num; // add 1;
    $len = strlen($num);
    for($i=$len; $i< 5; ++$i) {
        $num = '0'.$num;
    }
    return $num;
}	
function getrec($tablename,$tablepkey,$cond)
{
	$num =  $this->getvalfield($connection,$tablename,"max($tablepkey)","$cond");
	//if($num == NULL)
	//$num = 0;
    ++$num; // add 1;
    $len = strlen($num);
    for($i=$len; $i< 5; ++$i) {
        $num = '0'.$num;
    }
	$num='rec'.$num;
    return $num;
}

function getdayname($name) {

		if($name=="Mon") { $msg = "सोमवार";    }
		else if($name=="Tue") { $msg = "मंगलवार";     }
		else if($name=="Wed") {  $msg = "बुधवार";  }
		else if($name=="Thu") {  $msg = "गुरुवार";    }
		else if($name=="Fri") {   $msg = "शुक्रवार";   }
		else if($name=="Sat") {   $msg = "शनिवार";   }
		else if($name=="Sun") {   $msg = "रविवार";    }
		else {   $msg = "";   }
		
		return $msg;

}

	
	function getopeningbalcust($connection,$suppartyid,$fromdate) {
	$openbal = $this->getvalfield($connection,"m_supplier_party","prevbalance","suppartyid='$suppartyid'");
	//$tilldate = date('Y-m-d', strtotime('-1 day', strtotime($fromdate)));
	$totbillamt = $this->getrounfsaleamt($connection,$fromdate,$suppartyid);		
	$totalpaid = $this->getvalfield($connection,"payment","sum(paid_amt + discamt)","suppartyid='$suppartyid' and paymentdate <='$fromdate' and type='sale'");
	
	$totpur = $this->getpurchasetilldate($connection,$fromdate,$suppartyid);
	$purpay = $this->getvalfield($connection,"payment","sum(paid_amt + discamt)","suppartyid='$suppartyid' and paymentdate <='$fromdate'  and type='purchase'");
	
	$loadamt = $this->getvalfield($connection,"loading","sum(netsale)","suppartyid='$suppartyid' and loaddate <='$fromdate'");
	
	return $openbal + $totbillamt - $totalpaid - $totpur + $purpay + $loadamt;
	}
	
	function getpurchasetilldate($connection,$fromdate,$suppartyid) {
	$pur=0;
	$sql = mysqli_query($connection,"select purchaseid from purchaseentry where purchasedate <= '$fromdate' && suppartyid='$suppartyid'");
	while($row=mysqli_fetch_assoc($sql)) {
	$pur +=$this->gettotalpurchase($connection,$row['purchaseid']);
	}
	
	return $pur;
	}
	
	
	function gettotalpurchase($connection,$purchaseid) {
	
	$netamount = 0;
	$total_unit_rate=0;
	$sql = mysqli_query($connection,"select * from purchaseentrydetail where purchaseid='$purchaseid'");
	while($row=mysqli_fetch_assoc($sql)) {
	$total = $row['qty'] * $row['weight'] * $row['rate'];
	$total_unit_rate += $row['qty'] * $row['unitrate'];
	$netamount += $total ;
	}
	
	
	$tot_exp = 0;
	$sql = mysqli_query($connection,"select * from purchaseexp where purchaseid='$purchaseid'");
	while($row=mysqli_fetch_assoc($sql))
	{
	
	$type = $row['type'];
	$expprocess = $row['expprocess'];
	
	if($type=='rs')
	{
	$expamt = $row['expamt'];
	}
	else
	{
	$expamt = ($netamount * $row['expamt'])/100;
	}
	
	if($expprocess=='Add') {
	$tot_exp += $expamt;
	}	
	else
	{
	$tot_exp -= $expamt;
	}
	}	
	
	//echo $total_unit_rate;
	return round($netamount + $tot_exp + $total_unit_rate);
	}
	
	
	function getrounfsaleamt($connection,$date,$suppartyid) {
	$saleamount=0;
	
	$sql = mysqli_query($connection,"select billdate from saleenetry where suppartyid='$suppartyid' and billdate <='$date' group by billdate");
	while($row=mysqli_fetch_assoc($sql))	
	{
	$billdate = $row['billdate'];		
	$saleamount += round($this->getvalfield($connection,"saleenetry","sum((qty * rate * weight)+(qty * unitrate))","suppartyid='$suppartyid' and billdate ='$billdate'"));
	}
	return $saleamount;
	
	}



function getcarretopenbydate($connection,$suppartyid,$unitid,$date) {

		
		
		 $openbalcar = $this->getvalfield($connection,"openingunitentry","openunit","suppartyid='$suppartyid' and unitid='$unitid' && type=0");			
		  $qtycartoon = $this->getvalfield($connection,"saleenetry","sum(qty)","suppartyid='$suppartyid' and unitid='$unitid' && billdate <='$date'"); 
	    $retaty = $this->getvalfield($connection,"carretentry","sum(qty)","suppartyid='$suppartyid' and unitid='$unitid' && billdate <='$date' && is_sup=0");
		
		 $purqtycartoon = $this->getvalfield($connection,"purchaseentrydetail as A left join purchaseentry as B on A.purchaseid=B.purchaseid","sum(A.qty)","B.suppartyid='$suppartyid' and A.unitid='$unitid' && purchasedate <='$date'");  
		$purretaty = $this->getvalfield($connection,"carretentry","sum(qty)","suppartyid='$suppartyid' and unitid='$unitid' && billdate <='$date' && is_sup=1");
		
		$loadingcaret = $this->getvalfield($connection,"loaderentry as A left join loading as B on A.lodingid=B.lodingid","sum(A.qty)","B.suppartyid='$suppartyid' and A.unitid='$unitid' && loaddate <='$date'");  
	
		return $openbalcar + $qtycartoon - $retaty - $purqtycartoon + $purretaty + $loadingcaret;			

}



function getcarretopenbydate2($connection,$suppartyid,$unitid,$date) {

		
		
		 $openbalcar = $this->getvalfield($connection,"openingunitentry","openunit","suppartyid='$suppartyid' and unitid='$unitid' && type=0");			
		  $qtycartoon = $this->getvalfield($connection,"saleenetry","sum(qty)","suppartyid='$suppartyid' and unitid='$unitid' && billdate <='$date'"); 
	    $retaty = $this->getvalfield($connection,"carretentry","sum(qty)","suppartyid='$suppartyid' and unitid='$unitid' && billdate <='$date' && is_sup=0");
		
		 $purqtycartoon = $this->getvalfield($connection,"purchaseentrydetail as A left join purchaseentry as B on A.purchaseid=B.purchaseid","sum(A.qty)","B.suppartyid='$suppartyid' and A.unitid='$unitid' && purchasedate <='$date'");  
		$purretaty = $this->getvalfield($connection,"carretentry","sum(qty)","suppartyid='$suppartyid' and unitid='$unitid' && billdate <='$date' && is_sup=1");
	
		return $openbalcar - $qtycartoon + $retaty + $purqtycartoon - $purretaty;			

}



function gettotalloadamt($connection,$lodingid) {
	
	$total = $this->getvalfield($connection,"loading","netsale","lodingid='$lodingid'");
	return $total;
	
}



function getcustomerroaker2all($connection,$suppartyid,$fromdate,$todate) {
$nettoral= 0;
$roaker= array();		
	$sql_get = mysqli_query($connection,"Select Distinct(date) From (select billdate as date from saleenetry where 1=1 and suppartyid='$suppartyid' and billdate between '$fromdate' and '$todate' UNION ALL select paymentdate as date from payment where 1=1 and suppartyid='$suppartyid' && type='sale' and paymentdate between '$fromdate' and '$todate' UNION ALL  select purchasedate as date from purchaseentry where 1=1 and suppartyid='$suppartyid' and purchasedate between '$fromdate' and '$todate' UNION ALL select paymentdate as date from payment where 1=1 and suppartyid='$suppartyid' && type !='sale' and paymentdate between '$fromdate' and '$todate' UNION ALL select loaddate as date from loading where 1=1 and suppartyid='$suppartyid' and loaddate between '$fromdate' and '$todate')A group by date ORDER BY date");
while($row_get = mysqli_fetch_assoc($sql_get))
	{
		$amount =0;
		$msg='';
		$saleamount = round($this->getvalfield($connection,"saleenetry","sum((qty * rate * weight)+(qty * unitrate))","suppartyid='$suppartyid' and billdate='$row_get[date]'"));
		if($saleamount !=0 && $saleamount !='') {
		$msg ="Sale" ;						
		$newar = array($row_get['date'],$saleamount,'',$nettoral,$msg,'');
		$roaker[] = $newar;	
				}
		$msg='';		
		$sql = mysqli_query($connection,"select paymentid from payment where suppartyid='$suppartyid' and paymentdate='$row_get[date]' && type='sale'");
			while($row=mysqli_fetch_assoc($sql)) {	
				$paymentid = $row['paymentid'];			
			$payamount = $this->getvalfield($connection,"payment","sum(paid_amt + discamt)",
			"paymentid='$paymentid'");				
					if($payamount !=0) {
					$msg ="Receipt" ;
					}				
						if($saleamount=='') { $saleamount=0; }
						if($payamount=='') { $payamount=0; }
				
					if($msg !='') {
					$nettoral=0;
					$newar = array($row_get['date'],'',$payamount,$nettoral,$msg,$paymentid);				
					$roaker[] = $newar;
					}
			}
			
		
			$msg='';
			$puramt = 0;			
			$sql = mysqli_query($connection,"select * from purchaseentry where suppartyid='$suppartyid' and purchasedate = '$row_get[date]'");
			while($row=mysqli_fetch_assoc($sql)) {
			$puramt = $this->gettotalpurchase($connection,$row['purchaseid']);
			if($puramt !=0) {
			 $msg ="Bijak";
					$newar = array($row_get['date'],'',$puramt,'',$msg,$row['purchaseid']);				
					$roaker[] = $newar;
			  }
			}
			
			$msg='';
			$sql = mysqli_query($connection,"select paymentid,paid_amt,discamt from payment where suppartyid='$suppartyid' and paymentdate='$row_get[date]' && type='purchase'");
			while($row=mysqli_fetch_assoc($sql)) {	
				$paymentid = $row['paymentid'];			
				$payamount = $row['paid_amt'] + $row['discamt'];					
					if($payamount !=0) {
					$msg ="Payment" ;
					}				
					if($payamount=='') { $payamount=0; }				
					if($msg !='') {
					$nettoral=0;
					$newar = array($row_get['date'],$payamount,'','',$msg,$paymentid);					
					$roaker[] = $newar;
					}
			}
			
			
				$msg='';
				$loadamt = 0;	
				$numrow=0;	
				
				$sql = mysqli_query($connection,"select lodingid from loading where suppartyid='$suppartyid' and loaddate = '$row_get[date]'");
				$numrow = mysqli_num_rows($sql);
				while($row=mysqli_fetch_assoc($sql)) {
						//$lodingid = $row['lodingid'];
						$loadamt = $this->gettotalloadamt($connection,$row['lodingid']);
						if($loadamt=='') { $loadamt=0; }
						
						if($numrow!=0) {
						$msg="Loading";
						$newar = array($row_get['date'],$loadamt,'','',$msg,$row['lodingid']);				
						$roaker[] = $newar;
						
						}				
				}										
		}	
	return $roaker;	
}


function getcustomerroaker($connection,$suppartyid,$fromdate,$todate) {

$nettoral= 0;
$roaker= array();	
	
	$sql_get = mysqli_query($connection,"Select Distinct(date) From (select billdate as date from saleenetry where 1=1 and suppartyid='$suppartyid' and billdate between '$fromdate' and '$todate' UNION ALL select paymentdate as date from payment where 1=1 and suppartyid='$suppartyid' && type='sale' and paymentdate between '$fromdate' and '$todate' UNION ALL  select purchasedate as date from purchaseentry where 1=1 and suppartyid='$suppartyid' and purchasedate between '$fromdate' and '$todate' UNION ALL select paymentdate as date from payment where 1=1 and suppartyid='$suppartyid' && type !='sale' and paymentdate between '$fromdate' and '$todate' UNION ALL select loaddate as date from loading where 1=1 and suppartyid='$suppartyid' and loaddate between '$fromdate' and '$todate')A group by date ORDER BY date");
	
	
	while($row_get = mysqli_fetch_assoc($sql_get))
				{									
				//$type = $row_get['type'];
						$amount =0;
						$msg='';
						$saleamount = round($this->getvalfield($connection,"saleenetry","sum((qty * rate * weight)+(qty * unitrate))","suppartyid='$suppartyid' and billdate='$row_get[date]'"));
						if($saleamount !=0) {
								$msg .="Sale" ;
				}
				
				$payamount = $this->getvalfield($connection,"payment","sum(paid_amt + discamt)","suppartyid='$suppartyid' and paymentdate='$row_get[date]' && type='sale'");
				
				if($payamount !=0) {
				$msg .="/Receipt" ;
				}
				
				if($saleamount=='') { $saleamount=0; }
				if($payamount=='') { $payamount=0; }
				
				if($msg !='') {
				//$nettoral = $this->getopeningcustomer($connection,$row_get['date'],$suppartyid);
				$nettoral=0;
				$newar = array($row_get['date'],$saleamount,$payamount,$nettoral,$msg);				
				$roaker[] = $newar;
				}
				
				
				$msg='';
				$puramt = 0;
				
			$sql = mysqli_query($connection,"select * from purchaseentry where suppartyid='$suppartyid' and purchasedate = '$row_get[date]'");
			while($row=mysqli_fetch_assoc($sql)) {
			$total = $this->gettotalpurchase($connection,$row['purchaseid']);
			$puramt += $total;
			}
				
				$paidamt= $this->getvalfield($connection,"payment","sum(paid_amt + discamt)","suppartyid='$suppartyid' && type='purchase' and paymentdate='$row_get[date]'");	
					
				$netpur=0;
				if($paidamt !=0) { $msg .="Payment"; }
				if($puramt !=0) { $msg .="/Bijak"; }
				
				if($paidamt=='') { $paidamt=0; }
				if($puramt=='') { $puramt=0; }
				
				if($msg !='') {
				$newar = array($row_get['date'],$paidamt,$puramt,$netpur,$msg);				
				$roaker[] = $newar;
							}
				
				$msg='';
				$loadamt = 0;	
				$numrow=0;	
					
				$sql = mysqli_query($connection,"select lodingid from loading where suppartyid='$suppartyid' and loaddate = '$row_get[date]'");
				$numrow = mysqli_num_rows($sql);
				while($row=mysqli_fetch_assoc($sql)) {
						//$lodingid = $row['lodingid'];
						$total = $this->gettotalloadamt($connection,$row['lodingid']);
						$loadamt += $total;
				}
				
				  		
				
				if($numrow !=0) {
				$msg .="Loading";
				if($loadamt=='') { $loadamt=0; }
				$newar = array($row_get['date'],$loadamt,'0',$loadamt,$msg);				
				$roaker[] = $newar;
							}	
											
				}	
				return $roaker;	
}


function getpurexpense($connection,$purchaseid,$addexp_id) {
	$netamount = 0;
	$total_unit_rate=0;
	$sql = mysqli_query($connection,"select * from purchaseentrydetail where purchaseid='$purchaseid'");
	while($row=mysqli_fetch_assoc($sql)) {
	$total = $row['qty'] * $row['weight'] * $row['rate'];
	$total_unit_rate += $row['qty'] * $row['unitrate'];
	$netamount += $total ;
	}
	
	
	$tot_exp = 0;
	//echo "select * from purchaseexp where purchaseid='$purchaseid' && addexp_id='$addexp_id'"; die;
	$sql = mysqli_query($connection,"select * from purchaseexp where purchaseid='$purchaseid' && addexp_id='$addexp_id'");
	while($row=mysqli_fetch_assoc($sql))
	{
	
	$type = $row['type'];
	$expprocess = $row['expprocess'];
	
	if($type=='rs')
	{
	$expamt = $row['expamt'];
	}
	else
	{
	$expamt = ($netamount * $row['expamt'])/100;
	}
	
	if($expprocess=='Add') {
	$tot_exp += $expamt;
	}	
	else
	{
	$tot_exp -= $expamt;
	}
	}	
	
	//echo $total_unit_rate;
	return round($tot_exp);
		
		
}
	
function getcarretroaker($connection,$suppartyid,$fromdate,$todate) {

$nettoral= 0;
$roaker= array();	

$sql_get = mysqli_query($connection,"Select Distinct(date) From (
select billdate as date from saleenetry where 1=1 and suppartyid='$suppartyid' and billdate between '$fromdate' and '$todate'
 UNION ALL select billdate as date from carretentry where 1=1 and suppartyid='$suppartyid'  and billdate between '$fromdate' and '$todate'
  UNION ALL  select purchasedate as date from purchaseentry where 1=1 and suppartyid='$suppartyid' and purchasedate between '$fromdate' and '$todate'
   UNION ALL select loaddate as date from loading where 1=1 and suppartyid='$suppartyid' and loaddate between '$fromdate' and '$todate')A group by date ORDER BY date");

while($row_get = mysqli_fetch_assoc($sql_get))
{
	//print_r($row_get);
	$date = $row_get['date'];				
	$msg='';
				
	$custarray = array();	
	$supparray=array();	
	$loadarray =array();	
	$sql = mysqli_query($connection,"select unitid,unit_name,unit_name_hindi from m_unit where isstockable=1");
				while($row=mysqli_fetch_assoc($sql)) {
				
			$msg='';			
			$unitid = $row['unitid'];
			$unit_name = $row['unit_name'];
			
			$dateopen = date('Y-m-d', strtotime('-1 day', strtotime($date)));
			$cartbalopen = $this->getcarretopenbydate($connection,$suppartyid,$unitid,$dateopen);
			
					 $salecaret = $this->getvalfield($connection,"saleenetry","sum(qty)","suppartyid='$suppartyid' and unitid='$unitid' && billdate='$row_get[date]'");
					 $recaret = $this->getvalfield($connection,"carretentry","sum(qty)","suppartyid='$suppartyid' and unitid='$unitid' && billdate='$row_get[date]' && is_sup=0");
					 
					 $returncaret = $this->getvalfield($connection,"carretentry","sum(qty)","suppartyid='$suppartyid' and unitid='$unitid' && billdate='$row_get[date]' && is_sup=1");
					 
					 $purcaret  = $this->getvalfield($connection,"purchaseentrydetail as A left join purchaseentry as B on A.purchaseid=B.purchaseid","sum(qty)","suppartyid='$suppartyid' and A.unitid='$unitid' && purchasedate='$row_get[date]'");
					 
					  $loadcaret  = $this->getvalfield($connection,"loaderentry as A left join loading as B on A.lodingid=B.lodingid","sum(qty)","suppartyid='$suppartyid' and A.unitid='$unitid' && loaddate='$row_get[date]'");
					 
					$tot = 0;				
					
					$net =  $cartbalopen - $recaret + $salecaret; 
					
					if($retaty_rec !='') { $recno = '- Rec. No.'.$retaty_rec; } else { $recno='';}
					if($salecaret !=0) { $msg.="Sale Carret Out /"; }
					if($recaret !=0) { $msg.="Sale Carret In $recno/"; }
					$process="Customer";
					$custarray[]=array($unit_name,$process,$msg,$salecaret,$recaret,-$recaret + $salecaret,$net);
					
					
					$msg='';
					
					if($returncaret !=0) { $msg.="Bijak Carret Out /"; }
					if($purcaret !=0) { $msg.="Bijak Carret In $recno/"; }
					$process="Supplier";
					$net =  $net - $purcaret+$returncaret;
					
					$supparray[] = array($unit_name,$process,$msg,$returncaret,$purcaret,-$purcaret+$returncaret,$net);
					
					
					$msg='';
					
					
					if($loadcaret !=0) { $msg.="Loading/"; }
					$process="Loader";
					$net =  $net + $loadcaret;
					
					$loadarray[] = array($unit_name,$process,$msg,$loadcaret,"0",$loadcaret,$net);
					
					
				

	} //unitm
	
	
	$newar = array($date,$custarray);			
	$roaker[] = $newar;
	
	$newar = array($date,$supparray);			
	$roaker[] = $newar;
	
	$newar = array($date,$loadarray);			
	$roaker[] = $newar;
		
}	
	
	return $roaker;
}




function getcarretroakerall($connection,$suppartyid,$fromdate,$todate,$unitid) {

$nettoral= 0;
$roaker= array();	

$sql_get = mysqli_query($connection,"Select Distinct(date) From (
select billdate as date from saleenetry where 1=1 and suppartyid='$suppartyid' and billdate between '$fromdate' and '$todate'
 UNION ALL select billdate as date from carretentry where 1=1 and suppartyid='$suppartyid'  and billdate between '$fromdate' and '$todate'
  UNION ALL  select purchasedate as date from purchaseentry where 1=1 and suppartyid='$suppartyid' and purchasedate between '$fromdate' and '$todate'
   UNION ALL select loaddate as date from loading where 1=1 and suppartyid='$suppartyid' and loaddate between '$fromdate' and '$todate')A group by date ORDER BY date");

while($row_get = mysqli_fetch_assoc($sql_get))
{
	//print_r($row_get);
	$date = $row_get['date'];				
	$msg='';
				
	$custarray = array();	
	$supparray=array();	
	$loadarray =array();	
	$sql = mysqli_query($connection,"select unitid,unit_name,unit_name_hindi from m_unit where isstockable=1 && unitid='$unitid' limit 1");
		while($row=mysqli_fetch_assoc($sql)) {
				
			$msg='';			
			$unitid = $row['unitid'];
			$unit_name = $row['unit_name'];
			
			
			$tot = 0;				
			$recaret=0;			
			$dateopen = date('Y-m-d', strtotime('-1 day', strtotime($date)));
			$cartbalopen = $this->getcarretopenbydate($connection,$suppartyid,$unitid,$dateopen);
			$net =  $cartbalopen;
			
		
		$salecaret = $this->getvalfield($connection,"saleenetry","sum(qty)","suppartyid='$suppartyid' and unitid='$unitid' && billdate='$row_get[date]'");
		if($salecaret !=0) {
		if($salecaret !=0) { $msg.="Sale Carret Out"; }
		$process="Customer";					
		$net +=  $salecaret; 
		$msg = "Sale";		
		$custarray[]=array($unit_name,$process,$msg,$salecaret,'',$salecaret,$net,$row_get['date']);
		}
		
	
		$sc= mysqli_query($connection,"select * from carretentry where suppartyid='$suppartyid' and unitid='$unitid' && billdate='$row_get[date]' && is_sup=1");
			while($rc=mysqli_fetch_assoc($sc)) {
			$process="Supplier";
			$recno = $rc['recno'];
			if($recno!='')
			$recno = '- Rec. No.'.$recno;
			else
			$recno='';
			
			$returncaret= $rc['qty'];
			
			$msg = "Purchase Carret Return"; 
			$net +=  $returncaret; 			
	$custarray[]=array($unit_name,$process,$msg,$returncaret,'',$returncaret,$net,$row_get['date']);					
			
		}
		
	
		$sc= mysqli_query($connection,"select A.* from loaderentry as A left join loading as B on A.lodingid=B.lodingid where suppartyid='$suppartyid' and A.unitid='$unitid' && loaddate='$row_get[date]'");
					while($rc=mysqli_fetch_assoc($sc)) {
					$process="Loader";
										 
					 $loadcaret= $rc['qty'];
					 
					 $msg = "Loading"; 
					 $net +=  $loadcaret; 
					 
					 $custarray[]=array($unit_name,$process,$msg,$loadcaret,"",$loadcaret,$net,$row_get['date']);					
						
					}	 
					
		
			
	$sc= mysqli_query($connection,"select * from carretentry where suppartyid='$suppartyid' and unitid='$unitid' && billdate='$row_get[date]' && is_sup=0");
		while($rc=mysqli_fetch_assoc($sc)) {
		$process="Customer";
		$recno = $rc['recno'];
		if($recno!='')
		$recno = '- Rec. No.'.$retaty_rec;
		else
		$recno='';
		$recaret= $rc['qty'];
		
		$msg = "Cus Caret Received"; 
		$net -=  $recaret; 
		
		$custarray[]=array($unit_name,$process,$msg,'',$recaret,'',$net,$row_get['date']);
	
	}
	
		$sc= mysqli_query($connection,"select A.* from purchaseentrydetail as A left join purchaseentry as B on A.purchaseid=B.purchaseid where suppartyid='$suppartyid' and A.unitid='$unitid' && purchasedate='$row_get[date]'");
			while($rc=mysqli_fetch_assoc($sc)) {
			$process="Supplier";
			
			$purcaret= $rc['qty'];
			
			$msg = "Purchase"; 
			$net -=  $purcaret; 
			
			$custarray[]=array($unit_name,$process,$msg,'',$purcaret,$purcaret,$net,$row_get['date']);					
			
			}
				
			
			}	//unit close	

	if(!empty($custarray)) {
	//$newar = array($date,$custarray);	
	$roaker[] = $custarray;
	}
		
} 
	
	return $roaker;
}//function close






function getcarretroakerall2($connection,$suppartyid,$fromdate,$todate,$unitid) {

$nettoral= 0;
$roaker= array();	

$sql_get = mysqli_query($connection,"Select Distinct(date) From (
select billdate as date from saleenetry where 1=1 and suppartyid='$suppartyid' and billdate between '$fromdate' and '$todate'
 UNION ALL select billdate as date from carretentry where 1=1 and suppartyid='$suppartyid'  and billdate between '$fromdate' and '$todate'
  UNION ALL  select purchasedate as date from purchaseentry where 1=1 and suppartyid='$suppartyid' and purchasedate between '$fromdate' and '$todate'
   UNION ALL select loaddate as date from loading where 1=1 and suppartyid='$suppartyid' and loaddate between '$fromdate' and '$todate')A group by date ORDER BY date");

while($row_get = mysqli_fetch_assoc($sql_get))
{
	//print_r($row_get);
	$date = $row_get['date'];				
	$msg='';
				
	$custarray = array();	
	$supparray=array();	
	$loadarray =array();	
	$sql = mysqli_query($connection,"select unitid,unit_name,unit_name_hindi from m_unit where isstockable=1 && unitid='$unitid' limit 1");
				while($row=mysqli_fetch_assoc($sql)) {
				
			$msg='';			
			$unitid = $row['unitid'];
			$unit_name = $row['unit_name'];
			
			$tot = 0;				
			$recaret=0;
			
			$dateopen = date('Y-m-d', strtotime('-1 day', strtotime($date)));
			$cartbalopen = $this->getcarretopenbydate($connection,$suppartyid,$unitid,$dateopen);
			$net =  $cartbalopen;
			
					 $salecaret = $this->getvalfield($connection,"saleenetry","sum(qty)","suppartyid='$suppartyid' and unitid='$unitid' && billdate='$row_get[date]'");
					if($salecaret !=0) {
					if($salecaret !=0) { $msg.="Sale Carret Out "; }
					$process="Customer";					
					$net +=  $salecaret; 
					$msg = "Sale Carret Out ";
					
					$custarray[]=array($unit_name,$process,$msg,$salecaret,'',$salecaret,$net,$row_get['date']);
				}
					
					$sc= mysqli_query($connection,"select * from carretentry where suppartyid='$suppartyid' and unitid='$unitid' && billdate='$row_get[date]' && is_sup=0");
					while($rc=mysqli_fetch_assoc($sc)) {
					$process="Customer";
					$recno = $rc['recno'];
					if($recno!='')
					 $recno = '- Rec. No.'.$retaty_rec;
					else
					 $recno='';
					 $recaret= $rc['qty'];
					 
					 $msg = "Sale Carret In $recno"; 
					 $net -=  $recaret; 
					 
					 $custarray[]=array($unit_name,$process,$msg,'',$recaret,'',$net,$row_get['date']);
						
					}
					
					
					
					$sc= mysqli_query($connection,"select * from carretentry where suppartyid='$suppartyid' and unitid='$unitid' && billdate='$row_get[date]' && is_sup=1");
					while($rc=mysqli_fetch_assoc($sc)) {
					$process="Supplier";
					$recno = $rc['recno'];
					if($recno!='')
					 $recno = '- Rec. No.'.$recno;
					else
					 $recno='';
					 
					 $returncaret= $rc['qty'];
					 
					 $msg = "Bijak Carret Out $recno"; 
					 $net +=  $returncaret; 
					 
					 $custarray[]=array($unit_name,$process,$msg,$returncaret,'',$returncaret,$net,$row_get['date']);					
						
					}
					
					
					
					$sc= mysqli_query($connection,"select A.* from purchaseentrydetail as A left join purchaseentry as B on A.purchaseid=B.purchaseid where suppartyid='$suppartyid' and A.unitid='$unitid' && purchasedate='$row_get[date]'");
					while($rc=mysqli_fetch_assoc($sc)) {
					$process="Supplier";
										 
					 $purcaret= $rc['qty'];
					 
					 $msg = "Bijak Carret In "; 
					 $net -=  $purcaret; 
					 
					 $custarray[]=array($unit_name,$process,$msg,$purcaret,'',$purcaret,$net,$row_get['date']);					
						
					}
					
					
					$sc= mysqli_query($connection,"select A.* from loaderentry as A left join loading as B on A.lodingid=B.lodingid where suppartyid='$suppartyid' and A.unitid='$unitid' && loaddate='$row_get[date]'");
					while($rc=mysqli_fetch_assoc($sc)) {
					$process="Loader";
										 
					 $loadcaret= $rc['qty'];
					 
					 $msg = "Loading"; 
					 $net +=  $loadcaret; 
					 
					 $custarray[]=array($unit_name,$process,$msg,$loadcaret,"0",$loadcaret,$net,$row_get['date']);					
						
					}	 
					
					 
					
					 
					
					 
					
					  
					  /*
					 
					
					
					if($retaty_rec !='') { $recno = '- Rec. No.'.$retaty_rec; } else { $recno='';}
					if($salecaret !=0) { $msg.="Sale Carret Out /"; }
					if($recaret !=0) { $msg.="Sale Carret In $recno/"; }
					$process="Customer";
					$custarray[]=array($unit_name,$process,$msg,$salecaret,$recaret,-$recaret + $salecaret,$net);
					
					
					$msg='';
					
					if($returncaret !=0) { $msg.="Bijak Carret Out /"; }
					if($purcaret !=0) { $msg.="Bijak Carret In $recno/"; }
					$process="Supplier";
					$net =  $net - $purcaret+$returncaret;
					
					$supparray[] = array($unit_name,$process,$msg,$returncaret,$purcaret,-$purcaret+$returncaret,$net);
					
					
					$msg='';
					
					
					if($loadcaret !=0) { $msg.="Loading/"; }
					$process="Loader";
					$net =  $net + $loadcaret;
					
					$loadarray[] = array($unit_name,$process,$msg,$loadcaret,"0",$loadcaret,$net);
					
					
			*/	

	} //unitm
	
	
	//$newar = array($date,$custarray);			
	$roaker[] = $custarray;
	
	
}	
	
	return $roaker;
}



}
?>