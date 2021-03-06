<!-- bookRoom -->
<html>
	<head>
		<title>eLax民宿</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<!-- META 的功能僅是用來註明這些網頁資訊，且提供給瀏覽器或是搜尋引擎，並非是要給寫給瀏覽網頁的＂人＂看的內容。-->
        
		<!-- 設計自己blog的瀏覽器網址icon圖示 -->
		<link rel = "Shortcut Icon" type = "image/x-icon" href = "customer_picture/icon.ico" />
      
		<!-- 加載Bootstrap --> 
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		
		<!-- 加載css、js -->
		<link rel="stylesheet" type="text/css" href="customer_frame_css.css">
		<script src="customer_frame_js.js"></script>
		
		<!-- 加入以下css即可修改最上面背景圖片 -->
		<!-- 務必要加在載入customer_frame_css.css之後才可以覆蓋掉-->	
		<style type = "text/css">
			.jumbotron{
				/*background-color: white; */
				background-image: url('customer_picture/homepage3.jpg');	/* 最上面的背景 在此修改圖片* //* 建議大小為828x315 */
				background-repeat:no-repeat;
				opacity: 0.8;
				margin: 0;
				padding: 0;
				border: 0;
				padding-left: 3%;
				/*color: #2e2d4d;*/
				height: 70%;
				background-size: cover;
				text-align: center;	/*字體水平置中*/
				vertical-align: middle;/*nouse*/
				/*line-height: 70%;*/
			}
		</style>
		
		<script>
		</script>
	</head>
	<body>
	<div class="container-fluid">
	<div class="row content">
		<div class="jumbotron wrapper">
			<div class="textCss titleCenter">
				<h1>Re<br>Lax</h1>
				<h6>Homestay in Hualien</h6>
			</div>
		</div>
		<div class="col-md-12 center">
			<nav class="navbar navbar-default navbar-fixed-top">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="customerHomepage.html">放輕鬆民宿</a>
					</div>
					<div class="collapse navbar-collapse" id="myNavbar">
						<ul class="nav navbar-nav">
							<li><a href="houseInfo.html">民宿資訊</a></li>
							<li><a href="roomInfo.html">房間資訊</a></li>
							<li class="active"><a href="bookRoom.html">我要訂房</a></li>
							<li><a href="checkBookInfo.html">查看訂單</a></li>
							<li><a href="surround.html">附近導覽</a></li>
							<li><a href="customerComment.php">使用者評論</a></li>
						</ul>
					</div>
				</div>
			</nav>
			<div class="row-md-8 center">
<!-- ======================================================================================================================================================= -->
			<?php 
				include "db_conn.php";
				$customerName = $_POST['customerName'];
				$cellphone = $_POST['cellphone'];
				$accountNumber = $_POST['accountNumber'];
				
				$sql = "select max(customerID) from customer"; 
				$rs = mysqli_query($db,$sql); 
				$row = mysqli_fetch_array($rs); 
				$maxrow = $row["0"];
				$maxrow = $maxrow+1;
				$sql = "select * from customer"; 
				$result = mysqli_query($db, $sql);
				if($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						if ($row["customerName"]==$customerName && $row["cellphone"]==$cellphone && $row["accountNumber"]== $accountNumber){
							$customerID = $row["customerID"];
							break;
						}	
						$customerID = $maxrow;
					}						
				}
					
				$checkInDate = $_POST['checkInDate'];
				$checkOutDate = $_POST['checkOutDate'];
				$bigAmount = $_POST['bigAmount'];
				$smallAmount = $_POST['smallAmount'];
				$customerNumber = $_POST['customerNumber'];
				
				
				$sql = "select max(orderID) from roomorder"; 
				$rs = mysqli_query($db,$sql); 
				$row = mysqli_fetch_array($rs); 
				$row["0"]++;								
				$orderID = $row["0"];
				$payDeposit = '否';
				$payBalance = '否';
				$day = (strtotime($checkOutDate) - strtotime($checkInDate)) / 86400;	//計算天數
				$totalPrice = 0;//($bigAmount*2000 + $smallAmount*1800)*$day;	訂單金額 = (大間數量*2000 + 小間數量*1800) * 天數
				$bigAmountTotal = 0;
				$smallAmountTotal = 0;
				
				$searchEqual = "SELECT * FROM roomorder WHERE checkInDate = '$checkInDate' AND checkOutDate = '$checkOutDate'";
				$result = mysqli_query($db, $searchEqual);
				
				if(mysqli_num_rows($result) > 0) {
				// output data of each row
					while($row = mysqli_fetch_assoc($result)) {
						$bigAmountTotal += $row["bigAmount"];
						$smallAmountTotal += $row["smallAmount"];
					}
				}
				if($bigAmount + $bigAmountTotal > 2 && $smallAmount + $smallAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段大間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal > 2){
					echo "<script type='text/javascript'>alert('該時段大間和小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				
				//原退房在新增的入住後 原退房在新增的退房前
				$searchLast = "SELECT * FROM roomorder WHERE checkInDate < '$checkInDate' AND checkOutDate < '$checkInDate' AND checkOutDate > '$checkOutDate'";
				$result = mysqli_query($db, $searchLast);
				
				if(mysqli_num_rows($result) > 0) {
				// output data of each row
					while($row = mysqli_fetch_assoc($result)) {
						$bigAmountTotal += $row["bigAmount"];
						$smallAmountTotal += $row["smallAmount"];
					}
				}
				if($bigAmount + $bigAmountTotal > 2 && $smallAmount + $smallAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段大間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal > 2){
					echo "<script type='text/javascript'>alert('該時段大間和小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				
				$searchNext = "SELECT * FROM roomorder WHERE checkInDate > '$checkInDate' AND checkInDate < '$checkOutDate' AND checkOutDate > '$checkOutDate'";
				$result = mysqli_query($db, $searchNext);
				
				if(mysqli_num_rows($result) > 0) {
				// output data of each row
					while($row = mysqli_fetch_assoc($result)) {
						$bigAmountTotal += $row["bigAmount"];
						$smallAmountTotal += $row["smallAmount"];
					}
				}
				if($bigAmount + $bigAmountTotal > 2 && $smallAmount + $smallAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段大間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal > 2){
					echo "<script type='text/javascript'>alert('該時段大間和小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				
				//入房在新增的入住前 離開在新增的退房後
				$searchLong = "SELECT * FROM roomorder WHERE checkInDate < '$checkInDate' AND checkOutDate > '$checkOutDate'";
				$result = mysqli_query($db, $searchLong);
				
				if(mysqli_num_rows($result) > 0) {
				// output data of each row
					while($row = mysqli_fetch_assoc($result)) {
						$bigAmountTotal += $row["bigAmount"];
						$smallAmountTotal += $row["smallAmount"];
					}
				}
				if($bigAmount + $bigAmountTotal > 2 && $smallAmount + $smallAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段大間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal > 2){
					echo "<script type='text/javascript'>alert('該時段大間和小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				
				$searchNext1 = "SELECT * FROM roomorder WHERE checkInDate > '$checkInDate' AND checkOutDate < '$checkOutDate'";
				$result = mysqli_query($db, $searchNext1);
				
				if(mysqli_num_rows($result) > 0) {
				// output data of each row
					while($row = mysqli_fetch_assoc($result)) {
						$bigAmountTotal += $row["bigAmount"];
						$smallAmountTotal += $row["smallAmount"];
					}
				}
				if($bigAmount + $bigAmountTotal > 2 && $smallAmount + $smallAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段大間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal > 2){
					echo "<script type='text/javascript'>alert('該時段大間和小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				
				$searchNext2 = "SELECT * FROM roomorder WHERE checkInDate = '$checkInDate' AND checkOutDate > '$checkOutDate'";
				$result = mysqli_query($db, $searchNext2);
				
				if(mysqli_num_rows($result) > 0) {
				// output data of each row
					while($row = mysqli_fetch_assoc($result)) {
						$bigAmountTotal += $row["bigAmount"];
						$smallAmountTotal += $row["smallAmount"];
					}
				}
				if($bigAmount + $bigAmountTotal > 2 && $smallAmount + $smallAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段大間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal > 2){
					echo "<script type='text/javascript'>alert('該時段大間和小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				
				$searchNext3 = "SELECT * FROM roomorder WHERE checkInDate = '$checkInDate' AND checkOutDate < '$checkOutDate'";
				$result = mysqli_query($db, $searchNext3);
				
				if(mysqli_num_rows($result) > 0) {
				// output data of each row
					while($row = mysqli_fetch_assoc($result)) {
						$bigAmountTotal += $row["bigAmount"];
						$smallAmountTotal += $row["smallAmount"];
					}
				}
				if($bigAmount + $bigAmountTotal > 2 && $smallAmount + $smallAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段大間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal > 2){
					echo "<script type='text/javascript'>alert('該時段大間和小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				
				$searchNext4 = "SELECT * FROM roomorder WHERE checkInDate < '$checkInDate' AND checkOutDate = '$checkOutDate'";
				$result = mysqli_query($db, $searchNext4);
				
				if(mysqli_num_rows($result) > 0) {
				// output data of each row
					while($row = mysqli_fetch_assoc($result)) {
						$bigAmountTotal += $row["bigAmount"];
						$smallAmountTotal += $row["smallAmount"];
					}
				}
				if($bigAmount + $bigAmountTotal > 2 && $smallAmount + $smallAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段大間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal > 2){
					echo "<script type='text/javascript'>alert('該時段大間和小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				
				$searchNext5 = "SELECT * FROM roomorder WHERE checkInDate > '$checkInDate' AND checkOutDate = '$checkOutDate'";
				$result = mysqli_query($db, $searchNext5);
				
				if(mysqli_num_rows($result) > 0) {
				// output data of each row
					while($row = mysqli_fetch_assoc($result)) {
						$bigAmountTotal += $row["bigAmount"];
						$smallAmountTotal += $row["smallAmount"];
					}
				}
				if($bigAmount + $bigAmountTotal > 2 && $smallAmount + $smallAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段大間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal <= 2){
					echo "<script type='text/javascript'>alert('該時段小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				if($smallAmount + $smallAmountTotal > 2&& $bigAmount + $bigAmountTotal > 2){
					echo "<script type='text/javascript'>alert('該時段大間和小間房間已滿'); location.href='bookRoom.html'</script>";
					mysqli_close($db);
				}
				
				
				
				$dateday = array();
				for($i=0; $i<$day; $i++){
						$dateday[$i] = date("w", strtotime($checkInDate)+(86400*$i));	
						if($dateday[$i] == 6 || $dateday[$i] == 0){
							$totalPrice = $totalPrice+($bigAmount*2200 + $smallAmount*2000);
						}
						else{
							$totalPrice = $totalPrice+($bigAmount*2000 + $smallAmount*1800);
						}
					}
					
					
				$query = "SELECT * FROM customer where customerName = '$customerName' and cellphone = '$cellphone'  and  accountNumber = '$accountNumber'";
						if($stmt =  $db->query($query)){
							if(mysqli_fetch_row($stmt)<1){
								$query = ("insert into customer value(?,?,?,?)");
								$stmt = $db->prepare($query);
								$stmt->bind_param("isss",$maxrow,$customerName,$cellphone,$accountNumber);
								$stmt->execute();
			
							}
								
						}
						
					$query = ("insert into roomorder value(?,?,?,?,?,?,?,?,?,?)");
					$stmt = $db->prepare($query);
					$stmt->bind_param("iiissiissi",$orderID,$bigAmount,$smallAmount,$checkInDate,$checkOutDate,$customerID,$customerNumber,$payDeposit,$payBalance,$totalPrice);
					$stmt->execute();
				
				
				
				
				echo '<br><center><div class="panel panel-primary" style="border:3px #2e2d4d solid; width:90%;"rules="all">';
				echo'<div class="panel-heading">';
				echo'<h3 class="panel-title"><center>訂房成功</center></h3>';
				echo'</div>';
				echo'<div class="panel-body">';
						echo'<table class="table table-striped" style = " width:90%">';
							echo'<tr>';
							echo'<td style="color:red; text-align:center; width:30%">匯款帳號</td>';
							echo'<td>8787-9487-8742-5287(民宿帳號 注:請在三天內匯款不然視同放棄)</td></tr>';
							
							echo'<tr>';
							echo'<td style="text-align:center;">訂房姓名</td>';
							echo'<td>'.$customerName;
							echo'</td></tr>';
							
							echo'<tr>';
							echo'<td style="text-align:center;">電話</td>';
							echo'<td>'.$cellphone;
							echo'</td></tr>';
							
							echo'<tr>';
							echo'<td style="text-align:center;">轉帳帳號</td>';
							echo'<td>'.$accountNumber;
							echo'</td></tr>';
							
							echo'<tr>';
							echo'<td style="text-align:center;">住宿日期</td>';
							echo'<td>'.$checkInDate;
							echo'</td></tr>';
							
							echo'<tr>';
							echo'<td style="text-align:center;">退宿日期</td>';
							echo'<td>'.$checkOutDate;
							echo'</td></tr>';
							
							echo'<tr>';
							echo'<td style="text-align:center;">住宿人數</td>';
							echo'<td>'.$customerNumber;
							echo'</td></tr>';
							
							echo'<tr>';
							echo'<td style="text-align:center;">大型雙人房</td>';
							echo'<td>'.$bigAmount;
							echo'</td></tr>';
							echo'<tr>';
							echo'<td style="text-align:center;">小型雙人房</td>';
							echo'<td>'.$smallAmount;
							echo'</td></tr>';
							
						echo'</table>';

				echo'</div>';
			echo'</div></center>';
				?>	

<!-- ======================================================================================================================================================= -->					
			</div>
		</div>
	</body>
</html>