<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
				
	include '../database.php';


	//Tracking====================================================================================================
	$data_tracking = json_decode($_POST['data_tracking'],true);
	$sql = 'UPDATE customer_order_product'. 
		' SET order_shipping_cn_ref_no=?'. 
		' WHERE order_product_id=?';
	foreach($data_tracking as $key=>$item){
		if($key!='oid' && $key!='totalTracking' && $key!='remark'){
			$stmt = $con->prepare($sql);
			$stmt->bind_param('si',$item['ref'],$key);
			$res = $stmt->execute();
			if (!$res) {
				echo $con->error;
			}
		}
	}

	//update customer_order
	$sql = 'UPDATE customer_order'. 
		' SET tracking_no=?,remark=?'.
		' WHERE order_id=?';
	$stmt = $con->prepare($sql);
	$stmt->bind_param('ssi',$data_tracking['totalTracking'],$data_tracking['remark'],$data_tracking['oid']);
	$res = $stmt->execute();
	if(!$res){
		echo $con->error;
	}

	//del old trcking
	//$sqldel = 'DELETE FROM customer_order_product_tracking WHERE order_id=?';
	//$stmt = $con->prepare($sqldel);
	//$stmt->bind_param('i',$data_tracking['oid']);
	//$res = $stmt->execute();

	//insert
	$sql = 'INSERT INTO customer_order_product_tracking'. 
		' (order_product_id,order_id,tracking_no)'.
		' VALUES (?,?,?)';
	foreach($data_tracking as $key=>$item){
		if($key!='oid' && $key!='totalTracking' && $key!='remark') {
			//check tracking no
			$tracking_no = $item['ref'];
			$splited_trn = explode(",",$tracking_no);
			for ($i=0; $i<count($splited_trn); $i++) {
					if (!empty($key) && !empty($splited_trn[$i])) {
							if (isDup($splited_trn[$i],$data_tracking['oid'],$key)==0) {
									$stmt = $con->prepare($sql);
									$stmt->bind_param('iis',$key,$data_tracking['oid'],$splited_trn[$i]);
									$res = $stmt->execute();
							}
					}
					if(!$res){
						echo $con->error;
					}
			}
		}
	}

	//del not use
	foreach($data_tracking as $key=>$item) {
		if($key!='oid' && $key!='totalTracking' && $key!='remark') {		
			//split tracking no
			$tracking_no = $item['ref'];
			$splited_no = explode(",",$tracking_no);
			$tracking_curr = $item['curr_ref'];
			$splited_curr = explode(",", $tracking_curr);

			//get oid, opid
			$oid = $data_tracking['oid'];
			$opid = $key;

			if($tracking_curr==$tracking_no) {
				continue;
			}

			//if curr is blank quit 
			if ($tracking_curr=='') {
				continue;
			}

			//if insert blank deltete all
			if ($tracking_no=='') {
				$sql = 'DELETE FROM customer_order_product_tracking WHERE order_id='.$oid.' AND order_product_id='.$opid;
				$stmt = $con->prepare($sql);
				$res = $stmt->execute();
			}
			else {
				//del flag
				//0 - do not delete
				//other - delete by tracking_no			
				for ($i=0; $i<count($splited_curr); $i++) {
					$del = $splited_curr[$i];
					for ($j=0; $j<count($splited_no); $j++) {
						//echo $splited_curr[$i]."-".$splited_no[$j]."\r\n";
							if ($splited_curr[$i]==$splited_no[$j]) {
								$del = "0";
							}
					}
					
					if ($del!="0") {
						$sql = 'DELETE FROM customer_order_product_tracking'.
							' WHERE tracking_no=\''.$del.'\' AND order_id='.$oid.' AND order_product_id='.$opid;
						
						if ($stmt = $con->prepare($sql)) {
							//echo $sql;
							$res = $stmt->execute();
						}
						//echo $stmt->num_rows;
					}
					if(!$res){
						echo $con->error;
					}	
				}
			}
		}
	}
	
	//List=======================================================================================================
	$data = json_decode($_POST['data'],true);
	$sql = 'UPDATE customer_order_product_tracking'.
		' SET width=?,length=?,height=?,m3=?,weight=?,total=?,statusid=?,rate=?,type=?'.
		' WHERE tracking_no=?';
	foreach($data as $key=>$item) {
			$stmt = $con->prepare($sql);
			$stmt->bind_param('ddddddidis',$item['width'],$item['length'],$item['height'],$item['m3'],$item['weight'],$item['total'],$item['stat'],$item['rate'],$item['type'],$key);
			$res = $stmt->execute();
			if(!$res) {
				echo $con->error;
			}
	}
	 
	//Detail======================================================================================================
	$data = json_decode($_POST['data_detail'],true);
	$sql = 'UPDATE customer_order_product_tracking'. 
		' SET received_amount=?,uid=?'.
		' WHERE order_product_tracking_id=?';
	foreach($data as $key=>$item) {
			$stmt = $con->prepare($sql);
			$stmt->bind_param('isi',$item['rec'],$item['uid'],$key);
			$res = $stmt->execute();
			if (!$res) {
				echo $con->error;
			}
	}
	
	echo 'success';
	
	$con->close();

	function isDup($trn,$oid,$opid) {
			include '../database.php';
			$result = '';
			$sql = 'SELECT COUNT(*) FROM customer_order_product_tracking'.
				' WHERE order_product_id='.$opid.' AND tracking_no=\''.$trn.'\' AND order_id='.$oid;
			//echo $sql."\r\n";
			$stmt = $con->prepare($sql);
			$stmt->execute();
			$stmt->bind_result($count);
			while($stmt->fetch()) {
					$result = $count;
			}
			return $result;
	}
?>