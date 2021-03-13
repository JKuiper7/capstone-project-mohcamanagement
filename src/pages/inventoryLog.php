<?php
	function getInclude() {
		$dbHost = "localhost";
		$dbUser = "root";
		$dbPass = "";
		$db = "Overseer";

		return mysqli_connect($dbHost, $dbUser, $dbPass, $db);
	}

	function generateTableData() {
		$conn = getInclude();
		if($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$sql;
		if(!empty ($_POST['inventory'])) {
			$typeT=$_POST['inventory'];
			if($typeT=="all")
				$sql = "SELECT * FROM Items";
			else
				$sql = "SELECT * FROM Items where Type='".$typeT."'";

			$result = $conn->query($sql);

			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					echo "<tr>";
					echo "<td style='text-align:center;'>".$row["ItemName"]."</td>";
					echo "<td style='text-align:center;'>".$row["Par"]."</td>";
					echo "<td style='text-align:center;' colspan='2'><input type=\"number\" id=\"".$row["ItemName"]."\" name=\"".$row["ItemName"]."\" value=0></td>";
					echo "</tr>";
				}
				echo "<tr>";
				echo "<td colspan='4' style='text-align: center;'>";
				echo "<input type='submit' style='background-color: #343131;  color: #969595;'>";
				echo "</td>";
				echo "</tr>";
			}
		}
	}

	// Add types in drop-down menu 
	function generateOptions() {
		if(!empty($_POST['inventory'])) {
			$selected= $_POST['inventory'];
			setcookie("inventoryType", $selected);
		}
		$conn = getInclude();

		$sql = "SELECT * FROM InventoryType";
		$result = $conn->query($sql);
		while($row = $result->fetch_assoc()) {
			if($selected==$row["Type"]){
				echo'<option selected value="'.$row["Type"].'">'.$row["Type"].'</option>';
			}else{
				echo'<option value="'.$row["Type"].'">'.$row["Type"].'</option>';
			}
		}
	}

	function generateOptionsForAddItem() {
		$conn = getInclude();

		$sql = "SELECT * FROM InventoryType";
		$result = $conn->query($sql);
		while($row = $result->fetch_assoc()) {
			echo'<option value="'.$row["Type"].'">'.$row["Type"].'</option>';
		}
	}


	function selectInventory() {
		$conn = getInclude();
		$sql = "SELECT * FROM Item";
		$result = $conn->query($sql);
		while($row = $result->fetch_assoc()) {
			if(selection.value == $row["ItemName"]) {
				alert('If you choose this option, you can not receive any infomation');
			}
		}
	}

	function addItemToTable($itemEntry, $expectedPar, $expectedType) {
		$conn = getInclude();

		$query = "INSERT INTO Items Values('$itemEntry', '$expectedPar', 0, '$expectedType', 'Songbird')";
		mysqli_query($conn, $query);
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["addItem"])) {
		if(!empty($_POST["expectedPar"]) && !empty($_POST["itemEntry"]) && !empty($_POST["invType"])) {
			$itemEntry = $_POST["itemEntry"];
			$expectedPar = $_POST['expectedPar'];
			$expectedType = $_POST['invType'];
		
			addItemToTable($itemEntry, $expectedPar, $expectedType);
		}
		else {
			echo "<script>alert('Must enter all values.')</script>";
		}
	}

	// Add new types in database
	// , $Name
	function addInventoryTypeToTable($Type) {
        $conn = getInclude();

        $Name = "Songbird";
        $query = "INSERT INTO InventoryType VALUES('".$Type."', '".$Name."')"; 
        mysqli_query($conn, $query);
    }
	// && isset($_POST["Name"])
    if(isset($_POST["newType"])) {
        $Type = $_POST["newType"];
		// $Name = $_POST["Name"];
		// , $Name
        addInventoryTypeToTable($Type);
    }
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Page Title</title>

		<link rel="stylesheet" type="text/css" href="../style/style.css">
	</head>

	<body>
		<table id="itemTable" class="userCreationTable">
			<form method="post" action="inventoryLog.php">
				<tr>
					<td colspan="4" style="text-align: center;">Inventory Type</td>
				</tr>

				<tr>
				
					<td colspan="4" style="text-align: center;">
						<select name="inventory" id="inventory" onchange="this.form.submit()">
							<option selected disabled>Select Item Category</option>
							<?php
							if(!empty ($_POST['inventory'])) {
								$selected=$_POST['inventory'];
								}
								if($selected=='all'){
									echo'<option selected value="all">All</option>';
								}else{
									echo'<option value="all">All</option>';
								}
								generateOptions();
							?>
						</select>
					</td>
				</tr>
			</form>
			<!-- Inventory Order -->
			<form id="inputForm" action="inventoryOrder.php" method="post">
				<tr>
					<th>Item</th>
					<th>Par</th>
					<th colspan="2">Quantity on Hand</th>
				</tr>

				<?php
					generateTableData();
					
				?>

				<!-- <tr>
					<td colspan="4" style="text-align: center;">
						<input type="submit" style="background-color: #343131;  color: #969595;">
					</td>
				</tr> -->
			</form>


			<!-- Add Item -->	
			<form method="post" action="inventoryLog.php">
				<tr>
					<td style="padding-top: 40px;">
						<input type="text" id="itemEntry" name="itemEntry" placeholder="Item Name"/>
					</td>

					<td style="padding-top: 40px;">
						<label for="expectedPar">Item Par</label>
					</td>

					<td style="padding-top: 40px;">
						<input type="number" id="expectedPar" name="expectedPar" value=1 min=1 max=99/>
					</td>

					<td style="text-align: center; padding-top: 40px;">
						<select name="invType" id="invType">
							<option selected disabled>Select Item Category</option>
							<?php
								generateOptionsForAddItem();
							?>
						</select>
					</td>
					
				</tr>

				<tr>
					<td colspan="3" style="text-align: center;">
						<input type="submit" name="addItem" value="Add Item" style='background-color: #343131;  color: #969595;'/>
					</td>
				</tr>
			</form>

			<!-- Add Inventory Type -->	
			<form method="POST" action="inventoryLog.php">
				<tr>
					<td style="padding-top: 10px;" colspan="2">
						<input type="text" id="newType" name="newType" placeholder="Inventory Type"/>	
					</td>

					<td style="padding-top: 10px;" colspan="2" style="text-align: center;">
						<input type="submit" value="Add Type" style='background-color: #343131;  color: #969595;'/>
					</td>
				</tr>
			</form>

			<!-- Back Button -->	
			<tr>
				<td colspan="4" style="text-align: center;">
					<form method="post" action="adminMain.php">
						<button type="Submit" style='background-color: #343131;  color: #969595;'>Back</button>
					</form>
				</td>
			</tr>
		</table>	
	</body>
</html>