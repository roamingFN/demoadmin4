<!DOCTYPE html>
<html>
<head>
		<title>Cash</title>
		<meta charset="utf-8">
		<!--Jquery Datepicker Timepicker-->
		<link rel="stylesheet" href="../css/jquery-ui.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
		<script src="../js/jquery-ui.js"></script>
		<script src="../css/jquery-ui-timepicker-addon.min.css"></script>
		<script src="../js/jquery-ui-timepicker-addon.min.js"></script>
		<script src="./controller.js"></script>

		<link rel="stylesheet" type="text/css" href="../css/cargo.css">
		<link rel="stylesheet" type="text/css" href="../css/w3-green.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>

<!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">-->
<!--<script src="//code.jquery.com/jquery-1.10.2.js"></script>-->
<!--<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.css"></script>-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.js"></script>-->
		<script>
					$(function() {

						$( "#e-date" ).datepicker({
						    dateFormat: "dd-mm-yy"
						})
						
						
						$( "#e-time" ).timepicker({
							timeFormat: "HH:mm:ss"
						});
						
						$( "#transferDate" ).datepicker({
						    dateFormat: "dd-mm-yy"
						});

						$( "#transferDate" ).datepicker(
							    'setDate', new Date()
						);
					
					});

					init();
		</script>


		<?php
		session_start ();
		if (! isset ( $_SESSION ['ID'] )) {
			header ( "Location: ../login.php" );
		}
		
		include '../database.php';
		include './dialog/cancelBox.php';
		include '../utility/permission.php';

		const FORMID = 1;
		$_access = json_decode(getAccessForm($con,FORMID,$_SESSION['USERID']));
		$_adminFlg = getAdminFlag($con,$_SESSION['ID']);
		if ($_adminFlg==0) {
				if (empty($_access) || $_access[0]->visible==0) header ("Location: ../login.php");
		}
		
		// function genarate CRN
		function genCRN() {
			include '../database.php';
			$i = 0;
			$newTT = '0000001';
			$stmt = $con->prepare ( 'SELECT crn FROM cash ORDER BY crn DESC LIMIT 1' );
			$stmt->execute ();
			$result = $stmt->get_result ();
			while ( $row = $result->fetch_array ( MYSQLI_NUM ) ) {
				foreach ( $row as $r ) {
					$i += 1;
				}
			}
			// find year
			$year = substr ( ( string ) date ( "Y" ), 2, 2 );
			// cash has records
			if ($i) {
				$tempTT = ( string ) (( int ) substr ( $r, - 7 ) + 1);
				$len = strlen ( $tempTT );
				$yearBase = substr ( $r, 2, 2 );
				if ($year != $yearBase) {
				} else if ($len == 6) {
					$newTT = '0' . $tempTT;
				} else if ($len == 5) {
					$newTT = '00' . $tempTT;
				} else if ($len == 4) {
					$newTT = '000' . $tempTT;
				} else if ($len == 3) {
					$newTT = '0000' . $tempTT;
				} else if ($len == 2) {
					$newTT = '00000' . $tempTT;
				} else if ($len == 1) {
					$newTT = '000000' . $tempTT;
				} else {
					$newTT = $tempTT;
				}
			}
			return 'TT' . $year . $newTT;
		}
		
		// import excel
		function importExcel($file) {
			/**
			 * PHPExcel
			 */
			require_once '../Classes/PHPExcel.php';
			
			/**
			 * PHPExcel_IOFactory - Reader
			 */
			include '../Classes/PHPExcel/IOFactory.php';
			
			$inputFileName = 'import/' . $file;
			$inputFileType = PHPExcel_IOFactory::identify ( $inputFileName );
			$objReader = PHPExcel_IOFactory::createReader ( $inputFileType );
			$objReader->setReadDataOnly ( true );
			$objPHPExcel = $objReader->load ( $inputFileName );
			
			$objWorksheet = $objPHPExcel->setActiveSheetIndex ( 0 );
			$highestRow = $objWorksheet->getHighestRow ();
			$highestColumn = $objWorksheet->getHighestColumn ();
			
			$headingsArray = $objWorksheet->rangeToArray ( 'A1:' . $highestColumn . '1', null, true, true, true );
			$headingsArray = $headingsArray [1];
			
			$r = - 1;
			$i = 0;
			$namedDataArray = array ();
			
			// start at row 2
			for($row = 2; $row <= $highestRow; ++ $row) {
				$dataRow = $objWorksheet->rangeToArray ( 'A' . $row . ':' . $highestColumn . $row, null, true, true, true );
				if ((isset ( $dataRow [$row] ['A'] )) && ($dataRow [$row] ['A'] > '')) {
					++ $r;
					foreach ( $headingsArray as $columnKey => $columnHeading ) {
						// if read in date cell
						// if($columnHeading == "Date") {
						// $cell = $objPHPExcel->getActiveSheet()->rangeToArray("A1:".$highestColumn.$highestRow);
						// $i = 2;
						// foreach($cell as $val) {
						// // in my case 2nd column will be date so that I can get the format by
						// $date = date('Y/m/d',PHPExcel_Shared_Date::ExcelToPHP($objWorksheet->getCellByColumnAndRow(0, $row)->getValue())); // array index 1
						// }
						// $namedDataArray[$r][$columnHeading] = $date;
						// //echo $date;
						// }
						// if read in time cell
						if ($columnHeading == "Time") {
							$cell = $objWorksheet->getCellByColumnAndRow ( 1, $row );
							$cell_value = PHPExcel_Style_NumberFormat::toFormattedString ( $cell->getCalculatedValue (), 'hh:mm:ss' );
							$namedDataArray [$r] [$columnHeading] = $cell_value;
						} else if (is_null ( $dataRow [$row] [$columnKey] )) {
							$namedDataArray [$r] [$columnHeading] = "";
						} else {
							$namedDataArray [$r] [$columnHeading] = $dataRow [$row] [$columnKey];
						}
					}
				}
			}
			// print_r($namedDataArray);
			
			// echo '<pre>';
			// var_dump($namedDataArray);
			// echo '</pre><hr />';
			//
			// check acn in table bank_payment
			if (checkACN ( $namedDataArray ) == TRUE) {
				// insert data
				$importResult = 1;
				include '../database.php';
				foreach ( $namedDataArray as $result ) {
					$crn = genCRN ();
					// echo $crn.$result['Customer'].$result['Date'].$result['Time'].$result['Amount'].$result['Branch'].$result['Account'];
					$uid = $_SESSION ['ID'];
					$status = 0;
					// company bank
					$cbid = getCID ( $result ['Account'] );
					// bank
					if ($result ['Bank'] == "กรุงเทพ")
						$bid = 1;
					else if (trim ( $result ['Bank'] ) == "กรุงไทย")
						$bid = 2;
					else if (trim ( $result ['Bank'] ) == "กรุงศรีอยุธยา")
						$bid = 3;
					else if (trim ( $result ['Bank'] ) == "กสิกรไทย")
						$bid = 4;
					else if (trim ( $result ['Bank'] ) == "ไทยพาณิชย์")
						$bid = 5;
					else if (trim ( $result ['Bank'] ) == "ธนชาติ")
						$bid = 6;
					else if (trim ( $result ['Bank'] ) == "ทหารไทย")
						$bid = 7;
					else if (trim ( $result ['Bank'] ) == "ยูโอบี")
						$bid = 8;
					else
						$bid = 0;
					
					$resultDate = substr ( $result ['Date'], 6, 4 ) . '-' . substr ( $result ['Date'], 3, 2 ) . '-' . substr ( $result ['Date'], 0, 2 );
					$stmt = $con->prepare ( 'INSERT INTO cash(crn,customer,date,time,amount,uid,status,bid,cbid,branch,acn) VALUES(?,?,?,?,?,?,?,?,?,?,?)' );
					$stmt->bind_param ( 'ssssdsiiiss', $crn, $result ['Customer'], $resultDate, $result ['Time'], $result ['Amount'], $uid, $status, $bid, $cbid, $result ['Branch'], $result ['Account'] );
					
					$res = $stmt->execute ();
					
					if (! $res) {
						$importResult = $con->error;
					} else {
						$importResult = 1;
					}
				}
			} else {
				$importResult = 0;
			}
			return $importResult;
		}
		function checkACN($rs) {
			include '../database.php';
			// get bank_payment
			$cBanks = array ();
			if ($stmt = $con->prepare ( 'SELECT bank_id,account_no FROM bank_payment' )) {
				$stmt->execute ();
				$stmt->bind_result ( $b_id, $acn );
				while ( $stmt->fetch () ) {
					$cBanks [$b_id] = $acn;
				}
			}
			foreach ( $rs as $result ) {
				$flag = 0;
				reset ( $cBanks );
				if (in_array ( $result ['Account'], $cBanks )) {
					$flag = 1;
				}
				// echo 'result '.$flag.'<br>';
			}
			return $flag;
		}
		function getCID($rs) {
			include '../database.php';
			// get bank_payment
			$cBanks = array ();
			if ($stmt = $con->prepare ( 'SELECT bank_id,account_no FROM bank_payment' )) {
				$stmt->execute ();
				$stmt->bind_result ( $b_id, $acn );
				while ( $stmt->fetch () ) {
					$cBanks [$b_id] = $acn;
				}
			}
			// echo array_search($rs, $cBanks);
			return array_search ( $rs, $cBanks );
		}
		
		// search
		
		
		$_SESSION ['sql'] = '';
		$cases = array ();
		$request = '';
		ini_set ( 'display_errors', 0 );
		if (! empty ( $_GET ['customer'] )) {
			array_push ( $cases, ' customer LIKE "%' . $_GET ['customer'] . '%"' );
			$request .= '&customer=' . $_GET ['customer'];
		}
		if (! empty ( $_GET ['crn'] )) {
			array_push ( $cases, ' crn LIKE "%' . $_GET ['crn'] . '%"' );
			$request .= '&crn=' . $_GET ['crn'];
		}
		if (! empty ( $_GET ['from'] )) {
			
			array_push ( $cases, ' date>="' . substr ( $_GET ['from'], 6, 4 ) . '-' . substr ( $_GET ['from'], 3, 2 ) . '-' . substr ( $_GET ['from'], 0, 2 ) . ' 00:00:00"' );
			//$request .= 'from=' . substr ( $_GET ['from'], 6, 4 ) . '-' . substr ( $_GET ['from'], 3, 2 ) . '-' . substr ( $_GET ['from'], 0, 2 );
			$request .= '&from=' .$_GET ['from'];
			
		}
		if (! empty ( $_GET ['to'] )) {
			array_push ( $cases, ' date<="' . substr ( $_GET ['to'], 6, 4 ) . '-' . substr ( $_GET ['to'], 3, 2 ) . '-' . substr ( $_GET ['to'], 0, 2 ) . ' 23:59:59"' );
			//$request .= 'to=' . substr ( $_GET ['to'], 6, 4 ) . '-' . substr ( $_GET ['to'], 3, 2 ) . '-' . substr ( $_GET ['to'], 0, 2 );
			$request .= '&to=' .$_GET ['to'];
		}
		// if(!empty($_GET['acn'])){array_push($cases,' acn LIKE "%'.$_GET['acn'].'%"');$request .= 'acn='.$_GET['acn'];}
		
		if (! empty ( $_GET ['amount'] )) {
			array_push ( $cases, ' amount=' . $_GET ['amount'] );
			$request .= '&amount=' . $_GET ['amount'];
		}
		if (! empty ( $_GET ['uid'] )) {
			array_push ( $cases, ' uid="' . $_GET ['uid'] . '"' );
			$request .= '&uid=' . $_GET ['uid'];
		}
		
		if (! empty ( $_GET ['status'] )) {
			if ($_GET ['status'] == '-') {
				array_push ( $cases, ' status>=0' );
				$request .= '&status=-';
			} else {
				array_push ( $cases, ' status=' . $_GET ['status'] );
				$request .= '&status=' . $_GET ['status'];
			}
		}
		
		//search all 
		
		if (! empty ( $_GET ['searchAll'] )) {
					$searchAll=$_GET ['searchAll'].trim();
					$request .= '&searchAll=' . $_GET ['searchAll'];
			//echo 'SELECT bank_id FROM bank_payment where bank_name_en LIKE "%'.$searchAll.'%" OR bank_name_th LIKE "%'.$searchAll.'%"';
					$searcAllBank = array ();
					$stmt = $con->prepare ( 'SELECT bank_id FROM bank_payment where bank_name_en LIKE "%'.$searchAll.'%" OR bank_name_th LIKE "%'.$searchAll.'%" OR account_no LIKE "%'.$searchAll.'%"'  );
					//$stmt->bind_param ( "s", trim ( $_GET ['cbid2'] ) );
					$stmt->execute ();
					$stmt->bind_result($b_id);
					$j=0;
					while ( $stmt->fetch()) {
 						$searcAllBank [$j++] = $b_id; 						
						//echo $b_id.'<br/>';
			
					}
					unset ( $j );
// 					echo "<pre>";
// 					print_r($searcAllBank);
// 					echo "</pre>";
					$sqlSearchAll='';
					
					
					if(!empty($searcAllBank)){
						if(count($searcAllBank)>1){
							$sqlSearchAll="( ";
							$k=0;
							foreach($searcAllBank as $val){
								$sqlSearchAll.=' cbid = '.$val;
								
								if($k<count($searcAllBank)-1){
									$sqlSearchAll.=' OR';
								}
								$k++;
							}
							$sqlSearchAll.=' )';
							
						}else{
							$sqlSearchAll='( cbid =' . $searcAllBank [0] . ' )';
						}
						
					}else{
						$sqlSearchAll=' ( customer LIKE "%' . $searchAll . '%" OR crn LIKE "%' . $searchAll . '%" OR amount ="' . $searchAll . '" OR uid LIKE "%' . $searchAll . '%" OR branch LIKE "%' . $searchAll . '%" OR remarkc LIKE "%' . $searchAll . '%" )' ;
					}
					
					//echo $sqlSearchAll;
		}
		if (! empty ( $_GET ['searchAll'] )) {
			
			array_push ( $cases, $sqlSearchAll);
			$request .= '&customer=' . $_GET ['customer'];
		
		}
		
		$bf = "";
		if (! empty ( $_GET ['cbid2'] )) {			
			array_push ( $cases, '(' );
			$request .= '&cbid2=' . $_GET ['cbid2'];
		}
		
		if (! empty ( $_GET ['cbid'] )) {
			array_push ( $cases, $bf . ' cbid LIKE "%' . $_GET ['cbid'] . '%"' );
			$request .= '&cbid=' . $_GET ['cbid'];
		}
		
		$flagcbid2 = false;
		if (! empty ( $_GET ['cbid2'] )) {
			
			/**
			 * select bank_payment where bank_name_en=cbid2
			 */
			// echo 'SELECT bank_id FROM bank_payment where bank_name_en = "'.trim($_GET ['cbid2']).'"';
			$searcBank = array ();
			$stmt = $con->prepare ( 'SELECT bank_id FROM bank_payment where bank_name_en = ?' );
			$stmt->bind_param ( "s", trim ( $_GET ['cbid2'] ) );
			$stmt->execute ();
			$stmt->bind_result($b_id );
			$ii = 0;
			while ( $stmt->fetch () ) {
				$searcBank [$ii ++] = $b_id;

			}

			unset ( $ii );
			
			foreach ( $searcBank as $val ) {
				// echo $val;
				array_push ( $cases, ' cbid LIKE "%' . $val . '%"' );
			}
			array_push ( $cases, ')' );
			
			// print_r($cases);
			
			// array_push ( $cases, ' cbid LIKE "%' . $_GET ['cbid2'] . '%")' );
			//$request .= '&cbid=' . $_GET ['cbid'];
			$flagcbid2 = true;
		} // end cbid2
		
		ini_set ( 'display_errors', 1 );
		
// 		echo "<pre>";
// 		print_r ( $cases );
// 		echo "</pre>";
		
		$search = ' WHERE status=0';
		$searchTotal = '';
		$fbf = false;
		$fcbid = false;
		
		$search = ' WHERE status=0';
		$searchTotal = '';
		if (sizeof ( $cases ) > 0) {
			
			if ($cases [0] == '(' && $cases [sizeof ( $cases ) - 1] == ')') {
				$search .= ' AND ' . $cases [0];
				for($i = 1; $i < sizeof ( $cases ); $i ++) {
					$search .= $cases [$i];
					if ($i < sizeof ( $cases ) - 1 && $cases [$i] != '(') {
						if ($cases [$i + 1] != ')') {
							$search .= ' OR ';
						}
					}
				}
			} else {
				
				$search = ' WHERE' . $cases [0];
				$searchTotal = ' AND ' . $cases [0];
				for($i = 1; $i < sizeof ( $cases ); $i ++) {
					
					// echo $cases [$i] . '<br/>';
					
					if ($cases [$i] == '(') {
						$search .= ' AND ';
						for($j = $i; $j < sizeof ( $cases ); $j ++) {
							$search .= $cases [$j];
							if ($j < sizeof ( $cases ) - 1 && $cases [$j] != '(') {
								if ($cases [$j + 1] != ')') {
									$search .= ' OR ';
								}
							}
							$i ++;
						}
					} else {
						
						$search .= ' AND' . $cases [$i];
						if (! (strpos ( $cases [$i], 'status=' ) !== false)) {
							$searchTotal .= ' AND' . $cases [$i];
						}
					}
				}
			}
		}
		
		// paging
		// $pageSize = 20;
		$pageSize = 15;
		$allPage = 0;
		if (isset ( $_GET ['page'] )) {
			$nowPage = $_GET ['page'] - 1;
		} else {
			$nowPage = 0;
		}
		//echo $request;
		//print_r();
		
		 //echo 'SELECT * from cash' . $search;
		
		$_SESSION ['sql'] = 'SELECT * from cash' . $search;
		// echo $_SESSION['sql'];
		// echo $searchTotal;
		
		// add
		if (isset ( $_POST ['add'] )) {
			$arrErrors = array ();
			$arrErrEmptyField = array ();
			$resultTT = "";
			if (isset ( $_POST ['amount'] ) && isDataAmount ( $_POST ['amount'] )) {
				
				for($i = 0; $i < count ( $_POST ['amount'] ); ++ $i) {
					$resultTT = genCRN ();
					if (! empty ( $resultTT ) && ! empty ( $_POST ['date'] ) && ! empty ( $_POST ['time'] [$i] ) && ! empty ( $_POST ['amount'] [$i] ) && ! empty ( $_POST ['uid'] ) && ! empty ( $_POST ['cbid'] )) {
						$amountInt = ereg_replace ( "[^0-9.]", "", $_POST ['amount'] [$i] );
						
						$bidPost = 0;
						if ($_POST ['bid'] [$i] != '0' || $_POST ['bid'] [$i] != 0) {
							$bidPost = $_POST ['bid'] [$i];
						} else {
							$bidPost = 0;
						}
						
						$formatted_date = substr ( $_POST ['date'], 6, 4 ) . '-' . substr ( $_POST ['date'], 3, 2 ) . '-' . substr ( $_POST ['date'], 0, 2 );
						
						$stmt = $con->prepare ( 'INSERT INTO cash(crn,customer,date,time,amount,branch,remark,bid,uid,status,cbid) VALUES(?,?,DATE(?),?,?,?,?,?,?,?,?)' );
						$stmt->bind_param ( 'ssssdssssis', $resultTT, $_POST ['customer'] [$i], $formatted_date, $_POST ['time'] [$i], $amountInt, $_POST ['branch'] [$i], $_POST ['remark'] [$i], $bidPost, $_POST ['uid'], $_POST ['status'], $_POST ['cbid'] );
						
						$res = $stmt->execute ();
						if (! $res) {
							$arrErrors [] = array (
									'customer' => $_POST ['customer'] [$i],
									'status' => 0 
							);
						}
					} else if (empty ( $resultTT ) || empty ( $_POST ['date'] ) || empty ( $_POST ['time'] [$i] ) || empty ( $_POST ['amount'] [$i] ) || empty ( $_POST ['uid'] ) || empty ( $_POST ['cbid'] )) {
						// $arrErrEmptyField [] = 0;
					}
				} // end for
			} else {
				echo '<script>alert("กรุณาใส่ข้อมูลให้ครบทุกช่องค่ะ!");</script>';
			}
			$msgError = "";
			if (count ( $arrErrors ) > 0) {
				for($i = 0; $i < count ( $arrErrors ); ++ $i) {
					if ($arrErrors [$i] ['status'] == 0) {
						$msgError .= "customer: " . $arrErrors [$i] ['customer'] . ' การเพิ่มข้อมูลล้มเหลว  \n';
					}
				}
			}
			if (isset ( $_POST ['amount'] ) && isDataAmount ( $_POST ['amount'] )) {
				if (empty ( $msgError )) {
					echo '<script>alert("เพิ่มข้อมูลสำเร็จ");window.location = "./cash.php";</script>';
					// sleep(10);
					// header("Location: cash.php");
				} else {
					echo '<script>alert("' . $msgError . '");</script>';
				}
				
				// if (count ( $arrErrEmptyField ) > 0) {
				// echo '<script>alert("กรุณาใส่ข้อมูลให้ครบทุกช่องค่ะ!");</script>';
				// }
			}
		} // end add
		function isEmpty($arrays) {
			foreach ( $arrays as $val ) {
				if (empty ( $val )) {
					return true;
				}
			}
			return false;
		}
		function isDataAmount($param) {
			if (count ( $param ) > 0) {
				foreach ( $param as $val ) {
					if (strlen ( $val ) > 0) {
						return true;
					}
				}
			}
			return false;
		}
		
		// edit
		if (isset ( $_POST ['edit'] )) {
			// format date
			$formatted_date = substr ( $_POST ['date'], 6, 4 ) . '-' . substr ( $_POST ['date'], 3, 2 ) . '-' . substr ( $_POST ['date'], 0, 2 );
			$stmt = $con->prepare ( 'UPDATE cash SET customer=?,date=DATE(?),time=TIME(?),amount=?,branch=?,remark=?,bid=?,cbid=? WHERE crn=?' );
			$stmt->bind_param ( 'sssdssiss', $_POST ['customer'], $formatted_date, $_POST ['time'], $_POST ['amount'], $_POST ['branch'], $_POST ['remark'], $_POST ['bid'], $_POST ['cbid'], $_POST ['crn'] );
			$res = $stmt->execute ();
			if (! $res) {
				echo '<script>alert("การแก้ไขข้อมูลล้มเหลว");</script>';
			} else {
				echo '<script>alert("แก้ไขข้อมูลสำเร็จ");</script>';
			}
		}
		
		// delete
		if (isset ( $_POST ['del'] )) {
			if ($stmt = $con->prepare ( 'DELETE FROM cash WHERE crn="' . $_POST ['del'] . '"' )) {
				$res = $stmt->execute ();
				if (! $res) {
					echo '<script>alert("การลบข้อมูลล้มเหลว");</script>';
				} else {
					echo '<script>alert("ลบข้อมูลสำเร็จ");window.location = "./cash.php";</script>';
				}
			}
		}
		
		// cancel
		if (isset ( $_POST ['cancel'] ) && isset ( $_POST ['remarkc'] )) {
			if ($stmt = $con->prepare ( 'UPDATE cash SET remarkc="' . $_POST ['remarkc'] . '",status=2 WHERE crn="' . $_POST ['cancel'] . '"' )) {
				$res = $stmt->execute ();
				if (! $res) {
					echo '<script>alert("การยกเลิกข้อมูลล้มเหลว");</script>';
				} else {
					echo '<script>alert("ยกเลิกข้อมูลสำเร็จ");</script>';
				}
			}
		}
		
		// import
		if (isset ( $_POST ['import'] )) {
			if (! isset ( $_FILES ['excelDir'] ['name'] )) {
			} else {
				// echo $_FILES['excelDir']['name'];
				move_uploaded_file ( $_FILES ["excelDir"] ["tmp_name"], "import/" . $_FILES ['excelDir'] ['name'] );
				$res = importExcel ( $_FILES ["excelDir"] ["name"] );
				if ($res == 1) {
					echo '<script>alert("การเพิ่มข้อมูลสำเร็จ");</script>';
				} else {
					echo '<script>alert("การเพิ่มข้อมูลล้มเหลว\n' . $res . '");</script>';
				}
			}
		}
		
		
		
		/*
		 * $count = 0; $normal = 0; $complete = 0; $cancel = 0; $countNormal = 0; $countComplete = 0; $countCancel = 0;
		 */
		?>

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
					location.reload();
				}
			}
			var editOn = false;
			function edit(crn){
				
				document.getElementById('addBox').style.visibility = 'hidden';
				document.getElementById('searchBox').style.visibility = 'hidden';
				editOn = !editOn;
				if(editOn){
					console.log('edit:'+document.getElementById(crn+'branch').textContent);
					document.getElementById('editBox').style.visibility = 'visible';
					document.getElementById('e-crn').value = document.getElementById(crn).textContent;
					document.getElementById('e-customer').value = document.getElementById(crn+'customer').textContent;
					document.getElementById('e-date').value = document.getElementById(crn+'date').textContent.split(" ")[0];
					document.getElementById('e-time').value = document.getElementById(crn+'date').textContent.split(" ")[1];
					document.getElementById('e-amount').value = document.getElementById(crn+'amount').value;                                 
					document.getElementById('e-branch').value = document.getElementById(crn+'branch').textContent;
					document.getElementById('e-remark').value = document.getElementById(crn+'remark').textContent;

					console.log('bid:'+document.getElementById(crn+'bid').value);
					document.getElementById('bid').value=document.getElementById(crn+'bid').value;
					//document.getElementById('e-bid-'+document.getElementById(crn+'bid').value).selected = true;
					//document.getElementById('e-acn').value = document.getElementById(crn+'acn').textContent;
					console.log(document.getElementById(crn+'cbid').value);
					document.getElementById('e-cbid').value = document.getElementById(crn+'cbid').value;
					//document.getElementById('e-uid-'+document.getElementById(crn+'uid').textContent).selected = true;
					//document.getElementById('e-remarkc').value = document.getElementById(crn+'remarkc').textContent;
					//document.getElementById('e-status-'+document.getElementById(crn+'status').value).selected = true;
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
					

					$( "#dateFrom,#dateTo" ).datepicker({
					    dateFormat: "dd-mm-yy"
					});

				}else{
					document.getElementById('searchBox').style.visibility = 'hidden';
				}
			}
            
           
			
            function exportExcel(){
				window.open('cash_excel.php','_blank');
            }
            
		</script>
</head>

<body>

	<h1>
		<b><a href="cash.php">Cash</a></b>
	</h1>

	<h3>
		<a href="../index.php">&larr; Back</a>
	</h3>
	<br>

	<?php
			echo '<div class="menu">';
			if ($_access[0]->canadd==1 || $_adminFlg==1) echo '<i class="material-icons" onclick="add();" title="Add">&#xE147;</i>';
			echo '<i class="material-icons" onclick="exportExcel();" title="Export">&#xE24D;</i>
				<!-- <i class="material-icons" onclick="window.print();" title="Print">&#xE8AD;</i> -->
				<i class="material-icons" onclick="searchBox();" title="Search">&#xE880;</i>
			</div>';
	?>

	<table class="detail">
		<tr>
			<th>Number</th>
			<th>Customer</th>
			<th>Date Time</th>
			<th>Amount</th>
			<th>Branch</th>
			<th>Remark</th>
			<th>Bank</th>
			<th>Account No.</th>
			<th>Add Users</th>
			<th>Add DateTime</th>
			<th>Status</th>
			<th>Remark Cancel</th>
			<th>Action</th>
		</tr>
			<?php
			
			// get bank
			$banks = array ();
			if ($stmt = $con->prepare ( 'SELECT bname FROM bank ORDER BY CONVERT (bname USING tis620)' )) {
				$stmt->execute ();
				$stmt->bind_result ( $bname );
				while ( $stmt->fetch () ) {
					array_push ( $banks, $bname );
				}
			}
			
			;
			
			// get bank payment
			$cBanks = array ();
			$bName = array();
			if ($stmt = $con->prepare ( 'SELECT bank_id,bank_name_th,account_no,bank_name_en FROM bank_payment' )) {
				$stmt->execute ();
				$stmt->bind_result ( $b_id, $cif, $acn ,$nen);
				while ( $stmt->fetch () ) {
					$cBanks [$b_id] = $cif . " " . $acn;
					$bName[$b_id] = $nen;
				}
			}
			
			// get user
			$users = array ();
			if ($stmt = $con->prepare ( 'SELECT uid FROM user' )) {
				$stmt->execute ();
				$stmt->bind_result ( $uid );
				while ( $stmt->fetch () ) {
					array_push ( $users, $uid );
				}
			}
			
			// get customer name
			$_cus = array ();
			$_cus [0] = '';
			$sql = 'SELECT customer_firstname,customer_lastname,topup_id from customer c' . ' JOIN customer_request_topup t on t.customer_id=c.customer_id';
			if ($stmt = $con->prepare ( $sql )) {
				$stmt->execute ();
				$stmt->bind_result ( $cfname, $clname, $topup_id );
				while ( $stmt->fetch () ) {
					$_cus [$topup_id] = $cfname . ' ' . $clname;
				}
			}
			
			// error 0
			error_reporting ( 0 );
			
			// select bank account company distinct
			$banksUnique = array ();
			if ($stmt = $con->prepare ( 'SELECT distinct bank_name_en,bank_name_th FROM bank_payment' )) {
				$stmt->execute ();
				$stmt->bind_result ( $b_id, $acn );
				while ( $stmt->fetch () ) {
					$banksUnique [$b_id] = $acn;
				}
			}
			
			// show table
			$sql = 'SELECT cashid,crn,customer,date,time,amount,remark,branch,bid,acn,uid,ctime,remarkc,status,cbid,topup_id' . ' FROM cash';
			$orderBy = ' ORDER BY crn DESC';
			if ($stmt = $con->prepare ( $sql . $search . $orderBy )) {
				$stmt->execute ();
				$stmt->store_result ();
				$count = $stmt->num_rows;
				$allPage = ceil ( $count / $pageSize );
				$stmt->close ();
				
				$stmt = $con->prepare ( $sql . $search . $orderBy . ' LIMIT ' . $nowPage * $pageSize . ',' . $pageSize );
				// echo 'SELECT * FROM cash '.$search.' LIMIT '.$nowPage*$pageSize.','.$pageSize;
				$stmt->execute ();
				
				$stmt->bind_result ( $cid, $crn, $customer, $date, $time, $amount, $remark, $branch, $bid, $acn, $uid, $ctime, $remarkc, $status, $cbid, $topup_id );
				$puncCount = 0;
				while ( $stmt->fetch () ) {
					
					$bif = explode ( " ", $cBanks [$cbid] );
					
					// set status description
					$statdesc = "";
					if ($status == 0)
						$statdesc = 'Normal';
					else if ($status == 1)
						$statdesc = 'Complete';
					else if ($status == 2)
						$statdesc = 'Cancel';
						// date and time for 'Add Time' column
					$addDate = substr ( $ctime, 8, 2 ) . '-' . substr ( $ctime, 5, 2 ) . '-' . substr ( $ctime, 0, 4 );
					$addTime = substr ( $ctime, 10, 9 );
					
					echo '<tr class="' . ($puncCount % 2 == 0 ? 'punc ' : '') . ($status == 0 ? 'normal ' : '') . ($status == 2 ? 'cancel ' : '') . ($status == 1 ? 'complete  ' : '') . '">' . 
					'<td id="' . $crn . '">' . $crn . '</td>' . 
					'<td id="' . $crn . 'customer">' . $_cus [$topup_id] . '</td>' . 
					'<td id="' . $crn . 'date">' . substr ( $date, 8, 2 ) . '-' . substr ( $date, 5, 2 ) . '-' . substr ( $date, 0, 4 ) . ' '. $time .'</td>' . 
					//'<td id="' . $crn . 'time">' . $time . '</td>' . 
					'<input type="hidden" id="' . $crn . 'amount"/ value="' . $amount . '">' . 
					'<td id="num">' . number_format ( $amount, 2 ) . '</td>' . 
					'<input id="' . $crn . 'bid" type="hidden" value="' . ($bid) . '"/><td id="' . $crn . 'branch">' . $branch . '</td>' . 
					'<td id="' . $crn . 'remark">' . $remark . '</td>' . 
					'<td id="' . $crn . 'bname">' . $bName[$cbid] . '</td>' . 
					'<td id="' . $crn . 'bacn">' . $bif [1] . '</td><input id="' . $crn . 'cbid" type="hidden" value="' . $cbid . '"/>' . 
					'<td id="' . $crn . 'uid">' . $uid . '</td>' . 
					'<td>' . $addDate . ' ' . $addTime . '</td>' . 
					'<td id="' . $crn . 'status">' . $statdesc . '</td>' . 
					'<td id="' . $crn . 'remarkc">' . $remarkc . '</td>';
					
					if (($status != 1) && ($_access[0]->action==1 || $_adminFlg==1)) {
						echo '<td><button onclick="edit(\'' . $crn . '\')">Edit</button>' . '<form onsubmit="return confirm(\'ต้องการลบข้อมูลใช่หรือไม่?\');" action="cash.php?page=' . ($nowPage + 1) . '" method="post">' . '<input name="del" value="' . $crn . '" type="hidden"/><button>Del</button>' . '</form>';
						// action cancel
						// if ($status != 2) {
						// 	echo '<form onclick="input=prompt(\'กรุณาใส่เหตุผลที่ต้องการยกเลิก\');this.ok=false;' . 'if(input!=null){document.getElementById(\'cancel' . $crn . 'remarkc\').value=input;this.ok=true;}"' . ' onsubmit="return this.ok;" action="cash.php?page=' . ($nowPage + 1) . '" method="post">' . '<input name="cancel" value="' . $crn . '" type="hidden"/>' . '<input id="cancel' . $crn . 'remarkc" name="remarkc" type="hidden"/>' . '<button>Cancel</button>' . '</form>';
						// }
						if ($status != 2) {
								echo '<button onclick="showCancelBox(\''.$cid.'\',\''.$crn.'\')">Cancel</button>';
						}
						echo '</td></tr>';
					} else {
						echo '<td></td></tr>';
					}
					$puncCount ++;
				}
				$stmt->close ();
			}
			
			// summary
			$sum = array (
					0,
					0,
					0 
			);
			$count = array (
					0,
					0,
					0 
			);
			$totalSum = 0;
			$totalCount = 0;
			$groupByStat = ' GROUP BY STATUS';
			// echo 'SELECT status,sum(amount),count(cashid) FROM cash'.$search.$groupByStat;
			if ($stmt = $con->prepare ( 'SELECT status,sum(amount),count(cashid) FROM cash' . $search . $groupByStat )) {
				$stmt->execute ();
				$stmt->bind_result ( $stat, $amount, $countId );
				while ( $stmt->fetch () ) {
					$sum [$stat] = $amount;
					$count [$stat] = $countId;
					
					$totalSum += $amount;
					$totalCount += $countId;
				}
			}
			?>
		</table>
	<br>

	<!--Add Box-->
	<div id="addBox" class="wrap">
		<form method="post" id="frmSave">
			<table class="df">
				<tr>
					<th><h2 id="title">Add</h2></th>
					<td></td>
				</tr>
				<tr>
					<th style="width: 140px;">Number:</th>
					<td>-</td>
				</tr>
				<tr>
					<th>Transfer Date :</th>
					<td><input class="datepicker" name="date" required="required"
						id="transferDate" style="width: 10%;" /></td>
				</tr>
				<tr>
					<th>Account :</th>
					<td><select name="cbid">
						<?php
						/*
						 * for($i=0;$i<sizeof($cBanks);$i++){ echo '<option value="'.($i+1).'">'.$cBanks[$i].'</option>'; }
						 */
						reset ( $cBanks );
						for($i = 0; $i < sizeof ( $cBanks ); $i ++) {
							echo '<option value="' . key ( $cBanks ) . '">' . current ( $cBanks ) . '</option>';
							next ( $cBanks );
						}
						?>
                        </select>
						<button onclick="return false;" style="float: right;" id="append">+Append</button>
					</td>
				</tr>
				<input type="hidden" name="uid"
					value="<?php echo $_SESSION['ID'] ?>">;
				<input type="hidden" name="status" value="0">
				<input type="hidden" name="add" value="1" />

				<style>
.dg-group {
	overflow-x: hidden;
	overflow-y: auto;
	height: 385px;
}

.dg {
	box-shadow: none !important;
	position: relative !important;
	top: 3px !important;
	width: 100% !important;
}

.dg th {
	background: #3d8b40 none repeat scroll 0 0;
	border-right: 1px solid #00796b;
	color: #fff;
	padding: 4px;
	text-align: center;
}

.dg input {
	max-width: 98%;
	width: 100% !important;
}

@media screen and (-webkit-min-device-pixel-ratio:0) {
	.dg select {
		height: 21px !important;
		max-width: 100%;
		width: 100% !important;
	}
}

.dg select {
	height: 23px;
	max-width: 100%;
	width: 100% !important;
}

.dg tr {
	height: 0 !important;
}

.dg td {
	padding: 0 !important;
}

.activeDG {
	background-color: #eee;
}

.dg-del,.dg-Add {
	margin: 0;
	outline: medium none;
	padding: 0 3px;
	width: 40%;
}

.total span,.rows span {
	color: #000;
	font-family: tahoma;
	font-size: 13px;
	font-weight: bold;
}

#ttval {
	color: #f00;
	font-size: 16px;
}

.dg tr td:first-child {
	text-align: center;
}

.dg tr:hover {
	background: #93cf95 none repeat scroll 0 0;
}

.rows,.total {
	padding-left: 15px;
}

.paging a {
	text-decoration: underline;
}

a.current-page {
	text-decoration: none;
}
</style>


				<td colspan="2">
					<div class="dg-group">
						<table class="dg" id="dg">
							<thead>
								<th width="1%"></th>
								<th width="10%">Tranfer Time</th>
								<th width="10%">Amount</th>
								<th width="13%">Bank Name</th>
								<th width="13%">Branch Name</th>
								<th width="13%">Customer</th>
								<th width="13%">Remark</th>
								<th width="5%">Action</th>
							</thead>
							<tbody>
									<?php for($loop=1;$loop<=15;++$loop){?>
									<tr class="dgdel-<?php echo $loop;?>">
									<td><?php echo $loop;?></td>
									<td><input class="timepicker1" name="time[]" step="1"
										onkeyup="this.value=this.value.replace(/[^0-9:]/g,'');" /></td>
									<td><input
										onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');"
										name="amount[]" class="amount"></td>
									<td><select name="bid[]">
												<?php
										echo '<option id="e-bid" value="0">-</option>';
										for($i = 0; $i < sizeof ( $banks ); $i ++) {
											echo '<option id="e-bid-' . $i . '" value="' . ($i + 1) . '">' . $banks [$i] . '</option>';
										}
										?>
                        					</select></td>
									<td><input type="text" name="branch[]" class="branch" /></td>
									<td><input type="text" name="customer[]" /></td>
									<td><input type="text" name="remark[]" /></td>
									<td>
										<button class="dg-del" id="dgdel-<?php echo $loop;?>"
											onClick="return false;">Del</button>
										<button class="dg-Add" id="dgAdd-<?php echo $loop;?>"
											onClick="return false;">Add</button>
									</td>

								</tr>
									
									<?php }//end for?>
								</tbody>
						</table>

					</div>
				</td>
				</tr>

				<tr>
					<td colspan="2">
						<div class="rows">
							<span>Rows:</span> <span id="rval"></span>
						</div>
					</td>
				</tr>

				<tr class="confirm">
					<td><div class="total" style="text-align: left; width: 165px;">
							<span>Total:</span> <span id="ttval"></span>
						</div></td>
					<td>
						<button>Save</button> <a onclick="add();">Cancel</a>&emsp;
					</td>
				</tr>
				<tr></tr>
			</table>

		</form>
	</div>

	<script type="text/javascript">

	//count row data
	var rows=0;
	var format = function(num){
		var str = num.toString().replace("", ""), parts = false, output = [], i = 1, formatted = null;
		if(str.indexOf(".") > 0) {
			parts = str.split(".");
			str = parts[0];
		}
		str = str.split("").reverse();
		for(var j = 0, len = str.length; j < len; j++) {
			if(str[j] != ",") {
				output.push(str[j]);
				if(i%3 == 0 && j < (len - 1)) {
					output.push(",");
				}
				i++;
			}
		}
		formatted = output.reverse().join("");
		return( formatted + ((parts) ? "." + parts[1].substr(0, 2) : ""));
	};
	$(function(){
	    $(".amount").keyup(function(e){
	        $(this).val(format($(this).val()));
	    });
	});


	//onpaste
	function onTrim(obj){
		setTimeout(function(){
			var valPaste=$(obj).val();
			console.log($(obj));
			
	    }, 0);
		
	}

	$(".branch").bind("change", function(e){
	    // access the clipboard using the api
	    //var pastedData = e.originalEvent.clipboardData.getData('text');
	    //alert(pastedData);
	    console.log($(this).val());
	    var pastedData=$(this).val();
	    $(this).val(pastedData.trim());
	});

	




										
	$("#append").click(function() {
	    //default minRow=14
	    var minRow = 14;
	    var lineNoStr = $("#dg").find("tbody tr:last-child td:first-child").text();
	    lineNo = Number(lineNoStr) + 1;
	    var msgHtml = "";
	    for (i = lineNo; i <= (lineNo + minRow); ++i) {
	        msgHtml += '<tr class="dgdel-' + i + '">';
	        msgHtml += '<td>' + i + '</td>';
	        msgHtml += '<td><input class="timepicker2" name="time[]" step="1" onkeyup="this.value=this.value.replace(/[^0-9:]/g,\'\');"  /></td>';
	        msgHtml += '<td><input onkeyup="this.value=this.value.replace(/[^0-9.]/g,\'\');" name="amount[]" class="amount"></td>';
	        msgHtml += '<td>';
	        msgHtml += '<select name="bid[]">';
	        msgHtml += '<?php
									echo '<option id="e-bid" value="0">-</option>';
									for($i = 0; $i < sizeof ( $banks ); $i ++) {
										echo '<option id="e-bid-' . $i . '" value="' . ($i + 1) . '">' . $banks [$i] . '</option>';
									}
									?> ';
	        msgHtml += '</select>';
	        msgHtml += '</td>';
	        msgHtml += '<td>';
	        msgHtml += '<input type="text" name="branch[]" class="branch"/>';
	        msgHtml += '</td>';
	        msgHtml += '<td>';
	        msgHtml += '<input type="text" name="customer[]" />';
	        msgHtml += '</td>';
	        msgHtml += '<td>';
	        msgHtml += '<input type="text" name="remark[]"/>';
	        msgHtml += '<td><button class="dg-del" id="dgdel-' + i + '" onClick="return false;">Del</button>';
	        msgHtml += '<button class="dg-Add" id="dgAdd-" onClick="return false;" style="width: 52%;">Add</button></td>';
	        msgHtml += '</td>';
	        msgHtml += '</tr>';
	    }
	    $("#dg").find('tbody').append(msgHtml);

	    $("input,select").click(function() {
	        $('.dg').find('input,select').removeClass('activeDG');
	        o = $(this).parents("tr").attr('class');
	        $('.' + o).find('input,select').addClass('activeDG');
	        $('.' + o).find('option').css('background-color', '#fff');
	    });

	    


	    //check total
	    $('input[name="amount[]"]').bind("change keyup", function() {
	        var totalTmp = 0;
	        $('input[name="amount[]"]').each(function() {
	            //console.log($(this).val());
	            totalTmp += Number($(this).val().replace(/[^0-9\.]+/g,""));
	        });
	        console.log("totalInner:" + totalTmp);
	        $("#ttval").text(formatIn(totalTmp));
	        

	        if(0!=$(this).val().length){
				$obj=$(this).parents('tr').attr('class');
				$('.'+$obj).find('input[name="time[]"]').attr('required','required');
				$('.'+$obj).find('input[name="branch[]"]').attr('required','required');
				
	        }else{
	        	console.log('no pass');
	        	$obj=$(this).parents('tr').attr('class');
	        	//$('.'+$obj).find('input[name="time[]"]').removeAttr('required');
	        	//$('.'+$obj).find('input[name="branch[]"]').removeAttr('required');
	        }

	        
	    });

	    $('input[name="branch[]"]').bind("change keyup", function() {
    		if(0!=$(this).val().length){
    			$obj=$(this).parents('tr').attr('class');
    			$('.'+$obj).find('input[name="time[]"]').attr('required','required');
    			$('.'+$obj).find('input[name="amount[]"]').attr('required','required');
    			
            }else{
            	console.log('no pass');
            	$obj=$(this).parents('tr').attr('class');
            	$('.'+$obj).find('input[name="time[]"]').removeAttr('required');
            	$('.'+$obj).find('input[name="amount[]"]').removeAttr('required');
            }
    	});

	    function formatIn(n) {
	        return n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
	    }

	    var format = function(num){
			var str = num.toString().replace("", ""), parts = false, output = [], i = 1, formatted = null;
			if(str.indexOf(".") > 0) {
				parts = str.split(".");
				str = parts[0];
			}
			str = str.split("").reverse();
			for(var j = 0, len = str.length; j < len; j++) {
				if(str[j] != ",") {
					output.push(str[j]);
					if(i%3 == 0 && j < (len - 1)) {
						output.push(",");
					}
					i++;
				}
			}
			formatted = output.reverse().join("");
			return( formatted + ((parts) ? "." + parts[1].substr(0, 2) : ""));
		};

		    $(".amount").keyup(function(e){
		        $(this).val(format($(this).val()));
		    });



			$('input[name="time[]"]').bind("   keyup", function() {
				$(this).val().trim();
				$(this).val($(this).val().trim());
				if(0!=$(this).val().length){
					$obj=$(this).parents('tr').attr('class');
					console.log('pass new');
					$('.'+$obj).find('input[name="amount[]"]').attr('required','required');
					$('.'+$obj).find('input[name="branch[]"]').attr('required','required');
					
		        }else{
		        	console.log('no passnew');
		        	$obj=$(this).parents('tr').attr('class');
		        	$('.'+$obj).find('input[name="amount[]"]').removeAttr('required');
		        	$('.'+$obj).find('input[name="branch[]"]').removeAttr('required');
		        }
			});
			
			$('.timepicker2').on('change',function(e){
				//rows++;
				/**
				* 1.check this obj is format error is require 
				* 2.check trim empty is required
				*/

				getSizeRowCellValue('.timepicker2');
			});

			//$('#rval').text($("tr[class^='dgdel-']:last-child").find('td:first-child').text());
			
			function getSizeRowCellValue(classArray){
				/**
				* 1.get size all of table > tr > td 
				* 2. $('#rval').text($("tr[class^='dgdel-']:last-child").find('td:first-child').text())
				* 3. for loop and check current classnName[array] have value 
				* 4. update currentRow if 3 have value.
				*/
				var sizeAllCell=0;
				var time1=0;
				var currRow=0;
				sizeAllCell=$("tr[class^='dgdel-']:last-child").find('td:first-child').text();
				for(var i=0;i<sizeAllCell;++i){
					time1=$('.dgdel-'+(i+1)).find('input[name="time[]"]').val();
					if(time1!=''){
						console.log('time '+(i+1)+':'+time1);
						currRow++
						console.log('current row:'+currRow);
					}
					$('#rval').text(currRow);
				}
			}

		$(".branch").bind("change", function(e){
			    // access the clipboard using the api
			    //var pastedData = e.originalEvent.clipboardData.getData('text');
			    //alert(pastedData);
			    console.log($(this).val());
			    var pastedData=$(this).val();
			    $(this).val(pastedData.trim());
			});
			//end append add row
	

	}); //end append

	$(document).on('click','.dg-del',function(e) {
	    var c = $(this).attr("id");
	    console.log(c);
	    $("." + c).fadeOut("100", function() {
	        $(this).remove();
	        var ctr = $("#dg").find("tbody tr").length;
	        for (i = 1; i <= ctr; ++i) {
	            var l = $("#dg").find("tbody tr:nth-child(" + i + ") td:first-child")
	            $("#dg").find("tbody tr:nth-child(" + (i) + ")").attr('class', "dgdel-" + i);
	            $("#dg").find("tbody tr:nth-child(" + (i) + ") button").attr('id', "dgdel-" + i);
	            l.html(i);

	        }
	        var totalTmp = 0;
	        $('input[name="amount[]"]').each(function() {
	            totalTmp += Number($(this).val().replace(/[^0-9\.]+/g,""));
	        });
	        console.log("total:" + totalTmp);
	        $("#ttval").text(formatExt(totalTmp));
	       // $('#rval').text($("tr[class^='dgdel-']:last-child").find('td:first-child').text());
	        getSizeRowCellValue('.timepicker1');

	    });

	});

	$(document).on('click','.dg-Add',function(e) {
	    var c = $(this).attr("id");
	    var nextAppend= parseInt(c.split('-')[1])+1;
	    console.log('Next Append1:'+nextAppend);
	    var $this = $(this),
        $parentTR = $this.closest('tr');
	    //$parentTR.clone().insertAfter($parentTR);
	    //var cloneHtml=$parentTR.clone();
	    //$(cloneHtml).find('input').val().insertAfter($parentTR);
	    var i=0;
	    var msgHtml = "";
	        msgHtml += '<tr class="dgdel-0">';
	        msgHtml += '<td></td>';
	        msgHtml += '<td><input class="timepicker1" name="time[]" step="1" onkeyup="this.value=this.value.replace(/[^0-9:]/g,\'\');"  /></td>';
	        msgHtml += '<td><input onkeyup="this.value=this.value.replace(/[^0-9.]/g,\'\');" name="amount[]" class="amount"></td>';
	        msgHtml += '<td>';
	        msgHtml += '<select name="bid[]">';
	        msgHtml += '<?php
									echo '<option id="e-bid" value="0">-</option>';
									for($i = 0; $i < sizeof ( $banks ); $i ++) {
										echo '<option id="e-bid-' . $i . '" value="' . ($i + 1) . '">' . $banks [$i] . '</option>';
									}
									?> ';
	        msgHtml += '</select>';
	        msgHtml += '</td>';
	        msgHtml += '<td>';
	        msgHtml += '<input type="text" name="branch[]" class="branch"/>';
	        msgHtml += '</td>';
	        msgHtml += '<td>';
	        msgHtml += '<input type="text" name="customer[]" />';
	        msgHtml += '</td>';
	        msgHtml += '<td>';
	        msgHtml += '<input type="text" name="remark[]"/>';
	        msgHtml += '<td><button class="dg-del" id="dgdel-" onClick="return false;">Del</button>';
	        msgHtml += '<button class="dg-Add" id="dgAdd-" onClick="return false;" style="width: 52%;">Add</button></td>';
	        msgHtml += '</td>';
	        msgHtml += '</tr>';
			$(msgHtml).insertAfter($parentTR);
			 
		     var ctr = $("#dg").find("tbody tr").length;
             for (i = 1; i <= ctr; ++i) {
                var l = $("#dg").find("tbody tr:nth-child(" + i + ") td:first-child");
                $("#dg").find("tbody tr:nth-child(" + (i) + ")").attr('class', "dgdel-" + i);
                $("#dg").find("tbody tr:nth-child(" + (i) + ") button:first-child").attr('id', "dgdel-" + i);
                $("#dg").find("tbody tr:nth-child(" + (i) + ") button:last-child").attr('id', "dgAdd-" + i);
                l.html(i);
            }

             $(document).on('click','.dg-del',function(e) {
             //$(".dg-del").click(function(e) {
         	    var c = $(this).attr("id");
         	    console.log(c);
         	    $("." + c).fadeOut("100", function() {
         	        $(this).remove();
         	        var ctr = $("#dg").find("tbody tr").length;
         	        for (i = 1; i <= ctr; ++i) {
         	            var l = $("#dg").find("tbody tr:nth-child(" + i + ") td:first-child")
         	            $("#dg").find("tbody tr:nth-child(" + (i) + ")").attr('class', "dgdel-" + i);
         	            $("#dg").find("tbody tr:nth-child(" + (i) + ") button").attr('id', "dgdel-" + i);
         	            l.html(i);

         	        }
         	        var totalTmp = 0;
         	        $('input[name="amount[]"]').each(function() {
         	            totalTmp += Number($(this).val().replace(/[^0-9\.]+/g,""));
         	        });
         	        console.log("total:" + totalTmp);
         	        $("#ttval").text(formatExt(totalTmp));
         	        getSizeRowCellValue('.timepicker1');

         	    });

         	});

             $('.timepicker1').on('change',function(e){
         		console.log(rows);
         		getSizeRowCellValue('.timepicker1');
         	});

         	function getSizeRowCellValue(classArray){
        		/**
        		* 1.get size all of table > tr > td 
        		* 2. $('#rval').text($("tr[class^='dgdel-']:last-child").find('td:first-child').text())
        		* 3. for loop and check current classnName[array] have value 
        		* 4. update currentRow if 3 have value.
        		*/
        		var sizeAllCell=0;
        		var time1=0;
        		var currRow=0;
        		sizeAllCell=$("tr[class^='dgdel-']:last-child").find('td:first-child').text();
        		for(var i=0;i<sizeAllCell;++i){
        			time1=$('.dgdel-'+(i+1)).find('input[name="time[]"]').val();
        			if(time1!=''){
        				console.log('time '+(i+1)+':'+time1);
        				currRow++
        				console.log('current row:'+currRow);
        			}
        			$('#rval').text(currRow);
        		}    		
        	}

         	 $("input,select").click(function() {
     	        $('.dg').find('input,select').removeClass('activeDG');
     	        o = $(this).parents("tr").attr('class');
     	        $('.' + o).find('input,select').addClass('activeDG');
     	        $('.' + o).find('option').css('background-color', '#fff');
     	    });

     	    


     	    //check total
     	    $('input[name="amount[]"]').bind("change keyup", function() {
     	        var totalTmp = 0;
     	        $('input[name="amount[]"]').each(function() {
     	            //console.log($(this).val());
     	            totalTmp += Number($(this).val().replace(/[^0-9\.]+/g,""));
     	        });
     	        console.log("totalInner:" + totalTmp);
     	        $("#ttval").text(formatIn(totalTmp));
     	        

     	        if(0!=$(this).val().length){
     				$obj=$(this).parents('tr').attr('class');
     				$('.'+$obj).find('input[name="time[]"]').attr('required','required');
     				$('.'+$obj).find('input[name="branch[]"]').attr('required','required');
     				
     	        }else{
     	        	console.log('no pass');
     	        	$obj=$(this).parents('tr').attr('class');
     	        	//$('.'+$obj).find('input[name="time[]"]').removeAttr('required');
     	        	//$('.'+$obj).find('input[name="branch[]"]').removeAttr('required');
     	        }

     	        
     	    });

     	    $('input[name="branch[]"]').bind("change keyup", function() {
         		if(0!=$(this).val().length){
         			$obj=$(this).parents('tr').attr('class');
         			$('.'+$obj).find('input[name="time[]"]').attr('required','required');
         			$('.'+$obj).find('input[name="amount[]"]').attr('required','required');
         			
                 }else{
                 	console.log('no pass');
                 	$obj=$(this).parents('tr').attr('class');
                 	$('.'+$obj).find('input[name="time[]"]').removeAttr('required');
                 	$('.'+$obj).find('input[name="amount[]"]').removeAttr('required');
                 }
         	});

     	    function formatIn(n) {
     	        return n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
     	    }

     	    var format = function(num){
     			var str = num.toString().replace("", ""), parts = false, output = [], i = 1, formatted = null;
     			if(str.indexOf(".") > 0) {
     				parts = str.split(".");
     				str = parts[0];
     			}
     			str = str.split("").reverse();
     			for(var j = 0, len = str.length; j < len; j++) {
     				if(str[j] != ",") {
     					output.push(str[j]);
     					if(i%3 == 0 && j < (len - 1)) {
     						output.push(",");
     					}
     					i++;
     				}
     			}
     			formatted = output.reverse().join("");
     			return( formatted + ((parts) ? "." + parts[1].substr(0, 2) : ""));
     		};

     		    $(".amount").keyup(function(e){
     		        $(this).val(format($(this).val()));
     		    });



     			$('input[name="time[]"]').bind("   keyup", function() {
     				$(this).val().trim();
     				$(this).val($(this).val().trim());
     				if(0!=$(this).val().length){
     					$obj=$(this).parents('tr').attr('class');
     					console.log('pass new');
     					$('.'+$obj).find('input[name="amount[]"]').attr('required','required');
     					$('.'+$obj).find('input[name="branch[]"]').attr('required','required');
     					
     		        }else{
     		        	console.log('no passnew');
     		        	$obj=$(this).parents('tr').attr('class');
     		        	$('.'+$obj).find('input[name="amount[]"]').removeAttr('required');
     		        	$('.'+$obj).find('input[name="branch[]"]').removeAttr('required');
     		        }
     			});


     			$(".branch").bind("change", function(e){
     			    // access the clipboard using the api
     			    //var pastedData = e.originalEvent.clipboardData.getData('text');
     			    //alert(pastedData);
     			    console.log($(this).val());
     			    var pastedData=$(this).val();
     			    $(this).val(pastedData.trim());
     			});
      		

	}); // end add btn

	$("input,select").click(function() {
	    $('.dg').find('input,select').removeClass('activeDG');
	    o = $(this).parents("tr").attr('class');
	    $('.' + o).find('input,select').addClass('activeDG');
	    $('.' + o).find('option').css('background-color', '#fff');
	});

	//check total
	$('input[name="amount[]"]').bind("change keyup keydown", function(even) {
	    var totalTmp = 0;
	    $('input[name="amount[]"]').each(function() {
	        //console.log($(this).val());
	        totalTmp += Number($(this).val().replace(/[^0-9\.]+/g,""));

	        //<span id="ttval">30</span>
	        //$("#ttval").text(Number($(this).val()));
	    });
	    console.log("totalOut:" + totalTmp);
	    $("#ttval").text(formatExt(totalTmp));

	    if(0!=$(this).val().length){
			$obj=$(this).parents('tr').attr('class');
			$('.'+$obj).find('input[name="time[]"]').attr('required','required');
			$('.'+$obj).find('input[name="branch[]"]').attr('required','required');
			
        }else{
        	console.log('no pass');
        	$obj=$(this).parents('tr').attr('class');
        	$('.'+$obj).find('input[name="time[]"]').removeAttr('required');
        	$('.'+$obj).find('input[name="branch[]"]').removeAttr('required');
        }
	});

	$('input[name="branch[]"]').bind("change keyup", function() {
		if(0!=$(this).val().length){
			$obj=$(this).parents('tr').attr('class');
			$('.'+$obj).find('input[name="time[]"]').attr('required','required');
			$('.'+$obj).find('input[name="amount[]"]').attr('required','required');
			
        }else{
        	console.log('no pass');
        	$obj=$(this).parents('tr').attr('class');
        	$('.'+$obj).find('input[name="time[]"]').removeAttr('required');
        	$('.'+$obj).find('input[name="amount[]"]').removeAttr('required');
        }
	});

	//count row have value
	
	
	$('input[name="time[]"]').bind("   keyup", function() {
		$(this).val().trim();
		$(this).val($(this).val().trim());
		if(0!=$(this).val().length){
			$obj=$(this).parents('tr').attr('class');
			console.log('pass new');
			$('.'+$obj).find('input[name="amount[]"]').attr('required','required');
			$('.'+$obj).find('input[name="branch[]"]').attr('required','required');
			
        }else{
        	console.log('no passnew');
        	$obj=$(this).parents('tr').attr('class');
        	$('.'+$obj).find('input[name="amount[]"]').removeAttr('required');
        	$('.'+$obj).find('input[name="branch[]"]').removeAttr('required');
        }
	});
	
	$('.timepicker1').on('change',function(e){
		console.log(rows);
		getSizeRowCellValue('.timepicker1');
	});

	//$('#rval').text($("tr[class^='dgdel-']:last-child").find('td:first-child').text());
	
	function getSizeRowCellValue(classArray){
		/**
		* 1.get size all of table > tr > td 
		* 2. $('#rval').text($("tr[class^='dgdel-']:last-child").find('td:first-child').text())
		* 3. for loop and check current classnName[array] have value 
		* 4. update currentRow if 3 have value.
		*/
		var sizeAllCell=0;
		var time1=0;
		var currRow=0;
		sizeAllCell=$("tr[class^='dgdel-']:last-child").find('td:first-child').text();
		for(var i=0;i<sizeAllCell;++i){
			time1=$('.dgdel-'+(i+1)).find('input[name="time[]"]').val();
			if(time1!=''){
				console.log('time '+(i+1)+':'+time1);
				currRow++
				console.log('current row:'+currRow);
			}
			$('#rval').text(currRow);
		}
				
		
		
	}

	function formatExt(n) {
		
	    return n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
	}
</script>

	<!--Edit Box-->
	<div id="editBox" class="wrap">
		<form method="post">
			<table>
				<tr>
					<th><h2 id="title">Edit</h2></th>
					<td></td>
				</tr>
				<input type="hidden" id="e-crn" name="crn" value="" />
				<tr>
					<th>ข้อมูลการโอน</th>
					<td></td>
				</tr>
				<tr>
					<th>Customer :</th>
					<td><input id="e-customer" name="customer" /></td>
				</tr>
				<!--<tr><th>Account No. :</th><td><input id="e-acn" name="acn" required="required" maxlength="10"/></td></tr>-->
				<tr>
					<th>Bank Name :</th>
					<td><select name="bid" id="bid">
						<?php
						echo '<option id="e-bid" value="0">-</option>';
						for($i = 0; $i < sizeof ( $banks ); $i ++) {
							echo '<option id="e-bid-' . $i . '" value="' . ($i + 1) . '">' . $banks [$i] . '</option>';
						}
						?>
                        </select></td>
				</tr>
				<tr>
					<th>Branch :</th>
					<td><input id="e-branch" name="branch" /></td>
				</tr>
				<tr>
					<th>Amount :</th>
					<td><input id="e-amount" name="amount"
						onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" /></td>
				</tr>
				<tr>
					<th>Date :</th>
					<td><input class="datepicker" id="e-date" name="date" /></td>
				</tr>
				<tr>
					<th>Time :</th>
					<td><input class="timepicker" id="e-time" name="time" step="1" /></td>
				</tr>
				<tr>
					<th>เข้าบัญชีบริษัท</th>
					<td></td>
				</tr>
				<tr>
					<th>Account :</th>
					<?php
					
					?>
					<td><select id="e-cbid" name="cbid">
						<?php
						
						// reset ( $cBanks );
						// for($i = 0; $i < sizeof ( $cBanks ); $i ++) {
						// echo '<option value="' . key ( $cBanks ) . '">' . current ( $cBanks ) . '</option>';
						// next ( $cBanks );
						// }
						reset ( $cBanks );
						for($i = 0; $i < sizeof ( $cBanks ); $i ++) {
							echo '<option value="' . key ( $cBanks ) . '">' . current ( $cBanks ) . '</option>';
							next ( $cBanks );
						}
						?>
                        </select></td>
				</tr>
				<tr>
					<th>Remark :</th>
					<td><input id="e-remark" name="remark" /></td>
				</tr>
				<input type="hidden" name="edit" value="1" />
				<tr class="confirm">
					<td></td>
					<td><a onclick="edit();">Cancel</a>&emsp;
						<button>Update</button></td>
				</tr>
			</table>
		</form>
	</div>

	<!--Search Box-->
	<div id="searchBox" class="wrap">
		<form method="get">
			<table>
				<tr>
					<th><h2 id="title">Search</h2></th>
					<td></td>
				</tr>
				<tr>
					<th>Search All :</th>
					<td><input name="searchAll" /></td>
				</tr>
				<tr>
					<th>Customer :</th>
					<td><input name="customer" /></td>
				</tr>
				<tr>
					<th>Cash Ref. No. :</th>
					<td><input name="crn" /></td>
				</tr>
				<tr>
					<th>From :</th>
					<td><input id="dateFrom" class="datepicker" name="from" /></td>
				</tr>
				<tr>
					<th>To :</th>
					<td><input id="dateTo" class="datepicker" name="to" /></td>
				</tr>
				<!--<tr><th>Account No. :</th><td><input name="acn"/></td></tr>-->
				<tr>
					<th>Amount :</th>
					<td><input name="amount"
						onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" /></td>
				</tr>
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
				<tr>
					<th>Account :</th>
					<td><select name="cbid">
						<?php
						echo '<option value="">-</option>';
						reset ( $cBanks );
						for($i = 0; $i < sizeof ( $cBanks ); $i ++) {
							echo '<option value="' . key ( $cBanks ) . '">' . current ( $cBanks ) . '</option>';
							next ( $cBanks );
						}
						?>
					</select></td>
				</tr>

				<tr>
					<th>Status :</th>
					<td><select name="status">
							<option value="-">-</option>
							<option value="0" selected>Normal</option>
							<option value="1">Complete</option>
							<option value="2">Cancel</option>
					</select></td>
				</tr>
				
				
				<!-- <tr><th>Add User :</th><td><input name="uid"/></td></tr>-->
				<tr class="confirm">
					<td></td>
					<td><a onclick="searchBox();">Cancel</a>&emsp;
						<button>Search</button></td>
				</tr>
			</table>
		</form>
	</div>

	<?php
	// page underline
	/**
	 * 1.default page =1
	 * 2.
	 * if(page=currentpage not underline
	 */
	if (isset ( $_GET ['page'] )) {
		$curPage = $_GET ['page'] . trim ();
	} else {
		$curPage = 1;
	}
	
	?>


	<div class="paging">
			<?php
			echo 'หน้า&emsp;';
			for($i = 1; $i <= $allPage; $i ++) {
				if ($i == $curPage) {
					echo '<a class="current-page" href="?page=' . $i  . $request . '">' . intval ( $i ) . '</a>';
				} else {
					echo '<a href="?page=' . $i  . $request . '">' . intval ( $i ) . '</a>';
				}
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
			</div>

</body>
</html>

<?php
$con->close ();
?>

<style>
.df {
    display: block;
    height: 500px;
	overflow-y:scroll;
	width: 1000px !important;
}

.df input,.df select {
	width: 25%;
}
</style>


