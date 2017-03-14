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
            $sql = 'SELECT o.order_id,o.customer_id,o.date_order_created,o.order_status_code,c.customer_firstname,c.customer_lastname'
                        . ' FROM customer_order o JOIN customer c'
                        . ' ON o.customer_id = c.customer_id';
        }
        else {
            $sql = $_SESSION['sql'];
        }
	include 'database.php';
		if($stmt = $con->prepare($sql)){
			$stmt->execute();
			$stmt->bind_result($oid,$cid,$odate,$ostatus,$ofname,$olname);
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
			header("Content-Disposition: attachment;filename=order.xls "); // แล้วนี่ก็ชื่อไฟล์
			header("Content-Transfer-Encoding: binary ");

			xlsBOF();
			xlsCodepage();
			xlsWriteLabel(1,0,"Exported Order Data");
			xlsWriteLabel(3,0,"Order No.");
			xlsWriteLabel(3,1,"Customer name");
			xlsWriteLabel(3,2,"Order Date");
			xlsWriteLabel(3,3,"Order Status");
			
			$xlsRow = 4;
			while($stmt->fetch()) {
                                //formated odate otime
                                $oDate=substr($otime,8,2).'-'.substr($otime,5,2).'-'.substr($otime,0,4);
                                $oTime=substr($otime,10,9);
                                //status description
                                $odesc="";
                                if ($ostatus==0) $$odesc="รอตรวจสอบยอด";
                                else if ($ostatus==1) $$odesc="ตรวจสอบแล้วรอชำระเงิน";
                                else if ($ostatus==2) $$odesc="ชำระเงินแล้ว ดำเนินการสั่งซื้อ";
                                else if ($ostatus==3) $$odesc="ร้านค้ากำลังส่งสินค้ามาโกดังจีน";
                                else if ($ostatus==4) $odesc="โกดังจีนรับของแล้ว";
                                else if ($ostatus==5) $odesc="สินค้าอยู่ระหว่างมาไทย";
                                else if ($ostatus==6) $odesc="สินค้าถึงไทยแล้ว";
                                else if ($ostatus==7) $odesc="ชำระค่าขนส่งแล้ว รอจัดส่งสินค้า";
                                else if ($ostatus==8) $odesc="สินค้าจัดส่งให้ลูกค้าแล้ว";
                                else if ($ostatus==9) $odesc="ยกเลิก";
                                
				xlsWriteLabel($xlsRow,0,$oid);
				xlsWriteLabel($xlsRow,1,iconv("utf-8","tis-620",$ofname.' '.$olname));
				xlsWriteLabel($xlsRow,2,$oDate.' '.$oTime);
				xlsWriteLabel($xlsRow,3,iconv("utf-8","tis-620",$odesc));
				$xlsRow++;
			}
			xlsEOF();
			exit();
		}
?>