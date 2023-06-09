<?php 
require_once('config.php');
$location_qry = $conn->query("SELECT * FROM `location` where id = ".$_GET['lid']);
if($location_qry->num_rows > 0){
    foreach($location_qry->fetch_assoc() as $k => $v){
        $meta[$k] = $v;
    }
}
if(isset($meta['description']))
$meta['description'] = stripslashes(html_entity_decode($meta['description']));
?>
<style>
#uni_modal .modal-content>.modal-header,#uni_modal .modal-content>.modal-footer{
    display:none;
}
#uni_modal .modal-body{
    padding-top:0 !important;
}
#location_modal{
    direction:rtl !important;
}
#location_modal>*{
    direction:ltr !important;
}
</style>
<div class="modal-header border-0">
    <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
</div>
<div class="container-fluid">
    <div class="row" id="location_modal">
        <div class="col-6 border-left" id="frm-field">
            <h3>Schedule Form: (<?php echo $_GET['d'] ?>)</h3>
            <hr>
            <form id="schedule_form">
            <input type="hidden" name="lid" value="<?php echo $_GET['lid'] ?>">
            <input type="hidden" name="date_sched" value="<?php echo $_GET['d'] ?>">
                <div class="form-group">
                    <label for="name" class="control-label">Fullname</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="form-group">
                    <label for="contact" class="control-label">Contact</label>
                    <input type="text" class="form-control" name="contact" required>
                </div>
                <div class="form-group">
                    <label for="gender" class="control-label">Gender</label>
                    <select type="text" class="custom-select" name="gender" required>
                    <option>Male</option>
                    <option>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="dob" class="control-label">Date of Birth</label>
                    <input type="date" class="form-control" name="dob" required>
                </div>
                <div class="form-group">
                    <label for="address" class="control-label">Address</label>
                    <textarea class="form-control" name="address" rows="3" required></textarea>
                </div>
                <div class="form-group d-flex justify-content-end">
                    <button class="btn-dark btn">Submit Schedule</button>
                </div>
            </form>
        </div>
        <div class="col-6">
            <p><b>Location: </b><?php echo $meta['location'] ?></p>
            <hr class="border-primary">
            <div><?php echo $meta['description'] ?></div>
        </div>
    </div>
</div>
<script>
$(function(){
    $('#schedule_form').submit(function(e){
        e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_schedule",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
                        var name = "<h4><b>"+resp.name+"</b></h4>";
                        var code = "<h3><b>"+resp.code+"</b></h3>";
                        var ins = "<small><i>Copy or take a snapshot of the code below your name. The code will serve as your ticlet number for vaccination schedule. Please bring atleast 1 ID to verify your schedule information.</i></small>";
						$('#frm-field').html("<div>"+name+code+ins+"</div>")
                        $('#frm-field').addClass("text-center d-flex justify-content-center align-items-center")
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: $('#uni_modal').offset().top }, "fast");
                    }else{
						alert_toast("An error occured",'error');
                        console.log(resp)
					}
						end_loader();
				}
			})
    })
    $('#uni_modal').on('hidden.bs.modal', function (e) {
        if($('#schedule_form').length <= 0)
            location.reload()
    })
})
</script>


