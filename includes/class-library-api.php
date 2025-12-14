<?php

class Library_API {
    private $table_name;



    public function __construct() {
        $this->table_name = Library_DB::get_table_name();
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
             }


       public function register_routes() {
        register_rest_route( 'library/v1', '/books', [
            [

                'methods'  => 'GET',
                'callback' => [ $this, 'get_books' ],
                'permission_callback' => '__return_true', 
            ],
            [
                'methods'  => 'POST',
                'callback' => [ $this, 'create_book' ],
                'permission_callback' => [ $this, 'permissions_check' ],
            ],
        ]);





        register_rest_route( 'library/v1', '/books/(?P<id>\d+)', [
            [
                'methods'  => 'GET',
                'callback' => [ $this, 'get_book' ],
                'permission_callback' => '__return_true',
            ],
            [
                'methods'  => 'PUT',
                'callback' => [ $this, 'update_book' ],
                'permission_callback' => [ $this, 'permissions_check' ],
            ],
            [
                'methods'  => 'DELETE',
                'callback' => [ $this, 'delete_book' ],
                'permission_callback' => [ $this, 'permissions_check' ],
            ],
        ]);
        
    }

         public function permissions_check() {
                return current_user_can( 'edit_posts' );
    }

    public function get_books( $request ) {
        global $wpdb;
        
        $results = $wpdb->get_results( "SELECT * FROM {$this->table_name} ORDER BY created_at DESC" );
        return rest_ensure_response( $results );
    }

    public function get_book( $request ) {
        global $wpdb;
        $id = $request['id'];
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE id = %d", $id ) );

        if ( ! $row ) {
            return new WP_Error( 'no_book', 'Book not found', [ 'status' => 404 ] );
        }
        return rest_ensure_response( $row );
    }

            public function create_book( $request ) {
        global $wpdb;
        
        $params = $request->get_json_params();
        
       
             if ( empty( $params['title'] ) ) {
            return new WP_Error( 'missing_title', 'Title is required', [ 'status' => 400 ] );
        }

                $data = [
            'title' => sanitize_text_field( $params['title'] ),
            'description' => sanitize_textarea_field( $params['description'] ?? '' ),
                'author' => sanitize_text_field( $params['author'] ?? '' ),
            'publication_year' => intval( $params['publication_year'] ),
            'status' => sanitize_text_field( $params['status'] ?? 'available' ),
        ];

                $format = [ '%s', '%s', '%s', '%d', '%s' ];

        $wpdb->insert( $this->table_name, $data, $format );
        
        $new_id = $wpdb->insert_id;
        $new_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE id = %d", $new_id ) );

             return rest_ensure_response( $new_item );
    }

    public function update_book( $request ) {
         global $wpdb;
        $id = $request['id'];
        $params = $request->get_json_params();


        $data = [];
        $format = [];

        
        if ( isset( $params['title'] ) ) {
            $data['title'] = sanitize_text_field( $params['title'] );
            $format[] = '%s';
        }
        if ( isset( $params['description'] ) ) {
            $data['description'] = sanitize_textarea_field( $params['description'] );
            $format[] = '%s';
        }
        if ( isset( $params['author'] ) ) {
            $data['author'] = sanitize_text_field( $params['author'] );
            $format[] = '%s';
        }
        if ( isset( $params['publication_year'] ) ) {
            $data['publication_year'] = intval( $params['publication_year'] );
            $format[] = '%d';
        }
        if ( isset( $params['status'] ) ) {
            $data['status'] = sanitize_text_field( $params['status'] );
            $format[] = '%s';
        }


        if ( empty( $data ) ) {
            return new WP_Error( 'no_data', 'No data to update', [ 'status' => 400 ] );
        }


        $wpdb->update( $this->table_name, $data, [ 'id' => $id ], $format, [ '%d' ] );

        return rest_ensure_response( $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE id = %d", $id ) ) );
    }

    
            public function delete_book( $request ) {
        global $wpdb;
        $id = $request['id'];
        
        $deleted = $wpdb->delete( $this->table_name, [ 'id' => $id ], [ '%d' ] );
        
        if ( ! $deleted ) {
            return new WP_Error( 'cant_delete', 'Could not delete book', [ 'status' => 500 ] );
        }

        return new WP_REST_Response( null, 204 );
    }
}