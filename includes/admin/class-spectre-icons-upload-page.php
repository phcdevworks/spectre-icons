<?php
/**
 * Admin upload page for the Spectre Icons user icon library.
 *
 * Registers the "My Icons" settings page and handles AJAX upload and delete.
 * Builder-agnostic — no page-builder dependencies.
 *
 * @package SpectreIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin page and AJAX handler for the user icon library.
 */
final class Spectre_Icons_Upload_Page {

	const PAGE_SLUG = 'spectre-icons-my-icons';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_page' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'wp_ajax_spectre_icons_upload_icon', array( __CLASS__, 'handle_upload' ) );
		add_action( 'wp_ajax_spectre_icons_delete_icon', array( __CLASS__, 'handle_delete' ) );
	}

	/**
	 * Register the admin options page.
	 *
	 * @return void
	 */
	public static function register_page() {
		add_options_page(
			__( 'My Icons — Spectre Icons', 'spectre-icons' ),
			__( 'My Icons', 'spectre-icons' ),
			'manage_options',
			self::PAGE_SLUG,
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Enqueue upload page assets.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public static function enqueue_assets( $hook ) {
		if ( 'settings_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'spectre-icons-upload',
			SPECTRE_ICONS_URL . 'assets/css/admin/spectre-icons-upload.css',
			array(),
			SPECTRE_ICONS_VERSION
		);

		wp_enqueue_script(
			'spectre-icons-upload',
			SPECTRE_ICONS_URL . 'assets/js/admin/spectre-icons-upload.js',
			array(),
			SPECTRE_ICONS_VERSION,
			true
		);

		wp_localize_script(
			'spectre-icons-upload',
			'SpectreIconsUpload',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'spectre_icons_upload' ),
				'limit'   => Spectre_Icons_User_Library_Manager::get_limit(),
				'count'   => Spectre_Icons_User_Library_Manager::get_icon_count(),
				'i18n'    => array(
					'uploading'     => __( 'Uploading...', 'spectre-icons' ),
					'deleting'      => __( 'Deleting...', 'spectre-icons' ),
					'uploadError'   => __( 'Upload failed. Please try again.', 'spectre-icons' ),
					'deleteError'   => __( 'Delete failed. Please try again.', 'spectre-icons' ),
					'limitReached'  => __( 'Limit reached', 'spectre-icons' ),
					'svgOnly'       => __( 'Only SVG files are supported.', 'spectre-icons' ),
					'fileTooLarge'  => __( 'File must be smaller than 512 KB.', 'spectre-icons' ),
					'confirmDelete' => __( 'Remove this icon from your library?', 'spectre-icons' ),
				),
			)
		);
	}

	/**
	 * Handle SVG file upload via AJAX.
	 *
	 * @return void
	 */
	public static function handle_upload() {
		check_ajax_referer( 'spectre_icons_upload', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'spectre-icons' ) ), 403 );
		}

		if (
			empty( $_FILES['svg_file'] ) ||
			! isset( $_FILES['svg_file']['error'] ) ||
			UPLOAD_ERR_OK !== (int) $_FILES['svg_file']['error']
		) {
			wp_send_json_error( array( 'message' => __( 'No file received or upload error.', 'spectre-icons' ) ), 400 );
		}

		$file = $_FILES['svg_file'];
		$name = isset( $file['name'] ) ? sanitize_file_name( wp_unslash( $file['name'] ) ) : '';
		$ext  = strtolower( pathinfo( $name, PATHINFO_EXTENSION ) );

		if ( 'svg' !== $ext ) {
			wp_send_json_error( array( 'message' => __( 'Only SVG files are supported.', 'spectre-icons' ) ), 415 );
		}

		$size = isset( $file['size'] ) ? (int) $file['size'] : 0;
		if ( $size > 512 * 1024 ) {
			wp_send_json_error( array( 'message' => __( 'File must be smaller than 512 KB.', 'spectre-icons' ) ), 413 );
		}

		$tmp = isset( $file['tmp_name'] ) ? $file['tmp_name'] : '';
		if ( '' === $tmp || ! is_uploaded_file( $tmp ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid upload.', 'spectre-icons' ) ), 400 );
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$raw = file_get_contents( $tmp );
		if ( false === $raw ) {
			wp_send_json_error( array( 'message' => __( 'Could not read uploaded file.', 'spectre-icons' ) ), 500 );
		}

		$sanitized = Spectre_Icons_SVG_Sanitizer::sanitize( $raw );
		if ( '' === $sanitized ) {
			wp_send_json_error( array( 'message' => __( 'Invalid or unsafe SVG file.', 'spectre-icons' ) ), 422 );
		}

		if ( Spectre_Icons_User_Library_Manager::is_at_limit() ) {
			wp_send_json_error(
				array(
					'message' => sprintf(
						/* translators: %d: icon limit */
						__( 'You have reached the %d icon limit. Upgrade to pro for unlimited icons.', 'spectre-icons' ),
						Spectre_Icons_User_Library_Manager::get_limit()
					),
					'code'    => 'limit_reached',
				),
				422
			);
		}

		$result = Spectre_Icons_User_Library_Manager::add_icon( $sanitized, $name );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ), 500 );
		}

		wp_send_json_success(
			array(
				'slug'  => $result,
				'svg'   => $sanitized,
				'count' => Spectre_Icons_User_Library_Manager::get_icon_count(),
				'limit' => Spectre_Icons_User_Library_Manager::get_limit(),
			)
		);
	}

	/**
	 * Handle icon deletion via AJAX.
	 *
	 * @return void
	 */
	public static function handle_delete() {
		check_ajax_referer( 'spectre_icons_upload', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'spectre-icons' ) ), 403 );
		}

		$slug = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';

		if ( '' === $slug ) {
			wp_send_json_error( array( 'message' => __( 'No icon slug provided.', 'spectre-icons' ) ), 400 );
		}

		$result = Spectre_Icons_User_Library_Manager::delete_icon( $slug );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ), 404 );
		}

		wp_send_json_success(
			array(
				'slug'  => $slug,
				'count' => Spectre_Icons_User_Library_Manager::get_icon_count(),
				'limit' => Spectre_Icons_User_Library_Manager::get_limit(),
			)
		);
	}

	/**
	 * Render the My Icons admin page.
	 *
	 * @return void
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$icons    = Spectre_Icons_User_Library_Manager::get_icons();
		$count    = count( $icons );
		$limit    = Spectre_Icons_User_Library_Manager::get_limit();
		$at_limit = $count >= $limit;

		?>
		<div class="wrap spectre-icons-upload-page">
			<h1><?php esc_html_e( 'My Icons', 'spectre-icons' ); ?></h1>
			<p class="description">
				<?php esc_html_e( 'Upload your own SVG icons to use alongside the bundled Spectre Icons libraries in any supported builder.', 'spectre-icons' ); ?>
			</p>

			<div class="spectre-icons-upload-status">
				<span class="spectre-icons-upload-count"
					data-count="<?php echo esc_attr( $count ); ?>"
					data-limit="<?php echo esc_attr( $limit ); ?>">
					<?php
					printf(
						/* translators: 1: current icon count, 2: maximum icon limit */
						esc_html__( '%1$d / %2$d icons', 'spectre-icons' ),
						(int) $count,
						(int) $limit
					);
					?>
				</span>
				<?php if ( $at_limit ) : ?>
					<span class="spectre-icons-limit-badge">
						<?php esc_html_e( 'Limit reached', 'spectre-icons' ); ?>
					</span>
				<?php endif; ?>
			</div>

			<div class="spectre-icons-drop-zone<?php echo $at_limit ? ' spectre-icons-drop-zone--disabled' : ''; ?>"
				id="spectre-icons-drop-zone"
				<?php echo $at_limit ? 'aria-disabled="true"' : ''; ?>>
				<input type="file" id="spectre-icons-file-input" accept=".svg,image/svg+xml" multiple
					<?php echo $at_limit ? 'disabled' : ''; ?>>
				<label for="spectre-icons-file-input" class="spectre-icons-drop-label">
					<span class="dashicons dashicons-upload" aria-hidden="true"></span>
					<?php if ( $at_limit ) : ?>
						<span><?php esc_html_e( 'Icon limit reached. Remove icons to upload more.', 'spectre-icons' ); ?></span>
					<?php else : ?>
						<span><?php esc_html_e( 'Drop SVG files here or click to browse', 'spectre-icons' ); ?></span>
						<small><?php esc_html_e( 'SVG files only &middot; 512 KB max each', 'spectre-icons' ); ?></small>
					<?php endif; ?>
				</label>
			</div>

			<div class="spectre-icons-upload-messages" id="spectre-icons-messages" aria-live="polite"></div>

			<div class="spectre-icons-grid<?php echo empty( $icons ) ? ' spectre-icons-grid--empty' : ''; ?>"
				id="spectre-icons-grid">
				<?php if ( ! empty( $icons ) ) : ?>
					<?php foreach ( $icons as $slug => $svg ) : ?>
						<?php
						/* translators: %s: icon slug */
						$delete_label = sprintf( __( 'Remove %s', 'spectre-icons' ), $slug );
						?>
						<div class="spectre-icons-tile" data-slug="<?php echo esc_attr( $slug ); ?>">
							<div class="spectre-icons-tile__preview">
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG sanitized on upload via Spectre_Icons_SVG_Sanitizer.
								echo $svg;
								?>
							</div>
							<div class="spectre-icons-tile__label"><?php echo esc_html( $slug ); ?></div>
							<button
								type="button"
								class="spectre-icons-tile__delete"
								data-slug="<?php echo esc_attr( $slug ); ?>"
								aria-label="<?php echo esc_attr( $delete_label ); ?>">
								<span class="dashicons dashicons-trash" aria-hidden="true"></span>
							</button>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<p class="spectre-icons-empty-state">
						<?php esc_html_e( 'No icons uploaded yet. Upload an SVG file to get started.', 'spectre-icons' ); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
