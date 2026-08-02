/**
 * Particle Fall v1.1 — Frontend Particle System
 * Supports PNG, GIF, WebP, SVG. Color tinting via source-atop.
 */
(function () {
    'use strict';

    if (typeof pfSettings === 'undefined' || !pfSettings.className) return;

    var CLASS    = pfSettings.className;
    var COUNT    = pfSettings.count    || 50;
    var SPEED    = pfSettings.speed    || 3;
    var SIZE     = pfSettings.size     || 20;
    var IMG_URL  = pfSettings.imageUrl  || '';
    var IMG_TYPE = (pfSettings.imageType || '').toLowerCase();
    var TINT     = pfSettings.color     || '';   // hex, e.g. '#ff0000'
    var IS_SVG   = (IMG_TYPE === 'svg' || IMG_URL.match(/\.svg(\?|$)/i));

    /* Parse hex to rgba string for canvas */
    function hexToRgba(hex, alpha) {
        var r = parseInt(hex.slice(1, 3), 16);
        var g = parseInt(hex.slice(3, 5), 16);
        var b = parseInt(hex.slice(5, 7), 16);
        return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
    }

    /* ─── Preload image ─── */
    var particleImg = null;

    function loadImageFromUrl(src) {
        return new Promise(function (resolve) {
            if (!src) { resolve(null); return; }
            var img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload  = function () { resolve(img); };
            img.onerror = function () { resolve(null); };
            img.src = src;
        });
    }

    function loadSvgFromUrl(src) {
        return fetch(src)
            .then(function (res) {
                if (!res.ok) throw new Error('SVG fetch failed');
                return res.text();
            })
            .then(function (svgText) {
                var blob = new Blob([svgText], { type: 'image/svg+xml;charset=utf-8' });
                var blobUrl = URL.createObjectURL(blob);
                return new Promise(function (resolve) {
                    var img = new Image();
                    img.onload  = function () { resolve(img); };
                    img.onerror = function () { resolve(null); };
                    img.src = blobUrl;
                });
            })
            .catch(function () { return null; });
    }

    /* ─── Helpers ─── */
    function debounce(fn, ms) {
        var timer;
        return function () { clearTimeout(timer); timer = setTimeout(fn, ms); };
    }

    var instances = [];

    /* ─── Create a single particle ─── */
    function createParticle(w, h, randomY) {
        var sizeVar = 0.4 + Math.random() * 1.2;
        var pSize = Math.max(2, SIZE * sizeVar);
        return {
            x:          Math.random() * w,
            y:          randomY ? Math.random() * h : -pSize - Math.random() * 100,
            baseSize:   pSize,
            speed:      (0.5 + Math.random() * 0.8) * SPEED,
            opacity:    0.35 + Math.random() * 0.55,
            drift:      (Math.random() - 0.5) * 1.2,
            wobble:     Math.random() * Math.PI * 2,
            wobbleAmp:  0.3 + Math.random() * 0.7,
            wobbleSpd:  0.008 + Math.random() * 0.018,
            rotation:   Math.random() * Math.PI * 2,
            rotationSpd:(Math.random() - 0.5) * 0.04
        };
    }

    /* ─── Attach canvas ─── */
    function attachCanvas(el) {
        var canvas = document.createElement('canvas');
        canvas.className = 'pf-canvas';
        canvas.style.cssText =
            'position:absolute;top:0;left:0;width:100%;height:100%;' +
            'pointer-events:none;z-index:0;';

        var elStyle = window.getComputedStyle(el);
        if (elStyle.position === 'static') el.style.position = 'relative';
        el.style.overflow = 'hidden';

        el.insertBefore(canvas, el.firstChild);

        var children = el.children;
        for (var i = 0; i < children.length; i++) {
            if (children[i] !== canvas) {
                if (!children[i].style.position || children[i].style.position === 'static') {
                    children[i].style.position = 'relative';
                }
                if (!children[i].style.zIndex) {
                    children[i].style.zIndex = '1';
                }
            }
        }

        var ctx = canvas.getContext('2d');
        var dpr = Math.min(window.devicePixelRatio || 1, 2);
        var particles = [];

        var instance = { el: el, canvas: canvas, ctx: ctx, dpr: dpr, particles: particles, animId: null, w: 0, h: 0 };

        sizeCanvas(instance);

        for (var j = 0; j < COUNT; j++) {
            particles.push(createParticle(instance.w, instance.h, true));
        }

        animate(instance);
        instances.push(instance);
    }

    /* ─── Size canvas ─── */
    function sizeCanvas(inst) {
        var rect = inst.el.getBoundingClientRect();
        var newW = Math.floor(rect.width  * inst.dpr);
        var newH = Math.floor(rect.height * inst.dpr);
        if (inst.canvas.width !== newW || inst.canvas.height !== newH) {
            inst.canvas.width  = newW;
            inst.canvas.height = newH;
            inst.ctx.setTransform(inst.dpr, 0, 0, inst.dpr, 0, 0);
        }
        inst.w = rect.width;
        inst.h = rect.height;
    }

    /* ─── Draw one particle ─── */
    function drawParticle(ctx, p) {
        var half = p.baseSize / 2;

        ctx.save();
        ctx.globalAlpha = p.opacity;
        ctx.translate(p.x, p.y);
        ctx.rotate(p.rotation);

        if (particleImg) {
            /* Draw image */
            ctx.drawImage(particleImg, -half, -half, p.baseSize, p.baseSize);

            /* Tint with source-atop: paints color only on non-transparent pixels */
            if (TINT) {
                ctx.globalCompositeOperation = 'source-atop';
                ctx.fillStyle = TINT;
                ctx.fillRect(-half, -half, p.baseSize, p.baseSize);
                ctx.globalCompositeOperation = 'source-over';
            }
        } else {
            /* Fallback circle */
            var radius = Math.max(1, half);
            ctx.beginPath();
            ctx.arc(0, 0, radius, 0, Math.PI * 2);
            ctx.fillStyle = TINT ? hexToRgba(TINT, 0.8) : 'rgba(200,200,200,0.8)';
            ctx.fill();
        }

        ctx.restore();
    }

    /* ─── Animation loop ─── */
    function animate(inst) {
        sizeCanvas(inst);
        var ctx = inst.ctx;
        var w = inst.w, h = inst.h;

        ctx.clearRect(0, 0, w, h);

        for (var i = 0; i < inst.particles.length; i++) {
            var p = inst.particles[i];
            p.y        += p.speed;
            p.wobble   += p.wobbleSpd;
            p.x        += p.drift + Math.sin(p.wobble) * p.wobbleAmp;
            p.rotation += p.rotationSpd;

            if (p.y > h + p.baseSize) { p.y = -p.baseSize - Math.random() * 40; p.x = Math.random() * w; }
            if (p.x > w + p.baseSize) p.x = -p.baseSize;
            if (p.x < -p.baseSize)     p.x = w + p.baseSize;

            drawParticle(ctx, p);
        }

        inst.animId = requestAnimationFrame(function () { animate(inst); });
    }

    /* ─── Scan DOM ─── */
    function processElements() {
        var els = document.querySelectorAll('.' + CLASS);
        for (var i = 0; i < els.length; i++) {
            var el = els[i];
            if (!el.dataset.pfInit) {
                el.dataset.pfInit = '1';
                attachCanvas(el);
            }
            var ch = el.children;
            for (var j = 0; j < ch.length; j++) {
                if (!ch[j].classList.contains('pf-canvas') && !ch[j].style.zIndex) {
                    if (!ch[j].style.position || ch[j].style.position === 'static') {
                        ch[j].style.position = 'relative';
                    }
                    ch[j].style.zIndex = '1';
                }
            }
        }
    }

    /* ─── Resize ─── */
    window.addEventListener('resize', debounce(function () {
        for (var i = 0; i < instances.length; i++) sizeCanvas(instances[i]);
    }, 150));

    /* ─── MutationObserver ─── */
    var observer = new MutationObserver(debounce(function () { processElements(); }, 100));

    /* ─── Boot ─── */
    function boot() {
        processElements();
        observer.observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    /* ─── Load image ─── */
    if (IMG_URL) {
        if (IS_SVG) {
            loadSvgFromUrl(IMG_URL).then(function (img) { if (img) particleImg = img; });
        } else {
            loadImageFromUrl(IMG_URL).then(function (img) { if (img) particleImg = img; });
        }
    }

})();
