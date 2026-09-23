	function showToast(message, type) {
		var icon = type === 'success';
		var toast = $('<div class="toast ' + type + '">' + '<span>' + message + '</span></div>');
		$('#toast-container').append(toast);
		setTimeout(function() {
			toast.fadeOut(400, function() { $(this).remove(); });
		}, 3000);
	}

$(document).ready(function(){

	//Add Device 
	$(document).on('click', '#dev_add', function(){

		var dev_name = $('#dev_name').val();
		var dev_dep = $('#dev_dep').val();

		$.ajax({
		  url: 'dev_config.php',
		  type: 'POST',
		  data: {
		    'dev_add': 1,
		    'dev_name': dev_name,
		    'dev_dep': dev_dep,
		  },
		  success: function(response){
		    $('#dev_name').val('');
		    $('#dev_dep').val('');

		   if (response == 1) {
				$('#new-device').modal('hide');
				$('#dev_name').val('');
				$('#dev_dep').val('');
				showToast('A new device has been added successfully!', 'success');
			} else {
				showToast(response, 'error');
			}

		    $.ajax({
		      url: "dev_up.php",
		      type: 'POST',
		      data: {
		          'dev_up': 1,
		      }
		      }).done(function(data) {
		      $('#devices').html(data);
		    });
		  }
		});
	});


	//UPDATE
	$(document).on('click', '.dev_uid_up', function(){

    var el = this;
    var dev_id = $(this).data('id');

    bootbox.confirm({
        message: `
            <div style="text-align:center; padding: 10px 0;">
                <i class="fa-solid fa-arrows-rotate" style="font-size:48px; color:#f0a500; margin-bottom:16px;"></i>
                <h4 style="font-weight:700; color:#1a3a5c; margin-bottom:8px; font-size:30px;">Update Device Token?</h4>
                <p style="color:#666; font-size:14px;">Do you really want to update this device token?</p>
            </div>
        `,
        buttons: {
            confirm: { label: '<i class="fa-solid fa-check"></i> Yes', className: 'btn-success' },
            cancel: { label: '<i class="fa-solid fa-xmark"></i> No', className: 'btn-secondary' }
        },
        callback: function(result) {
            if(result){
                $.ajax({
                    url: 'dev_config.php',
                    type: 'POST',
                    data: { 'dev_uid_up': 1, 'dev_id_up': dev_id },
                    success: function(response){
                        $(el).closest('tr').css('background','#5cb85c');
                        $(el).closest('tr').fadeOut(300, function(){ $(this).show(); });
                        if(response == 1){
                            showToast('Device token updated successfully!', 'success');
                            $.ajax({
                                url: "dev_up.php", type: 'POST', data: { 'dev_up': 1 }
                            }).done(function(data){ $('#devices').html(data); });
                        } else {
                            showToast(response, 'error');
                        }
                    }
                });
            }
        }
    });
});



//DELETE
$(document).on('click', '.dev_del', function(){

    var el = this;
    var deleteid = $(this).data('id');

    bootbox.confirm({
        message: `
            <div style="text-align:center; padding: 10px 0;">
                <i class="fa-solid fa-trash" style="font-size:48px; color:#e74c3c; margin-bottom:16px;"></i>
                <h4 style="font-weight:700; color:#1a3a5c; margin-bottom:8px; font-size:30px;">Delete Device?</h4>
                <p style="color:#666; font-size:14px;">Do you really want to delete this device?</p>
            </div>
        `,
        buttons: {
            confirm: { label: '<i class="fa-solid fa-trash"></i> Yes', className: 'btn-primary' },
            cancel: { label: '<i class="fa-solid fa-xmark"></i> No', className: 'btn-secondary' }
        },
        callback: function(result) {
            if(result){
                $.ajax({
                    url: 'dev_config.php',
                    type: 'POST',
                    data: { 'dev_del': 1, 'dev_sel': deleteid },
                    success: function(response){
                        if(response == 1){
                            $(el).closest('tr').css('background','#d9534f');
                            $(el).closest('tr').fadeOut(800, function(){ $(this).remove(); });
                            showToast('Device deleted successfully!', 'success');
                            $.ajax({
                                url: "dev_up.php", type: 'POST', data: { 'dev_up': 1 }
                            }).done(function(data){ $('#devices').html(data); });
                        } else {
                            showToast(response, 'error');
                        }
                    }
                });
            }
        }
    });
});


//MODE
$(document).on('click', '.mode_sel', function(){

    var el = this;
    var dev_mode = $(this).attr("value");
    var dev_id = $(this).data('id');

    bootbox.confirm({
        message: `
            <div style="text-align:center; padding: 10px 0;">
                <i class="fa-solid fa-repeat" style="font-size:48px; color:#4a90d9; margin-bottom:16px;"></i>
                <h4 style="font-weight:700; color:#1a3a5c; margin-bottom:8px; font-size:30px;">Change Device Mode?</h4>
                <p style="color:#666; font-size:14px;">Do you really want to change this device mode?</p>
            </div>
        `,
        buttons: {
            confirm: { label: '<i class="fa-solid fa-check"></i> Yes', className: 'btn-success' },
            cancel: { label: '<i class="fa-solid fa-xmark"></i> No', className: 'btn-secondary' }
        },
        callback: function(result) {
            if(result){
                $.ajax({
                    url: 'dev_config.php',
                    type: 'POST',
                    data: { 'dev_mode_set': 1, 'dev_mode': dev_mode, 'dev_id': dev_id },
                    success: function(response){
                        if(response == 1){
                            $(el).closest('tr').css('background','#5cb85c');
                            $(el).closest('tr').fadeOut(300, function(){ $(this).show(); });
                            showToast('Device mode changed successfully!', 'success');
                            $.ajax({
                                url: "dev_up.php", type: 'POST', data: { 'dev_up': 1 }
                            }).done(function(data){ $('#devices').html(data); });
                        } else {
                            showToast(response, 'error');
                        }
                    }
                });
            } else {
                $.ajax({
                    url: "dev_up.php", type: 'POST', data: { 'dev_up': 1 }
                }).done(function(data){ $('#devices').html(data); });
            }
        }
    });
});
});