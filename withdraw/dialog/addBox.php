<div id="addBox" class="bgwrap">
		<div class="container">
				<div class="container-header">     	
						<h2 id="title">Add</h2>
				</div>

				<div class="container-detail">
						<form method="post" action="./utility/add.php">
						<table class="searchTable">
			              		<tr><th>Customer :</th><td>
			               		<select name="cid">
								<?php 
			                    		reset($_customers);
										for($i=0;$i<sizeof($_customers);$i++){
											echo '<option value="'.key($_customers).'">'.current($_customers).'</option>';
				                        	next($_customers);
										}
								?>
								</select></td></tr>
				             	<tr><th>Date :</th><td><input class="datetimepicker" name="datetime" required="required"/></td></tr>
				              	<tr><th>Amount :</th><td><input name="amount" required="required" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');"/></td></tr>                                
								<tr><th>Bank account :</th><td>
				            	<select id="cbid" name="cbid">
								<?php 
				                       	reset($_banks);
										for($i=0;$i<sizeof($_banks);$i++) {
												echo '<option value="'.key($_banks).'">'.current($_banks).'</option>';
				                            	next($_banks);
										}
								?>
								</select></td></tr>
		                        <tr><th>Remark :</th><td><input name="comment"/></td></tr>
		                        <input type="hidden" name="status" value="0"/>
								<input type="hidden" name="add" value="1"/>
						</table>
				</div>		
						
				<div class="container-footer">
						<a onclick="add();">Cancel</a>
						<button class="confirmButton">Insert</button>
				</div>
				</form>
				
		</div>
</div>