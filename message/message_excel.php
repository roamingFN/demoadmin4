<?php
        
        /** Error reporting */
        error_reporting(E_ALL);

        date_default_timezone_set('Europe/London');

        /** PHPExcel */
        require_once '../Classes/PHPExcel.php';

        session_start();
        include '../database.php';
 
        $sql = 'select co.order_number, pk.packageno, cs.customer_code,  tm.message_date , tm.subject, tm.active_link from customer cs right join (select tml1.eid, tml1.order_id, tml1.customer_id, tml1.subject, tml1.content, tml1.message_date , tml1.packageid, tml1.active_link from total_message_log tml1 where tml1.message_date = (select max(tml2.message_date) from total_message_log tml2 where tml2.order_id = tml1.order_id) group by order_id  order by tml1.eid  desc) tm on cs.customer_id = tm.customer_id left join package pk on pk.packageid = tm.packageid left join customer_order co on co.order_id = tm.order_id
where  tm.message_date >= date_sub(curdate(), interval 2 month) group by tm.eid
order by tm.message_date desc';

        // Create new PHPExcel object
        echo date('H:i:s') . " Create new PHPExcel object\n";
        $objPHPExcel = new PHPExcel();

        // Set properties
        echo date('H:i:s') . " Set properties\n";
        $objPHPExcel->getProperties()->setCreator("China_express")
                ->setLastModifiedBy("China_express")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Transport")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");
        
        //set header
        $objPHPExcel->getActiveSheet()->getStyle("A4:N4")->getFont()->setBold(true);
        //set number
        $objPHPExcel->getActiveSheet()->getStyle("D")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        //set auto width
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        foreach(range('A','N') as $columnID) {
             $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Add some data
        echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', 'Exported Message Data')
        ->setCellValue('A4', 'ลำดับที่')
        ->setCellValue('B4', 'เลขที่ order')
        ->setCellValue('C4', 'เลขที่ package')
        ->setCellValue('D4', 'รหัสลูกค้า')
        ->setCellValue('E4', 'วันที่ส่ง')
        ->setCellValue('F4', 'ข้อความ')
        ->setCellValue('G4', 'สถานะ');
        //echo $sql;
        // Write data from MySQL result
        if($stmt = $con->prepare($sql)){
                $stmt->execute();
		$stmt->bind_result(
				$order_number,
				$packageno,
				$customer_code,
				$message_date,
				$subject,
				$active_link);
	
			
                $i = 5;
                $j = 1;
                while($stmt->fetch()) {                 
                        //formated date
//         echo $packageno;
                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $j);
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, (!empty($order_number)?$order_number:'-'));
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, (!empty($packageno)?$packageno:'-'));
                        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $customer_code);
                        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $message_date);
                        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $subject);
                        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, ($active_link == '0')?'ตอบแล้ว':'ยังไม่ได้ตอบ');
                         $i++;
                         $j++;
                }
        }
        
        
        echo date('H:i:s') . " Rename sheet\n";
        $objPHPExcel->getActiveSheet()->setTitle('Transport');
        $objPHPExcel->setActiveSheetIndex(0);

        echo date('H:i:s') . " Write to Excel2007 format\n";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $strFileName = "message.xlsx";
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$strFileName.'"');
        header("Cache-Control: max-age=0");
        ob_clean();
        $objWriter->save('php://output');
        exit;
        
        
    ?>
