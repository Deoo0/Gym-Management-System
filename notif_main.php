<?php
    $notif = $_POST['notif'];
    $display = "";
    $display .= "
    <div id='notif-container'>
        <div id='notif-form'>
            <form action='POST' id='notifForm'>
                <div id='notif-items'>
                    <div id='close-notif' onclick='return closeNotif()'>X</div>
                    <label for=''>
                        <h1>
                            <strong>
                                Notification
                            </strong>
                        </h1>
                    </label>
                    <br>
                    <br>
                    
                </div>

            </form>
        </div>
    </div>
    ";
    echo $display;

?>