<div id="searchBox" class="bgwrap">
		<div class="container">
				<div class="container-header">
						<th><h2 id="title">Search</h2></th><td></td>
				</div>

				<div class="container-detail dark">
						<form method="get">
						<table class="searchTable" style="width:400px">
								<tbody>
								        <tr><th>Order Number :</th><td><input name="ono"/></td></tr>
								        <tr><th>ชื่อลูกค้า :</th><td>
												<?php 
													echo '<input name="cid" list="cid">';
									          		echo '<datalist id="cid">';
									          		foreach ($_cus as $key => $value) {
															echo '<option value="'.$value['customer_firstname'].' '.$value['customer_lastname'].' ('.$value['customer_code'].')" />';
													}
													echo '</datalist>';
												?>
										</select></td></tr>
								     	<tr><th>From :</th><td><input class="datepicker" name="from"/></td></tr>
								        <tr><th>To :</th><td><input class="datepicker" name="to"/></td></tr>
								        <tr><th>Status :</th><td><select name="status">
								        		<option value="-" selected>-</option>
								                <option value="6"><?php echo $_stat[6]?></option>
								                <option value="7"><?php echo $_stat[7]?></option>
								                <option value="8"><?php echo $_stat[8]?></option>
								                <option value="9"><?php echo $_stat[9]?></option>
										</select></td></tr>
										<tr><th>taobao :</th><td>
												<?php 
													echo '<input name="taobao" list="taobao">';
									          		echo '<datalist id="taobao">';
									          		reset($_taobao);
													for($i=0;$i<sizeof($_taobao);$i++) {
															next($_taobao);
															echo '<option value="'.current($_taobao).'">';
													}
													echo '</datalist>';
												?>
										</select></td></tr>
										<tr>
											<th>tracking no. :</th>
											<td><input name="tracking" /></td>
										</tr>
										<tr>
											<th><input type="checkbox" name="inComTaobao"></th>
											<td>Order ที่ยังมีบิล Taobao ไม่ครบ</td>

										</tr>
										<tr>
											<th><input type="checkbox" name="inComTracking"></th>
											<td>Order ที่ยังมีเลขที่ Tracking ไม่ครบ</td>
										</tr>
								</tbody>
						</table>
				</div>

				<div class="container-footer">
        				<a onclick="showSearchBox();">Cancel</a>
        				<button class="confirmButton">Search</button>
				</div>
				</form>
		</div>
</div>