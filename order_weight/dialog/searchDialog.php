<div class="bgwrap" id="searchDialog">
		<div class="container" style="width:70%">
		  		<div style="text-align:left;">
		  				<div style="padding: 1%;"><h1>Search</h1></div>
		  		</div>
		  		
		  		<form method="get" style="width: 100%">
						<div style="margin-top:10px">
				        		<table style="width:80%;" id="amountTable">
				        				<tbody>
				        						<tr>
				        								<th style="text-align: right;width: 40%;">เลขที่ Order </th>
				        								<td style="text-align: left; padding-left: 5%;width: 60%;"><input id="ono" maxlength="12" style="max-width: 150px"></td>
				        						</tr>
				        						<tr>
				        								<th style="text-align: right;">ประเภทสินค้า </th>
				        								<td style="text-align: left; padding-left: 5%"><select class="search-select" id="pType" style="width: 100%">
				        										<?php
																		foreach ($_pType as $key => $value) {
																				echo '<option value="'.$key.'">'.$value.'</option>';
																		}
																?>
														</select></td>
				        						</tr>
				        				</tbody>
				        		</table>
						</div>

						<div style="text-align:right;padding:10px">
								<a onclick="showSearchDialog();" type="button">Cancel</a>&emsp;
								<button id="searchButton" onclick="searchTracking()" type="button">Search</button>
						</div>

						<div id="containerResult" style="max-height:250px;text-align: left;padding: 3%;overflow-y: scroll;">
								<div id="search-loading" style="display: none;float: left;"><img src="../images/ajax-loader.gif"></div>
								<div id="searchResult" style="float: left;width:100%;"></div>
						</div>
				</form>
		</div>
</div>