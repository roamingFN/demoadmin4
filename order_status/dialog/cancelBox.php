<div id="cancelBox" class="bgwrap">
		<div class="container" style="width:1000px">
      		<div style="text-align:left;margin-left:10px">
      				<h1>ยกเลิกรายการรอตรวจสอบ</h1>
      		</div>

				<form action="./utility/cancel.php" style="width:100%;" method="post">
				<div style="margin-top:10px">
		        		<table style="width:80%;text-align:left;">
		        				<tbody>
		        						<tr>
		        								<th>Order No. :</th><td id="c-ono"></td>
		        								<th>Customer :</th><td id="c-cname"></td>
		        								<th></th><td></td>
		        						</tr>
		        						<tr>
		        								<th>Date Time :</th><td id="c-dt"></td>
		        								<th>Amount :</th><td id="c-amount"></td>
		        								<th>Status :</th><td id="c-stat"></td>
		        						</tr>
		        				</tbody>
		        		</table>
				</div>
				
				<div style="text-align:left;width:100%;margin-left:50px;">
						<div style="float:left;"><b>Remark Cancel :&nbsp;</b></div>
						<div><textarea id="c-remarkc" name="c-remarkc" style="width:70%;height:100px;font-size:16px;"></textarea></div>
				</div>

				<div style="text-align:center;padding:10px">
						<button class="order-button">ยืนยัน</button>
						<button class="order-cancel" onclick="showCancelBox();" type="button">กลับ</button>
				</div>
				<input type="hidden" name="c-oid" id="c-oid">
				<input type="hidden" name="c-cid" id="c-cid">
				<input type="hidden" name="cc-ono" id="cc-ono">
				<input type="hidden" name="cc-amount" id="cc-amount">
				<input type="hidden" name="cc-dt" id="cc-dt">
				</form>
		</div>
</div>