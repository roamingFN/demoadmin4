<div id="searchBox" class="bgwrap">
		<div class="container">
				<div class="container-header">
				<h2 id="title">Search</h2>
				</div>

				<div class="container-detail">
				<form method="get">
				        <table class="searchTable">
				            	<tr><th>Customer :</th><td>
				          		<select name="cid">
									<?php
				                        	echo '<option value="">-</option>';
				                       		reset($_customers);
											for($i=0;$i<sizeof($_customers);$i++){
													echo '<option value="'.key($_customers).'">'.current($_customers).'</option>';
					                         		next($_customers);
											}
									?>
								</select></td></tr>
				        		<tr><th>From :</th><td><input class="datepicker" name="from"/></td></tr>
								<tr><th>To :</th><td><input class="datepicker" name="to"/></td></tr>
								<tr><th>Amount :</th><td><input name="amount" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');"/></td></tr>
								<tr><th>Account number :</th><td><input name="cbid" maxlength="10"></td></tr>
								<tr><th>Status :</th><td><select name="status">
										<option value="-">-</option>
		                                <option value="0" selected>Waiting</option>
		                                <option value="1">Complete</option>
		                                <option value="2">Cancle</option>
								</select></td></tr>
						</table>
				</div>

				<div class="container-footer">
						<a onclick="searchBox();">Cancel</a>
						<button class="confirmButton">Search</button></td></tr>
				</div>
						
				</form>
		</div>
</div>