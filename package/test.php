<?php
session_start ();
if (! isset ( $_SESSION ['ID'] )) {
	header ( "Location: ../login.php" );
}
include '../database.php';
date_default_timezone_set('Asia/Bangkok');
	
// 	$delId=20;
	
// 	$_SESSION['orderTemp'][]=10;
// 	$_SESSION['orderTemp'][]=20;
	
// 	$sessionTemp=array();
// 	if(isset($_SESSION['orderTemp'])){
		
// 		$i=0;
// 		foreach($_SESSION['orderTemp'] as $val){
// 			echo $val;
// 			if($val==$delId){
// 				$sessionTemp[]=$val;
// 			}
// 		}
		
// 		if(count($sessionTemp)>0){
// 			unset($_SESSION['orderTemp']);
// 			foreach($sessionTemp as $val){
// 				$_SESSION['orderTemp'][]=$val;
// 			}
// 		}
		
// 		print_r($_SESSION['orderTemp']);
// 	}
// $array1=[1,2,3,5];
// $array2=[1,2,3,4];

// $temp=array();

// $array = array_unique (array_merge ($array1, $array2));

// echo count($array);
// foreach($array as $val){
// 	$temp[]=$val;
	
// }
// print_r($temp);
//$params=array();
//selectByItems($con,323);


function selectByItems($con,$params){
	$sql='select order_number from customer_order where order_id in(?)';
	$stmt=$con->prepare($sql);
	$stmt->bind_param('i',$params);
	$stmt->execute();
	
	
	//result
	$result=$stmt->get_result();
	$num_of_rows = $result->num_rows;
	
	$arrayData=[];
	while ($row = $result->fetch_assoc()) {
		//echo json_encode($row);
		$arrayData[]=$row;
	}
	
	echo json_encode($arrayData);
	
	
	$stmt->fetch();
	$stmt->close();
	
}

// $sizeOfId=0;
// $countOrderIdUnq='select count(distinct order_id) as _C from package_detail where packageid=2';
// if($result = $con->query ( $countOrderIdUnq )){
// 	$row = $result->fetch_array ( MYSQL_ASSOC );
// 	$sizeOfId=$row['_C'];
// }

// echo $sizeOfId;

//checkpakage($con);
$packageno="P0011";
echo '"'.$packageno.'"';

function checkpakage($con){
	$sql='select packageid from package where packageno=?';

	$stmt=$con->prepare($sql);
	$stmt->bind_param('s',trim('P16000003'));
	$stmt->execute();
	$result=$stmt->get_result();
	
	$packageid=array();
	while ($row = $result->fetch_assoc()) {
		//echo json_encode($row);
		$packageid[]=$row;
	}
	print_r($packageid);

}




***********
<div class="box">
<p>รอบที่1</p>
<div class="in">
<div>
<div class="col cl_1">วันที่ส่ง: <input id="datepickerCreate"
		class="china datepicker" style="padding: 2px;" value="" name="datepickerCreate[]" /></div>
		<div class="col cl_2">จำนวนกล่องที่ส่ง: <input id="total_box_1" class="total_box" class="amount" type="text" name="total_box[]"
				style="text-align: right" placeholder="" value="0" onkeyup="this.value=this.value.replace(/[^0-9]/g,'');" /></div>
				<button id="btn-frm-1" class="gr" onclick="chkBoxRemain(document.getElementById('total_box_1').value); return false;">ok</button>

				</div>
				<div>
				<?php
				$sql="select * from user";
$users=array();
if ($result = $con->query ( $sql )) {
	while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
		$users [] = $row;
	}
}
	
?>
					<div class="col cl_1">ผู้ส่ง: 
					<?php if(count($users)>0){?>
						<select name="user_send" id="user_send">
							<?php foreach($users as $val){?>
							<option value="<?php echo $val['uid']; ?>"><?php echo $val['uid']?></option>
							<?php }?>
						</select>
					<?php }?>
					</div>
					<div class="col cl_2">วันที่คีย์: <span><?php echo date('m/d/Y')?></span></div>
				</div>
				<div class="tracking_thai">
					<div class="col cl_1">เลขที่Trackingไทย: </div>
				</div>
				<div>
					<div class="trackingTH" id="trackingTH">
						
					</div>
				</div>
				<p>หมายเหตุ</p>
				<div>
					<textarea class="remark" name="remark[]"></textarea>
					
				</div>

			</div>
			</div>





	
?>