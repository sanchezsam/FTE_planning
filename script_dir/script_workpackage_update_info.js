$(document).ready(function(e) {
        $('.selectpicker').selectpicker();

        $('body').on('mousemove',function(){
                $('[data-toggle="tooltip"]').tooltip();
        });

        $("#addmore").on("click",function(){
                $.ajax({
                        type:'POST',
                        url:'action-form_update_wp_info.php',
                        data:{'action':'addDataRow'},
                        success: function(data){
                                $('#tb').append(data);
                                $('.selectpicker').selectpicker('refresh');
                                $('#save').removeAttr('hidden',true);
                        }
                });
        });

        $("#form").on("submit",function(){
                $.ajax({
                        type:'POST',
                        url:'action-form_update_wp_info.php',
                        data:$(this).serialize(),
                        success: function(data){
                                var a   =       data.split('|***|');
                                if(a[1]=="add"){
                                        $('#mag').html(a[0]);
                                        setTimeout(function(){location.reload();},1500);
                                }
                        }
                });
        });

});
