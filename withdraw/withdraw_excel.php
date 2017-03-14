<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
        /** Error reporting */
        error_reporting(E_ALL);

        date_default_timezone_set('Europe/London');

        require_once '../Classes/PHPExcel.php';

        session_start();
        include '../database.php';
        if (!isset($_SESSION['sql'])){
                $sql = 'SELECT * FROM customer_request_withdraw';
                $condition = '';
        }
        else {
                $sql = $_SESSION['sql'];
                $condition = $_SESSION['condition'];
        }

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

        //set header
        $objPHPExcel->getActiveSheet()->getStyle("A4:J4")->getFont()->setBold(true);
        //set number
        $objPHPExcel->getActiveSheet()->getStyle("E")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        //set auto width
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        foreach(range('A','G') as $columnID) {
             $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Add some data
        echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', 'Exported Withdraw Data')
        ->setCellValue('A4', 'Withdraw Number')
        ->setCellValue('B4', 'Customer')
        ->setCellValue('C4', 'Date')
        ->setCellValue('D4', 'Time')
        ->setCellValue('E4', 'Amount')
        ->setCellValue('F4', 'Bank Name')
        ->setCellValue('G4', 'Account Number')
        ->setCellValue('H4', 'Account Name')
        ->setCellValue('I4', 'Remark')
        ->setCellValue('J4', 'Status');
        
        // Write data from MySQL result
        //get customer name
        $customers = array();
        if($stmt = $con->prepare('SELECT customer_id,customer_firstname,customer_lastname FROM customer')){
                $stmt->execute();
                $stmt->bind_result($c_id,$cfn,$cln);
                while($stmt->fetch()){
                        $customers[$c_id] = $cfn." ".$cln;
                }
        }

        //get bank payment
        $cbanks = array();
        $acnum = array();        
        $acn = array();
        if($stmt = $con->prepare('SELECT bank_account_id,bank_name,account_no,account_name FROM customer_bank_account')){
                $stmt->execute();
                $stmt->bind_result($b_id,$bname,$accnum,$accname);
                while($stmt->fetch()){
                        $cbanks[$b_id] = $bname;
                        $acnum[$b_id] = $accnum;
                        $acn[$b_id] = $accname;
                }
        }
        
        if($stmt = $con->prepare($sql.$condition)){
                $stmt->execute();
                $stmt->bind_result($wid,$wno,$cid,$bid,$amount,$datetime,$status,$comment,$a,$b,$c,$d,$e,$f);
                $i = 5;
                while($stmt->fetch()) {
                    $date = substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
                    $time = substr($datetime,10,9);
                    if ($status==0) $statdesc="Waiting";
                    else if ($status==1) $statdesc="Complete";
                    else if ($status==2) $statdesc="Cancel";
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $wno);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $customers[$cid]);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $date);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $time);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $amount);
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $cbanks[$bid]);
                    $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $acnum[$bid]);
                    $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $acn[$bid]);
                    $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $comment);
                    $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $statdesc);
                    $i++;
                }
        }
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