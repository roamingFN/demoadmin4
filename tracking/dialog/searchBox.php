<div id="searchBox" class="bgwrap">
		<div class="container">
				<div class="container-header">
						<h2 id="title">Search</h2>
				</div>

				<div class="container-detail">
						<form method="get">
						<table class="searchTable">
								<tbody>
										<tr><th>Tracking Number :</th><td><input name="tno"/></td></tr>
								        <tr><th>Order Number :</th><td><input name="ono"/></td></tr>
								        <tr><th>วันที่    Order From :</th><td><input class="datepicker" name="from"/></td></tr>
								        <tr><th>To :</th><td><input class="datepicker" name="to"/></td></tr>
								        <tr><th>ชื่อลูกค้า :</th><td>
												<?php 
													echo '<input name="cname" list="cname">';
									          		echo '<datalist id="cname">';
									          		foreach ($_cus as $key => $value) {
															echo '<option value="'.$value['customer_firstname'].' '.$value['customer_lastname'].' ('.$value['customer_code'].')" />';
													}
													echo '</datalist>';
												?>
										</select></td></tr>
										<tr><th>ID ลูกค้า :</th><td><input name="cid"/></td></tr>
								        <tr><th>Status :</th><td><select style="width:200px;" name="ostatus">
								        		<option value="-" selected>-</option>
								                <option value="4"><?php echo $_stat[4]?></option>
								                <option value="5"><?php echo $_stat[5]?></option>
								                <option value="6"><?php echo $_stat[6]?></option>
								                <option value="7"><?php echo $_stat[7]?></option>
										</select></td></tr>
										<tr><th>ค่าขนส่ง :</th><td><input name="tranCost"/></td></tr>
										<tr><th>สถานะ Tracking :</th><td><select style="width:200px;" name="tstatus"/>
												<option value="-">-</option>
								                <option value="0" selected>Incomplete</option>
								                <option value="1">Complete</option>
										</select></td></tr>
										<tr><th>สถานะกล่อง :</th><td><select style="width:200px;" name="pstatus"/>
												<option value="" selected="">-</option>
												<?php
													foreach ($_pStatDesc as $key => $value) {
															echo '<option value="'.$key.'">'.$value.'</option>';
													}
												?>
										</select></td></tr>
										<tr><th>Search All :</th><td><input name="all"/></td></tr>
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