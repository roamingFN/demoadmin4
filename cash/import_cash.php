<?php
    function genCRN(){
                                include 'database.php';
                                $i = 0;
                                $newTT = '0000001';                                
                                $stmt = $con->prepare('SELECT crn FROM cash ORDER BY crn DESC LIMIT 1');
                                $stmt->execute();
                                $result = $stmt->get_result();
                                while ($row = $result->fetch_array(MYSQLI_NUM)) {
                                        foreach ($row as $r) {
                                            $i += 1;
                                        }
                                }
                                //find year
                                $year = substr((string)date("Y"),2,2);
                                //cash has records
                                if ($i) {
                                        $tempTT = (string) ((int) substr($r, -7) + 1);
                                        $len = strlen($tempTT);
                                        if ($len == 6) {
                                            $newTT = '0' . $tempTT;
                                        } else if ($len == 5) {
                                            $newTT = '00' . $tempTT;
                                        } else if ($len == 4) {
                                            $newTT = '000' . $tempTT;
                                        } else if ($len == 3) {
                                            $newTT = '0000' . $tempTT;
                                        } else if ($len == 2) {
                                            $newTT = '00000' . $tempTT;
                                        } else if ($len == 1) {
                                            $newTT = '000000' . $tempTT;
                                        } else {
                                            $newTT = $tempTT;
                                        }
                                }
                            return 'TT'.$year.$newTT;
                        }
                
    function importExcel($file) {
                    /** PHPExcel */
                    require_once 'Classes/PHPExcel.php';

                    /** PHPExcel_IOFactory - Reader */
                    include 'Classes/PHPExcel/IOFactory.php';

                    $inputFileName = 'import/'.$file;
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objReader->setReadDataOnly(true);
                    $objPHPExcel = $objReader->load($inputFileName);
                    
                    $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
                    $highestRow = $objWorksheet->getHighestRow();
                    $highestColumn = $objWorksheet->getHighestColumn();

                    $headingsArray = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1',null, true, true, true);
                    $headingsArray = $headingsArray[1];

                    $r = -1;
                    $i = 0;
                    $namedDataArray = array();
                    
                    //start at row 2
                    for ($row = 2; $row <= $highestRow; ++$row) {
                        $dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row,null, true, true, true);
                        if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {
                            ++$r;
                            foreach($headingsArray as $columnKey => $columnHeading) {
                                //if read in date cell
                                if($columnHeading == "Date") {
                                        $cell = $objPHPExcel->getActiveSheet()->rangeToArray("A1:".$highestColumn.$highestRow);
                                        $i = 2; 
                                        foreach($cell as $val) {
                                                // in my case 2nd column will be date so that I can get the format by
                                            $date = date('Y/m/d',PHPExcel_Shared_Date::ExcelToPHP($objWorksheet->getCellByColumnAndRow(1, $row)->getValue()));  // array index 1              
                                        }                            
                                        $namedDataArray[$r][$columnHeading] = $date;
                                        //echo $date;
                                }
                                //if read in time cell
                                else if($columnHeading == "Time") {
                                        $cell = $objWorksheet->getCellByColumnAndRow(2,$row);
                                        $cell_value = PHPExcel_Style_NumberFormat::toFormattedString($cell->getCalculatedValue(), 'hh:mm:ss');
                                        $namedDataArray[$r][$columnHeading] = $cell_value;
                                }
                                else {
                                        $namedDataArray[$r][$columnHeading] = $dataRow[$row][$columnKey];
                                }
                            }   
                        }                        
                    }
                    //print_r($namedDataArray);
                    
                    echo '<pre>';
                    var_dump($namedDataArray);
                    echo '</pre><hr />';
                    ?>
                    <table width="500" border="1">
                      <tr>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Amount</th>
                      </tr>
                    <?php                  
                    foreach ($namedDataArray as $result) {          
                    ?>         
                            <tr>
                                    <td><?php echo $result["Customer"]; ?></td>
                                    <td><?php echo $result["Date"]; ?></td>
                                    <td><?php echo $result["Time"]; ?></td>
                                    <td><?php echo $result["Amount"]; ?></td>
                            </tr>
                    <?php
                    }
                    ?>

                    </table>
            <?php
            //chekACN in table bank_payment
            if (checkACN($namedDataArray)==TRUE) {
                    //insert data
                    include 'database.php';
                    foreach ($namedDataArray as $result) {
                            $crn = genCRN();
                            echo $crn.$result['Customer'].$result['Date'].$result['Time'].$result['Amount'];
                            $uid = 'test00';
                            $status = 0;
                            $bid = 2;
                            $cbid = 1;
                            $stmt = $con->prepare('INSERT INTO cash(crn,customer,date,time,amount,uid,status,bid,cbid) VALUES(?,?,DATE(?),?,?,?,?,?,?)');
                            $stmt->bind_param('ssssdsiii',$crn,$result['Customer'],$result['Date'],$result['Time'],$result['Amount'],$uid,$status,$bid,$cbid);

                            $res = $stmt->execute();

                            if(!$res){
                                    echo '<script>alert(การเพิ่มข้อมูลล้มเหลว);</script>';
                            }
                            else {
                                    echo '<script>alert(เพิ่มข้อมูลสำเร็จ);</script>';
                            }
                    }            
            }
            else {
                    echo '<script>alert(การเพิ่มข้อมูลล้มเหลว);</script>';
            }
        }
            
    function checkACN($rs) {
            //get bank_payment
            $cBanks = array();
            if($stmt = $con->prepare('SELECT bank_id,account_no FROM bank_payment')){
                    $stmt->execute();
                    $stmt->bind_result($b_id,$acn);
                    while($stmt->fetch()){
                            $cBanks[$b_id] = $acn;                                              
                    }
            }
            foreach ($namedDataArray as $result) {
                    reset($cBanks);
                    for($i=0;$i<sizeof($cBanks);$i++) {
                            echo $result['Account'].' '.current($cBanks);
                            if ($result['Account']!= current($cBanks)) {
                                return FALSE;
                            }
                            next($cBanks);                        
                    }
            }
        return TRUE;
    }
    
    session_start();
    if(!isset($_FILES['excelDir']['name'])){        
    }
    else {
        move_uploaded_file($_FILES["excelDir"]["tmp_name"],"import/".$_FILES['excelDir']['name']);
        importExcel($_FILES["excelDir"]["name"]);
    }               
?>

