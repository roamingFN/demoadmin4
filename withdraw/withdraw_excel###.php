<?php
        
        /** Error reporting */
        error_reporting(E_ALL);

        date_default_timezone_set('Europe/London');

        /** PHPExcel */
        require_once '../Classes/PHPExcel.php';

        session_start();
        include 'database.php';
        if (!isset($_SESSION['sql'])){
            $sql = 'SELECT * FROM customer_request_withdraw';
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
                ->setDescription("withdraw")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");

        $objPHPExcel->getActiveSheet()->getStyle("A4:G4")->getFont()->setBold(true);
        
        // Add some data
        echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', 'Exported Withdraw Data')
        ->setCellValue('A4', 'Date')
        ->setCellValue('B4', 'Amount')
        ->setCellValue('C4', 'Bank Name')
        ->setCellValue('D4', 'Account Number')
        ->setCellValue('E4', 'Account Name')
        ->setCellValue('F4', 'Remark')
        ->setCellValue('G4', 'Status');
        
//        //get customer name
//        $customer = array();
//        if($stmt = $con->prepare('SELECT customer_id,customer_firstname,customer_lastname FROM customer')){
//                $stmt->execute();
//                $stmt->bind_result($c_id,$cfn,$cln);
//                while($stmt->fetch()){
//                        $customers[$c_id] = $cfn." ".$cln;
//                }
//        }
//
//        //get bank payment
//        $cbanks = array();
//        $acnum = array();        
//        $acn = array();
//        if($stmt = $con->prepare('SELECT bank_account_id,bank_name,account_no,account_name FROM customer_bank_account')){
//                $stmt->execute();
//                $stmt->bind_result($b_id,$bname,$accnum,$accname);
//                while($stmt->fetch()){
//                        $cbanks[$b_id] = $bname;
//                        $acnum[$b_id] = $accnum;
//                        $acn[$b_id] = $accname;
//                }
//        }

        // Write data from MySQL result
        if($stmt = $con->prepare($sql)){
                $stmt->execute();
		$stmt->bind_result($wid,$cid,$bid,$am,$datetime,$status,$comment);
			
                $i = 5;
                while($stmt->fetch()) {
                        //$bif = explode(" ", $cbanks[$bid]);
//                        $date = substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
//			$time = substr($datetime,10,9);
//                        if ($status==0) $statdesc="รอโอน";
//                        else if ($status==1) $statdesc="โอนแล้ว";
//                        
//                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $date);
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $am);
//                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $cbanks[$bid]);
//                        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $acnum[$bid]);
//                        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $acn[$bid]);
//                        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $comment);
//                        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $statdesc);
//                        $i++;
                }
        }
        
        // Rename sheet
        echo date('H:i:s') . " Rename sheet\n";
        $objPHPExcel->getActiveSheet()->setTitle('Withdraw');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Save Excel 2007 file
        echo date('H:i:s') . " Write to Excel2007 format\n";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $strFileName = "withdraw.xlsx";
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
