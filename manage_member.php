<?php include 'db_connect.php' ?>
<?php
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM members where id=".$_GET['id'])->fetch_array();
	foreach($qry as $k =>$v){
		$$k = $v;
	}
}

?>

<style>
	#image-display{
		height: 200px;
		width: 200px;
	}
	#pic-file{
		font-size: 14px;
	}

</style>
<div class="container-fluid">
	<form action="" id="manage-member"  method="POST" enctype="multipart/form-data">
		<div id="msg"></div>
				<input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id']:'' ?>" class="form-control">
		<!--<div class="row form-group">
			<div class="col-md-4">
						<label class="control-label">ID No.</label>
						<input type="text" name="member_id" class="form-control" value="<?php echo isset($member_id) ? $member_id:'' ?>" >
						<small><i>Leave this blank if you want to a auto generate ID no.</i></small>
					</div>
		</div>-->

		<center>
		<label for="pic-file">
			<img src="default.jpg" alt="" id="image-display">
		</label><br>
		<input type="file" id="pic-file" name="pic-file"  style="display:none; width: 200px;" onchange="return showPic()">
		</center>


		<div class="row form-group">
			<div class="col-md-4">
				<label class="control-label">Last Name</label>
				<input type="text" name="lastname" class="form-control" value="<?php echo isset($lastname) ? $lastname:'' ?>" required>
			</div>
			<div class="col-md-4">
				<label class="control-label">First Name</label>
				<input type="text" name="firstname" class="form-control" value="<?php echo isset($firstname) ? $firstname:'' ?>" required>
			</div>
			<div class="col-md-4">
				<label class="control-label">Middle Name</label>
				<input type="text" name="middlename" class="form-control" value="<?php echo isset($middlename) ? $middlename:'' ?>">
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-4">
				<label class="control-label">Email</label>
				<input type="email" name="email" class="form-control" value="<?php echo isset($email) ? $email:'' ?>" required>
			</div>
			<div class="col-md-4">
				<label class="control-label">Contact #</label>
				<input type="text" name="contact" class="form-control" value="<?php echo isset($contact) ? $contact:'' ?>" required>
			</div>
			<div class="col-md-4">
				<label class="control-label">Gender</label>
				<select name="gender" required="" class="custom-select" id="">
					<option <?php echo isset($gender) && $gender == 'Male' ? 'selected' : '' ?>>Male</option>
					<option <?php echo isset($gender) && $gender == 'Female' ? 'selected' : '' ?>>Female</option>
				</select>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-12">
				<label class="control-label">Address</label>
				<textarea name="address" class="form-control"><?php echo isset($address) ? $address : '' ?></textarea>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-4">
				<label class="control-label">Plan</label>
				<select name="plan_id" required="required" class="custom-select select2" id="">
					<option value=""></option>
					<?php
						$qry = $conn->query("SELECT * FROM plans order by plan asc");
						while($row= $qry->fetch_assoc()):
					?>
					<option value="<?php echo $row['id'] ?>" <?php echo isset($plan_id) && $plan_id == $row['id'] ? 'selected' : '' ?>><?php echo ucwords($row['plan']) ?></option>
					<?php endwhile; ?>
				</select>
			</div>
			<div class="col-md-4">
				<label class="control-label">Package</label>
				<select name="package_id" required="required" class="custom-select select2" id="">
					<option value=""></option>
					<?php
						$qry = $conn->query("SELECT * FROM packages order by package asc");
						while($row= $qry->fetch_assoc()):
					?>
					<option value="<?php echo $row['id'] ?>" <?php echo isset($package_id) && $package_id == $row['id'] ? 'selected' : '' ?>><?php echo ucwords($row['package']) ?></option>
					<?php endwhile; ?>
				</select>
			</div>
			<div class="col-md-4">
				<label class="control-label">Trainer</label>
				<select name="trainer_id" class="custom-select select2" id="">
					<option value=""></option>
					<?php
						$qry = $conn->query("SELECT * FROM trainers order by name asc");
						while($row= $qry->fetch_assoc()):
					?>
					<option value="<?php echo $row['id'] ?>" <?php echo isset($trainer_id) && $trainer_id == $row['id'] ? 'selected' : '' ?>><?php echo ucwords($row['name']) ?></option>
					<?php endwhile; ?>
				</select>
			</div>
		</div>
	</form>
</div>

<script>
	$('#manage-member').submit(function(e) {
    e.preventDefault(); // Prevent the default form submission
    start_load(); // Optional: Show a loading spinner or message

    // Create a FormData object to include both text inputs and files
    let formData = new FormData(this);

    $.ajax({
        url: 'ajax.php?action=save_member', // The server-side script
        method: 'POST',
        data: formData,
        contentType: false, // Important: Do not process or set content-type for FormData
        processData: false, // Prevent jQuery from processing data
        success: function(resp) {
            if (resp == 1) {
                alert_toast("Data successfully saved.", 'success');
                setTimeout(function() {
                    location.reload(); // Reload the page
                }, 1000);
            } else if (resp == 2) {
                $('#msg').html('<div class="alert alert-danger">ID No already existed.</div>');
                end_load();
            } else {
                alert("Error: " + resp);
            }
        },
        error: function(err) {
            console.log(err);
            alert("An error occurred. Please try again.");
        }
    });
});


	function showPic(){
    let picfile = document.getElementById('pic-file');
    let imgdisplay = document.getElementById('image-display');
    let filepic = picfile.files[0];
    let reader = new FileReader();
    reader.onload = function(actiondisplay){
      imgdisplay.src = actiondisplay.target.result;
    }
    reader.readAsDataURL(filepic);
    return false;
  }
</script>