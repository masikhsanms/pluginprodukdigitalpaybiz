<?php
/**
 * SQL WPDB
 * 
 * @since Plugin version 2.0
 */

function showDB( $nama_tabel,$andWhere="" ){
    global $wpdb;

    $table = $wpdb->prefix.$nama_tabel;
    $sql = "SELECT * FROM ". $table . " WHERE 1 ". $andWhere;
    $query = $wpdb->get_results( $sql );

    return $query;
}

function getRowDB($nama_tabel,$andWhere=""){
    global $wpdb;
    
    $table = $wpdb->prefix.$nama_tabel;
    $sql = "SELECT * FROM ".$table." WHERE 1 " .$andWhere;
    $query = $wpdb->get_row( $sql );

    return $query;
}

function addDB($nama_tabel,$data){
    global $wpdb;
    
    $table = $wpdb->prefix.$nama_tabel;
    
    $wpdb->insert($table,$data);
    
    $new_id = $wpdb->insert_id;

    return $new_id;
}

function updateDB($nama_tabel,$data,$where){
    global $wpdb;

    $table = $wpdb->prefix.$nama_tabel;
    $query = $wpdb->update( $table, $data, $where );

    return $query;
}