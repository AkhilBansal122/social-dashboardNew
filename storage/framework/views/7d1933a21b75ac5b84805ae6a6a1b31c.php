<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Social Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --bg: #0f0f14; --bg2: #17171f; --bg3: #1e1e28; --border: #2c2c3e; --text: #e4e4f0; --muted: #7878a0; --primary: #7c6ff7; --primary-h: #6a5ee8; --r: 12px; --r2: 8px; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .box { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r); padding: 2.25rem; width: 100%; max-width: 390px; }
        .logo { text-align: center; margin-bottom: 1.75rem; }
        .logo h1 { font-size: 1.45rem; font-weight: 700; background: linear-gradient(135deg, #f09433, #dc2743, #7c6ff7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .logo p { color: var(--muted); font-size: .875rem; margin-top: .4rem; }
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; font-size: .82rem; font-weight: 500; color: var(--muted); margin-bottom: .35rem; }
        .form-input { width: 100%; padding: .6rem .9rem; background: var(--bg3); border: 1px solid var(--border); border-radius: var(--r2); color: var(--text); font-size: .9rem; font-family: inherit; transition: border-color .18s; }
        .form-input:focus { outline: none; border-color: var(--primary); }
        .form-input::placeholder { color: var(--muted); opacity: .7; }
        .btn { display: block; width: 100%; padding: .65rem; background: var(--primary); color: #fff; border: none; border-radius: var(--r2); font-size: .95rem; font-weight: 600; cursor: pointer; font-family: inherit; transition: background .18s; margin-top: 1.25rem; }
        .btn:hover { background: var(--primary-h); }
        .alert { padding: .7rem .9rem; border-radius: var(--r2); margin-bottom: 1rem; font-size: .84rem; background: rgba(239,68,68,.09); border: 1px solid rgba(239,68,68,.22); color: #fca5a5; }
        .footer { text-align: center; margin-top: 1.25rem; font-size: .84rem; color: var(--muted); }
        .footer a { color: var(--primary); text-decoration: none; }
    </style>
</head>
<body>
<div class="box">
    <div class="logo">
        <h1>⚡ Social Dashboard</h1>
        <p>Create your account</p>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert"><?php echo e($errors->first()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <form action="/register" method="POST">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label class="form-label">Full name</label>
            <input type="text" name="name" class="form-input" value="<?php echo e(old('name')); ?>" placeholder="Jane Doe" required autofocus>
        </div>
        <div class="form-group">
            <label class="form-label">Email address</label>
            <input type="email" name="email" class="form-input" value="<?php echo e(old('email')); ?>" placeholder="you@example.com" required>
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-input" placeholder="Min. 8 characters" required>
        </div>
        <div class="form-group">
            <label class="form-label">Confirm password</label>
            <input type="password" name="password_confirmation" class="form-input" placeholder="Repeat password" required>
        </div>
        <button type="submit" class="btn">Create account →</button>
    </form>

    <div class="footer">Already have an account? <a href="/login">Sign in</a></div>
</div>
</body>
</html>
<?php /**PATH C:\laragon\www\social-dashboard-v2\resources\views/auth/register.blade.php ENDPATH**/ ?>