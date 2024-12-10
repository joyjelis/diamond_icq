/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
	'jquery',
	'mage/cookies',
	'mage/translate'
], function($) {
	'use strict';
	
	$(document).ready(function() {
		var options = {
			type: 'popup',
			responsive: true,
			innerScroll: true,
			clickableOverlay: false,
			modalClass: 'get-quote-modal',
			buttons: [],
			keyEventHandlers: {
				escapeKey: function () { 
					return; 
				}
			}
		};
		
		$("#quote, #quote-btn, #get-quote-btn, #quote-link").on('click',function(e){
			e.preventDefault();
			$('#quote-modal').modal(options).modal('openModal');
		});
	});
	
	$("#quote-popup-form").submit(function(e) {
		e.preventDefault();
		$(document).find('#jewellery_type-error').remove();
		
		var actionUrl = $(this).attr('action');
		var checked = false;
		var requiredMsg = null;
		var selectedType = null;
		$('input[name=jewellery_type]').each(function() {
			if ($(this).is(':checked')) {
				checked = true;
				selectedType = $(this).val();
			}
		});
		
		requiredMsg = '<div for="jewellery_type" generated="true" class="mage-error" id="jewellery_type-error">'
			+ $.mage.__('This is a required field.') + '</div>';
		if (checked) {
			$(document).find('#field-header').removeClass('active');
			$(document).find('#jewellery_type-error').remove();
			
			var date = new Date();
			var minutes = 15;
			date.setTime(date.getTime() + (minutes * 60 * 1000));
			$.mage.cookies.set('jewellery_type', selectedType, {expires: date, path: '/'});
			window.location.href = actionUrl;
		} else {
			$(document).find('#field-header').addClass('active');
			$(document).find('#field-header').append(requiredMsg);
		}
    });
});


require([
	'jquery',
	'Magento_Ui/js/modal/modal',
	'mage/cookies'
], function($, modal) {
	'use strict';
	
	$(document).ready(function() {
		var selectedFiles = [];
		var jewelleryType = $.mage.cookies.get('jewellery_type');
		if(!jewelleryType) {
			$(document).find('input[name=jewllery_name]').val('Others');
		} else {
			$(document).find('input[name=jewllery_name]').val(jewelleryType);
		}
		
		var formData = new FormData();
		var options = {
			type: 'popup',
			responsive: true,
			innerScroll: true,
			clickableOverlay: false,
			modalClass: 'image-preview-modal',
			buttons: []
		};
		$('#certificate-image').change(function() {
			var images = document.getElementById('certificate-image').files.length;
			for (var index = 0; index < images; index++) {
				formData.append("image[]", document.getElementById('certificate-image').files[index]);
			}
			
			var files = Array.from(this.files);
			$.each(files, function(index, file) {
				var reader = new FileReader();
				
				reader.onload = function(e) {
					var popuppreviewHtml = '<div style="position: relative; display: inline-block;">';
					popuppreviewHtml += '<img src="' + e.target.result + '" alt="Preview" />';
					popuppreviewHtml += '<button class="remove-button"><i class="fa fa-minus" aria-hidden="true"></i></button>';
					popuppreviewHtml += '</div>';
					
					$('#image').append(popuppreviewHtml);
					var parentDiv = $("#image");
					var lastDiv = parentDiv.find("div:last");
					var imgAttr = lastDiv.find("img").attr("src");
					$("#display-img").html('<img src="' + imgAttr + '" alt="Preview" />');
				};
				reader.readAsDataURL(file);
			});
			if ($(".image-preview-modal").hasClass("_show")) {
                $( "#popup-modal" ).modal( "closeModal" );
            }
			var popup = modal(options, $('#popup-modal'));
			$("#popup-modal").modal("openModal");
		});
		
		$('#submit-button').on('click', function(e) {
			e.preventDefault();
			
			var form = $(this).closest('form');
			if (form.length && !form.validation('isValid')) {
				return;
			}
			var ajaxUrl = form.attr('action');
			formData.append('params', form.serialize());
			formData.append('jewellery_type', form.find('input[name=jewllery_name]').val());
			formData.append('remarks', form.find('input[name=remarks]').val());
			formData.append('title', form.find('select[name=title]').val());
			formData.append('name', form.find('input[name=name]').val());
			formData.append('phone_number', form.find('input[name=phone_number]').val());
			$.ajax({
				url: ajaxUrl,
				type: 'POST',
				dataType: 'json',
				showLoader: true,
				cache: false,
				contentType: false,
				processData: false,
				data: formData,
				success: function(data) {
					if(data.status === 'success') {
						window.location.href = data.url;
					} 
				}
			});
		});
		
		$(document).on('click', '.remove-button', function() {
			$(this).closest('div').remove();
			var files = $('#certificate-image').prop('files');
			
			var remainingFiles = Array.from(files).filter(function(file) {
				return file.name !== $(this).siblings('img').attr('src');
			});
			
			var parentDiv = $("#image");
			var lastDiv = parentDiv.find("div:last");
			var imgAttr = lastDiv.find("img").attr("src");
			$("#display-img").html('<img src="' + imgAttr + '" alt="Preview" />');
			$('#certificate-image').prop('files', remainingFiles);
		});
		
		$(document).on('click', '#confirm-button', function() {
			var images = $('#image').html();
			$('#preview-image').html(images);
			$('#preview-image').find('.remove-button').remove();
			$('#preview-image').parent().addClass('preview-image-field');
			$("#popup-modal").modal('closeModal');
		});
		
		$('#next-button').click(function(e) {
			e.preventDefault();
			$('#add-certificate').css('display', 'none');
			$('#info-fieldset').css('display', 'block');
			
			var progress = $(this).parent().parent().parent();
			var progressVal = progress.data("progress-value");
			var progressBar = $('#progress-bar');
			progressBar.attr('aria-valuenow', progressVal);
			progressBar.attr('style', 'width:'+progressVal+'%');
		});
		
		$('#skip-button').click(function(e) {
			e.preventDefault();
			$('#add-certificate').css('display', 'none');
			$('#info-fieldset').css('display', 'block');
			
			var progress = $('#certificate-form').find('#add-certificate');
			var progressVal = progress.data("progress-value");
			var progressBar = $('#progress-bar');
			progressBar.attr('aria-valuenow', progressVal);
			progressBar.attr('style', 'width:'+progressVal+'%');
		});
		
		$('#back-button').click(function(e) {
			e.preventDefault();
			$('#add-certificate').css('display', 'block');
			$('#info-fieldset').css('display', 'none');
			
			var progress = $('#certificate-form').find('#jewllery-type');
			var progressVal = progress.data("progress-value");
			var progressBar = $('#progress-bar');
			progressBar.attr('aria-valuenow', progressVal);
			progressBar.attr('style', 'width:'+progressVal+'%');
		});
	});
});