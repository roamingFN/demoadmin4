<!DOCTYPE html>
<html>
	<head>
		<title>สถานะรายการ</title>
		<meta charset="utf-8">         
      	<link rel="stylesheet" href="../css/jquery-ui.css">
		<script src="../js/jquery-1.10.2.js"></script>
        <script src="../js/jquery-ui.js"></script>
        <script src="../css/jquery-ui-timepicker-addon.min.css"></script>
        <script src="../js/jquery-ui-timepicker-addon.min.js"></script>
        <script src="./controller.js"></script>
                
		<script>
				initIndex();
				setDatePicker();
		</script>
                
		<link rel="stylesheet" type="text/css" href="../css/cargo.css">
		<link rel="stylesheet" type="text/css" href="../css/w3-orange.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		<?php
	        session_start();
	        if (!isset($_SESSION['ID'])){
	            header("Location: ../login.php");
	        }
                        
			include '../database.php';
			include './dialog/cancelBox.php';
			include '../utility/permission.php';

			const FORMID = 4;
			$_access = json_decode(getAccessForm($con,FORMID,$_SESSION['USERID']));
			$_adminFlg = getAdminFlag($con,$_SESSION['ID']);
			if ($_adminFlg==0) {
					if (empty($_access) || $_access[0]->visible==0) header ("Location: ../login.php");
			}
           	
           	function getCID($rs){
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

          	function sendEmail($ono,$cmail,$cname) {
				$strTo = $cmail;
				$strSubject = '=?UTF-8?B?'.base64_encode('รายการสั่งซื้อหมายเลข '.$ono.' ของท่านได้ตรวจสอบเสร็จแล้ว').'?=';
				$strHeader = "MIME-Version: 1.0\' . \r\n";
				$strHeader .= "Content-type: text/html; charset=utf-8\r\n";
				$strHeader .= "From: support@order2easy.com";
				$strMessage = "สวัสดีค่ะ คุณ ".$cname."<br><br>".				
				"&nbsp;&nbsp;&nbsp;รายการสั่งซื้อ ".$ono." ของท่านได้ตรวจสอบเสร็จเรียบร้อยแล้วนะคะ<br>".
				"<br>ท่านสามารถดูรายละเอียดได้จากหน้ารายการสั่งซื้อของท่าน".
				"<br>หากรายละเอียดรายการสั่งซื้อถูกต้อง โปรดชำระภายใน 7 วัน".
				"<br>เพื่อทางเราจะได้ทำการจัดซื้อต่อไปค่ะ".
				"<br>(หากลูกค้าไม่ชำระภายในเวลาที่กำหนด รายการสั่งซื้อนี้จะถูกยกเลิกค่ะ)".
				"<br>สอบถามโทร 02-924-5850".			
				"<br><br>order2easy".
				"<br>เจ้าหน้าที่ผู้ตรวจสอบรายการ: ".$_SESSION['ID'].
				"<br>".date('Y-m-d H:i:s');

				@mail($strTo,$strSubject,$strMessage,$strHeader);
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
			if(!empty($_GET['from'])) {
					array_push($cases,' o.date_order_created>="'.substr($_GET['from'],6,4).'-'.substr($_GET['from'],3,2).'-'.substr($_GET['from'],0,2).' 00:00:00"');
                	$request .= '&from='.$_GET['from'];  
        	}
        	if(!empty($_GET['to'])) {
                	array_push($cases,' o.date_order_created<="'.substr($_GET['to'],6,4).'-'.substr($_GET['to'],3,2).'-'.substr($_GET['to'],0,2).' 23:59:59"');
                	$request .= '&to='.$_GET['to'];
            }
            if(!empty($_GET['status']) || ($_GET['status']=="0")) {
            		//echo $_GET['status'];
            		if ($_GET['status']=="-") {
            				array_push($cases,' (o.order_status_code=0 or o.order_status_code=1)');
            		}
            		else {
							array_push($cases,' o.order_status_code= '.$_GET['status']);
					}
					$request .= '&status='.$_GET['status'];
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
        	$_SESSION['sql'] = 'SELECT o.order_id,o.order_number,o.customer_id,o.date_order_created,o.order_status_code,c.customer_firstname,c.customer_lastname,'
            	   	. 'o.total_shop,o.total_link,o.product_quantity,o.order_price_yuan,process_status'
               		. ' FROM customer_order o JOIN customer c'
       		        . ' ON o.customer_id = c.customer_id'.$search;
       		//echo $_SESSION['sql'];
                        
			//delete
       		if(isset($_POST['del'])) {
				/*$sqldel1 = 'DELETE FROM customer_order WHERE order_id='.$_POST['del'].';';
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
				$res = $stmt->execute();*/
				$sqldel = 'UPDATE customer_order SET order_status_code=99 WHERE order_id='.$_POST['del'].';';
				$stmt = $con->prepare($sqldel);
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
			// if($stmt = $con->prepare('SELECT customer_id,customer_firstname,customer_lastname FROM customer')){
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
	</head>

	<body>
            <h1><b><a href="index.php">สถานะรายการ</a></b></h1>
            <h3><a href="../index.php">&larr; Back</a></h3><br>
		<div class="menu">
			<?php if ($_access[0]->canadd==1) echo '<i class="material-icons" onclick="exportExcel();" title="Export">&#xE24D;</i>'; ?>
			<i class="material-icons" onclick="window.print();" title="Print">&#xE8AD;</i>
			<i class="material-icons" onclick="searchBox();" title="Search">&#xE880;</i>
		</div>
		<table class="detail">
                <tr>
				<th>เลขที่ Order</th>
				<th>ชื่อลูกค้า</th>
				<th>วันที่ Order</th>
				<th>จำนวนร้านค้า</th>
				<th>จำนวน link</th>
				<th>จำนวนสินค้า</th>
				<th>ยอดค่าสินค้า</th>
				<th>สถานะ</th>
				<th>Action</th>
                </tr>
			<?php        
           		$orderBy = ' ORDER BY o.order_number DESC';
           		//for paging
				if($stmt = $con->prepare('SELECT o.order_id,o.customer_id,o.date_order_created,o.order_status_code,c.customer_firstname,c.customer_lastname,'
												. 'o.total_shop,o.total_link,o.product_quantity,o.order_price_yuan,process_status'
                                                . ' FROM customer_order o JOIN customer c'
                                                . ' ON o.customer_id = c.customer_id'.$search.$orderBy)){
					$stmt->execute();
					$stmt->store_result();
					$count = $stmt->num_rows;
					$allPage = ceil($count/$pageSize);
					$stmt->close();
				}
				if($stmt = $con->prepare('SELECT o.order_id,o.customer_id,o.date_order_created,o.order_status_code,c.customer_firstname,c.customer_lastname,'
												. 'o.total_shop,o.total_link,o.product_quantity,o.order_price_yuan,process_status'
                                                . ' FROM customer_order o JOIN customer c'
                                                . ' ON o.customer_id = c.customer_id'.$search.$orderBy)){
					$stmt->execute();
					$stmt->store_result();
					$count = $stmt->num_rows;
					$allPage = ceil($count/$pageSize);
					$stmt->close();
					
					$stmt = $con->prepare('SELECT o.order_id,o.order_number,o.customer_id,o.date_order_created,o.order_status_code,c.customer_firstname,c.customer_lastname,'
                                                . 'o.total_shop,o.total_link,o.product_quantity,o.order_price_yuan,o.process_status'
                                                . ' FROM customer_order o JOIN customer c'
                                                . ' ON o.customer_id = c.customer_id'.$search
                                                . $orderBy
                                                . ' LIMIT '.$nowPage*$pageSize.','.$pageSize);
					//echo 'SELECT * FROM cash '.$search.' LIMIT '.$nowPage*$pageSize.','.$pageSize;
                	$stmt->execute();
					$stmt->bind_result($order_id,$order_number,$customer_id,$datetime,$status,$fname,$lname,$totalShop,$totalLink,$quatity,$price,$processStat);
					$puncCount = 0;
					while($stmt->fetch()){
                        //date and time for 'Add Time' column
                        $addDate=substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
                  	    $addTime=substr($datetime,10,9);

						echo '<tr class="'.($puncCount%2==0? 'punc ':'').($status!=2? 'normal ':'').($status==9? 'cancel ':'').'">';
                                                        //. ' onClick="toProduct(\''.$order_id.'\')">'.
						echo '<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'">'.$order_number.'</td>'.
						'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'customer">'.$fname.' '.$lname.'</td>'.
                        '<input id="'.$order_id.'cid" type="hidden" value="'.$customer_id.'"/>'.
						'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'datetime">'.$addDate.' '.$addTime.'</td>'.
						'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'totalShop">'.$totalShop.'</td>'.
						'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'totalLink">'.$totalLink.'</td>'.
						'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'quatity">'.$quatity.'</td>'.
						'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'price">'.number_format($price,2).'</td>'.
                    	'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'status">'.$_codes[$status].'</td>';
						if ($_access[0]->action==1 || $_adminFlg==1) {
								echo '<td><button onClick="toProduct(\''.$order_id.'\')">Edit</button>'.
								'<button onClick="showCancelBox('.$order_id.')">Cancel</button></td>';
						}
						else {
								echo '<td></td>';
						}
						echo '</tr>';
						$puncCount++;

						echo '<input type="hidden" id="amount-'.$order_id.'" value='.$price.'>';
						echo '<input type="hidden" id="dt-'.$order_id.'" value="'.$datetime.'">';
						echo '<input id="'.$order_id.'st" type="hidden" value="'.$status.'"/>'; 
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
</html>
<?php
	include './dialog/searchBox.php';
	$con->close();
?>
