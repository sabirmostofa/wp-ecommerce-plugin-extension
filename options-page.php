<?php
if(!class_exists('WP_eCommerce')){
    ?>
    <div class="updated" style="font-size:16px; margin-top: 50px;"> WP_eCommerce isn't installed or Activated. This plugin requires WP_eCommerce Plugin to be installed and activated </div>
    <?php
    exit;
}
  

global $wpdb;



//if setting submited
if (isset($_POST['main-submit'])):
   

endif;



// If new city submitted
if (isset($_POST['city-submit'])):
   

endif;





?>

<div class="wrap">
    
        <h4>Select a date range</h4>
        
        <br/>
        <input style="width:20%;float:left;margin-right:30px" type='text' name='from' id="date_from"/>
        <input style="width:20%;float:left;margin-right:30px" type='text' name='from' id="date_to"/>
        

        <button class='button-primary'  id="show" >Show Data </button>


    <!-- Form to add a new city and URL -->

    <br/>
    <br/>
 
<div id="ajaxdata">
</div>
    
    <br/>
    <br/>

    <?php

    ?>
    <table class="widefat" >
        <thead>
            <tr>

                <th> Remove</th>
                <th> City Name</th>
                <th>City URL </th>
            </tr>
        </thead>
        <tbody>

            <?php
     
                  ?>
        </tbody>

</table>


</div>

<div style="clear:both;width:200px;heigth:20px"></div>
