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
            $sql = 'SELECT * FROM cash';
        }
        else {
            $sql = $_SESSION['sql'];
        }
	include 'database.php';
	//get bank
	$banks = array();
	if($stmt = $con->prepare('SELECT bname FROM bank')){
		$stmt->execute();
		$stmt->bind_result($bname);
		while($stmt->fetch()){
			array_push($banks,$bname);
		}
        }
        //get cbank
	$cbanks = array();
	if($stmt = $con->prepare('SELECT des FROM co_bank')){
		$stmt->execute();
		$stmt->bind_result($caccount);
		while($stmt->fetch()){
			array_push($cbanks,$caccount);
		}
        }
                
		if($stmt = $con->prepare($sql)) {
			$stmt->execute();
			$stmt->bind_result($cid,$crn,$customer,$date,$time,$amount,$remark,$branch,$bid,$acn,$uid,$ctime,$remarkc,$status,$cbid,$topup_id);
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
			header("Content-Disposition: attachment;filename=cash.xls "); // แล้วนี่ก็ชื่อไฟล์
			header("Content-Transfer-Encoding: binary ");

			xlsBOF();
			xlsCodepage();
			xlsWriteLabel(1,0,"Exported Cash Data");
			xlsWriteLabel(3,0,"Cash Ref. No.");
			xlsWriteLabel(3,1,"Customer");
			xlsWriteLabel(3,2,"Date");
			xlsWriteLabel(3,3,"Time");
			xlsWriteLabel(3,4,"Amount");
                        xlsWriteLabel(3,5,"Bank");
			xlsWriteLabel(3,6,"Branch");
			xlsWriteLabel(3,7,"Remark");			
			xlsWriteLabel(3,8,"Account No.");
			xlsWriteLabel(3,9,"Add User");
			xlsWriteLabel(3,10,"Add Time");
                        xlsWriteLabel(3,11,"Status");
			xlsWriteLabel(3,12,"Remark Cancel");
			
			$xlsRow = 4;
			while($stmt->fetch()){
                                //formated date
                                $formatted_date = substr($date,8,2).'-'.substr($date,5,2).'-'.substr($date,0,4);
                                //formated cdate ctime
                                $addDate=substr($ctime,8,2).'-'.substr($ctime,5,2).'-'.substr($ctime,0,4);
                                $addTime=substr($ctime,10,9);
                                //status description
                                if ($status==0) $status_des = "Normal";
                                else if ($status==1) $status_des = "Complete";
                                else if ($status==2) $status_des = "Cancel";
                                //write
				xlsWriteLabel($xlsRow,0,$crn);
				xlsWriteLabel($xlsRow,1,iconv("utf-8","tis-620",$customer));
				xlsWriteLabel($xlsRow,2,$formatted_date);
				xlsWriteLabel($xlsRow,3,$time);
				xlsWriteNumber($xlsRow,4,$amount);
                                xlsWriteLabel($xlsRow,5,iconv("utf-8","tis-620",$banks[$bid-1]));
				xlsWriteLabel($xlsRow,6,iconv("utf-8","tis-620",$branch));
				xlsWriteLabel($xlsRow,7,iconv("utf-8","tis-620",$remark));
				xlsWriteLabel($xlsRow,8,iconv("utf-8","tis-620",$cbanks[$cbid-1]));
				xlsWriteLabel($xlsRow,9,$uid);
				xlsWriteLabel($xlsRow,10,$addDate." ".$addTime);
                                xlsWriteLabel($xlsRow,11,$status_des);
				xlsWriteLabel($xlsRow,12,iconv("utf-8","tis-620",$remarkc));
				$xlsRow++;
			}
			xlsEOF();
			exit();
		}
	
?>