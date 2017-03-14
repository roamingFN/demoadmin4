<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
        /** Error reporting */
        error_reporting(E_ALL);

        date_default_timezone_set('Europe/London');

        require_once '../Classes/PHPExcel.php';

        session_start();
        include '../database.php';
        if (!isset($_SESSION['sql'])){
                $_SESSION['sql'] = 'select co.order_id,co.order_number,co.customer_id,co.order_status_code,'.
                        ' cop.order_shipping_cn_ref_no,cop.order_shipping_cn_m3_size,cop.order_shipping_cn_weight,cop.order_shipping_rate,cop.order_shipping_cn_cost,'.
                        ' cos.order_shipping_th_option,cos.order_shipping_th_ref_no,cos.order_shipping_th_cost,cos.order_shipping_th_date'.
                        ' from customer_order co'.
                        ' join customer_order_product cop on co.order_id=cop.order_id'.
                        ' join customer_order_shipping cos on co.order_id=cos.order_id';
        }
        else {
                $sql = $_SESSION['sql'];
        }

        $objPHPExcel = new PHPExcel();

        // Set properties
        echo date('H:i:s') . " Set properties\n";
        $objPHPExcel->getProperties()->setCreator("China_express")
                ->setLastModifiedBy("China_express")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("portage")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");

        //set header
        $objPHPExcel->getActiveSheet()->getStyle("A4:M4")->getFont()->setBold(true);
        //set number
        $objPHPExcel->getActiveSheet()->getStyle("E")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        //set number
        $objPHPExcel->getActiveSheet()->getStyle("F")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        //set number
        $objPHPExcel->getActiveSheet()->getStyle("G")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        //set number
        $objPHPExcel->getActiveSheet()->getStyle("H")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        //set number
        $objPHPExcel->getActiveSheet()->getStyle("K")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        //set number
        $objPHPExcel->getActiveSheet()->getStyle("M")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        
        //set auto width
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        foreach(range('A','M') as $columnID) {
             $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Add some data
        echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', 'Exported Portage Summary Data')
        ->setCellValue('A4', 'เลขที่ออร์เดอร์')
        ->setCellValue('B4', 'Customer')
        ->setCellValue('C4', 'tracking จีน')
        ->setCellValue('D4', 'สถานะ')
        ->setCellValue('E4', 'M3')
        ->setCellValue('F4', 'น้ำหนัก')
        ->setCellValue('G4', 'เรทค่าขนส่ง')
        ->setCellValue('H4', 'ค่าขนส่งจีน-ไทย')
        ->setCellValue('I4', 'บริการขนส่งในไทย')
        ->setCellValue('J4', 'Tracking ไทย')
        ->setCellValue('K4', 'ค่าขนส่งไทย')
        ->setCellValue('L4', 'วันที่ส่งของ')
        ->setCellValue('M4', 'ยอดรวม');
        
        // Write data from MySQL result
        //get customer name
        $customers = array();
        if($stmt = $con->prepare('SELECT customer_id,customer_firstname,customer_lastname FROM customer')){
                $stmt->execute();
                $stmt->bind_result($c_id,$cfn,$cln);
                while($stmt->fetch()){
                        $customers[$c_id] = $cfn." ".$cln;
                }
        }

        //get status descriuption
        $statdesc = array();
        if($stmt = $con->prepare('SELECT status_id,des FROM order_status_code')) {
                $stmt->execute();
                $stmt->bind_result($sid,$des);
                while($stmt->fetch()) {
                    $statdesc[$sid] = $des;
                }
        }
        
        $groupBy = ' GROUP BY cop.order_shipping_cn_ref_no';
        $orderBy = ' ORDER BY co.order_id';
        if($stmt = $con->prepare($sql.$groupBy.$orderBy)){
                $stmt->execute();
                $stmt->bind_result($oid,$ono,$cid,$status,$refcn,$m3Size,$weight,$shippingRate,$costcn,$option,$refth,$costth,$shippingdate);
                $i = 5;
                while($stmt->fetch()) {
                    //$date = substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
                    //$time = substr($datetime,10,9);
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $ono);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $customers[$cid]);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $refcn);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $statdesc[$status]);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $m3Size);
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $weight);
                    $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $shippingRate);
                    $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $costcn);
                    $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $option);
                    $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $refth);
                    $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $costth);
                    $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $shippingdate);
                    $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $costth+$shippingRate);
                    $i++;
                }
        }
$objPHPExcel->getActiveSheet()->setTitle('Portage');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Save Excel 2007 file
echo date('H:i:s') . " Write to Excel2007 format\n";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$strFileName = "portage.xlsx";
header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="'.$strFileName.'"');
header("Cache-Control: max-age=0");
// Write file to the browser
ob_clean();
$objWriter->save('php://output');
exit;