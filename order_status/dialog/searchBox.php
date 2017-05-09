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
	            		<option value="-" selected>-</option>
	                    <option value="5"><?php echo $_codes[5]?></option>
				</select></td></tr>
				<tr class="confirm"><td></td><td><a onclick="searchBox();">Cancel</a>&emsp;<button>Search</button></td></tr>
			</table>
		</form>
</div>