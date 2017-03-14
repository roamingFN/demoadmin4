<?php
        
        /** Error reporting */
        error_reporting(E_ALL);

        date_default_timezone_set('Europe/London');

        /** PHPExcel */
        require_once '../Classes/PHPExcel.php';

        session_start();
        include '../database.php';
        if (!isset($_SESSION['sql'])){
            $sql = 'SELECT * FROM customer_request_topup';
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
                ->setDescription("Topup")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");

        //set header
        $objPHPExcel->getActiveSheet()->getStyle("A4:K4")->getFont()->setBold(true);
        //set number
        $objPHPExcel->getActiveSheet()->getStyle("D")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        //set auto width
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        foreach(range('A','L') as $columnID) {
             $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Add some data
        echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', 'Exported Topup Data')
        ->setCellValue('A4', 'Customer')
        ->setCellValue('B4', 'Date')
        ->setCellValue('C4', 'Time')
        ->setCellValue('D4', 'Amount')
        ->setCellValue('E4', 'Transfer By')
        ->setCellValue('F4', 'Remark')
        ->setCellValue('G4', 'Bank Name')
        ->setCellValue('H4', 'Account name.')
        ->setCellValue('I4', 'Account no.')
        ->setCellValue('J4', 'Topup no.')
        ->setCellValue('K4', 'Status')
        ->setCellValue('L4', 'Remark Cancel');
        
        //get customer name
        $customer = array();
	   if($stmt = $con->prepare('SELECT customer_id,customer_firstname,customer_lastname FROM customer')){
        $stmt->execute();
		$stmt->bind_result($c_id,$cfn,$cln);
		while($stmt->fetch()){
			$customers[$c_id] = $cfn." ".$cln;
		}
	}

        //get bank payment
        $banks = array();
        $_act = array();
        if($stmt = $con->prepare('SELECT bank_id,bank_name_en,account_no,account_name FROM bank_payment')){
		$stmt->execute();
		$stmt->bind_result($b_id,$cif,$acn,$act);
		while($stmt->fetch()){
			$banks[$b_id] = $cif." ".$acn;
            $_act[$b_id] = $act;                                              
		}
	}

        // Write data from MySQL result
        $orderBy = ' ORDER BY topup_number DESC';
        if($stmt = $con->prepare($sql.$orderBy)){
            $stmt->execute();
            $stmt->bind_result($tid,$tno,$cid,$bid,$amount,$u_amount,$status,$datetime,$tran_method,$bill,$note,$comment,$remarkc,$cashId,$used,$emailno,$emaildt);
			
            $i = 5;
            while($stmt->fetch()) {                 
                //desc
                $bif = explode(" ", $banks[$bid]);
                $date = substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
                $time = substr($datetime,10,9);
                if ($status==0) $statdesc="Waiting";
                else if ($status==1) $statdesc="OK";
                else if ($status==2) $statdesc="Cancel";
                        
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $customers[$cid]);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $date);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $time);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $amount);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $tran_method);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $note);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $bif[0]);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $_act[$bid]);
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $bif[1]);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $tno);
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $statdesc);
                $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $remarkc);
                $i++;
            }
        }
        
        // Rename sheet
        echo date('H:i:s') . " Rename sheet\n";
        $objPHPExcel->getActiveSheet()->setTitle('Topup');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Save Excel 2007 file
        echo date('H:i:s') . " Write to Excel2007 format\n";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $strFileName = "topup.xlsx";
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
