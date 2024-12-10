(function(jQuery) {

	jQuery(function($){

        //live search
        var highlight_settings = { className: 'education-highlight', element: 'span' };

            //default mode
            $("#education-search").keyup(function(){
		        //remove highlights
                $('.educationpage-container').unhighlight(highlight_settings);

                var filter = $(this).val(),
                    count = 0,
                    found_title = false,
                    found_body = false;
                $(".educationpage_question_block").each(function(){
                    //check question title
                    found_title = $('h3.educationpage_question', this).text().search(new RegExp(filter, "i"));
                    //check question body
                    found_body = $('.educationpage_answer', this).text().search(new RegExp(filter, "i"));

                    if (found_title < 0 && found_body < 0 ) {
                        $(this).fadeOut();
                    } else {
                        $(this).show();
                        count++;
                    }
                });

                // Update the count
                $("#education-search-count").text(count);

                //highlight results
                $('.educationpage-container').highlight(filter, highlight_settings);

            });

            $('#education-reset').click(function(){
                $("#education-search").val('');
                $(".educationpage_question_block").show();
                $('.educationpage-container').unhighlight(highlight_settings);
                $("#education-search-count").text('');
                return false;
            });

            $('.educationpage_list_categories a').click(function(){
                $('#education-reset').trigger('click');
                return true;
            });


// ask a question

		$('.education_ask_button').on('click', function() {
			$('.ask_question').slideDown(150);
			$('html, body').animate({scrollTop: $('.ask_question').offset().top}, 150);
			$('.education_ask_button').hide();
			var dataForm = new VarienForm('education_ask_form', true);
		});
		$('.cancel_education_ask').on('click', function() {
			$('.education_ask_button').show();
			$('.ask_question').slideUp(150);
		});

	});

});
