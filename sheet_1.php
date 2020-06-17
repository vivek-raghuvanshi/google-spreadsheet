 <?php
 session_start();
require __DIR__.'/vendor/autoload.php';

$client=new \Google_Client();
$client->setApplicationName('Google Sheets and PHP');
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');
$client->setAuthConfig(__DIR__.'/credentials.json');

$service=new Google_Service_Sheets($client);
$spreadsheetId="1VPAGbLHN_fV7A2vuMp0WErmoETIwxJ9l3P3gKwwXtRA";

/*$range = 'sheet1!A2:C2';  // TODO: Update placeholder value.

// TODO: Assign values to desired properties of `requestBody`:
$requestBody = new Google_Service_Sheets_ClearValuesRequest();
$response = $service->spreadsheets_values->clear($spreadsheetId, $range, $requestBody);
*/
if(isset($_POST['delete'])){
$requests = [
  // Change the spreadsheet's title.
  new Google_Service_Sheets_Request([
      'deleteDimension' => [
          'range' => [
              'sheetId' => '0',
              'dimension' => 'ROWS',
              'startIndex' => $_POST['row']-1,
              'endIndex' => $_POST['row']

          ]
      ]
  ])
];

// Add additional requests (operations) ...
$batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
    'requests' => $requests
]);
$response = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
unset($_POST);
	if ($response) {
		$_SESSION['contact_error_mess']= "Successful Delete";
		header("Location:sheet_1.php");
		exit;
	} else {
		$_SESSION['contact_error_mess']= "Failed! Try again";
		header("Location:sheet_1.php");
		exit;
	}
}
/*End Delete*/


//Update
if(isset($_POST['update'])){
	/*print_r($_POST);
	die;*/
	$range="sheet1!A".$_POST['row'].":C".$_POST['row'];
	$values=[[$_POST['name'],$_POST['age'],date("Y-m-d H:i:s")],];
	$body=new Google_Service_Sheets_ValueRange([
	"values"=>$values
	]);
	$params=['valueInputOption'=>'RAW'];
	$result=$service->spreadsheets_values->update(
		$spreadsheetId,$range,$body,$params
	);
	unset($_POST);
	if ($result) {
		$_SESSION['contact_error_mess']= "Successful Update";
		header("Location:sheet_1.php");
		exit;
	} else {
		$_SESSION['contact_error_mess']= "Failed! Try again";
		header("Location:sheet_1.php");
		exit;
	}
}
//end update

//Start Insert
if(isset($_POST['add'])){
	/*print_r($_POST);
	die;*/
	$range="sheet1";
	$values=[[$_POST['name'],$_POST['age'],date("Y-m-d H:i:s")],];
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
		$_SESSION['contact_error_mess']= "Successful Insert";
		header("Location:sheet_1.php");
		exit;
		
	} else {
		$_SESSION['contact_error_mess']= "Failed! Try again";
		header("Location:sheet_1.php");
		exit;
	}

	
}
//end insert

//GET data from google sheet
$range="sheet1!A2:B";
$response=$service->spreadsheets_values->get($spreadsheetId,$range);
$values=$response->getValues();

$countRow=1;
/*echo "<pre>";
print_r($values);
echo "</pre>";*/
//die();
$message='';
/*if(empty($values)){
	$message.= "No data found";
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
	
}*/
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
		<div class="captcha-wrapper">
                        <div class="alert" style="<?=(isset($_SESSION['contact_error_mess']) && $_SESSION['contact_error_mess']!=''?'display:block':'display:none')?>">
                        	
                        	<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                        	<strong>
                            <?php
                            if (isset($_SESSION['contact_error_mess']) && $_SESSION['contact_error_mess'] != '') {
                                echo $_SESSION['contact_error_mess'];
                                unset($_SESSION['contact_error_mess']);
                            }

                            ?>
                        </strong>
                        
                        </div>
        </div>
	<table id="example" class="dataTables" >
        <thead>
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th></th>
                
            </tr>
        </thead>
        <tbody>
         <?php  
			if(empty($values)){ ?>
				<tr colspan="2"> 
		         <td>No data found</td>
		     </tr>
			<?php }
			  else{
			  	echo $message;
		        foreach ($values as  $value) {
				   $countRow++; ?>
						<tr> <form method='post' action='' id="form<?=$countRow?>">
						         <td><input class='inputRow' id="name<?=$countRow?>" readonly='' type='text' name='name' value="<?=$value[0]?>"></td>
						         <td><input class='inputRow' id="age<?=$countRow?>" readonly='' type='text' name='age' value="<?=$value[1]?>">
						         <input type='hidden' name='row' value="<?=$countRow?>"></td>
						         <td><input class='inputButton' id="btn<?=$countRow?>" type='submit' name='update' value='update'>
						         	<a href="javascript:void(0)" class="deleteClass" onclick="btnData(this.id)" id="bt-<?=$countRow?>">Edit</a>
						         	<input class='deleteClass' id="btnDelete<?=$countRow?>"  type='submit' name='delete' value='Delete'>
						         </td>
						      </form>
						  </tr>
		             	<?php
		            }
		        }
		         ?>
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

    	function btnData(a){
    		var d=a.split('-');
    		    $('#bt-'+d[1]).hide();
    		    $('#btnDelete'+d[1]).hide();
		     	$('#btn'+d[1]).css('display','block');
		     	$("#name"+d[1]). removeAttr("readonly");
		     	$("#age"+d[1]). removeAttr("readonly");
		     	$("#name"+d[1]). removeClass("inputRow");
		     	$("#age"+d[1]). removeClass("inputRow");
                $("#name"+d[1]). addClass("inputRow1");
		     	$("#age"+d[1]). addClass("inputRow1");

		     	//alert($("#form"+d[1]).serialize());
		     	
        }
    	$(document).ready(function() {
		    $('#example').DataTable({
		    	"searching": false,
		    	"lengthMenu":[[3,10,50,-1],[3,10,50,"All"]]
		    });
		     $(".alert").delay(3000).fadeOut(3000, function() {
									$(this).remove();
				});
				
    
        });
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
		.inputRow1 {
		  width: 80%;
		  border: 1px solid #555;
		  outline: none;
		  padding: 5px 5px;
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
	  display: none;
	}
	.deleteClass {
	  background-color: #fff;
	  border: none;
	  color: #000;
	  padding: 1px 2px;
	  text-decoration: none;
	  margin: 4px 2px;
	  cursor: pointer;
	  border: 1px solid #000;
	  
	}
	.alert {
		  padding: 10px;
		  background-color: #4CAF50;
		  color: white;
		  margin-bottom: 20px;
		}

		.closebtn {
		  margin-left: 15px;
		  color: white;
		  font-weight: bold;
		  float: right;
		  font-size: 22px;
		  line-height: 20px;
		  cursor: pointer;
		  transition: 0.3s;
		}

		.closebtn:hover {
		  color: black;
		}
		table.dataTable tbody td {
		    padding: 4px 10px;

		}
	
</style>
</body>
</html>
