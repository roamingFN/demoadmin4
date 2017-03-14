<?php
        
        /** Error reporting */
        error_reporting(E_ALL);

        date_default_timezone_set('Europe/London');

        /** PHPExcel */
        require_once '../Classes/PHPExcel.php';

        session_start();
        include '../database.php';
        if (!isset($_SESSION['sql'])){
            $sql = 'SELECT o.order_id,o.order_number,o.customer_id,o.date_order_created,o.order_status_code,c.customer_firstname,c.customer_lastname,'
                        . 'o.total_shop,o.total_link,o.product_quantity,o.order_price_yuan,process_status'
                        . ' FROM customer_order o JOIN customer c'
                        . ' ON o.customer_id = c.customer_id';
        }
        else {
            $sql = $_SESSION['sql'];
        }

        // Create new PHPExcel object
        echo date('H:i:s') . " Create new PHPExcel object\n";
        $objPHPExcel = new PHPExcel();

        // Set properties
        echo date('H:i:s') . " Set properties\n";
        $objPHPExcel->getProperties()->setCreator("China_express")
                ->setLastModifiedBy("China_express")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("order")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");
        
        //set header
        $objPHPExcel->getActiveSheet()->getStyle("A4:I4")->getFont()->setBold(true);
        //set number
        $objPHPExcel->getActiveSheet()->getStyle("G")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        //set auto width
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        foreach(range('A','I') as $columnID) {
             $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Add some data
        echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', 'Exported Order Data')
        ->setCellValue('A4', 'Order Number')
        ->setCellValue('B4', 'Customer')
        ->setCellValue('C4', 'Order Date')
        ->setCellValue('D4', 'จำนวนร้านค้า')
        ->setCellValue('E4', 'จำนวน link')
        ->setCellValue('F4', 'จำนวนสินค้า')
        ->setCellValue('G4', 'ยอดค่าสินค้า')
        ->setCellValue('H4', 'สถานะการสั่งซื้อ')
        ->setCellValue('I4', 'Order Status');
        
        // Write data from MySQL result
        $orderBy = ' ORDER BY o.order_number DESC';
        if($stmt = $con->prepare($sql.$orderBy)) {
        $stmt->execute();
		$stmt->bind_result($order_id,$order_number,$customer_id,$datetime,$status,$fname,$lname,$totalShop,$totalLink,$quatity,$price,$processStat);
			
                $i = 5;
                while($stmt->fetch()) {     
                        //formated datetime otime
                        $oDate=substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
                        $oTime=substr($datetime,10,9);
                        //status description
                        $odesc="";
                        if ($ostatus==0) $odesc="รอตรวจสอบยอด";
                        else if ($status==1) $odesc="ตรวจสอบแล้วรอชำระเงิน";
                        else if ($status==2) $odesc="ชำระเงินแล้ว ดำเนินการสั่งซื้อ";
                        else if ($status==3) $odesc="ร้านค้ากำลังส่งสินค้ามาโกดังจีน";
                        else if ($status==4) $odesc="โกดังจีนรับของแล้ว";
                        else if ($status==5) $odesc="สินค้าอยู่ระหว่างมาไทย";
                        else if ($status==6) $odesc="สินค้าถึงไทยแล้ว";
                        else if ($status==7) $odesc="ชำระค่าขนส่งแล้ว รอจัดส่งสินค้า";
                        else if ($status==8) $odesc="สินค้าจัดส่งให้ลูกค้าแล้ว";
                        else if ($status==9) $odesc="ยกเลิก";
                        //set process_stat
                        if ($processStat==0) $processStatDesc="รอสั่ง";
                        if ($processStat==1) $processStatDesc="กำลังสั่ง";
                        if ($processStat==2) $processStatDesc="สั่งแล้ว";
                        
                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $order_number);
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $fname.' '.$lname);
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $oDate.' '.$oTime);
                        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $totalShop);
                        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $totalLink);
                        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $quatity);
                        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $price);
                        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $processStatDesc);
                        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $odesc);
                        $i++;
                }
        }
        
        // Rename sheet
        echo date('H:i:s') . " Rename sheet\n";
        $objPHPExcel->getActiveSheet()->setTitle('Order');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Save Excel 2007 file
        echo date('H:i:s') . " Write to Excel2007 format\n";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $strFileName = "order.xlsx";
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$strFileName.'"');
        header("Cache-Control: max-age=0");
        // Write file to the browser
        ob_clean();
        $objWriter->save('php://output');
        exit;
        
        // Echo memory peak usage
        //echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r\n";

        // Echo done
        //echo date('H:i:s') . " Done writing file.\r\n"; 
        
        
    ?>
