<div id="editBox" class="bgwrap">
		<div class="container">
				<div class="container-header">
						<h2 id="title">Edit</h2>
				</div>

				<div class="container-detail">
				<form method="post" action="./utility/edit.php">
						<table class="searchTable">				
					        	<input type="hidden" id="e-wid" name="wid" value=""/>
					       		<tr><th>Customer :</th><td>
					        	<select name="cid">
									<?php 
					              		reset($_customers);
										for($i=0;$i<sizeof($_customers);$i++){
											echo '<option id="e-cid-'.key($_customers).'" value="'.key($_customers).'">'.current($_customers).'</option>';
					                      	next($_customers);
										}
									?>
								</select></td></tr>
					    		<tr><th>Date :</th><td><input id="e-datetime" class="datetimepicker" name="datetime" required="required"/></td></tr>
					     		<tr><th>Amount :</th><td><input id="e-amount" name="amount" required="required" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');"/></td></tr>                                
								<tr><th>Bank account :</th><td>
					       		<select name="cbid">
									<?php 
					                 	reset($_banks);
										for($i=0;$i<sizeof($_banks);$i++){
											echo '<option id="e-bid-'.key($_banks).'" value="'.key($_banks).'">'.current($_banks).'</option>';
					                      	next($_banks);
										}
									?>
								</select></td></tr>
								<tr><th>Status :</th><td><select name="status">
					            		<option id="e-stat-0" value="0">Waiting</option>
					                    <option id="e-stat-1" value="1">Complete</option>
					                    <option id="e-stat-2" value="2">Cancle</option>
								</select></td></tr>
					          	<tr><th>Remark :</th><td><input id="e-comment" name="comment"/></td></tr>
								<input type="hidden" name="edit" value="1"/>
						</table>
				</div>

				<div class="container-footer">
						<a onclick="edit();">Cancel</a>
						<button class="confirmButton">Edit</button>
				</div>
				</form>
		</div>
</div>