<?php
        
        /** Error reporting */
        error_reporting(E_ALL);

        date_default_timezone_set('Europe/London');

        /** PHPExcel */
        require_once '../Classes/PHPExcel.php';
        include '../database.php';
        session_start();
        
        if (!isset($_SESSION['sql'])) {
            $sql = 'SELECT c.cashid, c.crn,c.customer,c.date,c.time,c.amount,c.remark,c.branch,c.bid,c.acn,c.uid,c.ctime,c.remarkc,c.status,c.cbid,c.topup_id'.
                ' FROM cash c JOIN bank_payment bp ON c.cbid=bp.bank_id';
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
                ->setDescription("payment")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");
        
        //set header
        $objPHPExcel->getActiveSheet()->getStyle("A4:M4")->getFont()->setBold(true);
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
        ->setCellValue('A2', 'Exported Cash Data')
        ->setCellValue('A4', 'Cash Ref. No.')
        ->setCellValue('B4', 'Customer')
        ->setCellValue('C4', 'Date Time')
        ->setCellValue('D4', 'Amount')
        ->setCellValue('E4', 'Bank')
        ->setCellValue('F4', 'Branch')
        ->setCellValue('G4', 'Remark')
        ->setCellValue('H4', 'Account')
        ->setCellValue('I4', 'Add User')
        ->setCellValue('J4', 'Add Time')
        ->setCellValue('K4', 'Status')
        ->setCellValue('L4', 'Remark Cancel')
        ->setCellValue('M4', 'Topup No.');
        
        //get bank
	$banks = array();
	if($stmt = $con->prepare('SELECT bname FROM bank')){
		$stmt->execute();
		$stmt->bind_result($bname);
		while($stmt->fetch()){
			array_push($banks,$bname);
		}
        }
        //get company bank
        $cBanks = array();
        if($stmt = $con->prepare('SELECT * FROM bank_payment')){
                $stmt->execute();
                $stmt->bind_result($c_bid,$acname,$acnum,$bNameTH,$bNameEN,$brc,$pic);
                while($stmt->fetch()){
                        $cBanks[$c_bid] = $acnum;
                        $_bName[$c_bid] = $bNameEN;
                }
        }
        //get topup
        $topups = array();
        $topups[0] = "-";
        if ($stmt = $con->prepare('SELECT topup_id,topup_number'
                . ' FROM customer_request_topup'))
        {
                $stmt->execute();
                $stmt->bind_result($tid,$tno);
                $topups[0] = "-";
                while($stmt->fetch()){
                        $topups[$tid] = $tno;
                }
        }
        
        // Write data from MySQL result
        $orderBy = ' ORDER BY c.crn DESC';
        if($stmt = $con->prepare($sql.$orderBy)){
                $stmt->execute();
		$stmt->bind_result($cid,$crn,$customer,$date,$time,$amount,$remark,$branch,$bid,$acn,$uid,$ctime,$remarkc,$status,$cbid,$topup_id);
			
                $i = 5;
                while($stmt->fetch()) {                 
                        //formated date
                        $formatted_date = substr($date,8,2).'-'.substr($date,5,2).'-'.substr($date,0,4);
                        //formated cdate ctime
                        $addDate=substr($ctime,8,2).'-'.substr($ctime,5,2).'-'.substr($ctime,0,4);
                        $addTime=substr($ctime,10,9);
                        //status description
                        if ($status==0) $status_des = "Normal";
                        else if ($status==1) $status_des = "Complete";
                        else if ($status==2) $status_des = "Cancel";
                        
                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $crn);
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $customer);
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $formatted_date.' '.$time);
                        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $amount);
                        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $_bName[$cbid]);
                        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $branch);
                        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $remark);
                        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, substr($cBanks[$cbid],0,3).'-'.substr($cBanks[$cbid],3,1).'-'.substr($cBanks[$cbid],4,5).'-'.substr($cBanks[$cbid],9,1));
                        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $uid);
                        $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $addDate." ".$addTime);
                        $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $status_des);
                        $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $remarkc);
                        $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $topups[$topup_id]);
                        $i++;
                        $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $sql);
                }
        }
        // Rename sheet
        echo date('H:i:s') . " Rename sheet\n";
        $objPHPExcel->getActiveSheet()->setTitle('Payment');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Save Excel 2007 file
        echo date('H:i:s') . " Write to Excel2007 format\n";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $strFileName = "payment.xlsx";
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
