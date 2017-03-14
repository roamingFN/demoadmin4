<!DOCTYPE html>
<html>
	<head>
		<title>Portage Summary</title>
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
                    $( ".datetimepicker" ).datetimepicker({
                            dateFormat: "dd-mm-yy",
                            timeFormat: "HH:mm:ss",
                    		showSecond:true
					});
					$( ".datepicker" ).datepicker({
                            dateFormat: "dd-mm-yy"
					});        
			});
		</script>
                
		<link rel="stylesheet" type="text/css" href="../css/cargo.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		<style>
				i{
						color:#FF0033;
				}
				button,.button{
						color:#FF0033;
				}
				a{
						color:#FF0033;
				}
				th{
						background:#FF0033;
				}
				.undivide th{
						background:#FF0033;
				}
				.order-button:hover{
						color:#FF0033;
				}
		</style>
		<?php
                        session_start();
                        if (!isset($_SESSION['ID'])){
                            header("Location: ../login.php");
                        }
                        
			include '../database.php';

			//search
            $_SESSION['sql'] = '';
			$cases = array();
			$request = '';
			ini_set('display_errors', 0);
			if(!empty($_GET['ono'])){array_push($cases,' co.order_number="'.$_GET['ono'].'"');$request .= 'ono='.$_GET['ono'];}
			if(!empty($_GET['cid'])){array_push($cases,' co.customer_id='.$_GET['cid']);$request .= 'customer='.$_GET['cid'];}
			//if(!empty($_GET['datetime'])){array_push($cases,' withdraw_date="'.$_GET['datetime'].'"');$request .= 'withdraw_date='.$_GET['datetime'];}
			if(!empty($_GET['from'])){            
                array_push($cases,' cos.order_shipping_th_date>="'.substr($_GET['from'],6,4).'-'.substr($_GET['from'],3,2).'-'.substr($_GET['from'],0,2).' 00:00:00"');
                $request .= 'from='.substr($_GET['from'],6,4).'-'.substr($_GET['from'],3,2).'-'.substr($_GET['from'],0,2);
            }
			if(!empty($_GET['to'])){
                array_push($cases,' cos.order_shipping_th_date<="'.substr($_GET['to'],6,4).'-'.substr($_GET['to'],3,2).'-'.substr($_GET['to'],0,2).' 23:59:59"');
                $request .= 'to='.substr($_GET['to'],6,4).'-'.substr($_GET['to'],3,2).'-'.substr($_GET['to'],0,2);
            }
			if(!empty($_GET['refcn'])){array_push($cases,' cop.order_shipping_cn_ref_no="'.$_GET['refcn'].'"');$request .= 'refcn='.$_GET['refcn'];}
			if(!empty($_GET['refth'])){array_push($cases,' cos.order_shipping_th_ref_no="'.$_GET['refth'].'"');$request .= 'refth='.$_GET['refth'];}
			if(!empty($_GET['status'])) {
         			array_push($cases,' co.order_status_code='.$_GET['status']);$request .= 'status='.$_GET['status'];                             
            }        
			ini_set('display_errors', 1);

			$search = '';
			$searchTotal = '';
			if(sizeof($cases)>0){
				$search = ' WHERE'.$cases[0];
				$searchTotal = ' AND'.$cases[0];
				for($i=1;$i<sizeof($cases);$i++){
					$search .= ' AND'.$cases[$i];
					if(!(strpos($cases[$i],'status=')!==false)){
						$searchTotal .= ' AND'.$cases[$i];
					}
				}
			}
						$_SESSION['sql'] = 'select co.order_id,co.order_number,co.customer_id,co.order_status_code,'.
						' cop.order_shipping_cn_ref_no,cop.order_shipping_cn_m3_size,cop.order_shipping_cn_weight,cop.order_shipping_rate,cop.order_shipping_cn_cost,'.
						' cos.order_shipping_th_option,cos.order_shipping_th_ref_no,cos.order_shipping_th_cost,cos.order_shipping_th_date'.
						' from customer_order co'.
						' join customer_order_product cop on co.order_id=cop.order_id'.
						' join customer_order_shipping cos on co.order_id=cos.order_id'.$search;	
                        //echo $_SESSION['sql'];
                        
			//paging
			$pageSize = 15;
			$allPage = 0;
			if(isset($_GET['page'])){
				$nowPage = $_GET['page']-1;
			}else{
				$nowPage = 0;
			}
			
			// $count = 0;
			// $paid = 0;
			// $notPaid = 0;
   //                      $countPaid = 0;
   //                      $countNotPaid = 0;
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
			function exportExcel(){
				window.open('portage_excel.php','_blank');
            }
		</script>
	</head>
	<body>
            <h1><a href="portage_summary.php">สรุปค่าขนส่ง</a></h1>
            <h3><a href="../index.php">&larr; Back</a></h3><br>
      	<div class="menu">
			<i class="material-icons" onclick="exportExcel();" title="Export">insert_drive_file</i>
			<i class="material-icons" onclick="window.print();" title="Print">print</i>
			<i class="material-icons" onclick="searchBox();" title="Search">find_in_page</i>
		</div>
		<table class="detail">
            <tr>
            	<th>เลขที่ออเดอร์</th>
            	<th>Customer</th>
				<th>Tracking จีน</th>
				<th>สถานะ</th>
				<th>M3</th>
				<th>น้ำหนัก</th>
				<th>เรทค่าขนส่ง</th>
				<th>ค่าขนส่งจีน-ไทย</th>
				<th>บริการขนส่งในไทย</th>
                <th>Tracking ไทย</th>
				<th>ค่าขนส่งไทย</th>
				<th>วันที่ส่งของ</th>
				<th>ยอดรวม</th>
			</tr>
			<?php
				//get customer name
                $customers = array();
				if($stmt = $con->prepare('SELECT customer_id,customer_firstname,customer_lastname FROM customer')){
					$stmt->execute();
					$stmt->bind_result($c_id,$cfn,$cln);
					while($stmt->fetch()){
						$customers[$c_id] = $cfn." ".$cln;
					}
				}

                //get status descriuption
                $statdesc = array();
				if($stmt = $con->prepare('SELECT status_id,des FROM order_status_code')) {
					$stmt->execute();
					$stmt->bind_result($sid,$des);
					while($stmt->fetch()) {
						$statdesc[$sid] = $des;
					}
				}

				$sql = 'select co.order_id,co.order_number,co.customer_id,co.order_status_code,'.
					' cop.order_shipping_cn_ref_no,cop.order_shipping_cn_m3_size,cop.order_shipping_cn_weight,cop.order_shipping_rate,cop.order_shipping_cn_cost,'.
					' cos.order_shipping_th_option,cos.order_shipping_th_ref_no,cos.order_shipping_th_cost,cos.order_shipping_th_date'.
					' from customer_order co'.
					' join customer_order_product cop on co.order_id=cop.order_id'.
					' join customer_order_shipping cos on co.order_id=cos.order_id';
				$groupBy = ' GROUP BY cop.order_shipping_cn_ref_no';
                $orderBy = ' ORDER BY co.order_id';
                //echo $sql.$groupBy.$orderBy.$search;                
				if($stmt = $con->prepare($sql.$search.$groupBy.$orderBy)) {
					$stmt->execute();
					$stmt->store_result();
					$count = $stmt->num_rows;
					$allPage = ceil($count/$pageSize);
					$stmt->close();
					
					$stmt = $con->prepare($sql.$search.$groupBy.$orderBy.' LIMIT '.$nowPage*$pageSize.','.$pageSize);
                    $stmt->execute();
					$stmt->bind_result($oid,$ono,$cid,$status,$refcn,$m3Size,$weight,$shippingRate,$costcn,$option,$refth,$costth,$shippingdate);
					$puncCount = 0;
					
					while($stmt->fetch()){
                            //$date = substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
							//$time = substr($datetime,10,9);
                        	echo '<tr class="'.($puncCount%2==0? 'punc ':'').($status>=0? 'normal ':'').($status==9? 'cancel ':'').'">'.
							'<td>'.$ono.'</td>'.
							'<td>'.$customers[$cid].'</td>'.
							'<td>'.$refcn.'</td>'.
							'<td>'.$statdesc[$status].'</td>'.
							'<td id="num">'.number_format($m3Size,2).'</td>'.
							'<td id="num">'.number_format($weight,2).'</td>'.
							'<td id="num">'.number_format($shippingRate,2).'</td>'.
							'<td id="num">'.number_format($costcn,2).'</td>'.
							'<td>'.$option.'</td>'.
							'<td>'.$refth.'</td>'.
                            '<td id="num">'.number_format($costth,2).'</td>'.
                            '<td>'.$shippingdate.'</td>'.
                            '<td id="num">'.number_format($costth+$shippingRate,2).'</td>'.
                            '</tr>';
						$puncCount++;
					}
					$stmt->close();
				}
				
			?>
		</table><br>
        <div id="searchBox" class="wrap">
			<form method="get">
                   	<table>
					<tr><th><h2 id="title">Search</h2></th><td></td></tr>
					<tr><th>เลขที่ออร์เดอร์ :</th><td><input name="ono"></td></tr>
               		<tr><th>Customer :</th><td>
                	<select name="cid">
						<?php
                    	echo '<option value="">-</option>';
                    	reset($customers);
							for($i=0;$i<sizeof($customers);$i++){
								echo '<option value="'.key($customers).'">'.current($customers).'</option>';
                           		next($customers);
							}
						?>
					</select></td></tr>
					<tr><th>Tracking จีน :</th><td><input name="refcn"></td></tr>
					<tr><th>Tracking ไทย :</th><td><input name="refth"></td></tr>
                 	<tr><th>From :</th><td><input class="datepicker" name="from"/></td></tr>
					<tr><th>To :</th><td><input class="datepicker" name="to"/></td></tr>
					<tr><th>Status :</th><td><select name="status">
												<option value="">-</option>
                                                <option value="0">รอตรวจสอบ</option>
                                                <option value="1">ตรวจสอบแล้วรอชำระเงิน</option>
                                                <option value="2">ชำระแล้ว ดำเนินการสั่งซื้อ</option>
                                                <option value="3">ร้านค้ากำลังส่งสินค้ามาโกดังจีน</option>
                                                <option value="4">โกดังจีนรับของแล้ว</option>
                                                <option value="5">สินค้าอยู่ระหว่างมาไทย</option>
                                                <option value="6">สินค้าถึงไทยแล้ว (รอจ่ายค่าขนส่งไทยจีน + ค่าขนส่งในไทย)</option>
                                                <option value="7">ชำระค่าขนส่งแล้วรอจัดส่งสินค้า</option>
                                                <option value="8">สินค้าจัดส่งให้ลูกค้าแล้ว</option>
                                                <option value="9">ยกเลิก</option>
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
                            <td class="normal"><?php echo number_format($count); ?>&nbsp;</td>
                            <td>Records<br></td>
                        </tr>
		</div>
	</body>
</html>
<?php
	$con->close();
?>
