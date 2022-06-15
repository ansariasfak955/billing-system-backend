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
            });
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
      });
    } else {
      e.dismiss;
    }
  }
)});


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
            });
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

$('.select-all-permissions').on('click', function() {
  if ($(this).is(":checked")) {
    $('input:checkbox').not(this).toggleClass('checked');
    $('input:checkbox').not('input.Visible_Roles').not(this).prop('checked', true);
  } else {
    $('input:checkbox').not(this).not('input.Visible_Roles').prop('checked', false);
  }
});

$('.main_permission_input').on('click', function() {
  var permission = $(this).attr('data-permission');
  if ($(this).is(":checked")) {
    $('input.'+permission+'').not(this).prop('checked', true);
  } else {
    $('input.'+permission+'').not(this).prop('checked', false);
  }
});

// Show all checked if all subpermissions checked
$('input.main_permission_input').each(function(){
  var permission_name = $(this).attr('data-permission');
  var numberOfChecked = $('.edit-page input.'+permission_name+':checked:checked').length;
  var totalCheckboxes = $('.edit-page input.'+permission_name+':checkbox').length;

  if (numberOfChecked == totalCheckboxes) {
    $('.permission-'+permission_name+'').prop('checked', true);
  }
});