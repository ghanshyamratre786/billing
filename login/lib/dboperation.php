<?php  define("COMPANY","SHAKAMBHARI VEGETABLE COMPANY");
	define("COMPANY_HINDI","शाकंभरी वेजिटेबल कंपनीा");
	define("COMPANYMOBILE","9907050601");
	define("COMPANYADDRESS","Thok Sabji Market, Dumartarai, Raipur (C.G.)");
	define("COMPANYADDRESSHINDI","थोक सब्जी मंडी,  डूमरतराई, रायपुर (छ.ग.)");
	define("SOFTWAREINSTALL","01-05-2021"); 
 
//sql injection prevention
function test_input($data) {
  $data = trim($data);
  $data = addslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
// To encrypt data based on key //
function encrypt($string, $key)
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
// To decrypt data based on key //
function decrypt($string, $key)
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
function dbRowSelect($table_name, $form_data, $condition)
{
    // retrieve the keys of the array (column titles)
    $fields = $form_data;
	//print_r($fields);die;
    // build the query
    $sql = "SELECT ".implode(',', $fields)." from ".$table_name." ".$condition;
	 // run and return the query result resource

	if($res= mysqli_query($connection,$sql))
	{

		while($row = mysqli_fetch_array($res))
		{
		  $result[] = $row;
		}
	}
	return $result;
}


function showdtable($table_name, $form_data, $condition)
{
    // retrieve the keys of the array (column titles)
    $fields = $form_data;
	//print_r($fields);die;
    // build the query
	$sqlprimary = "SHOW KEYS FROM $table_name WHERE Key_name = 'PRIMARY'";
	$resprimary = mysqli_query($connection,$sqlprimary);
	$rowprimary = mysqli_fetch_array($resprimary);
	$primary_key = $rowprimary['Column_name'];
	
    $sql = "SELECT $primary_key, ".implode(',', $fields)." from ".$table_name." ".$condition;
	 // run and return the query result resource
	
	
	if($res = mysqli_query($connection,$sql))
	{
		$htmltable ="";
		$slno = 1;
		while($row = mysqli_fetch_row($res))
		{
		  $keyval = $row[0];
		  $htmltable .= "<tr><td>$slno</td>".create_td($row)."<td><a class='btn btn-info btn-xs' href='?$primary_key=$keyval'>Edit</a></td><td><a class='btn btn-danger btn-xs' onclick='funDel($keyval);' style='cursor:pointer'>Delete</a></td></tr>";
		  $slno++;
		}
	}
	return $htmltable;
}


function sendsmsGET($mobileNumber,$senderId,$routeId,$message,$serverUrl,$authKey)
{
	//echo $authKey; die;
    $getData = 'mobileNos='.$mobileNumber.'&message='.urlencode($message).'&senderId='.$senderId.'&routeId='.$routeId.'&smsContentType=Unicode';

    //API URL
    $url="http://".$serverUrl."/rest/services/sendSMS/sendGroupSms?AUTH_KEY=".$authKey."&".$getData;
	 
	 //echo $url;
	// die;

    // init the resource
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0

    ));


    //get response
    $output = curl_exec($ch);
	
	//print_r($output);
    //Print error if any
    if(curl_errno($ch))
    {
        echo 'error:' . curl_error($ch);
    }

    curl_close($ch);

    return $output;
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
function create_td($row)
{
	//print_r($row);
	$td_data = "";
    for($j = 1; $j < count($row); $j++)
	{
        $td_data .= "<td>".ucfirst($row[$j])."</td>";
    }
	//$td_data .= "</tr>";
	return $td_data;
}


function check_duplicate($connection,$table_name,$where)
{
	$sql = "select * from $table_name where $where";
	$res = mysqli_query($connection,$sql);
	$cnt = mysqli_num_rows($res);
	return $cnt;
}



function dbRowInsert($connection,$table_name, $form_data)
{
    // retrieve the keys of the array (column titles)
    $fields = array_keys($form_data);

    // build the query
     $sql = "INSERT INTO ".$table_name."
    (`".implode('`,`', $fields)."`)
    VALUES('".implode("','", $form_data)."')";
	//echo $sql;
    //die;
    // run and return the query result resource
	mysqli_query($connection,"SET NAMES utf8");
    return mysqli_query($connection,$sql);
}


// the where clause is left optional incase the user wants to delete every row!
function dbRowDelete($table_name, $where_clause='')
{
    // check for optional where clause
    $whereSQL = '';
    if(!empty($where_clause))
    {
        // check to see if the 'where' keyword exists
        if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
        {
            // not found, add keyword
            $whereSQL = " WHERE ".$where_clause;
        } else
        {
            $whereSQL = " ".trim($where_clause);
        }
    }
    // build the query
    $sql = "DELETE FROM ".$table_name.$whereSQL;

    // run and return the query result resource
    return mysqli_query($connection,$sql);
}


// again where clause is left optional
function dbRowUpdate($connection,$table_name, $form_data, $where_clause='')
{
    // check for optional where clause
    $whereSQL = '';
    if(!empty($where_clause))
    {
        // check to see if the 'where' keyword exists
        if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
        {
            // not found, add key word
            $whereSQL = " WHERE ".$where_clause;
        } 
		else
        {
            $whereSQL = " ".trim($where_clause);
        }
    }
    // start the actual SQL statement
    $sql = "UPDATE ".$table_name." SET ";

    // loop and build the column /
    $sets = array();
    foreach($form_data as $column => $value)
    {
         $sets[] = "`".$column."` = '".$value."'";
    }
    $sql .= implode(', ', $sets);
	
    // append the where statement
    $sql .= $whereSQL;
	//echo $sql; die;
    // run and return the query result
	mysqli_query($connection,"SET NAMES utf8");
    return mysqli_query($connection,$sql);
}


function dbInfo()
{
	if($_SERVER["SERVER_NAME"]=="localhost" || $_SERVER["SERVER_NAME"]=="trinityhome")
	{
		$host_name="localhost";
		$db_name="nipesh";
		$db_user="root";
		$db_pwd="";
	}
	else
	{
		//$host_name="";
		//$db_name="";
		//$db_user="";
		//$db_pwd="";
		echo "Online Db Connection Error";
	}

	$db_link =mysql_connect("$host_name","$db_user","$db_pwd");
	mysql_select_db("$db_name",$db_link);
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
		$getval .= ", ". $row[0];
	}
	return $getval;
}


// get value from any condition //
function getvalfield($table,$field,$where)
{
	$sql = "select $field from $table where $where";
	//echo $sql;
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
	$ndate = explode("-",$date);
	$year = $ndate[2];
	$day = $ndate[0];
	$month = $ndate[1];
	
	return $year . "-" . $month . "-" . $day;
}

function selectsimple($tablename,$condition)
{
	$rows = array();
	$result = mysqli_query($connection,"select * from $tablename where $condition");
	if($result === false) {
		return false;
	}
	while ($row = mysqli_fetch_array($result)) {
		$rows[] = $row;
	}
	return $rows;
}

function selectjoin($query)
{
	$rows = array();
	$result = mysqli_query($connection,$query);
	if($result === false) {
		return false;
	}
	while ($row = mysqli_fetch_array($result)) {
		$rows[] = $row;
	}
	return $rows;
}


function quote($value)
{
        return "'" .mysql_real_escape_string($value) . "'";
}


// get total no. of rows //
function getTotalNum($table,$where)
{
	$sql = "select * from $table where $where";
	//echo $sql;
	$getvalue = mysqli_query($connection,$sql);
	$getval = mysqli_num_rows($getvalue);
 
	return $getval;
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


//send sms code
function sendsms($smsuname,$smspass,$smssender,$msg,$mobile)
{
	//echo "Called";
	//http://bulksms.trinitysolutions.pw/sendsms.jsp?user=miliap&password=Panav@27&sms=hello&mobiles=9770131555&sender=MILAAP
	$request = ""; //initialize the request variable
	$param["user"] = $smsuname; //this is the username of our TM4B account
	$param["password"] = $smspass; //this is the password of our TM4B account
	$param["sender"] = $smssender;//this is our sender 
	$param["sms"] = $msg; //this is the message that we want to send
	$param["mobiles"] = $mobile; //these are the recipients of the message
			
	foreach($param as $key=>$val) //traverse through each member of the param array
	{ 
		$request.= $key."=".urlencode($val); //we have to urlencode the values
		$request.= "&"; //append the ampersand (&) sign after each paramter/value pair
	}
	$request = substr($request, 0, strlen($request)-1); //remove the final ampersand sign from the request
	
	//die;
	//First prepare the info that relates to the connection
	$host = "bulksms.trinitysolutions.pw";
	$script = "/sendsms.jsp";
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

function SelectDB($table_name,$where_clause)
{
	// check for optional where clause
	$whereSQL = '';
	if(!empty($where_clause))
	{
		// check to see if the ‘where’ keyword exists
		if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
		{
			// not found, add key word
			$whereSQL = "WHERE ".$where_clause;
		}
		else
		{
			$whereSQL = " ".trim($where_clause);
		}
	}
	// build the query
	 $sql = "SELECT * FROM ".$table_name." ".$whereSQL;
	// run and return the query result resource
	return mysqli_query($connection,$sql);
}


// ver. 1.01 - now its possible to use this function without the label element
function create_form_field($formelement, $label = "", $db_value = "", $length = 25) {
    $form_field = ($label != "") ? "<label for=\"".$formelement."\">".$label."</label>\n" : "";
    $form_field .= "  <input name=\"".$formelement."\" id=\"".$formelement."\" type=\"text\" size=\"".$length."\" value=\"";
    if (isset($_REQUEST[$formelement])) {
        $form_field .= $_REQUEST[$formelement];
    } elseif (isset($db_value) && !isset($_REQUEST[$formelement])) {
        $form_field .= $db_value;
    } else {
        $form_field .= "";
    }
    $form_field .= "\">\n";
    return $form_field;        
}


function check_email($mail_address) {
    $pattern = "/^[\w-]+(\.[\w-]+)*@";
    $pattern .= "([0-9a-z][0-9a-z-]*[0-9a-z]\.)+([a-z]{2,4})$/i";
    if (preg_match($pattern, $mail_address)) {
        $parts = explode("@", $mail_address);
        if (checkdnsrr($parts[1], "MX")){
            echo "The e-mail address is valid.";
            // return true;
        } else {
            echo "The e-mail host is not valid.";
            // return false;
        }
    } else {
        echo "The e-mail address contains invalid charcters.";
        // return false;
    }
}

// example sql statement
//$sql = "SELECT col_for_value, col_for_label FROM some_table";
function database_select($tbl_value, $tbl_label, $select_name, $label, $init_val = "") {
    $sql = "select * from student";
    $result = mysqli_query($connection,$sql);
    //$menu = "<label for=\"".$select_name."\">".$label."</label>\n";
    $menu .= "<select name=\"".$select_name."\" id=\"".$select_name."\">\n";
    $curr_val = (isset($_REQUEST[$select_name])) ? $_REQUEST[$select_name] : $init_val;
    $menu .= ($curr_val == "") ? "  <option value=\"\" selected>...\n" : "<option value=\"\">...\n";
    while ($obj = mysqli_fetch_object($result)) {
        $menu .= "  <option value=\"".$obj->$tbl_value."\"";
        $menu .= ($obj->$tbl_value == $curr_val) ? " selected" : "";
        $menu .= ">".$obj->$tbl_label."\n";
    }
    $menu .= "</select>\n";
    mysql_free_result($result);
    return $menu;
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

function uploadImage($imgpath,$docname)
{

	
     if(1==1)
	 {
		
		 $doc_name = $docname['name'];
		$tm="DOC";
		$tm.=microtime(true)*1000;
		$ext = pathinfo($doc_name, PATHINFO_EXTENSION);
		$doc_name=$tm.".".$ext;
		//echo $imgpath."$doc_name";die;
		if(move_uploaded_file($docname['tmp_name'],$imgpath."$doc_name"))
		{
			return($doc_name);
		}
		else
		{
			return("");
		}
			
   }
   else
   {
         return("0");
   }
}


?>