<?php 

/**

** Basic Function

** Author : Mas Ikhsan

** Developer : Lima Multimedia

**/
add_action('init','initApiLima');
function initApiLima()
{
	if( !isset( $_GET['apitest'] ) ) return;
	
	$url = 'https://paybizapi.paydia.co.id/payment';
	$request = 'POST';
	$data = json_encode(array(
		"ref_id"  => '14487hsdhsh26146',
		"produk_key" => "pascabayar_halo",
		"produk_type" => postpaid(),
		"type" => "dc",
		"cust_id" => "0811359960",
	));

	$cek_status = ApiRequestCekStatus( '14487okecoba123' );
	
	$payment = ApiRequestPayment( '14287etyw6146', 'xldata_xtra10', prepaid(), '085939406100' );

	$inquiry_status = ApiRequestInquiryDigitalContent( 'pascabayar_halo','0811359960' );

	$curl_decode = json_decode( $cek_status );
	
	$json = '{
		"status": "success",
		"trx_date": "2021-05-28 11:26:31",
		"ref_id": "aa22334",
		"invoice_no": "210528000003",
		"point": 982536,
		"cust_id": 85939406000,
		"produk_key": "xl_5000",
		"produk_name": "5.000",
		"trx_status": "trx_status.failed",
		"total": "50100",
		"detail": []
	  }';
	$curl_array = (array) json_decode($json); // convert object to array
	
	unset($curl_array['detail']);

	$note = '';
	foreach ($curl_array as $key => $value) {
		$note .= $key.' = '.$value.' | ';
	}
	

	// $order = wc_get_order( '3387' );
	/*
	* payment
	 stdClass Object (
		[status] => success
		[trx_date] => 2021-05-28 11:26:31
		[ref_id] => aa22334
		[invoice_no] => 210528000003
		[point] => 982536
		[cust_id] => 085939406000
		[produk_key] => xl_5000
		[produk_name] => 5.000
		[trx_status] => trx_status.failed
		[total] => 50100
		[detail] => stdClass Object( ) 
	)
	
	lima_produk_digital
	id, datetime, order_id,product_key,cust_id,ref_id
	
	*/
	$order_id = '3409';


	echo '<pre>';
	print_r( json_decode( $payment ) );
	echo '</pre>';
	die();
}

/**
 * fungsi cek ketika beli produk
 * cek terlebih dahulu tbl lima_produk_digital
 * 
 * @return Bolean
 */
function cekTableProdukDigital($cust_id, $date_format, $produk_key ){
	// get TB PRODUK DIGITAL
	$andWherePD 	.= "AND cust_id='".$cust_id."' ";
	$andWherePD		.= "AND DATE(datecreated) = DATE('".$date_format."') ";
	$andWherePD		.= "AND product_key='".$produk_key."' ";

	$cek_tb_produk_digital = getRowDB(TB_PRODUK_DIGITAL,$andWherePD);

	if( $cek_tb_produk_digital ){
		return true;
	}else{
		return false;
	}
}

/**
 * 
 * CEK TRANSAKSI NOMINAL SAMA DALAM TGL SAMA, NO SAMA
 * Ketika Admin rubah dari complate -> hold -> complate
 */
function cekTransaksiNominalSama($order_id){
	
	//get LOG ORDER
	$get_log_proses = get_post_meta( $order_id, 'log_order_hris', true );

	$unserialize_log = unserialize( $get_log_proses );
	$data_log_array = $unserialize_log[0];

	$cust_id = replaceSpace( $data_log_array['cust_id'] );
	$date_format = limaDateFormat( $data_log_array['trx_date'] );
	$produk_key = $data_log_array['produk_key'];
	$ref_id = $data_log_array['ref_id'];

	// get TB PRODUK DIGITAL
	$andWherePD 	= " AND order_id='".$order_id."' ";
	$andWherePD 	.= "AND cust_id='".$cust_id."' ";
	$andWherePD		.= "AND DATE(datecreated) = DATE('".$date_format."') ";
	$andWherePD		.= "AND product_key='".$produk_key."' ";
	$andWherePD		.= "AND ref_id='".$ref_id."' ";

	$cek_tb_produk_digital = getRowDB(TB_PRODUK_DIGITAL,$andWherePD);

	if( $cek_tb_produk_digital ){
		return true;
	}else{
		return false;
	}
}

/**
 * product_type Schema Api
 */
function prepaid(){
	return '1';
}

function postpaid(){
	return '2';
}

function transfer(){
	return '3';
}

/**
 * type Schema Api
 */
function typeDigitalContent(){
	return 'dc';
}

function typeTransferBank(){
	return 'transfer';
}

/**
 * Auth Berear Token
 */
function authProdukDigital(){
	$setting = get_option('setingan_irs');
	$auth = base64_encode( replaceSpace($setting['username_irs']).':'.replaceSpace($setting['password_irs']) );

	return $auth;
}

/**
 * get url setingan
 */
function urlApi(){
	$setting = get_option('setingan_irs');
	$url = $setting['apiUrl'];

	return $url;
}

/**
 * Replace Spasi
 */
function replaceSpace($string){
	return str_replace(' ', ' ', $string);
}



//Requerst API
function curl_request($url,$request,$data=""){

	$header[] = "Accept: application/json";

	$header[] = "Content-type: application/json";

	$header[] = 'Authorization: Bearer '.authProdukDigital();

    // persiapkan curl

    $ch = curl_init(); 

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

    curl_setopt($ch,CURLOPT_CUSTOMREQUEST,$request);

	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

    $output = curl_exec($ch); 

   

    curl_close($ch);    
	
	
    return $output;
}

/**
 * REQUEST API PAYMENT
 * Api endpoind Payment
 * @return json
 */
function ApiRequestPayment( $ref_order_id, $produk_key,$produk_type,$cust_id ){
	
	$endpoint = 'payment';
	$url = urlApi().'/'.$endpoint;
	
	$request = 'POST';

	$data = json_encode(array(
		"ref_id"  => $ref_order_id,
		"produk_key" => $produk_key,
		"produk_type" => $produk_type,
		"type" => typeDigitalContent(),
		"cust_id" => $cust_id,
	));

	$curl = curl_request( $url, $request, $data );

	return $curl;
}

/**
 * REQUEST API CEK STATUS TRANSAKSI
 * Api endpoind check-status
 */
function ApiRequestCekStatus( $ref_order_id ){
	$endpoint = 'check-status';
	$url = urlApi().'/'.$endpoint;
	
	$request = 'POST';

	$data = json_encode(array(
		"ref_id"  => $ref_order_id,
	));

	$curl = curl_request( $url, $request, $data );

	return $curl;
}

/**
 * REQUEST API IQUIRY DIGITAL CONTENT
 * Api endpoind check-status
 */
function ApiRequestInquiryDigitalContent( $produk_key,$cust_id ){
	$endpoint = 'inquiry';
	$url = urlApi().'/'.$endpoint;
	
	$request = 'POST';

	$data = json_encode( array(
		"produk_key"  => $produk_key,
		"cust_id"  => $cust_id,
	));

	$curl = curl_request( $url, $request, $data );

	return $curl;
}


/**
 * Register Init Rest API
 */
add_action( 'rest_api_init', function () {

	register_rest_route( 'limadigital/v1', '/callback', array(

		'methods' => 'GET',

		'callback' => 'call_back_api_hirs',

	) );

} );



function call_back_api_hirs(WP_REST_Request $request){



	if($_GET){



		$opsi = get_option('irs_log');

		if(!$opsi){

			$opsi_values = serialize($_GET);

			add_option('irs_log',$opsi_values);

		}else{

			$opsi_arr = array();

			$opsi_arr[] = unserialize($opsi);

			$opsi_arr[] = $_GET;



			update_option('irs_log',serialize($opsi_arr));

		}	

	}



	$callback = doing_irs_callback();

	return $callback;

}





function doing_irs_callback(){



	if (!isset($_GET['serverid']) || !isset($_GET['clientid']) || !isset($_GET['statuscode'])) { 



		$response 		= array('success' => false,'msg' => 'error parameter');



	    return $response;



	    die();

	}



	$serverid 		= $_GET['serverid'];

    $clientid 		= $_GET['clientid'];

    $statuscode		= $_GET['statuscode'];

    $kp 			= $_GET['kp'];

    $msisdn 		= $_GET['msisdn'];

    $sn 			= $_GET['sn'];

    $msg 			= $_GET['msg'];



    $order = wc_get_order( $clientid );



    if (!$order || !$order->get_id()) { 



    	$response = array('success' => false,'msg' => 'error order not found');



	    return $response;

    }

    

    $nomor_session_cart = '';

    foreach ($order->get_items() as $key => $item) {

    	$nomor_session_cart = $item->get_meta('Nomor');

    	

    	$product = wc_get_product($item->get_product_id());

    }



    // cek nomorTelfon Tujuan

    if (!$nomor_session_cart ) { 

    	$response = array('success' => false,'msg' => 'error msisdn not found');



	    return $response;

    }

     

    $data = [

    	'serverid' => $serverid,

    	'clientid' => $clientid,

    	'statuscode' => $statuscode,

    	'kp' => $kp,

    	'msisdn' => $msisdn,

    	'sn' => $sn,

    	'msg' => $msg

    ];



    $data['datetime'] = dateIndo();

    

    /*GET LOG META*/   

    $get_opt = get_post_meta($clientid,'log_order_hris',true);

    $un_opt = unserialize($get_opt);



    /*MASUKAN ARRAY IN ARRAY*/

    $un_opt[] = $data; 

    $serial_again = serialize($un_opt);

    

    /*UPDATE LOG SERIALIZE*/

    $opt_save = update_post_meta($clientid,'log_order_hris', $serial_again );



	$notes = '';

	foreach ($_GET as $i => $values) {

		$notes .= $i.' = '.$values.' | ';

	}



    if ($statuscode == '1') {

		// add Note Kusus ADMIN

		$order->add_order_note( $notes, 0 );



	    // masukan sn ke order note

		$note = __('Pembelian Sukses dengan SN: '.$sn,'lima');

		$order->add_order_note( $note, 1 );

    }



    if ($statuscode !== '1') {



    	// add Note Kusus ADMIN

		$order->add_order_note( $notes, 0 );



		sendMailAdmin($clientid,$msg);



	    // masukan sn ke order note

		$note = __('Pembelian '.$kp.' '.$msisdn.' Gagal.Silahkan Hubungi Customer Service.','lima');

		$order->add_order_note( $note, 1 );

    }



    $data['success'] = true;



    return $data;

    

    die();

}





/*Get Tab Category Function*/

function getCategoryPd(){



	$terms = get_terms( array(

	    'taxonomy' => 'product_cat',

	    'hide_empty' => true,

	    'orderby' => 'title'

	));



	//get dari get_option yang ada di option menu

	$array = get_option( 'setingan_pd_tab' );



	$li = '';

	foreach ($terms as $key => $term) {

		if (in_array($term->term_id, $array)) {

			$li .= '<li class="cat-name"><span data-id="'.$term->term_id.'">'.$term->name.'</span></li>';

		}

	}



	return $li;

}




add_filter( 'get_terms_args', 'lima_remove_category_product_digital', 10, 2 );
 function lima_remove_category_product_digital( $args, $taxonomies ) {
    if ( is_admin() && 'product_cat' !== $taxonomies[0] )
        return $args;

    $array = get_option( 'setingan_pd_tab' );

    $catID = $array[0];

    $product_cat = get_term($catID);

	$parent = isset($product_cat->parent) && $product_cat->parent ? $product_cat->parent : 0;

	/*$exclude = $array;
	if($parent){
		$exclude[] = $parent;
	}*/


    $args['exclude'] = array($parent); //hide category Pulsa & PPOB 

    return $args;
}



function dateIndo(){

	ini_set('date.timezone', 'Asia/Jakarta');

	$timezone = time() + (60 * 60 * 7);

	return gmdate('Y-m-d H:i:s', $timezone);

}


/**
 * Hook Woocoome Order Status Change
 * 
 * Fungsi curl request ke server
 * @param Int order_id
 * @param String old_status
 * @param String new_status
 * 
 */
add_action('woocommerce_order_status_changed', 'woo_order_status_change_custom', 10, 3);

function woo_order_status_change_custom($order_id,$old_status,$new_status) {
	$has_submit = get_post_meta($order_id,'log_order_hris',true);
	if($has_submit) return;

	$order = new WC_Order( $order_id );

	$orderstatus = $order->status;

	$sku_product_key = '';

    $no_tujuan = '';

    $idProduct = '';

    $sku_pasca = ''; 

    $no_pelanggan = '';


	if ($orderstatus == 'completed' || $orderstatus == 'processing') {


		foreach ($order->get_items() as $key => $item) {

	    	$no_tujuan .= $item->get_meta('Nomor');

	    	// $no_pelanggan .= $item->get_meta('Nomor Pelanggan');


	    	$idProduct .= $item->get_product_id();

	    	$product = wc_get_product($item->get_product_id());

	    	$sku_pasca .= $product->get_sku();     	

	    	$variation_id = $item->get_variation_id();

		    $sku_product_key .= get_post_meta($variation_id,'_sku',true);

		    break; //only allow 1 product item

	    }

		$cek_pd = get_post_meta($idProduct,'lima_select_pd',true);
		
		if ($cek_pd !== 'yes') { return; } //if Not product digital return


		$cek_pasca_bayar = get_post_meta($idProduct,'lima_select_pasca_bayar',true);

		$cek_product_type = get_post_meta($idProduct,'lima_select_product_type',true);


		$total = $order->get_total();

		$cek_transaksi_nomer_nominal = cekTransaksiNominalSama( $order_id );

		if( $cek_transaksi_nomer_nominal == false ): // cek transaksi nominal sama 

			//cekk pasca bayar

			if ($cek_pasca_bayar == 'yes') {

				$curl = ApiRequestPayment( $order_id, $sku_pasca, $cek_product_type, $no_tujuan );

				$curl_decode = json_decode($curl); //return object

				$dataPD = [
					'datecreated' =>$curl_decode->trx_date,
					'order_id' =>$order_id,
					'product_key' =>$curl_decode->produk_key,
					'cust_id' =>$curl_decode->cust_id,
					'ref_id' =>$curl_decode->ref_id,
				];

				if( $curl_decode->trx_status != 'trx_status.failed' ){

					//query insert tabel produk digital
					$insert_lima_produk_digital = addDB( TB_PRODUK_DIGITAL, $dataPD );
				
				}
				
				$curl_array = (array) $curl_decode; // convert object to array

				unset($curl_array['detail']);

				$note = '';

				foreach ($curl_array as $key => $value) {

					$note .= $key.' = '.$value.' | ';

				}		


				$arr_curl[] = (array) $curl_decode;

				$serialize_curl = serialize($arr_curl);


				// ADD LOG PROSESS IRS
				$insert_log_proses = update_post_meta($order_id,'log_order_hris',$serialize_curl);

				$order->add_order_note( $note, 0 );


			}else{ //pasca bayar end if

				$curl = ApiRequestPayment( $order_id, $sku_product_key, $cek_product_type, $no_tujuan );

				$curl_decode = json_decode($curl); //return object
				
				$dataPD = [
					'datecreated' =>$curl_decode->trx_date,
					'order_id' =>$order_id,
					'product_key' =>$curl_decode->produk_key,
					'cust_id' =>$curl_decode->cust_id,
					'ref_id' =>$curl_decode->ref_id,
				];

				if( $curl_decode->trx_status != 'trx_status.failed' ){

					//query insert tabel produk digital
					$insert_lima_produk_digital = addDB( TB_PRODUK_DIGITAL, $dataPD );
				
				}
				
				$curl_array = (array) $curl_decode; // convert object to array

				unset($curl_array['detail']);

				$note = '';

				foreach ($curl_array as $key => $value) {

					$note .= $key.' = '.$value.' | ';

				}		

				$datas = array(

					'status' => $curl_decode->status, 

					'trx_date' => $curl_decode->trx_date,

					'ref_id' => $curl_decode->ref_id,

					'invoice_no' => $curl_decode->invoice_no,

					'point' => $curl_decode->point,

					'cust_id' => $curl_decode->cust_id,

					'produk_key' => $curl_decode->produk_key,

					'produk_name' => $curl_decode->produk_name,

					'trx_status' => $curl_decode->trx_status,

					'total' => $curl_decode->total,

					// 'detail' => array('ref' => $curl_decode->detail->ref),

				);

				$arr_curl[] = $datas;

				$serialize_curl = serialize($arr_curl);


				// ADD LOG PROSESS IRS
				$insert_log_proses = update_post_meta($order_id,'log_order_hris',$serialize_curl);


				$order->add_order_note( $note, 0 );



			}

			if( $curl_decode->trx_status == 'trx_status.failed' ){

				$order->update_status('failed', 'order_note'); // order note is optional, if you want to  add a note to order
				
				$customer_note = 'Transaksi Gagal - '.$curl_decode->trx_status.'. Silahkan Hubungi CS';

			}else if($curl_decode->trx_status == 'trx_status.pending'){
				
				update_post_meta( $order_id,'produk_digital_need_check', '1' );

			}else if( $curl_decode->status == 'failed' ){
								
				
				$order->add_order_note( lima_implode_array($curl_array) );
				
				$order->update_status('failed', 'order_note');
				
				$order->add_order_note( 'Transaksi Anda gagal, Silahkan Hubungi CS',  1);

			}else{
				$customer_note = $curl_decode->status;
			}
		
		else:
			
			$customer_note = 'Transaksi Sudah Pernah di selesaikan';


		endif; //end if cek transaksi


		$order->add_order_note( $customer_note,  1);
	

	} //complete order

}



function sendMailAdmin($order_id,$irs_msg){

	$to = get_option('admin_email');

	$subject = 'Transaksi Paybiz Digital Content GAGAL ('.$order_id.')';

	$msg = '<p>'.'Hi Admin,'.'</p>'; 

	$msg .= 'Ada Transaksi Paybiz Digital Content dengan Order ID:'.$order_id.' GAGAL.</p>';

	$msg .= '<p>'.'Berikut Pesan Error Paybiz Digital Content :'.$irs_msg.'</p>';

	$msg .= '<p>'.'Untuk detail Silahkan lihat <a href="'.get_site_url().'/wp-admin/post.php?post='.$order_id.'&action=edit'.'">disini</a></p>';



	$body = $msg;

	$headers = array('Content-Type: text/html; charset=UTF-8');

 

	wp_mail( $to, $subject, $body, $headers );

}



//add_filter( 'woocommerce_cart_item_name', 'cart_variation_description', 20, 3);

function cart_variation_description( $name, $cart_item, $cart_item_key ) {


	// Get the corresponding WC_Product

    $product_item = $cart_item['data'];

    

    if(!$product_item->is_type( 'variation' )){

		return $name;

	}



    $attribute = $product_item->get_variation_attributes();

    $get_att = $attribute['attribute_type'];



    if(!empty($product_item) && $product_item->is_type( 'variation' ) ) {

        // WC 3+ compatibility

        $description = version_compare( WC_VERSION, '3.0', '<' ) ? $get_att : $cart_item['variation']['attribute_type'] ;

        $result = __( 'Type: ', 'woocommerce' ) . $description;

        return $name . '<br>' . $result;

    } else

        return $name;

}

function limaDateFormat($datetime,$format="Y-m-d"){
	
	$date = date_create( $datetime );

	return date_format( $date, $format );
}


add_action( 'woocommerce_thankyou', 'lima_view_order_and_thankyou_page', 20 );
add_action( 'woocommerce_view_order', 'lima_view_order_and_thankyou_page', 20 );

function lima_view_order_and_thankyou_page( $order_id ){
		return;
		 
	echo do_shortcode('[countdown date="2021-06-30"]');
}

/** ================ */
/* FUNGSI TESTING */
/** ================ */
add_action('admin_init','testing');
function testing(){
	if( !isset($_GET['tss']) ) return;

	// $order = wc_get_order('3910');
	// get_post_meta( $order->get_id(),'produk_digital_need_check', true )

		$args = array(
			'post_type' => 'shop_order',
		   	'posts_per_page' => -1,
			'post_status' => 'wc-completed'
		);

		$order_query = new WP_Query($args);
		  
		$orders = $order_query->posts;
		
		foreach( $orders as $order ){
			$cek_need_check = get_post_meta( $order->ID,'produk_digital_need_check', true );
			
			if(!$cek_need_check) continue;

			if( $cek_need_check == '1' ){
				$item_sku = array();
				$_order = wc_get_order( $order->ID ); 

				$no_tujuan ='';
				$idProduct ='';
				$sku_pasca ='';
				$sku_product_key ='';


				foreach ($_order->get_items() as $item) {
					$product = wc_get_product($item->get_product_id());
					
					$no_tujuan .= $item->get_meta('Nomor');

					$idProduct .= $item->get_product_id();

					$sku_pasca .= $product->get_sku();     	

					$variation_id = $item->get_variation_id();

					$sku_product_key .= get_post_meta($variation_id,'_sku',true);

					break; //only allow 1 product item

				}

				$cek_pd = get_post_meta($idProduct,'lima_select_pd',true);
		
				if ($cek_pd !== 'yes') { return; } //if Not product digital return

				$cek_pasca_bayar = get_post_meta($idProduct,'lima_select_pasca_bayar',true);

				$cek_product_type = get_post_meta($idProduct,'lima_select_product_type',true);

				// if( $cek_pasca_bayar == 'yes' ){
				$inquiry_status =  ApiRequestCekStatus( (string) $order->ID );
				// }else{
				// 	$inquiry_status = ApiRequestCekStatus($order->ID );
				// }

				$status_decode = (array) json_decode($inquiry_status);
					
				// unset($status_decode['total']);
				// unset($status_decode['fee']);

				// //implode response multidimensi array
				// $in_text = '';
				// foreach ($status_decode as $key=>$val){
				// 	if(is_array($val)) {
				// 		$in_text .= ($in_text != '' ? '|' : ''). implode("|", $val);;
				// 	} else {
				// 		$in_text .= ($in_text != '' ? '|' : ''). $val;
				// 	}
				// }

				// echo '<pre>';
				// print_r( $status_decode );
				// echo '</pre>';
				
				// if( $status_decode['status'] == 'success' ){
				// 	$customer_note = 'Transaksi - '.$in_text;
				// }else{
				// 	// $order->update_status('failed', 'order_note'); // order note is optional, if you want to  add a note to order
				// 	$customer_note = 'Transaksi - '.$in_text;
				// }
				
				// $_order->add_order_note( $customer_note,  1);

				// delete_post_meta( $_order->ID, 'produk_digital_need_check');

			} //end if produk_digital_need_check

		} //end foreach query args

		
		
		$cek_status_api =  (array) json_decode( ApiRequestCekStatus( '3928' ) );
		$cek_need_checks = get_post_meta( '3928','produk_digital_need_check', true );
		echo '<pre>';
		print_r($cek_need_checks);
		echo '</pre>';
	die();
}

/** ================ */
/*  END TESTING		 */
/** ================ */

/**
 * 
 * cron job
 */

add_action( 'lima_cron_cek_produk_digitals', 'cek_status_order_produk_digital' );
function cek_status_order_produk_digital() {
	$args = array(
		'post_type' => 'shop_order',
		'posts_per_page' => -1,
		'post_status' => 'wc-completed'
	);

	$order_query = new WP_Query($args);
	  
	$orders = $order_query->posts;
	  
	foreach( $orders as $order ){
		$cek_need_check = get_post_meta( $order->ID,'produk_digital_need_check', true );
		
		if( $cek_need_check == '1' ){
			$item_sku = array();
			$_order = wc_get_order( $order->ID ); 

			$no_tujuan ='';
			$idProduct ='';
			$sku_pasca ='';
			$sku_product_key ='';


			foreach ($_order->get_items() as $item) {
				$product = wc_get_product($item->get_product_id());
				
				$no_tujuan .= $item->get_meta('Nomor');

				$idProduct .= $item->get_product_id();

				$sku_pasca .= $product->get_sku();     	

				$variation_id = $item->get_variation_id();

				$sku_product_key .= get_post_meta($variation_id,'_sku',true);


				break; //only allow 1 product item

			}

			$cek_pd = get_post_meta($idProduct,'lima_select_pd',true);
	
			if ($cek_pd !== 'yes') { return; } //if Not product digital return

			$cek_pasca_bayar = get_post_meta($idProduct,'lima_select_pasca_bayar',true);

			$cek_product_type = get_post_meta($idProduct,'lima_select_product_type',true);

			// cek api pay biz status
			$cek_status_api =  ApiRequestCekStatus( (string) $order->ID );

			$status_decode = (array) json_decode($cek_status_api);
				
			$admin_note = lima_implode_array_api_admin($status_decode); //return string

			$custommer_note = lima_implode_array_api_custommer($status_decode);

			if( $status_decode['trx_status'] == 'trx_status.success' ){
								
				delete_post_meta( $order->ID, 'produk_digital_need_check');
	
				$_order->add_order_note( $admin_note);

				$_order->add_order_note( $custommer_note, 1 );

			}else if( $status_decode['trx_status'] == 'trx_status.failed' ){
				
				delete_post_meta( $order->ID, 'produk_digital_need_check');

				$_order->update_status('failed', 'order_note');

				$_order->add_order_note( $admin_note);
				
				$_order->add_order_note( $customer_note );
				
			}else if($status_decode['status'] == 'failed'){

				delete_post_meta( $order->ID, 'produk_digital_need_check');

				$_order->update_status('on-hold', 'order_note');
								
				$_order->add_order_note( $admin_note.' - '.' Untuk Mengulang Transaksi Silahkan Mengubah ke Status Order Ke Completed Kembali' );

				$_order->add_order_note( 'Transaksi Anda sedang dalam gangguan, Silahkan Hubungi Admin Kami dalam 1/24 Jam ',1 );

			} // end if status decode

		} //end if produk_digital_need_check

	} //end foreach query args

}

function lima_implode_array_api_custommer($array){
	
	unset($status_decode['status']);
	unset($status_decode['total']);
	unset($status_decode['point']);
	unset($status_decode['fee']);
	unset($status_decode['detail']['total']);

	//implode response multidimensi array
	$in_text = '';
	foreach ($array as $key=>$val){
		if(is_array($val)) {
			$in_text .= ($in_text != '' ? ' | ' : ''). $key.'='.implode("|", $val);;
		} else {
			$in_text .= ($in_text != '' ? ' | ' : ''). $key.'='.$val;
		}
	}

	return $in_text;
}

function lima_implode_array_api_admin($array){

	//implode response multidimensi array
	$in_text = '';
	foreach ($array as $key=>$val){
		if(is_array($val)) {
			$in_text .= ($in_text != '' ? ' | ' : ''). $key.'='.implode("|", $val);;
		} else {
			$in_text .= ($in_text != '' ? ' | ' : ''). $key.'='.$val;
		}
	}

	return $in_text;
}

function lima_implode_array($array){

	//implode response multidimensi array
	$in_text = '';
	foreach ($array as $key=>$val){
		if(is_array($val)) {
			$in_text .= ($in_text != '' ? ' | ' : ''). $key.'='.implode("|", $val);;
		} else {
			$in_text .= ($in_text != '' ? ' | ' : ''). $key.'='.$val;
		}
	}

	return $in_text;
}
?>