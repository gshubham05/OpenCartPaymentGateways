/**
 * 
 */
 
$(document).delegate('#confirm-order-button', 'click', function(event) {
	event.preventDefault();
	
	// Stop Multiple execution
	if ($("#instamojo-script-running").val() == 1) {
		return;
	}
	$("#instamojo-script-running").attr("value", 1);
	
	
    $.ajax({
        url: 'index.php?route=' + $('#instamojo-route').val(),
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
         	$('#confirm-order-button').button('loading');
		},
        complete: function() {
			$('#confirm-order-button').button('reset');
			$("#instamojo-script-running").attr("value", 0);
        },
        success: function(json) {console.log(json);
            $('.alert-dismissible, .text-danger').remove();
			$('#confirm-order-button').button('reset');

            if (json.error) {
                $('#confirm-order-button').button('reset');
                $('#collapse-checkout-confirm .panel-body').prepend('<div class="alert alert-danger alert-dismissible">' + json.error + '<!--<button type="button" class="close" data-dismiss="alert">&times;</button>--></div>');
            } else if (0 == json.payment_instamojo_checkout_mode) {
				// Redirect mode
				$('#instamojo-route').remove();
				$('#confirm-order-button').attr("id","instamojo-redirect-button");
				$('#instamojo-payment-form').attr("action", json.action);
				$('#instamojo-payment-form').submit();
			} else {
				// Pop Up mode
				$('#confirm-order-button').attr("id","instamojo-popup-button");
				$('#instamojo-payment-form').attr("action", json.action);
                Instamojo.open(json.action);
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});

$(document).delegate('#instamojo-popup-button', 'click', function(event) {
	event.preventDefault();
	Instamojo.open($('#instamojo-payment-form').attr('action'));
});