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
	
	function xlsWriteHeader($Row,$Col,$Value) { 
		$L = strlen($Value); 
		echo pack("ssssss",0x204,8+$L,$Row,$Col,0x02BC,$L); 
		echo $Value; 
		return; 
	}
	
        session_start();
	include 'database.php';
        if (!isset($_SESSION['sql'])){
            $sql = 'SELECT * FROM customer_request_topup';
        }
        else {
            $sql = $_SESSION['sql'];
        }
        
        include 'database.php';
        //get customer name
        $customer = array();
	if($stmt = $con->prepare('SELECT customer_id,customer_firstname,customer_lastname FROM customer')){
                $stmt->execute();
		$stmt->bind_result($c_id,$cfn,$cln);
		while($stmt->fetch()){
			$customers[$c_id] = $cfn." ".$cln;
		}
	}

        //get bank payment
        $banks = array();
        if($stmt = $con->prepare('SELECT bank_id,bank_name_en,account_no FROM bank_payment')){
		$stmt->execute();
		$stmt->bind_result($b_id,$cif,$acn);
		while($stmt->fetch()){
			$banks[$b_id] = $cif." ".$acn;                                              
		}
	}
        
	if($stmt = $con->prepare($sql)){
		$stmt->execute();
		$stmt->bind_result($tid,$tno,$cid,$bid,$amount,$status,$datetime,$tran_method,$bill,$note,$comment,$remarkc);
			
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
		header("Content-Disposition: attachment;filename=topup.xls "); // แล้วนี่ก็ชื่อไฟล์
		header("Content-Transfer-Encoding: binary ");

		xlsBOF();
		xlsCodepage();
		xlsWriteHeader(1,0,"Exported Topup Data");
		xlsWriteHeader(3,0,"Customer");
		xlsWriteHeader(3,1,"Date");
		xlsWriteHeader(3,2,"Time");
		xlsWriteHeader(3,3,"Amount");
                xlsWriteHeader(3,4,"Branch");
                xlsWriteHeader(3,5,"Remark");
                xlsWriteHeader(3,6,"Bank name");
                xlsWriteHeader(3,7,"Account no.");
                xlsWriteHeader(3,8,"Topup no.");
                xlsWriteHeader(3,9,"Status");
			
		$xlsRow = 4;
		while($stmt->fetch()) {
                        //desc
                        $bif = explode(" ", $banks[$bid]);
                        $date = substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
			$time = substr($datetime,10,9);
                        if ($status==0) $statdesc="OK";
                        else if ($status==2) $statdesc="Cancel";
                        
                        xlsWriteLabel($xlsRow,0,iconv("utf-8","tis-620",$customers[$cid]));
                        xlsWriteLabel($xlsRow,1,$date);
                        xlsWriteLabel($xlsRow,2,$time);
                        xlsWriteNumber($xlsRow,3,$amount);
                        xlsWriteLabel($xlsRow,4,iconv("utf-8","tis-620",$tran_method));
                        xlsWriteLabel($xlsRow,5,iconv("utf-8","tis-620",$note));
                        xlsWriteLabel($xlsRow,6,iconv("utf-8","tis-620",$bif[0]));
                        xlsWriteLabel($xlsRow,7,iconv("utf-8","tis-620",$bif[1]));
                        xlsWriteLabel($xlsRow,8,$tno);
                        xlsWriteLabel($xlsRow,9,$statdesc);
                        $xlsRow++;
		}
		xlsEOF();
		exit();
		}
?>