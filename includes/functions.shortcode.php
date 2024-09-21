<?php 

/**

* Shortcode Produk Digital By Lima Mutimedia

* Author Scipt: Ikhsan Ms

* Date: 31-03-2020

* shortcode: [lima-produk-digital]

*/



/*Shortcode Produk Digital*/

add_shortcode('lima-produk-digital','ShotrcodeProdukDigital');

function ShotrcodeProdukDigital(){

	ob_start();

	include LIMA_TEMP.'shortcodeProdukDigital.html.php';

	return ob_get_clean();

}



/*Get Category ID*/

add_action('wp_ajax_getCatId','getCatId');

add_action('wp_ajax_nopriv_getCatId', 'getCatId');

function getCatId(){



	$idTerm = $_POST['idTerm'];



	if ( !isset( $idTerm ) ) { return; }

	

	$taxonomy = 'product_cat';



	$args = [

		'post_type' => 'product',

		'posts_per_page' => -1,

		'tax_query' => [

			[

				'taxonomy' => $taxonomy,

				'field' => 'term_id',

				'terms' => $idTerm

			]

		]

	];



	$query = new WP_Query( $args );

	$posts = $query->posts;



	$li = '';

	foreach ($posts as $key => $value) {

		$ID = $value->ID;

		$title = $value->post_title;

		$img = wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'medium' );

		$img_url = $img[0];		

		$li .= '<li class="item-content col-md-2 col-xs-6">';

		$li .= '<p data-id="'.$ID.'" >'.$title.'</p>';

		$li .= '<input type="hidden" id="hidterm" value="'.$idTerm.'">';

		$li .= '<img class="img-item" src="'.$img_url.'">';

		$li .= '</li>';

	}



	echo $html = ItemCat( $li );

	

	wp_die();

}



/*Html Item Category*/

function ItemCat($li){

	include LIMA_TEMP.'itemcat.html.php';

}



/*handle Get Product TAb*/

add_action('wp_ajax_getProductTab','getProductTab');

add_action('wp_ajax_nopriv_getProductTab','getProductTab');

function getProductTab(){

	

	$idProduct = $_POST['idProduct'];

	$idTerm = $_POST['idTerm'];



	$cek_produk_digital = get_post_meta($idProduct,'lima_select_pd',true);

	$cek_pasca_bayar = get_post_meta($idProduct,'lima_select_pasca_bayar',true);



	if ($cek_produk_digital == 'yes' AND $cek_pasca_bayar == 'no' ) {

	

	$get_product = wc_get_product( $idProduct );

	$available_variations = $get_product->get_available_variations();

	$name_arr_att = $get_product->get_variation_attributes();

	

	$opt = '';

	foreach ($name_arr_att as $k => $name) {	

		$attribute_label_name = wc_attribute_label($k);

		$opt .= '<option value="0">'.__('Pilih '.$attribute_label_name,'lima').'</option>';



	}



	foreach ($available_variations as $key => $value) {

		$idVariations = $value['variation_id']; 

		$att = $value['attributes'];

		$variation_obj = new WC_Product_Variation( $idVariations );

		$stock_status = $variation_obj->get_stock_status();



		// print_r($stock_status);

		if ($stock_status == 'instock') {



			foreach ($att as $i => $optionLabel) {

				$tax = str_replace("attribute_", "", $i);

				$term_s = get_term_by('slug', $optionLabel, $tax);

				$dropdown_label = $term_s ? $term_s->name : $optionLabel;

				$opt .= '<option value="'.$idVariations.'">'.$dropdown_label.'</option>'; 

			}

		}



	}

	$opt .= '<input type="hidden" value="'.$idProduct.'" class="hidIdProduct">';



	$html = formPulsaReg($opt,$name_arr_att,$idProduct);

	

	}



	//condition Form Pasca Bayar tanpa Variation

	if($cek_produk_digital == 'yes' AND $cek_pasca_bayar == 'yes' ){

		

		$html = formCekPpob($idProduct);

	}



	echo $html;

	

	wp_die();

}



/*Html Form PPOB cek payment*/

function formCekPpob($productid){

	ob_start();

	include LIMA_TEMP.'formCekPpob.html.php';

}

/*Html Form*/

function formPulsaReg($opt="",$name_arr_att="",$idProduct){

	ob_start();

	include LIMA_TEMP.'formPulsaReg.html.php';

}



/*Replace Harga 0*/

add_filter( 'woocommerce_show_variation_price', '__return_true' );

add_action('wp_ajax_getHargaVariation','getHargaVariation');

add_action('wp_ajax_nopriv_getHargaVariation','getHargaVariation');

function getHargaVariation(){

	$idVariation = $_POST['idVariation'];

	$variable_product= new WC_Product_Variation( $idVariation );

	$price = $variable_product->price;

	$price_cons = (!$price) ? 0 : $price ;

	

	$text_harga = '<p class="harga_beli">'.wc_price($price_cons).'</p>';



	echo  $text_harga;

	

	wp_die();

}



/*buton beli redirect checkout*/

add_action('wp_ajax_getBeliPulsa','getBeliPulsa');

add_action('wp_ajax_nopriv_getBeliPulsa','getBeliPulsa');

function getBeliPulsa(){

	$idProduct = $_POST['idProduct'];

	$idVariation = $_POST['idVariation'];

	$nomor = $_POST['nomor'];

	$sku_product_key = get_post_meta($idVariation,'_sku',true);

	//cek table lima_produk_digital
	$cek_tb_lima_pd = cekTableProdukDigital($nomor, limaDateFormat( dateIndo() ), $sku_product_key );

	if( $cek_tb_lima_pd == true ) {
		
		$return = [

			'msg' => 'available',
	
			'url' => 'Maaf Anda Telah melakukan Transaksi dengan nominal yang sama untuk hari ini, silahkan transaksi dengan nominal berbeda'
	
		];
	
		echo json_encode($return);
		die();
	}

	$cart_item_data = array(
		'nomor_produk_digital_val' => $nomor
	);

    // $nomor_tagihan = update_post_meta($idProduct,'_nomor_tagihan',$nomor);

	$url = site_url().'/checkout/';
	
	if( WC()->cart->is_empty() ) {
		
		WC()->cart->add_to_cart( $idProduct, 1, $idVariation, array(), $cart_item_data );
		
	
		
		$return = [
	
			'msg' => 'success',
	
			'url' => $url
	
		];

	}else{
		$found = $current = false;

		if(  get_post_meta($idProduct, 'lima_select_pd', true )  ){
			$current = true;
		}

		foreach ( WC()->cart->get_cart() as $cart_item ){

			$cek_pd = get_post_meta($cart_item['product_id'], 'lima_select_pd', true );
	
			if( $cek_pd ){
				$found = true;
				break; // stop the loop.
			}

		}

		if( $found && $current ){
			
			$return = [

				'msg' => 'failed',
		
				'url' => 'Maaf Anda Belum Menyelesaikan Transaksi,Silahkan Lanjutkan Proses transaksi'
		
			];
		}

	}


	echo json_encode($return);



	wp_die();

}





/**

 * Display engraving text in the cart.

 *

 * @param array $item_data

 * @param array $cart_item

 *

 * @return array

 */

function lima_display_nomor_in_cart( $item_data, $cart_item ) {



	$product_id = $cart_item['product_id'];

	

	$cek_pasca_bayar = get_post_meta($product_id,'lima_select_pasca_bayar',true);

	$cek_produk_digital = get_post_meta($product_id,'lima_select_pd',true);

	

	if ($cek_produk_digital == 'yes' AND $cek_pasca_bayar == 'yes') {

		$item_data[] = array(

				'key'     => __( 'ID PELANGGAN', 'lima' ),

				'value'   => wc_clean( $cart_item['idPelanggan'] ),

				'display' => '',

		);

		return $item_data;

	}



	if ($cek_produk_digital == 'yes') {

		$item_data[] = array(

				'key'     => __( 'Nomor Pelanggan', 'lima' ),

				'value'   => wc_clean( $cart_item['nomor_produk_digital_val'] ),

				'display' => '',

		);

		return $item_data;

	}



}



add_filter( 'woocommerce_get_item_data', 'lima_display_nomor_in_cart', 10, 2 );



//fungsi Add meta in order Nomor Tujuan

function add_nomor_pd_to_order_items( $item, $cart_item_key, $values, $order ) {

	$product_id = $item->get_product_id();

	$cek_pasca_bayar = get_post_meta($product_id,'lima_select_pasca_bayar',true);

	$cek_produk_digital = get_post_meta($product_id,'lima_select_pd',true);

	

	if ($cek_produk_digital == 'yes') {

		$item->add_meta_data( __( 'Nomor', 'lima' ), $values['nomor_produk_digital_val'] );

	}

	if ($cek_produk_digital == 'yes' AND $cek_pasca_bayar == 'yes') {

		$item->add_meta_data( __( 'Tagihan', 'lima' ), $values['tagihan'] );

		$item->add_meta_data( __('Nomor','lima'), $values['idPelanggan']);

	}





} 

add_action( 'woocommerce_checkout_create_order_line_item', 'add_nomor_pd_to_order_items', 10, 4 );  



//function hide product in shop and single

add_action( 'woocommerce_product_query', 'lima_hide_products_category_shop' );

   

function lima_hide_products_category_shop( $q ) {

  			

  		if (!is_admin()) {

		    $tax_query = (array) $q->get( 'tax_query' );

		  

			$getSetTab = get_option( 'setingan_pd_tab' );



		    $tax_query[] = array(

		           'taxonomy' => 'product_cat',

		           'field' => 'id',

		           'terms' => $getSetTab, // Category slug here

		           'operator' => 'NOT IN'

		    );

		  

		    $q->set( 'tax_query', $tax_query );

  		}  		  

}


/**

 * Remove Categories from WooCommerce Product Category Widget

 */

//* Used when the widget is displayed as a dropdown

add_filter( 'woocommerce_product_categories_widget_dropdown_args', 'lima_exclude_wc_widget_categories' );

//* Used when the widget is displayed as a list

add_filter( 'woocommerce_product_categories_widget_args', 'lima_exclude_wc_widget_categories' );

function lima_exclude_wc_widget_categories( $cat_args ) {

	$getSetTab = get_option( 'setingan_pd_tab' );



	$cat_args['exclude'] = $getSetTab; // Insert the product category IDs you wish to exclude

	

	return $cat_args;

}



//hide produk digital when search

add_action( 'pre_get_posts', 'search_filter_get_posts' );

function search_filter_get_posts($query) {

	if( is_admin() ) return;

    if ( is_search() || is_product() ){
	
	$taxquery = (array) $query->get( 'tax_query' );

    $getSetTab = get_option( 'setingan_pd_tab' );
	
    $taxquery = array(

        array(

            'taxonomy' => 'product_cat',

            'field' => 'id',

            'terms' => $getSetTab,

            'operator'=> 'NOT IN'

        )

    );

    $query->set( 'tax_query', $taxquery );

	}

}





add_filter( 'woocommerce_checkout_fields' , 'lima_checkout_produk_digital',999 ); 

function lima_checkout_produk_digital( $fields ) {

   global $woocommerce; 

   $only_digital = '';

   $pasca_bayar = '';

   

   foreach( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {

      

   	  $idProduct = $cart_item['product_id'];

      $cek_produk_digital = get_post_meta($idProduct,'lima_select_pd',true);

      $cek_pasca_bayar = get_post_meta($idProduct,'lima_select_pasca_bayar',true);

   		

      $pasca_bayar .= $cek_pasca_bayar; 

      $only_digital .= $cek_produk_digital; 

      

      break;



   }

	    if( $only_digital == 'yes' || $pasca_bayar == 'yes' ) {	

	       unset($fields['billing']['billing_company']);

	       unset($fields['billing']['billing_address_1']);

	       unset($fields['billing']['billing_address_2']);

	       unset($fields['billing']['billing_city']);

	       unset($fields['billing']['billing_postcode']);

	       unset($fields['billing']['billing_country']);

	       unset($fields['billing']['billing_state']);

	       unset($fields['billing']['billing_last_name']);

		   unset($fields['billing']['agenwebsite_billing_city']);

	       add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
		   
		   add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false');

		   
		}

		
		
		return $fields;
		
}

/**
 * Smbunyikan Shipping ketika produk digital
 */
add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'disable_shipping_calc_on_cart', 99 );

function disable_shipping_calc_on_cart( $show_shipping ) {

	global $woocommerce; 

	$only_digital = '';
 
	$pasca_bayar = '';
 
	
 
	foreach( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {
		
	   $idProduct = $cart_item['product_id'];
 
	   $cek_produk_digital = get_post_meta($idProduct,'lima_select_pd',true);
 
	   $cek_pasca_bayar = get_post_meta($idProduct,'lima_select_pasca_bayar',true);
  
	   $pasca_bayar .= $cek_pasca_bayar; 
 
	   $only_digital .= $cek_produk_digital; 

	   break;
	}

	if( $only_digital == 'yes' || $pasca_bayar == 'yes' ) {	

		add_filter('woocommerce_product_needs_shipping', function(){return false;});

		return false;
	}

	return $show_shipping;

}





add_action('wp', 'lima_single');

function lima_single()

{

    if (is_product() || is_shop()) {

        $ID = get_queried_object_id();

        $cek_produk_digital = get_post_meta($ID,'lima_select_pd',true);

        $getSetTab = get_option( 'setingan_pd_tab' );



        $get_cat = ( isset($_GET['product_cat']) ) ? $_GET['product_cat'] : '';

        if ($cek_produk_digital == 'yes' || in_array($get_cat, $getSetTab) ) {

        	header("HTTP/1.1 301 Moved Permanently");

			header("Location: ".site_url().'/shop/');

			exit();

        }

    }

}



// Fungsi Cek TAGIHAN PASCA BAYAR

add_action('wp_ajax_get_cekTagihan','get_cekTagihan');

add_action('wp_ajax_nopriv_get_cekTagihan','get_cekTagihan');

function get_cekTagihan(){



	$idProduct = $_POST['id_produk'];

	$nomorPelanggan = $_POST['nomorPelanggan'];

	if (!isset($nomorPelanggan)) {  return; }


	// Validasi

	$cek_produk_digital = get_post_meta($idProduct,'lima_select_pd',true);

	$cek_pasca_bayar = get_post_meta($idProduct,'lima_select_pasca_bayar',true);



	if ($cek_produk_digital !== 'yes' AND $cek_pasca_bayar !== 'yes') { return false; }



	$product = wc_get_product( $idProduct );

	$sku = $product->get_sku();


	$curl = ApiRequestInquiryDigitalContent( $sku, $nomorPelanggan );

	$curl_decode = json_decode($curl);

	if ($curl_decode->status == 'success') {

		$return = [

			'msg' => 'success',

			'hsl' => InfoTagihanPPOB(
					$curl_decode->cust_id, 
					$curl_decode->produk_name, 
					$curl_decode->cust_name,
					$curl_decode->total,
					$idProduct
				)

		];

	}else{
		$return = [

			'msg' => 'failed',

			'hsl' => 'Maaf sistem sedang mengalami gangguan. Silahkan hubungi CS.'

		];
	}

	echo json_encode($return);

	die();
}





function get_value_msg_tagihan($msg,$search_key){

	$x = explode('@', $msg);

	foreach($x as $data){



		$string = str_replace(' ', '', $data);



		$get_key = strpos($string,$search_key);

		if($get_key === false){

			continue;

		}else{

			$value = str_replace($search_key, '', $string);



			return $value;

			break;

		}

	}

}





function get_tagihan_from_msg($msg){



	$search_key = 'TOTALBAYAR:';

	$tagihan = get_value_msg_tagihan($msg,$search_key);

	$tagihan = str_replace('.','',$tagihan);

	$tagihan = floatval($tagihan);



	return $tagihan;

}





function get_data_pelanggan_from_msg($msg,$search_key='NAMA:'){



	$key = 'DATA:';

	$data = get_value_msg_tagihan($msg,$key);

	$data = str_replace('{','',$data);

	$data = str_replace('}','',$data);



	$ex = explode(',', $data);

	foreach($ex as $string){

		if(strpos($string,$search_key) === false){

			continue;

		}else{

			$value = str_replace($search_key, '', $string);



			return $value;

			break;

		}

	}

}







function InfoTagihanPPOB($idpelanggan,$produk,$nama,$tagihan,$idProduct){ 

	ob_start();

	include LIMA_TEMP.'infoTagihan.php';

	return ob_get_clean();

}



add_action('wp_ajax_get_bayarTagihan','get_bayarTagihan');

add_action('wp_ajax_nopriv_get_bayarTagihan','get_bayarTagihan');

function get_bayarTagihan(){

	$produkId = $_POST['produkId'];

	$harga = $_POST['harga'];

	$harga_rep = str_replace("Rp","",$harga);

	$harga_rep = str_replace(".","",$harga_rep);

	$idPleanggan = $_POST['idPleanggan'];



	$cart_item_data = [

		'tagihan' => $harga_rep,

		'idPelanggan' => $idPleanggan

	];

	

	WC()->cart->add_to_cart( $produkId, 1, 0, array(), $cart_item_data );


	// $url_cart = wc_get_checkout_url();//site_url().'/checkout';

	$url_cart = site_url().'/checkout/';

	

	$return = [

		'msg' => 'success',

		'url' => $url_cart

	];



	echo json_encode($return);



	die();

}



function get_saldo(){

	$saldo = get_option('saldo_irs');

	return;

}



function updateSaldo($value){

	$query = update_option('saldo_irs',$value);

	return;

}



function set_price_cart_ppob( $cart ) {

   //  Exit function if price is changed at backend

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )

        return;



    foreach ( $cart->get_cart() as $key => $item ) {



    	if(isset($item['tagihan'])){

	    	$tagihan = $item['tagihan'];

	        $item['data']->set_price( $tagihan );

	    }

        

    }

}

add_action( 'woocommerce_before_calculate_totals', 'set_price_cart_ppob', 10, 1 );





//Overide MINIcart

add_filter( 'woocommerce_cart_item_price', 'woocommerce_cart_item_price_filter', 10, 3 );

function woocommerce_cart_item_price_filter( $price, $cart_item, $cart_item_key ) {

	    // your code to calculate $new_price

		$product_id = isset($cart_item['product_id']) ? $cart_item['product_id'] : '';

		$tagihan = isset($cart_item['tagihan']) ? $cart_item['tagihan'] : '';



		if(!$product_id || !$tagihan) return $price;



		// Validasi

		$cek_produk_digital = get_post_meta($product_id,'lima_select_pd',true);

		$cek_pasca_bayar = get_post_meta($product_id,'lima_select_pasca_bayar',true);



		if ($cek_produk_digital == 'yes' AND $cek_pasca_bayar == 'yes') {

			return wc_price($tagihan,4);

		}else{

			return $price;

		}	   	

	   	

	}



?>