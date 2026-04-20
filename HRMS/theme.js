/**
 * Dynamic Color Theme Engine
 * Smoothly cycles the page background through logo-matched colors
 * (Navy Blue + Orange palette) using JS-driven hue animation.
 */
(function () {
    // Color stops that complement the logo (navy + orange)
    const stops = [
        { a: '#dbeafe', b: '#e0efff', c: '#fff7ed' }, // blue-white-peach
        { a: '#e0efff', b: '#fff0d6', c: '#ffd6a3' }, // blue-light orange
        { a: '#ffd6a3', b: '#ffe4c4', c: '#fff7ed' }, // warm orange-cream
        { a: '#ffe4c4', b: '#e0efff', c: '#dbeafe' }, // cream-blue
        { a: '#dbeafe', b: '#c7d9f9', c: '#e8f0fe' }, // deeper blue
        { a: '#c7d9f9', b: '#fff0d6', c: '#ffecd1' }, // blue to peach
    ];

    let currentIndex = 0;
    let nextIndex = 1;
    let progress = 0;

    function lerp(a, b, t) {
        // Parse hex color and interpolate
        const parse = hex => {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return [r, g, b];
        };
        const [ar, ag, ab] = parse(a);
        const [br, bg, bb] = parse(b);
        const r = Math.round(ar + (br - ar) * t);
        const g = Math.round(ag + (bg - ag) * t);
        const bl = Math.round(ab + (bb - ab) * t);
        return `rgb(${r},${g},${bl})`;
    }

    function tick() {
        progress += 0.003; // Speed of transition (lower = slower)
        if (progress >= 1) {
            progress = 0;
            currentIndex = nextIndex;
            nextIndex = (nextIndex + 1) % stops.length;
        }

        const cur = stops[currentIndex];
        const nxt = stops[nextIndex];

        const a = lerp(cur.a, nxt.a, progress);
        const b = lerp(cur.b, nxt.b, progress);
        const c = lerp(cur.c, nxt.c, progress);

        document.body.style.background =
            `linear-gradient(135deg, ${a} 0%, ${b} 50%, ${c} 100%)`;
        document.body.style.backgroundAttachment = 'fixed';

        requestAnimationFrame(tick);
    }

    // Remove any existing CSS background animation to avoid conflict
    document.body.style.animation = 'none';
    requestAnimationFrame(tick);
})();
