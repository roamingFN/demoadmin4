<?php
// $sendto = 'pratchayac@hotmail.com';
$sendto = 'kukeit.c@gmail.com';
$headers = "From: support@order2easy.com\r\n";
$headers .= "X-Mailer: PHP/" . phpversion () . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/plain; charset=utf-8\r\n";
$headers .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$subject = 'test';
$subject = "=?UTF-8?B?" . base64_encode ( $subject ) . "?=";
$mailmsg = 'test2';

$sendmail = mail ( $sendto, $subject, $mailmsg, $headers );
if ($sendmail) {
	echo "ส่งข้อความเรียบร้อยแล้ว";
} else {
	echo "ผิดพลาด, ไม่สามารถส่งข้อความผ่านเว็บไซท์ได้ โปรดใช้วิธีส่งผ่านอีเมลของท่าน";
}
?>