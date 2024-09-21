<?php 

/**

* Fungsi All Custom in Admin

* Author: Mas Ikhsan Ms 

*/



//1. First Register the Tab by hooking into the 'woocommerce_product_data_tabs' filter

add_filter( 'woocommerce_product_data_tabs', 'produk_digital_data_tab' );

function produk_digital_data_tab( $product_data_tabs ) {

    $product_data_tabs['lima-pd'] = array(

        'label' => __( 'Produk Digital', 'lima' ),

        'target' => 'pd_product_data',

        'class'     => array( 'show_if_simple','show_if_variable' ),

    );

    return $product_data_tabs;

}



/** CSS To Add Custom tab Icon */

function pd_custom_style() { ?>

	<style>

	#woocommerce-product-data ul.wc-tabs li.lima-pd_options a:before { font-family: Dashicons; content: "\f310"; }

	</style>

<?php 

}

add_action( 'admin_head', 'pd_custom_style' );



//2. functions you can call to output text boxes, select boxes, etc.

add_action('woocommerce_product_data_panels', 'woocom_pd_product_data_fields');

function woocom_pd_product_data_fields() {

  global $post;

  // Note the 'id' attribute needs to match the 'target' parameter set above ?> 

  <div id='pd_product_data' class = 'panel woocommerce_options_panel' > <?php ?> 

    <div class = 'options_group'> 

  <?php



     // Select

	  woocommerce_wp_select(

	    array(

	      'id' => 'lima_select_pd',

	      'label' => __( 'Aktifkan Produk Digital', 'woocommerce' ),

	      'options' => array(

	         'no' => __( 'No', 'woocommerce' ),

	         'yes' => __( 'Yes', 'woocommerce' ),

	      ),

	      'description' => __('Aktifkan sebagai Produk Digital, select choice ini di gunakan ketika memilih produk sebagai produk digital tanpa pasca bayar, jika ingin sebagi pasca bayar silahkan pilih yes pada setingan pasca bayar dibawah','lima'),

	      'desc_tip' => true

	    )

	  );



    // Select

    woocommerce_wp_select(

      array(

        'id' => 'lima_select_pasca_bayar',

        'label' => __( 'Set Pasca Bayar', 'woocommerce' ),

        'options' => array(

           'no' => __( 'No', 'woocommerce' ),

           'yes' => __( 'Yes', 'woocommerce' ),

        ),

        'description' => __('Aktifkan sebagai Pasca Bayar perlu diingat Wajib men set juga sebagai Produk Digital di setingan atas, select choice ini di gunakan ketika memilih produk sebagai pasca bayar','lima'),

        'desc_tip' => true

      )

    );

    woocommerce_wp_select(

      array(

        'id' => 'lima_select_product_type',

        'label' => __( 'Product Type', 'woocommerce' ),

        'options' => array(

          prepaid() => __( 'Prepaid', 'woocommerce' ),

          postpaid() => __( 'Postpaid', 'woocommerce' ),

        ),

        'description' => __('Setting Product type perlu diingat Wajib men set juga sebagai Produk Digital di setingan atas','lima'),

        'desc_tip' => true

      )

    );


  ?> 

    </div>

  </div>

<?php

}

/** 3. Hook callback function to save custom fields information */

function woocom_save_proddata_pd_fields($post_id) {

    // Save Number Field

    $number_field = $_POST['lima_select_pd'];

    $pasca_bayar = $_POST['lima_select_pasca_bayar'];

    $product_type = $_POST['lima_select_product_type'];

    if (isset($number_field,$pasca_bayar,$product_type)) {

        update_post_meta($post_id, 'lima_select_pd', esc_attr( $number_field ) );
      
        update_post_meta($post_id, 'lima_select_pasca_bayar', esc_attr( $pasca_bayar ) );
        
        update_post_meta($post_id, 'lima_select_product_type', esc_attr( $product_type ) );

    }

}

add_action( 'woocommerce_process_product_meta_simple', 'woocom_save_proddata_pd_fields'  );

// You can uncomment the following line if you wish to use those fields for "Variable Product Type"

add_action( 'woocommerce_process_product_meta_variable', 'woocom_save_proddata_pd_fields'  );



// Setingan PD

function getSelectCategoryPdAdmin(){



  $terms = get_terms( array(

      'taxonomy' => 'product_cat',

      'hide_empty' => false,

      'orderby' => 'id'

  ));



  //get dari get_option yang ada di option menu

  $getSetTab = get_option( 'setingan_pd_tab' );

  $opt = '';

  foreach ($terms as $key => $term) {

    $selected = (in_array($term->term_id,$getSetTab)) ? 'selected' : '' ;

    $opt .= '<option value="'.$term->term_id.'"'. $selected .'>'.$term->name.'</option>';

  }



  return $opt;

}



add_action('admin_menu', 'register_seting_pd_submenu_page');

function register_seting_pd_submenu_page() {

    add_submenu_page( 'woocommerce', 'Produk Digital', 'Produk Digital', 'manage_options', 'seting-produk-digital', 'sub_menu_produk_callback' ); 

}



function sub_menu_produk_callback() {

    include LIMA_TEMP.'submenu_produk_digital.php';

}



add_action('wp_ajax_getSaveOptionPd','getSaveOptionPd');

function getSaveOptionPd(){

  

  $termId_array = $_POST['termId'];

  $apiUrl   = $_POST['apiUrl'];

  // $id_irs   = $_POST['id_irs'];

  $username_irs = $_POST['username_irs'];

  $password_irs = $_POST['password_irs'];

  // $pin_irs = $_POST['pin_irs'];

  $markup_ppob = $_POST['markup_ppob'];

  

  $data = [

    'apiUrl' => $apiUrl,

    // 'id_irs' => $id_irs,

    'username_irs' => $username_irs,

    'password_irs' => $password_irs,

    // 'pin_irs' => $pin_irs,

    'markup_ppob' => $markup_ppob

  ];

      $update_opt = update_option( 'setingan_pd_tab', $termId_array );
      
      $update_set_api = update_option( 'setingan_irs', $data );

      $return = ['msg' => 'success'];

      echo json_encode($return);


  die();

}



?>