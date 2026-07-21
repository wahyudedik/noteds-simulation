<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $simulation->title }} - Embed</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Roboto', sans-serif;
            background: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
        }
        .embed-container {
            width: 100%;
            height: 100vh;
            position: relative;
        }
        .embed-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .embed-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(15, 23, 42, 0.9));
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .embed-footer a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 12px;
            transition: color 0.2s;
        }
        .embed-footer a:hover { color: #e2e8f0; }
        .embed-branding {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .embed-branding svg {
            width: 14px;
            height: 14px;
            fill: #64748b;
        }
    </style>
</head>
<body>
    <div class="embed-container">
        <iframe
            src="{{ $playUrl }}"
            sandbox="allow-scripts allow-same-origin allow-popups allow-forms"
            allowfullscreen
            loading="lazy"
            title="{{ $simulation->title }}"
        ></iframe>
        <div class="embed-footer">
            <span class="text-slate-500 text-xs">{{ $simulation->title }}</span>
            <a href="{{ route('simulations.show', $simulation->slug) }}" target="_blank" rel="noopener noreferrer" class="embed-branding">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                {{ config('app.name') }}
            </a>
        </div>
    </div>
</body>
</html>
