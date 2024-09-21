jQuery(document).ready(function(){

	TabAct();

	clickTriger();

	ProductTab();

	loadChangeNominal();

	BeliPulsaButton();

	cekTagihan();

	bayarTagihan();

});



function clickTriger(){

	var $ = jQuery;

	$('body').find(".categori-pd li.cat-name:first").trigger("click");

}



function TabAct(){

	

	var $ = jQuery;

	

	$('body').on('click','.cat-name',function(){

		let idTerm = $(this).closest(this).find('span').data('id'),

			data = {action:'getCatId', 'idTerm':idTerm};

		

		//addClass active

		$('.cat-name').removeClass( "active" );

		$(this).addClass( "active" );

		

		//BlockUI

		$('.content-tab').block({ 

                message: '<h3>Waiting...</h3>', 

        }); 

		

		//Lempar Ajax

		$.ajax({

			url:lima.ajaxurl,

			data: data,

			type: 'POST',

			dataType:'HTML',

			success: function(response){

				if(response){

  					setTimeout($.unblockUI(), 5000); 

					$('body').find('.content-tab').html(response);

				}

				// console.log(response);

			}

		});  

	

	});



}



function ProductTab(){

	var $ = jQuery;

	$('body').on('click','.item-content', function(){

		let idProduct = $(this).closest(this).find('p').data('id'),

			// idTerm = $(this).closest(this).find('p').data('term'),

			idTerm = $(this).closest(this).find('#hidterm').val(),

			data = {action:'getProductTab','idProduct':idProduct,'idTerm':idTerm};



		//BlockUI

		$('.content-tab').block({ 

                message: '<h1>Waiting...</h1>', 

        }); 

		

		$.ajax({

			url: lima.ajaxurl,

			data: data,

			type: 'POST',

			dataType: 'HTML',

			success: function(response){

				if (response) {

					setTimeout($.unblockUI(), 5000); 

					$('body').find('.content-tab').html(response);

				}

				// console.log(response);

			}

		});



	});

}



function loadChangeNominal(){

	changeNominal('.lima-pd-nominal');

}



function changeNominal($element){

	var $ = jQuery;

	$('body').on('change',$element, function(){

		let idVariation = $(this).val(),

			data = {action:'getHargaVariation','idVariation':idVariation};



		$.ajax({

			url:lima.ajaxurl,

			type: 'POST',

			data: data,

			dataType:'HTML',

			success: function(response){

				$('body').find('.harga_beli').replaceWith(response);

			}

		});



	});

}



function BeliPulsaButton(){

	var $ = jQuery;

	$('body').on('click','#subCheckout', function(){

		let noMor = $(this).closest('.groupForm').find('.noMor').val(),

			idProduct = $(this).closest('.groupForm').find('.hidIdProduct').val(),

			idVariation = $(this).closest('.groupForm').find('.lima-pd-nominal').val(),

			data = {action:'getBeliPulsa','idProduct': idProduct,'idVariation':idVariation,'nomor':noMor};

		

		if ( isNaN( noMor )) { 

			alertSwiftWrong('Maaf Nomor Salah');

			return false;

		}else if ( noMor == ''){

			alertSwiftWrong('Maaf Nomor Harus Diisi');

			return false;

		}else if ( idVariation == '0' ) {

			alertSwiftWrong('Maaf Anda Belum Memilih Nominal');

			return false;

		}



		$('.content-tab').block({ 

                message: '<h3>Waiting...</h3>', 

        }); 



		$.ajax({

			url:lima.ajaxurl,

			data: data,

			type: 'POST',

			dataType: 'JSON',

			success: function(response){

				console.log(response);

				if(response.msg == 'available'){

					$('.content-tab').unblock();

					let alertBootstrap = '<div class="alertError">';
						alertBootstrap += '<strong>Oopps!!!</strong> ' + response.url;
						alertBootstrap += '</div>';
						
					$('body').find('.msgError').html(alertBootstrap);
					
					return false;

				}else if (response.msg == 'success') {

					window.location.href = response.url;

				}else if( response.msg == 'failed' ){
					
					$('.content-tab').unblock();

					let alertBootstrap = '<div class="alertError">';
						alertBootstrap += '<strong>Oopps!!!</strong> ' + response.url;
						alertBootstrap += '</div>';
						
					$('body').find('.msgError').html(alertBootstrap);
					
					return false;
				}

			},

			error: function(xhr,status, error){

				//alert('Error Lima : ' + xhr.status);

			}

		});

		



	}); 

}



function cekTagihan(){

	var $ = jQuery;

	$('body').on('click','#chekTagihan', function(){

		let idproduk = $(this).closest('.ppob-pd').find('.idproduk').val(),

			nomorPelanggan = $(this).closest('.ppob-pd').find('.nomor_pelanggan').val(),

			data = {action:'get_cekTagihan', 'id_produk':idproduk, 'nomorPelanggan': nomorPelanggan.replace(/\s/g, '') };

		

		// cek Nomor

		if ( isNaN( nomorPelanggan )) { 

			alertSwiftWrong('Maaf Nomor Salah');

			return false;

		}else if ( nomorPelanggan == ''){

			alertSwiftWrong('Maaf Nomor Harus Diisi');

			return false;

		}



		//BlockUI

		$('.ppob-pd').block({ 

                message: '<h1>Waiting...</h1>', 

        }); 



		$.ajax({

			url:lima.ajaxurl,

			data: data,

			type: 'POST',

			dataType: 'JSON',

			success: function(response){
				
				// console.log(response.hsl);

				if (response.msg == 'success') {

					setTimeout($.unblockUI(), 5000);
					
					$('body').find('.ppob-pd').html(response.hsl);

				}else {

					alertSwiftWrong(response.hsl);

					$('.ppob-pd').unblock();

					return false;


				}


			},

			error: function(xhr,status, error){

				$('.ppob-pd').unblock();

			}

		});		

	});

}



function bayarTagihan(){

	var $ = jQuery;

	$('body').on('click','.bayarTagihan', function(){

		let produkId = $(this).closest('.infoTagihan').find('#_produk').val(),

			harga = $(this).closest('.infoTagihan').find('#_harga').val(),

			idPleanggan = $(this).closest('.infoTagihan').find('.idpelanggan').text(),

			tagihanTable = $(this).closest('.infoTagihan').find('.tagihan').text(),

			data = {

				action:'get_bayarTagihan', 

				'produkId':produkId,

				'harga':tagihanTable,

				'idPleanggan':idPleanggan

			};

			//BlockUI

		$('.ppob-pd').block({ 

			message: '<h1>Waiting...</h1>', 

		}); 


		$.ajax({

			url:lima.ajaxurl,

			data:data,

			type:'POST',

			dataType:'JSON',

			success:function(response){

				if (response.msg == 'success') {
					
					// console.log(response);
					window.location.href = response.url;

				}else{

					$('.ppob-pd').unblock();
					return false;
				}

			},

			error:function(xhr,status,error){

				//alert("Error Lima :" + xhr.status);

			}

		});

	});

}



function alertSwiftSuccess(){

	Swal.fire(

		'',

		'Mohon Tunggu...',

		'sukses'

	);

}



function alertSwiftWrong($text){

	Swal.fire({

	  icon: 'error',

	  title: 'Oops...',

	  text: $text

	});

}