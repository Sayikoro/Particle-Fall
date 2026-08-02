<?php
/**
 * Plugin Name: Particle Fall
 * Plugin URI: https://z.ai
 * Description: Falling particle animation on any block by CSS class. File-based presets, i18n (RU/EN), live preview. Compatible with Elementor.
 * Version: 1.3.1
 * Author: Z.ai
 * Text Domain: particle-fall
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'PF_VERSION', '1.3.1' );
define( 'PF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

class Particle_Fall_Plugin {

    /* ═══ i18n ═══ */
    private function get_locale() {
        $saved = get_option( 'pf_locale', '' );
        if ( in_array( $saved, [ 'ru', 'en' ], true ) ) return $saved;
        $wl = get_user_locale();
        return ( strpos( $wl, 'ru_' ) === 0 ) ? 'ru' : 'en';
    }

    private function t() {
        static $strings = null;
        if ( $strings !== null ) return $strings[ $this->get_locale() ] ?? [];
        $strings = [
            'ru' => [
                'description'    => 'Анимация падающих частиц на фон блока по CSS-классу. Совместим с Elementor.',
                'css_class'      => 'CSS-класс',
                'class_hint'     => 'Без точки. Тот же класс — в Elementor → Расширенные → CSS-классы.',
                'presets'        => 'Пресеты частиц',
                'recent'         => 'Недавние',
                'preset_hint'    => 'Выберите пресет — или загрузите своё изображение ниже.',
                'recent_hint'    => 'Последние использованные частицы.',
                'no_presets'     => 'Нет пресетов. Добавьте SVG-файлы в папку particles/.',
                'no_recent'      => 'Нет недавних.',
                'image_color'    => 'Изображение и цвет',
                'media_file'     => 'Медиафайл',
                'upload'         => 'Загрузить',
                'remove'         => 'Удалить',
                'image_hint'     => 'PNG, GIF, WebP или SVG с прозрачным фоном.',
                'color'          => 'Цвет',
                'clear_color'    => 'Сбросить',
                'color_hint'     => 'Окрашивает частицы. Пусто = оригинальный цвет.',
                'animation'      => 'Анимация',
                'count'          => 'Количество',
                'speed'          => 'Скорость',
                'size'           => 'Размер',
                'count_hint'     => 'Частиц на экране (5–500)',
                'speed_hint'     => 'Скорость падения (0.5–15)',
                'size_hint'      => 'Размер частицы (2–150 px)',
                'preview'        => 'Предпросмотр',
                'select_hint'    => 'Выберите пресет или загрузите изображение',
                'save'           => 'Сохранить изменения',
                'how_to_use'     => 'Как использовать',
                'step_1'         => '<strong>Укажите CSS-класс</strong> — введите имя (напр., <code class="bg-gray-200 px-1 rounded text-xs">snow-fall</code>)',
                'step_2'         => '<strong>Выберите пресет</strong> — или загрузите своё изображение (PNG / SVG)',
                'step_3'         => '<strong>Подберите цвет</strong> — окрашивает частицы, пусто = оригинал',
                'step_4'         => '<strong>Настройте анимацию</strong> — количество, скорость, размер',
                'step_5'         => '<strong>В Elementor</strong> — секция → «Расширенные» → «CSS-классы» → тот же класс',
                'step_6'         => '<strong>Сохраните</strong> — нажмите кнопку ниже',
                'media_title'    => 'Выберите изображение или SVG',
                'media_button'   => 'Использовать',
                'particles_lbl'  => 'частиц',
                'custom_label'   => 'Пользовательские',
                'donate_title'   => 'Поддержать проект',
                'donate_sub'     => 'DonationAlerts',
                'donate_russia'  => 'Из России?',
                'donate_russia_hint' => 'DonationAlerts — карты, СБП, телефон',
                'donate_crypto'  => 'Криптовалюта',
                'donate_copy'    => 'Скопировано',
                'donate_close'   => 'Свернуть',
            ],
            'en' => [
                'description'    => 'Falling particle animation on any block by CSS class. Compatible with Elementor.',
                'css_class'      => 'CSS Class',
                'class_hint'     => 'Without dot. Same class in Elementor → Advanced → CSS Classes.',
                'presets'        => 'Particle Presets',
                'recent'         => 'Recent',
                'preset_hint'    => 'Select a preset — or upload your own image below.',
                'recent_hint'    => 'Recently used particles.',
                'no_presets'     => 'No presets. Add SVG files to the particles/ folder.',
                'no_recent'      => 'No recent particles.',
                'image_color'    => 'Image & Color',
                'media_file'     => 'Media File',
                'upload'         => 'Upload',
                'remove'         => 'Remove',
                'image_hint'     => 'PNG, GIF, WebP or SVG with transparent background.',
                'color'          => 'Color',
                'clear_color'    => 'Clear',
                'color_hint'     => 'Tints particles. Empty = original color.',
                'animation'      => 'Animation',
                'count'          => 'Count',
                'speed'          => 'Speed',
                'size'           => 'Size',
                'count_hint'     => 'Particles on screen (5–500)',
                'speed_hint'     => 'Fall speed (0.5–15)',
                'size_hint'      => 'Particle size (2–150 px)',
                'preview'        => 'Preview',
                'select_hint'    => 'Select a preset or upload an image',
                'save'           => 'Save Changes',
                'how_to_use'     => 'How to Use',
                'step_1'         => '<strong>Set a CSS class</strong> — enter a name (e.g., <code class="bg-gray-200 px-1 rounded text-xs">snow-fall</code>)',
                'step_2'         => '<strong>Select a preset</strong> — or upload your own image (PNG / SVG)',
                'step_3'         => '<strong>Pick a color</strong> — tints particles, empty = original',
                'step_4'         => '<strong>Adjust animation</strong> — count, speed, size',
                'step_5'         => '<strong>In Elementor</strong> — section → "Advanced" → "CSS Classes" → same class',
                'step_6'         => '<strong>Save</strong> — click the button below',
                'media_title'    => 'Choose image or SVG',
                'media_button'   => 'Use',
                'particles_lbl'  => 'particles',
                'custom_label'   => 'Custom',
                'donate_title'   => 'Support the Project',
                'donate_sub'     => ' DonationAlerts',
                'donate_russia'  => 'From Russia?',
                'donate_russia_hint' => 'DonationAlerts — cards, SBP, phone',
                'donate_crypto'  => 'Cryptocurrency',
                'donate_copy'    => 'Copied',
                'donate_close'   => 'Collapse',
            ],
        ];
        return $strings[ $this->get_locale() ] ?? [];
    }

    private function tx( $key ) {
        $t = $this->t();
        return $t[ $key ] ?? $key;
    }

    /* ═══ FILE-BASED PRESETS ═══ */
    private function scan_presets() {
        $dir = PF_PLUGIN_DIR . 'particles/';
        if ( ! is_dir( $dir ) ) return [];

        $locale = $this->get_locale();

        // Optional presets.json metadata
        $meta = [];
        $meta_file = $dir . 'presets.json';
        if ( file_exists( $meta_file ) ) {
            $raw = file_get_contents( $meta_file );
            $decoded = json_decode( $raw, true );
            if ( is_array( $decoded ) ) $meta = $decoded;
        }

        $presets = [];
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS ),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ( $it as $file ) {
            if ( ! $file->isFile() ) continue;
            if ( strtolower( $file->getExtension() ) !== 'svg' ) continue;

            $relative  = str_replace( $dir, '', $file->getPathname() );
            $id        = str_replace( '.svg', '', $relative );
            $filename  = $file->getBasename( '.svg' );
            $subpath   = $it->getSubPath();

            // Group from subdirectory or fallback
            $default_group = $subpath ? $this->prettify( $subpath ) : $this->tx( 'custom_label' );
            $default_label = $this->prettify( $filename );
            $default_color = $this->extract_svg_color( $file->getPathname() );

            // Merge with presets.json metadata (supports i18n)
            if ( isset( $meta[ $id ] ) ) {
                $m = $meta[ $id ];
                // label can be string or {ru:.., en:..}
                $label = is_array( $m['label'] ?? null )
                    ? ( $m['label'][ $locale ] ?? $m['label']['en'] ?? $default_label )
                    : ( $m['label'] ?? $default_label );
                // group can be string or {ru:.., en:..}
                $group = is_array( $m['group'] ?? null )
                    ? ( $m['group'][ $locale ] ?? $m['group']['en'] ?? $default_group )
                    : ( $m['group'] ?? $default_group );
                $color = $m['color'] ?? $default_color;
            } else {
                $label = $default_label;
                $group = $default_group;
                $color = $default_color;
            }

            $presets[ $id ] = [
                'label' => $label,
                'group' => $group,
                'file'  => $relative,
                'color' => $color,
            ];
        }
        return $presets;
    }

    private function prettify( $str ) {
        return ucwords( str_replace( [ '-', '_' ], ' ', $str ) );
    }

    private function extract_svg_color( $path ) {
        $c = file_get_contents( $path );
        if ( $c && preg_match( '/fill="([^"#]+|#[0-9a-fA-F]{3,8})"/i', $c, $m ) ) {
            if ( preg_match( '/^#[0-9a-fA-F]{3,8}$/', $m[1] ) ) return $m[1];
        }
        return '#ffffff';
    }

    /* ═══ CONSTRUCTOR ═══ */
    public function __construct() {
        add_action( 'admin_menu',                    [ $this, 'add_admin_menu' ] );
        add_action( 'admin_init',                    [ $this, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts',         [ $this, 'enqueue_admin_assets' ] );
        add_action( 'wp_enqueue_scripts',            [ $this, 'enqueue_frontend_assets' ] );
        add_action( 'wp_ajax_pf_set_locale',         [ $this, 'ajax_set_locale' ] );
        add_filter( 'upload_mimes',                   [ $this, 'allow_svg_mime' ] );
        add_filter( 'wp_check_filetype_and_ext',      [ $this, 'fix_svg_filetype' ], 10, 4 );
        add_filter( 'wp_generate_attachment_metadata', [ $this, 'sanitize_svg_upload' ], 10, 2 );
    }

    /* SVG support */
    public function allow_svg_mime( $m ) { $m['svg']='image/svg+xml'; $m['svgz']='image/svg+xml'; return $m; }
    public function fix_svg_filetype( $d,$f,$n,$m ) { $e=strtolower(pathinfo($n,PATHINFO_EXTENSION)); if($e==='svg'||$e==='svgz'){$d['ext']='svg';$d['type']='image/svg+xml';} return $d; }
    public function sanitize_svg_upload( $meta,$id ) {
        $file=get_attached_file($id);
        if(!$file||!in_array(strtolower(pathinfo($file,PATHINFO_EXTENSION)),['svg','svgz'],true))return $meta;
        $s=file_get_contents($file); if($s===false)return $meta;
        $s=preg_replace('/<script[^>]*>.*?<\/script>/si','',$s);
        $s=preg_replace('/<\?xml.*?\?>/si','',$s);
        $s=preg_replace('/\bon\w+\s*=\s*["\'][^"\']*["\']/si','',$s);
        $s=preg_replace('/javascript:/si','',$s);
        $s=preg_replace('/<\/?(meta|link|base|iframe|object|embed|applet)\b[^>]*>/si','',$s);
        file_put_contents($file,$s); return $meta;
    }

    /* ═══ ADMIN MENU ═══ */
    public function add_admin_menu() {
        add_menu_page( 'Particle Fall','Particle Fall','manage_options','particle-fall',[ $this,'render_admin_page' ],'dashicons-image-filter',80);
    }

    /* ═══ SETTINGS ═══ */
    public function register_settings() {
        register_setting('pf_settings_group','pf_css_class',      ['type'=>'string','sanitize_callback'=>'sanitize_text_field','default'=>'']);
        register_setting('pf_settings_group','pf_particle_image',  ['type'=>'string','sanitize_callback'=>'esc_url_raw','default'=>'']);
        register_setting('pf_settings_group','pf_preset',          ['type'=>'string','sanitize_callback'=>'sanitize_text_field','default'=>'']);
        register_setting('pf_settings_group','pf_particle_color',  ['type'=>'string','sanitize_callback'=>'sanitize_hex_color','default'=>'']);
        register_setting('pf_settings_group','pf_particle_count',  ['type'=>'integer','default'=>50]);
        register_setting('pf_settings_group','pf_particle_speed',  ['type'=>'number','default'=>3]);
        register_setting('pf_settings_group','pf_particle_size',   ['type'=>'integer','default'=>20]);
    }

    /* ═══ AJAX ═══ */
    public function ajax_set_locale() {
        check_ajax_referer( 'pf_nonce', 'nonce' );
        $l = sanitize_text_field( $_POST['locale'] ?? '' );
        if ( in_array( $l, [ 'ru', 'en' ], true ) ) {
            update_option( 'pf_locale', $l );
        }
        wp_send_json_success();
    }

    /* ═══ ENQUEUE ADMIN ═══ */
    public function enqueue_admin_assets( $hook ) {
        if ( $hook !== 'toplevel_page_particle-fall' ) return;
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        // Tailwind CDN — preflight disabled so WP admin styles stay intact
        wp_enqueue_script( 'tailwindcss', 'https://cdn.tailwindcss.com', [], null, false );
        wp_add_inline_script( 'tailwindcss', 'tailwind.config={corePlugins:{preflight:false}};', 'after' );
        wp_enqueue_script( 'pf-admin-js', PF_PLUGIN_URL . 'assets/js/admin.js', [ 'jquery', 'wp-color-picker' ], PF_VERSION, true );
        wp_localize_script( 'pf-admin-js', 'pfPresets', $this->get_localized_presets() );
        wp_localize_script( 'pf-admin-js', 'pfAdmin', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'pf_nonce' ),
            'locale'  => $this->get_locale(),
        ] );
    }

    private function get_localized_presets() {
        $o = [];
        foreach ( $this->scan_presets() as $id => $p ) {
            $o[ $id ] = [
                'url'   => PF_PLUGIN_URL . 'particles/' . $p['file'],
                'color' => $p['color'],
                'label' => $p['label'],
                'group' => $p['group'],
            ];
        }
        return $o;
    }

    /* ═══ ENQUEUE FRONTEND ═══ */
    public function enqueue_frontend_assets() {
        $c = get_option( 'pf_css_class', '' ); if ( empty( $c ) ) return;
        wp_enqueue_script( 'pf-frontend-js', PF_PLUGIN_URL . 'assets/js/particle-fall.js', [], PF_VERSION, true );
        $img = get_option( 'pf_particle_image', '' ); $t = '';
        if ( ! empty( $img ) ) { $pp = wp_check_filetype( wp_parse_url( $img, PHP_URL_PATH ) ); $t = strtolower( $pp['ext'] ?? '' ); }
        wp_localize_script( 'pf-frontend-js', 'pfSettings', [
            'className' => sanitize_text_field( $c ),
            'imageUrl'  => esc_url( $img ), 'imageType' => $t,
            'color'     => sanitize_hex_color( get_option( 'pf_particle_color', '' ) ),
            'count'     => max( 1, intval( get_option( 'pf_particle_count', 50 ) ) ),
            'speed'     => max( 0.1, floatval( get_option( 'pf_particle_speed', 3 ) ) ),
            'size'      => max( 2, intval( get_option( 'pf_particle_size', 20 ) ) ),
        ] );
    }

    private function opt( $k, $d = '' ) { return get_option( $k, $d ); }

    /* ═══ ADMIN PAGE ═══ */
    public function render_admin_page() {
        $locale = $this->get_locale();
        $css_class      = $this->opt( 'pf_css_class' );
        $particle_image = $this->opt( 'pf_particle_image' );
        $preset         = $this->opt( 'pf_preset' );
        $color          = $this->opt( 'pf_particle_color' );
        $count          = $this->opt( 'pf_particle_count', 50 );
        $speed          = $this->opt( 'pf_particle_speed', 3 );
        $size           = $this->opt( 'pf_particle_size', 20 );
        $presets        = $this->scan_presets();
        $t              = $this->t();
        $img_preview    = '';
        if ( ! empty( $particle_image ) ) {
            $img_preview = '<img src="' . esc_url( $particle_image ) . '" alt="Preview" class="block max-w-[120px] w-full h-auto rounded-lg border border-white/10 p-0.5">';
        }
        ?>
        <div class="wrap" id="pf-admin-root" style="background:transparent;">
        <div class="-mx-20 min-h-screen bg-gradient-to-br from-[#020617] via-[#0a0f1e] to-[#020617] relative overflow-hidden">

            <!-- dot grid pattern -->
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image:radial-gradient(rgba(255,255,255,0.8) 1px,transparent 1px);background-size:28px 28px;"></div>

            <!-- ambient glow blobs -->
            <div class="absolute top-[-200px] right-[-100px] w-[500px] h-[500px] rounded-full bg-cyan-500/[0.07] blur-[120px] pointer-events-none"></div>
            <div class="absolute bottom-[-150px] left-[-80px] w-[400px] h-[400px] rounded-full bg-violet-500/[0.06] blur-[100px] pointer-events-none"></div>

            <div class="relative z-10 max-w-[1200px] mx-auto px-5 py-8">

                <!-- ═══ HEADER ═══ -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-400 to-violet-500 flex items-center justify-center shadow-lg shadow-cyan-500/25">
                                <span class="dashicons dashicons-image-filter text-[22px] text-white" style="line-height:40px;"></span>
                            </div>
                            <h1 class="text-[26px] font-bold text-white m-0 tracking-tight">
                                Particle Fall
                                <span class="text-[11px] font-normal bg-white/[0.08] text-slate-400 px-2.5 py-0.5 rounded-full ml-1.5 border border-white/[0.06]"><?php echo esc_html( PF_VERSION ); ?></span>
                            </h1>
                        </div>
                        <p class="text-[13px] text-slate-500 ml-[52px] m-0"><?php echo esc_html( $t['description'] ); ?></p>
                    </div>
                    <!-- Lang switch -->
                    <div class="flex items-center gap-1 bg-white/[0.05] rounded-xl p-1 border border-white/[0.08]">
                        <button type="button" class="pf-lang-btn px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all <?php echo $locale==='ru'?'bg-gradient-to-r from-cyan-500/20 to-violet-500/20 text-white shadow-sm border border-white/10':'text-slate-500 hover:text-slate-300 border border-transparent'; ?>" data-lang="ru">RU</button>
                        <button type="button" class="pf-lang-btn px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all <?php echo $locale==='en'?'bg-gradient-to-r from-cyan-500/20 to-violet-500/20 text-white shadow-sm border border-white/10':'text-slate-500 hover:text-slate-300 border border-transparent'; ?>" data-lang="en">EN</button>
                    </div>
                </div>

                <!-- ═══ DONATION BANNER ═══ -->
                <details class="group bg-gradient-to-r from-rose-500/[0.06] via-amber-500/[0.04] to-violet-500/[0.06] border border-rose-400/[0.12] rounded-2xl overflow-hidden backdrop-blur-sm mb-6">
                    <summary class="flex items-center justify-between px-5 py-3.5 cursor-pointer list-none select-none hover:bg-white/[0.03] transition-colors [&::-webkit-details-marker]:hidden">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-rose-500/10 border border-rose-400/20 flex items-center justify-center">
                                <span class="text-rose-400 text-sm">♥</span>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-white"><?php echo esc_html( $t['donate_title'] ); ?></span>
                                <span class="text-[11px] text-slate-500 ml-2"><?php echo esc_html( $t['donate_sub'] ); ?></span>
                            </div>
                        </div>
                        <span class="dashicons dashicons-arrow-down-alt2 text-slate-500 text-[16px] transition-transform duration-300 group-open:rotate-180"></span>
                    </summary>

                    <div class="border-t border-white/[0.06] px-5 py-4 space-y-4">
                        <!-- DonationAlerts (Russia) -->
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest m-0 mb-2"><?php echo esc_html( $t['donate_russia'] ); ?></p>
                            <a href="https://www.donationalerts.com/r/holapsicon" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/[0.05] border border-white/[0.1] rounded-xl text-sm text-slate-300 hover:bg-white/[0.1] hover:border-white/[0.15] hover:text-white transition-all no-underline">
                                <svg class="w-4 h-4 text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                DonationAlerts
                                <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            </a>
                            <p class="text-[11px] text-slate-600 mt-1.5 m-0"><?php echo esc_html( $t['donate_russia_hint'] ); ?></p>
                        </div>

                       
                    </div>
                </details>

                <form method="post" action="options.php">
                    <?php settings_fields( 'pf_settings_group' ); ?>

                    <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 items-start">

                        <!-- ═══ LEFT ═══ -->
                        <div class="space-y-5">

                            <!-- CSS Class -->
                            <div class="bg-white/[0.03] border border-white/[0.08] rounded-2xl p-5 backdrop-blur-sm">
                                <h2 class="text-sm font-semibold text-white m-0 mb-3 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 shadow-sm shadow-cyan-400/50"></span>
                                    <?php echo esc_html( $t['css_class'] ); ?>
                                </h2>
                                <input type="text" id="pf_css_class" name="pf_css_class" value="<?php echo esc_attr( $css_class ); ?>" class="w-full max-w-sm px-4 py-2 bg-white/[0.05] border border-white/[0.1] rounded-xl text-[15px] font-mono tracking-wider text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-500/50 transition-all" placeholder="my-particles">
                                <p class="text-xs text-slate-500 mt-2 m-0"><?php echo esc_html( $t['class_hint'] ); ?></p>
                            </div>

                            <!-- ═══ Presets / Recent — COLLAPSIBLE ═══ -->
                            <details class="group bg-white/[0.03] border border-white/[0.08] rounded-2xl overflow-hidden backdrop-blur-sm" <?php echo empty( $preset ) ? '' : 'open'; ?>>
                                <summary class="flex items-center justify-between px-5 py-4 cursor-pointer list-none select-none hover:bg-white/[0.02] transition-colors [&::-webkit-details-marker]:hidden">
                                    <h2 class="text-sm font-semibold text-white m-0 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-violet-400 shadow-sm shadow-violet-400/50"></span>
                                        <?php echo esc_html( $t['presets'] ); ?>
                                    </h2>
                                    <span class="dashicons dashicons-arrow-down-alt2 text-slate-500 text-[16px] transition-transform duration-300 group-open:rotate-180"></span>
                                </summary>

                                <div class="border-t border-white/[0.06]">
                                    <!-- Tab bar -->
                                    <div class="flex px-5 pt-3 gap-1">
                                        <button type="button" class="pf-tab-btn active px-4 py-2 text-xs font-semibold rounded-lg border-b-2 border-cyan-400 text-cyan-400 bg-cyan-400/10 -mb-px transition-all" data-tab="presets"><?php echo esc_html( $t['presets'] ); ?></button>
                                        <button type="button" class="pf-tab-btn px-4 py-2 text-xs font-semibold rounded-lg border-b-2 border-transparent text-slate-500 hover:text-slate-300 hover:bg-white/[0.03] -mb-px transition-all" data-tab="recent"><?php echo esc_html( $t['recent'] ); ?></button>
                                    </div>

                                    <input type="hidden" id="pf_preset" name="pf_preset" value="<?php echo esc_attr( $preset ); ?>">

                                    <!-- Presets panel -->
                                    <div class="pf-tab-panel p-5 pt-4" id="pf-panel-presets">
                                        <?php
                                        $groups = [];
                                        foreach ( $presets as $id => $p ) $groups[ $p['group'] ][ $id ] = $p;
                                        if ( empty( $groups ) ) {
                                            echo '<p class="text-sm text-slate-500 m-0">' . esc_html( $t['no_presets'] ) . '</p>';
                                        } else {
                                            foreach ( $groups as $gn => $items ) :
                                                ?>
                                                <h3 class="text-[11px] font-bold text-slate-500 uppercase tracking-widest m-0 mb-2.5 <?php echo $gn === array_key_first( $groups ) ? 'mt-0' : 'mt-6'; ?>"><?php echo esc_html( $gn ); ?></h3>
                                                <div class="flex flex-wrap gap-2.5">
                                                <?php foreach ( $items as $id => $p ) :
                                                    $svg_path = PF_PLUGIN_DIR . 'particles/' . $p['file'];
                                                    $svg_content = file_exists( $svg_path ) ? file_get_contents( $svg_path ) : '';
                                                    $active = ( $preset === $id ) ? ' ring-1 ring-cyan-400 bg-cyan-400/10 shadow-sm shadow-cyan-400/20' : ' hover:border-white/20 hover:bg-white/[0.06]';
                                                    ?>
                                                    <button type="button" class="pf-preset-card flex flex-col items-center gap-1.5 w-[76px] p-2.5 border border-white/[0.08] rounded-xl bg-white/[0.03] cursor-pointer transition-all text-[10px] text-slate-400<?php echo $active; ?>" data-id="<?php echo esc_attr( $id ); ?>">
                                                        <span class="w-9 h-9 flex items-center justify-center overflow-hidden" style="color:<?php echo esc_attr( $p['color'] ); ?>"><?php echo $svg_content; ?></span>
                                                        <span class="font-medium text-center leading-tight line-clamp-2"><?php echo esc_html( $p['label'] ); ?></span>
                                                    </button>
                                                <?php endforeach; ?>
                                                </div>
                                            <?php endforeach;
                                            echo '<p class="text-xs text-slate-600 mt-4 m-0">' . esc_html( $t['preset_hint'] ) . '</p>';
                                        }
                                        ?>
                                    </div>

                                    <!-- Recent panel -->
                                    <div class="pf-tab-panel p-5 pt-4 hidden" id="pf-panel-recent">
                                        <div id="pf-recent-list" class="flex flex-wrap gap-2.5">
                                            <p class="pf-recent-empty text-sm text-slate-500 m-0"><?php echo esc_html( $t['no_recent'] ); ?></p>
                                        </div>
                                        <p class="text-xs text-slate-600 mt-4 m-0"><?php echo esc_html( $t['recent_hint'] ); ?></p>
                                    </div>
                                </div>
                            </details>

                            <!-- Image & Color -->
                            <div class="bg-white/[0.03] border border-white/[0.08] rounded-2xl p-5 backdrop-blur-sm">
                                <h2 class="text-sm font-semibold text-white m-0 mb-3 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-sm shadow-emerald-400/50"></span>
                                    <?php echo esc_html( $t['image_color'] ); ?>
                                </h2>
                                <!-- Upload -->
                                <div class="mb-5">
                                    <label class="block text-xs font-medium text-slate-400 mb-2"><?php echo esc_html( $t['media_file'] ); ?></label>
                                    <input type="hidden" id="pf_particle_image" name="pf_particle_image" value="<?php echo esc_url( $particle_image ); ?>">
                                    <div class="flex items-center gap-3">
                                        <button type="button" class="inline-flex items-center gap-2 px-4 py-2 text-sm border border-white/[0.1] rounded-xl bg-white/[0.04] text-slate-300 hover:bg-white/[0.08] hover:border-white/[0.15] cursor-pointer transition-all" id="pf_upload_btn">
                                            <span class="dashicons dashicons-upload text-[16px] m-0" style="vertical-align:middle;"></span>
                                            <?php echo esc_html( $t['upload'] ); ?>
                                        </button>
                                        <button type="button" class="text-sm text-rose-400 hover:text-rose-300 cursor-pointer transition-colors<?php echo empty( $particle_image ) ? ' hidden' : ''; ?>" id="pf_remove_btn"><?php echo esc_html( $t['remove'] ); ?></button>
                                    </div>
                                    <div id="pf_image_preview" class="mt-3"><?php echo $img_preview; ?></div>
                                    <p class="text-xs text-slate-500 mt-2 m-0"><?php echo esc_html( $t['image_hint'] ); ?></p>
                                </div>
                                <!-- Color -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-400 mb-2" for="pf_particle_color"><?php echo esc_html( $t['color'] ); ?></label>
                                    <div class="flex items-center gap-3">
                                        <input type="text" id="pf_particle_color" name="pf_particle_color" value="<?php echo esc_attr( $color ); ?>" class="pf-color-picker" style="width:56px;height:32px;border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:2px;">
                                        <button type="button" class="text-xs px-3 py-1.5 border border-white/[0.1] rounded-lg bg-white/[0.04] text-slate-400 hover:bg-white/[0.08] hover:text-slate-300 cursor-pointer transition-all<?php echo empty( $color ) ? ' hidden' : ''; ?>" id="pf_color_clear"><?php echo esc_html( $t['clear_color'] ); ?></button>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-2 m-0"><?php echo esc_html( $t['color_hint'] ); ?></p>
                                </div>
                            </div>

                            <!-- Animation -->
                            <div class="bg-white/[0.03] border border-white/[0.08] rounded-2xl p-5 backdrop-blur-sm">
                                <h2 class="text-sm font-semibold text-white m-0 mb-4 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shadow-sm shadow-amber-400/50"></span>
                                    <?php echo esc_html( $t['animation'] ); ?>
                                </h2>
                                <div class="space-y-5">
                                    <?php
                                    $sliders = [
                                        ['id'=>'pf_particle_count','name'=>'pf_particle_count','label'=>$t['count'],'val'=>$count,'min'=>5,'max'=>500,'step'=>5,'suffix'=>'','hint'=>$t['count_hint']],
                                        ['id'=>'pf_particle_speed','name'=>'pf_particle_speed','label'=>$t['speed'],'val'=>$speed,'min'=>0.5,'max'=>15,'step'=>0.5,'suffix'=>'','hint'=>$t['speed_hint']],
                                        ['id'=>'pf_particle_size','name'=>'pf_particle_size','label'=>$t['size'],'val'=>$size,'min'=>2,'max'=>150,'step'=>1,'suffix'=>'<span class="text-[10px] font-normal text-slate-500 ml-0.5">px</span>','hint'=>$t['size_hint']],
                                    ];
                                    foreach ( $sliders as $s ) :
                                    ?>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-400 mb-2" for="<?php echo esc_attr($s['id']); ?>"><?php echo esc_html( $s['label'] ); ?></label>
                                        <div class="flex items-center gap-4">
                                            <input type="range" id="<?php echo esc_attr($s['id']); ?>" name="<?php echo esc_attr($s['name']); ?>" min="<?php echo esc_attr($s['min']); ?>" max="<?php echo esc_attr($s['max']); ?>" step="<?php echo esc_attr($s['step']); ?>" value="<?php echo esc_attr($s['val']); ?>" class="flex-1 max-w-[340px] h-1.5 rounded-full appearance-none bg-white/[0.08] cursor-pointer accent-cyan-400">
                                            <span id="<?php echo esc_attr(str_replace('pf_particle_','pf_',$s['id']).'_val'); ?>" class="inline-block min-w-[52px] text-center text-sm font-bold text-cyan-400 bg-cyan-400/[0.08] border border-cyan-400/20 px-3 py-1.5 rounded-lg tabular-nums"><?php echo esc_html($s['val']); echo $s['suffix']; ?></span>
                                        </div>
                                        <p class="text-xs text-slate-600 mt-1.5 m-0"><?php echo esc_html( $s['hint'] ); ?></p>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- How to Use -->
                            <details class="group bg-white/[0.02] border border-white/[0.06] rounded-2xl overflow-hidden">
                                <summary class="flex items-center justify-between px-5 py-3.5 cursor-pointer list-none select-none hover:bg-white/[0.02] transition-colors [&::-webkit-details-marker]:hidden">
                                    <span class="text-sm font-medium text-slate-400"><?php echo esc_html( $t['how_to_use'] ); ?></span>
                                    <span class="dashicons dashicons-arrow-down-alt2 text-slate-600 text-[16px] transition-transform duration-300 group-open:rotate-180"></span>
                                </summary>
                                <div class="px-5 pb-4 border-t border-white/[0.04]">
                                    <ol class="m-0 pl-5 text-[13px] text-slate-400 leading-relaxed space-y-1.5">
                                        <?php for ( $i = 1; $i <= 6; $i++ ) : ?>
                                            <li class="text-slate-400"><?php echo $t[ 'step_' . $i ]; ?></li>
                                        <?php endfor; ?>
                                    </ol>
                                </div>
                            </details>

                            <!-- Save -->
                            <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-cyan-500 to-violet-500 text-white font-semibold text-sm hover:from-cyan-400 hover:to-violet-400 transition-all shadow-lg shadow-cyan-500/25 hover:shadow-cyan-500/40 hover:shadow-violet-500/20 cursor-pointer border-0">
                                <?php echo esc_html( $t['save'] ); ?>
                            </button>
                        </div>

                        <!-- ═══ RIGHT: Live Preview ═══ -->
                        <div class="lg:sticky lg:top-8 order-first lg:order-last">
                            <!-- Glow border wrapper -->
                            <div class="relative -inset-px rounded-2xl bg-gradient-to-b from-cyan-500/30 via-violet-500/20 to-cyan-500/10 p-px">
                                <div class="rounded-2xl overflow-hidden bg-[#0b0f1a]">
                                    <div class="px-4 py-2.5 text-[11px] font-bold text-slate-500 bg-white/[0.03] border-b border-white/[0.06] uppercase tracking-widest flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse shadow-sm shadow-cyan-400/50"></span>
                                        <?php echo esc_html( $t['preview'] ); ?>
                                    </div>
                                    <div class="w-full h-[420px] bg-gradient-to-br from-[#030712] via-[#0a0f1e] to-[#0f172a] relative overflow-hidden" id="pf_preview_box">
                                        <canvas id="pf_preview_canvas" class="block w-full h-full"></canvas>
                                    </div>
                                    <div class="px-4 py-2 text-[11px] text-slate-600 bg-white/[0.02] border-t border-white/[0.04] flex items-center min-h-[34px] font-mono" id="pf_preview_info"><?php echo esc_html( $t['select_hint'] ); ?></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        </div>
        <?php
    }
}

new Particle_Fall_Plugin();