$(document).ready(function() {


$('#addReplaced').click(function() {
    console.log('Add Replace button clicked');
    $('#replacedModal').modal('show');
    $('#replacedForm')[0].reset();
    $('.modal-title').html("<i class='fa fa-plus'></i> Add Replace");
    $('#action').val("Add");
    $('#btn_action').val("addReplace");
});

});