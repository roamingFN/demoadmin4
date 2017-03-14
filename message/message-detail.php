<!DOCTYPE html>
<?php
	session_start ();
	if (! isset ( $_SESSION ['ID'] )) {
		header ( "Location: ../login.php" );
	}

	include '../database.php';
	date_default_timezone_set ( 'Asia/Bangkok' );
	$orderby = "asc";
	$opt = $_GET['opt'];
	$order_id = $_GET['id'];
	$customer_id = $_GET['cid'];



	// echo $order_id;
	// echo "- - - - - - - - -";
	// echo $customer_id;
	// echo "- - - - - - - - -";
	// // echo $opt;
	// echo "- - - - - - - - -";

	if ($_GET['opt'] == 'oid') {
		$order_id = $_GET['id'];
		// echo "oid = ";
		// echo $order_id;
		// echo "- - - - - - - - -";
		$totalMessgeLog = "select cs.customer_email, tml.*,u.email from total_message_log tml left join user u on u.userid=tml.user_id INNER JOIN customer cs on tml.customer_id = cs.customer_id where tml.customer_id  = '$customer_id' and tml.order_id =  '$order_id'  order by tml.message_date";
		// echo $totalMessgeLog;

	} else {
		// echo "pid = ";
	 	$package_id = $_GET['id'];
		// echo $package_id;
		// echo "- - - - - - - - -";
		$totalMessgeLog = "select cs.customer_email, tml.*,u.email from total_message_log tml left join user u on u.userid=tml.user_id INNER JOIN customer cs ON cs.customer_id = tml.customer_id where tml.customer_id = '$customer_id' and tml.packageid = '$package_id'  order by tml.message_date";
		// echo $totalMessgeLog;
	 }


	$result = $con->query ( $totalMessgeLog );
	$totalMessageLogData = array ();
	while ( $row = $result->fetch_array ( MYSQL_ASSOC ) ) {
		$totalMessageLogData [] = $row;
	}
?>
<html>
<head>
<title>Message detail</title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" href="../css/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="../css/cargo.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
	rel="stylesheet">
<link
	href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300'
	rel='stylesheet' type='text/css'>

<link href="./index.css" rel="stylesheet">
<link rel="stylesheet" href="../css/chatstyle.css" />
<link rel="stylesheet" href="../css/cargo.css" />
<style>
i {
	color: #0070c0;
}

.paging a {
	text-decoration: underline;
}

a.current-page {
	text-decoration: none;
}

button, .button {
	color: #fff;
}

a {
	color: #cc0099;
}

th {
	background: #cc0099;
	border-right: 1px solid #663300;
}

.undivide th, .detail-order-complete th {
	background: #0070c0;
}

.detail-order-complete th {
	border-right: 1px solid #0070c0;
	color: #fff;
	padding: 4px;
	text-align: center;
	width: 127px !important;
}

.order-button:hover {
	color: #0070c0;
}

.wrap th {
	width: 32%;
}

#orderComplete table {
	width: 60%;
}

.detail-order-complete {
	box-shadow: none !important;
	display: block !important;
	max-height: 400px !important;
	position: relative !important;
	width: 98% !important;
	overflow-y: auto;
}

#search input {
	background: #e4f1fb none repeat scroll 0 0;
	border: 0 none;
	color: #7F7F7F;
	float: left;
	font: 12px 'Helvetica', 'Lucida Sans Unicode', 'Lucida Grande',
		sans-serif;
	height: 20px;
	margin: 0;
	padding: 10px;
	transition: background 0.3s ease-in-out 0s;
	width: 300px;
}

#search button {
	background: url("images/search.png") no-repeat scroll center center
		#0070c0;
	cursor: pointer;
	height: 40px;
	text-indent: -99999em;
	transition: background 0.3s ease-in-out 0s;
	width: 40px;
	border: 2px solid #fff;
}

#search button:hover {
	background-color: #021828;
}

.searchBox {
	margin-right: 24px;
	float: right;
}

.tracking_thai {
	background-color: #ddd9c3;
}

table.detail-order-complete  tr:hover {
	background: #b2dfdb none repeat scroll 0 0 !important;
}

.trackingTH {
	margin-top: 1em;
}

.trackingTH label {
	padding: 0 5px;
}

.trackingTH div {
	margin: 5px 0px;
}

form {
	display: block;
}

.material-icons {
	font-size: 18px;
	cursor: pointer;
}

i {
	color: #f00;
	padding-left: 5px;
	vertical-align: middle;
}

.in {
	border: 1px solid #000;
	width: 90%;
	padding: 20px;
}

.content {
	margin: 0 auto;
	width: 1024px;
}

.col_f {
	width: 74px;
	max-width: 74px;
	text-align: center;
	padding: 10px;
}

.col_l {
	width: 175px;
	max-width: 175px;
	text-align: center;
	padding: 10px;
}

.col_subject {
	width: 300px;
	max-width: 300px;
	padding: 10px;
}

.col_content {
	width: 500px;
	max-width: 500px;
	padding: 10px;
}

td h3, td table {
	font-size: 13px !important;
}

td table, td div {
	box-shadow: none;
	border: none !important;
	background: none !important;
	width: auto !important;
	padding: 0 !important;
	margin: 0 !important;
}

tr:hover {
	/*background: #009688;*/
	cursor: default;
}

.section_top .detail {
	width: 1024px;
	background-color: #fff;
	margin: 20px auto;
}



button, .button {
	background: #fff none repeat scroll 0 0;
	border: 1px solid #3949ab;
	border-radius: 4px;
	color: #3949ab;
	font-size: 14px;
	padding: 7px 15px;
}
.wrapper {
    background-color: #fff;
    margin: 0 auto;
    width: 80%;
}
</style>
<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../css/jquery-ui-timepicker-addon.min.css"></script>
<script src="../js/jquery-ui-timepicker-addon.min.js"></script>
<script src="js/ajaxlib.js"></script>
<script src="js/util.js"></script>
<script src="js/packagelib.js"></script>
<script src="js/package_ui_events.js"></script>
<script type="text/javascript">
	$( document ).ready(function() {
			$('#btnBack').on('click',function(){
				window.location.href ='./index.php';
			});
	});
</script>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<script>
	tinymce.init({
		  selector: 'textarea',
		  height: 85,
		  menubar:false,
		  statusbar: false,
		  forced_root_block : "",
		  toolbar: ' styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
		  content_css: '//www.tinymce.com/css/codepen.min.css'
		});

		$(document).ready(function(){

			$('#msgSendBtn').on('click',function(){
				/**
				 check data is not empty
				 send data with ajax (order_id,customer_id,txtMessage)
				 return data when select and redirect class "msgBox-send"
				*/
				var content = tinyMCE.get('txtMessage').getContent();
				var id = $('#msgOrderId').val(); // orderid , packageid
				var opt = $('#msgOpt').val(); 	//opt : (oid,pid)
				var cus_id = $('#msgCusid').val();

				if($.trim(content) != ''){
					//alert("content"+content+"-> Order ID = "+orderId+" Option = "+opt);
				   // editor is empty ...
					$.post("./message-do.php",{frmMsgSend:$.trim(content),id:id,opt:opt,cus_id:cus_id},function(res){
						var json = $.parseJSON(res);
						console.log(json);
						var html='<div class="msgBox-send-right">';
						    html+='<div class="msgBox-send-content">';
				        	html+='<div class="triangle-isosceles right">'+json[0].content+'</div>';
				    		html+='</div>';
				    		html+='<div class="msgBox-send-info">';
				            html+='<div class="msgBox-username">'+json[0].email+'</div>';
				        	html+='<div class="msgBox-date">'+json[0].message_date+'</div>';
				    		html+='</div>';
							html+='</div>';

							$('.msgBox-send').append(html);
							$(".msgBox-send").animate({ scrollTop: 20000}, 1000);
							var tinymce_editor_id = 'txtMessage';
							tinymce.get(tinymce_editor_id).setContent('');

				  	});
				}

			});
		});

	</script>
</head>
<body>
	<h1>
		<a href="./index.php">รายละเอียดหน้าข้อความ</a>
	</h1>
	<h3>
		<a href="index.php">&larr; Back</a>&nbsp;<a href="../index.php">&larr;
			Home</a>
	</h3>
	<br />
	<!-- <header></header> -->
	<div class="wrapper">
		<div class="msgBox">
			<div class="msgBox-header">
				<h3>รายการข้อความ</h3>
			</div>
			<div class="msgBox-send">
			<?php if(count($totalMessageLogData)>0){ ?>
			<?php foreach($totalMessageLogData as $val){?>
			<?php if($val['user_id']==0){
				// echo "<pre>";
				// print_r($val);
				// echo "</pre>";
				?>

			<div class="msgBox-send-left">
				<div class="msgBox-send-info">
					<div class="msgBox-username"><?php echo $val['customer_email'];?></div>
					<div class="msgBox-date"><?php echo $val['message_date'];?></div>
				</div>
				<div class="msgBox-send-content">
					<div class="triangle-isosceles left"><?php echo $val['content'];?></div>
				</div>
			</div>
			
			<?php
					} else {
			?>
			
			<div class="msgBox-send-right">
				<div class="msgBox-send-content">
					<div class="triangle-isosceles right"><?php echo $val['content'];?></div>
				</div>
				<div class="msgBox-send-info">
					<div class="msgBox-username">
						<?php
							$cs_email = array ();
							$c_id = $val['customer_id'];
							// $sql_us_email = "";
							$us_id = $_SESSION['USERID'];
							$sql_cs_email = "SELECT us.email FROM total_message_log tml INNER JOIN user us ON tml.user_id = us.userid WHERE tml.user_id = '$us_id' group by us.email";
							$result_email = $con->query ( $sql_cs_email );
							$sql_cs_email_data = array ();
							while ( $row = $result_email->fetch_array ( MYSQL_ASSOC ) ) {
								$sql_cs_email_data [] = $row;
							}
							echo $sql_cs_email_data[0]['email'];
						?>
					</div>
					<div class="msgBox-date"><?php echo $val['message_date'];?></div>
				</div>
			</div>
			<?php }?>
			<?php }?>
			<?php }?>
		</div>


		</div>
		<form action="" onSubmit="return false;" id="frmMsgSend">
			<div class="msgSend">
				<div class="msgSend-message">
					<textarea name="txtMessage" id="txtMessage"></textarea>
				</div>
			</div>

			<input type="hidden" name="msgOrderId" id="msgOrderId"
				value="<?php echo $order_id;?>" />
			<input type="hidden" name="msgOpt" id="msgOpt" value="<?php echo $opt; ?>">
			<input type="hidden" name="msgCusid" id="msgCusid" value="<?php echo $customer_id; ?>">
			<div class="msgSend-btn">
				<button id="msgSendBtn">Send</button>
			</div>
		</form>
		<!-- end message box -->





		<!-- Insert Chat by Hack -->

		<!-- End insert Chat by Hack -->
	</div>

</body>
</html>