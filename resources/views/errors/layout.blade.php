<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title') · Walleys</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #f7f9fa; color: #0f172a; font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        a { text-decoration: none; }
        a:focus-visible { outline: 2px solid #138080; outline-offset: 4px; }
        .page { display: flex; min-height: 100vh; flex-direction: column; padding: 24px; }
        .brand { display: inline-flex; width: fit-content; align-items: center; gap: 10px; color: #0f172a; font-size: 20px; font-weight: 800; letter-spacing: -.5px; border-radius: 4px; }
        .brand img { width: 34px; height: 34px; object-fit: contain; }
        main { display: flex; flex: 1; align-items: center; justify-content: center; padding: 48px 0 72px; }
        .panel { width: 100%; max-width: 480px; border: 1px solid #e2e8f0; border-top: 4px solid #1e293b; border-radius: 20px; background: #fff; padding: 32px; }
        .code { display: inline-flex; margin: 0; border-radius: 7px; background: #f1f5f9; color: #475569; padding: 6px 9px; font-size: 12px; font-weight: 600; }
        .code.warning { background: #fffbeb; color: #92400e; }
        .code.error { background: #fff1f2; color: #9f1239; }
        h1 { margin: 20px 0 0; font-size: 30px; line-height: 1.2; font-weight: 750; letter-spacing: -.8px; }
        .message { margin: 16px 0 0; color: #64748b; font-size: 14px; line-height: 1.75; }
        .actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 28px; }
        .button { display: inline-flex; min-height: 44px; align-items: center; justify-content: center; border: 1px solid #138080; border-radius: 10px; background: #138080; color: #fff; padding: 10px 16px; font-size: 14px; font-weight: 600; }
        .button:hover { border-color: #106868; background: #106868; }
        .button.secondary { border-color: #e2e8f0; background: #fff; color: #475569; }
        .button.secondary:hover { border-color: #b1e8e4; background: #effbfa; color: #106868; }
        @media (min-width: 640px) { .page { padding: 32px 40px; } .panel { padding: 40px; } }
        @media (max-width: 359px) { .page { padding: 20px; } .panel { padding: 24px; } h1 { font-size: 27px; } .button { width: 100%; } }
    </style>
</head>
<body>
    <div class="page">
        <a href="/" class="brand" aria-label="Walleys home"><img src="/images/logo.png" alt="" width="34" height="34"><span>Walleys</span></a>
        <main>
            <section class="panel" aria-labelledby="error-title">
                <p class="code @yield('severity')">Error @yield('code')</p>
                <h1 id="error-title">@yield('title')</h1>
                <p class="message">@yield('message')</p>
                <div class="actions">
                    <a href="@yield('primary-url', '/')" class="button">@yield('primary-label', 'Back to home')</a>
                    <a href="@yield('secondary-url', '/login')" class="button secondary">@yield('secondary-label', 'Go to login')</a>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
