<!DOCTYPE html>
<html>
	<head>
			<title>Withdraw</title>
			<meta charset="utf-8">
			<!-- CSS -->
			<link rel="stylesheet" type="text/css" href="../css/materialIcons.css">
			<link rel='stylesheet' type='text/css' href="../css/OpenSans.css">
			<link rel="stylesheet" type="text/css" href="../css/orderAdmin.css">
			<link rel="stylesheet" type="text/css" href="../css/dialog.css">
	        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
	        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css" />
	        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
			<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>

	        <!-- Script -->
	        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
	                
		<?php
            session_start();
            if (!isset($_SESSION['ID'])){
                header("Location: ../login.php");
            }
        
        	include './utility/function.php';
			include '../database.php';
			include './init.php';
			include './dialog/addBox.php';
			include './dialog/editBox.php';
			include './dialog/searchBox.php';
			include './dialog/loading.php';
			include '../utility/permission.php';

			const FORMID = 29;
			$_access = json_decode(getAccessForm($con,FORMID,$_SESSION['USERID']));
			$_adminFlg = getAdminFlag($con,$_SESSION['ID']);
			if ($_adminFlg==0) {
					if (empty($_access) || $_access[0]->visible==0) header ("Location: ../login.php");
			}
			
			function getCbid($ac) {
				include '../database.php';
				//get bank payment for search
				$cbsearch = array();
                if($stmt = $con->prepare('SELECT bank_account_id,account_no FROM customer_bank_account')){
						$stmt->execute();
						$stmt->bind_result($bid,$accnum);
						while($stmt->fetch()) {
								$cbsearch[$bid] = $accnum;                                              
						}
				}
					$result = array_search($ac, $cbsearch);

					if (empty($result)) {
						$result = 0;
					}

					return $result;
			}
		?>
	</head>

	<body>
			<div>
		            <h1><a class="brown" href="withdraw.php">Withdraw</a></h1>
		            <h3><a class="brown" href="../index.php">&larr; Back</a></h3><br>
            </div>

          	<div class="icon brown">
					<i class="material-icons" onclick="add();" title="Add">add_circle</i>
					<i class="material-icons" onclick="exportExcel();" title="Export">insert_drive_file</i>
					<i class="material-icons" onclick="searchBox();" title="Search">find_in_page</i>
			</div>

			<div>
					<?php
							//init===========================================================
							$puncCount = 0;
							
							//paging
							$pageSize = 20;
							$allRows = getNumberOfRows($con,$sqlCount,$condition,'');
							$allPage = ceil($allRows/$pageSize);
							if(isset($_GET['page'])) {
									$nowPage = $_GET['page']-1;
							} else{
									$nowPage = 0;
							}
							$paging = ' LIMIT '.$nowPage*$pageSize.','.$pageSize;
					?>

					<table class="result brown">
							<thead>
						            <tr>
						            	<th>Withdraw Number</th>
						            	<th>Customer</th>
										<th>Date</th>
										<th>Time</th>
										<th>Amount</th>
										<th>Bank Name</th>
										<th>Account Number</th>
										<th>Account Name</th>
										<th>Remark</th>
						                <th>Status</th>
										<th>Action</th>
									</tr>
							</thead>

						<?php
								echo '<tbody class="none">';                    
								$dataSet = getData($con,$sql,$condition,$orderBy,'',$paging);
								foreach ($dataSet as $key => $value) {
										$wid = $value['withdraw_request_id'];

										$datetime = $value['withdraw_date'];
			                            $date = substr($datetime,8,2).'-'.substr($datetime,5,2).'-'.substr($datetime,0,4);
										$time = substr($datetime,10,9);

			                        	echo '<tr class="'.($puncCount%2==0? 'punc ':'').'">'.
										'<td>'.$value['withdraw_number'].'</td>'.
										'<td>'.$value['customer_firstname'].' '.$value['customer_lastname'].'</td>'.
										'<td>'.$date.'</td>'.
										'<td>'.$time.'</td>'.
										'<td id="num">'.number_format($value['withdraw_amount'],2).'</td>'.
										'<td id="'.$wid.'bname">'.$value['bank_name'].'</td>'.
										'<td id="'.$wid.'accnum">'.$value['account_no'].'</td>'.
			                            '<td id="'.$wid.'accname">'.$value['account_name'].'</td>'.
			                            '<td id="'.$wid.'comment">'.$value['comment'].'</td>';
			                            
			                            //status
			                            $status = $value['withdraw_status'];
										if ($status==0) {
			                            		echo '<td><select id="'.$wid.'statusFront">'
			                                    .'<option value="0" selected>Waiting</option>'
			                                    .'<option value="1">Complete</option>'
			                                    .'<option value="2">Cancel</option></select></td>'
			                            	    .'<td>';
			                            }
			                            else if ($status==1) {
			                            		echo '<td><select id="'.$wid.'statusFront">'
			                                    .'<option value="0">Waiting</option>'
			                                    .'<option value="1" selected>Complete</option>'
			                                    .'<option value="2">Cancel</option></select></td>'
			                                    .'<td>';
			                                    $countComplete++;
			                            }
			                            else if ($status==2) {
			                                    echo '<td><select id="'.$wid.'statusFront">'
			                                        .'<option value="0">Waiting</option>'
			                                        .'<option value="1">Complete</option>'
			                                        .'<option value="2" selected>Cancel</option></select></td>'
			                                        .'<td>';
			                            }

			                        echo '<input type="hidden" id="'.$wid.'"/ value="'.$wid.'">'.
			                        '<input type="hidden" id="'.$wid.'wno"/ value="'.$value['withdraw_number'].'">'.
			                        '<input type="hidden" id="'.$wid.'customer"/ value="'.$value['customer_id'].'">'.
			                        '<input type="hidden" id="'.$wid.'datetime"/ value="'.$datetime.'">'.
			                        '<input type="hidden" id="'.$wid.'amount"/ value="'.$value['withdraw_amount'].'">'.
			                        '<input type="hidden" id="'.$wid.'bid"/ value="'.$value['customer_bank_account_id'].'">'.
			                        '<input type="hidden" id="'.$wid.'status"/ value='.$status.'>'.
			                        '<a class="brown" onclick="save(\''.$wid.'\')">Save</a> '.
									'<a class="brown" onclick="edit(\''.$wid.'\')">Edit</a> '.
			                        //'<form onsubmit="return confirm(\'ต้องการลบข้อมูลใช่หรือไม่?\');" action="withdraw.php?page='.($nowPage+1).'" method="post">'.
			                        //'<input name="del" value="'.$wid.'" type="hidden"/>'.
			                        '<a class="brown" onclick="del(\''.$wid.'\')">Del</a>'.
									//'</form>'.
									'</td></tr>';
									$puncCount++;
							}
							
							//summary
			                $sum = array(0,0,0);
			                $count = array(0,0,0);
			                $totalSum = 0;
			                $totalCount = 0;
			                $groupByStat = ' GROUP BY w.withdraw_status';
			                $sql = 'SELECT w.withdraw_status,sum(w.withdraw_amount),count(w.withdraw_request_id) FROM customer_request_withdraw w'.$condition.$groupByStat;
			                //echo $sql;                 
			                if($stmt = $con->prepare($sql)){
			                    $stmt->execute();
			                    $stmt->bind_result($stat,$amount,$countId);
			                    while($stmt->fetch()){
			                            $sum[$stat] = $amount;
			                            $count[$stat] = $countId;

			                            $totalSum += $amount;
			                            $totalCount += $countId;
			                    }
			                }
			                echo '</tbody>';
						?>
					</table>
			</div>

			<div class="paging">
					<?php 
							echo 'หน้า ';
							for($i=1;$i<=$allPage;$i++) {
									if (($nowPage+1)!=$i) echo '<a class="brown" href="?page='.$i.'&'.$request.'"><ins>'.$i.'</ins></a>';
									else echo '<a class="brown" href="?page='.$i.'&'.$request.'">'.$i.'</a>';
							}
					?>
			</div>

			<div class="summary">
	                <table>
		                    <tr>
		                        <td><b>จำนวนรายการทั้งหมด</b></td>
		                        <td class="normal"><b>Waiting :</b></td>
		                        <td class="number"><?php echo number_format($count[0]); ?>&nbsp;</td>
		                        <td class="classomplete"><b>Complete :</b></td>
		                        <td class="number"><?php echo number_format($count[1]); ?>&nbsp;</td>
		                        <td class="cancel"><b>Cancel :</b></td>
		                        <td class="number"><?php echo number_format($count[2]); ?>&nbsp;</td>
		                        <td><b>Total :</b></td>
		                        <td class="number"><?php echo number_format($allRows); ?>&nbsp;</td>
		                        <td>Records<br></td>
		                    </tr>
		                    <tr>
		                        <td><b>จำนวนยอดทั้งหมด</b></td>
		                        <td class="normal"><b>Waiting :</b></td>
		                        <td class="number"><?php echo number_format($sum[0],2); ?>&nbsp;</td>
		                        <td class="complete"><b>Complete :</b></td>
		                        <td class="number"><?php echo number_format($sum[1],2); ?>&nbsp;</td>
		                        <td class="cancel"><b>Cancel :</b></td>
		                        <td class="number"><?php echo number_format($sum[2],2); ?>&nbsp;</td>
		                        <td><b>Total :</b></td>
		                        <td class="number"><?php echo number_format($totalSum,2); ?>&nbsp;</td>
		                        <td style="text-align: left;">THB</td>
		                    </tr>
	               	</table>
			</div>

			<!--Script -->
			<script src="./controller.js"></script>
			<script>
					$(function() {
							$( ".datepicker" ).datepicker({
		                            dateFormat: "dd-mm-yy"
							});        
					});
					$( ".datetimepicker" ).datetimepicker({
                            dateFormat: "dd-mm-yy",
                            timeFormat: "HH:mm:ss",
                     		showSecond:true
					});
			</script>
	</body>
</html>

<?php
		$con->close();
?>