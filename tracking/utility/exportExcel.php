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
                $sql = 'SELECT pt.tracking_no, pt.order_product_tracking_id, pt.m3, pt.weight, pt.type, pt.rate, pt.statusid as tstatusid, pt.uid, pt.last_edit_date,o.order_id, o.order_number, o.date_order_created, o.remark, c.customer_id, c.customer_code,c.customer_firstname, c.customer_lastname,p.statusid as pstatusid
                FROM customer_order_product_tracking pt JOIN customer_order o ON pt.order_id=o.order_id 
                JOIN customer c ON o.customer_id=c.customer_id
                JOIN package p ON p.packageid=pt.packageid';
                $condition = '';
                $orderBy = '';
        }
        else {
                $sql = $_SESSION['sql'];
                $condition = $_SESSION['condition'];
                $orderBy = $_SESSION['orderBy'];
        }
        
        $_pStatDesc = getPackageStatInfo($con);

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
        $objPHPExcel->getActiveSheet()->getStyle("G")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle("H")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle("J")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle("K")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $objPHPExcel->getActiveSheet()->getStyle("N")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        
        //set auto width
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        foreach(range('A','P') as $columnID) {
             $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Add some data
        echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', 'Exported Order Tracking')
        ->setCellValue('A4', 'Tracking no.')
        ->setCellValue('B4', 'เลขที่ Order')
        ->setCellValue('C4', 'วันที่ Order')
        ->setCellValue('D4', 'วันที่ถึงไทย')
        ->setCellValue('E4', 'ชื่อลูกค้า')
        ->setCellValue('F4', 'รหัสลูกค้า')
        ->setCellValue('G4', 'คิว')
        ->setCellValue('H4', 'Kg')
        ->setCellValue('I4', 'Type')
        ->setCellValue('J4', 'Rate')
        ->setCellValue('K4', 'ค่าขนส่งจีนไทย')
        ->setCellValue('L4', 'สถานะ Tracking')
        ->setCellValue('M4', 'สถานะกล่อง')
        ->setCellValue('N4', 'ค่าเฉลี่ย')
        ->setCellValue('O4', 'ผู้ตรวจ')
        ->setCellValue('P4', 'หมายเหตุ');
        
        // Write data from MySQL result
        $i = 5;
        $dataSet = getData($con,$sql,$condition,$orderBy,'','');
        foreach ($dataSet as $key => $value) {
                $oid = $value['order_id'];
                $m3 = $value['m3'];
                $kg = $value['weight'];
                $rate = $value['rate'];
                $ptid = $value['order_product_tracking_id'];
                if ($value['type']==1) {        //m3
                        $typeDesc = 'คิว';
                        $tran = $m3*$rate;
                }
                else if ($value['type']==2) {       //kg
                        $typeDesc = 'Kg';
                        $tran = $kg*$rate;
                }
                
                if ($value['tstatusid']==0) {
                        $statDesc = 'incomplete';
                }
                else if ($value['tstatusid']==1) {
                        $statDesc = 'complete';
                }
                if ($m3==0) {
                        $avg=0;
                }
                else {
                        $avg = $tran/$m3;
                }
                if($value['date_order_created']=='' || $value['date_order_created']=='0000-00-00 00:00:00') $odt = '';
                else $odt = date_format(date_create($value['date_order_created']),"d/m/Y H:i:s");                        
                if($value['last_edit_date']=='' || $value['last_edit_date']=='0000-00-00 00:00:00') $dt = '';
                else $dt = date_format(date_create($value['last_edit_date']),"d/m/Y H:i:s");

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $value['tracking_no']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $value['order_number']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $odt);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $dt);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $value['customer_firstname'].' '.$value['customer_lastname']);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $value['customer_code']);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $value['m3']);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $value['weight']);
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $typeDesc);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $value['rate']);
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $tran);
                $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $statDesc);
                $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $_pStatDesc[$value['pstatusid']]);
                $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $avg);
                $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $value['uid']);
                $objPHPExcel->getActiveSheet()->setCellValue('P' . $i, $value['remark']);
                $i++;
        }
        
        // Rename sheet
        echo date('H:i:s') . " Rename sheet\n";
        $objPHPExcel->getActiveSheet()->setTitle('Order_Tracking');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Save Excel 2007 file
        echo date('H:i:s') . " Write to Excel2007 format\n";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $strFileName = "order_tracking.xlsx";
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$strFileName.'"');
        header("Cache-Control: max-age=0");

        // Write file to the browser
        ob_clean();
        $objWriter->save('php://output');
        exit;
        
?>
