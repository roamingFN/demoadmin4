<!DOCTYPE html>
<html>
	<head>
		<title>Payment</title>
		<meta charset="utf-8">
		<!--Jquery Datepicker Timepicker-->
                <link rel="stylesheet" href="../css/jquery-ui.css">
				<script src="../js/jquery-1.10.2.js"></script>
                <script src="../js/jquery-ui.js"></script>
                <script src="../css/jquery-ui-timepicker-addon.min.css"></script>
                <script src="../js/jquery-ui-timepicker-addon.min.js"></script>
                <script src="./controller.js"></script>
                
                <!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">-->
                <!--<script src="//code.jquery.com/jquery-1.10.2.js"></script>-->
                <!--<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>-->
                <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.css"></script>-->
		<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.js"></script>-->
                
		<script>
			$(function() {
				$( ".datepicker" ).datepicker({
					dateFormat: "dd-mm-yy"
				});
				$( ".timepicker" ).timepicker({
					timeFormat: "HH:mm:ss"
				});
			});
		</script>
                
		<link rel="stylesheet" type="text/css" href="../css/cargo.css">
		<link rel="stylesheet" type="text/css" href="../css/w3-indigo.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
		<?php
            session_start();
            if (!isset($_SESSION['ID'])){
                header("Location: ../login.php");
            }
                        
			include '../database.php';
			include '../utility/permission.php';

			const FORMID = 3;
			$_access = json_decode(getAccessForm($con,FORMID,$_SESSION['USERID']));
			$_adminFlg = getAdminFlag($con,$_SESSION['ID']);
			if ($_adminFlg==0) {
					if (empty($_access) || $_access[0]->visible==0) header ("Location: ../login.php");
			}
			
			//search
           	$_SESSION['sql'] = '';
			$cases = array();
			$request = '';
			ini_set('display_errors', 0);
			if(!empty($_GET['customer'])) {
				array_push($cases,' c.customer LIKE "%'.$_GET['customer'].'%"');
				$request .= 'customer='.$_GET['customer'];
			}
			if(!empty($_GET['crn'])){array_push($cases,' c.crn LIKE "%'.$_GET['crn'].'%"');$request .= 'crn='.$_GET['crn'];}
			if(!empty($_GET['from'])){            
                array_push($cases,' c.date>="'.substr($_GET['from'],6,4).'-'.substr($_GET['from'],3,2).'-'.substr($_GET['from'],0,2).' 00:00:00"');
                $request .= 'from='.$_GET['from'];
            }
			if(!empty($_GET['to'])){
                array_push($cases,' c.date<="'.substr($_GET['to'],6,4).'-'.substr($_GET['to'],3,2).'-'.substr($_GET['to'],0,2).' 23:59:59"');
                $request .= 'to='.$_GET['to'];
            }
			//if(!empty($_GET['acn'])){array_push($cases,' acn LIKE "%'.$_GET['acn'].'%"');$request .= 'acn='.$_GET['acn'];}
			if(!empty($_GET['cbid'])){array_push($cases,' c.cbid LIKE "%'.$_GET['cbid'].'%"');$request .= 'cbid='.$_GET['cbid'];}
			//if(!empty($_GET['status'])||$_GET['status']=='0'){array_push($cases,' status='.$_GET['status']);$request .= 'status='.$_GET['status'];}
			if(!empty($_GET['amount'])){array_push($cases,' c.amount='.$_GET['amount']);$request .= 'amount='.$_GET['amount'];}
			if(!empty($_GET['uid'])){array_push($cases,' c.uid="'.$_GET['uid'].'"');$request .= 'uid='.$_GET['uid'];}
            if(isset($_GET['status'])){
				if($_GET['status']=='-'){
					array_push($cases,' c.status>=0');
					$request .= 'status=-';
				}else{
					array_push($cases,' c.status='.$_GET['status']);
					$request .= 'status='.$_GET['status'];
				}
            }
            if(!empty($_GET['searchall'])) {
				array_push($cases,' c.crn LIKE \'%'.$_GET['searchall'].'%\''.
										' or c.customer LIKE \'%'.$_GET['searchall'].'%\''.
										' or c.amount=\''.$_GET['searchall'].'\''.
										' or c.remark LIKE \'%'.$_GET['searchall'].'%\''.
										' or c.remarkc LIKE \'%'.$_GET['searchall'].'%\''.
										' or c.uid LIKE \'%'.$_GET['searchall'].'%\''.
										' or c.branch LIKE \'%'.$_GET['searchall'].'%\''.
										' or bp.account_name LIKE \'%'.$_GET['searchall'].'%\''.
										' or bp.account_no LIKE \'%'.$_GET['searchall'].'%\''.
										' or bp.bank_name_en LIKE \'%'.$_GET['searchall'].'%\'');
				$request .= 'searchall='.$_GET['searchall'];
			}
			if(!empty($_GET['cbid2'])) {
				array_push($cases,' bp.bank_name_en="'.$_GET['cbid2'].'"');
				$request .= 'cbid2='.$_GET['cbid2'];
			}
			ini_set('display_errors', 1);
			
			$search = ' WHERE c.status=0';               
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
                        $_SESSION['sql'] = 'SELECT c.cashid, c.crn,c.customer,c.date,c.time,c.amount,c.remark,c.branch,c.bid,c.acn,c.uid,c.ctime,c.remarkc,c.status,c.cbid,c.topup_id'.
             				' FROM cash c JOIN bank_payment bp ON c.cbid=bp.bank_id'.$search;	
                        //echo $_SESSION['sql'];
                        
			//edit
			if(isset($_POST['edit'])){
                                //format date
                                $formatted_date = substr($_POST['date'],6,4).'-'.substr($_POST['date'],3,2).'-'.substr($_POST['date'],0,2);
				
				$stmt = $con->prepare('UPDATE cash SET customer=?,date=DATE(?),time=TIME(?),amount=?,branch=?,remark=?,bid=?,cbid=? WHERE crn=?');
				$stmt->bind_param('sssdssiss',$_POST['customer'],$formatted_date,$_POST['time'],$_POST['amount'],$_POST['branch'],$_POST['remark'],$_POST['bid'],$_POST['cbid'],$_POST['crn']);
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
				if($stmt = $con->prepare('DELETE FROM cash WHERE crn="'.$_POST['del'].'"')){
					$res = $stmt->execute();
					if(!$res){
						echo '<script>alert("การลบข้อมูลล้มเหลว");</script>';
					}
                    else{
                       	echo '<script>alert("ลบข้อมูลสำเร็จ");</script>';
                    }
				}
			}
			
			//cancel
			if(isset($_POST['cancel'])&& isset($_POST['remarkc'])){
				if($stmt = $con->prepare('UPDATE cash SET remarkc="'.$_POST['remarkc'].'",status=2 WHERE crn="'.$_POST['cancel'].'"')){
					$res = $stmt->execute();
					if(!$res){
						echo '<script>alert("การยกเลิกข้อมูลล้มเหลว");</script>';
					}
                    else{
                       	echo '<script>alert("ยกเลิกข้อมูลสำเร็จ");</script>';
                    }
				}
			}
			
			//paging
			$pageSize = 15;
			$allPage = 0;
			if(isset($_GET['page'])){
				$nowPage = $_GET['page']-1;
			}else{
				$nowPage = 0;
			}
			
		?>	
	</head>

	<body>
            <h1><b><a href="payment.php">Payment</a></b></h1>
            <h3><a href="../index.php">&larr; Back</a></h3><br>
		
       	<?php
						echo '<div class="menu">
							<i class="material-icons" onclick="exportExcel();" title="Export">&#xE24D;</i>
							<!-- <i class="material-icons" onclick="window.print();" title="Print">&#xE8AD;</i> -->
							<i class="material-icons" onclick="searchBox();" title="Search">&#xE880;</i>
						</div>';
		?>
		<!--<div>
			<form method="get">
				<input name="searchall" placeholder="Search all" style="width:300px;height:30px;font-size:15px"> 
			<button type="submit" class="order-button">Search</button><br><br>
			</form>
		</div>-->
		<table class="detail">
            <tr>
				<th>Cash Ref. No.</th>
				<th>Customer</th>
				<th>Date Time</th>
				<th>Amount</th>
				<th>Bank</th>
				<th>Branch</th>
				<th>Remark</th>
				<th>Account</th>
				<th>Add User</th>
				<th>Add Time</th>
				<th>Status</th>
				<th>Remark Cancel</th>
             	<th>เลขที่เติมเงิน</th>
				<th>Action</th>
			</tr>

			<script>
			var topups = {};
			var topups_in = {};
			<?php
				//get bank
				$banks = array();
				if($stmt = $con->prepare('SELECT bname FROM bank')){
					$stmt->execute();
					$stmt->bind_result($bname);
					while($stmt->fetch()){
						array_push($banks,$bname);
					}
				}
                                
              	//get bank payment
                $cBanks = array();
                $_bName = array();
                if($stmt = $con->prepare('SELECT bank_id,bank_name_th,account_no,bank_name_en FROM bank_payment')){
					$stmt->execute();
					$stmt->bind_result($b_id,$cif,$acn,$bname);
					while($stmt->fetch()){
						$cBanks[$b_id] = $cif." ".$acn;
						$_bName[$b_id] = $bname;                                              
					}
				}
                                
				//get user
				$users = array();
				if($stmt = $con->prepare('SELECT uid FROM user')){
					$stmt->execute();
					$stmt->bind_result($uid);
					while($stmt->fetch()){
						array_push($users,$uid);
					}
				}

				// select bank account company distinct
				$banksUnique = array ();
				if ($stmt = $con->prepare ( 'SELECT distinct bank_name_en,bank_name_th FROM bank_payment' )) {
						$stmt->execute ();
						$stmt->bind_result ( $b_id, $acn );
						while ( $stmt->fetch () ) {
							$banksUnique [$b_id] = $acn;
						}
				}
                
                //get topup not in cash
				if ($stmt = $con->prepare('SELECT topup_id,topup_number,customer_id,topup_amount,topup_bank,topup_date,topup_status'
                                        . ' FROM customer_request_topup'
                                        . ' WHERE topup_id not in (select topup_id from cash)'
                                        . ' AND topup_status=0'
                                        . ' ORDER BY topup_number DESC' 
                                        ))
                                {
					$stmt->execute();
					$stmt->bind_result($tid,$tno,$cid,$amount,$bid,$date,$status);
					while($stmt->fetch()){
						$topups[$tid] = $tno;
						echo 'topups["'.$tno.'"] = {tid:"'.$tid.'",cid:"'.$cid.'",topup:'.$amount.',bid:'.$bid.',date:"'.$date.'",status:'.$status.'};';
					}
				}
                
                //get topup in cash
              	$topups_in = array();				
				if ($stmt = $con->prepare('SELECT topup_id,topup_number,topup_amount,customer_id'
                                        . ' FROM customer_request_topup'
                                        . ' WHERE topup_id in (select topup_id from cash)'
                                        . ' AND (topup_status=0 OR topup_status=1)'
                                        . ' ORDER BY topup_number DESC')) {
					$stmt->execute();
					$stmt->bind_result($tid,$tno,$amount,$cid);
					while($stmt->fetch()){
						$topups_in[$tid] = $tno;
						echo 'topups_in["'.$tno.'"] = {tid:"'.$tid.'",otopup:'.$amount.',cid:'.$cid.'};';
					}
				}
			
				//get customer name
                $_cus = array();
                $_cus[0] = '';
                $sql = 'SELECT customer_firstname,customer_lastname,topup_id from customer c'.
                        ' JOIN customer_request_topup t on t.customer_id=c.customer_id';
                if($stmt = $con->prepare($sql)) {
                    $stmt->execute();
                    $stmt->bind_result($cfname,$clname,$topup_id);
                    while($stmt->fetch()) {
                        $_cus[$topup_id] = $cfname.' '.$clname;
                    }
                }

                //error 0
				error_reporting(0);	
				?></script>
				
				<?php
				//print_r($topups);
                //print_r($topups_in);
             	$sql = 'SELECT c.cashid, c.crn,c.customer,c.date,c.time,c.amount,c.remark,c.branch,c.bid,c.acn,c.uid,c.ctime,c.remarkc,c.status,c.cbid,c.topup_id'.
             	' FROM cash c JOIN bank_payment bp ON c.cbid=bp.bank_id';
             	$orderBy = ' ORDER BY c.crn DESC';
             	
				if($stmt = $con->prepare($sql.$search.$orderBy)) {
					//for paging
					$stmt->execute();
					$stmt->store_result();
					$count = $stmt->num_rows;
					$allPage = ceil($count/$pageSize);
					$stmt->close();
					
					$stmt = $con->prepare($sql.$search.$orderBy.' LIMIT '.$nowPage*$pageSize.','.$pageSize);
					//echo $sql.$search.' LIMIT '.$nowPage*$pageSize.','.$pageSize;                   	
                   	$stmt->execute();
					$stmt->bind_result($cid,$crn,$customer,$date,$time,$amount,$remark,$branch,$bid,$acn,$uid,$ctime,$remarkc,$status,$cbid,$topup_id);
					$puncCount = 0;

					while($stmt->fetch()) {
                       	//binfo
                        $bif = explode(" ", $cBanks[$cbid]);
                        
                        //set status description
                        $statdesc="";
                        if ($status==0) $statdesc='Normal';
                        else if ($status==1) $statdesc='Complete';
                        else if ($status==2) $statdesc='Cancle';
                        
                        //date and time for 'Add Time' column
                        $addDate=substr($ctime,8,2).'-'.substr($ctime,5,2).'-'.substr($ctime,0,4);
                        $addTime=substr($ctime,10,9);
                                                
						echo '<tr class="'.($puncCount%2==0? 'punc ':'').($status==0? 'normal ':'').($status==2? 'cancel ':'').($status==1? 'complete ':'').'">'.
						'<td id="'.$crn.'">'.$crn.'</td>'.
						'<td id="'.$crn.'customer">'.$_cus[$topup_id].'</td>'.
						'<td id="'.$crn.'date">'.substr($date,8,2).'-'.substr($date,5,2).'-'.substr($date,0,4).' '.$time.'</td>'.
						'<input type="hidden" id="'.$cid.'date" value='.substr($date,8,2).'-'.substr($date,5,2).'-'.substr($date,0,4).'>'.
						'<input type="hidden" id="'.$crn.'time" value='.$time.'>'.
						'<input type="hidden" id="'.$crn.'amount"/ value="'.$amount.'">'.
						'<td id="'.$cid.'num">'.number_format($amount,2).'</td>'.
						//'<input id="'.$crn.'bid" type="hidden" value="'.($bid).'"/><td>'.$banks[$bid-1].'</td>'.
						'<input id="'.$crn.'bid" type="hidden" value="'.($bid).'"/>'.
						//'<td id="'.$cid.'bank" bank="'.($cbid).'">'.$banks[$bid-1].'</td>'.
						'<td id="'.$cid.'bank" bank="'.($cbid).'">'.$_bName[$cbid].'</td>'.
						'<td id="'.$crn.'branch">'.$branch.'</td>'.
						'<td id="'.$crn.'remark">'.$remark.'</td>'.
						//'<td id="'.$crn.'acn">'.$acn.'</td>'.
						'<td>'.$bif[1].
						'<input id="'.$crn.'cbid" type="hidden" value="'.$cbid.'"/>'.
						'<td id="'.$crn.'uid">'.$uid.'</td>'.
						'<td>'.$addDate.' '.$addTime.'</td>'.
						'<td id="'.$crn.'status">'.$statdesc.'</td>'.
						'<input id="'.$cid.'status1" type="hidden" value="'.$status.'"/>'.
						'<td id="'.$crn.'remarkc">'.$remarkc.'</td>';
                                                
							if($status==0){
									echo '<td><input size="15" maxlength="15" id="'.$cid.'topup" value="'.(isset($topups_in[$topup_id])? $topups_in[$topup_id]:'').'"/></td>';
									if ($_access[0]->action==1 || $_adminFlg==1) echo '<td><button onclick="save2(\''.$cid.'\')">Save</button>';
									else echo '<td></td>';
									echo '<input type="hidden" id="'.$cid.'tu" value="'.$topup_id.'"/>';
									echo '<input type="hidden" id="'.$cid.'tnum" value="'.(isset($topups_in[$topup_id])? $topups_in[$topup_id]:'').'"/>';
							}
							else if($status==1){
									echo '<td><input size="15" maxlength="15" id="'.$cid.'topup" value="'.(isset($topups_in[$topup_id])? $topups_in[$topup_id]:'').'" disabled /></td>';
									if ($_access[0]->action==1 || $_adminFlg==1) echo '<td><button onclick="cancel(\''.$cid.'\')">Cancel</button>';
									else echo '<td></td>';
									echo '<input type="hidden" id="'.$cid.'tu" value="'.$topup_id.'"/>';
									echo '<input type="hidden" id="'.$cid.'tnum" value="'.(isset($topups_in[$topup_id])? $topups_in[$topup_id]:'').'"/>';
							}
                            else if ($status==2) {
									echo '<td><input size="15" maxlength="15" disabled/></td>';
                                    if ($_access[0]->action==1 || $_adminFlg==1) echo '<td><button onclick="save2(\''.$cid.'\')">Save</button>';
                                    else echo '<td></td>';
                                    echo '<input type="hidden" id="'.$cid.'tu" value="'.$topup_id.'"/>';
                                    echo '<input type="hidden" id="'.$cid.'tnum" value="'.(isset($topups_in[$topup_id])? $topups_in[$topup_id]:'').'"/>';
                          	}
						echo '</td></tr>';
						$puncCount++;
					}
					$stmt->close();
				}
				
				//test
                $sum = array(0,0,0);
                $count = array(0,0,0);
                $totalSum = 0;
                $totalCount = 0;
                $groupByStat = ' GROUP BY c.STATUS';
                $sql = 'SELECT c.status,sum(c.amount),count(c.cashid)'.
             		' FROM cash c JOIN bank_payment bp ON c.cbid=bp.bank_id';
                //echo 'SELECT status,sum(amount),count(cashid) FROM cash'.$search.$groupByStat;                 
                if($stmt = $con->prepare($sql.$search.$groupByStat)){
                    $stmt->execute();
                    $stmt->bind_result($stat,$amount,$countId);
                    while($stmt->fetch()){
                            $sum[$stat] = $amount;
                            $count[$stat] = $countId;

                            $totalSum += $amount;
                            $totalCount += $countId;
                    }
                }
			?>
		</table><br>
             
		<div id="editBox" class="wrap">
			<form method="post">
				<table>
					<tr><th><h2 id="title">Edit</h2></th><td></td></tr>
					<input type="hidden" id="e-crn" name="crn" value=""/>
                                        <tr><th>ข้อมูลการโอน</th><td></td></tr>
					<tr><th>Customer :</th><td><input id="e-customer" name="customer"/></td></tr>
                                        <!--<tr><th>Account No. :</th><td><input id="e-acn" name="acn" required="required" maxlength="10"/></td></tr>-->
                                        <tr><th>Bank Name :</th><td><select name="bid">
						<?php 
							for($i=0;$i<sizeof($banks);$i++){
								echo '<option id="e-bid-'.$i.'" value="'.($i+1).'">'.$banks[$i].'</option>';
							}
						?>
					</select></td></tr>
                                        <tr><th>Branch :</th><td><input id="e-branch" name="branch"/></td></tr>
                                        <tr><th>Amount :</th><td><input id="e-amount" name="amount" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');"/></td></tr>
					<tr><th>Date :</th><td><input class="datepicker" id="e-date" name="date"/></td></tr>
                                        <tr><th>Time :</th><td><input class="timepicker" id="e-time" name="time" step="1"/></td></tr>
                                        <tr><th>เข้าบัญชีบริษัท</th><td></td></tr>
					<tr><th>Account :</th><td><select id="e-cbid" name="cbid">
						<?php
							reset($cBanks);
							for($i=0;$i<sizeof($cBanks);$i++){
								echo '<option value="'.key($cBanks).'">'.current($cBanks).'</option>';
                                                                next($cBanks);
							}
						?>
					</select></td></tr>
					<tr><th>Remark :</th><td><input id="e-remark" name="remark"/></td></tr>			
					<!--<tr><th>Add User :</th><td><select name="uid">
						<?php 
							for($i=0;$i<sizeof($users);$i++){
								echo '<option id="e-uid-'.$users[$i].'">'.$users[$i].'</option>';
							}
						?>
					</select></td></tr>
					<tr><th>Remark Cancel :</th><td><input id="e-remarkc" name="remarkc"/></td></tr>
					<tr><th>Status :</th>
                                            <td><select name="status">
                                                <option id="e-status-0" value="0">Normal</option>
                                                <option id="e-status-1" value="1">Complete</option>
                                                <option id="e-status-2" value="2">Cancel</option>
                                        </select></td></tr>-->
					<input type="hidden" name="edit" value="1"/>
					<tr class="confirm"><td></td><td><a onclick="edit();">Cancel</a>&emsp;<button>Update</button></td></tr>
				</table>
			</form>
		</div>
		<div id="searchBox" class="wrap">
			<form method="get">
				<table>
					<tr><th><h2 id="title">Search</h2></th><td></td></tr>
					<tr><th>Search All :</th><td><input name="searchall"/></td></tr>
					<tr><th>Customer :</th><td><input name="customer"/></td></tr>
					<tr><th>Cash Ref. No. :</th><td><input name="crn"/></td></tr>
					<tr><th>From :</th><td><input class="datepicker" name="from"/></td></tr>
					<tr><th>To :</th><td><input class="datepicker" name="to"/></td></tr>
					<!--<tr><th>Account No. :</th><td><input name="acn"/></td></tr>-->
					<tr><th>Amount :</th><td><input name="amount" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');"/></td></tr>
					<tr>
					<th>Bank :</th>
					<td><select name="cbid2">
						<?php
						// $banksUnique;						
							echo '<option value="">-</option>';
							reset ( $banksUnique );
							for($i = 0; $i < sizeof ( $banksUnique ); $i ++) {
								echo '<option value="' . key ( $banksUnique ) . '">' . current ( $banksUnique ) . '</option>';
								next ( $banksUnique );
							}
						?>
					</select></td>
					</tr>
					<tr><th>Account :</th><td><select name="cbid">
						<?php
                            echo '<option value="">-</option>';
							reset($cBanks);
							for($i=0;$i<sizeof($cBanks);$i++){
								echo '<option value="'.key($cBanks).'">'.current($cBanks).'</option>';
                                next($cBanks);
							}
						?>
					</select></td></tr>
					<tr><th>Status :</th><td><select name="status">
												<option value="-">-</option>
                                                <option value="0" selected>Normal</option>
                                                <option value="1">Complete</option>
                                                <option value="2">Cancel</option>
					</select></td></tr>
                                       <!-- <tr><th>Add User :</th><td><input name="uid"/></td></tr>-->
					<tr class="confirm"><td></td><td><a onclick="searchBox();">Cancel</a>&emsp;<button>Search</button></td></tr>
				</table>
			</form>
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
                            <td class="normal"><b>Normal :</b></td>
                            <td class="normal"><?php echo number_format($count[0]); ?>&nbsp;</td>
                            <td class="complete"><b>Complete :</b></td>
                            <td class="complete"><?php echo number_format($count[1]); ?>&nbsp;</td>
                            <td class="cancel"><b>Cancel :</b></td>
                            <td class="cancel"><?php echo number_format($count[2]); ?>&nbsp;</td>
                            <td><b>Total :</b></td>
                            <td><?php echo number_format($totalCount); ?>&nbsp;</td>
                            <td>Records<br></td>
                        </tr>
                        <tr>
                            <td><b>จำนวนยอดทั้งหมด</b></td>
                            <td class="normal"><b>Normal :</b></td>
                            <td class="normal"><?php echo number_format($sum[0],2); ?>&nbsp;</td>
                            <td class="complete"><b>Complete :</b></td>
                            <td class="complete"><?php echo number_format($sum[1],2); ?>&nbsp;</td>
                            <td class="cancel"><b>Cancel :</b></td>
                            <td class="cancel"><?php echo number_format($sum[2],2); ?>&nbsp;</td>
                            <td><b>Total :</b></td>
                            <td><?php echo number_format($totalSum,2); ?>&nbsp;</td>
                            <td style="text-align: left;">THB</td>
                        </tr>
                   	</table>
		</div>

		<?php
				include './dialog/loading.php';
		?>
	</body>
</html>
<?php
	$con->close();
?>
