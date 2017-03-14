<?php
$formcode = "manage-page";

include '../connect.php';
include '../session.php';
include '../permission.php';

if (!isViewPermitted($formcode)) {
	header('Location: ../index.php?error_code=view_not_permitted');
}

$cat = 'featured';

if(isset($_GET['cat'])){
	$cat = $_GET['cat'];
}


?>
<!DOCTYPE html>
<html>
<head>
	<title>Manage Page</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/cargo.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
</head>
<body style="padding:10px;">
<?php if ($cat=='group') { ?>
	<h1><a href="index.php">Manage Page</a></h1>
	<h3><a href="../index.php">&larr; Back</a></h3><br>
	<div class="menu">
	<?php 
		if (isAddPermitted($formcode)) {
			echo '<i class="material-icons" data-toggle="modal" data-target="#addGroup" title="Add">add_circle</i>';
		}
	?>
		<i class="material-icons" onclick="window.print();" title="Print">print</i>
		<i class="material-icons" data-toggle="modal" data-target="#searchGroup"  title="Search">find_in_page</i>
	</div>
	<ul class="nav nav-tabs">
		<li role="presentation" <?php if($cat=='featured'){ echo 'class="active"';} ?> ><a href="index.php?cat=featured">สินค้าแนะนำ</a></li>
		<li role="presentation" <?php if($cat=='men'){ echo 'class="active"';} ?> ><a href="index.php?cat=men">สินค้าสุภาพบุรุษ</a></li>
		<li role="presentation" <?php if($cat=='women'){ echo 'class="active"';} ?> ><a href="index.php?cat=women">สินค้าสุภาพสตรี</a></li>
		<li role="presentation" <?php if($cat=='group'){ echo 'class="active"';} ?> ><a href="index.php?cat=group">หมวดหมู่สินค้า</a></li>
	</ul>
	<br />
	<table class="detail">
		<tr>
			<th>#</th>
			<th>รูป</th>
			<th>ชื่อหมวดสินค้า</th>
			<th>ชื่อ Icon</th>
			<th width="400">ลิงค์หมวดสินค้า</th>
			<th>การกระทำ</th>
		</tr>
<?php

	$select_str = "select * from website_featured_cate where 1 ";


	if ($_REQUEST['search_group_name'] != '') { $select_str .= "and featured_cate_name like '%".$_REQUEST['search_group_name']."%' "; }
	if ($_REQUEST['search_group_url']!= '') 	{ $select_str .= "and featured_cate_link = '".$_REQUEST['search_group_url']."' "; 	}

	$search_str = "";
	if ($_REQUEST['search_group_name'] != '') { $search_str .= "&search_group_name=".$_REQUEST['search_group_name']; }
	if ($_REQUEST['search_group_url']!= '') 	{ $search_str .= "&search_group_url=".$_REQUEST['search_group_url']; 	}


	//###### Calculate Page ######
	$objQuery = mysql_query($select_str) or die ("Error Query [".$select_str."]");
	$Num_Rows = mysql_num_rows($objQuery);

	$Per_Page = 10; 
	
	if (isset($_GET["Page"])) {
		$Page = $_GET["Page"];
	}else{
		$Page=1;
	}

	$Prev_Page = $Page-1;
	$Next_Page = $Page+1;

	$Page_Start = (($Per_Page*$Page)-$Per_Page);
	if($Num_Rows<=$Per_Page)
	{
		$Num_Pages =1;
	}
	else if(($Num_Rows % $Per_Page)==0)
	{
		$Num_Pages =($Num_Rows/$Per_Page) ;
	}
	else
	{
		$Num_Pages =($Num_Rows/$Per_Page)+1;
		$Num_Pages = (int)$Num_Pages;
	}
	//###########################

	$select_str .= "order by featured_cate_id desc ";
	$select_str .= "limit $Page_Start , $Per_Page ";

	//echo $select_str;
	$select_group = mysql_query($select_str);

	if (mysql_num_rows($select_group)>0) {
		$current_row = 1;
		while ($row = mysql_fetch_array($select_group)) {
		?>
				<tr>
					<td><?php echo $current_row; ?></td>
					<td><img class="img img-thumb" style="width:50px;height:50px;" src="<?php echo $row['featured_cate_img']; ?>"></td>
					<td><?php echo $row['featured_cate_name']; ?></td>
					<td><?php echo $row['featured_cate_text_icon']; ?></td>
					<td width="400"><?php echo $row['featured_cate_link']; ?></td>
					<td>
					<?php
						if (isActionPermitted($formcode)) {
							echo'<a href="update_group.php?group_id='.$row['featured_cate_id'].'"><button>
							<span class="glyphicon glyphicon-edit"></span> แก้ไข</button></a>
							<button onclick="deleteGroup('.$row['featured_cate_id'].');">
							<span class="glyphicon glyphicon-remove"></span> ลบ</button>';
						}
					?>
						
					</td>
				</tr>
		<?php
		$current_row++;
		}
	}
?>

	</table>
	<br>
	<div class="paging"> 
		หน้า
<?php 
	if($Prev_Page)
	{
		echo " <a href='$_SERVER[SCRIPT_NAME]?cat=".$cat."&Page=$Prev_Page".$search_str."'><span class='glyphicon glyphicon-chevron-left'></span></a> ";
	}

	for($i=1; $i<=$Num_Pages; $i++){
		if($i != $Page)
		{
			echo "<a href='$_SERVER[SCRIPT_NAME]?cat=".$cat."&Page=$i".$search_str."'>$i</a>";
		}
		else
		{
			echo "<b> $i </b>";
		}
	}
	if($Page!=$Num_Pages)
	{
		echo " <a href ='$_SERVER[SCRIPT_NAME]?cat=".$cat."&Page=$Next_Page".$search_str."'><span class='glyphicon glyphicon-chevron-right'></span></a> ";
	}
?>

	</div>
	<div class="results">
		<table>
			<tr>
					<td><b>จำนวนรายการทั้งหมด</b></td>
					<td class="normal"><b><?php echo mysql_num_rows($select_group)."/".mysql_num_rows($objQuery); ?></b></td>
			</tr>
		</table>
	</div>

<?php }else{ ?>
	<h1><a href="index.php">Manage Page</a></h1>
	<h3><a href="../index.php">&larr; Back</a></h3><br>
	<div class="menu">
		<i class="material-icons" data-toggle="modal" data-target="#addItem" title="Add">add_circle</i>
		<i class="material-icons" onclick="window.print();" title="Print">print</i>
		<i class="material-icons" data-toggle="modal" data-target="#searchItem"  title="Search">find_in_page</i>
	</div>
	<ul class="nav nav-tabs">
		<li role="presentation" <?php if($cat=='featured'){ echo 'class="active"';} ?> ><a href="index.php?cat=featured">สินค้าแนะนำ</a></li>
		<li role="presentation" <?php if($cat=='men'){ echo 'class="active"';} ?> ><a href="index.php?cat=men">สินค้าสุภาพบุรุษ</a></li>
		<li role="presentation" <?php if($cat=='women'){ echo 'class="active"';} ?> ><a href="index.php?cat=women">สินค้าสุภาพสตรี</a></li>
		<li role="presentation" <?php if($cat=='group'){ echo 'class="active"';} ?> ><a href="index.php?cat=group">หมวดหมู่สินค้า</a></li>
	</ul>
	<br />
	<table class="table table-bordered">
		<tr>
			<th>#</th>
			<th>รูป</th>
			<th>ชื่อสินค้า</th>
			<th>ลิงค์สินค้า</th>
			<th>ราคา</th>
			<th>การกระทำ</th>
		</tr>
<?php

	$select_str = "select * from website_featured_item where featured_item_type = '$cat' ";

	if(isset($_POST['search_item'])){
		if ($_POST['search_item_name'] != '') { $select_str .= "and featured_item_name like '%".$_POST['search_item_name']."%' "; }
		if ($_POST['search_item_url']!= '') 	{ $select_str .= "and featured_item_link = '%".$_POST['search_item_url']."%' "; 	}
		if ($_POST['search_item_price']!= '') { $select_str .= "and featured_item_price = '".$_POST['search_item_price']."' "; 		}
	}

	//###### Calculate Page ######
	$objQuery = mysql_query($select_str) or die ("Error Query [".$select_str."]");
	$Num_Rows = mysql_num_rows($objQuery);

	$Per_Page = 10; 
	
	if (isset($_GET["Page"])) {
		$Page = $_GET["Page"];
	}else{
		$Page=1;
	}

	$Prev_Page = $Page-1;
	$Next_Page = $Page+1;

	$Page_Start = (($Per_Page*$Page)-$Per_Page);
	if($Num_Rows<=$Per_Page)
	{
		$Num_Pages =1;
	}
	else if(($Num_Rows % $Per_Page)==0)
	{
		$Num_Pages =($Num_Rows/$Per_Page) ;
	}
	else
	{
		$Num_Pages =($Num_Rows/$Per_Page)+1;
		$Num_Pages = (int)$Num_Pages;
	}
	//###########################

	$select_str .= "order by featured_item_id desc ";
	$select_str .= "limit $Page_Start , $Per_Page ";

	//echo $select_str;
	$select_page = mysql_query($select_str);

	if (mysql_num_rows($select_page)>0) {
		$current_row = 1;
		while ($row = mysql_fetch_array($select_page)) {
		?>
				<tr>
					<td><?php echo $current_row; ?></td>
					<td><img class="img img-thumb" style="width:50px;height:50px;" src="<?php echo $row['featured_item_img']; ?>"></td>
					<td><?php echo $row['featured_item_name']; ?></td>
					<td><?php echo $row['featured_item_link']; ?></td>
					<td><?php echo number_format($row['featured_item_price'],2); ?></td>
					<td>
					<?php
						if (isActionPermitted($formcode)) {
							echo'<a href="update.php?page_id='.$row['featured_item_id'].'&cat='.$row['featured_item_type'].'"><button >
							<span class="glyphicon glyphicon-edit"></span> แก้ไข</button></a>
							<button onclick="deleteItem('.$row['featured_item_id'].');">
							<span class="glyphicon glyphicon-remove"></span> ลบ</button>';
						}
					?>
						
					</td>
				</tr>
		<?php
		$current_row++;
		}
	}
?>

	</table>
	<br>
	<div class="paging"> 
		หน้า
<?php 
	if($Prev_Page)
	{
		echo " <a href='$_SERVER[SCRIPT_NAME]?Page=$Prev_Page&cat=$cat'><span class='glyphicon glyphicon-chevron-left'></span></a> ";
	}

	for($i=1; $i<=$Num_Pages; $i++){
		if($i != $Page)
		{
			echo "<a href='$_SERVER[SCRIPT_NAME]?Page=$i&cat=$cat'>$i</a>";
		}
		else
		{
			echo "<b> $i </b>";
		}
	}
	if($Page!=$Num_Pages)
	{
		echo " <a href ='$_SERVER[SCRIPT_NAME]?Page=$Next_Page&cat=$cat'><span class='glyphicon glyphicon-chevron-right'></span></a> ";
	}
?>

	</div>
	<div class="results">
		<table>
			<tr>
					<td><b>จำนวนรายการทั้งหมด</b></td>
					<td class="normal"><b><?php echo mysql_num_rows($select_page)."/".mysql_num_rows($objQuery); ?></b></td>
			</tr>
		</table>
	</div>


<?php } ?>


<?php if ($cat=='group') { ?>
<div class="modal fade" id="addGroup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">เพิ่มหมวดสินค้าใหม่</h4>
			</div>
			<div class="modal-body">
				<form id="signupform" class="form-horizontal" role="form" method="post" >
					<div class="form-group">
							<label class="col-md-3 control-label">ชื่อหมวดสินค้า </label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="group_name" placeholder="ชื่อสินค้า">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-group_name" style="color:red;"></label>
							</div>
					</div>
					<div class="form-group">
							<label class="col-md-3 control-label">ชื่อ Icon หมวดสินค้า </label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="group_text_icon" placeholder="ชื่อ Icon หมวดสินค้า">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-group_text_icon" style="color:red;"></label>
							</div>
					</div>
					
					<div class="form-group">
							<label class="col-md-3 control-label">ลิงค์สินค้า </label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="group_url" placeholder="ลิงค์สินค้า">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-group_url" style="color:red;"></label>
							</div>
					</div>
					<div class="form-group">
							<label class="col-md-3 control-label">ลิงค์รูปสินค้า </label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="group_img" placeholder="ลิงค์รูปสินค้า">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-group_img" style="color:red;"></label>
							</div>
					</div>
					<input type="hidden" id="item_type" value="<?php echo $cat; ?>">
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
				<button type="button" class="btn btn-primary" onclick="addFeaturedGroup();">บันทึก</button>
				<span id="loading_group"></span>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="searchGroup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">ค้นหาหมวดสินค้า</h4>
			</div>
			<div class="modal-body">
				<form action="index.php?cat=<?php echo $cat ?>" method="post">
					<div class="form-group">
						<label for="exampleInputEmail3">ชื่อหมวดสินค้า</label>
						<input type="text" class="form-control" name="search_group_name" placeholder="ชื่อหมวดสินค้า">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword3">ลิงค์หมวดสินค้า</label>
						<input type="text" class="form-control" name="search_group_url" placeholder="ลิงค์หมวดสินค้า">
					</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-default" name="search_group">ค้นหา</button>
					<a href="index.php?cat=<?php echo $cat ?>"><button class="btn btn-default">แสดงทั้งหมด</button></a>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
function deleteGroup(groupID){
	swal({   
		title: 'ต้องการลบหมวดสินค้านี้', 
		text: 'เมื่อลบหมวดสินค้าแล้วจะไม่สามารถกู้คืนได้!',  
		type: 'warning',
		showCancelButton: true,   
		showLoaderOnConfirm: true,   
		closeOnConfirm: false 
	}, 
	function(isConfirm) {   
		if (isConfirm) {
				var req;
				if (window.XMLHttpRequest) {
					req = new XMLHttpRequest();
				}
				else if (window.ActiveXObject) {
					req = new ActiveXObject("Microsoft.XMLHTTP"); 
				}
				else{
					alert("Browser error");
					return false;
				}
				req.onreadystatechange = function()
				{
					if (req.readyState == 4) {
						//alert('deleted');
						swal({
							title: 'ลบหมวดสินค้า',
							text: 'หมวดสินค้าถูกลบแล้ว',
							type: 'success',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'ตกลง',
							closeOnConfirm: false }, 
							function() {
								location.reload();
							}
						);
					}
					else
					{
						//alert('wait');
					}
				}

				req.open("POST", "delete_group.php", true);	// ส่งค่าไปประมวลผลที่ไฟล์ sql.php
				req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				req.send("group_id="+groupID);
				
			} else {     
				swal('Cancelled','Your imaginary file is safe :)','error');
			} 

	});
}

function addFeaturedGroup(){

	var flag=0;

	var name = document.getElementById('group_name').value;
	var url = document.getElementById('group_url').value;
	var img = document.getElementById('group_img').value;
	var text_icon = document.getElementById('group_text_icon').value;

	if (name == "") {
		document.getElementById('help-group_name').innerText = "กรุณากรอกชื่อหมวดสินค้า";
		flag = 1;
	}else{
		document.getElementById('help-group_name').innerText = "";
	}

	if (url == "") {
		document.getElementById('help-group_url').innerText = "กรุณากรอกลิงค์หมวดสินค้า";
		flag = 1;
	}else{
		document.getElementById('help-group_url').innerText = "";
	}

	if (img == "") {
		document.getElementById('help-group_img').innerText = "กรุณากรอกลิงค์รูปหมวดสินค้า";
		flag = 1;
	}else{
		document.getElementById('help-group_img').innerText = "";
	}

	if (text_icon == "") {
		document.getElementById('help-group_text_icon').innerText = "กรุณากรอกลิงค์รูปหมวดสินค้า";
		flag = 1;
	}else{
		document.getElementById('help-group_text_icon').innerText = "";
	}



	if (flag != 1) {
		var req;
		if (window.XMLHttpRequest) {
			req = new XMLHttpRequest();
		}
		else if (window.ActiveXObject) {
			req = new ActiveXObject("Microsoft.XMLHTTP"); 
		}
		else{
			alert("Browser error");
			return false;
		}
		req.onreadystatechange = function()
		{
			if (req.readyState == 4) {
				//alert('deleted');
				var loading = document.getElementById('loading_group');
				loading.innerHTML = '';
				
				swal({
					title: 'เพิ่มหมวดสินค้า',
					text: 'เพิ่มหมวดสินค้าเรียบร้อย',
					type: 'success',
					showCancelButton: false,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'ตกลง',
					closeOnConfirm: false }, 
					function() {
						location.reload();
					}
				);
			}
			else
			{
				var loading = document.getElementById('loading_group');
				loading.innerHTML = '<img src="../images/ajax-loader.gif">';
			}
		}

		req.open("POST", "insert_group.php", true);	// ส่งค่าไปประมวลผลที่ไฟล์ sql.php
		req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		req.send("name="+name+"&url="+url+"&img="+img+"&text_icon="+text_icon);
	}
}

</script>

<?php }else{ ?>

<!-- ### MODAL ### -->
<div class="modal fade" id="addItem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">เพิ่มสินค้าใหม่</h4>
			</div>
			<div class="modal-body">
				<form id="signupform" class="form-horizontal" role="form" method="post" >
					<div class="form-group">
							<label class="col-md-3 control-label">ชื่อสินค้า </label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="item_name" placeholder="ชื่อสินค้า">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-item_name" style="color:red;"></label>
							</div>
					</div>
					
					<div class="form-group">
							<label class="col-md-3 control-label">ลิงค์สินค้า </label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="item_url" placeholder="ลิงค์สินค้า">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-item_url" style="color:red;"></label>
							</div>
					</div>
					<div class="form-group">
							<label class="col-md-3 control-label">ลิงค์รูปสินค้า </label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="item_img" placeholder="ลิงค์รูปสินค้า">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-item_img" style="color:red;"></label>
							</div>
					</div>
					<div class="form-group">
							<label class="col-md-3 control-label">ราคาสินค้า </label>
							<div class="col-md-8">
									<input type="text" class="form-control" id="item_price" placeholder="ราคาสินค้า">
							</div>
							<div class="col-md-offset-3 col-md-8">
								<label class="control-label" id="help-item_price" style="color:red;"></label>
							</div>
					</div>
					<input type="hidden" id="item_type" value="<?php echo $cat; ?>">
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
				<button type="button" class="btn btn-primary" onclick="addFeaturedItem('<?php echo $cat; ?>');">บันทึก</button>
				<span id="loading_item"></span>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="searchItem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">ค้นหาสินค้า</h4>
			</div>
			<div class="modal-body">
				<form action="index.php?cat=<?php echo $cat ?>" method="post">
					<div class="form-group">
						<label for="exampleInputEmail3">ชื่อสินค้า</label>
						<input type="text" class="form-control" name="search_item_name" placeholder="ชื่อสินค้า">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword3">ลิงค์สินค้า</label>
						<input type="text" class="form-control" name="search_item_url" placeholder="ลิงค์สินค้า">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword3">ราคา</label>
						<input type="text" class="form-control" name="search_item_price" placeholder="ราคา">
					</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-default" name="search_item">ค้นหา</button>
					<a href="index.php?cat=<?php echo $cat ?>"><button class="btn btn-default">แสดงทั้งหมด</button></a>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- ### MODAL ### -->

<script type="text/javascript">

function deleteItem(pageID){
	swal({   
		title: 'ต้องการลบสินค้านี้?',   
		text: 'เมื่อลบสินค้าแล้วจะไม่สามารถกู้คืนได้!',   
		type: 'warning',
		showCancelButton: true,
		closeOnConfirm: false,   
		showLoaderOnConfirm: true,
	},
	function(isConfirm) {   
		if (isConfirm) {
			var req;
			if (window.XMLHttpRequest) {
				req = new XMLHttpRequest();
			}
			else if (window.ActiveXObject) {
				req = new ActiveXObject("Microsoft.XMLHTTP"); 
			}
			else{
				alert("Browser error");
				return false;
			}
			req.onreadystatechange = function()
			{
				if (req.readyState == 4) {
					//alert('deleted');
					if (req.responseText == "action_not_permitted") {
						swal({
							title: 'การทำรายการล้มเหลว',
							text: 'คุณไม่ได้รับสิทธ์ในการทำรายการนี้, โปรดติดต่อเจ้าหน้าที่',
							type: 'error',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							confirmButtonText: 'ตกลง',
							closeOnConfirm: false }, 
							function() {
								location.reload();
							}
						);
						return;
					}

					swal({
						title: 'ลบสินค้า',
						text: 'สินค้าถูกลบแล้ว!',
						type: 'success',
						showCancelButton: false,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'ตกลง',
						closeOnConfirm: false }, 
						function() {
							location.reload();
						}
					);
				}
				else
				{

				}
			}

			req.open("POST", "delete.php", true);	// ส่งค่าไปประมวลผลที่ไฟล์ sql.php
			req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			req.send("page_id="+pageID);
			
		} else {     

		} 
	});
}

function addFeaturedItem(cat){

	var flag=0;

	var name = document.getElementById('item_name').value;
	var url = document.getElementById('item_url').value;
	var img = document.getElementById('item_img').value;
	var price = document.getElementById('item_price').value;

	if (name == "") {
		document.getElementById('help-item_name').innerText = "กรุณากรอกชื่อสินค้า";
		flag = 1;
	}else{
		document.getElementById('help-item_name').innerText = "";
	}

	if (url == "") {
		document.getElementById('help-item_url').innerText = "กรุณากรอกลิงค์สินค้า";
		flag = 1;
	}else{
		document.getElementById('help-item_url').innerText = "";
	}

	if (img == "") {
		document.getElementById('help-item_img').innerText = "กรุณากรอกลิงค์รูปสินค้า";
		flag = 1;
	}else{
		document.getElementById('help-item_img').innerText = "";
	}

	if (price == "") {
		document.getElementById('help-item_price').innerText = "กรุณากรอกราคาสินค้า";
		flag = 1;
	}else{
		document.getElementById('help-item_price').innerText = "";
	}

	if (flag != 1) {
		var req;
		if (window.XMLHttpRequest) {
			req = new XMLHttpRequest();
		}
		else if (window.ActiveXObject) {
			req = new ActiveXObject("Microsoft.XMLHTTP"); 
		}
		else{
			alert("Browser error");
			return false;
		}
		req.onreadystatechange = function()
		{
			if (req.readyState == 4) {
				//alert('deleted');
				if (req.responseText == "add_not_permitted") {
					swal({
						title: 'การทำรายการล้มเหลว',
						text: 'คุณไม่ได้รับสิทธ์ในการทำรายการนี้, โปรดติดต่อเจ้าหน้าที่',
						type: 'error',
						showCancelButton: false,
						confirmButtonColor: '#3085d6',
						confirmButtonText: 'ตกลง',
						closeOnConfirm: false }, 
						function() {
							location.reload();
						}
					);
					return;
				}
				
				var loading = document.getElementById('loading_item');
				loading.innerHTML = '';

				swal({
					title: 'เพิ่มสินค้า',
					text: 'เพิ่มสินค้าเรียบร้อย!',
					type: 'success',
					showCancelButton: false,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'ตกลง',
					closeOnConfirm: false }, 
					function() {
						location.reload();
					}
				);
			}
			else
			{
				var loading = document.getElementById('loading_item');
				loading.innerHTML = '<img src="../images/ajax-loader.gif">';
			}
		}

		req.open("POST", "insert.php", true);	// ส่งค่าไปประมวลผลที่ไฟล์ sql.php
		req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		req.send("name="+name+"&url="+url+"&img="+img+"&price="+price+"&cat="+cat);
	}
}
</script>

<?php } ?>

<script src="../js/sweetalert2.min.js"></script> 
<link rel="stylesheet" type="text/css" href="../js/sweetalert2.css">
</body>
</html>