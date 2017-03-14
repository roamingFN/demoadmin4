<div id="backRefundBox" class="bgwrap">
		<div class="container">

				<div class="container-header">
						<h2 id="title">ดึงยอดคืนเงินกลับ</h2>
				</div>

				<form action="./utility/backRefund.php" method="post">
				<div class="container-detail">
		        		<table>
		        			<tr>
			        				<th>ยอดเงิน</th>
			        				<th id="brefx-return_baht"></th>
			        				<th style="text-align:left;">บาท</th>
	        				</tr>
						</table>
				</div>
			
				<div class="container-footer">
						<button class="confirmButton">ยืนยัน</button>
						<button class="cancelButton" onclick="showBackRefundBox();" type="button">ยกเลิก</button>
				</div>
				<input type="hidden" id="bref-oid" name="bref-oid">
                <input type="hidden" id="bref-opid" name="bref-opid">
                <input type="hidden" id="bref-cid" name="bref-cid">
                <input type="hidden" id="bref-return_baht" name="bref-return_baht">
				</form>
		</div>
</div>