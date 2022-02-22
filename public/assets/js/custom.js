
$(document).on("submit", ".first", function(e) {
    e.preventDefault();

    var id = $(this).children('.item-id').val();
    var searlised = $( this ).serialize();
  
    swal({
        title: "Delete?",
        text: "Please ensure and then confirm!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel!",
        reverseButtons: !0
    }).then(function (e) {

        if (e.value === true) {
            var CSRF_TOKEN = $('meta[name="_token"]').attr('content');

            $.ajax({
                type: 'POST',
                url: window.location.href+"/"+id,
                data: searlised ,
                dataType: 'JSON',
                success: function (results) {
                    console.log(results);
                    if (results.status === true) {
                        swal({
                               title: "Done!", 
                               text: results.message, 
                               type: "success"
                             }).then(function(){ 
                               location.reload();
                           }
                        );
                    } else {
                        swal({
                               title: "Error!", 
                               text: results.message, 
                               type: "error"
                             }).then(function(){ 
                               location.reload();
                           }
                        );
                    }
                }
            });

        } else {
            e.dismiss;
        }

    }, function (dismiss) {
        return false;
    })
});


$(document).on( 'click', '.grid-batch-delete' ,function(){
            var gridarray = [];
                $("input:checkbox[class=select-checkbox]:checked").each(function(){
                    gridarray.push($(this).data('id'));
                });
                if(gridarray == ""){

                    alert("Please select entries");
                    exit();
                }
   
        var action = $(this).data('ajax-url');
        swal({
            title: "Delete?",
            text: "Please ensure and then confirm!",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: !0
        }).then(function (e) {
            if (e.value === true) {
                var gridarray = [];
                $("input:checkbox[class=select-checkbox]:checked").each(function(){
                    gridarray.push($(this).data('id'));
                });
                var CSRF_TOKEN = $('meta[name="_token"]').attr('content');
                
                $.ajax({
                    type: 'POST',
                    url: action,
                    data: { _token: CSRF_TOKEN, _method:"post", ids: gridarray},
                    dataType: 'JSON',
                    success: function (results) {
                        if (results.status === true) {
                            swal({
                                   title: "Done!", 
                                   text: results.message, 
                                   type: "success"
                                 }).then(function(){ 
                                   location.reload();
                               }
                            );
                        } else {
                            swal({
                                   title: "Error!", 
                                   text: results.message, 
                                   type: "error"
                                 }).then(function(){ 
                                   location.reload();
                               });
                        }
                    }
                })
            }
        });
  return false;  
});

$('#select').on('click', function(){
    $('.select-checkbox').each(function(){
        $(this).prop("checked", !$(this).prop("checked"));
    });
});