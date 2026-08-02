/**
 * Particle Fall — Admin JS v1.3
 * Tailwind-based, i18n, tabs (Presets/Recent), recent particles, live preview canvas.
 */
jQuery(function ($) {

    var presets = (typeof pfPresets !== 'undefined') ? pfPresets : {};
    var admin   = (typeof pfAdmin   !== 'undefined') ? pfAdmin   : { ajaxUrl: '', nonce: '', locale: 'en' };

    /* ═══════════════════════════════════════════
       i18n CLIENT STRINGS (for dynamic content)
       ═══════════════════════════════════════════ */
    var i18n = {
        ru: {
            particles_lbl: 'частиц',
            select_hint: 'Выберите пресет или загрузите изображение',
            no_recent: 'Нет недавних.'
        },
        en: {
            particles_lbl: 'particles',
            select_hint: 'Select a preset or upload an image',
            no_recent: 'No recent particles.'
        }
    };
    var tx = i18n[admin.locale] || i18n.en;

    /* ═══════════════════════════════════════════
       LANGUAGE SWITCH
       ═══════════════════════════════════════════ */
    $(document).on('click', '.pf-lang-btn', function () {
        var lang = $(this).data('lang');
        if (!lang) return;
        $.post(admin.ajaxUrl, { action: 'pf_set_locale', nonce: admin.nonce, locale: lang }, function () {
            location.reload();
        });
    });

    /* ═══════════════════════════════════════════
       TAB SWITCHING
       ═══════════════════════════════════════════ */
    $(document).on('click', '.pf-tab-btn', function () {
        var tab = $(this).data('tab');
        // Update button styles
        $('.pf-tab-btn').removeClass('active border-cyan-400 text-cyan-400 bg-cyan-400/10').addClass('border-transparent text-slate-500 hover:text-slate-300 hover:bg-white/[0.03]');
        $(this).addClass('active border-cyan-400 text-cyan-400 bg-cyan-400/10').removeClass('border-transparent text-slate-500 hover:text-slate-300 hover:bg-white/[0.03]');
        // Toggle panels
        $('.pf-tab-panel').addClass('hidden');
        $('#pf-panel-' + tab).removeClass('hidden');
    });

    /* ═══════════════════════════════════════════
       RECENT PARTICLES (localStorage)
       ═══════════════════════════════════════════ */
    var RECENT_KEY = 'pf_recent';
    var MAX_RECENT = 12;

    function getRecent() {
        try { return JSON.parse(localStorage.getItem(RECENT_KEY)) || []; } catch(e) { return []; }
    }

    function saveRecent(list) {
        localStorage.setItem(RECENT_KEY, JSON.stringify(list.slice(0, MAX_RECENT)));
    }

    function addRecent(url, label, color) {
        if (!url) return;
        var list = getRecent();
        // Remove duplicate by url
        list = list.filter(function (r) { return r.url !== url; });
        list.unshift({ url: url, label: label || 'Custom', color: color || '' });
        saveRecent(list);
        renderRecent();
    }

    function removeRecent(url) {
        var list = getRecent().filter(function (r) { return r.url !== url; });
        saveRecent(list);
        renderRecent();
    }

    function renderRecent() {
        var $container = $('#pf-recent-list');
        var list = getRecent();
        $container.empty();

        if (list.length === 0) {
            $container.append('<p class="pf-recent-empty text-sm text-slate-500 m-0">' + tx.no_recent + '</p>');
            return;
        }

        list.forEach(function (item) {
            var card = $(
                '<div class="relative group/pf inline-flex flex-col items-center gap-1.5 w-[76px] p-2.5 border border-white/[0.08] rounded-xl bg-white/[0.03] hover:border-cyan-400/40 hover:bg-white/[0.06] transition-all cursor-pointer">' +
                    '<img src="' + escAttr(item.url) + '" alt="" class="w-9 h-9 object-contain pointer-events-none" onerror="this.style.display=\'none\'">' +
                    '<span class="text-[10px] font-medium text-slate-400 text-center leading-tight line-clamp-2">' + escHtml(item.label) + '</span>' +
                    '<button type="button" class="pf-recent-remove absolute -top-1.5 -right-1.5 w-4 h-4 rounded-full bg-rose-500 text-white text-[10px] leading-none flex items-center justify-center opacity-0 group-hover/pf:opacity-100 transition-opacity" data-url="' + escAttr(item.url) + '">&times;</button>' +
                '</div>'
            );
            // Click on card (not remove button) → select
            card.on('click', function (e) {
                if ($(e.target).hasClass('pf-recent-remove')) return;
                selectRecentItem(item);
            });
            $container.append(card);
        });
    }

    function selectRecentItem(item) {
        $('.pf-preset-card').removeClass('ring-1 ring-cyan-400 bg-cyan-400/10 shadow-sm shadow-cyan-400/20');
        $('#pf_preset').val('');
        $('#pf_particle_image').val(item.url);
        $('#pf_image_preview').html('<img src="' + escAttr(item.url) + '" alt="Preview" class="block max-w-[120px] w-full h-auto rounded-lg border border-white/10 p-0.5">');
        $('#pf_remove_btn').removeClass('hidden');
        if (item.color) {
            $colorInput.wpColorPicker('color', item.color);
            $colorInput.val(item.color);
            $('#pf_color_clear').removeClass('hidden');
        }
        Preview.update();
    }

    function escAttr(s) { return $('<span>').text(s || '').html(); }
    function escHtml(s) { return $('<span>').text(s || '').html(); }

    // Init recent on load
    renderRecent();

    // Remove recent item
    $(document).on('click', '.pf-recent-remove', function (e) {
        e.stopPropagation();
        removeRecent($(this).data('url'));
    });

    /* ═══════════════════════════════════════════
       LIVE PREVIEW ENGINE
       ═══════════════════════════════════════════ */
    var Preview = {
        canvas: null, ctx: null, particles: [], image: null, animId: null,
        w: 0, h: 0, dpr: Math.min(window.devicePixelRatio || 1, 2),
        opts: { count: 50, speed: 3, size: 20, color: '', imageUrl: '' },

        init: function () {
            this.canvas = document.getElementById('pf_preview_canvas');
            if (!this.canvas) return;
            this.ctx = this.canvas.getContext('2d');
            this.sizeCanvas();
            this.readSettings();
            this.loadImage();
            this.populate();
            this.animate();
        },

        readSettings: function () {
            this.opts.count    = parseInt($('#pf_particle_count').val()) || 50;
            this.opts.speed    = parseFloat($('#pf_particle_speed').val()) || 3;
            this.opts.size     = parseInt($('#pf_particle_size').val()) || 20;
            this.opts.color    = $('#pf_particle_color').val() || '';
            this.opts.imageUrl = $('#pf_particle_image').val() || '';
        },

        loadImage: function () {
            var self = this;
            var url  = this.opts.imageUrl;
            this.image = null;
            if (!url) return;

            var isSvg = url.match(/\.svg(\?|$)/i);
            if (isSvg) {
                fetch(url).then(function (r) { return r.text(); }).then(function (svg) {
                    var blob = new Blob([svg], { type: 'image/svg+xml' });
                    var img  = new Image();
                    img.onload  = function () { self.image = img; self.updateInfo(); };
                    img.onerror = function () { self.updateInfo(); };
                    img.src = URL.createObjectURL(blob);
                }).catch(function () { self.updateInfo(); });
            } else {
                var img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload  = function () { self.image = img; self.updateInfo(); };
                img.onerror = function () { self.updateInfo(); };
                img.src = url;
            }
        },

        update: function () {
            var oldUrl = this.opts.imageUrl;
            this.readSettings();
            while (this.particles.length < this.opts.count) this.particles.push(this.createParticle(true));
            while (this.particles.length > this.opts.count) this.particles.pop();
            if (this.opts.imageUrl !== oldUrl) this.loadImage();
            this.updateInfo();
        },

        updateInfo: function () {
            var el = document.getElementById('pf_preview_info');
            if (!el) return;
            var c = this.opts.count, s = this.opts.speed, sz = this.opts.size;
            var img = this.image ? ' ●' : ' ○';
            el.textContent = c + ' ' + tx.particles_lbl + ' · ' + s + ' · ' + sz + 'px' + img;
        },

        createParticle: function (randomY) {
            var v = 0.4 + Math.random() * 1.2;
            var pSize = Math.max(2, this.opts.size * v);
            return {
                x: Math.random() * this.w,
                y: randomY ? Math.random() * this.h : -pSize - Math.random() * 100,
                baseSize: pSize,
                speed: (0.5 + Math.random() * 0.8) * this.opts.speed,
                opacity: 0.35 + Math.random() * 0.55,
                drift: (Math.random() - 0.5) * 1.2,
                wobble: Math.random() * Math.PI * 2,
                wobbleAmp: 0.3 + Math.random() * 0.7,
                wobbleSpd: 0.008 + Math.random() * 0.018,
                rotation: Math.random() * Math.PI * 2,
                rotationSpd: (Math.random() - 0.5) * 0.04
            };
        },

        populate: function () {
            this.particles = [];
            for (var i = 0; i < this.opts.count; i++) this.particles.push(this.createParticle(true));
        },

        sizeCanvas: function () {
            var box = this.canvas.parentElement;
            var r   = box.getBoundingClientRect();
            var nw  = Math.floor(r.width  * this.dpr);
            var nh  = Math.floor(r.height * this.dpr);
            if (this.canvas.width !== nw || this.canvas.height !== nh) {
                this.canvas.width  = nw;
                this.canvas.height = nh;
                this.ctx.setTransform(this.dpr, 0, 0, this.dpr, 0, 0);
            }
            this.w = r.width;
            this.h = r.height;
        },

        hexToRgba: function (hex, a) {
            var r = parseInt(hex.slice(1, 3), 16);
            var g = parseInt(hex.slice(3, 5), 16);
            var b = parseInt(hex.slice(5, 7), 16);
            return 'rgba(' + r + ',' + g + ',' + b + ',' + a + ')';
        },

        drawParticle: function (ctx, p) {
            var half = p.baseSize / 2;
            ctx.save();
            ctx.globalAlpha = p.opacity;
            ctx.translate(p.x, p.y);
            ctx.rotate(p.rotation);
            if (this.image) {
                ctx.drawImage(this.image, -half, -half, p.baseSize, p.baseSize);
                if (this.opts.color) {
                    ctx.globalCompositeOperation = 'source-atop';
                    ctx.fillStyle = this.opts.color;
                    ctx.fillRect(-half, -half, p.baseSize, p.baseSize);
                    ctx.globalCompositeOperation = 'source-over';
                }
            } else {
                var radius = Math.max(1, half);
                ctx.beginPath();
                ctx.arc(0, 0, radius, 0, Math.PI * 2);
                ctx.fillStyle = this.opts.color ? this.hexToRgba(this.opts.color, 0.8) : 'rgba(200,200,200,0.8)';
                ctx.fill();
            }
            ctx.restore();
        },

        animate: function () {
            var self = this;
            (function loop() {
                self.sizeCanvas();
                var ctx = self.ctx, w = self.w, h = self.h;
                ctx.clearRect(0, 0, w, h);
                for (var i = 0; i < self.particles.length; i++) {
                    var p = self.particles[i];
                    p.y += p.speed; p.wobble += p.wobbleSpd;
                    p.x += p.drift + Math.sin(p.wobble) * p.wobbleAmp;
                    p.rotation += p.rotationSpd;
                    if (p.y > h + p.baseSize) { p.y = -p.baseSize - Math.random() * 40; p.x = Math.random() * w; }
                    if (p.x > w + p.baseSize) p.x = -p.baseSize;
                    if (p.x < -p.baseSize) p.x = w + p.baseSize;
                    self.drawParticle(ctx, p);
                }
                self.animId = requestAnimationFrame(loop);
            })();
        }
    };

    /* ═══════════════════════════════════════════
       COLOR PICKER
       ═══════════════════════════════════════════ */
    var $colorInput = $('#pf_particle_color');
    $colorInput.wpColorPicker({
        defaultColor: '',
        change: function () {
            $('#pf_color_clear').removeClass('hidden');
            Preview.update();
        },
        clear: function () {
            $('#pf_color_clear').addClass('hidden');
            Preview.update();
        }
    });

    $('#pf_color_clear').on('click', function () {
        $colorInput.wpColorPicker('color', '');
        $colorInput.val('');
        $(this).addClass('hidden');
        Preview.update();
    });

    /* ═══════════════════════════════════════════
       PRESET SELECTION
       ═══════════════════════════════════════════ */
    $(document).on('click', '.pf-preset-card', function () {
        var id = $(this).data('id'), p = presets[id];
        if (!p) return;
        // Highlight
        $('.pf-preset-card').removeClass('ring-1 ring-cyan-400 bg-cyan-400/10 shadow-sm shadow-cyan-400/20');
        $(this).addClass('ring-1 ring-cyan-400 bg-cyan-400/10 shadow-sm shadow-cyan-400/20');
        // Set values
        $('#pf_particle_image').val(p.url);
        $('#pf_preset').val(id);
        $('#pf_image_preview').html('<img src="' + escAttr(p.url) + '" alt="' + escAttr(p.label) + '" class="block max-w-[120px] w-full h-auto rounded-lg border border-white/10 p-0.5">');
        $('#pf_remove_btn').removeClass('hidden');
        if (p.color) {
            $colorInput.wpColorPicker('color', p.color);
            $colorInput.val(p.color);
            $('#pf_color_clear').removeClass('hidden');
        }
        // Add to recent
        addRecent(p.url, p.label, p.color);
        Preview.update();
    });

    /* ═══════════════════════════════════════════
       MEDIA UPLOADER
       ═══════════════════════════════════════════ */
    var frame;
    $('#pf_upload_btn').on('click', function (e) {
        e.preventDefault();
        if (frame) { frame.open(); return; }
        frame = wp.media({ title: (admin.locale === 'ru') ? 'Выберите изображение или SVG' : 'Choose image or SVG', button: { text: (admin.locale === 'ru') ? 'Использовать' : 'Use' }, multiple: false });
        frame.on('select', function () {
            var a   = frame.state().get('selection').first().toJSON();
            var url = a.url;
            if (a.type === 'image' && a.subtype !== 'svg+xml' && a.sizes && a.sizes.full) url = a.sizes.full.url;
            var label = a.title || a.filename || 'Custom';
            // Deselect presets
            $('.pf-preset-card').removeClass('ring-1 ring-cyan-400 bg-cyan-400/10 shadow-sm shadow-cyan-400/20');
            $('#pf_preset').val('');
            $('#pf_particle_image').val(url);
            $('#pf_image_preview').html('<img src="' + escAttr(url) + '" alt="Preview" class="block max-w-[120px] w-full h-auto rounded-lg border border-white/10 p-0.5">');
            $('#pf_remove_btn').removeClass('hidden');
            // Add to recent
            addRecent(url, label, '');
            Preview.update();
        });
        frame.open();
    });

    $('#pf_remove_btn').on('click', function (e) {
        e.preventDefault();
        $('#pf_particle_image').val(''); $('#pf_preset').val('');
        $('.pf-preset-card').removeClass('ring-1 ring-cyan-400 bg-cyan-400/10 shadow-sm shadow-cyan-400/20');
        $('#pf_image_preview').empty();
        $(this).addClass('hidden');
        Preview.update();
    });

    /* ═══════════════════════════════════════════
       CRYPTO COPY
       ═══════════════════════════════════════════ */
    var copiedMsg = (admin.locale === 'ru') ? 'Скопировано!' : 'Copied!';
    $(document).on('click', '.pf-copy-crypto', function () {
        var addr = $(this).data('address');
        if (!addr) return;
        var $btn = $(this);
        navigator.clipboard.writeText(addr).then(function () {
            var orig = $btn.text();
            $btn.text(copiedMsg).addClass('!text-emerald-400 !border-emerald-400/30');
            setTimeout(function () { $btn.text(orig).removeClass('!text-emerald-400 !border-emerald-400/30'); }, 1500);
        });
    });

    /* ═══════════════════════════════════════════
       RANGE SLIDERS → live update preview
       ═══════════════════════════════════════════ */
    $('#pf_particle_count').on('input', function () {
        $('#pf_count_val').text($(this).val());
        Preview.update();
    });
    $('#pf_particle_speed').on('input', function () {
        $('#pf_speed_val').text($(this).val());
        Preview.update();
    });
    $('#pf_particle_size').on('input', function () {
        $('#pf_size_val').html($(this).val() + '<span class="text-[10px] font-normal text-slate-500 ml-0.5">px</span>');
        Preview.update();
    });

    /* Class input — strip leading dot */
    $('#pf_css_class').on('blur', function () { $(this).val($(this).val().replace(/^\./, '').trim()); });

    /* ═══════════════════════════════════════════
       BOOT PREVIEW
       ═══════════════════════════════════════════ */
    $(Preview.init.bind(Preview));

});
