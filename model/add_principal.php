<?php
include_once('controller/config.php');
if(isset($_POST["do"])&&($_POST["do"]=="add_principal")){

	$index_number = $_POST["index_number"];	
	$full_name = $_POST["full_name"];
	$i_name= $_POST["i_name"];
	$gender = $_POST["gender"];
	$address = $_POST["address"];
	$phone = $_POST["phone"];
	$email = $_POST["email"];
	$password = $_POST["password"];
	$birth_date = $_POST["birth_date"];
	
	$current_date=date("Y-m-d");
	
	$target_dir = "uploads/";
	$name = basename($_FILES["fileToUpload"]["name"]);
	$size = $_FILES["fileToUpload"]["size"];
	$type = $_FILES["fileToUpload"]["type"];
	$tmpname = $_FILES["fileToUpload"]["tmp_name"];
	
	$max = 31457280;
	$extention = strtolower(substr($name, strpos($name, ".")+ 1));
	$filename = date("Ymjhis");
	
	$sql1="SELECT * FROM admin where index_number='$index_number'";	
	$result1=mysqli_query($conn,$sql1);
	$row1=mysqli_fetch_assoc($result1);
	$index_number1=$row1['index_number'];
	
	$sql2="SELECT * FROM admin where email='$email'";	
	$result2=mysqli_query($conn,$sql2);
	$row2=mysqli_fetch_assoc($result2);
	$email2=$row2['email'];
	
	$msg=0;//for alerts
	$image_path =  $target_dir.$filename.".".$extention;

	if($index_number == $index_number1 ){
		//1 The index number is duplicated.
		$msg+=1;
		
		if($email == $email2){
			// Both index number and email duplicate. 
			$msg+=3; //(Note: msg value is not equel to 3, its value is 1+=3 -> 1+3 = 4 :D)
		}

	}else if($email == $email2){
		
		// Only email address duplicates.
		$msg+=5;
		
	}else{
		//
	 	if(($extention == "jpg" || $extention == "jpeg" || $extention == "png") && $size < $max){//This line is not needed, bcz we checked it before.    																					
		 	if(move_uploaded_file($tmpname, $image_path)){
				//MSK-000143-5	
				
				$sql = "INSERT INTO admin (index_number,full_name,i_name,gender,address,phone,email,image_name,reg_date,birth_date)
			            VALUES ('".$index_number."','".$full_name."','".$i_name."','".$gender."','".$address."','".$phone."','".$email."','".$image_path.                        "','".$current_date."' ,'".$birth_date."')";
				if(mysqli_query($conn,$sql)){
					$msg+=2;  
					// The record has been successfully inserted into the database.
					$sql3= "INSERT INTO user (email,password,type)
			                VALUES ('".$email."','$password','Admin')";
					
					mysqli_query($conn,$sql3);
				}else{
					$msg+=3;  
					// Connection problem.
				}
	
			}else{
				// If there aren't any folder - "uploads"
				$msg+=6;  
			}

		}else{
			//you can do anything when image type or size have a problem, but we have checked it before submit the form. 
			//And you need to know, how to check that using PHP
		}
	}
	header("Location: view/Dashboard_Principal/principal.php?do=alert_from_insert&msg=$msg");
}
?>