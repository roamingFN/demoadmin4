<?php
        
        /** Error reporting */
        error_reporting(E_ALL);

        date_default_timezone_set('Europe/London');

        /** PHPExcel */
        require_once '../Classes/PHPExcel.php';

        session_start();
        include '../database.php';
        if (!isset($_SESSION['sql'])) {
            $sql = 'SELECT o.order_id,o.order_number,o.customer_id,o.date_order_created,o.order_status_code,c.customer_firstname,c.customer_lastname,'
                    . 'o.total_shop,o.total_link,o.product_quantity,o.order_price_yuan,o.process_status'
                    . ',COUNT(DISTINCT pt.order_product_tracking_id),COUNT(DISTINCT CASE WHEN pt.statusid=1 THEN pt.order_product_tracking_id END),COUNT(DISTINCT CASE WHEN pt.statusid=0 THEN pt.order_product_tracking_id END),SUM(op.order_shipping_cn_cost)'
                    . ' FROM customer_order o JOIN customer c ON o.customer_id = c.customer_id'
                    . ' LEFT JOIN customer_order_product_tracking pt ON o.order_id = pt.order_id'
                    . ' JOIN customer_order_product op ON o.order_id = op.order_id';
        }
        else {
            $sql = $_SESSION['sql'];
        }

        //set status description
        $_codes = array();
        if($stmt = $con->prepare('SELECT des FROM order_status_code')){
                $stmt->execute();
                $stmt->bind_result($des);
                while($stmt->fetch()){
                        array_push($_codes,$des);
                }
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
                ->setDescription("order confirm")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");
        
        //set header
        $objPHPExcel->getActiveSheet()->getStyle("A4:I4")->getFont()->setBold(true);
        //set number
        $objPHPExcel->getActiveSheet()->getStyle("D")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle("E")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
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
        ->setCellValue('D4', 'ยอดค่าสินค้า')
        ->setCellValue('E4', 'ค่าขนส่ง')
        ->setCellValue('F4', 'สถานะ')
        ->setCellValue('G4', 'จำนวน Tracking')
        ->setCellValue('H4', 'Tracking Complete')
        ->setCellValue('I4', 'Tracking Incomplete');
        
        // Write data from MySQL result
        $orderBy = ' ORDER BY o.order_number DESC';
        $groupBy = ' GROUP BY o.order_id';
        if($stmt = $con->prepare($sql.$groupBy.$orderBy)) {
        $stmt->execute();
		$stmt->bind_result($order_id,$order_number,$customer_id,$datetime,$status,$fname,$lname,$totalShop,$totalLink,$quatity,$price,$processStat,$tracking,$com,$incom,$tran);		
                $i = 5;
                while($stmt->fetch()) {     
                        //formated datetime otime
                        $oDate=substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
                        $oTime=substr($datetime,10,9);
                        
                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $order_number);
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $fname.' '.$lname);
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $oDate.' '.$oTime);
                        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $price);
                        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $tran);
                        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $_codes[$status]);
                        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $tracking);
                        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $com);
                        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $incom);
                        $i++;
                }
        }
        
        // Rename sheet
        echo date('H:i:s') . " Rename sheet\n";
        $objPHPExcel->getActiveSheet()->setTitle('Order_confirm');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Save Excel 2007 file
        echo date('H:i:s') . " Write to Excel2007 format\n";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $strFileName = "order_buy.xlsx";
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
