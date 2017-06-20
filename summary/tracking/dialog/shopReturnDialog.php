<div id="shopReturnDialog" class="bgwrap">
		<div class="container" style="width:90%;">
			<div class="container-header">
        		<h2>Rate <span id="title"></span></h2>
     		</div>
			
            <form id="return_summary" method="post" action="./tracking/utility/saveReturn.php">
            <div class="container-detail dark">
                <table style="margin:0;width: 100%;" id="returnDialog">
                    <thead>
                        <th width="3%">ลำดับ</th>
                        <th>จำนวนตัว</th>
                        <th>ยอดเงินที่ร้านคืนจริง</th>
                        <th>ราคา/ชิ้น (หยวน)</th>
                        <th>ราคารวม (หยวน)</th>
                        <th>ค่ารถ (หยวน)</th>
                        <th>ยอดรวม (หยวน)</th>
                        <th>ยอดรวม (บาท)</th>
                        <th>สถานะ</th>
                        <th>Action</th>
                        <th>หมายเหตุ</th>
                    </thead>
                    
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><input style="text-align: right;" name="return_quantity" id="return_quantity" style="width: 80px;"></td>
                            <td><input style="text-align: right;" name="return_yuan" id="return_yuan" style="width: 130px;"></td>
                            <td><input style="text-align: right;background: #d3d3d3;" name="backshop_price" id="backshop_price" style="width: 130px;" readonly></td>
                            <td><input style="text-align: right;background: #d3d3d3;" name="total" id="total" style="width: 130px;" readonly></td>
                            <td><input style="text-align: right;background: #d3d3d3;" name="tran_cost" id="tran_cost" style="width: 130px;" readonly></td>
                            <td><input style="text-align: right;background: #d3d3d3;" name="yuan" id="yuan" style="width: 130px;" readonly></td>
                            <td><input style="text-align: right;background: #d3d3d3;" name="baht" id="baht" style="width: 130px;" readonly></td>
                            <td id="status"></td>
                            <td><div id="action"></div></td>
                            <td><input id="remark"></td>
                        </tr>
                    </tbody>
                    
                    <tfoot>
                        <tr style="text-align: right;">
                            <td>รวม</td>
                            <td id="total_return_quantity"></td>
                            <td id="total_return_yuan"></td>
                            <td></td>
                            <td id="total_total"></td>
                            <td id="total_tran_cost"></td>
                            <td id="total_yuan"></td>
                            <td id="total_baht"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <input type="hidden" name="oid" id="dialog_oid" value="">
                <input type="hidden" name="opid" id="dialog_opid" value="">
                <input type="hidden" name="cid" id="dialog_cid" value="">
                <input type="hidden" name="backshop_quantity" id="backshop_quantity" value="">
                <input type="hidden" name="backshop_price" id="backshop_price" value="">
                <input type="hidden" name="rate" id="dialog_rate" value="">
			</div>

			<div class="console" style="text-align:center;padding:10px">
				<button class="saveButton" type="submit" value="Submit">บันทึก</button>
				<button class="backButton" type="button" onclick="showShopReturnDialog()">กลับ</button>
			</div>

			</form>
		</div>
</div>

<script>
    $(document).ready(function() {
        $("#return_quantity").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and . and f5
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 116, 190]) !== -1 ||
                 // Allow: Ctrl+A
                (e.keyCode == 65 && e.ctrlKey === true) ||
                 // Allow: Ctrl+C
                (e.keyCode == 67 && e.ctrlKey === true) ||
                 // Allow: Ctrl+X
                (e.keyCode == 88 && e.ctrlKey === true) ||
                 // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                    // let it happen, don't do anything
                    return;
                }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
            }
        });

        $("#return_quantity").keyup(function (e) {
            var total_quan = 0;
            var total_yuan = 0;
            var total_cost = 0;
            var total_grand_yuan = 0;
            var total_grand_baht = 0;
            var rate = document.getElementById('dialog_rate').value;
             
            $('#returnDialog tbody').each( function () {
                //get value
                quan = Number($(this).find('input').eq(0).val());
                price = Number($(this).find('input').eq(2).val());
                tran_cost = Number($(this).find('input').eq(4).val());
                total = quan*price;
                grand_yuan = total+tran_cost;
                grand_baht = grand_yuan*rate;
                
                //display
                $(this).find('input').eq(3).val(total.toFixed(2));
                $(this).find('input').eq(5).val(grand_yuan.toFixed(2));
                $(this).find('input').eq(6).val(grand_baht.toFixed(2));

                //summary
                total_quan += quan;
                total_yuan += (total);
                total_cost += (tran_cost);

                total_grand_yuan += (total+tran_cost);
                total_grand_baht += (grand_yuan*rate);
            });

            //display
            $('#returnDialog tfoot').find('td').eq(1).text(total_quan);
            $('#returnDialog tfoot').find('td').eq(4).text(total_yuan.toFixed(2));
            $('#returnDialog tfoot').find('td').eq(5).text(total_cost.toFixed(2));
            $('#returnDialog tfoot').find('td').eq(6).text(grand_yuan.toFixed(2));
            $('#returnDialog tfoot').find('td').eq(7).text(grand_baht.toFixed(2));

        });

        $("#return_quantity").click(function (){
            if ($(this).val()==0) {
                $(this).val('');
            }
        });
    });
</script>