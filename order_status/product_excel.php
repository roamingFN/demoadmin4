<?php
        
        /** Error reporting */
        error_reporting(E_ALL);

        date_default_timezone_set("Asia/Bangkok");

        /** PHPExcel */
        require_once '../Classes/PHPExcel.php';

        session_start();
        include '../database.php';
        include './utility/function.php';
        if (isset($_GET['order_id'])) {
                $sql = 'SELECT p.product_name,p.product_size,p.product_color,pt.producttypename,op.first_unitquantity,op.quantity,op.first_unitprice,op.unitprice,op.order_shipping_cn_cost,op.order_product_totalprice,op.order_status,r.remark_tha,o.order_rate,o.order_number
                FROM customer_order_product op JOIN product p ON op.product_id=p.product_id
                LEFT JOIN product_type pt ON op.producttypeid=pt.producttypeid
                JOIN order_remark r ON op.remark_id=r.remark_id
                JOIN customer_order o ON o.order_id=op.order_id';
                $condition = ' WHERE op.order_id='.$_GET['order_id'].' AND op.chkflg=1';
        }
        else {
                return;
        }

        // Create new PHPExcel object
        echo date('H:i:s') . " Create new PHPExcel object\n";
        $objPHPExcel = new PHPExcel();

        // Set properties
        echo date('H:i:s') . " Set properties\n";
        $objPHPExcel->getProperties()->setCreator("Order2Easy")
                ->setLastModifiedBy("Order2Easy")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("order product confirm")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");
        
        //set header
        $objPHPExcel->getActiveSheet()->getStyle("A4:M4")->getFont()->setBold(true);
        
        //set number
        $objPHPExcel->getActiveSheet()->getStyle("G")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle("H")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle("I")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle("J")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle("K")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        
        //set auto width
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        foreach(range('A','M') as $columnID) {
             $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Add some data
        echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', 'Exported Order Product Data')
        ->setCellValue('A4', 'ชื่อสินค้า')
        ->setCellValue('B4', 'ขนาด')
        ->setCellValue('C4', 'สี')
        ->setCellValue('D4', 'ประเภทสินค้า')
        ->setCellValue('E4', 'จำนวนที่ลูกค้าสั่ง')
        ->setCellValue('F4', 'จำนวนที่สั่งได้')
        ->setCellValue('G4', 'ราคา/ชิ้น (หยวน)')
        ->setCellValue('H4', 'ราคาแก้ไข (หยวน)')
        ->setCellValue('I4', 'ค่าขนส่งในจีน (หยวน)')
        ->setCellValue('J4', 'รวม (หยวน)')
        ->setCellValue('K4', 'รวม (บาท)')
        ->setCellValue('L4', 'สถานะการสั่ง')
        ->setCellValue('M4', 'หมายเหตุ');

        // Write data from MySQL result
        $i = 5;    
        $orderBy = ' ORDER BY op.order_product_id DESC';
        $dataSet = getData($con,$sql,$condition,$orderBy,'','');
        $ono = $dataSet[0]['order_number'];
        foreach ($dataSet as $key => $value) {
                $order_status = '';
                if ($value['order_status']==1) $orderStatDesc = 'ได้';
                if ($value['order_status']==2) $orderStatDesc = 'ไม่ได้';
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $value['product_name']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $value['product_size']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $value['product_color']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $value['producttypename']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $value['first_unitquantity']);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $value['quantity']);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $value['first_unitprice']);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $value['unitprice']);
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $value['order_shipping_cn_cost']);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $value['order_product_totalprice']/$value['order_rate']);
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $value['order_product_totalprice']);
                $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $orderStatDesc);
                $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $value['remark_tha']);
                $i++;
        }
        
        // Rename sheet
        echo date('H:i:s') . " Rename sheet\n";
        $objPHPExcel->getActiveSheet()->setTitle('Order_product_confirm');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Save Excel 2007 file
        echo date('H:i:s') . " Write to Excel2007 format\n";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $strFileName = "รอตรวจสอบ_".$ono.".xlsx";
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
