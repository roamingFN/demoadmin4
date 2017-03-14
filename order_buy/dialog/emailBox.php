<!--email Box-->
<div id="emailBox" class="bgwrap">
		<div class="container" style="width:80%;height:80%">
			<form method="post" style="width:100%;">		
     		<div>
        		<table class="emailBox">
        			<tr>
        					<th style="text-align:right;">To: </th>
        					<th style="text-align:right;"><input style="width:100%;font-size:14px" id="email-cmail" name="email-cmail" value=""/></th>
        			</tr>
        			<tr>
        				<th style="text-align:right;">Email Subject: </th>
        				<th style="text-align:right;"><input style="width:100%;font-size:14px" id="email-subject" name="email-subject" value=""/></th>
        			</tr>
				</table>
			</div>

			<div style="width:100%;text-align:center;">
				<table>
						<td>
        						<textarea style="width:100%;height:300px;font-size:14px;" id="email-content" name="email-content"></textarea>
        				</td>
				</table>
			</div>
		
			<div style="text-align:center;padding:10px">
				<input type="hidden" name="email-oid" id="email-oid" value="">
				<input type="hidden" name="email-opid" id="email-opid" value="">
				<input type="hidden" name="email-cid" id="email-cid" value="">
				<input type="hidden" name="email-ono" id="email-ono" value="">
				<input type="hidden" name="email" value="1"/>
				<button class="order-button">ส่ง email</button>
				<a onclick="emailBox();"><button class="order-cancel" type="button">Cancel</button></a>
			</div>
			</form>
		</div>
</div>