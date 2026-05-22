<?php
/**
 * Plugin Name: ThumbAPI — Auto Featured Image on Publish
 * Plugin URI: https://thumbapi.dev
 * Description: Automatically generates and assigns a featured image for new posts via the ThumbAPI thumbnail generation REST API.
 * Version: 1.0.0
 * Author: Aldin Kozica
 * Author URI: https://thumbapi.dev
 * License: MIT
 * Text Domain: thumbapi
 *
 * @package ThumbAPI
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ---------------------------------------------------------------------------
// Settings page
// ---------------------------------------------------------------------------

add_action( 'admin_menu', function () {
    add_options_page(
        'ThumbAPI',
        'ThumbAPI',
        'manage_options',
        'thumbapi-settings',
        'thumbapi_render_settings_page'
    );
} );

add_action( 'admin_init', function () {
    register_setting( 'thumbapi', 'thumbapi_api_key' );
    register_setting( 'thumbapi', 'thumbapi_format' );
    register_setting( 'thumbapi', 'thumbapi_image_style' );
} );

function thumbapi_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>ThumbAPI Settings</h1>
        <p>
            Configure how this plugin generates featured images for new posts.
            Get an API key at
            <a href="https://app.thumbapi.dev" target="_blank" rel="noopener">app.thumbapi.dev</a>.
        </p>
        <form action="options.php" method="post">
            <?php settings_fields( 'thumbapi' ); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="thumbapi_api_key">API key</label></th>
                    <td>
                        <input name="thumbapi_api_key" id="thumbapi_api_key" type="password"
                               value="<?php echo esc_attr( get_option( 'thumbapi_api_key', '' ) ); ?>"
                               class="regular-text" />
                        <p class="description">Header <code>x-api-key</code> value sent with each request.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="thumbapi_format">Format</label></th>
                    <td>
                        <select name="thumbapi_format" id="thumbapi_format">
                            <?php
                            $current_format = get_option( 'thumbapi_format', 'blogpost' );
                            foreach ( array( 'blogpost', 'youtube', 'instagram', 'x', 'linkedin' ) as $opt ) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    esc_attr( $opt ),
                                    selected( $current_format, $opt, false ),
                                    esc_html( $opt )
                                );
                            }
                            ?>
                        </select>
                        <p class="description"><code>blogpost</code> (1200×630) is recommended for WordPress featured / OG images.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="thumbapi_image_style">Image style</label></th>
                    <td>
                        <select name="thumbapi_image_style" id="thumbapi_image_style">
                            <?php
                            $current_style = get_option( 'thumbapi_image_style', 'faceless' );
                            foreach ( array( 'faceless', 'with-image', 'with-logo' ) as $opt ) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    esc_attr( $opt ),
                                    selected( $current_style, $opt, false ),
                                    esc_html( $opt )
                                );
                            }
                            ?>
                        </select>
                        <p class="description">For <code>with-logo</code>, upload your logo once in the ThumbAPI dashboard under Assets — it's used automatically.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// ---------------------------------------------------------------------------
// Generate featured image on publish
// ---------------------------------------------------------------------------

add_action( 'transition_post_status', 'thumbapi_on_publish', 10, 3 );

function thumbapi_on_publish( $new_status, $old_status, $post ) {
    if ( 'publish' !== $new_status || 'publish' === $old_status ) {
        return; // Only fire on the transition INTO publish, not on every save.
    }

    if ( 'post' !== $post->post_type ) {
        return; // Adjust here if you want this to fire for pages or custom post types.
    }

    if ( has_post_thumbnail( $post->ID ) ) {
        return; // Don't overwrite an existing featured image.
    }

    $api_key = get_option( 'thumbapi_api_key', '' );
    if ( empty( $api_key ) ) {
        error_log( 'ThumbAPI: API key not configured. Skipping post ' . $post->ID );
        return;
    }

    $format = get_option( 'thumbapi_format', 'blogpost' );
    $style  = get_option( 'thumbapi_image_style', 'faceless' );

    $response = wp_remote_post(
        'https://api.thumbapi.dev/v1/generate',
        array(
            'timeout' => 60,
            'headers' => array(
                'x-api-key'    => $api_key,
                'Content-Type' => 'application/json',
            ),
            'body'    => wp_json_encode( array(
                'title'        => $post->post_title,
                'format'       => $format,
                'imageStyle'   => $style,
                'outputFormat' => 'webp',
            ) ),
        )
    );

    if ( is_wp_error( $response ) ) {
        error_log( 'ThumbAPI request failed: ' . $response->get_error_message() );
        return;
    }

    $code = wp_remote_retrieve_response_code( $response );
    if ( 200 !== $code ) {
        error_log( 'ThumbAPI HTTP ' . $code . ': ' . wp_remote_retrieve_body( $response ) );
        return;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( empty( $body['image'] ) ) {
        error_log( 'ThumbAPI returned no image for post ' . $post->ID );
        return;
    }

    // Strip the `data:image/webp;base64,` prefix if present, then decode.
    $data_uri = $body['image'];
    $base64   = strpos( $data_uri, ',' ) !== false
        ? substr( $data_uri, strpos( $data_uri, ',' ) + 1 )
        : $data_uri;
    $binary   = base64_decode( $base64 );
    if ( false === $binary ) {
        error_log( 'ThumbAPI: failed to base64-decode response for post ' . $post->ID );
        return;
    }

    $attachment_id = thumbapi_save_attachment( $binary, $post );
    if ( ! $attachment_id ) {
        return;
    }

    set_post_thumbnail( $post->ID, $attachment_id );
}

/**
 * Save a binary image buffer as a WordPress attachment, attached to the given post.
 */
function thumbapi_save_attachment( $binary, $post ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $upload_dir = wp_upload_dir();
    $filename   = sanitize_title( $post->post_title ) . '-' . $post->ID . '.webp';
    $file_path  = trailingslashit( $upload_dir['path'] ) . $filename;

    if ( false === file_put_contents( $file_path, $binary ) ) {
        error_log( 'ThumbAPI: could not write file ' . $file_path );
        return 0;
    }

    $attachment = array(
        'post_mime_type' => 'image/webp',
        'post_title'     => $post->post_title,
        'post_content'   => '',
        'post_status'    => 'inherit',
        'guid'           => trailingslashit( $upload_dir['url'] ) . $filename,
    );

    $attachment_id = wp_insert_attachment( $attachment, $file_path, $post->ID );
    if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
        error_log( 'ThumbAPI: wp_insert_attachment failed for post ' . $post->ID );
        return 0;
    }

    $metadata = wp_generate_attachment_metadata( $attachment_id, $file_path );
    wp_update_attachment_metadata( $attachment_id, $metadata );

    return $attachment_id;
}
