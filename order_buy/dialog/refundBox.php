<div id="refundBox" class="bgwrap">
		<div class="container" style="width:80%;">
			<div class="containerheader">
        		<h2 id="title">ยอดคืนเงิน</h2>
     		</div>
			
			<form id="ref-form" method="post">		
     		<div>
        		<div style="width:100%;text-align:center;">
                <table style="margin:0;text-align:right;">
                    <tr>
                            <td>จำนวนที่สั่ง</td><td><input style="text-align:right;" id="ref-quan1"></td><td>ชิ้น</td>
                            <td>ราคาที่สั่ง</td><td><input style="text-align:right;" id="ref-cpp1"></td><td>หยวน</td>
                            <td>ค่ารถที่ลูกค้าจ่าย</td><td><input style="text-align:right;" id="ref-tran1"></td><td>หยวน</td>
                    </tr>
                    <tr>
                            <td>จำนวนที่สั่งได้</td><td><input style="text-align:right;" id="ref-quan"></td><td>ชิ้น</td>
                            <td>ราคาที่สั่งได้</td><td><input style="text-align:right;" id="ref-cpp"></td><td>หยวน</td>
                            <td>ค่ารถที่ร้านสรุป</td><td><input style="text-align:right;" id="ref-tran"></td><td>หยวน</td>
                    </tr>
                    <tr>
                            <td>ยอดขาด</td><td><input style="text-align:right;" id="ref-sumquan"></td><td>ชิ้น</td>
                            <td>ส่วนต่าง</td><td><input style="text-align:right;" id="ref-sumcpp"></td><td>หยวน</td>
                            <td>ส่วนต่าง</td><td><input style="text-align:right;" id="ref-sumtran"></td><td>หยวน</td>
                    </tr>
                </table>

                <table style="width: 70%;">
                        <tr>
                                <td style="font-weight: bold;">สรุป</td>
                                <td style="width:30%;">จำนวนสินค้าที่ขาด</td>
                                <td id="ref-grandQuan" style="text-align:right;font-weight: bold;"></td>
                                <td>ชิ้น</td>
                        </tr>
                        <tr>
                                <td></td>
                                <td id="ref-desc"></td>
                                <td id="ref-grandVal" style="text-align:right;font-weight: bold;"></td>
                                <td>หยวน</td>
                        </tr>
                        <tr>
                                <td></td>
                                <td>Rate</td>
                                <td id="ref-grandRate" style="text-align:right;font-weight: bold;"></td>
                                <td></td>
                        </tr>
                        <tr>
                                <td></td>
                                <td id="ref-grandDesc"></td>
                                <td id="ref-grandSum" style="text-align:right;font-weight: bold;"></td>
                                <td>บาท</td>
                        </tr>
                </table>
			</div>
		
			<div style="text-align:center;padding:10px">
				<input type="hidden" name="ref-oid" id="ref-oid" value="">
				<input type="hidden" name="ref-opid" id="ref-opid" value="">
				<input type="hidden" name="ref-cid" id="ref-cid" value="">
				<input type="hidden" name="tmp-ordered" id="tmp-ordered" value="">
				<input type="hidden" name="tmp-received" id="tmp-received" value="">
				<input type="hidden" name="tmp-missed" id="tmp-missed" value="">
				<input type="hidden" name="tmp-price" id="tmp-price" value="">
				<input type="hidden" name="tmp-totalCn" id="tmp-totalCn" value="">
				<input type="hidden" name="tmp-rate" id="tmp-rate" value="">
				<input type="hidden" name="tmp-tran" id="tmp-tran" value="">
				<input type="hidden" name="tmp-total" id="tmp-total" value="">
                <input type="hidden" name="tmp-price1" id="tmp-price1" value="">
				<input type="hidden" name="refund" value="1"/>
				<button id="ref-submit" class="order-button">ตกลง</button>
				<a onclick="refund();"><button class="order-cancel" type="button">กลับ</button></a>
			</div>
			</form>
		</div>
</div>