<?php
        
        /** Error reporting */
        error_reporting(E_ALL);

        date_default_timezone_set('Europe/London');

        /** PHPExcel */
        require_once '../Classes/PHPExcel.php';

        session_start();
        include '../database.php';
 
        $sql = 'SELECT
  p.packageid,
  p.packageno,
  c.customer_firstname,
  c.customer_lastname,
  c.customer_code,
  p.total,
  ps.packagestatusname,
  p.paydate,
  wt.transport_th_name,
  p.total_tracking,
  p.total_count,
  pse.send_user,
  pse.send_date,
  pse.send_remark,
  pse.add_user,
  pse.key_date
FROM package p
INNER JOIN customer c
  ON c.customer_id = p.customerid
INNER JOIN package_status ps
  ON ps.packagestatusid = p.statusid
INNER JOIN website_transport wt
  ON wt.transport_id = p.shippingid
LEFT JOIN package_send pse ON p.packageid=pse.packageid
WHERE p.statusid >= 3
group by p.packageid
ORDER BY packageid desc';

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
        ->setCellValue('A2', 'Exported Transport Data')
        ->setCellValue('A4', 'เลขที่กล่อง')
        ->setCellValue('B4', 'ชื่อลูกค้า')
        ->setCellValue('C4', 'ID ลูกค้า')
        ->setCellValue('D4', 'ยอดค่าขนส่ง')
        ->setCellValue('E4', 'สถานะ')
        ->setCellValue('F4', 'วันที่ชำระ')
        ->setCellValue('G4', 'วิธีส่ง')
        ->setCellValue('H4', 'จำนวนTracking')
        ->setCellValue('I4', 'จำนวนกล่อง')
        ->setCellValue('J4', 'ผู้ส่ง')
        ->setCellValue('K4', 'วันที่ส่ง')
        ->setCellValue('L4', 'หมายเหตุ')
        ->setCellValue('M4', 'Add')
        ->setCellValue('N4', 'Add Date');
        
        //echo $sql;
        // Write data from MySQL result
        if($stmt = $con->prepare($sql)){
                $stmt->execute();
		$stmt->bind_result(
				$packageid,
				$packageno,
				$customer_firstname,
				$customer_lastname,
				$customer_code,
				$total,
				$packagestatusname,
				$paydate,
				$transport_th_name,
				$total_tracking,
				$total_count,
				$send_user,
				$send_date,
				$send_remark,
				$add_user,
				$key_date);
	
			
                $i = 5;
                while($stmt->fetch()) {                 
                        //formated date
//         echo $packageno;
                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $packageno);
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $customer_firstname.' '.$customer_lastname);
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $customer_code);
                        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $total);
                        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $packagestatusname);
                        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $paydate);
                        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $transport_th_name);
                        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $total_tracking);
                        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $total_count);
                        $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, (!empty($send_user)?$send_user:'-'));
                        $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, (!empty($send_date)?$send_date:'-'));
                        $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, (!empty($send_remark)?$send_remark:'-'));
                        $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, (!empty($add_user)?$add_user:'-'));
                        $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, (!empty($key_date)?$key_date:'-'));
                         $i++;
                }
        }
        
        echo date('H:i:s') . " Rename sheet\n";
        $objPHPExcel->getActiveSheet()->setTitle('Transport');
        $objPHPExcel->setActiveSheetIndex(0);

        echo date('H:i:s') . " Write to Excel2007 format\n";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $strFileName = "transport.xlsx";
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$strFileName.'"');
        header("Cache-Control: max-age=0");
        ob_clean();
        $objWriter->save('php://output');
        exit;
        
        
    ?>
