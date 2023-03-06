$(document).ready(function () {
  // Send Search Text to the server
  $("#search").keyup(function () {
    let searchText = $(this).val();
    if (searchText != "") {
      $.ajax({
        url: "action_workpackage_info.php",
        method: "post",
        data: {
          query: searchText,
          currentYear: searchYear,
        },
        success: function (response) {
          $("#show-list").html(response);
        },
      });
    } else {
      $("#show-list").html("");
    }
  });
  // Set searched text in input field on click of search button
  $(document).on("click", "a", function () {
    $("#search").val($(this).text());
    $("#show-list").html("");
  });
});



$(document).ready(function(e) {
        $('.selectpicker').selectpicker();

        $('body').on('mousemove',function(){
                $('[data-toggle="tooltip"]').tooltip();
        });

        $("#addmore").on("click",function(){
                $.ajax({
                        type:'POST',
                        url:'action-form_update_wp_activities.php',
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
                        url:'action-form_update_wp_activities.php',
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
