<?php
session_start ();
if (! isset ( $_SESSION ['ID'] )) {
	header ( "Location: ../login.php" );
}
include '../database.php';

if(isset($_GET['id'])){
	$packageid=$_GET['id'];
	//echo $packageno;
	$sql='select c.customer_firstname,c.customer_lastname,p.packageno,p.customerid,c.customer_code,p.shipping_address,ca.line_1,ca.city,ca.country,ca.zipcode,ca.phone 
from package p inner join customer_address ca on ca.address_id=p.shipping_address 
inner join customer c on c.customer_id=ca.customer_id
where p.packageid=?';
	$stmt=$con->prepare($sql);
	$stmt->bind_param('s',$packageid);
	$stmt->execute();
	
	
	//result
	$result=$stmt->get_result();
	$num_of_rows = $result->num_rows;
	
	$arrayData=array();
	while ($row = $result->fetch_assoc()) {
		//echo json_encode($row);
		$arrayData[]=$row;
	}
	$content1='';
	$content2='';

	$zipCodeArr=array();
	if(count($arrayData)>0){
		
		
		$content1=$arrayData[0]['customer_firstname'].' '.$arrayData[0]['customer_lastname'];
		$content2=$arrayData[0]['line_1'].' '.$arrayData[0]['city'];
		$zipCodeArr=getZipCodeArray($arrayData[0]['zipcode']);
		
		
	}
	
	$sql='select p.packageno,co.order_id,co.order_number
from package p
inner join package_detail pd on pd.packageid=p.packageid
inner join customer_order co on co.order_id=pd.order_id
where p.packageid=? group by co.order_id';
	$stmt=$con->prepare($sql);
	$stmt->bind_param('s',$packageid);
	$stmt->execute();
	
	
	//result
	$result=$stmt->get_result();
	$num_of_rows = $result->num_rows;
	
	$orderArr=array();
	while ($row = $result->fetch_assoc()) {
		//echo json_encode($row);
		$orderArr[]=$row;
	}
	
}


function getZipCodeArray($params){
	$tempArr=array();
	if(count($params)>0){
		for($i=0;$i<strlen($params);++$i){
			$tempArr[$i]=substr($params, $i,1);
		}
	}
	return $tempArr;
}


?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Title of the document</title>
<script type="text/javascript" src="./libs/jquery-3.1.0.js"></script>    
<script type="text/javascript" src="./libs/jquery-barcode.js"></script>
<script type="text/javascript">
	$( document ).ready(function() {
		$("#bcTarget").barcode({code: "<?php echo $arrayData[0]['packageno']?>", crc:false}, "code128",{barWidth:2, barHeight:30});    


		$('#print').on('click',function(){
			$(this).hide();
			window.print();
		}); 
	});
</script>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
	rel="stylesheet">
<link
	href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300'
	rel='stylesheet' type='text/css'>


<style type="text/css">

	h1,h2,h3,h4{
		margin: 0;
		padding: 0;
	}
	.hl,.hr{
		display: inline-block;
		width: 48%;
	}
	.hl h4{
		bottom: 10px;
    	font-size: 14px;
    	position: relative;
    	text-align: center;
	}
	
	.hr{
		border-left: 1px solid;
	}
	
	.hr > div:nth-child(1){
		 font-size: 13px;
    	text-align: left;
	}
	.hr > div:nth-child(2){
		text-align: center;
	}
	.header{
		display: block;
		border-bottom: 1px solid;
		
	}
	.wrapper,.boxPrint{
		font-family:tahoma;
		display: block;
		border: 1px solid;
		width: 500px;
	}
	
	.ct{
		margin-left: 22px;
    	margin-right: 17px;
    	margin-top: 10px;
	}
	
	.ct div{
		 line-height: 23px;
	}
	
	.ct span{
		font:bold 14px tahoma;		
	}
	
	.cf{
		  margin-top: 15px;
    	  text-align: center;
	}
	
	.cf div div{
		font: 12px tahoma;
    	margin-top: 5px;
    	padding-left: 6em;
    	text-align: left;
	}
	
	.cf ul{
		margin: 0;
		padding: 0;
	}
	.cf li{
		border: 1px solid;
    	display: inline-block;
    	padding: 8px;
	}
	
	.fl{
		font: 12px tahoma;
	}
	
	.fl ul{
		margin:0;
		padding: 0;
		
	}
	
	.fl li{
		list-style: none;
		padding: 10px 0 10px 10px;
	}
	
	
	.fl,.fr{
		display: inline-block;
	}
	
	.fr{
		padding: 0 1.5em 0 1.5em;
	}
	
	.footer{
		border-top: 1px solid;
    	margin-top: 10px;
    	padding-top: 5px;
	}
	
	.boxPrint{
		border: medium none;
   		margin-top: 2em;
    	text-align: center;
	}
	.boxPrint i{
		cursor: pointer;
	}
</style>
</head>

<body>
<div class="wrapper">


	<div class="header">
		<div class="hl">
			<h4>ชื่อที่อยู่ผู้รับ / Address</h4>

		</div>
		<div class="hr">
			<div>สำหรับติดต่อผู้รับ/Tel</div>
			<div><?php echo $arrayData[0]['phone']?></div>
		</div>
	</div>
	<div class="content">
		<div class="ct">
			<div><span>ถึง / To</span> <?php echo $content1;?></div>
			<div><?php echo $content2;?> </div>
		
		</div>
		<div class="cf">
			<div>
				<ul>
					<li><span style="border-right: 1px solid; padding: 8px 10px 8px 0px; font: bold 14px tahoma;">รหัสไปรษณีย์</span><span style="padding-left: 13px;font: bold 14px tahoma;">Postcode</span></li>
					<?php if(count($zipCodeArr)>0){?>
					<?php foreach($zipCodeArr as $val){?>
					<li><?php echo $val; ?></li>
					<?php }?>
					<?php }?>
				</ul>
<!-- 				<div>KERRY</div> -->
			</div>
		</div>
	</div>
	<div class="footer">
		<div class="fl">
			<ul>
				
					<?php if(count($orderArr)>0){?>
					<?php foreach($orderArr as $val){?>
					<li>
					<label>เลขที่ออร์เดอร์ :</label><?php echo $val['order_number']?>
					</li>
					<?php }}?>
				
				<li><label>รหัสลูกค้า :</label><?php echo $arrayData[0]['customer_code']?></li>
				<!-- <li><label>ID ลูกค้า :</label><?//php echo $arrayData[0]['customer_code']?></li> -->
			</ul>
		</div>
		<div class="fr">
			<div id="bcTarget"></div>   
		</div>
	</div>

</div>
<div class="boxPrint">
	<i title="print"  id="print" class="material-icons">print</i>
</div>

</body>

</html>