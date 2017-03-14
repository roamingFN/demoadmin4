<div id="emailBox" class="bgwrap">
		<div class="container">

				<form action="./utility/sendEmail.php" method="post">
	     		<div class="container-header">
	        		<table style="width:100%;">
		        			<tr>
			        				<th><span>Email Subject</span>  <input id="email-subject" name="email-subject" value=""/></th>
		        			</tr>
					</table>
				</div>

				<div class="container-detail">
						<table>
			        			<tr>
			        					<textarea class="email" id="email-content" name="email-content"></textarea>
			        			</tr>
						</table>
				</div>
			
				<div class="container-footer">
						<button class="confirmButton">ส่ง</button>
						<button class="cancelButton" onclick="showEmailBox();" type="button">ยกเลิก</button>
				</div>
				<input type="hidden" id="email-oid" name="email-oid">
				<input type="hidden" id="email-ono" name="email-ono">
                <input type="hidden" id="email-opid" name="email-opid">
                <input type="hidden" id="email-cid" name="email-cid">
				</form>
		</div>
</div>