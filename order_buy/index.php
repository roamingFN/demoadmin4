<!DOCTYPE html>
<html>
	<head>
		<title>Order Buy</title>
		<meta charset="utf-8">         
      	<link rel="stylesheet" href="../css/jquery-ui.css">
		<script src="../js/jquery-1.10.2.js"></script>
        <script src="../js/jquery-ui.js"></script>
        <script src="../css/jquery-ui-timepicker-addon.min.css"></script>
        <script src="../js/jquery-ui-timepicker-addon.min.js"></script>
                
		<script>
			$(function() {
          			$( ".datepicker" ).datepicker({
                    dateFormat: "dd-mm-yy"
                    //timeFormat: "HH:mm:ss",
                    //showSecond:true
				});        
			});
		</script>
                
		<link rel="stylesheet" type="text/css" href="../css/cargo.css">
		<link rel="stylesheet" type="text/css" href="../css/w3-blueGray.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		<?php
            session_start();
            if (!isset($_SESSION['ID'])){
                header("Location: ../login.php");
            }
                        
			include '../database.php';
           	include '../utility/permission.php';

			const FORMID = 5;
			$_access = json_decode(getAccessForm($con,FORMID,$_SESSION['USERID']));
			$_adminFlg = getAdminFlag($con,$_SESSION['ID']);
			if ($_adminFlg==0) {
					if (empty($_access) || $_access[0]->visible==0) header ("Location: ../login.php");
			}

           	function getCID($rs) {
               		include '../database.php';
                	//get bank_payment
                  	//get customer name
             		$cus = array();
					if($stmt = $con->prepare('SELECT customer_id,customer_firstname,customer_lastname FROM customer')){
							$stmt->execute();
							$stmt->bind_result($cid,$cfname,$clname);
							while($stmt->fetch()){
                 					$cus[$cid] = $cfname.' '.$clname;
							}
					}
                  	//echo array_search($rs, $cBanks);
                  	return array_search($rs, $cus);
          	}

			//search
          	$_SESSION['sql'] = '';
			$cases = array();
			$request = '';
			ini_set('display_errors', 0);
			if(!empty($_GET['ono'])) {
					array_push($cases,' o.order_number LIKE \'%'.$_GET['ono'].'%\'');
					$request .= '&o.order_number='.$_GET['ono'];
			}
			if(!empty($_GET['cid'])) {
					$cid = $_GET['cid'];
					$cid = explode('(', $cid);
					$cid = trim($cid[0]);
					array_push($cases, ' CONCAT(c.customer_firstname, " ",c.customer_lastname) LIKE \'%'.$cid.'%\'');
					$request .= '&cid='.$cid;
			}
			if(!empty($_GET['from'])){
					array_push($cases,' o.date_order_created>="'.substr($_GET['from'],6,4).'-'.substr($_GET['from'],3,2).'-'.substr($_GET['from'],0,2).' 00:00:00"');
                	$request .= '&from='.$_GET['from'];  
        	}
        	if(!empty($_GET['to'])){
                	array_push($cases,' o.date_order_created<="'.substr($_GET['to'],6,4).'-'.substr($_GET['to'],3,2).'-'.substr($_GET['to'],0,2).' 23:59:59"');
                	$request .= '&to='.$_GET['to'];
            }
            if(!empty($_GET['status'])){
            		if ($_GET['status']!="-") { 
							array_push($cases,' o.order_status_code= '.$_GET['status']);
					}
					else {
							array_push($cases,' (o.order_status_code>2 AND o.order_status_code<7)');
					}
					$request .= '&status='.$_GET['status'];
			}
			if(!empty($_GET['taobao'])) {
					array_push($cases,' o.taobao LIKE \'%'.$_GET['taobao'].'%\'');
					$request .= '&taobao='.$_GET['taobao'];
			}
			if(!empty($_GET['tracking'])) {
					array_push($cases,' o.tracking_no LIKE \'%'.$_GET['tracking'].'%\'');
					$request .= '&tracking='.$_GET['tracking'];
			}
			if(!empty($_GET['inComTaobao'])) {
					array_push($cases,' (op.order_taobao="" AND op.order_status=1)');
					$request .= '&inComTaobao=on';
			}
			if(!empty($_GET['inComTracking'])) {
					array_push($cases,' (op.order_shipping_cn_ref_no="" AND op.order_status=1)');
					$request .= '&inComTracking=on';
			}
			ini_set('display_errors', 1);
			
			//$search = ' WHERE (o.order_status_code>2 AND o.order_status_code<7)';
			$search = ' WHERE (o.order_status_code=3)';
			$searchTotal = '';
			if(sizeof($cases)>0){
				$search = ' WHERE '.$cases[0];
				$searchTotal = ' AND'.$cases[0];
				for($i=1;$i<sizeof($cases);$i++){
					$search .= ' AND'.$cases[$i];
					if(!(strpos($cases[$i],'status=')!==false)){
						$searchTotal .= ' AND'.$cases[$i];
					}
				}
			}
        	$_SESSION['sql'] = 'SELECT o.order_id,o.order_number,o.customer_id,o.date_order_created,o.order_status_code,c.customer_firstname,c.customer_lastname,'
            	   	. 'o.total_shop,o.total_link,o.product_quantity,o.order_price_yuan,process_status,taobao,tracking_no,product_available,o.flag_return'
               		. ' FROM customer_order o'
                	. ' JOIN customer_order_product op ON o.order_id=op.order_id'
                    . ' JOIN customer c ON o.customer_id = c.customer_id'
               		//. ' LEFT JOIN customer_order_product p ON o.order_id=p.order_id'
               		. $search;
            //echo $_SESSION['sql'];
        	            
			//delete
       		if(isset($_POST['del'])) {
				$sqldel1 = 'DELETE FROM customer_order WHERE order_id='.$_POST['del'].';';
       			$sqldel2 = 'DELETE FROM customer_order_product WHERE order_id='.$_POST['del'].';';
       			$sqldel3 = 'DELETE FROM customer_order_product_tracking WHERE order_id='.$_POST['del'].';';
       			$sqldel4 = 'DELETE FROM customer_order_shipping WHERE order_id='.$_POST['del'].';';
       			$sqldel5 = 'DELETE FROM customer_request_payment WHERE order_id='.$_POST['del'].';';
       			$sqldel6 = 'DELETE FROM customer_statement WHERE order_id='.$_POST['del'].';';
				$stmt = $con->prepare($sqldel1);
				$res = $stmt->execute();

				$stmt = $con->prepare($sqldel2);
				$res = $stmt->execute();

				$stmt = $con->prepare($sqldel3);
				$res = $stmt->execute();

				$stmt = $con->prepare($sqldel4);
				$res = $stmt->execute();

				$stmt = $con->prepare($sqldel5);
				$res = $stmt->execute();

				$stmt = $con->prepare($sqldel6);
				$res = $stmt->execute();
				if(!$res){
						echo '<script>alert("การลบข้อมูลล้มเหลว");</script>';
				}
                else{
                   	    echo '<script>alert("ลบข้อมูลสำเร็จ");</script>';
                }
			}
                        
			//paging
			$pageSize = 20;
			$allPage = 0;
			if(isset($_GET['page'])){
				$nowPage = $_GET['page']-1;
			}else{
				$nowPage = 0;
			}
			
			$count = 0;
			$normal = 0;
			$cancel = 0;
           	$countNormal = 0;	
           	$countCancel = 0;

           	//get customer name
   //          $cus = array();
			// if($stmt = $con->prepare('SELECT customer_id,customer_firstname,customer_lastname,customer_code FROM customer')){
			// 		$stmt->execute();
			// 		$stmt->bind_result($cid,$cfname,$clname);
			// 		while($stmt->fetch()){
   //               		$cus[$cid] = $cfname.' '.$clname;
			// 		}
			// }

           	//get customer info
			$_cus = array();
			$sqlgetCus = 'SELECT customer_id,customer_firstname,customer_lastname,customer_code FROM customer ORDER BY customer_firstname ASC';
			$result = mysqli_query($con,$sqlgetCus);
			while ($row = mysqli_fetch_assoc($result)) {
					$_cus[] = $row;
			}

           	//get taobao
			$_taobao = array();
			if($stmt = $con->prepare('SELECT order_taobao FROM customer_order_product')){
					$stmt->execute();
					$stmt->bind_result($taobao);
					while($stmt->fetch()){
                 		array_push($_taobao, $taobao);
					}
			}

			//set status description
			$_codes = array();
			if($stmt = $con->prepare('SELECT des FROM order_status_code')){
					$stmt->execute();
					$stmt->bind_result($des);
					while($stmt->fetch()){
						array_push($_codes,$des);
					}
			}
		?>
                
        <!--show boxs or export-->
		<script>
			var searchOn = false;
			function searchBox() {
				searchOn = !searchOn;
				if(searchOn){
					document.getElementById('searchBox').style.visibility = 'visible';
				}else{
					document.getElementById('searchBox').style.visibility = 'hidden';
				}
			}
			function exportExcel(){
					window.open('order_excel.php','_blank');
           	}
            function toProduct(oid){
            		location.href="product.php?order_id=" + oid;
        	}
		</script>
	</head>
	<body>
            <h1><b><a href="index.php">ดำเนินการสั่งซื้อ</a></b></h1>
            <h3><a href="../index.php">&larr; Back</a></h3><br>
		<div class="menu">
			<i class="material-icons" onclick="exportExcel();" title="Export">&#xE24D;</i>
			<i class="material-icons" onclick="window.print();" title="Print">&#xE8AD;</i>
			<i class="material-icons" onclick="searchBox();" title="Search">&#xE880;</i>
		</div>
		<table class="detail" style="table-layout:fixed;">
                <tr>
				<th>เลขที่ Order</th>
				<th>ชื่อลูกค้า</th>
				<th width="6%">วันที่ Order</th>
				<th width="5%">จำนวนร้านค้า</th>
				<th width="5%">จำนวน link</th>
				<th width="10%"><div>จำนวนที่สั่งสินค้า</div></th>
				<th width="10%"><div>จำนวนสินค้า</div><div>หลังตรวจสอบ</div></th>
				<th>ยอดค่าสินค้า</th>
				<th width="14%">สถานะ</th>
				<th width="5%">คืนเงินแล้ว</th>
				<th width="10%">Action</th>
				<th>Taobao</th>
				<th>Tracking no.</th>
                </tr>
			<?php
           		$orderBy = ' ORDER BY o.order_number DESC';
				$sql = 'SELECT o.order_id,o.order_number,o.customer_id,o.date_order_created,o.order_status_code,c.customer_firstname,c.customer_lastname,'
												. 'o.total_shop,o.total_link,o.product_quantity,o.order_price_yuan,process_status,taobao,tracking_no,product_available,o.flag_return'
                                                . ' FROM customer_order o'
                                                . ' JOIN customer_order_product op ON o.order_id=op.order_id'
                                                . ' JOIN customer c ON o.customer_id = c.customer_id';
                                                //. ' LEFT JOIN customer_order_product p ON o.order_id=p.order_id';
           		$orderBy = ' ORDER BY o.order_number DESC';
           		$groupBy = ' GROUP BY o.order_id';
           		$limit = ' LIMIT '.$nowPage*$pageSize.','.$pageSize;

           		//for paging
				if($stmt = $con->prepare($sql.$search.$groupBy.$orderBy)){
					$stmt->execute();
					$stmt->store_result();
					$count = $stmt->num_rows;
					$allPage = ceil($count/$pageSize);
					$stmt->close();
				}

				//echo $sql.$search.$groupBy.$orderBy.$limit;
				if($stmt = $con->prepare($sql.$search.$groupBy.$orderBy.$limit)) {             	
                	$stmt->execute();
					$stmt->bind_result($order_id,$order_number,$customer_id,$datetime,$status,$fname,$lname,$totalShop,$totalLink,$quatity,$price,$processStat,$taobao,$trckno,$productAvail,$flag_return);
					$puncCount = 0;
					while($stmt->fetch()) {
                       	//date and time for 'Add Time' column
                        $addDate=substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
                        $addTime=substr($datetime,10,9);
                        $return = '';
                        if ($status==3) {
                        		$return = '-';
                        }
                        else if ($status>3){
                        		if ($flag_return==0) {
                        				$return = 'ยังไม่ได้คืน';
                        		}
                        		else if ($flag_return==1) {
                        				$return = 'คืนเงินแล้ว';
                        		}
                        }
						echo '<tr class="'.($puncCount%2==0? 'punc ':'').($status!=2? 'normal ':'').($status==9? 'cancel ':'').'">';
                                                        //. ' onClick="toProduct(\''.$order_id.'\')">'.
						echo '<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'">'.$order_number.'</td>'.
						'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'customer">'.$fname.' '.$lname.'</td>'.
                        '<input id="'.$order_id.'cid" type="hidden" value="'.$customer_id.'"/>'.
						'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'datetime">'.$addDate.' '.$addTime.'</td>'.
						'<td align="center" onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'totalShop">'.$totalShop.'</td>'.
						'<td align="center" onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'totalLink">'.$totalLink.'</td>'.
						'<td align="center" onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'quatity">'.$quatity.'</td>'.
						'<td align="center" onClick="toProduct(\''.$order_id.'\')">'.$productAvail.'</td>'.
						'<td align="right" onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'price">'.number_format($price,2).'</td>'.
                    	'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'status">'.$_codes[$status].'</td>'.
                        '<input id="'.$order_id.'st" type="hidden" value="'.$status.'"/>'.
						'<td>'.$return.'</td>';
						if ($_access[0]->action==1 || $_adminFlg==1) echo '<td style="text-align: center;"><button onClick="toProduct(\''.$order_id.'\')">Edit</button>';
						else echo '<td></td>';
						//'<form onsubmit="return confirm(\'ต้องการลบข้อมูลใช่หรือไม่?\');" action="index.php?page='.($nowPage+1).'" method="post">'.
						//'<input name="del" value="'.$order_id.'" type="hidden"/><button>Del</button>'.
						//'</form></td>'.
						echo '<td id="'.$order_id.'taobao"><div style="word-wrap:break-word;">'.$taobao.'</div></td>'.
						'<td id="'.$order_id.'tracking"><div style="word-wrap:break-word;">'.$trckno.'</div></td>'.
						'</tr>';
						$puncCount++;
					}
					$stmt->close();
				}
			?>
		</table><br>

		<div class="paging">
			<?php 
				echo 'หน้า&emsp;';
				for($i=1;$i<=$allPage;$i++) {
					if (($nowPage+1)!=$i) echo '<a href="?page='.$i.$request.'"><ins>'.intval($i).'</ins></a>';
					else echo '<a href="?page='.$i.$request.'">'.intval($i).'</a>';
				}
			?>
		</div>

		<div class="results">
                <table>
                    <tr>
                        <td><b>จำนวนรายการทั้งหมด</b></td>
                        <td><?php echo number_format($count); ?>&nbsp;</td>
                        <td>Orders<br></td>
                    </tr>
               	</table>
		</div>
	</body>


<?php
		include './dialog/searchBox.php';
		$con->close();
?>
</html>