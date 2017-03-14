<?php
        
        /** Error reporting */
        error_reporting(E_ALL);

        date_default_timezone_set('Europe/London');

        /** PHPExcel */
        require_once '../Classes/PHPExcel.php';

        session_start();
        include '../database.php';
        if (!isset($_SESSION['sql'])){
            $sql = 'SELECT customer.customer_id,customer.customer_firstname,customer.customer_lastname,'
                                        . 'customer_order.order_id,customer_order.order_number,customer_order.order_status_code,customer_order.order_price,customer_order.date_order_paid,'
                                        . 'count(Distinct shop_name),count(customer_order_product.order_id),sum(customer_order_product.quantity)'
                                        . ' FROM customer_order'
                                        . ' join customer on customer.customer_id=customer_order.customer_id'
                                        . ' join customer_order_product on customer_order.order_id=customer_order_product.order_id'
                                        . ' join product on product.product_id = customer_order_product.product_id'
                                        . ' where customer_order.order_status_code=2 or customer_order.order_status_code=3';
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
                ->setDescription("order summary")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");

        $objPHPExcel->getActiveSheet()->getStyle("A4:L4")->getFont()->setBold(true);
        
        // Add some data
        echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', 'Exported Order_summary Data')
        ->setCellValue('A4', 'เลขที่ออเดอร์')
        ->setCellValue('B4', 'Customer')
        ->setCellValue('C4', 'วันที่ลูกค้าแจ้งชำระ')
        ->setCellValue('D4', 'จำนวนร้านค้า')
        ->setCellValue('E4', 'จำนวน link')
        ->setCellValue('F4', 'จำนวนสินค้า')
        ->setCellValue('G4', 'ยอดค่าสินค้า (หยวน)')
        ->setCellValue('H4', 'เลขที่ Confirm')
        ->setCellValue('I4', 'Status');

        // Write data from MySQL result
        $groupBy = ' GROUP BY customer_order.order_id';
        $orderBy = ' ORDER BY customer_order.order_number';
        if($stmt = $con->prepare($sql.$groupBy.$orderBy)){
                $stmt->execute();
		$stmt->bind_result($cid,$fname,$lname,$oid,$ono,$status,$price,$datetime,$countShop,$countLink,$countProduct);
			
                $i = 5;
                while($stmt->fetch()) {                 
                        //formated date
                        $formatted_date = substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
                        //status description
                        if ($status==2) $status_des = "ชำระเงินแล้ว";
                        
                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $ono);
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $fname. ' '.$lname);
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $formatted_date);
                        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $countShop);
                        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $countLink);
                        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $countProduct);
                        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $price);
                        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $ono);
                        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $status_des);
                        $i++;
                }
        }
        
        // Rename sheet
        echo date('H:i:s') . " Rename sheet\n";
        $objPHPExcel->getActiveSheet()->setTitle('summary');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Save Excel 2007 file
        echo date('H:i:s') . " Write to Excel2007 format\n";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $strFileName = "summary.xlsx";
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
