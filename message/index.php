	
<!DOCTYPE html>
<html>
<head>
<title>Package</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="../css/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="../css/cargo.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
	rel="stylesheet">
<link
	href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300'
	rel='stylesheet' type='text/css'>

<style>
i {
	color: #cc0099;
}

.paging a {
    text-decoration: underline;
}

a.current-page {
    text-decoration: none;
}

button,.button {
	color: #cc0099;
}

a {
	color: #cc0099;
}

th {
	background: #cc0099;
	border-right:1px solid #663300;
}

.undivide th {
	background: #E91E63;
}

.order-button:hover {
	color: #E91E63;
}


.wrap th {
	width: 32%;
}
</style>
<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../css/jquery-ui-timepicker-addon.min.css"></script>
<script src="../js/jquery-ui-timepicker-addon.min.js"></script>
<script src="js/ajaxlib.js"></script>
<script src="js/util.js"></script>
<script src="js/packagelib.js"></script>
<script src="js/package_ui_events.js"></script>
<script>
	$(function() {
		$( "#dform,#dto" ).datepicker({
	        dateFormat: "yy-mm-dd"
		});

		//dbclick tr class detail
		$( "table.detail tr" ).dblclick(function() {
		  //alert( "Handler for .dblclick() called." );
		  var id = $(this).attr("id");
		  var eid = $(this).attr("eid");
		  var cid = $(this).attr("cid");
		  var opt = $(this).attr("opt");
		 detail(eid,cid,id,opt);
		});


	});

function exportExcel(){
	window.open('message_excel.php','_blank');
}
var editOn = false;
function detail(eid,cid,id,opt){
	window.location.href = './message-detail.php?eid='+eid+'&cid='+cid+'&id='+id+'&opt='+opt;
}
var searchOn=false;

function searchBox(){
	searchOn = !searchOn;
	if(searchOn){
		document.getElementById('searchBox').style.visibility = 'visible';

	}else{
		document.getElementById('searchBox').style.visibility = 'hidden';
	}
}

// Global variable
var offset = 20;
var page = 1;
var pack = new Package();
var packages = [];

</script>
<?php
session_start ();
if (! isset ( $_SESSION ['ID'] )) {
	header ( "Location: ../login.php" );
}
include '../database.php';
?>

<?php
//paging
$pageSize = 15;
$allPage = 0;
if (isset ( $_GET ['page'] )) {
	$nowPage = $_GET ['page'] - 1;

} else {
	$nowPage = 0;
}

if (isset ( $_GET ['page'] )) {
	$curPage = $_GET ['page'] ;
} else {
	$curPage = 1;
}

$countPackage=0;
?>
</head>
<body>
	<h1><a href="index.php">Message</a></h1>
	<h3><a href="../index.php">&larr; Back</a></h3>
	<br>
	<div class="menu">
		<i class="material-icons" onclick="exportExcel();" title="Export">insert_drive_file</i>
		<i class="material-icons" onclick="searchBox();" title="Search">find_in_page</i>
	</div>
	<div id="detail">
		<table class="detail">
			<tr>
				<th>ลำดับที่</th>
				<th>เลขที่ order</th>
				<th>เลขที่ package</th>
				<th>รหัสลูกค้า</th>
				<th>วันที่ส่ง</th>
				<th>ข้อความ</th>
				<th>สถานะ</th>
				<th>Action</th>
			</tr>
			<!-- detail -->

			<?

			$queryStringURI=explode("&",$_SERVER['QUERY_STRING']);
			//search block
			$matches  = preg_grep ('/^active_link/i', $queryStringURI);

			if(strlen($_SERVER['QUERY_STRING'])>0 && count($matches)>0){

				$sql= "select tm.packageid, pk.packageno, tm.eid, tm.order_id, co.order_number, cs.customer_id, cs.customer_code, tm.subject, tm.content, tm.message_date , tm.active_link, tm.user_id from customer cs right join (select tml1.eid, tml1.order_id, tml1.customer_id, tml1.subject, tml1.content, tml1.message_date , tml1.packageid, tml1.active_link, tml1.user_id from total_message_log tml1 where tml1.message_date = (select max(tml2.message_date) from total_message_log tml2 where tml2.order_id = tml1.order_id) group by order_id  order by tml1.eid  desc) tm on cs.customer_id = tm.customer_id left join package pk on pk.packageid = tm.packageid left join customer_order co on co.order_id = tm.order_id";
			}else{
				//query 2 months
				$sql="select tm.packageid, pk.packageno, tm.eid, tm.order_id, co.order_number, cs.customer_id, cs.customer_code, tm.subject, tm.content, tm.message_date , tm.active_link, tm.user_id from customer cs right join (select tml1.eid, tml1.order_id, tml1.customer_id, tml1.subject, tml1.content, tml1.message_date , tml1.packageid, tml1.active_link, tml1.user_id from total_message_log tml1 where tml1.message_date = (select max(tml2.message_date) from total_message_log tml2 where tml2.order_id = tml1.order_id) group by order_id  order by tml1.eid  desc) tm on cs.customer_id = tm.customer_id left join package pk on pk.packageid = tm.packageid left join customer_order co on co.order_id = tm.order_id where  tm.message_date >= date_sub(curdate(), interval 2 month)";
			}
			if(isset($_GET['active_link'])){
				if(! empty ( $_GET ['searchAll'] )
				||! empty ( $_GET ['order_number'] )
				||! empty ( $_GET ['packageno'] )
				||! empty ( $_GET ['customer_code'] )
				||! empty ( $_GET ['from'] )
				||! empty ( $_GET ['to'] )
				||! empty ( $_GET ['active_link'])||$_GET ['active_link']==0 ){
					$sql.=' where ';
				}
			}
			$sqlSearch='';
			$request='';
			if (! empty ( $_GET ['searchAll'] )) {
				$arrSearchAll=array(
					'co.order_number like "%'.trim($_GET['searchAll']).'%"',
					'pk.packageno like "%'.trim($_GET['searchAll']).'%"',
					'cs.customer_code like "%'.trim($_GET['searchAll']).'%"',
					'tm.message_date like "%'.trim($_GET['searchAll']).'%"',
					'tm.message_date like "%'.trim($_GET['searchAll']).'%"',
					'tm.active_link like "%'.trim($_GET['searchAll']).'%"'
				);
				$i=0;
				foreach($arrSearchAll as $val){
					$sqlSearch.=$val.' ';
					if($i<count($arrSearchAll)-1){
						$sqlSearch.=" OR ";
					}
					++$i;
				}
				$request.='&searchAll='.$_GET ['searchAll'];
			}
			$arrCriteria=array();
			$allFlag=false;
			$flagForm=false;
			$flagTo=false;
			$bleft='';
			$bright='';
			if(!empty($_GET['from']) && !empty($_GET['to'])){
				$bleft='(';
				$bright=')';
			}
			if(!empty($_GET['from'])){
				array_push($arrCriteria,$bleft.' tm.message_date like "%'.$_GET['from'].'%"');
				$request.='&from='.$_GET ['from'];
				$flagForm=true;
			}
			if(!empty($_GET['to'])){
				array_push($arrCriteria,' tm.message_date like "%'.$_GET['to'].'%"'. $bright);
				$request.='&to='.$_GET ['to'];
				$flagTo=true;
			}
			if(!empty($_GET['order_number'])){
				array_push($arrCriteria,' co.order_number like "%'.trim($_GET['order_number']).'%"');
				$request.='&order_number='.trim($_GET ['order_number']);
			}
			if(!empty($_GET['packageno'])){
				array_push($arrCriteria,' pk.packageno like "%'.trim($_GET['packageno']).'%"');
				$request.='&packageno='.trim($_GET ['packageno']);
			}
			if(!empty($_GET['customer_code'])){
				array_push($arrCriteria,' cs.customer_code like "%'.trim($_GET['customer_code']).'%"');
				$request.='&customer_code='.trim($_GET ['customer_code']);
			}
			if(isset($_GET['active_link'])){
				array_push($arrCriteria,' tm.active_link like "%'.$_GET['active_link'].'%"');
				$request.='&active_link='.$_GET ['active_link'];

			}
			$countPackage=0;
			if(count($arrCriteria) > 0){
				$i=0;
				if(! empty ( $_GET ['searchAll'] )){
					$sqlSearch.=' and ';
				}
				foreach ($arrCriteria as $key=>$val){
					$sqlSearch.=$val.' ';
					if($i<count($arrCriteria)-1){

						if($flagTo===true  && $flagForm===true ){
							$sqlSearch.=' or ';
							$flagTo=$flagForm=false;
						}else{
							$sqlSearch.=' and ';
						}
					}
					$i++;
				}
			}
			$package = array ();
			$packageSize=array ();
			$sql.=$sqlSearch.' order by tm.message_date desc';
			if ($result = $con->query ( $sql )) {
				while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
					$packageSize [] = $row;
				}
				$countPackage=count($packageSize);
				$allPage=ceil($countPackage/$pageSize);
				$sqlLimit=$sql.' LIMIT '. $nowPage * $pageSize .",".$pageSize ;
				$indexPaging=($nowPage * $pageSize);
				if ($result = $con->query ($sqlLimit )) {
					while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
						$package [] = $row;
					}
				}
			}
			$count = array ();
			if ($result = $con->query ( "select tm.packageid, pk.packageno, tm.eid, tm.order_id, co.order_number, cs.customer_id, cs.customer_code, tm.subject, tm.content, tm.message_date , tm.active_link, tm.user_id from customer cs right join (select tml1.eid, tml1.order_id, tml1.customer_id, tml1.subject, tml1.content, tml1.message_date , tml1.packageid, tml1.active_link, tml1.user_id from total_message_log tml1 where tml1.message_date = (select max(tml2.message_date) from total_message_log tml2 where tml2.order_id = tml1.order_id) group by order_id  order by tml1.eid  desc) tm on cs.customer_id = tm.customer_id left join package pk on pk.packageid = tm.packageid left join customer_order co on co.order_id = tm.order_id
where  tm.message_date >= date_sub(curdate(), interval 2 month) group by tm.eid order by tm.message_date desc"))
			{
				while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
					$count [] = $row;
				}
			}
				$puncCount = 0;
			?>
			<!-- detail -->

			<?php
				$sizeOfPackage= count($package);
				if($sizeOfPackage>0){
					for($i=0;$i<$sizeOfPackage;++$i){
			?>
				<tr class="<?php  echo (($puncCount % 2 == 0)? 'punc ' : '');?> " eid="<?php echo trim($package[$i]["eid"]); ?>" cid="<?php echo trim($package[$i]["customer_id"]); ?>" 
					
					
					
					id="<?php echo (!empty($package[$i]['order_id']))?$package[$i]['order_id']:$package[$i]['packageid']; ?>"
					opt="<?php echo (!empty($package[$i]['order_id']))?'oid':'pid'; ?>"
					>
					<!-- window.location.href = './message-detail.php?eid='+eid+'&cid='+cid+'&id='+id+'&opt='+opt; -->
					<td id="top-<?php echo ($i+1+$indexPaging); ?>"><?php echo ($i+1+$indexPaging); ?></td>
					<td id="<?php echo $package[$i]['packageno'].'order_number'; ?>">
						<?php
							echo (empty($package[$i]['order_number']))? "-": $package[$i]['order_number'];
						?>
					</td>
					<td id="<?php echo $package[$i]['packageno'].'packageno'; ?>">
						<?php
							echo (empty($package[$i]['packageno']))? "-": $package[$i]['packageno'];
						?>
					</td>
					<td id="<?php echo $package[$i]['packageno'].'customer_code'; ?>">
						<?php
							echo (empty ($package[$i]['customer_code']))? "-": $package[$i]['customer_code'];
						?>
					</td>
					<td id="<?php echo $package[$i]['packageno'].'message_date'; ?>"><?php echo $package[$i]['message_date']; ?></td>
					<td id="<?php echo $package[$i]['packageno'].'subject'; ?>"> <?php echo (strip_tags($package[$i]['subject'])); ?></td>
					<td id="<?php echo $package[$i]['active_link'].'active_link'; ?>">
						<?php
							echo (($package[$i]['user_id'])== "0")? "ยังไม่ได้ตอบ": "ตอบแล้ว";
						?>
					</td>
					<td>
						<?php
							if(!empty($package[$i]["order_id"])){?>
								<button onclick="detail('<?php echo trim($package[$i]["eid"]); ?>','<?php echo trim($package[$i]["customer_id"]); ?>','<?php echo trim($package[$i]["order_id"]); ?>','oid')">Detail</button>
						<?php }elseif (!empty($package[$i]["packageid"])) {?>
								<button onclick="detail('<?php echo trim($package[$i]["eid"]); ?>','<?php echo trim($package[$i]["customer_id"]); ?>','<?php echo trim($package[$i]["packageid"]); ?>','pid')">Detail</button>
						<?php }?>
					</td>
				</tr>

			<?php
				$puncCount++;
				}//end for
			}
			if($countPackage<=0){
				$package = array();
				$packageSize=array();
				$sql.=$sqlSearch.' group by p.packageid order by packageid desc';
				if ($result = $con->query ( $sql )) {
					while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
						$packageSize [] = $row;
					}
					$countPackage=count($packageSize);
					$allPage=ceil($countPackage/$pageSize);
					$sqlLimit=$sql.' LIMIT '. $nowPage * $pageSize .",".$pageSize ;
					if ($result = $con->query ($sqlLimit )) {
						while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
							$package [] = $row;
						}
					}
				}
				$count = array ();

				if ($result = $con->query ( $sql )) {

					while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
						$count [] = $row;
					}
				}
				$puncCount = 0;
			?>
			<!-- detail -->
			<?php
				$sizeOfPackage= count($package);
				if($sizeOfPackage>0){
					for($i=0;$i<$sizeOfPackage;++$i){
			?>
				<tr class="<?php  echo (($puncCount % 2 == 0)? 'punc ' : '') . ($package[$i]['statusid'] == 7 ? 'cancel ' : '') ?> ">
					<td id="<?php echo $package[$i]['packageno'].$package[$i]; ?>"><?php echo $package[$i]['packageno']; ?></td>
					<td id="<?php echo $package[$i]['packageno'].'order_number'; ?>"><?php echo $package[$i]['order_number']; ?></td>
					<td id="<?php echo $package[$i]['packageno'].'packageno'; ?>"><?php echo $package[$i]['packageno']; ?></td>
					<td id="<?php echo $package[$i]['customer_code'].'customer_code'; ?>"><?php echo $package[$i]['customer_code']; ?></td>
					<td id="<?php echo $package[$i]['customer_code'].'message_date'; ?>"><?php echo $package[$i]['message_date']; ?></td>
					<td id="<?php echo $package[$i]['customer_code'].'subject'; ?>"><?php echo $package[$i]['subject']; ?></td>
					<td id="<?php echo $package[$i]['customer_code'].'active_link'; ?>"><?php echo $package[$i]['active_link']; ?></td>
					<td>
					<button onclick="edit('<?php echo trim($package[$i]['packageno']); ?>')">Edit</button>
					</td>
				</tr>

			<?php
				$puncCount++;
				}//end for
			}
			}

			?>
		</table>
		<br />
	</div>
	<div class="paging">
		<?php
			echo 'หน้า&emsp;';
			for($i = 1; $i <= $allPage; $i ++) {
				if ($i == $curPage) {
					echo '<a class="current-page" href="?page=' . $i  .$request. '">' . intval ( $i ) . '</a>';
				} else {
					echo '<a href="?page=' . $i  .$request. '">' . intval ( $i ) . '</a>';
				}
			}
		?>
	</div>
	<div id="searchBox" class="wrap">
		<form method="get">
			<table>
				<tr>
					<th><h2 id="title">Search</h2></th>
					<td></td>
				</tr>
				<tr>
					<th>Search All :</th>
					<td><input name="searchAll" /></td>
				</tr>
				<tr>
					<th>เลขที่ order:</th>
					<td><input name="order_number" /></td>
				</tr>
				<tr>
					<th>เลขที่ Package  :</th>
					<td><input name="packageno" /></td>
				</tr>
				<tr>
					<th>รหัสลูกค้า :</th>
					<td><input name="customer_code" /></td>
				</tr>
				<tr>
					<th>วันที่ส่ง :</th>
					<td><input class="datetimepicker" id="dform" type="text" name="from" /></td>
				</tr>
				<tr>
					<th>To :</th>
					<td><input class="datetimepicker" id="dto" type="text" name="to" /></td>
				</tr>
				<tr>
					<th>สถานะ :</th>
					<td>
					<select name="active_link">
						<option value=''>-</option>
						<option value='0'>ตอบแล้ว</option>
						<option value='1'>ยังไม่ได้ตอบ</option>
					</select>
					</td>
				</tr>
				<tr class="confirm">
					<td></td>
					<td><a onclick="searchBox();">Cancel</a>&emsp;
						<button>Search</button></td>
				</tr>
			</table>
		</form>
	</div>
	<div class="results">
		<table>
			<tr>
				<td><b>จำนวนรายการทั้งหมด</b></td>
				<td class="normal"></td>
				<?php if($countPackage>0){ ?>
					<td class="normal"><?php echo $countPackage; ?>&nbsp;</td>
				<?php }else{ ?>
					<td class="normal"><?php echo (count($count)>0)? number_format(count($count)) : ''; ?>&nbsp;</td>
				<?php }?>
				<td>กล่อง<br></td>
			</tr>
		</table>
</body>
</html>
<?php
	$con->close ();
?>