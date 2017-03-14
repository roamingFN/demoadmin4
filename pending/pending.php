<!DOCTYPE html>
<html>
	<head>
		<title>เตรียมส่งสินค้า</title>
		<meta charset="utf-8">
		<!--Jquery Datepicker Timepicker-->
                <link rel="stylesheet" href="../css/jquery-ui.css">
		<script src="../js/jquery-1.10.2.js"></script>
                <script src="../js/jquery-ui.js"></script>
                <script src="../css/jquery-ui-timepicker-addon.min.css"></script>
                <script src="../js/jquery-ui-timepicker-addon.min.js"></script>
                
                <!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">-->
                <!--<script src="//code.jquery.com/jquery-1.10.2.js"></script>-->
                <!--<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>-->
                <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.css"></script>-->
		<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.js"></script>-->
                
		<script>
			$(function() {
				$( ".datepicker" ).datepicker({
						dateFormat: "dd-mm-yy",
				});        
			});
		</script>
                
		<link rel="stylesheet" type="text/css" href="../css/cargo.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		<style>
				i{
						color:#CC66CC;
				}
				button,.button{
						color:#CC66CC;
				}
				a{
						color:#CC66CC;
				}
				th{
						background:#CC66CC;
				}
				.undivide th{
						background:#CC66CC;
				}
				.order-button:hover{
						color:#CC66CC;
				}
		</style>
		
		<?php
                        session_start();
                        if (!isset($_SESSION['ID'])){
                            header("Location: ../login.php");
                        }
                        
			include '../database.php';
                        
			//search
			$cases = array();
			$statusSearch = false;
			$request = '';
			//ini_set('display_errors', 0);
			if(!empty($_GET['oid'])){array_push($cases,' customer_order.order_id='.$_GET['oid']);$request .= 'oid='.$_GET['oid'];}
            if(!empty($_GET['customer'])){array_push($cases,' (customer.customer_firstname LIKE "%'.$_GET['customer'].'%" OR customer.customer_lastname LIKE "%'.$_GET['customer'].'%")');$request .= 'customer='.$_GET['customer'];}
			if(!empty($_GET['status'])){
				if ($_GET['status']=='-') { 
						array_push($cases,' (customer_order.order_status_code=6 or customer_order.order_status_code=7)');
						$request .= 'status=-';
				}
				else {
						array_push($cases,' customer_order.order_status_code='.$_GET['status']);
						$request .= 'status='.$_GET['status'];
				}
			}
			if(!empty($_GET['from'])){array_push($cases,' customer_order.date_order_created>="'.substr($_GET['from'],6).'-'.substr($_GET['from'],3,2).'-'.substr($_GET['from'],0,2).'"');$request .= 'from='.$_GET['from'];}
			if(!empty($_GET['to'])){array_push($cases,' customer_order.date_order_created<="'.substr($_GET['to'],6).'-'.substr($_GET['to'],3,2).'-'.substr($_GET['to'],0,2).'"');$request .= 'to='.$_GET['to'];}
			//ini_set('display_errors', 1);
			
			// $search = '';
			// for($i=0;$i<sizeof($cases);$i++){
			// 	$search .= ' AND'.$cases[$i];
			// }
            

            $search = ' AND customer_order.order_status_code=6';               
			if(sizeof($cases)>0){
				$search = ' AND'.$cases[0];
				for($i=1;$i<sizeof($cases);$i++){
					$search .= ' AND'.$cases[$i];
				}
			}
			//echo $search;

			//paging
			$pageSize = 15;
			$allPage = 0;
			if(isset($_GET['page'])){
				$nowPage = $_GET['page']-1;
			}else{
				$nowPage = 0;
			}
		?>
		<script>
			
			var searchOn = false;
			function searchBox(){
				searchOn = !searchOn;
				if(searchOn){
					document.getElementById('searchBox').style.visibility = 'visible';
				}else{
					document.getElementById('searchBox').style.visibility = 'hidden';
				}
			}
		</script>
	</head>
	<body>
            <h1><a href="pending.php">เตรียมส่งสินค้า</a></h1>
            <h3><a href="../index.php">&larr; Back</a></h3><br>
                <div class="menu">
			<i class="material-icons" onclick="searchBox();" title="Search">find_in_page</i>
		</div>
		<table class="detail">
            <tr>
				<th>Tracking จีน</th>
				<th>เลขที่ออเดอร์</th>
				<th>Customer</th>
				<th>M3</th>
				<th>Kg.</th>
				<th>สถานะ</th>
                <th>บริการขนส่งในไทย</th>
                <th>tracking ไทย</th>
				<th>วันที่ส่ง</th>
				<th>ที่อยู่ลูกค้า</th>
			</tr>
			<?php            
				$query = 'SELECT customer.customer_id,customer.customer_firstname,customer.customer_lastname,'.
					'customer_order.order_id,customer_order.order_number,customer_order.order_status_code,customer_order_product.order_shipping_cn_ref_no,'.
					'customer_order_product.order_shipping_cn_m3_size,customer_order_product.order_shipping_cn_weight,'.
					'customer_order_shipping.order_shipping_th_option,customer_order_shipping.order_shipping_th_ref_no,customer_order.date_order_created'.
					' FROM customer,customer_order,customer_order_product,customer_order_shipping '.
					'WHERE (customer_order.customer_id=customer.customer_id AND customer_order.order_id=customer_order_product.order_id AND '.
					'customer_order.order_id=customer_order_shipping.order_id AND customer_order_product.order_id=customer_order_shipping.order_id)';
				
				$groupByTracking = ' GROUP BY customer_order_shipping.order_shipping_th_ref_no';	
				if($stmt = $con->prepare($query.$search.$groupByTracking)){
					
					$stmt->execute();
					$stmt->store_result();
					$count = $stmt->num_rows;
					$allPage = ceil($count/$pageSize);
					$stmt->close();
					
					$stmt = $con->prepare($query.$search.' LIMIT '.$nowPage*$pageSize.','.$pageSize);
                    $stmt->execute();
					$stmt->bind_result($cid,$fname,$lname,$oid,$ono,$status,$track,$m3,$weight,$option,$osrn,$datetime);
					$puncCount = 0;
					while($stmt->fetch()){
						//info
						$date = substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
						$time = substr($datetime,10,9);
						echo '<tr class="'.($puncCount%2==0? 'punc ':'')/*.($status==0? 'normal ':'').($status==2? 'cancel ':'')*/.'">'.
						'<td>'.$track.'</td>'.
						'<td><a target="_blank" href="customer.php?cid='.$cid.'">'.$ono.'</a></td>'.
						'<td>'.$fname.' '.$lname.'</td>'.
						'<td>'.$m3.'</td>'.
						'<td>'.$weight.'</td>'.
						'<td>'.($status==6? 'รอจ่าย':'จ่ายแล้ว').'</td>'.
						'<td>'.$option.'</td>'.
						'<td>'.$osrn.'</td>'.
						'<td>'.$date.' '.$time.'</td>'.
						'<td><a class="button" target="_blank" href="customer.php?cid='.$cid.'&print=1">Print</a>'.
						'<a class="button" target="_blank" href="pending_excel.php?cid='.$cid.'">Excel</a></td>';
						
						echo '</td></tr>';
						$puncCount++;
					}
					$stmt->close();
				}

				$summaryQuery = 'SELECT customer_order.order_status_code,count(customer_order.order_id)'.
					' FROM customer,customer_order,customer_order_product,customer_order_shipping'.
					' WHERE (customer_order.customer_id=customer.customer_id AND customer_order.order_id=customer_order_product.order_id AND'.
					' customer_order.order_id=customer_order_shipping.order_id AND customer_order_product.order_id=customer_order_shipping.order_id)';
				
				//summary
                $count = array();
                $count[6] = 0;
                $count[7] = 0;
                $totalCount = 0;
                $groupByStat = ' GROUP BY customer_order.order_status_code';               
                if($stmt = $con->prepare($summaryQuery.$search.$groupByStat)){
                    $stmt->execute();
                    $stmt->bind_result($stat,$countId);
                    while($stmt->fetch()){
                            $count[$stat] = $countId;
                            $totalCount += $countId;
                    }
                }
			?>
		</table><br>
            <div id="searchBox" class="wrap">
			<form method="get">
                <table>
					<tr><th><h2 id="title">Search</h2></th><td></td></tr>
						<tr><th>เลขที่ออเดอร์  :</th><td><input name="oid" /></td></tr>
						<tr><th>Customer :</th><td><input name="customer" /></td></tr>
						<tr><th>สถานะ :</th><td><select name="status"/>
							<option value="-">-</option>
							<option value="6" selected>รอจ่าย</option>
							<option value="7">จ่ายแล้ว</option>
						</select></td></tr>
						<tr><th>From :</th><td><input class="datepicker" name="from" /></td></tr>
						<tr><th>To :</th><td><input class="datepicker" name="to" /></td></tr>
					</select></td></tr>
					<tr class="confirm"><td></td><td><a onclick="searchBox();">Cancel</a>&emsp;<button>Search</button></td></tr>
				</table>
			</form>
		</div>
		<div class="paging">
			<?php 
				echo 'หน้า&emsp;';
				for($i=1;$i<=$allPage;$i++) {
					echo '<a href="?page='.$i.'&'.$request.'">'.intval($i).'</a>';
				}
			?>
		</div>
		<div class="results">
                    <table>
                        <tr>
                            <td><b>จำนวนรายการทั้งหมด</b></td>
                            <td class="normal"><b>รอจ่าย :</b></td>
                            <td class="normal"><?php echo number_format($count[6]); ?>&nbsp;</td>
                            <td class="complete"><b>จ่ายแล้ว :</b></td>
                            <td class="complete"><?php echo number_format($count[7]); ?>&nbsp;</td>
                            <td><b>Total :</b></td>
                            <td><?php echo number_format($totalCount); ?>&nbsp;</td>
                            <td>Records<br></td>
                        </tr>
		</div>
	</body>
</html>
<?php
	$con->close();
?>
