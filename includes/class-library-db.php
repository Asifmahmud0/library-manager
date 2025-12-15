<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;


}
class Library_DB {


    public static function get_table_name()  {
        global $wpdb;
        return $wpdb->prefix . 'library_books';
      }

     public static function create_table() {
        global $wpdb;
        $table_name = self::get_table_name();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (

            id  bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description longtext,
            author varchar(255) DEFAULT '' NOT NULL,
            publication_year int(4),
            status varchar(20) DEFAULT 'available',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        )   $charset_collate;";

               require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

}