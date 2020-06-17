 <?php
require __DIR__.'/vendor/autoload.php';

$client=new \Google_Client();
$client->setApplicationName('Google Sheets and PHP');
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');
$client->setAuthConfig(__DIR__.'/credentials.json');

$service=new Google_Service_Sheets($client);
$spreadsheetId="1VPAGbLHN_fV7A2vuMp0WErmoETIwxJ9l3P3gKwwXtRA";


//Update
if(isset($_POST['update'])){
	/*print_r($_POST);
	die;*/
	$range="sheet1!A".$_POST['row'].":C".$_POST['row'];
	$values=[[$_POST['name'],$_POST['age'],date("Y-m-d")],];
	$body=new Google_Service_Sheets_ValueRange([
	"values"=>$values
	]);
	$params=['valueInputOption'=>'RAW'];
	$result=$service->spreadsheets_values->update(
		$spreadsheetId,$range,$body,$params
	);
	unset($_POST);
	if ($result) {
		echo "Successful Update";
	} else {
		echo "Failed! Try again";
	}
}
//end update

//Start Insert
if(isset($_POST['add'])){
	/*print_r($_POST);
	die;*/
	$range="sheet1";
	$values=[[$_POST['name'],$_POST['age'],date("Y-m-d")],];
	$body=new Google_Service_Sheets_ValueRange([
	"values"=>$values
	]);
	$params=['valueInputOption'=>'RAW'];
	$insert=['insertDataOption'=>'ROWS'];
	$result=$service->spreadsheets_values->append(
		$spreadsheetId,$range,$body,$params,$insert
	);
	unset($_POST);
	if ($result) {
		echo "Successful Insert";
	} else {
		echo "Failed! Try again";
	}
	
}
//end insert

//GET data from google sheet
$range="sheet1!A2:B";
$response=$service->spreadsheets_values->get($spreadsheetId,$range);
$values=$response->getValues();

/*echo "<pre>";
print_r($values);
echo "</pre>";*/
//die();
$message='';
if(empty($values)){
	$message.= "<tr colspan='2'><td>No data found</tr></td>";
}else{
	$countRow=1;
	
	foreach ($values as  $value) {
		$countRow++;
		$message.= "<tr> <form method='post' action=''>
		         <td><input class='inputRow' type='text' name='name' value='".$value[0]."'></td>
		         <td><input class='inputRow' type='text' name='age' value='".$value[1]."'>
		         <input type='hidden' name='row' value='".$countRow."'></td>
		         <td><input class='inputButton' type='submit' name='update' value='update'></td>
		      </form></tr>";
	}
	
}
//end get data

?>

<!DOCTYPE html>
<html>
<head>
	<title>Google Sheet</title>

	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
</head>
<body>
	<div class="container">
	<table id="example" >
        <thead>
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        	<?php  echo $message;?>
        </tbody>
        
    </table>
	
        
		<form method="post" action="">
			<fieldset>
               <legend>Form:</legend>
				<label>Name:</label><input class="input" type="text" name="name" placeholder="enter name" required=""><br>
				<label>Age:</label><input class="input" type="text" name="age" placeholder="Enter age" required=""><br>
				<input class="input" type="submit" name="add" value="Add">
			</fieldset>
		</form>
	</div>



    <script type="text/javascript">
    	$(document).ready(function() {
    $('#example').DataTable({
    	"searching": false,
    	"lengthMenu":[[3,10,50,-1],[3,10,50,"All"]]
    });
} );
    </script>
<style type="text/css">
	.container{
		width: 600px;
		border: 1px solid #555;
		padding: 10px;
		margin-top: 30px;
	}
	 .input[type=text] {
		  width: 100%;
		  padding: 12px 20px;
		  margin: 8px 0;
		  box-sizing: border-box;
		  border: 1px solid #555;
		  outline: none;
		}

		.input[type=text]:focus {
		  background-color: lightblue;
		}
		.inputRow {
		  width: 100%;
		  border: 0;
		}
	
	.input[type=button], .input[type=submit], .input[type=reset] {
	  background-color: #4CAF50;
	  border: none;
	  color: white;
	  padding: 16px 32px;
	  text-decoration: none;
	  margin: 4px 2px;
	  cursor: pointer;
	}
	 table {
		  border-collapse: collapse;
		}

		table, th, td {
		  border: 1px solid black;
		}

	 .inputButton {
	  background-color: #fff;
	  border: none;
	  color: #000;
	  padding: 1px 2px;
	  text-decoration: none;
	  margin: 4px 2px;
	  cursor: pointer;
	  border: 1px solid #000;
	}
	
</style>
</body>
</html>
