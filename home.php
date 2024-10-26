<?php include 'db_connect.php' ?>
<style>
   span.float-right.summary_icon {
    font-size: 3rem;
    position: absolute;
    right: 1rem;
    color: #ffffff96;
}
.imgs{
		margin: .5em;
		max-width: calc(100%);
		max-height: calc(100%);
	}
	.imgs img{
		max-width: calc(100%);
		max-height: calc(100%);
		cursor: pointer;
	}
	#imagesCarousel,#imagesCarousel .carousel-inner,#imagesCarousel .carousel-item{
		height: 60vh !important;background: black;
	}
	#imagesCarousel .carousel-item.active{
		display: flex !important;
	}
	#imagesCarousel .carousel-item-next{
		display: flex !important;
	}
	#imagesCarousel .carousel-item img{
		margin: auto;
	}
	#imagesCarousel img{
		width: auto!important;
		height: auto!important;
		max-height: calc(100%)!important;
		max-width: calc(100%)!important;
	}
</style>

<div class="containe-fluid">
	<div class="row mt-3 ml-3 mr-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <?php echo "Welcome back ". $_SESSION['login_name']."!"  ?>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body bg-primary">
                                    <div class="card-body text-white">
                                        <span class="float-right summary_icon"><i class="fa fa-users"></i></span>
                                        <h4><b>
                                            <?php echo $conn->query("SELECT * FROM registration_info where status = 1")->num_rows; ?>
                                        </b></h4>
                                        <p><b>Active Members</b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body bg-info">
                                    <div class="card-body text-white">
                                        <span class="float-right summary_icon"><i class="fa fa-th-list"></i></span>
                                        <h4><b>
                                            <?php echo $conn->query("SELECT * FROM plans")->num_rows; ?>
                                        </b></h4>
                                        <p><b>Total Membership Plans</b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body bg-warning">
                                    <div class="card-body text-white">
                                        <span class="float-right summary_icon"><i class="fa fa-list"></i></span>
                                        <h4><b>
                                            <?php echo $conn->query("SELECT * FROM packages")->num_rows; ?>
                                        </b></h4>
                                        <p><b>Total Packages</b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>	

                    
                </div>
            </div>      			
        </div>
    </div>
</div>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>QR Code Scanner</title>
    <script src="html5-qrcode.min.js"></script>
    <link rel="stylesheet" href="qr-style.css" />
  </head>
  <body id="qr-scanner">
    <div id="scan">
    <div id="notif"></div>
    <h2>Check Valid Member</h2>
    <div id="reader"></div>
    <div id="result"></div>
    <button id="startBtn">Start Scanning</button>
    </div>

    <script>
      let html5QrCode = new Html5Qrcode("reader"); // Initialize outside for scope
      let isScanning = false;

      // Start scanning
      function startScanning() {
        if (!isScanning) {
          // Dynamically set qrbox size to match the video feed aspect ratio
          let aspectRatio = 600 / 400; // Example: adjust to match your camera feed ratio
          let qrboxSize = {
            width: 300, // Set this value based on your requirements
            height: 500 / aspectRatio, // Ensuring it matches the aspect ratio of the video feed
          };
          html5QrCode
            .start(
              { facingMode: "environment" }, // Use the environment camera
              {
                fps: 10, // Frames per second
                qrbox: qrboxSize, // Set the scanning box size
              },
              qrCodeSuccessCallback,
              (errorMessage) => {
                // Handle scanning errors
                console.warn(`QR code scan error: ${errorMessage}`);
                document.getElementById("result").innerText = "Scanning.....";
              }
            )
            .then(() => {
              isScanning = true;
              console.log("Scanning started...");
            })
            .catch((err) => {
              console.error("Failed to start scanning:", err);
              alert(
                "Error starting the scanner. Please check camera permissions."
              );
            });
        }
      }

      // Stop scanning
      function stopScanning() {
        if (isScanning) {
          html5QrCode
            .stop()
            .then(() => {
              isScanning = false;
              console.log("Scanning stopped.");
              document.getElementById("result").innerText = "Scanning stopped.";
            })
            .catch((err) => {
              console.error("Failed to stop scanning:", err);
              alert("Error stopping the scanner.");
            });
        }
      }

      // Handle QR code success
      const qrCodeSuccessCallback = (decodedText, decodedResult) => {
        document.getElementById(
          "result"
        ).innerText = `QR Code scanned: ${decodedText}`;
        validateMembership(decodedText); // Call your validation logic
      };
      function notif(data) {
        var notif = document.getElementById("reader");
        var formdata = new FormData();
        formdata.append("notif", data);
        fetch("noti.php", {
          method: "POST",
          body: formdata,
        })
          .then((response) => response.text())
          .then((data) => {
            notif.innerHTML = data;
          });
        return false;
      }

      function notifred(data) {
        var notif = document.getElementById("reader");
        var formdata = new FormData();
        formdata.append("notif", data);
        fetch("noti-red.php", {
          method: "POST",
          body: formdata,
        })
          .then((response) => response.text())
          .then((data) => {
            notif.innerHTML = data;
          });
        return false;
      }

      // Validate membership
      function validateMembership(memberId) {
        fetch(`validate.php?memberId=${memberId}`)
          .then((response) => response.json())
          .then((data) => {
            if (data.valid) {
              // Show both the member's name and the expiry date
              notif("Membership Valid");
              document.getElementById(
                "result"
              ).innerText = `Member: ${data.name}\n Expiry Date: ${data.expiry}\n Member ID: ${data.memberId}`;
            } else {
              notifred("Membership Expired");
              document.getElementById(
                "result"
              ).innerText = `Member: ${data.name}\n Expiry Date: ${data.expiry}\n Member ID: ${data.memberId}`;
            }
          })
          .catch((error) => {
            console.error("Error during validation:", error);
            notif("member not found");
          });

        stopScanning();
      }

      // Add event listeners for start and stop buttons
      document
        .getElementById("startBtn")
        .addEventListener("click", startScanning);
    </script>
  </body>
</html>

<script>
	$('#manage-records').submit(function(e){
        e.preventDefault()
        start_load()
        $.ajax({
            url:'ajax.php?action=save_track',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                resp=JSON.parse(resp)
                if(resp.status==1){
                    alert_toast("Data successfully saved",'success')
                    setTimeout(function(){
                        location.reload()
                    },800)

                }
                
            }
        })
    })
    $('#tracking_id').on('keypress',function(e){
        if(e.which == 13){
            get_person()
        }
    })
    $('#check').on('click',function(e){
            get_person()
    })
    function get_person(){
            start_load()
        $.ajax({
                url:'ajax.php?action=get_pdetails',
                method:"POST",
                data:{tracking_id : $('#tracking_id').val()},
                success:function(resp){
                    if(resp){
                        resp = JSON.parse(resp)
                        if(resp.status == 1){
                            $('#name').html(resp.name)
                            $('#address').html(resp.address)
                            $('[name="person_id"]').val(resp.id)
                            $('#details').show()
                            end_load()

                        }else if(resp.status == 2){
                            alert_toast("Unknow tracking id.",'danger');
                            end_load();
                        }
                    }
                }
            })
    }
</script>