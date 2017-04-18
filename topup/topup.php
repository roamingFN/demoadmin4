<!DOCTYPE html>
<html>
	<head>
		<title>Top up</title>
		<meta charset="utf-8">
		<!--Jquery Datepicker Timepicker-->
       	<link rel="stylesheet" href="../css/jquery-ui.css" media="all"> 
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
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
                                $( ".datepicker" ).datepicker({
										dateFormat: "dd-mm-yy"
								});        
			});
		</script>
                
		<link rel="stylesheet" type="text/css" href="../css/cargo.css"  media="all">
		<link rel="stylesheet" type="text/css" href="../css/w3-red.css"  media="all">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" media="all">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css' media="all">

		<?php
			session_start();
			if (!isset($_SESSION['ID'])){
            		header("Location: ../login.php");
       		}
       		date_default_timezone_set("Asia/Bangkok");
			include '../database.php';
			include './utility/function.php';
			include '../utility/permission.php';

			const FORMID = 2;
			$_access = json_decode(getAccessForm($con,FORMID,$_SESSION['USERID']));
			$_adminFlg = getAdminFlag($con,$_SESSION['ID']);
			if ($_adminFlg==0) {
					if (empty($_access) || $_access[0]->visible==0) header ("Location: ../login.php");
			}

        	function sendEmail($con,$tno,$cmail,$cname,$total,$tid,$date,$acnum,$ccode,$cid) {
        		include '../configPath.php';
				$strTo = $cmail;
				$strSubject = '=?UTF-8?B?'.base64_encode('รายการเติมเงินเลขที่ '.$tno.' ของท่านได้ถูกยกเลิก').'?=';
				$strHeader = "MIME-Version: 1.0\' . \r\n";
				$strHeader .= "Content-type: text/html; charset=utf-8\r\n";
				$strHeader .= "From: Order2Easy <order2easy_admin@order2easy.com>";
				$strMessage = "สวัสดีค่ะ คุณ ".$cname." (".$ccode.")<br><br>".				
				"<table width='800px'>".
					"<tr>
						<td width='140px'></td>
						<td colspan='4' align='left'>รายการเติมเงินเลขที่ <a href='http://www.order2easy.com/".$_path_frontend."/topup'>".$tno."</a> ยอดเงิน ".number_format((float)$total,2)." วันที่โอน ".$date." โดยโอนเข้าบัญชี ".$acnum."</td>
					</tr>".
					"<tr>
						<td colspan='5'>ข้อมูลที่ท่านแจ้งไม่ถูกต้อง ทางเราขอยกเลิกรายการนี้ (ถ้ารายการนี้มีการชำระเงินจะถูกยกเลิกโดยอัตโนมัติ)</td>
					</tr>".
					"<tr>
						<td colspan='5' height='20px'></td>
					</tr>".
					"<tr>
						<td colspan='5' style='color:red;'>***กรุณาระบุยอดเงิน วันที่โอน และธนาคารที่ท่านโอนเข้าให้ถูกต้อง</td>
					</tr>".
					"<tr>
						<td colspan='5' height='20px'></td>
					</tr>".
					"<tr>
						<td colspan='5'>และเพื่อความรวดเร็ว หลังจากที่ท่านได้ทำการโอนเงินค่าสินค้าแล้ว กรุณาดำเนินการตามขั้นตอนต่อไปนี้</td>
					</tr>".
					"<tr>
						<td width='140px'></td>
						<td colspan='4'>1. แจ้งการเติมเงิน โดยกด link ตามนี้ <a href='http://www.order2easy.com/".$_path_frontend."/topup'><button>เติมเงิน</a></td>
					</tr>".
					"<tr>
						<td width='140px'></td>
						<td colspan='4'>2. กรอกข้อมูลรายละเอียดต่างๆตามทีปรากฏบนหน้าเว็ปไซด์ จากนั้นกดปุ่มตกลง</td>
					</tr>".
					"<tr>
						<td width='140px'></td>
						<td colspan='4'>3. ไปยังหน้ารายการสั่งซื้อ เพื่อกดปุ่มชำระเงิน</td>
					</tr>".
					"<tr>
						<td colspan='5' height='20px'></td>
					</tr>".
					"<tr>
						<td colspan='5'>(หากลูกค้าไม่ชำระภายในเวลาที่กำหนด รายการสั่งซื้อนี้จะถูกยกเลิกโดยอัตโนมัติค่ะ)</td>
					</tr>".
					"<tr>
						<td>สอบถามโทร</td>
						<td colspan='4'>02-924-5023</td>
					</tr>".
					"<tr>
						<td></td>
						<td colspan='4'>02-924-5850</td>
					</tr>".
					"<tr>
						<td></td>
						<td colspan='4'>089-052-8899</td>
					</tr>".
					"<tr>
						<td>Email</td>
						<td colspan='4'>order2easy_admin@order2easy.com</td>
					</tr>".
					"<tr>
						<td>Line</td>
						<td colspan='4'>order2easy</td>
					</tr>".
					"<tr>
						<td colspan='5' height='20px'></td>
					</tr>".
				"</table>".
				"<br>order2easy".
				"<br>เจ้าหน้าที่ผู้ตรวจสอบรายการ: ".$_SESSION['ID'].
				"<br>".date('d-m-Y H:i:s');
				
				@mail($strTo,$strSubject,$strMessage,$strHeader);
				// $head = 'รายการเติมเงินเลขที่ '.$tno.' ของท่านได้ถูกยกเลิก';
				// $stmt = $con->prepare('INSERT INTO topup_email_log (topup_id,subject,content) VALUES (?,?,?)');
				// $stmt->bind_param('sss',$tid,$head,$strMessage);
				// $res = $stmt->execute();

				$subject = 'รายการเติมเงินหมายเลข '.$tno.' ของท่านได้ถูกยกเลิก';
				if ($stmt = $con->prepare('INSERT INTO total_message_log (topup_id,customer_id,user_id,subject,content,message_date) VALUES (?,?,?,?,?,now())')) {
					$stmt->bind_param('iiiss',$tid,$cid,$_SESSION['ID'],$subject,$strMessage);
					$res = $stmt->execute();
				}
				else {
					echo $con->error;
				}
			}

            function genTUN(){
                    include '../database.php';
                    $i = 0;
                    $newTT = '00001';
                    //get last rec                                
                    $stmt = $con->prepare('SELECT topup_number FROM customer_request_topup ORDER BY topup_number DESC LIMIT 1');
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_array(MYSQLI_NUM)) {
                            foreach ($row as $r) {
                                $i += 1;
                            }
                    }
                    //find year
                    $year = substr((string)date("Y"),2,2);
                    $month = (string)date("m");
                    $day = (string)date("d");
                    //cash has records
                    if ($i) {
                            $tempTT = (string) ((int) substr($r, -5) + 1);
                            $len = strlen($tempTT);
                            $dayBase = substr($r, 5, 2);
                            if ($day!=$dayBase) {
                            }
                            else if ($len == 4) {
                                $newTT = '0' . $tempTT;
                            } else if ($len == 3) {
                                $newTT = '00' . $tempTT;
                            } else if ($len == 2) {
                                $newTT = '000' . $tempTT;
                            } else if ($len == 1) {
                                $newTT = '0000' . $tempTT;
                            } else {
                                $newTT = $tempTT;
                            }
                    }
                return 'A'.$year.$month.$day.$newTT;
            } 
                        
			//search
       		$_SESSION['sql'] = '';
			$cases = array();
			$request = '';
			ini_set('display_errors', 0);
			if(!empty($_GET['cid'])) {
				array_push($cases,' c.customer_id='.$_GET['cid']);
				$request .= '&cid='.$_GET['cid'];
			}
			//if(!empty($_GET['datetime'])){array_push($cases,' topup_date="'.$_GET['datetime'].'"');$request .= 'topup_date='.$_GET['datetime'];}
			if(!empty($_GET['from'])){           
                array_push($cases,' topup_date>="'.substr($_GET['from'],6,4).'-'.substr($_GET['from'],3,2).'-'.substr($_GET['from'],0,2).' 00:00:00"');
                //array_push($cases,' topup_date>=CONCAT(str_to_date(\''.$_GET['from'].'\', \'%d-%m-%Y\'), \' 00:00:00\')');
                $request .= '&from='.$_GET['from'];
            }
			if(!empty($_GET['to'])) {
                array_push($cases,' topup_date<="'.substr($_GET['to'],6,4).'-'.substr($_GET['to'],3,2).'-'.substr($_GET['to'],0,2).' 23:59:59"');
                $request .= '&to='.$_GET['to'];
            }
            if (isset($_GET['status'])){
				if($_GET['status']=='-'){
					array_push($cases,' topup_status>=0');
					$request .= '&status=-';
				}else{
					array_push($cases,' topup_status='.$_GET['status']);
					$request .= '&status='.$_GET['status'];
				}
            }
			if(!empty($_GET['amount'])) {
				array_push($cases,' topup_amount="'.$_GET['amount'].'"');
				$request .= '&amount='.$_GET['amount'];
			}
			if(!empty($_GET['method'])) {
				array_push($cases,' transfer_method LIKE =%"'.$_GET['method'].'%"');
				$request .= '&method='.$_GET['method'];
			}
			//if(!empty($_GET['note'])){array_push($cases,' acn LIKE "%'.$_GET['acn'].'%"');$request .= 'acn='.$_GET['acn'];}
			if(!empty($_GET['bid'])) {
				array_push($cases,' topup_bank='.$_GET['bid']);
				$request .= '&bid='.$_GET['bid'];
			}
			if(!empty($_GET['tno'])) {
				array_push($cases,' topup_number=\''.$_GET['tno'].'\'');
				$request .= '&tno='.$_GET['tno'];
			}
			//if(!empty($_GET['status']||$_GET['status']=='0')){array_push($cases,' status='.$_GET['status']);$request .= 'status='.$_GET['status'];}
			if(!empty($_GET['searchall'])) {
				array_push($cases,' ct.topup_amount=\''.$_GET['searchall'].'\''.
										' or ct.transfer_method LIKE \'%'.$_GET['searchall'].'%\''.
										' or ct.remarkc LIKE \'%'.$_GET['searchall'].'%\''.
										' or ct.customer_notes LIKE \'%'.$_GET['searchall'].'%\''.
										' or ct.topup_number LIKE \'%'.$_GET['searchall'].'%\''.
										' or bp.account_name LIKE \'%'.$_GET['searchall'].'%\''.
										' or bp.account_no LIKE \'%'.$_GET['searchall'].'%\''.
										' or bp.bank_name_en LIKE \'%'.$_GET['searchall'].'%\''.
										' or (c.customer_firstname LIKE \'%'.$_GET['searchall'].'%\' or c.customer_lastname LIKE \'%'.$_GET['searchall'].'%\')');
				$request .= '&searchall='.$_GET['searchall'];
			}
			ini_set('display_errors', 1);
			
			$search = ' WHERE (ct.topup_status=0)';
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
         	
         	$_SESSION['sql'] = 'SELECT ct.topup_id,ct.topup_number,ct.customer_id,ct.topup_bank,ct.topup_amount,ct.usable_amout,ct.topup_status,ct.topup_date,ct.transfer_method,ct.bill_file_directory,ct.customer_notes,ct.comment,ct.remarkc,ct.cashid,ct.used,ct.emailno,ct.emaildt'.
            	' FROM customer_request_topup ct JOIN bank_payment bp ON ct.topup_bank=bp.bank_id'.
            	' JOIN customer c ON c.customer_id=ct.customer_id'.$search;	
          	//echo $_SESSION['sql'];
       		//echo $request;
       		//add
            if(isset($_POST['add'])) {
                   	$resultTU = "";
                  	$resultTU = genTUN();
                                
               		//formated datetime 'yyyy-mm-dd h:m:s'
                  	$addDateTime=substr($_POST['datetime'],6,4).'-'.substr($_POST['datetime'],3,2).'-'.substr($_POST['datetime'],0,2). ' '.substr($_POST['datetime'],10,9);
					//echo $addDateTime;
					$stmt = $con->prepare('INSERT INTO customer_request_topup(topup_number,customer_id,topup_bank,topup_amount,topup_status,topup_date,transfer_method,customer_notes)'
                    		. ' VALUES(?,?,?,?,?,?,?,?)');
					$stmt->bind_param('ssssssss',$resultTU,$_POST['cid'],$_POST['bid'],$_POST['amount'],$_POST['status'],$addDateTime,$_POST['method'],$_POST['note']);
					$res = $stmt->execute();
					if(!$res){
						echo '<script>alert("การเพิ่มข้อมูลล้มเหลว");window.location.href="topup.php";</script>';
					}
                    else {
                       	echo '<script>alert("เพิ่มข้อมูลสำเร็จ");window.location.href="topup.php";</script>';
                    }
          	}       
                        
			//edit
			if(isset($_POST['edit'])){
             	$addDateTime=substr($_POST['datetime'],6,4).'-'.substr($_POST['datetime'],3,2).'-'.substr($_POST['datetime'],0,2). ' '.substr($_POST['datetime'],10,9);
				$stmt = $con->prepare('UPDATE customer_request_topup SET customer_id=?,topup_bank=?,topup_amount=?,topup_date=?,transfer_method=?,customer_notes=? WHERE topup_id=?');
				$stmt->bind_param('ssssssi',$_POST['cid'],$_POST['bid'],$_POST['amount'],$addDateTime,$_POST['method'],$_POST['note'],$_POST['tid']);
				$res = $stmt->execute();
				if(!$res){
					echo '<script>alert("การแก้ไขข้อมูลล้มเหลว");window.location.href="topup.php";</script>';
				}
            	else{
                  	echo '<script>alert("แก้ไขข้อมูลสำเร็จ");window.location.href="topup.php";</script>';
               	}
			}

			//cancel
			if (isset($_POST['cancel'])) {
				$used = getTopupUsed($con,$_POST['c-tid']);
				$icount = 0;
				//used
				if ($used==1) {
					
						$arrOid = getArrOid($con,$_POST['c-tid']);  
						foreach ($arrOid as $key => $oid) {
							
								if (findPayment($con,$oid)) {
										updatePaymentStat($con,$oid);
										updateOrderStat($con,$oid);
										if($icount == 0){
											updateStatement($con,$oid,$_POST['c-tid'],$_POST['tmp-amount'],$_POST['tmp-tno'],$_POST['c-cid']);
											$icount = 1;
										}
								}
								$arrTid = getArrTid($con,$oid);
								foreach ($arrTid as $tid => $amount) {
								 		if ($tid==$_POST['c-tid']) continue;
								 		$topupStat = getTopupStat($con,$tid);
								 		if ($topupStat==1||$topupStat==0) {
								 				$stmt = $con->prepare('UPDATE customer_request_topup SET usable_amout=usable_amout+'.$amount.' WHERE topup_id='.$tid);
								 				$res = $stmt->execute();
								 		}
								 		else {
								 				$stmt = $con->prepare('UPDATE customer_request_topup SET usable_amout=0 WHERE topup_id='.$tid);
								 				$res = $stmt->execute();
								 		}
								}

								//12/03/2017	case 3 
								deleteOrderStatement($con,$oid,$_POST['c-tid']);
								updateOrderStat($con,$oid);
						}
				}
				//not used
				else {
						updateStatement($con,'',$_POST['c-tid'],$_POST['tmp-amount'],$_POST['tmp-tno'],$_POST['c-cid']);
						if($stmt = $con->prepare('UPDATE customer SET wait_amount=wait_amount-'.$_POST['tmp-amount'].' WHERE customer_id='.$_POST['c-cid'])) {
						$res = $stmt->execute();
						if(!$res) {
								echo '<script>alert("การยกเลิกข้อมูลล้มเหลว '.$stmt->error.'");';
								echo 'window.location.href="topup.php";</script>';
						}
						}
				}

				//update topup status
				if($stmt = $con->prepare('UPDATE customer_request_topup SET remarkc="'.$_POST['cc-rmkcc'].'",topup_status=2,emailno=1,emaildt=now(),cancel_by="'.$_SESSION['ID'].'",cancel_date=now() WHERE topup_id="'.$_POST['c-tid'].'"')) {
					$res = $stmt->execute();
					if(!$res) {
							echo '<script>alert("การยกเลิกข้อมูลล้มเหลว '.$stmt->error.'");';
							echo 'window.location.href="topup.php";</script>';
					}
					else {
							sendEmail($con,$_POST['tmp-tno'],$_POST['tmp-cmail'],$_POST['tmp-cname'],$_POST['tmp-amount'],$_POST['c-tid'],$_POST['tmp-date'],$_POST['tmp-acnum'],$_POST['tmp-ccode'],$_POST['c-cid']);
							//send email
                    		echo '<script>alert("การยกเลิกข้อมูลสำเร็จ");window.location.href="topup.php";</script>';	
					}
				}
			}
                        
            //delete
			if(isset($_POST['del'])){
				if($stmt = $con->prepare('DELETE FROM customer_request_topup WHERE topup_id="'.$_POST['del'].'"')){
					$res = $stmt->execute();
					if(!$res){
						echo '<script>alert("การลบข้อมูลล้มเหลว");window.location.href="topup.php";</script>';
					}
                   	else{
                       	echo '<script>alert("ลบข้อมูลสำเร็จ");window.location.href="topup.php";</script>';
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
			
			$count = 0;
			$normal = 0;
			$cancel = 0;
          	$countNormal = 0;
            $countCancel = 0;
		?>
	</head>
	<body>
            <h1><b><a href="topup.php">Top up</a></b></h1>
            <h3><a href="../index.php">&larr; Back</a></h3><br>
      	
       	<?php
			   	echo '<div class="menu">';
			   	if ($_access[0]->canadd==1 || $_adminFlg==1) echo '<i class="material-icons" onclick="add();" title="Add">&#xE147;</i>';
				echo '<i class="material-icons" onclick="exportExcel();" title="Export">&#xE24D;</i>
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
				<th>Customer</th>
				<th>Date</th>
				<th>Time</th>
				<th>Amount</th>
				<th>Transfer By</th>
				<th>Remark</th>
				<th>Bank Name</th>
				<th>Account Name</th>
				<th>Account No.</th>
            	<th>เลขที่เติมเงิน</th>
                <th>Status</th>
				<th>Action</th>
				<th>Remark cancel</th>
				<th>Sent email</th>
				<th>วันที่ส่ง email</th>
			</tr>
			<?php
				//get customer name
             	$customers = array();
             	$_cmail = array();
             	$_ccode = array();
				if($stmt = $con->prepare('SELECT customer_id,customer_firstname,customer_lastname,customer_email,customer_code FROM customer')){
					$stmt->execute();
					$stmt->bind_result($c_id,$cfn,$cln,$cmail,$ccode);
					while($stmt->fetch()){
						$customers[$c_id] = $cfn." ".$cln;
						$_cmail[$c_id] = $cmail;
						$_ccode[$c_id] = $ccode;
					}
				}

                   	//get bank payment
                    $cBanks = array();
                    $_act = array();
                    if($stmt = $con->prepare('SELECT bank_id,bank_name_en,account_no,account_name FROM bank_payment')){
					$stmt->execute();
					$stmt->bind_result($b_id,$cif,$acn,$act);
					while($stmt->fetch()){
						$cBanks[$b_id] = $cif." ".$acn;
						$_act[$b_id] = $act;                                               
					}
				}
            	
            	$sql = 'SELECT ct.topup_id,ct.topup_number,ct.customer_id,ct.topup_bank,ct.topup_amount,ct.usable_amout,ct.topup_status,ct.topup_date,ct.transfer_method,ct.bill_file_directory,ct.customer_notes,ct.comment,ct.remarkc,ct.cashid,ct.used,ct.emailno,ct.emaildt'.
            	' FROM customer_request_topup ct JOIN bank_payment bp ON ct.topup_bank=bp.bank_id'.
            	' JOIN customer c ON c.customer_id=ct.customer_id';
              	$orderBy = ' ORDER BY ct.topup_number DESC';
				if($stmt = $con->prepare($sql.$search.$orderBy)){
					$stmt->execute();
					$stmt->store_result();
					$count = $stmt->num_rows;
					$allPage = ceil($count/$pageSize);
					$stmt->close();
					
					$stmt = $con->prepare($sql.$search.$orderBy.' LIMIT '.$nowPage*$pageSize.','.$pageSize);
                   	$stmt->execute();
					$stmt->bind_result($tid,$tno,$cid,$bid,$amount,$u_amount,$status,$datetime,$tran_method,$bill,$note,$comment,$remarkc,$cashId,$used,$emailno,$emaildt);
					$puncCount = 0;
					while($stmt->fetch()){
                        //binfo
                        $bif = explode(" ", $cBanks[$bid]);
                        $date = substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
						$time = substr($datetime,10,9);

						$edt = substr($emaildt,8,2).'-'.substr($emaildt,5,2).'-'.substr($emaildt,0,4);
						//stat desc
						$statDesc = '';
						if ($status==0) {
								$statDesc = 'Waiting';
						}
						else if ($status==1) {
								$statDesc = 'OK';
						}
						else if ($status==2) {
								$statDesc = 'Cancel';
						}

                        echo '<tr class="'.($puncCount%2==0? 'punc ':'').($status==0? 'normal ':'').($status==2? 'cancel ':'').($status==1? 'complete ':'').'">'.
						'<td id="'.$tid.'cname">'.$customers[$cid].'</td>'.
						'<td id="'.$tid.'date">'.$date.'</td>'.
						'<td id="'.$tid.'time">'.$time.'</td>'.
						'<td id="num">'.number_format($amount,2).'</td>'.
						'<td id="'.$tid.'branch">'.$tran_method.'</td>'.
						'<td id="'.$tid.'remark">'.$note.'</td>'.
						'<td id="'.$tid.'bname">'.$bif[0].'</td>'.
						'<td id="'.$tid.'act">'.$_act[$bid].'</td>'.
						'<td id="'.$tid.'acn">'.$bif[1].'</td>'.
						'<td id="'.$tid.'tno"><a style="cursor: pointer;" onclick="showPic(\''.$bill.'\')">'.$tno.'</a></td>';

                 		echo '<td>'.$statDesc.'</td>';

                 		//waiting status
                        if ($status==0) {
                        	if ($_access[0]->action==1 || $_adminFlg==1) {
                        		echo '<td><button onclick="cancelBox('.$tid.')">Cancel</button></td>';
                        	}
                        	else {
                        		echo '<td></td>';
                        	}
                        	echo '<td id="'.$tid.'remarkc">'.$remarkc.'</td>';
                        	echo '<td align="center">-</td><td></td>';
						}
						//complete status
						else if ($status==1 && ($_access[0]->action==1 || $_adminFlg==1)) {
							echo '<td></td>';
							echo '<td id="'.$tid.'remarkc">'.$remarkc.'</td>';
	                       	echo '<td align="center">-</td><td></td>';
						}
						//cancel status
						else if ($status==2) {
							if ($_access[0]->action==1 || $_adminFlg==1) {
								echo '<td style="text-align: center;"><button onclick="reverseStatus(\''.$tid.'\')">ย้อนสถานะ</button>  ';
								echo '<button onclick="sendEmail(\''.$tid.'\')">Email</button></td>';
							}
							else {
								echo '<td></td>';
							}
							echo '<td id="'.$tid.'remarkc">'.$remarkc.'</td>';
							echo '<td align="center">'.$emailno.'</td>';
							echo '<td align="center">'.$edt.'</td>';
						}
						
						echo '</tr>';
						echo '<input type="hidden" id="'.$tid.'"/ value="'.$tid.'">'.
                        '<input type="hidden" id="'.$tid.'customer"/ value="'.$cid.'">'.
                        '<input type="hidden" id="'.$tid.'datetime"/ value="'.$date.' '.$time.'">'.
                        '<input type="hidden" id="'.$tid.'amount"/ value="'.$amount.'">'.
                        '<input type="hidden" id="'.$tid.'bid"/ value="'.$bid.'">'.
                        '<input type="hidden" id="'.$tid.'cmail" value="'.$_cmail[$cid].'">'.
                        '<input type="hidden" id="'.$tid.'ccode" value="'.$_ccode[$cid].'">';
						$puncCount++;
					}
					$stmt->close();
				}
				
                $sum = array(0,0,0);
                $count = array(0,0,0);
                $totalSum = 0;
                $totalCount = 0;
                $groupByStat = ' GROUP BY topup_status';
                //echo 'SELECT status,sum(amount),count(cashid) FROM cash'.$search.$groupByStat;
                $sql = 'SELECT topup_status,sum(topup_amount),count(topup_id)'.
            	' FROM customer_request_topup ct JOIN bank_payment bp ON ct.topup_bank=bp.bank_id'.
            	' JOIN customer c ON c.customer_id=ct.customer_id';
            	//echo $sql.$search.$groupByStat;
                if($stmt = $con->prepare($sql.$search.$groupByStat)) {
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
       	
       	<div id="addBox" class="wrap">
			<form method="post">
               	<table>
					<tr><th><h2 id="title">Add</h2></th><td></td></tr>
					<tr><th>Customer :</th><td>
                    <select name="cid">
						<?php 
                            reset($customers);
							for($i=0;$i<sizeof($customers);$i++){
								echo '<option value="'.key($customers).'">'.current($customers).'</option>';
                                next($customers);
							}
						?>
					</select></td></tr>
                 	<tr><th>Date :</th><td><input class="datetimepicker" name="datetime" required="required"/></td></tr>
                    <tr><th>Amount :</th><td><input name="amount" required="required" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');"/></td></tr>
                    <tr><th>Branch :</th><td><input name="method" required="required"/></td></tr>                                         
					<tr><th>Remark :</th><td><input name="note"/></td></tr>
					<tr><th>Bank account :</th><td>
                    <select name="bid">
						<?php 
                            reset($cBanks);
							for($i=0;$i<sizeof($cBanks);$i++){
								echo '<option value="'.key($cBanks).'">'.current($cBanks).'</option>';
                              	next($cBanks);
							}
						?>
					</select></td></tr>
                	<input type="hidden" name="status" value="0"/>
					<input type="hidden" name="add" value="1"/>
					<tr class="confirm"><td></td><td><a onclick="add();">Cancel</a>&emsp;<button>Insert</button></td></tr>
				</table>
			</form>
		</div>

		<div id="editBox" class="wrap">
			<form method="post">
				<table>
					<tr><th><h2 id="title">Edit</h2></th><td></td></tr>
					<input type="hidden" id="e-tid" name="tid" value=""/>
                 	<tr><th>Customer :</th><td>
                 	<select name="cid">
						<?php 
                          	reset($customers);
							for($i=0;$i<sizeof($customers);$i++){
								echo '<option id="e-cid-'.key($customers).'" value="'.key($customers).'">'.current($customers).'</option>';
                                next($customers);
							}
						?>
					</select></td></tr>
                  	<tr><th>Date :</th><td><input id="e-datetime" class="datetimepicker" name="datetime" required="required"/></td></tr>
                    <tr><th>Amount :</th><td><input id ="e-amount" name="amount" required="required" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');"/></td></tr>
                    <tr><th>Branch :</th><td><input id="e-method" name="method" required="required"/></td></tr>                                         
					<tr><th>Remark :</th><td><input id="e-note" name="note"/></td></tr>
					<tr><th>Bank account :</th><td>
                    <select name="bid">
						<?php 
                           	reset($cBanks);
							for($i=0;$i<sizeof($cBanks);$i++){
								echo '<option id="e-bid-'.  key($cBanks).'" value="'.key($cBanks).'">'.current($cBanks).'</option>';
                                	next($cBanks);
							}
						?>
					</select></td></tr>
					<input type="hidden" name="edit" value="1"/>
					<tr class="confirm"><td></td><td><a onclick="edit();">Cancel</a>&emsp;<button>Update</button></td></tr>
				</table>
			</form>
		</div>
      	
      	<div id="searchBox" class="wrap">
			<form method="get">
          		<table>
               		<tr><th><h2 id="title">Search</h2></th><td></td></tr>
               		<tr><th>Seach All :</th><td><input name="searchall"/></td></tr>
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
                 	<tr><th>From :</th><td><input class="datepicker" name="from"/></td></tr>
					<tr><th>To :</th><td><input class="datepicker" name="to"/></td></tr>
                    <tr><th>Amount :</th><td><input name="amount" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');"/></td></tr>
                    <tr><th>Branch :</th><td><input name="method"/></td></tr>
                    <tr><th>Topup Number :</th><td><input name="tno"/></td></tr>
                    <tr><th>Status :</th><td><select name="status">
                    		<option value="-">-</option>
							<option value="0" selected>Waiting</option>
                            <option value="1">OK</option>
                            <option value="2">Cancel</option>
					</select></td></tr>                                         
					<tr><th>Remark :</th><td><input name="note"/></td></tr>
					<tr><th>Bank account :</th><td>
                    <select name="bid">
						<?php
                       		echo '<option value="">-</option>';
                            reset($cBanks);
							for($i=0;$i<sizeof($cBanks);$i++){
								echo '<option value="'.key($cBanks).'">'.current($cBanks).'</option>';
                                next($cBanks);
							}
						?>
					</select></td></tr>
					<tr class="confirm"><td></td><td><a onclick="searchBox();">Cancel</a>&emsp;<button>Search</button></td></tr>
                </table>
			</form>
		</div>

      	<!-- Img box-->
      	<div id="imgBox" class="wrap">
            <table>
            	<tr><td width="460px" height="460px" align="center"><img id="picSlip" alt="Slip"></td></tr>
              	<tr class="confirm"><td><tr class="confirm"><td></td><td><a onclick="showPic();">Cancel</a></td></tr></td></tr>
          	</table>
      	</div>

      	<!-- cancel box -->
      	<!--<div id="cancelBox" class="wrap">
      		<form method="post">
      			<table style="width:650px;height:200px;">
      				<tr><th><h2 id="title">Cancel</h2></th><td></td></tr>
      				<tr><th>เหตุผลที่ต้องการยกเลิก :</th><td><input type="text" name="remarkc" required/></td></tr>
      				<tr class="confirm"><td></td><td><a onclick="cancelBox();">Cancel</a>&emsp;<button>Ok</button></td></tr>
      				<input type="hidden" id="c-tid" name="c-tid" value="" />
      				<input type="hidden" name="cancel" value="1"/>
      			</table>
      		</form>
      	</div>-->

      	<div id="cancelBox" class="bgwrap">
      		<div class="container" style="width:1000px">
      		<div style="text-align:left;margin-left:10px">
      				<h1>ยกเลิกรายการเติมเงิน</h1>
      		</div>

      		<form style="width:100%;text-align:center;" method="post">		
     			<div style="margin-top:10px;">
        		<table style="width:80%;text-align:left;">
        			<tr>
        				<th>Customer :</th><td name="cc-cname" id="cc-cname"></td>
        				<th>เลขที่เติมเงิน :</th><td name="cc-tno" id="cc-tno"></td>
        				<th>Acc. Name :</th><td name="cc-acname" id="cc-acname"></td>
        			</tr>
        			<tr>
        				<th>Amount :</th><td name="cc-amount" id="cc-amount"></td>
        				<th>Date :</th><td name="cc-date" id="cc-date"></td>
        				<th>Time :</th><td id="cc-time"></td>
        			</tr>
        			<tr>
        				<th>Transfer By :</th><td id="cc-by"/></td>
        				<th>Bank Name :</th><td id="cc-bname"/></td>
        				<th>Acc. No :</th><td name="cc-acnum" id="cc-acnum"></td>
        			</tr>
        		</table>
        		</div>

        		<div style="text-align:left;width:100%;margin-left:50px;">
						<div style="float:left;"><b>Remark Cancel :&nbsp;</b></div>
						<div><textarea name="cc-rmkcc" id="cc-rmkcc" style="width:70%;height:100px;font-size:16px;"></textarea></div>
				</div>

        		<div style="text-align:center;padding:10px">
        			<input type="hidden" id="c-tid" name="c-tid" value="" />
        			<input type="hidden" id="c-cid" name="c-cid" value="" />
        			<input type="hidden" id="tmp-tno" name="tmp-tno" value="" />
        			<input type="hidden" id="tmp-cmail" name="tmp-cmail" value="" />
        			<input type="hidden" id="tmp-cname" name="tmp-cname" value="" />
        			<input type="hidden" id="tmp-amount" name="tmp-amount">
        			<input type="hidden" id="tmp-date" name="tmp-date" value="" />
        			<input type="hidden" id="tmp-acnum" name="tmp-acnum" value="" />
        			<input type="hidden" id="tmp-ccode" name="tmp-ccode" value="" />
					<input type="hidden" name="cancel" value="1"/> 
					<button class="order-button">ยืนยัน</button>
					<a onclick="cancelBox();"><button class="order-cancel" type="button">ยกเลิก</button></a>
				</div>
			</form>
			</div>
      	</div>

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
                            <td class="normal"><b>Waiting :</b></td>
                            <td class="normal"><?php echo number_format($count[0]); ?>&nbsp;</td>
                            <td class="complete"><b>OK :</b></td>
                            <td class="complete"><?php echo number_format($count[1]); ?>&nbsp;</td>
                            <td class="cancel"><b>Cancel :</b></td>
                            <td class="cancel"><?php echo number_format($count[2]); ?>&nbsp;</td>
                            <td><b>Total :</b></td>
                            <td><?php echo number_format($totalCount); ?>&nbsp;</td>
                            <td>Records<br></td>
                        </tr>
                        <tr>
                            <td><b>จำนวนยอดทั้งหมด</b></td>
                            <td class="normal"><b>Waiting :</b></td>
                            <td class="normal"><?php echo number_format($sum[0],2); ?>&nbsp;</td>
                            <td class="complete"><b>OK :</b></td>
                            <td class="complete"><?php echo number_format($sum[1],2); ?>&nbsp;</td>
                            <td class="cancel"><b>Cancel :</b></td>
                            <td class="cancel"><?php echo number_format($sum[2],2); ?>&nbsp;</td>
                            <td><b>Total :</b></td>
                            <td><?php echo number_format($totalSum,2); ?>&nbsp;</td>
                            <td style="text-align: left;">THB</td>
                        </tr>
		</div>
		<script src="./controller.js"></script>
	</body>
</html>
<?php
	$con->close();
?>
