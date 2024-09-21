<?php  

/*

* Plugin Name: Lima Produk Digital Paybiz

* Description: Produk Digital Pulsa,Paket Data, PPOB by Lima multimedia in website, full support contact Lima Multimedia. shortcode page [lima-produk-digital]

* Plugin URI: https://limamultimedia.com

* Author: Team Dev Lima Multimedia

* Author URI: https://limamultimedia.com 

* License: GPL

* Version: 1.0

*/



define('LIMA_VER_PD', rand());

define('LIMA_DIR_PD', plugin_dir_path( __FILE__ ));

define('LIMA_TEMP', plugin_dir_path( __FILE__ ).'/templates/');

define('TB_PRODUK_DIGITAL','lima_produk_digital');



include 'functions.php';


$file = ['functions.shortcode','functions.admin','function.sql'];

foreach ($file as $key => $files) {

	include 'includes/'.$files.'.php';

}



$plugin = plugin_basename( __FILE__ );



add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link' );

function plugin_add_settings_link( $links ) {

	$url = 'https://limamultimedia.com';

    $settings_link = '<a href="'.$url.'" style="color:Green;font-weight:bold;">' . __( 'Premium' ) . '</a>';

    array_push( $links, $settings_link );

    return $links;

}



add_action("after_plugin_row_{$plugin}", function( $plugin_file, $plugin_data, $status ) {

  echo '<tr class="active"><td>&nbsp;</td><td colspan="2">

            <p style="background: #fff8e5;padding: 5px 10px;border-radius: 5px;border: 1px solid #ccc;">'.__('<strong style="color: #0080bf;">Gunakan Shortcode Berikut ini :</strong> [lima-produk-digital]', 'lima').'</p>

        </td></tr>';

}, 10, 3 );



add_action( 'wp_enqueue_scripts', 'wpdocs_plugin_scripts' );

function wpdocs_plugin_scripts() {



	wp_enqueue_script('jquery');



	wp_enqueue_script('uiBlock','https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.js',array(),LIMA_VER_PD,true);



    wp_enqueue_style('style-css',plugins_url('css/style.css',__FILE__), array(), LIMA_VER_PD,'all');

    wp_enqueue_script( 'script-a1', plugins_url('js/lima-script.js',__FILE__), array(), LIMA_VER_PD, true );



    wp_enqueue_script('swift-alert','https://cdn.jsdelivr.net/npm/sweetalert2@9',array(),LIMA_VER_PD,true);



    wp_localize_script( 'script-a1', 'lima',

        array( 

            'ajaxurl' => admin_url( 'admin-ajax.php' ),

        )

    );

}



function admin_enquelima(){

    wp_enqueue_script('jquery');



    if ($_GET['page'] == 'seting-produk-digital' ) {



        wp_enqueue_style('selec2-lima','https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css', array(),LIMA_VER_PD,'all');



        wp_enqueue_script('js-select2','https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js',array(),LIMA_VER_PD,true);



        wp_enqueue_script( 'script-admin-lima', plugins_url('js/admin-scriptPD.js',__FILE__), array(), LIMA_VER_PD, true );



        wp_localize_script( 'script-admin-lima', 'lima',

            array( 

                'ajaxurl' => admin_url( 'admin-ajax.php' ),

            )

        );

        wp_enqueue_script('swift-alert','https://cdn.jsdelivr.net/npm/sweetalert2@9',array(),LIMA_VER_PD,true);

        

        wp_enqueue_style('style-css',plugins_url('css/style.css',__FILE__), array(), LIMA_VER_PD,'all');

    }

}

add_action('admin_enqueue_scripts','admin_enquelima');


/**
 * HOOK Create TB wordpress 
 * 
 * @since Pengemmbangan Plugin Produk Digital IRS
 * @version 2.0
 **/ 
function lima_create_plugin_database_table()
{
    global $table_prefix, $wpdb;

    $lima_produk_digital = $table_prefix.'lima_produk_digital';
    $charset_collate = $wpdb->get_charset_collate();

    if($wpdb->get_var( "show tables like '$lima_produk_digital'" ) != $lima_produk_digital){

        $sql = "CREATE TABLE $lima_produk_digital (
                  id int(11) NOT NULL AUTO_INCREMENT,
                  datecreated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                  order_id int(11) NOT NULL,
                  product_key varchar(250) NOT NULL,
                  cust_id varchar(250) NOT NULL,
                  ref_id varchar(250) NOT NULL,
                  PRIMARY KEY (id)
                ) $charset_collate;";
       
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    
    }
}
register_activation_hook( __FILE__, 'lima_create_plugin_database_table' );


?>