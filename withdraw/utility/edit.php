<?php
		include '../../database.php';

		//edit
		if(isset($_POST['edit'])) {
                $addDateTime=substr($_POST['datetime'],6,4).'-'.substr($_POST['datetime'],3,2).'-'.substr($_POST['datetime'],0,2). ' '.substr($_POST['datetime'],10,9);
				$stmt = $con->prepare('UPDATE customer_request_withdraw SET customer_id=?,customer_bank_account_id=?,withdraw_amount=?,withdraw_date=?,withdraw_status=?,comment=? WHERE withdraw_request_id=?');
				$stmt->bind_param('sssssss',$_POST['cid'],$_POST['cbid'],$_POST['amount']
                                        ,$addDateTime,$_POST['status'],$_POST['comment'],$_POST['wid']);
				$res = $stmt->execute();
				if(!$res){
						echo '<script>alert("การแก้ไขข้อมูลล้มเหลว");</script>';
				}
                else{
                        echo '<script>alert("แก้ไขข้อมูลสำเร็จ");</script>';
                }
                echo '<script>window.location.href="../withdraw.php"</script>';
		}
?>