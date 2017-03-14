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
	include 'database.php';
        if (!isset($_SESSION['sql'])){
            $sql = 'SELECT * FROM customer_request_withdraw';
        }
        else {
            $sql = $_SESSION['sql'];
        }
        //echo $sql;
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
        $cbanks = array();
        if($stmt = $con->prepare('SELECT bank_account_id,bank_name,account_no,account_name FROM customer_bank_account')){
                $stmt->execute();
                $stmt->bind_result($b_id,$bname,$accnum,$accname);
                while($stmt->fetch()){
                        $cbanks[$b_id] = $bname." ".$accnum." ".$accname;                                              
                }
        }
        
	if($stmt = $con->prepare($sql)){
		$stmt->execute();
		$stmt->bind_result($wid,$cid,$bid,$amount,$datetime,$status,$comment);
			
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
		header("Content-Disposition: attachment;filename=withdraw.xls"); // แล้วนี่ก็ชื่อไฟล์
		header("Content-Transfer-Encoding: binary ");

		xlsBOF();
		xlsCodepage();
		xlsWriteLabel(1,0,"Exported Withdraw Data");
		xlsWriteLabel(3,0,"Date");
		xlsWriteLabel(3,1,"Amount");
		xlsWriteLabel(3,2,"Bank Name");
		xlsWriteLabel(3,3,"Account Number");
                xlsWriteLabel(3,4,"Account Name");
                xlsWriteLabel(3,5,"Remark");
                xlsWriteLabel(3,6,"Status");
			
		$xlsRow = 4;
		while($stmt->fetch()) {
                        $bif = explode(" ", $cbanks[$bid]);
                        $date = substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
			$time = substr($datetime,10,9);
                        if ($status==0) $statdesc="รอโอน";
                        else if ($status==1) $statdesc="โอนแล้ว";
                        
                        xlsWriteLabel($xlsRow,0,$date);
                        xlsWriteNumber($xlsRow,1,$amount);
                        xlsWriteLabel($xlsRow,2,iconv("utf-8","tis-620",$bif[0]));
                        xlsWriteLabel($xlsRow,3,$bif[1]);
                        xlsWriteLabel($xlsRow,4,iconv("utf-8","tis-620",$bif[2]. ' ' . $bif[3]));
                        xlsWriteLabel($xlsRow,5,iconv("utf-8","tis-620",$comment));
                        xlsWriteLabel($xlsRow,6,iconv("utf-8","tis-620",$statdesc));
                        $xlsRow++;
		}
		xlsEOF();
		exit();
		}
?>