<!DOCTYPE html>
<html>
	<head>
		<title>สรุปรายการสั่งสินค้า</title>
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
						color:#CC9933;
				}
				button,.button{
						color:#CC9933;
				}
				a{
						color:#CC9933;
				}
				th{
						background:#CC9933;
				}
				.undivide th{
						background:#CC9933;
				}
				.order-button:hover{
						color:#CC9933;
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
			$statusSearch = false;
			$request = '';
			//ini_set('display_errors', 0);
			if(!empty($_GET['ono'])){array_push($cases,' customer_order.order_number='.$_GET['ono']);$request .= 'ono='.$_GET['ono'];}
            if(!empty($_GET['customer'])){array_push($cases,' (customer.customer_firstname LIKE "%'.$_GET['customer'].'%" OR customer.customer_lastname LIKE "%'.$_GET['customer'].'%")');$request .= 'customer='.$_GET['customer'];}
			//if(!empty($_GET['status'])){array_push($cases,' customer_order.order_status_code='.$_GET['status']);$request .= 'status='.$_GET['status'];$statusSearch = true;}
			if(!empty($_GET['from'])){array_push($cases,' customer_order.date_order_created="'.substr($_GET['from'],6).'-'.substr($_GET['from'],3,2).'-'.substr($_GET['from'],0,2).'"');$request .= 'from='.$_GET['from'];}
			//if(!empty($_GET['to'])){array_push($cases,' customer_order.date_order_created<="'.substr($_GET['to'],6).'-'.substr($_GET['to'],3,2).'-'.substr($_GET['to'],0,2).'"');$request .= 'to='.$_GET['to'];}
			//ini_set('display_errors', 1);
			
			$search = '';
			for($i=0;$i<sizeof($cases);$i++){
				$search .= ' AND'.$cases[$i];
			}
                        $_SESSION['sql'] = 'SELECT customer.customer_id,customer.customer_firstname,customer.customer_lastname,'
                                        . 'customer_order.order_id,customer_order.order_number,customer_order.order_status_code,customer_order.order_price,customer_order.date_order_paid,'
                                        . 'count(Distinct shop_name),count(customer_order_product.order_id),sum(customer_order_product.quantity)'
                                        . ' FROM customer_order'
                                        . ' join customer on customer.customer_id=customer_order.customer_id'
                                        . ' join customer_order_product on customer_order.order_id=customer_order_product.order_id'
                                        . ' join product on product.product_id = customer_order_product.product_id'
                                        . ' where customer_order.order_status_code=2 or customer_order.order_status_code=3'.$search;
                        //echo $_SESSION['sql'];
                        
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
                        function exportExcel(){
				window.open('summary_excel.php','_blank');
                        }
		</script>
	</head>
	<body>
            <h1><a href="order_summary.php">สรุปรายการสั่งสินค้า</a></h1>
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
				<th>วันที่ลูกค้าแจ้งชำระ</th>
				<th>จำนวนร้านค้า</th>
				<th>จำนวน link</th>
				<th>จำนวนสินค้า</th>
                                <th>ยอดค่าสินค้า (หยวน)</th>
				<th>เลขที่ Confirm</th>
				<th>Status</th>
			</tr>
			<?php

				$query = 'SELECT customer.customer_id,customer.customer_firstname,customer.customer_lastname,'
                                        . 'customer_order.order_id,customer_order.order_number,customer_order.order_status_code,customer_order.order_price,customer_order.date_order_paid,'
                                        . 'count(Distinct shop_name),count(customer_order_product.order_id),sum(customer_order_product.quantity)'
                                        . ' FROM customer_order'
                                        . ' join customer on customer.customer_id=customer_order.customer_id'
                                        . ' join customer_order_product on customer_order.order_id=customer_order_product.order_id'
                                        . ' join product on product.product_id = customer_order_product.product_id'
                                        . ($statusSearch==false? ' where customer_order.order_status_code=2 or customer_order.order_status_code=3':'');
                                $groupBy = ' GROUP BY customer_order.order_id';
                                $orderBy = ' ORDER BY customer_order.order_number';
                                //echo $query.$search.$groupBy;                                

                                if($stmt = $con->prepare($query.$search.$groupBy.$orderBy)) {					
					$stmt->execute();
					$stmt->store_result();
					$count = $stmt->num_rows;
					$allPage = ceil($count/$pageSize);
					$stmt->close();
					
					$stmt = $con->prepare($query.$search.$groupBy.$orderBy.' LIMIT '.$nowPage*$pageSize.','.$pageSize);
                                        $stmt->execute();
					$stmt->bind_result($cid,$fname,$lname,$oid,$ono,$status,$price,$datetime,$countShop,$countLink,$countProduct);
					$puncCount = 0;
                              
					while($stmt->fetch()){
						//info
						$date = substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
						$time = substr($datetime,10,9);
						echo '<tr class="'.($puncCount%2==0? 'punc ':'')/*.($status==0? 'normal ':'').($status==2? 'cancel ':'')*/.'">'.
						'<td>'.$ono.'</td>'.
						'<td>'.$fname.' '.$lname.'</td>'.
						'<td>'.$date.'</td>'.
						'<td>'.$countShop.'</td>'.
						'<td>'.$countLink.'</td>'.
						'<td>'.$countProduct.'</td>'.
						'<td>'.$price.'</td>'.
						'<td>'.$ono.'</td>'.
						'<td>'.($status==2? 'รอจ่าย':'จ่ายแล้ว').'</td>';
						
						echo '</td></tr>';
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
						<tr><th>เลขที่ออเดอร์  :</th><td><input name="ono" /></td></tr>
						<tr><th>Customer :</th><td><input name="customer" /></td></tr>
						<tr><th>วันที่สั่งซื้อ</th><td><input class="datepicker" name="from" /></td></tr>
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
                            <td><?php echo number_format($count); ?>&nbsp;</td>
                            <td>Records<br></td>
                        </tr>
		</div>
	</body>
</html>
<?php
	$con->close();
?>
