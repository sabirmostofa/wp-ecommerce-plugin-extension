/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function($){
    
    $('#date_from').datepicker();
    $('#date_to').datepicker();
    
    
        $('#show').bind('click',function(evt){
        evt.preventDefault();
        var dateT =$('#date_to').val();
        var dateS =$('#date_from').val();

        
            $.ajax({
            type :  "post",
            url : ajaxurl,
            timeout : 5000,
            data : {
                'action' : 'com_ext',
                'date_from' : dateS,		  
                'date_to' : dateT		  
            },			
            success :  function(data){  
                $('#ajaxdata').html(data);
               
            }
        })	//end of ajax	
        
        })
})
