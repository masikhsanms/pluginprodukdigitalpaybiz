jQuery(document).ready(function(){

	LimaSelect();

	saveSetingAdm();

});



function LimaSelect(){

	

 	jQuery('body').find('.select-pd').select2();	

}



function saveSetingAdm(){

	var $ = jQuery;

	$('body').on('click','.btnSaveSet', function(){

		let termId = $(this).closest('.content-setting-pd').find('.select-pd option:selected')

                .toArray().map(item => item.value),

            apiUrl = $(this).closest('.content-setting-pd').find('.apiUrlPd').val(),

            // id_irs = $(this).closest('.content-setting-pd').find('.id_irs').val(),

            username_irs = $(this).closest('.content-setting-pd').find('.username_irs').val(),

            password_irs = $(this).closest('.content-setting-pd').find('.password_irs').val(),

            // pin_irs = $(this).closest('.content-setting-pd').find('.pin_irs').val(),

            markup_ppob = $(this).closest('.content-setting-pd').find('.markup_ppob').val(),



            data = {

            	action:'getSaveOptionPd',

            	'termId':termId,

            	'apiUrl':apiUrl,

            	// 'id_irs':id_irs,

            	'username_irs':username_irs,

            	'password_irs':password_irs,

            	// 'pin_irs':pin_irs,

            	'markup_ppob':markup_ppob

            };

			
			
		$.ajax({
			
			url:lima.ajaxurl,
			
			type:'POST',
			
			data:data,
			
			dataType:'JSON',
			
			success: function(response){
				

				if (response.msg == 'success') {


					alertSwiftSuccess();

				}else{

					alertSwiftWrong('Data Gagal Di Update');

				}

			},

			error: function(xhr,status,error){



			}

		});

	});

}



function alertSwiftSuccess(){

	Swal.fire(

		'Update Sukses',

		'',

		'success'

	);

}



function alertSwiftWrong($text){

	Swal.fire({

	  icon: 'error',

	  title: 'Oops...',

	  text: $text

	});

}