function notif(data){
  var notification = document.getElementById("notification");
  var formdata = new FormData();
  formdata.append("notif",data);
  fetch("notif_main.php",{
    method: "POST",
    body: formdata,
  })
    .then((response) => response.text())
    .then((showdata)=>{
      notification.innerHTML = showdata;
    })
}
function closeNotif(){
  var notification = document.getElementById("notification");
  notification.innerHTML="";
}
function startScanning() {
    document
    .getElementById("startBtn")
    .addEventListener("click", startScanning);

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
  function validateMembership(memberId) {
    fetch(`validate.php?memberId=${memberId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.valid) {
          // Show both the member's name and the expiry date
          notif("Membership Valid");
          document.getElementById(
            "result"
          ).innerText = `Member: ${data.name}\n Expiry Date: ${data.expiry}\n Member ID: ${data.id}\n\nENJOY YOUR WORKOUT 💪`;
        } else {
          notifred("Membership Expired");
          document.getElementById(
            "result"
          ).innerText = `Member: ${data.name}\n Expiry Date: ${data.expiry}\n Member ID: ${data.id}\n\nNeed to Renew Your Membership 💪`;
        }
      })
      .catch((error) => {
        console.error("Error during validation:", error);
        notif("member not found");
      });

    stopScanning();
  }

