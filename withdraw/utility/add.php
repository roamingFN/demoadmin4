<?php
		include '../../database.php';
		include './function.php';

		//add
        if(isset($_POST['add'])) {
                $wno = genWithdrawNumber($con);
                echo $wno;
                //formated datetime 'yyyy-mm-dd h:m:s'
                $addDateTime=substr($_POST['datetime'],6,4).'-'.substr($_POST['datetime'],3,2).'-'.substr($_POST['datetime'],0,2). ' '.substr($_POST['datetime'],10,9);
				//echo $addDateTime;
				$stmt = $con->prepare('INSERT INTO customer_request_withdraw(customer_id,customer_bank_account_id,withdraw_amount,withdraw_date,withdraw_status,comment,withdraw_number) VALUES(?,?,?,?,?,?,?)');
				$stmt->bind_param('sssssss',$_POST['cid'],$_POST['cbid'],$_POST['amount'],$addDateTime,$_POST['status'],$_POST['comment'],$wno);
				$res = $stmt->execute();
				if(!$res) {
						echo '<script>alert("การเพิ่มข้อมูลล้มเหลว");</script>';
				}
                else {
                        echo '<script>alert("เพิ่มข้อมูลสำเร็จ");</script>';
                }
                //echo $_POST['cid'].' '.$_POST['cbid'].' '.$_POST['amount'].' '.$addDateTime.' '.$_POST['status'].' '.$_POST['comment'];

                echo '<script>window.location.href="../withdraw.php"</script>';
       	}
?>