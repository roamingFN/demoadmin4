<?php

function convertOrderStatus($status){
	switch ($status) {
		case '0':
			return "รอตรวจสอบ";
		case '1':
			return "ตรวจสอบแล้ว รอโอนเงิน";
		case '2':
			return "ยกเลิกออเดอร์";
		case '3':
			return "จ่ายค่าสินค้า และค่าขนส่งในจีนเรียบร้อย";
		case '4':
			return "จ่ายค่าขนส่งจีน-ไทย และค่าขนส่งในไทยเรียบร้อย";
		case '5':
			return "สินค้าอยู่ในโกดัง เตรียมจัดส่ง";
		case '6':
			return "จัดส่งสินค้าเรียบร้อย";	
		default:
			return $status;
	}
}
function convertPaymentStatus($status){
	switch ($status) {
		case '0':
			return "รอตรวจสอบยอด";
		case '1':
			return "ยอดไม่พอ";
		case '2':
			return "ดำเนินการแล้ว";
		default:
			return $status;
	}
}

function convertRequestType($status){
	switch ($status) {
		case '1':
			return "ค่าสินค้า";
		case '2':
			return "ค่าขนส่ง";
		default:
			return $status;
	}
}

function convertTopupStatus($status){
	switch ($status) {
		case '0':
			return "รอตรวจสอบ";
		case '1':
			return "ยกเลิก";
		case '2':
			return "ตรวจสอบแล้ว";
		default:
			return $status;
	}
}

function formatBankAccNo($acc_no){
		$acc_no = substr_replace($acc_no, '-', 3, 0);
		$acc_no = substr_replace($acc_no, '-', 5, 0);
		$acc_no = substr_replace($acc_no, '-', 11, 0);
		return $acc_no;
	}

function file_newname($path, $filename){
    if ($pos = strrpos($filename, '.')) {
           $name = substr($filename, 0, $pos);
           $ext = substr($filename, $pos);
    } else {
           $name = $filename;
    }

    $newpath = $path.'/'.$filename;
    $newname = $filename;
    $counter = 0;
    while (file_exists($newpath)) {
           $newname = $name .'_'. $counter . $ext;
           $newpath = $path.'/'.$newname;
           $counter++;
     }

    return $newpath;
}

?>