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

    // 1. Get parameters
    $page     = isset( $request['page'] ) ? intval( $request['page'] ) : 1;
    $per_page = isset( $request['per_page'] ) ? intval( $request['per_page'] ) : 10;
    $search   = isset( $request['search'] ) ? sanitize_text_field( $request['search'] ) : '';

    // 2. Calculate Offset
    $offset = ( $page - 1 ) * $per_page;

    // 3. Build Query
    $query = "SELECT * FROM {$this->table_name}";
    $count_query = "SELECT COUNT(*) FROM {$this->table_name}";
    $args = [];

    // 4. Add Search Logic
    if ( ! empty( $search ) ) {
        $search_term = '%' . $wpdb->esc_like( $search ) . '%';
        $where_clause = " WHERE title LIKE %s OR author LIKE %s";
        
        $query .= $where_clause;
        $count_query .= $where_clause;
        
        array_push( $args, $search_term, $search_term );
    }

    // 5. Add Pagination
    $query .= " ORDER BY created_at DESC LIMIT %d OFFSET %d";
    array_push( $args, $per_page, $offset );

    // 6. Execute
    if ( ! empty( $args ) ) {
        $results = $wpdb->get_results( $wpdb->prepare( $query, $args ) );
        
        // Remove limit/offset args for the count query
        // (We only need the search terms for counting)
        $count_args = array_slice( $args, 0, count( $args ) - 2 ); 
        $total_items = !empty($count_args) 
            ? $wpdb->get_var( $wpdb->prepare( $count_query, $count_args ) )
            : $wpdb->get_var( $count_query );
    } else {
        $results = $wpdb->get_results( $query ); // Fallback (shouldn't happen with defaults)
        $total_items = $wpdb->get_var( $count_query );
    }

    // 7. Return Data + Headers for Pagination
    $response = new WP_REST_Response( $results );
    $response->header( 'X-WP-Total', $total_items );
    $response->header( 'X-WP-TotalPages', ceil( $total_items / $per_page ) );

    return $response;
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