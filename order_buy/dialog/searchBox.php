<div id="searchBox" class="wrap">
		<form method="get">
			<table>
				<tr><th><h2 id="title">Search</h2></th><td></td></tr>
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
	            		<option value="-">-</option>
	                    <option value="3" selected><?php echo $_codes[3]?></option>
	                    <option value="4"><?php echo $_codes[4]?></option>
	                    <option value="5"><?php echo $_codes[5]?></option>
	                    <option value="6"><?php echo $_codes[6]?></option>
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
				<tr class="confirm"><td></td><td><a onclick="searchBox();">Cancel</a>&emsp;<button>Search</button></td></tr>
			</table>
		</form>
</div>