<?php
        
        /** Error reporting */
        error_reporting(E_ALL);

        date_default_timezone_set("Asia/Bangkok");

        /** PHPExcel */
        require_once '../../Classes/PHPExcel.php';
        require_once './function.php';
        include '../../database.php';

        session_start();
        if (!isset($_SESSION['sql'])) {
                $sql = 'SELECT c.customer_firstname, c.customer_lastname, os.total_tracking, os.total_tracking_in_package, os.total_count_confirmed, os.total_count_backshop, os.total_received, os.total_price_payment, os.total_price_backshop, os.total_price_received, os.total_return_product1, os.total_return_product2, os.total_return, os.remark, o.order_id, o.order_number, o.customer_id
                FROM customer_order_summary os JOIN customer_order o ON os.order_id=o.order_id
                JOIN customer c ON o.customer_id = c.customer_id';
                $condition = '';
                $orderBy = '';
        }
        else {
                $sql = $_SESSION['sql'];
                $condition = $_SESSION['condition'];
                $orderBy = $_SESSION['orderBy'];
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
        
        //set header
        $objPHPExcel->getActiveSheet()->getStyle("A4:P4")->getFont()->setBold(true);

        //set number
        $objPHPExcel->getActiveSheet()->getStyle("J")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle("K")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle("L")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle("M")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle("N")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle("O")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        //set auto width
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        foreach(range('A','P') as $columnID) {
             $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Add some data
        echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', 'Exported Order Summary')
        ->setCellValue('A4', 'Order Number')
        ->setCellValue('B4', 'Customer Name')
        ->setCellValue('C4', 'Customer ID')
        ->setCellValue('D4', 'จำนวน Tracking')
        ->setCellValue('E4', 'จำนวน Tracking ที่ปิดกล่องแล้ว')
        ->setCellValue('F4', 'จำนวนที่ลูกค้าสั่ง (ชิ้น)')
        ->setCellValue('G4', 'จำนวนที่ร้านค้า Confirm (ชิ้น)')
        ->setCellValue('H4', 'จำนวนที่ถึงโกดังไทย (ชิ้น)')
        ->setCellValue('I4', 'จำนวนที่ขาด (ชิ้น)')
        ->setCellValue('J4', 'ยอดที่ลูกค้าโอน (บาท)')
        ->setCellValue('K4', 'ยอดที่ร้านค้า confirm (บาท)')
        ->setCellValue('L4', 'ยอดค่าสินค้าที่ถึงโกดังไทย (บาท)')
        ->setCellValue('M4', 'ยอดคืนเงินครั้งที่ 1')
        ->setCellValue('N4', 'ยอดค่าสินค้าที่ขาด (บาท)')
        ->setCellValue('O4', 'ยอดคืนแล้ว')
        ->setCellValue('P4', 'หมายเหตุ');
        
        // Write data from MySQL result
        $i = 5;
        $dataSet = getData($con,$sql,$condition,$orderBy,'','');
        foreach ($dataSet as $key => $value) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $value['order_number']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $value['customer_firstname'].' '.$value['customer_lastname']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $value['customer_id']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $value['total_tracking']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $value['total_tracking_in_package']);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $value['total_count_confirmed']);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $value['total_count_backshop']);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $value['total_received']);
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $value['total_count_backshop']-$value['total_received']);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $value['total_price_payment']);
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $value['total_price_backshop']);
                $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $value['total_price_received']);
                $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $value['total_return_product1']);
                $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $value['total_return_product2']);
                $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $value['total_return']);
                $objPHPExcel->getActiveSheet()->setCellValue('P' . $i, $value['remark']);
                $i++;
        }
        
        // Rename sheet
        echo date('H:i:s') . " Rename sheet\n";
        $objPHPExcel->getActiveSheet()->setTitle('Order_Summary');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Save Excel 2007 file
        echo date('H:i:s') . " Write to Excel2007 format\n";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $strFileName = "Order_Summary.xlsx";
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$strFileName.'"');
        header("Cache-Control: max-age=0");

        // Write file to the browser
        ob_clean();
        $objWriter->save('php://output');
        exit;
        
?>
