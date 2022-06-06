jQuery( document ).ready(function($) {
	/*----- Date Field -----*/
	jQuery('input[name="rx_date"]').attr('readonly','readonly');
	jQuery('input[name="rx_expiry_date"]').attr('readonly','readonly');
	jQuery('input[name="patient_dob"]').attr('readonly','readonly');
	jQuery('input[name="rx_date"]').datepicker({ 
		startView: 2,
        autoHide: true,
        format: 'dd/mm/yyyy',
        endDate: -1	 
	}); 
	jQuery('input[name="rx_expiry_date"]').datepicker({  
		startView: 2,
        autoHide: true,
        format: 'dd/mm/yyyy',
        endDate: -1	 
	}); 
	jQuery('input[name="patient_dob"]').datepicker({ 
		startView: 2,
        autoHide: true,
        format: 'dd/mm/yyyy',
        endDate: -1	 
	}); 
});

function validate_prescription(form_index) {
}