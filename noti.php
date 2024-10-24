<?php
    
    $notif = $_POST['notif'];
    $display = "";
    $display .= "<div id='container'>
        <div id='item'>
            <div id='text-content'>$notif</div>
        </div>
    </div>";
    echo $display;
?>


