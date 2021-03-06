<?php
include_once "system.php" ;
include_once "menu.php";
include_once 'class/Product.php';
include_once 'class/Room.php';
include_once 'class/Employee.php';
include_once 'class/TuitionClass.php';
include_once 'class/Period.php';
include_once "datepicker/class.datepicker.php";
include_once 'class/Log.php';
include_once 'class/ProductCategory.php';

$log = new Log();
$dp=new datepicker("$url");
$dp->dateFormat='Y-m-d';

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

$p = new Product($xoopsDB,$tableprefix,$log);
$rm = new Room($xoopsDB,$tableprefix,$log);
$o = new TuitionClass($xoopsDB,$tableprefix,$log);
$s = new XoopsSecurity($xoopsDB,$tableprefix,$log);
$e = new Employee($xoopsDB,$tableprefix,$log);
$c = new ProductCategory($xoopsDB,$tableprefix,$log);
$period = new Period($xoopsDB,$tableprefix,$log);

if($o->organization_id == "")
$o->organization_id = $defaultorganization_id;

$o->orgctrl="";
$o->productctrl="";
$o->seachperiodctrl=$period->getPeriodList(-1);
$o->seachorgctrl=$permission->selectionOrg($o->createdby,$defaultorganization_id);
$o->seachempctrl=$e->getEmployeeList(0,'M','employee_id','Y');
$o->seachproductctrl=$p->getSelectProduct(0,'C','','Y');
$o->seachcategoryctrl=$c->getSelectCategory(0,'Y');

$o->cur_name=$cur_name;
$o->cur_symbol=$cur_symbol;
$action="";


$showdatefrom=$dp->show("datefrom");
$showdateto=$dp->show("dateto");


echo <<< EOF
<script type="text/javascript">
	function autofocus(){
		var desc=document.forms['frmTutionClass'].description.value;
		if(desc=="")
		updateDescription();
		triggerclasstype();
		
		document.forms['frmSearchClass'].tuitionclass_code.focus();
	}

	function validateClass(){




		if(confirm("Confirm to save this record?")){
		var starttime=document.forms['frmTutionClass'].starttime.value;
		var endtime=document.forms['frmTutionClass'].endtime.value;
		var classcode = document.forms['frmTutionClass'].tuitionclass_code.value;
		var hours = document.forms['frmTutionClass'].hours.value;
		var classcount = document.forms['frmTutionClass'].classcount.value;
		var product_id = document.forms['frmTutionClass'].product_id.value;
		var period_id = document.forms['frmTutionClass'].period_id.value;
		var employee_id = document.forms['frmTutionClass'].employee_id.value;
		var description = document.forms['frmTutionClass'].description.value;


		if(!IsNumeric(starttime)   || classcode=="" || !IsNumeric(endtime) ||
			 !IsNumeric(hours) || description ==''|| hours =='' || classcount >20 || period_id==0 || product_id==0
			){
			alert('Please fill in classcode, description, product, period and make sure start time,end time, and hour is fill in by number, if you choose special class type, make sure "Class Count" less than 20');
			return false;
		}
		else
			return true;
		}
		else
			return false;
	}
	function updateDescription(){
		var w = document.forms['frmTutionClass'].product_id.selectedIndex;
		var selected_text =document.forms['frmTutionClass'].product_id.options[w].text;
		document.forms['frmTutionClass'].description.value=selected_text;
	}

	function zoomProduct(){
		var id = document.forms['frmTutionClass'].product_id.value;
		window.open("product.php?action=edit&product_id="+id);
	}
	function zoomTutor(){
		var id = document.forms['frmTutionClass'].employee_id.value;
		window.open("employee.php?action=edit&employee_id="+id);
	}

	function triggerclasstype(){
		//var w = document.forms['frmTutionClass'].day.selectedIndex;
		//var selected_text =document.forms['frmTutionClass'].day.options[w].text;

		var classtype=document.forms['frmTutionClass'].classtype.value;

		switch(classtype){
			case "W":
				document.forms['frmTutionClass'].classcount.value="5";
				document.getElementById("weeklyview").style.display='';
				document.getElementById("weekly2view").style.display='none';
				document.getElementById("monthlyview").style.display='none';
				document.getElementById("specialview").style.display='none';
 				document.getElementById("scheduleview").style.display='';
 				document.getElementById("specialwarning").style.display='none';

			break;
			case "V":
				document.forms['frmTutionClass'].classcount.value="10";
				document.getElementById("weeklyview").style.display='';
				document.getElementById("weekly2view").style.display='';
				document.getElementById("monthlyview").style.display='none';
				document.getElementById("specialview").style.display='none';
 				document.getElementById("scheduleview").style.display='';
 				document.getElementById("specialwarning").style.display='none';

			break;
			case "M":
				document.forms['frmTutionClass'].classcount.value="0";
				document.getElementById("weeklyview").style.display='none';
				document.getElementById("weekly2view").style.display='none';
 				document.getElementById("monthlyview").style.display='';
 				document.getElementById("specialview").style.display='none';
 				document.getElementById("scheduleview").style.display='none';
 				document.getElementById("specialwarning").style.display='none';

			break;
			case "S":
//				document.forms['frmTutionClass'].classcount.value="9";
				//document.forms['frmTutionClass'].daycount="5";
				document.getElementById("weeklyview").style.display='none';
				document.getElementById("weekly2view").style.display='none';
 				document.getElementById("monthlyview").style.display='none';
 				document.getElementById("specialview").style.display='';
 				document.getElementById("scheduleview").style.display='';
 				document.getElementById("specialwarning").style.display='';

			break;
			default:
				document.forms['frmTutionClass'].classcount.value="5";
				document.getElementById("weeklyview").style.display='';
 				document.getElementById("monthlyview").style.display='none';
 				document.getElementById("specialview").style.display='none';
 				document.getElementById("scheduleview").style.display='';
 				document.getElementById("specialwarning").style.display='none';

			break;
		}
		
	}
</script>

EOF;


if (isset($_POST['action'])){
	$action=$_POST['action'];
	$o->tuitionclass_id=$_POST["tuitionclass_id"];

}
elseif(isset($_GET['action'])){
	$action=$_GET['action'];
	$o->tuitionclass_id=$_GET["tuitionclass_id"];

}
else
$action="";


$log->showLog(3,"Retrieving Tution Class ID: $o->tuitionclass_id");
$token=$_POST['token'];
$o->product_id=$_POST["product_id"];
$o->period_id=$_POST["period_id"];
$o->employee_id=$_POST["employee_id"];//tutor
$o->description=$_POST['description'];
$o->day=$_POST['day'];
$o->day2=$_POST['day2'];
$o->room_id=$_POST['room_id'];
$o->room_id2=$_POST['room_id2'];

$o->classcount=$_POST['classcount'];
$o->oldclasscount=$_POST['oldclasscount'];
$o->oldclasstype=$_POST['oldclasstype'];
$o->classtype=$_POST['classtype'];
$o->starttime=$_POST['starttime'];
$o->starttime2=$_POST['starttime2'];
$o->endtime=$_POST['endtime'];
$o->endtime2=$_POST['endtime2'];
$o->organization_id=$_POST['organization_id'];
$o->tuitionclass_code=$_POST['tuitionclass_code'];
$isactive=$_POST['isactive'];
$o->hours=$_POST['hours'];
$o->createdby=$xoopsUser->getVar('uid');
$o->isAdmin=$xoopsUser->isAdmin();
$o->updatedby=$xoopsUser->getVar('uid');
$o->deleteAttachment=$_POST['deleteAttachment'];

$tmpfile= $_FILES["upload_file"]["tmp_name"];
$filesize=$_FILES["upload_file"]["size"] / 1024;
$filetype=$_FILES["upload_file"]["type"];

$o->created= date("y/m/d H:i:s", time()) ;
$o->updated=$o->created;
$o->showSearchForm();
if ($isactive=="Y" or $isactive=="on")
	$o->isactive='Y';
else
	$o->isactive='N';

 switch ($action){
	//When user submit new organization
  case "create" :
		// if the token is exist and not yet expired


	if ($s->check(false,$token,"CREATE_CLASS")){
		$log->showLog(4,"Accessing create class event, with product_id=$o->product_id");
		if($o->insertTuitionClass()){

			
			
			if($filesize>0 || $filetype=='application/pdf')
			$o->savefile($tmpfile);
			$latest_id=$o->getLatestClassID();
			$o->tuitionclass_id=$latest_id;

			if($o->classtype=='W')
				$o->genDefaultDate($o->day,$o->period_id);
			elseif($o->classtype=='V')
				$o->genDefaultDate2($o->period_id);
			elseif($o->classtype=='S')
				$o->genEmptyDate($o->tuitionclass_id,$o->classcount-$daycount);
			redirect_header("tuitionclass.php?action=edit&tuitionclass_id=$latest_id",$pausetime,"Your data is saved, the new id=$latest_id");
		}
	else {
		$log->showLog(1,"Warning! '$o->tuitionclass_code' cannot save, please verified your data!");
		$token=$s->createToken($tokenlife,"CREATE_CLASS");
		$o->productctrl=$p->getSelectProduct($o->product_id,'Y','onChanged="updateDescription()"');
		$o->periodctrl=$period->getPeriodList($o->period_id);
		$o->orgctrl=$permission->selectionOrg($o->createdby,$o->organization_id);
		$o->employeectrl=$e->getEmployeeList($o->employee_id);
		$o->getInputForm("new",-1,$token);
		$o->room1ctrl=$rm->getSelectRoom($o->room_id,'Y','room_id');
		$o->room2ctrl=$rm->getSelectRoom($o->room_id2,'Y','room_id2');
		$o->showTuitionClassTable(); 
		}
	}
	else{	// if the token is not valid or the token is expired, it back to previous form with previous inputed data
		$log->showLog(1,"Warning! '$o->tuitionclass_code' cannot save due to token expired!");
		$token=$s->createToken($tokenlife,"CREATE_CLASS");
		$o->productctrl=$p->getSelectProduct($o->product_id,'Y','onChanged="updateDescription()"');
		$o->periodctrl=$period->getPeriodList($o->period_id);
		$o->room1ctrl=$rm->getSelectRoom($o->room_id,'Y','room_id');
		$o->room2ctrl=$rm->getSelectRoom($o->room_id2,'Y','room_id2');
		$o->orgctrl=$permission->selectionOrg($o->createdby,$o->organization_id);
		$o->employeectrl=$e->getEmployeeList($o->employee_id);
		$o->getInputForm("new",-1,$token);
		$o->showTuitionClassTable(); 
	}
 
break;
	//when user request to edit particular organization
  case "edit" :
	if($o->fetchTuitionClassInfo($o->tuitionclass_id)){
/*
	$o->orgctrl=$permission->selectionOrg($o->createdby,0);
	$o->productctrl=$p->getSelectProduct(0,'C','onChange="updateDescription()"' ,'Y');
	$o->periodctrl=$period->getPeriodList(0,'period_id','Y');
	$o->employeectrl=$e->getEmployeeList(0,'M','employee_id','Y');
*/
		$o->orgctrl=$permission->selectionOrg($o->createdby,$o->organization_id);
		$o->productctrl=$p->getSelectProduct($o->product_id,'C','onChange="updateDescription()"','Y');
		$o->periodctrl=$period->getPeriodList($o->period_id,'period_id','Y');
		$o->employeectrl=$e->getEmployeeList($o->employee_id,0,'employee_id','Y');
		//create a new token for editing a form
		$token=$s->createToken($tokenlife,"CREATE_CLASS");
		$o->schedulectrl=$o->showschedule($o->tuitionclass_id);
		$o->testctrl=$o->showTest($o->tuitionclass_id);
		$o->room1ctrl=$rm->getSelectRoom($o->room_id,'Y','room_id');
		$o->room2ctrl=$rm->getSelectRoom($o->room_id2,'Y','room_id2');
		$o->getInputForm("edit",$o->tuitionclass_id,$token);
	//	$o->showTuitionClassTable(); 
	}
	else	//if can't find particular organization from database, return error message
		redirect_header("tuitionclass.php",3,"<b style='color:red'>Some error on viewing your product data, probably database corrupted.</b>");

break;
  case "search" :
	
	$active=$_POST['active'];
	$tuitionclass_code=$_POST['tuitionclass_code'];
	$period_id=$_POST['period_id'];
	$organization_id=$_POST['organization_id'];
	$employee_id=$_POST['employee_id'];
	$product_id=$_POST['product_id'];
	$category_id=$_POST['category_id'];

	$log->showLog(3,"search tuitionclass with parameter:$active,$tuitionclass_code,$period_id, 
			$organization_id,$employee_id");
	$wherestr=genWhereString($tuitionclass_code,$active,$period_id,$log,$organization_id,$employee_id,$product_id,$category_id);
	$token=$s->createToken($tokenlife,"CREATE_CLASS");
	//$o->categoryctrl=$c->getSelectCategory(0);
	$o->orgctrl=$permission->selectionOrg($o->createdby,$defaultorganization_id);
	$o->productctrl=$p->getSelectProduct(0,'C','onChange="updateDescription()"','Y');
	$o->periodctrl=$period->getPeriodList(0,'period_id','Y');
	$o->employeectrl=$e->getEmployeeList(0,0,'employee_id','Y');
	$o->tuitionclass_code="";
	$o->room1ctrl=$rm->getSelectRoom(0,'Y','room_id');
	$o->room2ctrl=$rm->getSelectRoom(0,'Y','room_id2');

	$o->getInputForm("new",0,$token);
	$o->showMiniTuitionClassTable($wherestr);
break;

//when user press save for change existing organization data
  case "update" :
	if ($s->check(false,$token,"CREATE_CLASS")){
		$o->updatedby=$xoopsUser->getVar('uid'); //get current uid
		if($o->updateTuitionClass()) {//if data save successfully
			if($o->deleteAttachment=='on')
				$o->deletefile();
			if($filesize>0 || $filetype=='application/pdf')
				$o->savefile($tmpfile);
			if($o->oldclasstype!=$o->classtype || $o->oldclasscount!=$o->classcount){
				switch($o->classtype){
				 case 'M':
					$o->removeschedule($o->tuitionclass_id);
				 break;
				 case 'W':
					$id=$o->tuitionclass_id;
					$daycount=$o->getScheduleCount($id);

					if($o->oldclasscount==0)
					$o->genDefaultDate($o->day,$o->period_id);
					elseif($o->oldclasscount<5)
					$o->genEmptyDate($id,5-$daycount);
					elseif($o->oldclasscount>5)
					$o->removeschedule($id,$daycount-5);

				 break;
				 case 'V':
					$id=$o->tuitionclass_id;
					$daycount=$o->getScheduleCount($id);
					if($o->oldclasscount==0)
					$o->genDefaultDate2($o->period_id);
					elseif($o->oldclasscount<10)
					$o->genEmptyDate($id,10-$daycount);
					elseif($o->oldclasscount>10)
					$o->removeschedule($id,$daycount-10);

				 break;
				 case 'S':
					$daycount=$o->getScheduleCount($o->tuitionclass_id);
					if($daycount< $o->classcount)
					$o->genEmptyDate($o->tuitionclass_id,$o->classcount-$daycount);
					elseif($daycount>$o->classcount)
					$o->removeschedule($o->tuitionclass_id,$daycount-$o->classcount);	

				 break;
				}
			}
			redirect_header("tuitionclass.php?action=edit&tuitionclass_id=$o->tuitionclass_id",$pausetime,"Your data is saved.");
			}
		else
			redirect_header("tuitionclass.php?action=edit&tuitionclass_id=$o->tuitionclass_id",$pausetime,"<b style='color:red'>Warning! Can't save the data, please make sure all value is insert properly.</b>");
		}
	else{
		redirect_header("tuitionclass.php?action=edit&tuitionclass_id=$o->tuitionclass_id",$pausetime,"<b style='color:red'>Warning! Can't save the data due to token expired.</b>");
	}
  break;
  case "delete" :
	if ($s->check(false,$token,"CREATE_CLASS")){
		if($o->deleteTuitionClass($o->tuitionclass_id)){
			$o->deletefile($o->tuitionclass_id);
			
			redirect_header("tuitionclass.php",$pausetime,"Data removed successfully.");
			}
		else
			redirect_header("tuitionclass.php?action=edit&tuitionclass_id=$o->tuitionclass_id",$pausetime,"<b style='color:red'>Warning! Can't delete data from database due to data dependency error.</b>");
	}
	else
		redirect_header("tuitionclass.php?action=edit&tuitionclass_id=$o->tuitionclass_id",$pausetime,"<b style='color:red'>Warning! Can't delete data from database due to token expired.</b>");
	
  break;
  case "cloneclass":
	$o->cloneclass($o->tuitionclass_id);
	$latest_id=$o->getLatestClassID();
	redirect_header("tuitionclass.php?action=edit&tuitionclass_id=$latest_id",$pausetime,"Your data is cloned, redirect to new class.");

  break;
  case "reschedule":
	if($o->fetchTuitionClassInfo($o->tuitionclass_id)){
		$o->removeschedule($o->tuitionclass_id);
		$id=$o->tuitionclass_id;
		$daycount=$o->getScheduleCount($id);
		if($o->classtype=="W")
		{	$o->removeschedule($id,$daycount-5);
		//	if($daycount==0)
			$o->genDefaultDate($o->day,$o->period_id);
		//	elseif($daycount<5)
		//	$o->genEmptyDate($id,5-$daycount);
		//	elseif($day>5)
			
			redirect_header("tuitionclass.php?action=edit&tuitionclass_id=$id",$pausetime,"Data re-scheduled, refresh this data.");
		}
		elseif($o->classtype=="V")
		{
			$o->removeschedule($id,$daycount-10);
//			if($daycount==0)
			$o->genDefaultDate2($o->period_id);
			//elseif($daycount<10)
		//	$o->genEmptyDate($id,5-$daycount);
		//	elseif($day>10)
		//	$o->removeschedule($id,$daycount-10);
		redirect_header("tuitionclass.php?action=edit&tuitionclass_id=$id",$pausetime,"Data re-scheduled, refresh this data.");
		}
		else
	redirect_header("tuitionclass.php?action=edit&tuitionclass_id=$id",$pausetime,"This data cannot re-scheduled. Redirect back to this record");
	
	}


  break;
  default :
	$token=$s->createToken($tokenlife,"CREATE_CLASS");
	//$o->categoryctrl=$c->getSelectCategory(0);
	$o->orgctrl=$permission->selectionOrg($o->createdby,$defaultorganization_id);
	$o->productctrl=$p->getSelectProduct(0,'C','onChange="updateDescription()"' ,'Y');
	$o->periodctrl=$period->getPeriodList(0,'period_id','Y');
	$o->employeectrl=$e->getEmployeeList(0,'M','employee_id','Y');
	$o->room1ctrl=$rm->getSelectRoom(0,'Y','room_id');
	$o->room2ctrl=$rm->getSelectRoom(0,'Y','room_id2');
	
	$o->getInputForm("new",0,$token);
	//$o->showTuitionClassTable();
  break;

}
echo '</td>';
require(XOOPS_ROOT_PATH.'/footer.php');

function genWhereString($tuitionclass_code,$active,$period_id,$log,$organization_id,$employee_id,$product_id,$category_id){

$filterstring="";

//echo "<h1>a $active a</h1>";

if($tuitionclass_code!=""){
$filterstring="t.tuitionclass_code LIKE '$tuitionclass_code' AND";}

if($active=="Y" || $active=="N"){
$filterstring=$filterstring . "  $needand t.isactive = '$active' AND";}

if($category_id!=0)
$filterstring=$filterstring . " $needand p.category_id=$category_id AND";

if($product_id!=0)
$filterstring=$filterstring . " $needand t.product_id=$product_id AND";

if($period_id!=0)
$filterstring=$filterstring . " $needand t.period_id=$period_id AND";

if($employee_id!=0)
$filterstring=$filterstring . " $needand t.employee_id=$employee_id AND";



if ($filterstring==""){
$log->showLog(3,"Return filtering string: $filterstring");
	return "WHERE t.tuitionclass_id>0 and t.organization_id=$organization_id";
	}
else {

	$filterstring =substr_replace($filterstring,"",-3);  
$log->showLog(3,"Return filtering string: $filterstring");
	return "WHERE t.tuitionclass_id>0 AND t.organization_id=$organization_id AND $filterstring";
	}
}

?>
