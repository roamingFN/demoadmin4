<!DOCTYPE html>
<html>
	<head>
		<title>Order Tracking</title>
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
		<link rel="stylesheet" type="text/css" href="../css/w3-lime.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.5.1/chosen.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.5.1/chosen.jquery.js"></script>
		<?php
                        session_start();
                        if (!isset($_SESSION['ID'])){
                            header("Location: ../login.php");
                        }
                        
			include '../database.php';
           	
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

			//search
          	$_SESSION['sql'] = '';
			$cases = array();
			$request = '';
			ini_set('display_errors', 0);
			if(!empty($_GET['oid'])){array_push($cases,' o.order_id='.$_GET['oid']);$request .= 'oid='.$_GET['oid'];}
			if(!empty($_GET['cid'])) {
					//find CID
					$cid = getCID($_GET['cid']);
					array_push($cases,' o.customer_id='.$cid);
					$request .= 'cid='.$cid;
			}
			if(!empty($_GET['from'])){
					array_push($cases,' o.date_order_created>="'.substr($_GET['from'],6,4).'-'.substr($_GET['from'],3,2).'-'.substr($_GET['from'],0,2).'"');
                	$request .= 'from='.substr($_GET['from'],6,4).'-'.substr($_GET['from'],3,2).'-'.substr($_GET['from'],0,2);  
        	}
        	if(!empty($_GET['to'])){
                	array_push($cases,' o.date_order_created<="'.substr($_GET['to'],6,4).'-'.substr($_GET['to'],3,2).'-'.substr($_GET['to'],0,2).'"');
                	$request .= 'to='.substr($_GET['to'],6,4).'-'.substr($_GET['to'],3,2).'-'.substr($_GET['to'],0,2);
            }
			if(!empty($_GET['status'])){
					array_push($cases,' o.order_status_code= '.$_GET['status']);
					$request .= 'status='.$_GET['status'];
			}
			if(!empty($_GET['order_id'])){
					array_push($cases,' o.order_id= '.$_GET['order_id']);
					$request .= 'order_id='.$_GET['order_id'];
			}
			if(!empty($_GET['ptype'])){
					array_push($cases,' op.producttypeid='.$_GET['ptype']);
					$request .= 'ptype='.$_GET['ptype'];
			}
			ini_set('display_errors', 1);
			
			$search = ' WHERE (o.order_status_code>=4)';
			$searchTotal = '';
			if(sizeof($cases)>0){
				$search = $search.' AND'.$cases[0];
				$searchTotal = ' AND'.$cases[0];
				for($i=1;$i<sizeof($cases);$i++){
					$search .= ' AND'.$cases[$i];
					if(!(strpos($cases[$i],'status=')!==false)){
						$searchTotal .= ' AND'.$cases[$i];
					}
				}
			}

        	$_SESSION['sql'] = 'SELECT o.order_id,o.order_number,o.customer_id,o.date_order_created,o.order_status_code,c.customer_firstname,c.customer_lastname,'
            	   	. 'o.total_shop,o.total_link,o.product_quantity,o.order_price_yuan,o.process_status'
            	   	. ',COUNT(DISTINCT pt.order_product_tracking_id),COUNT(DISTINCT CASE WHEN pt.statusid=1 THEN pt.order_product_tracking_id END),COUNT(DISTINCT CASE WHEN pt.statusid=0 THEN pt.order_product_tracking_id END),SUM(op.order_shipping_cn_cost)'
               		. ' FROM customer_order o JOIN customer c ON o.customer_id = c.customer_id'
               		. ' LEFT JOIN customer_order_product_tracking pt ON o.order_id = pt.order_id'
               		. ' JOIN customer_order_product op ON o.order_id = op.order_id'
       		        . $search;
       		//echo $_SESSION['sql'];
                        
			//close
			if(isset($_POST['close'])) {
					echo '<script>alert("Close - '.$_POST['close'].'");</script>';
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
            $cus = array();
			if($stmt = $con->prepare('SELECT customer_id,customer_firstname,customer_lastname FROM customer')){
					$stmt->execute();
					$stmt->bind_result($cid,$cfname,$clname);
					while($stmt->fetch()) {
                 		$cus[$cid] = $cfname.' '.$clname;
					}
			}

           	//set status description
			$_codes = array();
			if($stmt = $con->prepare('SELECT status_id,des FROM order_status_code')){
					$stmt->execute();
					$stmt->bind_result($id,$des);
					while($stmt->fetch()) {
						$_codes[$id] = $des;
					}
			}

			//get Incomplete pic
			$sql = 'SELECT pt.order_id,p.product_id,p.product_img FROM customer_order_product_tracking pt'.
				' JOIN customer_order_product op ON op.order_product_id = pt.order_product_id'.
				' JOIN product p ON op.product_id = p.product_id'.
				' WHERE pt.statusid=0'.
				' GROUP BY pt.order_product_id';
			$_incom = array();
			if($stmt = $con->prepare($sql)) {
					$stmt->execute();
					$stmt->bind_result($order_id,$pid,$img);
					while($stmt->fetch()){
						$_incom[$order_id][$pid] = $img;
					}
			}
			//print_r($_incom);

			//get product type
			$_ptype = array();
			$_ptype[""] = "-";
			if($stmt = $con->prepare('SELECT producttypeid,producttypename,rate_type,product_type FROM product_type')){
				$stmt->execute();
				$stmt->bind_result($ptid,$ptname,$rate,$type);
				while($stmt->fetch()){
						$_ptype[$ptid] = $ptname;
				}
			}
		?>
                
        <!--show boxs or export-->
		<script>
			var searchOn = false;
			function searchBox(){
				document.getElementById('searchBox2').style.visibility = 'hidden';
				searchOn = !searchOn;
				if(searchOn){
					document.getElementById('searchBox').style.visibility = 'visible';
				}else{
					document.getElementById('searchBox').style.visibility = 'hidden';
				}
			}

			var searchOn2 = false;
			function searchBox2(){
				document.getElementById('searchBox').style.visibility = 'hidden';
				searchOn2 = !searchOn2;
				if(searchOn2){
					document.getElementById('searchBox2').style.visibility = 'visible';
				}else{
					document.getElementById('searchBox2').style.visibility = 'hidden';
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
            <h1><b><a href="index.php">Tracking</a></b></h1>
            <h3><a href="../index.php">&larr; Back</a></h3><br>
		<div class="menu">
			<i class="material-icons" onclick="exportExcel();" title="Export">insert_drive_file</i>
			<i class="material-icons" onclick="window.print();" title="Print">print</i>
			<i class="material-icons" onclick="searchBox();" title="Search">find_in_page</i>
			<i class="material-icons" onclick="searchBox2();" title="Search">find_in_page</i>
		</div>
		<table class="detail">
                <tr>
				<th>เลขที่ Order</th>
				<th>ชื่อลูกค้า</th>
				<th>วันที่ Order</th>
				<th>ยอดค่าสินค้า</th>
				<th>ค่าขนส่ง</th>
				<th>สถานะ</th>
				<th>จำนวน Tracking</th>
				<th>Tracking Completed</th>
				<th>Tracking Incompleted</th>
				<th>Action</th>
                </tr>
			<?php
				$sql = 'SELECT o.order_id,o.order_number,o.customer_id,o.date_order_created,o.order_status_code,c.customer_firstname,c.customer_lastname,'
            	   	. 'o.total_shop,o.total_link,o.product_quantity,o.order_price_yuan,o.process_status'
            	   	. ',COUNT(DISTINCT pt.order_product_tracking_id),COUNT(DISTINCT CASE WHEN pt.statusid=1 THEN pt.order_product_tracking_id END),COUNT(DISTINCT CASE WHEN pt.statusid=0 THEN pt.order_product_tracking_id END),SUM(op.order_shipping_cn_cost)'
               		. ' FROM customer_order o JOIN customer c ON o.customer_id = c.customer_id'
               		. ' LEFT JOIN customer_order_product_tracking pt ON o.order_id = pt.order_id'
               		. ' JOIN customer_order_product op ON o.order_id = op.order_id';
           		$orderBy = ' ORDER BY o.order_number DESC';
           		$groupBy = ' GROUP BY o.order_id';
				if($stmt = $con->prepare($sql.$search.$groupBy.$orderBy)){
					//paging
					$stmt->execute();
					$stmt->store_result();
					$count = $stmt->num_rows;
					$allPage = ceil($count/$pageSize);
					$stmt->close();
					
					//table
					$stmt = $con->prepare($sql.$search.$groupBy.$orderBy.' LIMIT '.$nowPage*$pageSize.','.$pageSize);
                	$stmt->execute();
					$stmt->bind_result($order_id,$order_number,$customer_id,$datetime,$status,$fname,$lname,$totalShop,$totalLink,$quatity,$price,$processStat,$tracking,$com,$incom,$tran);
					$puncCount = 0;
					while($stmt->fetch()){
                    	//date and time for 'Add Time' column
                        $addDate=substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
                        $addTime=substr($datetime,10,9);

						echo '<tr class="'.($puncCount%2==0? 'punc ':'').($status!=2? 'normal ':'').($status==9? 'cancel ':'').'">';
						echo '<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'">'.$order_number.'</td>'.
						'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'customer">'.$fname.' '.$lname.'</td>'.
                        '<input id="'.$order_id.'cid" type="hidden" value="'.$customer_id.'"/>'.
						'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'datetime">'.$addDate.' '.$addTime.'</td>'.
						'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'price">'.number_format($price,2).'</td>'.
                    	'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'tran">'.$tran.'</td>'.
                    	'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'status">'.$_codes[$status].'</td>'.
                    	'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'tracking">'.$tracking.'</td>'.
                    	'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'completed">'.$com.'</td>'.
                    	'<td onClick="toProduct(\''.$order_id.'\')" id="'.$order_id.'incompleted">'.$incom.'</td>'.
                        '<input id="'.$order_id.'st" type="hidden" value="'.$status.'"/>'.
						'<td><button onClick="toProduct(\''.$order_id.'\')">Edit</button>'.
						'<form onsubmit="return confirm(\'ต้องการปิดรายการใช่หรือไม่?\');" action="index.php?page='.($nowPage+1).'" method="post">'.
						'<input name="close" value="'.$order_id.'" type="hidden"/><button>Close</button>'.
						'</form>'.
						'</td></tr>';
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
                    <tr><th>Order No. :</th><td><input name="oid"/></td></tr>
                    <tr><th>ชื่อลูกค้า :</th><td>
						<?php 
							echo '<input name="cid" list="cid">';
                      		echo '<datalist id="cid">';
                      		reset($cus);
							for($i=0;$i<sizeof($cus);$i++){
									next($cus);
									echo '<option value="'.current($cus).'">';
							}
							echo '</datalist>';
						?>
					</select></td></tr>
                 	<tr><th>From :</th><td><input class="datetimepicker" name="from"/></td></tr>
                    <tr><th>To :</th><td><input class="datetimepicker" name="to"/></td></tr>
                    <tr><th>Status :</th><td><select name="status">
                    		<option value="" selected>-</option>
                            <option value="4"><?php echo $_codes[4]?></option>
                            <option value="5"><?php echo $_codes[5]?></option>
					</select></td></tr>
					<tr class="confirm"><td></td><td><a onclick="searchBox();">Cancel</a>&emsp;<button>Search</button></td></tr>
				</table>
			</form>
		</div>
		<!--
		<div id="searchBox2" style="background:rgba(0,0,0,0.5);
		margin 0 auto;
		position:fixed;
		width:100%;height:100%;top:0;left:0;
		visibility:hidden;">
			<div style="position:absolute;width:600px;top:10%;
			margin-left:auto;
			margin-right:auto;
			left:0;
   			right:0;
   			padding:10px;
			background:#fff">
			<form method="get">
					<center><h2 id="title">Incomplete</h2></center>
					<?php
						foreach ($_incom as $key => $value) {
							echo $key.'<br>';
							foreach ($value as $pic) {
								echo '<a href="index.php?order_id='.$key.'"><img height="70" width="70" src="'.$pic.'"/></a>';
							}
							echo '<br>';
						}
					?>
					<tr class="confirm"><td></td><td><a onclick="searchBox2();">Cancel</a>&emsp;</td></tr>
			</form>
			</div>
		</div>-->

		<!--Add Box-->
<div id="searchBox2" class="bgwrap" >
		<div class="container">
			<div class="containerheader">
        		<h2 id="title">Add Tracking</h2>
        		<h2 id="title">Incomplete</h2>
     		</div>
					
     		<div style="overflow:auto;height:200px">
     		<form method="post">  			
        		<table border="1px">
        		<form method="get">
					<?php
						foreach ($_incom as $key => $value) {
							echo '<tr><td>'.$key.'</td></tr>';
							foreach ($value as $pic) {
								echo '<tr><td><a href="index.php?order_id='.$key.'"><img height="70" width="70" src="'.$pic.'"/></a></td></tr>';
							}
						}
					?>
				</table>
			</form>
			</div>

			<form method="get">
			<div class="containerheader">
				<h2 id="title">Product type</h2>
			</div>
			<div>
				<table>
					<tr><td><select name="ptype" class="search-select">
						<?php foreach ($_ptype as $key => $value) {
								echo '<option value="'.$key.'">'.$value.'</option>';
						}?>
					</select></td></tr>
				</table>
			</div>	

			<div class="containerfooter">
				<a onclick="searchBox2();">Cancel</a>&emsp;<button>Search
			</div>
			</form>
		</div>
</div>

		<div class="paging">
			<?php 
				echo 'หน้า&emsp;';
				for($i=1;$i<=$allPage;$i++) {
					if (($nowPage+1)!=$i) echo '<a href="?page='.$i.'&'.$request.'"><ins>'.intval($i).'</ins></a>';
					else echo '<a href="?page='.$i.'&'.$request.'">'.intval($i).'</a>';
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
<script>
		$('.search-select').chosen();
</script>
<?php
	$con->close();
?>
