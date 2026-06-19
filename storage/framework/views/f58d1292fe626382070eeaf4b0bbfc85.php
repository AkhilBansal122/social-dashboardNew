<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title ?? 'Dashboard'); ?> — Social Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0f0f14;
            --bg2:       #17171f;
            --bg3:       #1e1e28;
            --bg4:       #252532;
            --border:    #2c2c3e;
            --text:      #e4e4f0;
            --muted:     #7878a0;
            --primary:   #7c6ff7;
            --primary-h: #6a5ee8;
            --ig1: #f09433; --ig2: #e6683c; --ig3: #dc2743; --ig4: #cc2366; --ig5: #bc1888;
            --snap: #fffc00;
            --green:  #22c55e;
            --red:    #ef4444;
            --amber:  #f59e0b;
            --blue:   #3b82f6;
            --r:  12px;
            --r2: 8px;
            --r3: 6px;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.6;
            font-size: 15px;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg2); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

        /* ── Navbar ── */
        .nav {
            background: var(--bg2);
            border-bottom: 1px solid var(--border);
            height: 58px;
            display: flex;
            align-items: center;
            padding: 0 1.75rem;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 200;
        }
        .nav-brand {
            font-weight: 700;
            font-size: 1.05rem;
            background: linear-gradient(135deg, var(--ig1), var(--ig3), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            letter-spacing: -.01em;
        }
        .nav-spacer { flex: 1; }
        .nav-user {
            font-size: .825rem;
            color: var(--muted);
            padding: .3rem .75rem;
            background: var(--bg3);
            border: 1px solid var(--border);
            border-radius: 99px;
        }

        /* ── Layout ── */
        .wrap { max-width: 1300px; margin: 0 auto; padding: 2rem 1.5rem 4rem; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .5rem 1.1rem; border-radius: var(--r2);
            font-size: .85rem; font-weight: 500; cursor: pointer;
            border: 1px solid transparent; text-decoration: none;
            transition: all .18s; white-space: nowrap; line-height: 1;
        }
        .btn-sm  { padding: .35rem .8rem; font-size: .8rem; }
        .btn-xs  { padding: .25rem .6rem; font-size: .75rem; }
        .btn-primary   { background: var(--primary); color: #fff; border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-h); border-color: var(--primary-h); }
        .btn-ghost { background: transparent; color: var(--muted); border-color: var(--border); }
        .btn-ghost:hover { color: var(--text); border-color: var(--muted); }
        .btn-danger { background: rgba(239,68,68,.12); color: #f87171; border-color: rgba(239,68,68,.25); }
        .btn-danger:hover { background: rgba(239,68,68,.22); }
        .btn-ig   { background: linear-gradient(135deg, var(--ig1), var(--ig3), var(--ig5)); color: #fff; border-color: transparent; }
        .btn-ig:hover { opacity: .88; }
        .btn-snap { background: var(--snap); color: #111; border-color: transparent; }
        .btn-snap:hover { background: #e8e500; }
        .btn:disabled, .btn[disabled] { opacity: .45; cursor: not-allowed; pointer-events: none; }

        /* ── Cards ── */
        .card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: var(--r);
            overflow: hidden;
        }
        .card-body { padding: 1.25rem; }

        /* ── Badges ── */
        .badge {
            display: inline-flex; align-items: center; gap: .3rem;
            padding: .18rem .6rem; border-radius: 99px;
            font-size: .72rem; font-weight: 600; letter-spacing: .01em;
        }
        .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
        .badge-green  { background: rgba(34,197,94,.13);  color: #4ade80; }
        .badge-amber  { background: rgba(245,158,11,.13); color: #fbbf24; }
        .badge-red    { background: rgba(239,68,68,.13);  color: #f87171; }
        .badge-muted  { background: rgba(120,120,160,.13); color: var(--muted); }
        .badge-purple { background: rgba(124,111,247,.13); color: #a78bfa; }

        /* ── Alerts ── */
        .alert {
            display: flex; align-items: flex-start; gap: .7rem;
            padding: .8rem 1rem; border-radius: var(--r2);
            font-size: .85rem; line-height: 1.5;
        }
        .alert-success { background: rgba(34,197,94,.08);  border: 1px solid rgba(34,197,94,.2);  color: #86efac; }
        .alert-error   { background: rgba(239,68,68,.08);  border: 1px solid rgba(239,68,68,.2);  color: #fca5a5; }
        .alert-warning { background: rgba(245,158,11,.08); border: 1px solid rgba(245,158,11,.2); color: #fde68a; }
        .alert-info    { background: rgba(59,130,246,.08); border: 1px solid rgba(59,130,246,.2); color: #93c5fd; }

        /* ── Forms ── */
        .form-group { margin-bottom: 1.1rem; }
        .form-label { display: block; font-size: .82rem; font-weight: 500; color: var(--muted); margin-bottom: .35rem; }
        .form-input {
            width: 100%; padding: .6rem .9rem;
            background: var(--bg3); border: 1px solid var(--border);
            border-radius: var(--r2); color: var(--text); font-size: .9rem;
            font-family: inherit; transition: border-color .18s;
        }
        .form-input:focus { outline: none; border-color: var(--primary); }
        .form-input::placeholder { color: var(--muted); opacity: .7; }
        .form-error { font-size: .78rem; color: #f87171; margin-top: .3rem; }

        /* ── Spinner ── */
        .spin {
            display: inline-block; width: 13px; height: 13px;
            border: 2px solid rgba(255,255,255,.25);
            border-top-color: currentColor;
            border-radius: 50%; animation: rotate .6s linear infinite;
        }
        @keyframes rotate { to { transform: rotate(360deg); } }

        /* ── Divider ── */
        .divider { height: 1px; background: var(--border); margin: 1.5rem 0; }
    </style>
</head>
<body>
<nav class="nav">
    <a class="nav-brand" href="/dashboard">⚡ Social Dashboard</a>
    <div class="nav-spacer"></div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
        <span class="nav-user"><?php echo e(auth()->user()->name); ?></span>
        <form action="/logout" method="POST" style="display:inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-ghost btn-sm">Sign out</button>
        </form>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</nav>

<div class="wrap">
    <?php echo e($slot); ?>

</div>

<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH C:\laragon\www\social-dashboard-v2\resources\views/layouts/app.blade.php ENDPATH**/ ?>