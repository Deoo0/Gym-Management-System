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

<style>
    #container{
        height: 410px;
        background-color: red;
        align-content: center;
    }
    #text-content{
        color: white;
        text-align: center;
        font-weight: bold;
        font-size: 30px;

    }
</style>