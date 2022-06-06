var rx_type_id = "";
var prev_step = "";
var curr_step = "";

function updateRxDropdowns(data_arr) {
	
	var variationsForm = jQuery(".variations_form");
	
	/*----- Color -----*/
	if(typeof JSON.parse(data_arr).color != "undefined") {
		var colorData = JSON.parse(data_arr).color;
		var colorarr = Object.keys(colorData);
		var singleVal = "";
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_color] option' ).each(function(){
			if(colorarr.indexOf(jQuery(this).text()) === -1) {
				//console.log("text-> " + jQuery(this).text());
				if(jQuery(this).val() != ""){
					jQuery(this).remove();
				}
			}
			else {
				if(colorarr.length == 1) {
					singleVal = jQuery(this).text();
					jQuery(this).prop('selected', true);
				}
			}
		});
		
		if(colorarr.length == 1) {
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_color]').parent().addClass("single_val_attr");
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_color]').parent().append('<span class="attrib_val">' + singleVal + '</span>');
		}
	}
	else {
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_color]' ).closest(".box2").remove();
	}
	
	/*----- Sphere -----*/
	if(typeof JSON.parse(data_arr).sphere != "undefined") {
		var sphereData = JSON.parse(data_arr).sphere;
		var spherearr = Object.keys(sphereData);
		var singleVal = "";
		//console.log(spherearr);
		//jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_sphere]' ).find('option').remove();
		//jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_sphere]' ).append('<option disabled selected value> Select </option>');
		/* for (var key in sphereData) {
			console.log(key + "->" + sphereData[key]["label"] + "->" + sphereData[key]["value"]);
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_sphere]' ).append('<option value="' + sphereData[key]["value"] + '">' + sphereData[key]["label"] + '</option>');
		} */
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_sphere] option' ).each(function(){
			if(spherearr.indexOf(jQuery(this).text()) === -1) {
				//console.log("text-> " + jQuery(this).text());
				if(jQuery(this).val() != ""){
					jQuery(this).remove();
				}	
			}
			else { 
				if(spherearr.length == 1) {
					singleVal = jQuery(this).text();
					jQuery(this).prop('selected', true);
				}
			}
		});
		
		if(spherearr.length == 1) {
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_sphere]').parent().addClass("single_val_attr");
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_sphere]').parent().append('<span class="attrib_val">' + singleVal + '</span>');
		}
	}
	else {
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_sphere]' ).closest(".box2").remove();
	}
	
	/*----- Diameter -----*/
	if(typeof JSON.parse(data_arr).diameter != "undefined") {
		var diameterData = JSON.parse(product_parameters).diameter;
		var diameterarr = Object.keys(diameterData);
		var singleVal = "";
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_diameter] option' ).each(function(){
			if(diameterarr.indexOf(jQuery(this).text()) === -1) {
				//console.log("text-> " + jQuery(this).text());
				if(jQuery(this).val() != ""){
					jQuery(this).remove();
				}
			}
			else {
				if(diameterarr.length == 1) {
					singleVal = jQuery(this).text();
					jQuery(this).prop('selected', true);
				}
			}
		});
		
		if(diameterarr.length == 1) {
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_diameter]').parent().addClass("single_val_attr");
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_diameter]').parent().append('<span class="attrib_val">' + singleVal + '</span>');
		}
	}
	else {
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_diameter]' ).closest(".box2").remove();
	}
	
	/*----- Base Curve -----*/
	if(typeof JSON.parse(data_arr).basecurve != "undefined") {
		var basecurveData = JSON.parse(data_arr).basecurve;
		var basecurvearr = Object.keys(basecurveData);
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_base-curve] option' ).each(function(){
			if(basecurvearr.indexOf(jQuery(this).text()) === -1) {
				//console.log("text-> " + jQuery(this).text());
				if(jQuery(this).val() != ""){
					jQuery(this).remove();
				}
			}
			else {
				if(basecurvearr.length == 1) {
					singleVal = jQuery(this).text();
					jQuery(this).prop('selected', true);
				}
			}
		});
		
		if(basecurvearr.length == 1) {
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_base-curve]').parent().addClass("single_val_attr");
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_base-curve]').parent().append('<span class="attrib_val">' + singleVal + '</span>');
		}
	}
	else {
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_base-curve]' ).closest(".box2").remove();
	}
	
	/*----- Axis -----*/
	if(typeof JSON.parse(data_arr).axis != "undefined") {
		var axisData = JSON.parse(data_arr).axis;
		var axisarr = Object.keys(axisData);
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_axis] option' ).each(function(){
			if(axisarr.indexOf(jQuery(this).text()) === -1) {
				//console.log("text-> " + jQuery(this).text());
				if(jQuery(this).val() != ""){
					jQuery(this).remove();
				}
			}
			else {
				if(axisarr.length == 1) {
					singleVal = jQuery(this).text();
					jQuery(this).prop('selected', true);
				}
			}
		});
		
		if(axisarr.length == 1) {
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_axis]').parent().addClass("single_val_attr");
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_axis]').parent().append('<span class="attrib_val">' + singleVal + '</span>');
		}
	}
	else {
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_axis]' ).closest(".box2").remove();
	}
	
	/*----- Cylinder -----*/
	if(typeof JSON.parse(data_arr).cylinder != "undefined") {
		var cylinderData = JSON.parse(data_arr).cylinder;
		var cylinderarr = Object.keys(cylinderData);
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_cylinder] option' ).each(function(){
			if(cylinderarr.indexOf(jQuery(this).text()) === -1) {
				//console.log("text-> " + jQuery(this).text());
				if(jQuery(this).val() != ""){
					jQuery(this).remove();
				}
			}
			else {
				if(cylinderarr.length == 1) {
					singleVal = jQuery(this).text();
					jQuery(this).prop('selected', true);
				}
			}
		});
		
		if(cylinderarr.length == 1) {
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_cylinder]').parent().addClass("single_val_attr");
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_cylinder]').parent().append('<span class="attrib_val">' + singleVal + '</span>');
		}
	}
	else {
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_cylinder]' ).closest(".box2").remove();
	}
	
	/*----- Addition -----*/
	if(typeof JSON.parse(data_arr).addition != "undefined") {
		var additionData = JSON.parse(data_arr).addition;
		var additionarr = Object.keys(additionData);
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_addition] option' ).each(function(){
			if(additionarr.indexOf(jQuery(this).text()) === -1) {
				//console.log("text-> " + jQuery(this).text());
				if(jQuery(this).val() != ""){
					jQuery(this).remove();
				}
			}
			else {
				if(additionarr.length == 1) {
					singleVal = jQuery(this).text();
					jQuery(this).prop('selected', true);
				}
			}
		});
		
		if(additionarr.length == 1) {
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_addition]').parent().addClass("single_val_attr");
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_addition]').parent().append('<span class="attrib_val">' + singleVal + '</span>');
		}
	}
	else {
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_addition]' ).closest(".box2").remove();
	}
	
	/*----- Dominance -----*/
	if(typeof JSON.parse(data_arr).dominance != "undefined") {
		var dominanceData = JSON.parse(data_arr).dominance;
		var dominancearr = Object.keys(dominanceData);
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_dominance] option' ).each(function(){
			if(dominancearr.indexOf(jQuery(this).text()) === -1) {
				//console.log("text-> " + jQuery(this).text());
				if(jQuery(this).val() != ""){
					jQuery(this).remove();
				}
			}
			else {
				if(dominancearr.length == 1) {
					singleVal = jQuery(this).text();
					jQuery(this).prop('selected', true);
				}
			}	
		});
		
		if(dominancearr.length == 1) {
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_dominance]').parent().addClass("single_val_attr");
			jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_dominance]').parent().append('<span class="attrib_val">' + singleVal + '</span>');			
		}
	}
	else {
		jQuery(variationsForm).find( 'select[data-attribute_name=attribute_pa_dominance]' ).closest(".box2").remove();
	}
}

function readURL(input, target) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		
		reader.onload = function(e) {
			var mimeType=input.files[0]['type'];
			var ext = jQuery(input).val().split('.').pop().toLowerCase();
			const fsize=input.files[0].size;
			const fileSize= Math.round((fsize / 1024));
			var targetimg = jQuery(rx_type_id + " .cl-uploaded-image");
			if (fileSize > 10240) {
				console.log('File too big!');	
				jQuery(rx_type_id + ' .cl-uploaded-image input[type="file"]').val('');
				jQuery(rx_type_id + ' .cl-image-area').css({"border-color":"#e31937", "background-color": "#e319370d"});          
				if(jQuery(rx_type_id + ' .upload_img_wraper').find('.image-notice').length == 0) { 
					jQuery(rx_type_id + ' .upload_img_wraper').append('<span class="image-notice">File too large. Please upload a file less than 10mb.</span>');		
				}
				else {		
					jQuery(rx_type_id + ' .upload_img_wraper').find('.image-notice').html("File too large. Please upload a file less than 10mb.");
				}	
				return false;				
			}
			else {
				if(mimeType.split('/')[0] === 'image'){
				   jQuery(rx_type_id + ' .' + target).attr('src', e.target.result);  
				   jQuery(rx_type_id + ' .cl-image-area').fadeOut(100);
					setTimeout(function() {
						jQuery(targetimg).slideDown("fast");  
						jQuery(rx_type_id + " .img-up-success").slideDown("fast"); 
					}, 100);
					jQuery(rx_type_id + ' .cl-image-area').css({"border-color": "#aec1e3", "background-color": "#ffffff"});  
					jQuery(rx_type_id + ' .upload_img_wraper .image-notice').remove();
				   return true;			   
				}	
				else if (jQuery.inArray(ext, ['pdf','doc','docx','txt']) == -1) {
					console.log('invalid File Type!');
					jQuery(rx_type_id + ' .cl-uploaded-image input[type="file"]').val('');
					jQuery(rx_type_id + ' .cl-image-area').css({"border-color":"#e31937", "background-color": "#e319370d"});          
					if(jQuery(rx_type_id + ' .upload_img_wraper').find('.image-notice').length == 0) { 
						jQuery(rx_type_id + ' .upload_img_wraper').append('<span class="image-notice">Please upload a valid file type.</span>');		
					}
					else {		
						jQuery(rx_type_id + ' .upload_img_wraper').find('.image-notice').html("Please upload a valid file type.");
					}	
					return false;
				}
				else {
					jQuery(rx_type_id + ' .' + target).attr('src', "/wp-content/plugins/opticommerce-cl/assets/img/doc-placeholder.jpg");
					jQuery(rx_type_id + ' .cl-image-area').fadeOut(100);
					setTimeout(function() {
						jQuery(targetimg).slideDown("fast"); 
						jQuery(rx_type_id + " .img-up-success").slideDown("fast");
					}, 100);
					jQuery(targetimg).append('<span class="file-name">' + jQuery(input).val().split('\\').pop() + '</span>');
					jQuery(rx_type_id + ' .cl-image-area').css({"border-color": "#aec1e3", "background-color": "#ffffff"}); 
					jQuery(rx_type_id + ' .upload_img_wraper .image-notice').remove();
					return true;
				}	
			}	
		}
		reader.readAsDataURL(input.files[0]);
	}
}

function showSelectionStep(currStep, nextStep){
	var fname = jQuery("#first_name").val();
	var lname = jQuery("#last_name").val();
	var valid = true;
	if(fname === "") {
		jQuery("#first_name").next().fadeIn();
		valid = false;
	}
	if(lname === "") {
		jQuery("#last_name").next().fadeIn();
		valid = false;
	}
	if(valid) {
		jQuery("#" + currStep).hide();
		jQuery("#" + nextStep).fadeIn();
	}	
}

function check_radio(rx_id, e, th){
	e.preventDefault();
	var parElem = jQuery(th).parent();
	jQuery(".option-wrapper").not(parElem).removeClass("selected");
	parElem.addClass("selected");
	jQuery("#" + rx_id).attr("checked", "checked");
	var rx_step_id = jQuery(th).attr("href");
	rx_type_id = rx_step_id;
	//console.log(rx_type_id);
	jQuery(".tab-pane").hide();  
	jQuery(rx_step_id).show();
	jQuery("#rx-type-selection").hide();
	jQuery("#rx-type").fadeIn();
	jQuery(".footer-nav .close-btn").hide();	
	prev_step = "rx-type-selection";
	curr_step = "rx-type";
}

function show_fields(rx_id, e, th){
	e.preventDefault();
	var tabElem = jQuery(jQuery(th).attr("href"));
	var rx_step_id = jQuery(th).attr("href");
	jQuery(".verification-method .col-50 a").not(th).removeClass("selected");
	jQuery(th).addClass("selected");
	jQuery("#" + rx_id).attr("checked", "checked");
	console.log(rx_step_id);
	jQuery(".op-practice-details .tab-pane").not(tabElem).hide();  
	jQuery(".verification-method").find('span.error').fadeOut("fast");
	jQuery(rx_step_id).fadeIn();	
}

function check_vlidation(rxType){
	
	var is_prescription_valid = true;
	var is_rx_valid = true;
	
	/*---------------------*/
	/*----- Enter new -----*/
	/*---------------------*/
	if(rxType == "Enter RX") {
		
		/*===== Right Eye =====*/
		if(jQuery('input[name="attribute_pa_eye-right"]').is(":checked")) {
			//---- sphere ----
			if(jQuery("#pa_sphere0").length) {
				var rightSph = jQuery("#pa_sphere0 option:selected").val();
				if (rightSph === "") {
					jQuery("#pa_sphere0").parent().css("border-color", "#e31937");         
					is_prescription_valid = false;          
					is_rx_valid = false;
					console.log("sphere right-> " + rightSph);
				} 
				else {
					jQuery("#pa_sphere0").parent().css("border-color", "#c0c0c0");          
					//console.log("sphere right-> " + rightSph);
				}
			}
			//----- Diameter -----
			if(jQuery("#pa_diameter0").length) {
				var rightDia = jQuery("#pa_diameter0 option:selected").val();
				if (rightDia === "") {
					jQuery("#pa_diameter0").parent().css("border-color", "#e31937");         
					is_prescription_valid = false;          
					is_rx_valid = false;
					console.log("Dia right-> " + rightDia);
				} 
				else {
					jQuery("#pa_diameter0").parent().css("border-color", "#c0c0c0");          
					//console.log("Dia right-> " + rightDia);
				}
			}
			//----- Base Curve -----
			if(jQuery("#pa_base-curve0").length) {
				var rightBC = jQuery("#pa_base-curve0 option:selected").val();
				if (rightBC === "") {
					jQuery("#pa_base-curve0").parent().css("border-color", "#e31937"); 
					is_prescription_valid = false;          
					is_rx_valid = false;
					console.log("BC right-> " + rightBC);
				} 
				else {
					jQuery("#pa_base-curve0").parent().css("border-color", "#c0c0c0");          
					//console.log("BC right-> " + rightBC);
				}
			}
			//----- Cylinder -----
			if(jQuery("#pa_cylinder0").length) {
				var rightcyl = jQuery("#pa_cylinder0 option:selected").val();
				if (rightcyl === "") {
					jQuery("#pa_cylinder0").parent().css("border-color", "#e31937"); 
					is_prescription_valid = false;          
					is_rx_valid = false;
				} 
				else {
					jQuery("#pa_cylinder0").parent().css("border-color", "#c0c0c0"); 
				}
			}
			//----- Axis -----
			if(jQuery("#pa_axis0").length) {
				var rightaxis = jQuery("#pa_axis0 option:selected").val();
				if (rightaxis === "") {
					jQuery("#pa_axis0").parent().css("border-color", "#e31937"); 
					is_prescription_valid = false;          
					is_rx_valid = false;
				} 
				else {
					jQuery("#pa_axis0").parent().css("border-color", "#c0c0c0"); 
				}
			}
			//----- ADD -----
			if(jQuery("#pa_add0").length) {
				var rightadd = jQuery("#pa_add0 option:selected").val();
				if (rightadd === "") {
					jQuery("#pa_add0").parent().css("border-color", "#e31937"); 
					is_prescription_valid = false;          
					is_rx_valid = false;
				} 
				else {
					jQuery("#pa_add0").parent().css("border-color", "#c0c0c0"); 
				}
			}
			//----- Dominance -----
			if(jQuery("#pa_dominance0").length) {
				var rightdominance = jQuery("#pa_dominance0 option:selected").val();
				if (rightdominance === "") {
					jQuery("#pa_dominance0").parent().css("border-color", "#e31937"); 
					is_prescription_valid = false;          
					is_rx_valid = false;
				} 
				else {
					jQuery("#pa_dominance0").parent().css("border-color", "#c0c0c0"); 
				}
			}
		}	
		
		/*===== Left Eye =====*/
		if(jQuery('input[name="attribute_pa_eye-left"]').is(":checked")) {
			//---- sphere ----
			if(jQuery("#pa_sphere1").length) {
				var leftSph = jQuery("#pa_sphere1 option:selected").val();
				if (leftSph === "") {
					jQuery("#pa_sphere1").parent().css("border-color", "#e31937");
					is_prescription_valid = false;          
					is_rx_valid = false;
					console.log("sphere left-> " + leftSph);
				} 
				else {
					jQuery("#pa_sphere1").parent().css("border-color", "#c0c0c0");          
					//console.log("sphere left-> " + leftSph);
				}
			}
			//----- Diameter -----
			if(jQuery("#pa_diameter1").length) {
				var leftDia = jQuery("#pa_diameter1 option:selected").val();
				if (leftDia === "") {
					jQuery("#pa_diameter1").parent().css("border-color", "#e31937");  
					is_prescription_valid = false;          
					is_rx_valid = false;
					console.log("Dia left-> " + leftDia);
				} 
				else {
					jQuery("#pa_diameter1").parent().css("border-color", "#c0c0c0"); 
					//console.log("Dia left-> " + leftDia);
				}
			}
			//----- Base Curve -----
			if(jQuery("#pa_base-curve1").length) {
				var leftBC = jQuery("#pa_base-curve1 option:selected").val();
				if (leftBC === "") {
					jQuery("#pa_base-curve1").parent().css("border-color", "#e31937");        
					is_prescription_valid = false;          
					is_rx_valid = false;
					console.log("BC left-> " + leftBC);
				} 
				else {
					jQuery("#pa_base-curve1").parent().css("border-color", "#c0c0c0");  
					//console.log("BC left-> " + leftBC);
				}
			}
			//----- Cylinder -----
			if(jQuery("#pa_cylinder1").length) {
				var leftcyl = jQuery("#pa_cylinder1 option:selected").val();
				if (leftcyl === "") {
					jQuery("#pa_cylinder1").parent().css("border-color", "#e31937"); 
					is_prescription_valid = false;          
					is_rx_valid = false;
				} 
				else {
					jQuery("#pa_cylinder1").parent().css("border-color", "#c0c0c0"); 
				}
			}
			//----- Axis -----
			if(jQuery("#pa_axis1").length) {
				var leftaxis = jQuery("#pa_axis1 option:selected").val();
				if (leftaxis === "") {
					jQuery("#pa_axis1").parent().css("border-color", "#e31937"); 
					is_prescription_valid = false;          
					is_rx_valid = false;
				} 
				else {
					jQuery("#pa_axis1").parent().css("border-color", "#c0c0c0"); 
				}
			}
			//----- ADD -----
			if(jQuery("#pa_add1").length) {
				var leftadd = jQuery("#pa_add1 option:selected").val();
				if (leftadd === "") {
					jQuery("#pa_add1").parent().css("border-color", "#e31937"); 
					is_prescription_valid = false;          
					is_rx_valid = false;
				} 
				else {
					jQuery("#pa_add1").parent().css("border-color", "#c0c0c0"); 
				}
			}
			//----- Dominance -----
			if(jQuery("#pa_dominance1").length) {
				var leftdominance = jQuery("#pa_dominance1 option:selected").val(); 
				if (leftdominance === "") {
					jQuery("#pa_dominance1").parent().css("border-color", "#e31937"); 
					is_prescription_valid = false;          
					is_rx_valid = false;
				} 
				else {
					jQuery("#pa_dominance1").parent().css("border-color", "#c0c0c0"); 
				}
			}
		}
		
		//----- Verification method -----//
		//var verMethod = jQuery("input[name=verification_type]:checked").val();
		if(!jQuery("input[name=verification_type]").is(":checked")) {
			if(jQuery(".verification-method").find('span.error').length == 0) {
				jQuery(".verification-method").append('<span class="error">* Please select a verification method.</span>');
			}
			else {		
				jQuery(".verification-method").find('span.error').fadeIn("fast");
			}        
			is_prescription_valid = false;          
		} 
		else {
			jQuery(".verification-method").find('span.error').fadeOut("fast"); 
		}
		
		/*====== Upload ======*/
		if(jQuery(rx_type_id).find('input[type="file"]').length && jQuery("input[name=verification_type]:checked").val() == "RX Entry With Photo" ) {
			if(jQuery(rx_type_id).find('input[type="file"]').get(0).files.length == 0) {
				jQuery('.cl-image-area').css({"border-color":"#e31937", "background-color": "#e319370d"});          
				if(jQuery(rx_type_id + ' .upload_img_wraper').find('.image-notice').length == 0) { 
					jQuery(rx_type_id + ' .upload_img_wraper').append('<span class="image-notice">* Please upload a copy of prescription.</span>');		
				}
				else {		
					jQuery(rx_type_id + ' .upload_img_wraper').find('.image-notice').html("* Please upload a copy of prescription.");
				}			
				is_prescription_valid = false; 	
			} 
			else {
				jQuery(rx_type_id + ' .cl-image-area').css({"border-color": "#c0c0c0", "background-color": "#ffffff"}); 
				jQuery(rx_type_id + ' .upload_img_wraper').find('.image-notice').fadeOut("fast");	
			}
		}
		
		/*===== Optician Detail =====*/
		if(jQuery("input[name=verification_type]:checked").val() == "RX Entry With Opticians Details") {
			if(jQuery(rx_type_id).find('input[name="opticians_name"]').val() === "") {
				jQuery(rx_type_id).find('input[name="opticians_name"]').parent().css("border-color", "#e31937"); 
				jQuery(rx_type_id).find('input[name="opticians_name"]').parent().next().fadeIn();
				is_prescription_valid = false; 
			}
			else {
				jQuery(rx_type_id).find('input[name="opticians_name"]').parent().css("border-color", "#c0c0c0");          
				jQuery(rx_type_id).find('input[name="opticians_name"]').parent().next().fadeOut("fast");
			}
			
			if(jQuery(rx_type_id).find('input[name="opticians_phone"]').val() === "") {
				jQuery(rx_type_id).find('input[name="opticians_phone"]').parent().css("border-color", "#e31937"); 
				jQuery(rx_type_id).find('input[name="opticians_phone"]').parent().next().fadeIn();
				is_prescription_valid = false; 
			}
			else {
				jQuery(rx_type_id).find('input[name="opticians_phone"]').parent().css("border-color", "#c0c0c0");          
				jQuery(rx_type_id).find('input[name="opticians_phone"]').parent().next().fadeOut("fast");
			}
			
			if(jQuery(rx_type_id).find('input[name="opticians_address"]').val() === "") {
				jQuery(rx_type_id).find('input[name="opticians_address"]').parent().css("border-color", "#e31937"); 
				jQuery(rx_type_id).find('input[name="opticians_address"]').parent().next().fadeIn();
				is_prescription_valid = false; 
			}
			else {
				jQuery(rx_type_id).find('input[name="opticians_address"]').parent().css("border-color", "#c0c0c0");          
				jQuery(rx_type_id).find('input[name="opticians_address"]').parent().next().fadeOut("fast");
			}
			
			if(jQuery(rx_type_id).find('input[name="dob"]').val() === "") {
				jQuery(rx_type_id).find('input[name="dob"]').parent().css("border-color", "#e31937"); 
				jQuery(rx_type_id).find('input[name="dob"]').parent().next().fadeIn();
				is_prescription_valid = false; 
			}
			else {
				jQuery(rx_type_id).find('input[name="dob"]').parent().css("border-color", "#c0c0c0");          
				jQuery(rx_type_id).find('input[name="dob"]').parent().next().fadeOut("fast");
			}
		}
		
	}
	
	jQuery(rx_type_id).find('input.required').each(function(){
		if(jQuery(this).val() === "") {
			jQuery(this).parent().css("border-color", "#e31937"); 
			jQuery(this).parent().next().fadeIn();
			is_prescription_valid = false; 
		}
		else {
			jQuery(this).parent().css("border-color", "#c0c0c0");           
			jQuery(this).parent().next().fadeOut("fast");
		}
	});	
	
	/*====== Upload ======*/
	if(jQuery(rx_type_id).find('input[type="file"]').length && rxType != "Enter RX") {
		if(jQuery(rx_type_id).find('input[type="file"]').get(0).files.length == 0) {
			jQuery('.cl-image-area').css({"border-color":"#e31937", "background-color": "#e319370d"});          
			if(jQuery(rx_type_id + ' .upload_img_wraper').find('.image-notice').length == 0) { 
				jQuery(rx_type_id + ' .upload_img_wraper').append('<span class="image-notice">* Please upload a copy of prescription.</span>');		
			}
			else {		
				jQuery(rx_type_id + ' .upload_img_wraper').find('.image-notice').html("* Please upload a copy of prescription.");
			}			
			is_prescription_valid = false; 	
		} 
		else {
			jQuery(rx_type_id + ' .cl-image-area').css({"border-color": "#c0c0c0", "background-color": "#ffffff"}); 
			jQuery(rx_type_id + ' .upload_img_wraper').find('.image-notice').fadeOut("fast");	
		}
	}
	
	/*===== Existing patient practice =====*/
	if(jQuery(rx_type_id).find('select[name="practice_name"]').length) {
		var practiceName = jQuery(rx_type_id).find('select[name="practice_name"]').val(); 
		if (practiceName === "") {
			jQuery(rx_type_id).find('select[name="practice_name"]').parent().css("border-color", "#e31937");  
			if(jQuery(rx_type_id).find('select[name="practice_name"]').closest(".col-50").find('span.error').length == 0) {
				jQuery(rx_type_id).find('select[name="practice_name"]').closest(".col-50").append('<span class="error">* Select practice</span>');
			}
			else {		
				jQuery(rx_type_id).find('select[name="practice_name"]').closest(".col-50").find('span.error').fadeIn("fast");
			}        
			is_prescription_valid = false;          
		} 
		else {
			jQuery(rx_type_id).find('select[name="practice_name"]').parent().css("border-color", "#c0c0c0");          
			jQuery(rx_type_id).find('select[name="practice_name"]').closest(".col-50").find('span.error').fadeOut("fast");
		}
	}
	
	if(!is_rx_valid) { 
		if(jQuery(".sp-prescription-box-elements").find('span.error').length == 0) {
			jQuery(".sp-prescription-box-elements").prepend('<span class="error top">* Please select all required fields.</span>'); 
		}
		else {		
			jQuery(".sp-prescription-box-elements").find('span.error').fadeIn("fast");
		} 
	}
	else {
		jQuery(".sp-prescription-box-elements").find('span.error').fadeOut("fast");
	}
	
	if (!is_prescription_valid) { 
		return false;            
	}
	else {
		return true;
	}
}

function submitClForm(e){ 
	
	e.preventDefault();
	
	var proID = jQuery('input[name="product_id"]').val();
	var varID = jQuery('input[name="variation_id[]"]').val();
	var rxType = jQuery('input[name="cl_type"]:checked').val();
	var rightQty = "";
	var leftQty = "";
	var valid = check_vlidation(rxType);
	var cartUrl = jQuery('input[name="cart_url"]').val();

	if(jQuery('input[name="attribute_pa_eye-right"]').is(":checked")) {
		rightQty = jQuery('#quantity-1').val();
		console.log(rightQty);
	}
	else {
		rightQty = 0;
	}	
	
	if(jQuery('input[name="attribute_pa_eye-left"]').is(":checked")) {
		leftQty = jQuery('#quantity-2').val();
		console.log(leftQty);
	} 
	else {
		leftQty = 0;
	}	
	
	if(!valid) { 
		jQuery('html, body').animate({
			scrollTop: jQuery("#cl-steps-wrapper").offset().top          
		}, 1200);
		jQuery(".fixedloader").fadeOut(); 
		return;
	}
	else {
		jQuery(".fixedloader").fadeIn();		
	}
	
	var formData = new FormData();
	
	formData.append('action', 'woocommerce_add_to_cart_cl_products_callback');
    formData.append('product_id', proID);
    formData.append('add-to-cart', proID);
    formData.append('variation_id', varID);
    formData.append('right_qty', rightQty);
    formData.append('left_qty', leftQty);
    formData.append('cl_type', rxType);
	
	if(jQuery("#pa_color").length) {
		formData.append('attribute_pa_color', jQuery("#pa_color").val());
	}	
	
	/*---------------------*/
	/*----- Enter new -----*/
	/*---------------------*/
	if(rxType == "Enter RX") {
		var variationData = {};
		
		variations_right = jQuery(rx_type_id).find( '#variationRow0 select[name^=attribute]' );
		variations_left = jQuery(rx_type_id).find( '#variationRow1 select[name^=attribute]' );

		if(jQuery('input[name="attribute_pa_eye-right"]').is(":checked")) {
			variations_right.each( function() {
			
				var jQuerythis = jQuery( this ),
					attributeName = jQuerythis.data( 'attribute_name' ),
					attributevalue = jQuerythis.val(),
					index;
				
				variationData[attributeName] = {}; 
				
				variationData[attributeName]["right"] = attributevalue;
			
			});
		}
		
		if(jQuery('input[name="attribute_pa_eye-left"]').is(":checked")) { 
			variations_left.each( function() {
			
				var jQuerythis = jQuery( this ),
					attributeName = jQuerythis.data( 'attribute_name' ),
					attributevalue = jQuerythis.val(),
					index;
				
				variationData[attributeName]["left"] = attributevalue;
			
			});
		}	
		
		/*----- CL RX Verification Info -----*/
		formData.append('verification_type', jQuery('input[name="verification_type"]:checked').val()); 	 			
		formData.append('variations', JSON.stringify(variationData));
		
		if(jQuery("input[name=verification_type]:checked").val() == "RX Entry With Photo") {
			formData.append('presc_upload', jQuery(rx_type_id + ' input[name="cl_img"]')[0].files[0]); 
		}	
		else {
			formData.append('opticians_name', jQuery(rx_type_id).find('input[name="opticians_name"]').val());
			formData.append('opticians_phone', jQuery(rx_type_id).find('input[name="opticians_phone"]').val());
			formData.append('opticians_address', jQuery(rx_type_id).find('input[name="opticians_address"]').val());
			formData.append('existing_patient_dob', jQuery(rx_type_id).find('input[name="dob"]').val());
		}	
 		
		//console.log(JSON.stringify(variationData));
		formData.append('upccode', jQuery('input[name="upccode"]').val()); 
	
	}
	
	/*===== Upload Prescription Step =====*/
	if(jQuery(rx_type_id).find('input[type="file"]').length && rxType != "Enter RX") {
		formData.append('presc_upload', jQuery(rx_type_id + ' input[name="cl_img"]')[0].files[0]);
	}
	
	/*===== Existing Patient =====*/
	if(jQuery(rx_type_id).find('input[name="existing_patient_dob"]').length) {
		formData.append('dob', jQuery(rx_type_id).find('input[name="existing_patient_dob"]').val());
	}	
	if(jQuery(rx_type_id).find('select[name="practice_name"]').length) {
		formData.append('practice_name', jQuery(rx_type_id).find('select[name="practice_name"]').val());
	}			
	
	/*===== Patient Info =====*/
	if(jQuery(rx_type_id + ' input[name="first_name"]').length) {
		formData.append('first_name', jQuery(rx_type_id + ' input[name="first_name"]').val());
	}
	if(jQuery(rx_type_id + ' input[name="last_name"]').length) {
		formData.append('last_name', jQuery(rx_type_id + ' input[name="last_name"]').val());
	}	
	
	jQuery.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'post',
        data: formData,
        timeout:50000,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response){
			jQuery(".fixedloader").fadeOut();
			window.location.href = cartUrl;
            if(response != '0'){
				
            }else{
                alert('error!!');
            }
        },
    }); 
}

function apiValidate(parEle) {
	
	jQuery(".fixedloader").fadeIn();
	
	var proID = jQuery('input[name="cl_id"]').val();
	var formData = new FormData();
	
	formData.append('action', 'cl_product_validity_check');
    formData.append('clid', proID);
	
	var variationData = {};
		
	variations = jQuery(parEle).find( 'select[name^=attribute]' );

	variations.each( function() {
		var jQuerythis = jQuery( this ),
		attributeName = jQuerythis.data( 'attribute_name' ),
		attributevalue = jQuerythis.find(":selected").text();;
						
		if(attributeName == "attribute_pa_sphere") {
			formData.append('sphere', attributevalue);
		}				
		if(attributeName == "attribute_pa_diameter") {
			formData.append('diameter', attributevalue);
		}				
		if(attributeName == "attribute_pa_base-curve") {
			formData.append('basecurve', attributevalue);
		}				
		if(attributeName == "attribute_pa_cylinder") {
			formData.append('cylinder', attributevalue);
		}				
		if(attributeName == "attribute_pa_axis") {
			formData.append('axis', attributevalue);
		}				
		if(attributeName == "attribute_pa_addition") {
			formData.append('addition', attributevalue);
		}				
		if(attributeName == "attribute_pa_dominance") {
			formData.append('dominance', attributevalue);
		}				
	}); 
	
	if(jQuery("#pa_color").length) {
		formData.append('color', jQuery("#pa_color").val());
	}	
	
	jQuery.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'post',
        data: formData,
        timeout:50000,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response){
			jQuery(".fixedloader").fadeOut();
            if(response != '0'){
				//console.log("aai oeee");
            }else{
                alert('error!!');
            }
        },
    }); 		
	
}

jQuery( document ).ready(function($) {
     
	$(".variations_form")[0].reset();
	
	updateRxDropdowns(product_parameters);
	
	$(".lg-custom-drop").each(function(){
		if($(this).find("select option:selected").val() != "") {
			$(this).addClass("slide");
		}
	});
	$(".lg-custom-input").each(function(){
		console.log($(this).find("input").val());
		if($(this).find("input").val() != "") {
			$(this).addClass("slide");
		}
	});	
	
	/*===== Eyes Dropdown/Selection =====*/
	$('input[name^="attribute_pa_eye"]').on("change", function(){
		if($(this).is(":checked")) {
			$(this).closest("li").removeClass("disabled");
			if($(this).val() == "right") {
				$('input[name^="attribute_pa_eye-right"]').prop("checked", true);
				$('input[name^="attribute_pa_eye-right"]').closest("li").removeClass("disabled");
			}
			else if($(this).val() == "left") {
				$('input[name^="attribute_pa_eye-left"]').prop("checked", true);
				$('input[name^="attribute_pa_eye-left"]').closest("li").removeClass("disabled");
			}
		}
		else {
			$(this).closest("li").addClass("disabled");
			if($(this).val() == "right") {
				$('input[name^="attribute_pa_eye-right"]').prop("checked", false);
				$('input[name^="attribute_pa_eye-right"]').closest("li").addClass("disabled");
			}
			else if($(this).val() == "left") {
				$('input[name^="attribute_pa_eye-left"]').prop("checked", false);
				$('input[name^="attribute_pa_eye-left"]').closest("li").addClass("disabled"); 
			}
			$(this).closest("li").find(".lg-custom-drop").css("border-color", "#c0c0c0");  
		}		
	});
	
	/*===== Back Button =====*/
	$(".back-btn").on("click", function(e){
		e.preventDefault();
		$("#" + curr_step).hide();
		$("#" + prev_step).fadeIn();
		$(".footer-nav .close-btn").show();
		
		$('input[name="verification_type"]').prop("checked", false);
		$('input[name="verification_type"]').parent().removeClass("selected");
	});	
	
	/*====== Close RX pop up ======*/
	$(".close-btn").on("click", function(e){
		e.preventDefault();
		
		$("#cl-steps-wrapper").fadeOut(200);	
		$("#cl-steps-wrapper section").fadeOut(200); 
		
		setTimeout(function(){
			jQuery("form.variations_form").appendTo(".product-prescription .row1");
			$(".colour-option").show();
			$("#eye-selection").show();
			$("#continue-btn").show();
			$(".fl-builder-content").fadeIn(200);
		}, 200);	
		
		$('input[name="verification_type"]').prop("checked", false);
		$('input[name="verification_type"]').parent().removeClass("selected")
		prev_step = "";
		curr_step = "";
	});
	
	/*====== Decrease qty ======*/
	$(".stepper .decrease").on("click", function(e){

		e.preventDefault();
		
		var qty = parseInt($(this).next().text().split(" ").shift());
		var qtyText = "";
		
		if(qty > 0) {
			qty = qty - 1;
		}
		
		if(qty == 0) {
			$(this).addClass("disabled");
			qtyText = " boxes";
			//$(this).closest("li").find('input[name^="attribute_pa_eye"]').prop("checked", false).change();
		}	
		else if(qty == 1) {
			qtyText = " box";
			$(this).removeClass("disabled");
		}
		else {
			qtyText = " boxes";
			$(this).removeClass("disabled");
		}
		
		$(this).next().html(qty + qtyText); 
		$(this).parent().find(".increase").removeClass("disabled");
		
		//console.log(qty + qtyText);
	});
	/*====== Increase qty ======*/
	$(".stepper .increase").on("click", function(e){

		e.preventDefault();
		
		var qty = parseInt($(this).prev().text().split(" ").shift()); 
		var qtyText = "";
		
		if(qty < 12) {
			qty = qty + 1;
		}		
		
		if(qty == 12) {
			$(this).addClass("disabled");
			qtyText = " boxes";
		}
		else if(qty == 1) {
			qtyText = " box";
			$(this).removeClass("disabled");
		}
		else {
			qtyText = " boxes";
			$(this).removeClass("disabled");
		}
		
		$(this).prev().html(qty + qtyText); 
		$(this).parent().find(".decrease").removeClass("disabled");
		$("#eye-selection").find('span.error').fadeOut("fast");
		
		//console.log(qty + qtyText);
	});
	/*=============================*/ 
	
	/*====== Continue to Steps ======*/
	$("#continue-btn a").on("click", function(e){
		
		e.preventDefault();
		
		var isValid = true;
		var colorVal = "";
		var right_eye_qty = parseInt($(".right-eye-selection .stepper .q-value").text().split(" ").shift());
		var left_eye_qty = parseInt($(".left-eye-selection .stepper .q-value").text().split(" ").shift());
		
		if(jQuery("#pa_color").length) {
			colorVal = jQuery("#pa_color option:selected").val();
		}
		else {
			colorVal = "no color";
		}
		
		if(colorVal === "") {
			jQuery("#pa_color").parent().css("border-color", "#e31937"); 
			if(jQuery(".colour-option").find('span.error').length == 0) {
				jQuery(".colour-option").append('<span class="error">* Please select lens color.</span>'); 
			}
			else {		
				jQuery(".colour-option").find('span.error').fadeIn("fast");
			}
			isValid = false;
		}
		
		if(right_eye_qty == 0 && left_eye_qty == 0) {
			if($("#eye-selection").find('span.error').length == 0) {
				$("#eye-selection").append('<span class="error top">Please select a valid quantity.</span>');
			}
			else {		
				$("#eye-selection").find('span.error').fadeIn("fast");
			}	
			isValid = false;			
		}
		
		if(!jQuery('input[name="attribute_pa_eye-right"]').is(":checked") && !jQuery('input[name="attribute_pa_eye-left"]').is(":checked") ) {
			if($("#eye-selection").find('span.error').length == 0) {
				$("#eye-selection").append('<span class="eyeerror top">Please select an eye.</span>');
			}
			else {		
				$("#eye-selection").find('span.eyeerror').fadeIn("fast");
			}	
			isValid = false;			
		}
		else {
			$("#eye-selection").find('span.eyeerror').fadeOut("fast");
		}
		
		if(isValid) {	
			$("div.fl-builder-content").hide(); 
			jQuery("form.variations_form").appendTo("#fl-main-content");
			$(".colour-option").hide();
			$("#eye-selection").hide();
			$("#continue-btn").hide();
			jQuery(".colour-option").find('span.error').fadeOut("fast");
			$("#quantity-1").val(right_eye_qty);
			$("#quantity-2").val(left_eye_qty);
			$("#rx-type-selection").show();
			$("#cl-steps-wrapper").fadeIn(200);	 
			prev_step = "";
			curr_step = "cl-steps-wrapper";
		}	
			
	});
	/*=============================*/
	
	/*====== Text Fields Validation =======*/
	$('input.required').on("change", function(){
		if($(this).val() != "") {
			$(this).parent().next().fadeOut();
			$(this).parent().css("border-color", "#c0c0c0");
			$(this).parent().addClass("slide"); 
		}
	});
	$('.doc-info input').on("change", function(){
		if($(this).val() != "") {
			$(this).parent().next().fadeOut();
			$(this).parent().css("border-color", "#c0c0c0");
			$(this).parent().addClass("slide");
		}
	});
	/*=============================*/
	
	/*===== File Upload =====*/
	$('.upload_img input[type="file"]').change(function() {	 	 
		var _this_ = this;
		var target = jQuery(rx_type_id + ' .cl-uploaded-image');
		jQuery(target).find("img").remove();
		var img = jQuery('<img class="dynamic">'); 
		img.appendTo(rx_type_id + ' .cl-uploaded-image');
		var img_id = "dynamic";
		if (readURL(_this_, img_id)) {
			jQuery(rx_type_id + ' .cl-image-area').fadeOut(100);
			setTimeout(function() {
				jQuery(target).slideDown("fast"); 
			}, 100);
		}
	});
	
	$(".cl-uploaded-image .close-upload").on("click", function(){
		jQuery(rx_type_id + ' .cl-uploaded-image').fadeOut(100);
		jQuery(rx_type_id + ' .img-up-success').fadeOut(100);		
		jQuery(rx_type_id + ' .cl-uploaded-image .file-name').remove();
		setTimeout(function() {
			jQuery(rx_type_id + ' .cl-image-area input[type="file"]').val('');
			jQuery(rx_type_id + ' .cl-image-area').slideDown("fast");
		}, 100);	
	});
	/*==============================*/
	
	/*===== Dropdowns/Input label effect =====*/
	$('.lg-custom-drop select').on('change', function() {
		if( this.value != "") {
			$(this).parent().addClass("slide"); 
			$(this).closest(".box2").find('span.error').fadeOut("fast");
			$(this).parent().css("border-color", "#c0c0c0");        
			$(this).closest(".col-50").find('span.error').fadeOut("fast");	
			$(this).closest(".colour-option").find('span.error').fadeOut("fast");	
		} 
	});
	$('.lg-custom-input input').keydown(function() {
        $(this).parent().addClass("slide"); 
    });
    $('.lg-custom-input input').focus(function() {
        if ($(this).val() === '') {
            $(this).parent().addClass("slide"); 
        }
    });
    $('.lg-custom-input input').blur(function() {
        if ($(this).val() === '') {
            $(this).parent().removeClass("slide"); 
        }
    });
	/*=======================================*/
	
	/*===== API call =====*/
	$("ul.rx-values li .lg-custom-drop select").on('change', function() {
		var parEle = $(this).closest("li");
		var allSel = true;
		$(parEle).find("select").each(function(){ 
			if($(this).val() === "") {
				allSel = false;
			}
		});
		
		if(allSel) {
			apiValidate(parEle);
		}
	});	
	/*====================*/
	
	/*===== Switch to Enter New =====*/
	$(".switch-enter").on("click", function(e){ 
		e.preventDefault();
		var parElem = jQuery("#sp-new6").closest(".option-wrapper");
		jQuery(".option-wrapper").not(parElem).removeClass("selected");
		parElem.addClass("selected");
		jQuery("#sp-new6").attr("checked", "checked");
		var rx_step_id = "#enter_rx";
		rx_type_id = rx_step_id;
		jQuery(".tab-pane").hide();  
		jQuery(rx_step_id).fadeIn();
		//jQuery("#rx-type-selection").hide();
		//jQuery("#rx-type").fadeIn();
		//jQuery(".footer-nav .close-btn").hide();	
		//prev_step = "rx-type-selection";
		//curr_step = "rx-type";
	});
	/*====================*/
	
	/*----- Date Field -----*/
	jQuery('input[name="dob"]').attr('readonly','readonly');
	jQuery('input[name="existing_patient_dob"]').attr('readonly','readonly'); 
	jQuery('input[name="dob"]').datepicker({ 
		startView: 2,
        autoHide: true,
        format: 'dd/mm/yyyy',
        endDate: -1	 
	});
	jQuery('input[name="existing_patient_dob"]').datepicker({
        startView: 2,
        autoHide: true,
        format: 'dd/mm/yyyy',
        endDate: -1	 
    }); 
	
});