<?php
		session_start();
		if(!isset($_SESSION['ID'])){
			header("Location: ../login.php");
		}
		
		include '../../database.php';
		include '../function.php';

		if (!isset($_POST['data'])) echo null;
		$result = array();
		$data = json_decode($_POST['data']);

		//get customer code
		$ccode = getCustomerCode($con,$_POST['cid']);

		$rtno = genCRN($con);
		foreach ($data as $opid => $value) {
				//update customer_order_product
				$sql = 'UPDATE customer_order_product SET return_status=2,return_baht=? WHERE order_product_id=?';
				$stmt = $con->prepare($sql);
				$stmt->bind_param('di',$value->returnBaht,$opid);
				$res = $stmt->execute(); 

				//update topup
				$tid = getTopupID($con,$_POST['cid'],$_POST['oid']);
				$sql = 'UPDATE customer_request_topup SET usable_amout=usable_amout+? WHERE topup_id=?';
				$stmt = $con->prepare($sql);
				$stmt->bind_param('di',$value->returnBaht,$tid);
				$res = $stmt->execute();

				//insert return-------------------------- 
				$loss = $value->quan1-$value->quan;
				if ($value->rate==0) {
						$returnYuan = 0;
				}
				else {
						$returnYuan = $value->returnBaht/$value->rate;
				}

				$transport = getTransport($con,$opid);
				$refundSQL = 'INSERT INTO customer_order_return (return_no, return_date, order_product_id, first_unitquantity, quantity, loss_quantity, unitprice, total_yuan, rate, total_baht, return_status, topup_id, order_id, return_type, customer_code, pay_unitprice, pay_transport, transport)'.
				' VALUES (?,now(),?,?,?,?,?,?,?,?,1,?,?,1,?,?,?,?)';
				$stmt = $con->prepare($refundSQL);
				$stmt->bind_param('siiiiddddiisddd',$rtno,$opid,$value->quan1,$value->quan,$loss,$value->price,$returnYuan,$value->rate,$value->returnBaht,$tid,$_POST['oid'],$ccode,$value->price1,$transport['pay_transport'],$transport['transport']);		
				$res = $stmt->execute();

				//update order_product.current_status=98 if this shop has no item
				$shopname = getShopName($con,$opid);
				if (checkOrderStatusInShop($con,$_POST['oid'],$shopname)) {
					$sql = 'UPDATE customer_order_product SET current_status=98 WHERE order_product_id=?';
					$stmt = $con->prepare($sql);
					$stmt->bind_param('i',$opid);
					$res = $stmt->execute();
				}
		}

		//insert Statement
		$refundSQL = 'INSERT INTO customer_statement (customer_id,statement_name,statement_date,debit,credit,order_id) VALUES (?,?,?,?,?,?)';
		$credit = 0;
		$statement_name = 'คืนเงิน - เลขที่ '.$rtno;
		$stmt = $con->prepare($refundSQL);
		$stmt->bind_param('ssssss',$_POST['cid'],$statement_name,date("Y-m-d H:i:s"),$_POST['totalReturn'],$credit,$_POST['oid']); 
		$res = $stmt->execute();
		
		// //update customer
		$sql = 'UPDATE customer SET current_amount=current_amount+? WHERE customer_id=?';
		$stmt = $con->prepare($sql);
		$stmt->bind_param('ss',$_POST['totalReturn'],$_POST['cid']);
		$res = $stmt->execute();

		//insert total message log
		$uid = getuserid($con,$_SESSION['ID']);
        $subject = 'คืนเงินค่าสินค้า รายการสั่งซื้อ '.$rtno;
        $content = 'คืนเงินค่าสินค้า จำนวนเงิน '.number_format($_POST['totalReturn'],2).' บาท รายละเอียดคลิกปุ่มลูกศรที่แถวสินค้า <img src="images/more.png">';
        $sql = 'INSERT INTO total_message_log (order_id,customer_id,user_id,subject,content,message_date,active_link)
        	VALUES (?,?,?,?,?,now(),1)';
        $stmt = $con->prepare($sql);
        $stmt->bind_param('iiiss',$_POST['oid'],$_POST['cid'],$uid,$subject,$content);
        $stmt->execute();

        //update customer_order.flag_return
        $sql = 'UPDATE customer_order SET flag_return=1 WHERE order_id=?';
        $stmt = $con->prepare($sql);
        $stmt->bind_param('i',$_POST['oid']);
        $stmt->execute();

		$con->close();
		echo json_encode($result);
?>