<?php

class Library_Admin {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );


    }

    public function add_menu_page() {
        add_menu_page(
            'Library Manager',
            'Library Manager',
            'edit_posts',
            'library-manager',
            [ $this, 'render_admin_page' ],
            'dashicons-book',
            25
        );
    }

    public function render_admin_page() {
        echo '<div id="library-manager-app"><h2>Loading,........</h2></div>';
    }



    public function enqueue_admin_scripts( $hook ) {
        if ( 'toplevel_page_library-manager' !== $hook ) {
            return;
        }




        // Auto-generated asset file from wp-scripts
        $asset_file = include( LIBRARY_MANAGER_PATH . 'build/index.asset.php' );

        wp_enqueue_script(
            'library-manager-app',
            LIBRARY_MANAGER_URL . 'build/index.js',
            $asset_file['dependencies'],
            $asset_file['version'],
            true
        );



        // Pass settings to JS
        wp_localize_script( 'library-manager-app', 'wpApiSettings', [
            'root'  => esc_url_raw( rest_url( 'library/v1/' ) ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
        ] );
        


        //  CSS for layout
        wp_add_inline_style( 'wp-admin', '
            .library-table { width: 100%; 
            border-collapse: collapse; 
            background: #fff;  
            margin-top: 20px; }

            .library-table th, .library-table td { 
            text-align: left;
             padding: 10px; 
             border-bottom: 1px solid #ddd;
              }
            .btn { 
            cursor: pointer;
             padding: 5px 10px; 
             margin-right: 5px; 
             }
            .btn-danger { 
            color: red;
             }

            .form-container {
             background: #fff;
              padding: 20px; 
              max-width: 500px;
               margin-top: 20px; 
               border:  1px solid #ccc;
                }
            .form-group {
             margin-bottom: 15px; 
             }
            .form-group label {
             display: block; 
             margin-bottom: 5px; 
             font-weight: bold; 
             }
            .form-group input, .form-group textarea, .form-group select { 
            width: 100%; 
            padding: 8px; }


            /* Status Colors */
            .status-available {
            color: #007cba;
            background: #e3f2fd; 
            padding: 4px 8px; 
            border-radius: 4px;
             font-weight: bold;
              }
            .status-borrowed { 
            color: #d63638;
             background: #fcebeb; 
             padding: 4px 8px;
              border-radius: 4px; 
              font-weight: bold;
               }
            .status-unavailable { 
            color: #666;
             background: #f0f0f1; 
             padding: 4px 8px; 
             border-radius: 4px;
             font-weight: bold; 
             }
        ' );
    }
}