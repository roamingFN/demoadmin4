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
            $sql = 'SELECT customer.customer_id,customer.customer_firstname,customer.customer_lastname,'
                                        . 'customer_order.order_id,customer_order.order_number,customer_order.order_status_code,customer_order.order_price,customer_order.date_order_paid,'
                                        . 'count(Distinct shop_name),count(customer_order_product.order_id),sum(customer_order_product.quantity)'
                                        . ' FROM customer_order'
                                        . ' join customer on customer.customer_id=customer_order.customer_id'
                                        . ' join customer_order_product on customer_order.order_id=customer_order_product.order_id'
                                        . ' join product on product.product_id = customer_order_product.product_id'
                                        . ' where customer_order.order_status_code=2';
        }
        else {
            $sql = $_SESSION['sql'];
        }
	include 'database.php';
                $groupby = ' GROUP BY customer_order.order_id';
		if($stmt = $con->prepare($sql.$groupby)){
			$stmt->execute();
			$stmt->bind_result($cid,$fname,$lname,$oid,$ono,$status,$price,$datetime,$countShop,$countLink,$countProduct);
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
			header("Content-Disposition: attachment;filename=summary.xls "); // แล้วนี่ก็ชื่อไฟล์
			header("Content-Transfer-Encoding: binary ");

			xlsBOF();
			xlsCodepage();
			xlsWriteLabel(1,0,"Exported Order Summary Data");
			xlsWriteLabel(3,0,iconv("utf-8","tis-620","เลขที่ออเดอร์"));
			xlsWriteLabel(3,1,"Customer");
			xlsWriteLabel(3,2,iconv("utf-8","tis-620","วันที่ลูกค้าแจ้งชำระ"));
			xlsWriteLabel(3,3,iconv("utf-8","tis-620","จำนวนร้านค้า"));
			xlsWriteLabel(3,4,iconv("utf-8","tis-620","จำนวน link"));
                        xlsWriteLabel(3,5,iconv("utf-8","tis-620","จำนวนสินค้า"));
			xlsWriteLabel(3,6,iconv("utf-8","tis-620","ยอดค่าสินค้า(หยวน)"));
			xlsWriteLabel(3,7,iconv("utf-8","tis-620","เลขที่ Confirm"));			
			xlsWriteLabel(3,8,"Status");
			
			$xlsRow = 4;
			while($stmt->fetch()){
                                //formated date
                                $formatted_date = substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
                                //status description
                                if ($status==2) $status_des = "ชำระเงินแล้ว";
             
                                //write
				xlsWriteLabel($xlsRow,0,$oid);
				xlsWriteLabel($xlsRow,1,iconv("utf-8","tis-620",$fname. ' '.$lname));
				xlsWriteLabel($xlsRow,2,$formatted_date);
				xlsWriteLabel($xlsRow,3,$countShop);
				xlsWriteNumber($xlsRow,4,$countLink);
                                xlsWriteLabel($xlsRow,5,$countProduct);
				xlsWriteLabel($xlsRow,6,$price);
				xlsWriteLabel($xlsRow,7,$ono);
				xlsWriteLabel($xlsRow,8,iconv("utf-8","tis-620",$status_des));
				$xlsRow++;
			}
			xlsEOF();
			exit();
		}
	
?>