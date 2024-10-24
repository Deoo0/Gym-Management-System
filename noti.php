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
        background-color: green;
        align-content: center;
    }
    #text-content{
        color: black;
        text-align: center;
        font-weight: bold;
        font-size: 30px;

    }
</style>