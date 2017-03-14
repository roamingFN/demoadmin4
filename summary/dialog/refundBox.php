<!--Refund Box-->
<div id="refundBox" class="bgwrap">

		<div class="container">
    			<div class="container-header">
            		  <h2 id="title">คืนเงิน</h2>
         		</div>
    			
    			<form action="./utility/refund.php" method="post">		
         		<div class="container-detail">
                		<table>
                    			<tr>
                        				<th>จำนวนที่สั่ง</th>
                        				<th>จำนวนที่สั่งได้</th>
                        				<th>ขาด</th>
                        				<th>ราคา/ชิ้น (หยวน)</th>
                        				<th>รวม (หยวน)</th>
                        				<th>เรท</th>
                        				<th>รวม (บาท)</th>
                    			</tr>
                    			<tr>
                        				<th id="refx-bsQuan"></th>
                        				<th id="refx-rcQuan"></th>
                        				<th id="refx-missing"></th>
                        				<th id="refx-bsPrice"></th>
                        				<th id="refx-return_yuan"></th>
                        				<th id="refx-rate"></th>
                        				<th id="refx-return_baht"></th>
                    			</tr>
                    			<tr>
                        				<th colspan="4">ร้านเรียกเก็บค่ารถเพิ่ม</th>
                        				<th id="refx-bsTran_yuan"></th>
                        				<th id="refx-bsRate"></th>
                        				<th id="refx-bsTran_baht"></th>
                    			</tr>
                                <tr>
                                        <th colspan="5">ยอดคืนเงิน</th>
                                        <th id="refx-total_baht"></th>
                                        <th>บาท</th>
                                </tr>
        				</table>
    			</div>
    		
    			<div class="container-footer">
                        <button class="confirmButton">ยืนยัน</button>
        				<button class="cancelButton" onclick="showRefundBox();" type="button">ยกเลิก</button>	
    			</div>
                <input type="hidden" id="ref-oid" name="ref-oid">
                <input type="hidden" id="ref-opid" name="ref-opid">
                <input type="hidden" id="ref-cid" name="ref-cid">
                <input type="hidden" id="ref-bsQuan" name="ref-bsQuan">
                <input type="hidden" id="ref-rcQuan" name="ref-rcQuan">
                <input type="hidden" id="ref-missing" name="ref-missing">
                <input type="hidden" id="ref-return_yuan" name="ref-return_yuan">
                <input type="hidden" id="ref-return_baht" name="ref-return_baht">
                <input type="hidden" id="ref-bsPrice" name="ref-bsPrice">
                <input type="hidden" id="ref-total_baht" name="ref-total_baht">
                <input type="hidden" id="ref-rate" name="ref-rate">
    			</form>
		</div>
</div>