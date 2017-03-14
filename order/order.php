<!DOCTYPE html>
<html>
	<head>
		<title>Order</title>
		<meta charset="utf-8">
                
                <link rel="stylesheet" href="../css/jquery-ui.css">
		<script src="../js/jquery-1.10.2.js"></script>
                <script src="../js/jquery-ui.js"></script>
                <script src="../css/jquery-ui-timepicker-addon.min.css"></script>
                <script src="../js/jquery-ui-timepicker-addon.min.js"></script>
                
		<script>
			$(function() {
                                $( ".datetimepicker" ).datetimepicker({
                                        dateFormat: "dd-mm-yy",
                                        timeFormat: "HH:mm:ss",
                                        showSecond:true
				});        
			});
		</script>
                
		<link rel="stylesheet" type="text/css" href="../css/cargo.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		<style>
				i{
						color:#FF9933;
				}
				button,.button{
						color:#FF9933;
				}
				a{
						color:#FF9933;
				}
				th{
						background:#FF9933;
				}
				.undivide th{
						background:#FF9933;
				}
				.order-button:hover{
						color:#FF9933;
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
			if(!empty($_GET['oid'])){array_push($cases,' o.order_id='.$_GET['oid']);$request .= 'o.order_id='.$_GET['oid'];}
			if(!empty($_GET['cid'])){array_push($cases,' o.customer_id='.$_GET['cid']);$request .= 'o.customer_id='.$_GET['cid'];}
			if(!empty($_GET['from'])&&!empty($_GET['to'])){
                            array_push($cases," o.date_order_created BETWEEN '".$_GET['from']."' AND '".$_GET['to']."' ");
                            $request .= " o.date_order_created BETWEEN '".$_GET['from']."' AND '".$_GET['to']."' ";   
                        }
			if(!empty($_GET['status'])){array_push($cases,' o.order_status_code= '.$_GET['status']);$request .= 'order_status_code='.$_GET['status'];}
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
                        
			//add
			if(isset($_POST['add'])){
                            //echo $_POST['oid'].$_POST['cid'].$_POST['datetime'].$_POST['status'];
                            if (empty($_POST['status'])) echo "<br>".$_POST['status'];
                                if(!empty($_POST['oid'])&&!empty($_POST['cid'])&&!empty($_POST['datetime']))
                                {
                                        //formated datetime 'yyyy-mm-dd h:m:s'
                                        $addDateTime=substr($_POST['datetime'],6,4).'-'.substr($_POST['datetime'],3,2).'-'.substr($_POST['datetime'],0,2). ' '.substr($_POST['datetime'],10,9);
					//echo $addDateTime;
                                        $stmt = $con->prepare('INSERT INTO customer_order(order_id,customer_id,date_order_created,order_status_code) VALUES(?,?,?,?)');
					$stmt->bind_param('iiss',$_POST['oid'],$_POST['cid'],$addDateTime,$_POST['status']);
					$res = $stmt->execute();
					if(!$res){
						echo '<script>alert("การเพิ่มข้อมูลล้มเหลว");</script>';
					}
                                        else {
                                                echo '<script>alert("เพิ่มข้อมูลสำเร็จ");</script>';
                                        }
				} 
                                else if(empty($_POST['oid'])||empty($_POST['cid']))
                                {
					echo '<script>alert("กรุณาใส่ข้อมูลให้ครบทุกช่องค่ะ!");</script>';
				}
			}
			
			//edit
			if(isset($_POST['edit'])){
                            //echo $_POST['oid'].$_POST['cid'].$_POST['datetime'].$_POST['status'];
                                //formated datetime 'yyyy-mm-dd h:m:s'
                                $addDateTime=substr($_POST['datetime'],6,4).'-'.substr($_POST['datetime'],3,2).'-'.substr($_POST['datetime'],0,2). ' '.substr($_POST['datetime'],10,9);
				$stmt = $con->prepare('UPDATE customer_order SET customer_id=?,date_order_created=?,order_status_code=? WHERE order_id=?');
				$stmt->bind_param('sssi',$_POST['cid'],$addDateTime,$_POST['status'],$_POST['oid']);
				$res = $stmt->execute();
				if(!$res){
					echo '<script>alert("การแก้ไขข้อมูลล้มเหลว");</script>';
				}
                                else{
                                        echo '<script>alert("แก้ไขข้อมูลสำเร็จ");</script>';
                                }
			}
			
			//delete
			if(isset($_POST['del'])){
				if($stmt = $con->prepare('DELETE FROM customer_order WHERE order_id="'.$_POST['del'].'"')){
					$res = $stmt->execute();
					if(!$res){
						echo '<script>alert("การลบข้อมูลล้มเหลว");</script>';
					}
                                        else{
                                                echo '<script>alert("ลบข้อมูลสำเร็จ");</script>';
                                        }
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
		?>
                
                <!--show boxs or export-->
		<script>
			var addOn = false;
			function add(){
				document.getElementById('editBox').style.visibility = 'hidden';
				document.getElementById('searchBox').style.visibility = 'hidden';
				addOn = !addOn;
				if(addOn){
					document.getElementById('addBox').style.visibility = 'visible';
				}else{
					document.getElementById('addBox').style.visibility = 'hidden';
				}
			}
			var editOn = false;
			function edit(oid){
				document.getElementById('addBox').style.visibility = 'hidden';
				document.getElementById('searchBox').style.visibility = 'hidden';
				editOn = !editOn;
				if(editOn){
					document.getElementById('editBox').style.visibility = 'visible';
					document.getElementById('e-oid').value = document.getElementById(oid).textContent;
					document.getElementById('e-cid-'+document.getElementById(oid+'cid').value).selected = true;
                                        document.getElementById('e-datetime').value = document.getElementById(oid+'datetime').textContent;
					document.getElementById('e-status-'+document.getElementById(oid+'st').value).selected = true;
				}else{
					document.getElementById('editBox').style.visibility = 'hidden';
				}
			}
			var searchOn = false;
			function searchBox(){
				document.getElementById('addBox').style.visibility = 'hidden';
				document.getElementById('editBox').style.visibility = 'hidden';
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
                                //click edit
                                if (editOn){
                                    
                                }
                                else {
                                    location.href="product.php?order_id=" + oid;
                                }
                        }
		</script>
	</head>
	<body>
            <h1><a href="order.php">Order</a></h1>
            <h3><a href="../index.php">&larr; Back</a></h3><br>
		<div class="menu">
			<i class="material-icons" onclick="add();" title="Add">add_circle</i>
			<i class="material-icons" onclick="exportExcel();" title="Export">insert_drive_file</i>
			<i class="material-icons" onclick="window.print();" title="Print">print</i>
			<i class="material-icons" onclick="searchBox();" title="Search">find_in_page</i>
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
				<th>สถานะการสั่งซื้อ</th>
				<th>สถานะ</th>
				<th>Action</th>
                </tr>
			<?php
                                //get customer name
				$cus_info = array();
                                $cus_id = array();
				if($stmt = $con->prepare('SELECT customer_id,customer_firstname,customer_lastname FROM customer')){
					$stmt->execute();
					$stmt->bind_result($cid,$cfname,$clname);
					while($stmt->fetch()){
						array_push($cus_info,$cid.' '.$cfname.' '.$clname);
                                                array_push($cus_id, $cid);
					}
				}
                                $orderBy = ' ORDER BY o.order_number DESC';
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
                                                //set status_info
                                                if ($status==0) $status_info="รอตรวจสอบยอด";
                                                if ($status==1) $status_info="ตรวจสอบแล้วรอชำระเงิน";
                                                if ($status==2) $status_info="ชำระเงินแล้ว ดำเนินการสั่งซื้อ";
                                                if ($status==3) $status_info="ร้านค้ากำลังส่งสินค้ามาโกดังจีน";
                                                if ($status==4) $status_info="โกดังจีนรับของแล้ว";
                                                if ($status==5) $status_info="สินค้าอยู่ระหว่างมาไทย";
                                                if ($status==6) $status_info="สินค้าถึงไทยแล้ว";
                                                if ($status==7) $status_info="ชำระค่าขนส่งแล้ว รอจัดส่งสินค้า";
                                                if ($status==8) $status_info="สินค้าจัดส่งให้ลูกค้าแล้ว";
                                                if ($status==9) $status_info="ยกเลิก";
                                                //date and time for 'Add Time' column
                                                $addDate=substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
                                                $addTime=substr($datetime,10,9);
                                                //set process_stat
                                                if ($processStat==0) $processStatDesc="รอสั่ง";
                                                if ($processStat==1) $processStatDesc="กำลังสั่ง";
                                                if ($processStat==2) $processStatDesc="สั่งแล้ว";

						echo '<tr class="'.($puncCount%2==0? 'punc ':'').($status!=2? 'normal ':'').($status==9? 'cancel ':'').'"'
                                                        . ' onClick="toProduct(\''.$order_id.'\')">'.
						'<td id="'.$order_id.'">'.$order_number.'</td>'.
						'<td id="'.$order_id.'customer">'.$fname.' '.$lname.'</td>'.
                        '<input id="'.$order_id.'cid" type="hidden" value="'.$customer_id.'"/>'.
						'<td id="'.$order_id.'datetime">'.$addDate.' '.$addTime.'</td>'.
						'<td id="'.$order_id.'totalShop">'.$totalShop.'</td>'.
						'<td id="'.$order_id.'totalLink">'.$totalLink.'</td>'.
						'<td id="'.$order_id.'quatity">'.$quatity.'</td>'.
						'<td id="'.$order_id.'price">'.number_format($price,2).'</td>'.
						'<td id="'.$order_id.'processStat">'.$processStatDesc.'</td>'.
                    	'<td id="'.$order_id.'status">'.$status_info.'</td>'.
                        '<input id="'.$order_id.'st" type="hidden" value="'.$status.'"/>'.
						'<td><button onclick="edit(\''.$order_id.'\')">Edit</button>'.
						'<form onsubmit="return confirm(\'ต้องการลบข้อมูลใช่หรือไม่?\');" action="order.php?page='.($nowPage+1).'" method="post">'.
						'<input name="del" value="'.$order_id.'" type="hidden"/><button>Del</button>'.
						'</form>'.
						'</td></tr>';
						$puncCount++;
					}
					$stmt->close();
				}
			?>
		</table><br>
                    <div id="addBox" class="wrap">
			<form method="post">
                                <table>
					<tr><th><h2 id="title">Add</h2></th><td></td></tr>
					<tr><th>Order No. :</th><td><input name="oid" required="required"/></td></tr>
                                        <tr><th>ชื่อลูกค้า :</th><td><select name="cid">
						<?php 
							for($i=0;$i<sizeof($cus_info);$i++){
								echo '<option value="'.$cus_id[$i].'">'.$cus_info[$i].'</option>';
							}
						?>
					</select></td></tr>
                                        <tr><th>วันที่สั่งซื้อ :</th><td><input class="datetimepicker" name="datetime" required="required"/></td></tr>
                                        <tr><th>สถานะ :</th>
                                            <td><select name="status">
                                                    <option value="0">รอตรวจสอบยอด</option>
                                                    <option value="1">ตรวจสอบแล้วรอชำระเงิน</option>
                                                    <option value="2">ชำระเงินแล้ว ดำเนินการสั่งซื้อ</option>
                                                    <option value="3">ร้านค้ากำลังส่งสินค้ามาโกดังจีน</option>
                                                    <option value="4">โกดังจีนรับของแล้ว</option>
                                                    <option value="5">สินค้าอยู่ระหว่างมาไทย</option>
                                                    <option value="6">สินค้าถึงไทยแล้ว</option>
                                                    <option value="7">ชำระค่าขนส่งแล้ว รอจัดส่งสินค้า</option>
                                                    <option value="8">สินค้าจัดส่งให้ลูกค้าแล้ว</option>
                                                    <option value="9">ยกเลิก</option>
                                                </select>
                                            </td></tr>
					<input type="hidden" name="add" value="1"/>
					<tr class="confirm"><td></td><td><a onclick="add();">Cancel</a>&emsp;<button>Insert</button></td></tr>
				</table>
			</form>
		</div>
		<div id="editBox" class="wrap">
			<form method="post">
				<table>
					<tr><th><h2 id="title">Edit</h2></th><td></td></tr>
					<tr><th>Order No. :</th><td><input id="e-oid" name="oid" readonly/></td></tr>
                                        <tr><th>ชื่อลูกค้า :</th><td><select name="cid">
						<?php 
							for($i=0;$i<sizeof($cus_info);$i++){
								echo '<option id="e-cid-'.$cus_id[$i].'" value="'.$cus_id[$i].'">'.$cus_info[$i].'</option>';
							}
						?>
					</select></td></tr>
                                        <tr><th>วันที่สั่งซื้อ :</th><td><input class="datetimepicker" id="e-datetime" name="datetime" step="1"/></td></tr>
                                        <tr><th>สถานะ :</th>
                                            <td><select name="status">
                                                    <option id="e-status-0" value="0">รอตรวจสอบยอด</option>
                                                    <option id="e-status-1" value="1">ตรวจสอบแล้วรอชำระเงิน</option>
                                                    <option id="e-status-2" value="2">ชำระเงินแล้ว ดำเนินการสั่งซื้อ</option>
                                                    <option id="e-status-3" value="3">ร้านค้ากำลังส่งสินค้ามาโกดังจีน</option>
                                                    <option id="e-status-4" value="4">โกดังจีนรับของแล้ว</option>
                                                    <option id="e-status-5" value="5">สินค้าอยู่ระหว่างมาไทย</option>
                                                    <option id="e-status-6" value="6">สินค้าถึงไทยแล้ว</option>
                                                    <option id="e-status-7" value="7">ชำระค่าขนส่งแล้ว รอจัดส่งสินค้า</option>
                                                    <option id="e-status-8" value="8">สินค้าจัดส่งให้ลูกค้าแล้ว</option>
                                                    <option id="e-status-9" value="9">ยกเลิก</option>
                                                </select>
                                            </td></tr>
					<input type="hidden" name="edit" value="1"/>
					<tr class="confirm"><td></td><td><a onclick="edit();">Cancel</a>&emsp;<button>Update</button></td></tr>
				</table>
			</form>
		</div>
		<div id="searchBox" class="wrap">
			<form method="get">
				<table>
					<tr><th><h2 id="title">Search</h2></th><td></td></tr>
                                        <tr><th>Order No. :</th><td><input name="oid"/></td></tr>
                                        <tr><th>ชื่อลูกค้า :</th><td><select name="cid">
						<?php 
                                                        echo '<option value="">-</option>';
							for($i=0;$i<sizeof($cus_info);$i++){
								echo '<option value="'.$cus_id[$i].'">'.$cus_info[$i].'</option>';
							}
						?>
					</select></td></tr>
                                        <tr><th>From :</th><td><input class="datetimepicker" type="datetime-local" name="from"/></td></tr>
                                        <tr><th>To :</th><td><input class="datetimepicker" type="datetime-local" name="to"/></td></tr>
                                        <tr><th>สถานะ :</th>
                                            <td><select name="status">
                                                    <option value="">-</option>
                                                    <option value="0">รอตรวจสอบยอด</option>
                                                    <option value="1">ตรวจสอบแล้วรอชำระเงิน</option>
                                                    <option value="2">ชำระเงินแล้ว ดำเนินการสั่งซื้อ</option>
                                                    <option value="3">ร้านค้ากำลังส่งสินค้ามาโกดังจีน</option>
                                                    <option value="4">โกดังจีนรับของแล้ว</option>
                                                    <option value="5">สินค้าอยู่ระหว่างมาไทย</option>
                                                    <option value="6">สินค้าถึงไทยแล้ว</option>
                                                    <option value="7">ชำระค่าขนส่งแล้ว รอจัดส่งสินค้า</option>
                                                    <option value="8">สินค้าจัดส่งให้ลูกค้าแล้ว</option>
                                                    <option value="9">ยกเลิก</option>
                                                </select>
                                            </td></tr>
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
                            <td>Orders<br></td>
                        </tr>
		</div>
	</body>
</html>
<?php
	$con->close();
?>
