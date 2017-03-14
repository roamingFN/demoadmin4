<?php
	function xlsBOF() { 
		echo pack("ssssss",0x809,0x8,0x0,0x10,0x0,0x0);  
		return; 
	} 
	
	function xlsCodepage(){
		$record = 0x0042;
		$length = 0x0002;
		$header = pack('vv',$record,$length);
		$data = pack('v', 874);
		echo $header,$data;
	}

	function xlsEOF() { 
		echo pack("ss",0x0A,0x00); 
		return; 
	} 

	function xlsWriteNumber($Row,$Col,$Value) { 
		echo pack("sssss",0x203,14,$Row,$Col,0x0); 
		echo pack("d",$Value); 
		return; 
	} 

	function xlsWriteLabel($Row,$Col,$Value) { 
		$L = strlen($Value); 
		echo pack("ssssss",0x204,8+$L,$Row,$Col,0x0,$L); 
		echo $Value; 
		return; 
	}
	
        session_start();
	include '../database.php';
        if (!isset($_SESSION['sql'])){
            $sql = 'SELECT * FROM customer_address WHERE customer_id='.$_GET['cid'];
        }
        else {
            $sql = $_SESSION['sql'];
        }
	include 'database.php';
	
                
		if($stmt = $con->prepare($sql)){
			$stmt->execute();
			$stmt->bind_result($aid,$cid,$aname,$line,$city,$country,$zipcode,$phone,$other);
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
			header("Content-Disposition: attachment;filename=address.xls "); // แล้วนี่ก็ชื่อไฟล์
			header("Content-Transfer-Encoding: binary ");

			xlsBOF();
			xlsCodepage();
			xlsWriteLabel(1,0,iconv("utf-8","tis-620","ที่อยู่ลูกค้า"));
			xlsWriteLabel(3,0,iconv("utf-8","tis-620","ชื่อที่อยู่"));
			xlsWriteLabel(3,1,iconv("utf-8","tis-620","ที่อยู่"));
			xlsWriteLabel(3,2,iconv("utf-8","tis-620","จังหวัด"));
			xlsWriteLabel(3,3,iconv("utf-8","tis-620","ประเทศ"));
			xlsWriteLabel(3,4,iconv("utf-8","tis-620","รหัสไปรษณีย์"));
			xlsWriteLabel(3,5,iconv("utf-8","tis-620","โทรศัพท์"));
			xlsWriteLabel(3,6,iconv("utf-8","tis-620","อื่นๆ"));
			
			$xlsRow = 4;
			while($stmt->fetch()){
                                //write
				xlsWriteLabel($xlsRow,0,iconv("utf-8","tis-620",$aname));
				xlsWriteLabel($xlsRow,1,iconv("utf-8","tis-620",$line));
				xlsWriteLabel($xlsRow,2,iconv("utf-8","tis-620",$city));
				xlsWriteLabel($xlsRow,3,iconv("utf-8","tis-620",$country));
				xlsWriteLabel($xlsRow,4,iconv("utf-8","tis-620",$zipcode));
				xlsWriteLabel($xlsRow,5,iconv("utf-8","tis-620",$phone));
				xlsWriteLabel($xlsRow,6,iconv("utf-8","tis-620",$other));
				$xlsRow++;
			}
			xlsEOF();
			exit();
		}
	
?>