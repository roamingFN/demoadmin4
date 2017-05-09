<?php
	session_start();
	if(!isset($_SESSION['ID'])){
		header("Location: ../login.php");
	}
	
	include '../../../database.php';
	include './function.php';

	if (!isset($_POST['data'])) echo null;
	$result = array();
	$data = json_decode($_POST['data']);
	
	//get customer code, return code
	$oid = $_POST['oid'];
	$ccode = getCustomerCode($con,$_POST['cid']);
	$rtno = genCRN($con);

	foreach ($data as $opid => $value) {
			//update customer_order_product
			// $sql = 'UPDATE customer_order_product SET return_status=2,return_baht=? WHERE order_product_id=?';
			// $stmt = $con->prepare($sql);
			// $stmt->bind_param('di',$value->returnBaht,$opid);
			// $res = $stmt->execute(); 

			//update topup
			$tid = getTopupID($con,$_POST['cid'],$_POST['oid']);
			$sql = 'UPDATE customer_request_topup SET usable_amout=usable_amout+? WHERE topup_id=?';
			$stmt = $con->prepare($sql);
			$stmt->bind_param('di',$_POST['return_yuan2'],$tid);
			$res = $stmt->execute();

			//cal 
			$loss = $value->backshop_quan-$value->return_quan;
			$return_yuan = $value->return_quan*$value->backshop_price;
			$return_baht = $return_yuan*$value->rate;

			//sql insert
			$refundSQL = 'INSERT INTO customer_order_return_summary (return_date
																,return_status
																,return_type
																,return_no
																,order_product_id
																,first_unitquantity
																,quantity
																,loss_quantity
																,unitprice
																,total_yuan
																,rate
																,total_baht
																,topup_id
																,order_id
																,customer_code)'.
			' VALUES (now(),1,2,?,?,?,?,?,?,?,?,?,?,?,?)';
			$stmt = $con->prepare($refundSQL);
			$stmt->bind_param('siiiiddddiis', $rtno, $opid, $value->backshop_quan, $loss, $value->return_quan, $value->backshop_price, $return_yuan ,$value->rate,$return_baht,$tid,$oid,$ccode);		
			$res = $stmt->execute();
	}

	//insert Statement
	$refundSQL = 'INSERT INTO customer_statement (customer_id,statement_name,statement_date,debit,credit,order_id) VALUES (?,?,?,?,?,?)';
	$credit = 0;
	$date = date("Y-m-d H:i:s");
	$statement_name = 'คืนเงิน - เลขที่ '.$rtno;
	$stmt = $con->prepare($refundSQL);
	$stmt->bind_param('ssssss',$_POST['cid'],$statement_name,$date,$_POST['return_yuan2'],$credit,$_POST['oid']); 
	$res = $stmt->execute();
	
	//update customer
	$sql = 'UPDATE customer SET current_amount=current_amount+? WHERE customer_id=?';
	$stmt = $con->prepare($sql);
	$stmt->bind_param('ss',$_POST['return_yuan2'],$_POST['cid']);
	$res = $stmt->execute();

	//insert total message log
	$uid = getuserid($con,$_SESSION['ID']);
    $subject = 'คืนเงินค่าสินค้า รายการสั่งซื้อ '.$rtno;
    $content = 'คืนเงินค่าสินค้า จำนวนเงิน '.number_format($_POST['return_yuan2'],2).' บาท รายละเอียดคลิกปุ่มลูกศรที่แถวสินค้า <img src="images/more.png">';
    $sql = 'INSERT INTO total_message_log (order_id,customer_id,user_id,subject,content,message_date,active_link)
    	VALUES (?,?,?,?,?,now(),1)';
    $stmt = $con->prepare($sql);
    $stmt->bind_param('iiiss',$_POST['oid'],$_POST['cid'],$uid,$subject,$content);
    $stmt->execute();

    //update customer_order.flag_return
    $sql = 'UPDATE customer_order SET summary_return_flag=1 WHERE order_id=?';
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i',$_POST['oid']);
    $stmt->execute();

	$con->close();
	echo json_encode($result);
?>