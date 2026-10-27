<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f7f5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cuaca Tanjungpinang — Cerah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        :root{--ink:#142f4f;--muted:#6f8298;--line:#d6e4ef;--paper:#fff;--canvas:#eaf2f8;--teal:#2476bd;--teal-dark:#155a98;--mint:#e7f2fc;--blue:#eaf4ff;--amber:#f4c542;--shadow:0 14px 34px rgba(30,76,120,.11);--radius:22px}
        *{box-sizing:border-box}body{margin:0;background:var(--canvas);color:var(--ink);font:15px/1.55 'Plus Jakarta Sans',sans-serif}button,select{font:inherit}button{cursor:pointer}a{color:inherit;text-decoration:none}.shell{max-width:1440px;min-height:100vh;margin:auto;padding:0 34px 42px}.topbar{height:82px;display:flex;align-items:center;justify-content:space-between;gap:20px;border-bottom:1px solid var(--line)}.brand{display:flex;align-items:center;gap:10px;letter-spacing:0}.brand-logo{width:52px;height:52px;object-fit:contain;mix-blend-mode:multiply;flex:0 0 auto}.brand-name{display:block;color:#175b98;font:800 18px/1 'Plus Jakarta Sans',sans-serif;letter-spacing:1.4px}.brand-tagline{display:block;margin-top:4px;color:#55728c;font:600 10px/1.3 'Plus Jakarta Sans',sans-serif;letter-spacing:.1px}.brand-mark{display:none}.top-actions{display:flex;align-items:center;gap:12px}.live-pill{display:flex;align-items:center;gap:7px;color:#52749a;font-size:13px;font-weight:600}.live-dot{width:8px;height:8px;border-radius:50%;background:#45b887;box-shadow:0 0 0 4px #45b88720}.icon-btn{width:40px;height:40px;display:grid;place-items:center;border:1px solid var(--line);border-radius:13px;background:#fff;color:var(--ink)}.icon-btn svg{width:17px}.icon-btn:hover{border-color:#a8c9e6;background:#f8fbff}.page{padding-top:28px}.intro{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:22px}.eyebrow{margin:0 0 5px;color:var(--teal);font-size:12px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase}.intro h1{margin:0;font:800 clamp(27px,3vw,36px)/1.2 'Plus Jakarta Sans',sans-serif;letter-spacing:-1px}.intro p{margin:7px 0 0;color:var(--muted);font-size:15px}.error-banner{display:none;margin-bottom:18px;padding:13px 16px;border:1px solid #f4dda4;border-radius:14px;background:#fff6e2;color:#8a5b08}.error-banner.show{display:block}.hero{position:relative;min-height:270px;overflow:hidden;padding:30px 34px;border-radius:27px;background:linear-gradient(118deg,#175b98,#277db9 57%,#62add4);color:#fff;box-shadow:0 18px 40px #1d66a527}.hero:before,.hero:after{position:absolute;border:1px solid #ffffff24;border-radius:50%;content:"";pointer-events:none}.hero:before{top:-270px;right:6%;width:410px;height:410px}.hero:after{top:-220px;right:-4%;width:520px;height:520px}.hero-content{position:relative;z-index:1;display:grid;grid-template-columns:1.1fr .9fr;align-items:center;gap:25px}.hero-place{color:#e0f1ff;font-size:14px;font-weight:600}.hero-date{margin-top:3px;color:#d0e8fa;font-size:13px}.temp-row{display:flex;align-items:center;gap:20px;margin-top:9px}.weather-symbol{font-size:54px;line-height:1}.temperature{font:700 clamp(64px,8vw,91px)/.98 'Plus Jakarta Sans',sans-serif;letter-spacing:-5px}.temperature sup{margin-left:3px;font-size:27px;vertical-align:top}.condition{margin:6px 0 1px;font-size:19px;font-weight:700}.feels{color:#e3f2ff;font-size:13px}.hero-summary{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:11px}.hero-stat{padding:14px 15px;border:1px solid #ffffff45;border-radius:16px;background:#ffffff18;backdrop-filter:blur(4px)}.hero-stat-label{display:flex;align-items:center;gap:7px;color:#e1f1fc;font-size:12px}.hero-stat-value{margin-top:7px;font:700 20px 'Plus Jakarta Sans',sans-serif}.hero-stat-unit{margin-left:3px;color:#e1f1fc;font:500 12px 'Plus Jakarta Sans',sans-serif}.section{margin-top:30px}.section-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:14px}.section-title{margin:0;font:800 19px 'Plus Jakarta Sans',sans-serif;letter-spacing:-.35px}.section-subtitle{margin:4px 0 0;color:var(--muted);font-size:13px}.section-link{color:var(--teal);font-size:13px;font-weight:700}.metric-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:13px}.metric-card,.panel,.day-card,.advice-card{border:1px solid var(--line);border-radius:var(--radius);background:var(--paper);box-shadow:var(--shadow)}.metric-card{min-height:112px;padding:17px 18px}.metric-top{display:flex;align-items:center;justify-content:space-between;color:var(--muted);font-size:13px;font-weight:600}.metric-icon{width:32px;height:32px;display:grid;place-items:center;border-radius:11px;background:var(--mint);color:var(--teal)}.metric-icon svg{width:16px}.metric-value{margin-top:9px;font:700 25px 'Plus Jakarta Sans',sans-serif;letter-spacing:-.5px}.metric-note{margin-top:1px;color:var(--muted);font-size:12px}.panel{padding:20px 21px}.days-grid{display:grid;grid-template-columns:repeat(7,minmax(105px,1fr));gap:10px}.day-card{padding:15px 10px 13px;text-align:center;border-radius:18px;box-shadow:none}.day-card.today{border-color:#f0d46c;background:#fffcf0}.day-name{color:var(--muted);font-size:12px;font-weight:700}.day-icon{margin:10px 0 5px;font-size:25px}.day-condition{min-height:30px;color:var(--muted);font-size:11px}.day-temps{display:flex;justify-content:center;gap:9px;margin-top:8px;font:700 14px 'Plus Jakarta Sans',sans-serif}.day-temps .low{color:#94a2b0;font-weight:500}.rain-chance{margin-top:9px;color:#397cae;font-size:11px}.hour-panel{overflow:hidden;padding:18px 20px 20px}.hours{display:grid;grid-auto-columns:minmax(78px,1fr);grid-auto-flow:column;gap:8px;overflow-x:auto;padding-bottom:4px;scrollbar-width:thin}.hour-card{padding:12px 7px 11px;border-radius:15px;background:#f6faff;text-align:center}.hour-time{color:var(--muted);font-size:11px;font-weight:600}.hour-icon{margin:8px 0 5px;font-size:21px}.hour-temp{font:700 15px 'Plus Jakarta Sans',sans-serif}.hour-rain{margin-top:6px;color:#397cae;font-size:11px}.content-grid{display:grid;grid-template-columns:1.35fr .85fr;gap:15px}.advice-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.advice-card{display:flex;gap:12px;padding:15px 16px;border-radius:17px;box-shadow:none}.advice-emoji{width:34px;height:34px;display:grid;flex:0 0 34px;place-items:center;border-radius:12px;background:#fff7dc;font-size:17px}.advice-title{font-size:13px;font-weight:700}.advice-message{margin-top:4px;color:var(--muted);font-size:12px;line-height:1.55}.status{display:inline-flex;align-items:center;gap:5px;margin-top:7px;color:var(--teal);font-size:10px;font-weight:700;letter-spacing:.4px;text-transform:uppercase}.status:before{width:6px;height:6px;border-radius:50%;background:#4caf84;content:""}.status.waspada{color:#ad6b22}.status.waspada:before{background:#edae4e}.status.saran{color:#5b7890}.status.saran:before{background:#6c9fc2}.marine-card{height:100%;padding:20px 21px;border:1px solid #d8e8f5;border-radius:var(--radius);background:linear-gradient(145deg,#edf6ff,#fff 70%);box-shadow:var(--shadow)}.marine-heading{display:flex;align-items:center;justify-content:space-between}.marine-badge{padding:6px 9px;border-radius:9px;background:#fff4c9;color:#80630b;font-size:11px;font-weight:700}.marine-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-top:17px}.marine-metric{padding:13px;border:1px solid #e8f0f0;border-radius:15px;background:#ffffffba}.marine-label{color:var(--muted);font-size:11px}.marine-value{margin-top:5px;font:700 20px 'Plus Jakarta Sans',sans-serif}.marine-note{margin:13px 2px 0;color:#7c8d8a;font-size:11px;line-height:1.55}.empty{padding:22px;color:var(--muted);font-size:13px;text-align:center}.loading{color:#e1f1fc}.footer{display:flex;justify-content:space-between;gap:14px;margin-top:27px;padding:17px 3px;border-top:1px solid var(--line);color:#8592a0;font-size:11px}.footer a{color:var(--teal);font-weight:700}.attribution{display:flex;align-items:center;gap:5px}.attribution svg{width:13px;color:var(--teal)}
        .brand{gap:10px;letter-spacing:0}.brand-logo{width:52px;height:52px;object-fit:contain;mix-blend-mode:multiply;flex:0 0 auto}.brand-name{display:block;font:800 18px/1 Manrope,sans-serif;letter-spacing:1.4px;color:#175b98}.brand-tagline{display:block;color:#55728c;font:600 9px/1.3 'DM Sans',sans-serif;letter-spacing:.1px;margin-top:4px}.brand-mark{display:none}
        @media(max-width:640px){.brand-logo{width:45px;height:45px}.brand-name{font-size:16px}.brand-tagline{font-size:8px}}
        @media(max-width:1000px){.shell{padding:0 22px 32px}.hero-content{grid-template-columns:1fr .9fr}.days-grid{grid-template-columns:repeat(7,120px);overflow-x:auto;padding-bottom:5px}.metric-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.content-grid{grid-template-columns:1fr}.marine-grid{grid-template-columns:repeat(4,1fr)}}
        @media(max-width:640px){.shell{padding:0 15px 25px}.topbar{height:68px}.brand{font-size:17px}.brand-mark{height:35px;width:35px}.live-pill{font-size:0}.live-dot{width:9px;height:9px}.page{padding-top:22px}.intro{display:block}.intro h1{font-size:27px}.select-wrap{margin-top:16px;justify-content:space-between}.select-wrap select{max-width:none;flex:1}.hero{padding:23px 21px;min-height:0;border-radius:23px}.hero-content{grid-template-columns:1fr;gap:19px}.hero-summary{grid-template-columns:repeat(2,1fr)}.hero-stat{padding:11px 12px}.hero-stat-value{font-size:18px}.temperature{font-size:75px}.temperature sup{font-size:23px}.weather-symbol{font-size:45px}.metric-card{padding:14px;min-height:105px;border-radius:18px}.metric-value{font-size:21px}.section{margin-top:24px}.section-head{align-items:flex-start}.section-title{font-size:17px}.panel{padding:16px;border-radius:19px}.advice-list{grid-template-columns:1fr}.marine-grid{grid-template-columns:repeat(2,1fr)}.footer{flex-direction:column}.day-card{min-width:110px}}
        .location-control{position:relative;width:min(370px,100%);flex:0 0 auto}.location-caption{display:block;color:#748ba3;font-size:10px;font-weight:700;letter-spacing:1.15px;margin:0 0 6px 3px}.location-toggle{width:100%;min-height:58px;display:flex;align-items:center;gap:12px;text-align:left;padding:9px 13px;background:#fff;border:1px solid var(--line);border-radius:16px;color:var(--ink);box-shadow:0 7px 20px #1e4b7308;transition:.18s}.location-toggle:hover,.location-toggle[aria-expanded=true]{border-color:#9dc4e4;box-shadow:0 8px 24px #2679bd18}.location-pin{width:36px;height:36px;display:grid;place-items:center;border-radius:12px;background:var(--mint);color:var(--teal);flex:0 0 auto}.location-pin svg{width:18px}.location-copy{min-width:0;flex:1}.location-level{display:block;color:#8194a7;font-size:10px;margin-bottom:1px}.location-name{display:block;font-weight:700;font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.location-chevron{width:17px;color:#71869a;transition:transform .18s}.location-toggle[aria-expanded=true] .location-chevron{transform:rotate(180deg)}.location-menu{position:absolute;z-index:10;right:0;top:calc(100% + 10px);width:100%;padding:15px;background:#fff;border:1px solid var(--line);border-radius:19px;box-shadow:0 20px 50px #183b5e20;animation:menu-in .16s ease-out}.location-menu[hidden]{display:none}@keyframes menu-in{from{opacity:0;transform:translateY(-5px)}to{opacity:1;transform:translateY(0)}}.location-menu-head{display:flex;align-items:center;justify-content:space-between;margin:2px 3px 10px}.location-menu-head small{display:block;color:var(--teal);font-size:9px;letter-spacing:1px;font-weight:700}.location-menu-head strong{font:700 14px Manrope,sans-serif}.location-up{border:0;background:#edf5fc;color:var(--teal);border-radius:10px;width:34px;height:34px;font-size:16px}.location-up[hidden]{display:none}.location-crumbs{display:flex;gap:5px;align-items:center;flex-wrap:wrap;margin:0 3px 12px;color:#99a8b5;font-size:10px}.location-crumbs button{border:0;background:transparent;color:var(--teal);padding:2px 0;font-size:10px;font-weight:700}.location-options{display:grid;gap:7px;max-height:290px;overflow:auto}.location-option{width:100%;display:flex;align-items:center;justify-content:space-between;text-align:left;padding:11px 12px;border:1px solid #eaf0f5;background:#f9fcff;border-radius:13px;color:var(--ink);transition:.16s}.location-option:hover{background:#eef7ff;border-color:#c1dbef;transform:translateY(-1px)}.location-option strong{display:block;font-size:12px}.location-option small{display:block;color:var(--muted);font-size:10px;margin-top:2px}.location-option .arrow{color:#78a0bf;font-size:17px}.location-empty{padding:12px;color:var(--muted);font-size:11px;text-align:center;background:#f6faff;border-radius:12px}
        .region-day-row{display:grid;grid-template-columns:repeat(7,minmax(88px,1fr));gap:8px;overflow:auto}.region-day{min-width:88px;text-align:center;background:#f6faff;border-radius:14px;padding:10px 7px}.region-day:nth-child(1){background:#fffbee}.region-day-date{font-size:10px;font-weight:700;color:#61778d}.region-day-icon{font-size:21px;margin:6px 0 3px}.region-day-condition{font-size:9px;color:var(--muted);min-height:27px}.region-day-temp{font:700 12px Manrope,sans-serif;margin-top:5px}.region-day-rain{font-size:9px;color:#397cae;margin-top:3px}.detail-only[hidden],.overview-only[hidden],#marine-section[hidden],#map-section[hidden],.main-nav .nav-link[hidden]{display:none!important}
        .geo-status{margin:7px 3px 0;color:#5c7590;font-size:10px;line-height:1.45}.geo-status[hidden],.geo-button[hidden]{display:none}.geo-button{margin:8px 0 0 2px;padding:7px 10px;border:1px solid #d5e6f4;border-radius:10px;background:#fff;color:#246ca5;font-size:10px;font-weight:700}.geo-button:hover{background:#f2f8fe;border-color:#abcde8}
        .topbar{height:80px}.main-nav{display:flex;align-items:center;gap:5px;margin-left:auto}.nav-link{display:flex;align-items:center;gap:8px;padding:9px 13px;border:0;border-radius:11px;background:transparent;color:#526b84;font-size:12px;font-weight:700;white-space:nowrap;transition:background .16s,color .16s}.nav-link:hover,.nav-link.active{background:#e8f2fb;color:#185f99}.location-control{width:auto;flex:0 0 auto}.location-toggle{min-height:40px;width:auto;padding:8px 11px;box-shadow:none;color:#526b84}.location-toggle:hover,.location-toggle[aria-expanded=true]{border-color:transparent;background:#e8f2fb;box-shadow:none;color:#185f99}.nav-location-current{display:grid;gap:0;margin-left:3px;padding-left:9px;border-left:1px solid #dce7f1;text-align:left}.nav-location-current small{color:#8293a5;font-size:9px;line-height:1.15;font-weight:500}.nav-location-current strong{max-width:135px;overflow:hidden;color:var(--ink);font-size:10px;text-overflow:ellipsis}.main-nav .location-chevron{width:14px}.location-menu{top:calc(100% + 9px);right:0;left:auto;width:320px}.top-actions{margin-left:13px}.geo-status{position:static;width:auto;margin:12px 0 0;padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:#f7fbff;color:#526b84;font-size:11px;line-height:1.45;box-shadow:none}.geo-button{position:static;width:100%;margin:12px 0 0;padding:9px 11px;border:1px solid #d5e6f4;border-radius:10px;background:#f4f9fe;color:#246ca5;font-size:11px;font-weight:700;text-align:center;white-space:nowrap}.geo-button:hover{background:#eaf4fc;border-color:#abcde8}
        @media(max-width:1000px){.topbar{gap:8px}.main-nav{gap:1px}.nav-link{padding:8px 9px}.top-actions{margin-left:4px}}
        @media(max-width:640px){.topbar{height:auto;min-height:68px;padding:8px 0;flex-wrap:wrap}.main-nav{order:3;width:100%;justify-content:center;margin:0;border-top:1px solid var(--line);padding-top:7px}.top-actions{order:2;margin-left:auto}.brand{order:1}.nav-link{font-size:11px}.nav-location-current strong{max-width:90px}.location-control{width:auto;margin-top:0}.location-menu{left:auto;right:0;width:min(320px,calc(100vw - 30px));transform:none}}
        @media(max-width:640px){.region-forecast{padding:14px}.region-forecast-head{align-items:flex-start}.region-forecast-open{padding:7px 9px}.region-day-row{grid-template-columns:repeat(7,88px)}}
        @media(max-width:640px){.location-control{width:auto;margin-top:0}.location-menu{left:auto;right:0}.location-toggle{min-height:40px}.location-options{max-height:45vh}}
        .map-panel{position:relative;overflow:hidden;padding:12px;border:1px solid var(--line);border-radius:22px;background:#fff;box-shadow:var(--shadow)}#weather-map{height:410px;width:100%;border-radius:15px;background:#edf4f8;z-index:1}.map-note{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:11px 5px 2px;color:var(--muted);font-size:11px}.map-level{flex:0 0 auto;padding:5px 8px;border-radius:8px;background:#fff6d7;color:#7a620e;font-size:10px;font-weight:700}.map-pin{width:28px;height:28px;display:grid;place-items:center;border:2px solid #fff;border-radius:50% 50% 50% 4px;background:#3182bd;color:#fff;font:700 11px Manrope,sans-serif;box-shadow:0 3px 10px #173c5c55;transform:rotate(-45deg)}.map-pin span{transform:rotate(45deg)}.map-pin.selected{background:#f2bd28;color:#183b5d;box-shadow:0 0 0 4px #f2bd2855,0 3px 10px #173c5c55}.leaflet-tooltip.map-tooltip{border:0;border-radius:8px;padding:5px 8px;color:#18314e;font:700 11px 'DM Sans',sans-serif;box-shadow:0 4px 12px #183b5c22}.map-fallback{min-height:150px;display:grid;place-items:center;padding:24px;color:var(--muted);text-align:center}
        @media(max-width:640px){#weather-map{height:350px}.map-panel{padding:8px}.map-note{align-items:flex-start;font-size:10px}}
        .climate-summary{display:grid;grid-template-columns:1.5fr repeat(3,1fr);gap:12px}.climate-card,.climate-chart-card{border:1px solid var(--line);border-radius:19px;background:#fff;padding:17px 18px;box-shadow:var(--shadow)}.climate-card{display:flex;flex-direction:column;gap:6px;min-height:105px}.climate-card strong{font:700 20px 'Plus Jakarta Sans',sans-serif;letter-spacing:-.4px}.climate-feature{background:linear-gradient(135deg,#f6fbff,#fff8e4);border-color:#e9e4cd}.climate-feature strong{font-size:17px}.climate-kicker{color:#758ba0;font-size:10px;font-weight:800;letter-spacing:.8px}.climate-small{color:var(--muted);font-size:11px;line-height:1.4}.climate-chart-card{margin-top:13px}.climate-chart-head,.climate-legend{display:flex;align-items:center;justify-content:space-between;gap:12px;color:var(--muted);font-size:11px}.climate-chart-head>div{display:grid;gap:3px}.climate-chart-head strong{color:var(--ink);font-size:13px}.climate-chart{height:112px;display:flex;align-items:flex-end;gap:4px;overflow-x:auto;margin:20px 2px 6px;padding:0 3px}.rain-column{position:relative;min-width:11px;flex:1;height:100%;display:flex;align-items:flex-end;justify-content:center}.rain-column:before{content:"";position:absolute;bottom:0;left:50%;width:1px;height:100%;background:#eef3f7;z-index:0}.rain-bar{position:relative;z-index:1;width:min(18px,80%);min-height:2px;border-radius:5px 5px 2px 2px;background:linear-gradient(180deg,#63b6db,#2783bd)}.rain-column[title]:hover .rain-bar{background:#f1c443}.climate-legend{padding-top:9px;border-top:1px solid #eef2f6}.climate-legend>span:first-child{display:flex;align-items:center;gap:6px}.rain-dot{width:8px;height:8px;border-radius:50%;background:#3189c1}.climate-chart-head>span{white-space:nowrap}.climate-disclaimer{margin:10px 3px;color:var(--muted);font-size:10px;line-height:1.5}.climate-chart-card[hidden],#climate-summary[hidden]{display:none}.main-nav>.nav-link{font-size:13px}.intro p,.section-subtitle{font-size:14px}.section-title{font-family:'Plus Jakarta Sans',sans-serif}.location-name{font-size:14px}.region-forecast-name{font-family:'Plus Jakarta Sans',sans-serif}.region-day-date{font-size:11px}.region-day-condition{font-size:10px}.map-note{font-size:12px}
        @media(max-width:800px){.climate-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.climate-feature{grid-column:span 2}}
        @media(max-width:640px){.climate-summary{gap:8px}.climate-card{min-height:96px;padding:13px}.climate-card strong{font-size:17px}.climate-feature strong{font-size:15px}.climate-kicker{font-size:9px}.climate-small{font-size:10px}.climate-chart-card{padding:15px}.climate-chart-head,.climate-legend{font-size:10px}.main-nav .nav-link{font-size:11px}}
        .topbar{height:72px}.page{padding-top:19px}.intro{align-items:center;margin-bottom:15px}.intro h1{font-size:clamp(25px,2.5vw,32px)}.intro p{margin-top:4px}.location-menu{width:min(390px,calc(100vw - 32px));padding:12px}.location-options{gap:2px;max-height:min(65vh,460px);padding:2px}.region-tree-row{display:flex;align-items:center;gap:5px;min-height:39px;padding-left:calc(var(--tree-depth,0)*13px)}.region-tree-row.level-city,.region-tree-row.level-regency{--tree-depth:1}.region-tree-row.level-district{--tree-depth:2}.region-tree-row.level-village{--tree-depth:3}.region-tree-select{flex:1;min-width:0;display:flex;align-items:center;justify-content:space-between;gap:8px;padding:8px 10px;border:0;border-radius:10px;background:transparent;color:var(--ink);text-align:left}.region-tree-select:hover,.region-tree-select[aria-current=true]{background:#edf6ff;color:#145f9f}.region-tree-select span{overflow:hidden;font-size:12px;font-weight:650;text-overflow:ellipsis;white-space:nowrap}.region-tree-select small{flex:0 0 auto;color:#8295a8;font-size:10px}.region-tree-toggle{width:30px;height:30px;flex:0 0 30px;border:1px solid #e3edf5;border-radius:9px;background:#fff;color:#327caf;font-size:17px;line-height:1;transition:transform .18s,background .18s}.region-tree-toggle[aria-expanded=true]{transform:rotate(180deg);background:#eef7ff}.region-tree-children{border-left:1px solid #dce9f3;margin-left:15px;padding-left:4px}.region-tree-children[hidden]{display:none}.location-crumbs,.location-up{display:none!important}.hero-place{font-size:14px}.hero-stat-note{font-size:11px}.hero-stat-note::first-letter{text-transform:uppercase}
        @media(max-width:640px){.topbar{height:auto;min-height:68px}.page{padding-top:16px}.intro h1{font-size:26px}.region-tree-row.level-city,.region-tree-row.level-regency{--tree-depth:0}.region-tree-row.level-district{--tree-depth:1}.region-tree-row.level-village{--tree-depth:2}.location-menu{left:auto;right:0;width:min(370px,calc(100vw - 30px))}}
        #climate-insights{padding:22px;border:1px solid #e4edf5;border-radius:24px;background:linear-gradient(155deg,#f8fbff 0%,#f3f8fc 48%,#fff9e8 100%)}#climate-insights>.section-head{align-items:center;margin-bottom:18px}.climate-summary{grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.climate-card{position:relative;min-height:126px;padding:18px;border-radius:18px;box-shadow:0 8px 22px #264d6c0b}.climate-feature{grid-column:span 1;background:linear-gradient(145deg,#fff,#edf7ff);border-color:#dceaf5}.climate-card strong{font-size:22px;line-height:1.3}.climate-feature strong{font-size:17px}.climate-kicker{font-size:10px;letter-spacing:.9px}.climate-small{font-size:12px;line-height:1.5}.climate-chart-card{margin-top:14px;padding:18px 20px;border-radius:19px;box-shadow:0 8px 24px #264d6c0b}.climate-chart-head{align-items:flex-start;font-size:12px}.climate-chart-head>div{gap:4px}.climate-chart-head strong{font-size:15px}.climate-chart-head #climate-source{font-size:11px;line-height:1.5}.climate-chart-wrap{width:100%;overflow-x:auto;margin-top:8px}.climate-chart{display:block;width:100%;min-width:650px;height:auto;overflow:visible;margin:0;padding:0}.climate-grid-line{stroke:#e8eef4;stroke-width:1}.climate-axis-line{stroke:#9aabb9;stroke-width:1.2}.climate-axis-label,.climate-date-label{fill:#70849a;font:11px 'Plus Jakarta Sans',sans-serif}.climate-date-label{font-size:10px}.climate-bar{fill:#358ec3;transition:fill .15s}.climate-bar:hover{fill:#edbd36}.climate-bar-missing{fill:#e7edf2;stroke:#c6d3dd;stroke-width:1;stroke-dasharray:2 2}.climate-legend{justify-content:flex-start;flex-wrap:wrap;gap:8px 18px;margin-top:3px;padding-top:12px;font-size:11px}.climate-legend #climate-chart-range{margin-left:auto;color:#8191a0}.rain-dot,.missing-dot{width:9px;height:9px;border-radius:50%;background:#358ec3}.missing-dot{background:#e7edf2;border:1px dashed #aab9c6}.climate-disclaimer{margin:12px 3px 0;padding:11px 13px;border:1px solid #e8edf1;border-radius:12px;background:#ffffffa8;color:#687f93;font-size:11px;line-height:1.6}.climate-chart-card[hidden],#climate-summary[hidden]{display:none}
        @media(max-width:900px){.climate-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.climate-feature{grid-column:span 2}}
        @media(max-width:640px){#climate-insights{padding:15px;border-radius:20px}.climate-summary{gap:9px}.climate-card{min-height:112px;padding:13px;border-radius:15px}.climate-card strong{font-size:18px}.climate-feature strong{font-size:15px}.climate-kicker{font-size:9px}.climate-small{font-size:10px}.climate-chart-card{padding:14px}.climate-chart-head,.climate-legend{font-size:10px}.climate-legend #climate-chart-range{width:100%;margin:0}.climate-chart{min-width:640px}}
        .climate-axis-title{fill:#4e687f;font:700 11px 'Plus Jakarta Sans',sans-serif}.metric-icon.quality-good{color:#168555;background:#e7f8ee}.metric-icon.quality-moderate{color:#9b7100;background:#fff6d7}.metric-icon.quality-sensitive{color:#b75b18;background:#fff0e2}.content-grid{grid-template-columns:1fr}.advice-list{grid-template-columns:repeat(3,minmax(0,1fr))}.advice-emoji svg{width:23px;height:23px}.hero-stat.quality-good{border-left:4px solid #36c982}.hero-stat.quality-moderate{border-left:4px solid #57b6d8}.hero-stat.quality-sensitive{border-left:4px solid #f0b83f}.hero-stat.quality-unhealthy{border-left:4px solid #e55a53}.hero-stat.quality-hazardous{border-left:4px solid #8d9398}.pm25-scale{position:relative;display:block;height:6px;margin-top:7px;border-radius:8px;background:linear-gradient(90deg,#39b66d 0 6.2%,#369bd1 6.2% 22.1%,#f1c644 22.1% 60%,#e45b51 60% 99.9%,#858a8e 99.9%)}.pm25-scale i{position:absolute;top:50%;width:9px;height:9px;border:2px solid #fff;border-radius:50%;background:#183954;transform:translate(-50%,-50%)}.pm25-scale-label{display:block;margin-top:4px;color:#fff;font-size:9px;line-height:1.4}@media(max-width:900px){.advice-list{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:640px){.advice-list{grid-template-columns:1fr}}
        .hero{min-height:230px;padding:25px 30px}.hero-content{grid-template-columns:minmax(0,1fr) minmax(280px,1.05fr) minmax(0,1fr);gap:28px}.hero-brief{align-self:center;max-width:300px}.hero-panel-title{margin-bottom:8px;color:#fff;font-size:14px;font-weight:750;letter-spacing:.15px}.hero-brief p{margin:0;color:#f4f9ff;font-size:14px;line-height:1.65}.hero-brief-note{margin-top:10px;color:#dceeff;font-size:12px}.hero-metrics{align-self:center;display:flex;flex-direction:column;border-top:1px solid #ffffff35;border-bottom:1px solid #ffffff35}.hero-metric{display:grid;grid-template-columns:30px minmax(0,1fr) auto;align-items:center;gap:10px;min-height:48px;padding:6px 2px;border-bottom:1px solid #ffffff35}.hero-metric:last-child{border-bottom:0}.hero-metric-icon{width:25px;height:25px;display:grid;place-items:center;border-radius:8px;background:#ffffff1f;color:#fff}.hero-metric-icon svg{width:15px;height:15px}.hero-metric-label{color:#edf6ff;font-size:12px;font-weight:550}.hero-metric-value{color:#fff;font-size:15px;font-weight:750;white-space:nowrap}.hero-metric.quality-good .hero-metric-icon{background:#d9f7e5;color:#176e45}.hero-metric.quality-moderate .hero-metric-icon{background:#fff1c9;color:#8a6500}.hero-metric.quality-sensitive .hero-metric-icon{background:#ffe5cd;color:#995016}.hero-metric.quality-unhealthy .hero-metric-icon{background:#ffdeda;color:#a6332b}.hero-metric.quality-hazardous .hero-metric-icon{background:#e7e8ea;color:#383d45}.hero-current{align-self:center;min-width:0}.hero-current .hero-place{color:#fff;font-size:14px;font-weight:650}.hero-current .hero-date{color:#e0f0fc;font-size:12px}.hero-current .temp-row{gap:15px;margin-top:12px}.hero-current .weather-symbol{font-size:48px}.hero-current .temperature{font-size:72px}.hero-current .condition{margin:3px 0 0;font-size:18px}.hero-current .feels{margin-top:3px;color:#e6f3ff;font-size:12px}@media(max-width:900px){.hero{padding:23px}.hero-content{grid-template-columns:1fr 1.1fr;gap:22px}.hero-current{grid-column:1/-1;display:grid;grid-template-columns:1fr auto;align-items:center;width:100%;padding-top:16px;border-top:1px solid #ffffff35}.hero-current .hero-place,.hero-current .hero-date{grid-column:1}.hero-current .temp-row{grid-column:2;grid-row:1/4;margin:0}.hero-current .condition,.hero-current .feels{grid-column:1}.hero-current .condition{margin-top:4px}}@media(max-width:620px){.hero{min-height:0;padding:20px 18px}.hero-content{grid-template-columns:1fr;gap:16px}.hero-brief{max-width:none;padding-bottom:13px;border-bottom:1px solid #ffffff35}.hero-brief p{font-size:13px}.hero-metrics{width:100%}.hero-metric{min-height:45px}.hero-current{grid-column:auto;grid-template-columns:1fr auto;padding-top:13px}.hero-current .temp-row{grid-column:2;grid-row:1/4}.hero-current .temperature{font-size:58px}.hero-current .weather-symbol{font-size:40px}}
        @media(max-width:900px){.hero-current-meta{grid-column:1;grid-row:1}.hero-current-weather{grid-column:2;grid-row:1/3}.hero-current .temp-row{grid-column:auto;grid-row:auto;margin:0}.hero-current .condition,.hero-current .feels{grid-column:auto}}
        @media(max-width:620px){.hero-current-meta{grid-column:1;grid-row:1}.hero-current-weather{grid-column:2;grid-row:1/3}.hero-current .temp-row{grid-column:auto;grid-row:auto}}
        .hero{min-height:200px;padding:19px 26px}.hero-content{grid-template-columns:minmax(0,1fr) minmax(280px,1.05fr) minmax(0,1fr);gap:25px}.hero-metrics{border-top:0;border-bottom:0}.hero-metric{min-height:43px;padding:5px 2px}.hero-brief{max-width:280px}.hero-brief p{font-size:13px;line-height:1.55}.hero-brief-note{margin-top:7px;font-size:11px}@media(max-width:900px){.hero{min-height:0;padding:19px 23px}.hero-content{grid-template-columns:1fr 1.1fr;gap:20px}.hero-brief{grid-column:1/-1;max-width:none;padding-top:11px;border-top:1px solid #ffffff35}.hero-current{padding-top:0;border-top:0}}@media(max-width:620px){.hero{padding:17px 18px}.hero-content{grid-template-columns:1fr;gap:13px}.hero-current{padding:0 0 12px;border-top:0;border-bottom:1px solid #ffffff35}.hero-brief{grid-column:auto;padding-top:10px}.hero-metric{min-height:41px}}
        .hero-current .weather-symbol{position:relative;top:-4px;line-height:1}
        .hero{min-height:300px;padding:20px 24px}.hero-content{grid-template-columns:minmax(0,3fr) minmax(0,2fr);align-items:stretch;gap:20px}.hero-today{display:grid;grid-template-columns:minmax(0,1fr) minmax(180px,.8fr);align-items:center;gap:18px;min-width:0}.hero-current{display:block;grid-column:auto;align-self:center;width:auto;padding:0;border:0}.hero-current-meta,.hero-current-weather{display:block;grid-column:auto;grid-row:auto}.hero-current-meta{min-width:0}.hero-current .temp-row{grid-column:auto;grid-row:auto;gap:11px;margin-top:10px}.hero-current .weather-symbol{font-size:44px}.hero-current .temperature{font-size:63px}.hero-metrics{width:100%;border:0}.hero-metric{min-height:42px;padding:4px 2px}.hero-hours{min-width:0;padding-left:17px;border-left:1px solid #ffffff38}.hero-hours-title{margin:0 0 8px;color:#fff;font-size:13px;font-weight:750}.forecast-table-wrap{max-height:258px;overflow:auto;scrollbar-width:thin;scrollbar-color:#ffffff66 transparent}.forecast-table{width:100%;border-collapse:collapse;color:#fff;font-size:10px}.forecast-table th{position:sticky;top:0;z-index:1;padding:7px 5px;background:#277db9;color:#e8f4ff;font-size:9px;font-weight:700;text-align:left;white-space:nowrap}.forecast-table td{padding:6px 5px;border-bottom:1px solid #ffffff30;white-space:nowrap}.forecast-table tr:last-child td{border-bottom:0}.forecast-hour-time,.forecast-hour-temp,.forecast-hour-rain{font-weight:700}.forecast-condition{display:flex;align-items:center;gap:5px;min-width:0}.forecast-condition-icon{font-size:14px;line-height:1}.forecast-condition>span:last-child{overflow:hidden;text-overflow:ellipsis}.forecast-hour-rain{color:#f4df8b;text-align:right}@media(max-width:900px){.hero{min-height:0}.hero-content{grid-template-columns:1fr;gap:16px}.hero-today{grid-template-columns:minmax(0,1fr) minmax(190px,.8fr)}.hero-current{display:block;grid-column:auto;padding:0;border:0}.hero-current .temp-row{margin-top:10px}.hero-hours{padding:13px 0 0;border-top:1px solid #ffffff38;border-left:0}.forecast-table-wrap{max-height:260px}}@media(max-width:620px){.hero{padding:17px 18px}.hero-today{grid-template-columns:1fr;gap:10px}.hero-metrics{border:0}.hero-current{display:block;padding-bottom:12px;border-bottom:1px solid #ffffff38}.hero-current .temp-row{margin:8px 0 0}.hero-hours{padding-top:11px}.forecast-table{font-size:9px}.forecast-table th{font-size:8px}.forecast-table td{padding:6px 3px}.forecast-condition{gap:3px}.forecast-condition-icon{font-size:12px}}
        .hero-current-meta{min-width:0}.hero-place{display:grid;gap:2px;min-width:0;line-height:1.35}.hero-place-main{display:block;max-width:100%;overflow:hidden;color:#fff;font-size:15px;font-weight:750;text-overflow:ellipsis;white-space:nowrap}.hero-place-context{display:block;max-width:100%;overflow:hidden;color:#dceeff;font-size:11px;font-weight:550;text-overflow:ellipsis;white-space:nowrap}.hero-current .hero-date{margin-top:5px}
        .current-layout{display:grid;grid-template-columns:minmax(0,3fr) minmax(0,2fr);align-items:start;gap:18px}.current-layout .hero{min-height:240px;height:100%;padding:20px 22px}.current-layout .hero-content{grid-template-columns:minmax(0,1fr) minmax(165px,.78fr);align-items:center;gap:14px}.current-layout .hero-today{grid-template-columns:minmax(0,1fr);gap:12px}.current-layout .hero-metrics{justify-self:start;max-width:270px}.current-layout .hero-metric{grid-template-columns:27px minmax(0,1fr) auto;gap:7px;min-height:41px;padding:4px 0}.current-layout .hero-metric-label{font-size:11px}.current-layout .hero-metric-value{font-size:14px}.forecast24-card{min-width:0;padding:17px 18px;border:1px solid #e2ebf4;border-radius:21px;background:#fff;color:var(--ink);box-shadow:var(--shadow)}.forecast24-title{margin:0 0 12px;color:var(--ink);font:800 15px/1.35 'Plus Jakarta Sans',sans-serif}.forecast24-card .forecast-table-wrap{max-height:660px;overflow:auto;scrollbar-width:thin;scrollbar-color:#b8cddd #f4f8fb}.forecast24-card .forecast-table{color:var(--ink);font-size:12px}.forecast24-card .forecast-table th{top:0;padding:8px 7px;background:#f3f8fc;color:#657b90;font-size:10px}.forecast24-card .forecast-table td{padding:7px;border-bottom:1px solid #edf1f5}.forecast24-card .forecast-condition-icon{font-size:16px}.forecast24-card .forecast-hour-rain{color:#327da9}.forecast24-card .forecast-table tr:last-child td{border-bottom:0}@media(max-width:900px){.current-layout{grid-template-columns:1fr;gap:14px}.current-layout .hero{min-height:0}.current-layout .hero-content{grid-template-columns:minmax(0,1fr) minmax(190px,.8fr)}.current-layout .hero-today{grid-template-columns:minmax(0,1fr);gap:10px}.current-layout .hero-metrics{max-width:none}.forecast24-card .forecast-table-wrap{max-height:660px}}@media(max-width:620px){.current-layout .hero{padding:17px 18px}.current-layout .hero-content{grid-template-columns:1fr;gap:10px}.current-layout .hero-today{gap:9px}.current-layout .hero-metrics{width:100%}.forecast24-card{padding:14px 12px;border-radius:17px}.forecast24-card .forecast-table{font-size:10px}.forecast24-card .forecast-table th{font-size:9px;padding:7px 4px}.forecast24-card .forecast-table td{padding:6px 4px}.forecast24-card .forecast-condition{gap:4px}}
        .current-layout .hero-content{grid-template-columns:minmax(0,1fr)}.current-layout .hero-today{grid-template-columns:minmax(0,1fr) minmax(160px,.85fr);gap:12px}.current-layout .hero-metrics{max-width:none}@media(max-width:620px){.current-layout .hero-today{grid-template-columns:1fr}}
        .forecast24-card{height:240px;display:flex;flex-direction:column}.forecast24-card .forecast-table-wrap{flex:1;min-height:0;max-height:none}.current-layout .hero-current{transform:translateY(12px)}.current-layout .hero-metrics{transform:translateY(18px)}.forecast24-card .forecast-table th:nth-child(1),.forecast24-card .forecast-table td:nth-child(1),.forecast24-card .forecast-table th:nth-child(3),.forecast24-card .forecast-table td:nth-child(3),.forecast24-card .forecast-table th:nth-child(4),.forecast24-card .forecast-table td:nth-child(4){text-align:center}.forecast24-card .forecast-table th:nth-child(2){text-align:center}.forecast24-card .forecast-hour-rain{text-align:center}.current-layout .hero-metric.quality-good .hero-metric-icon,.current-layout .hero-metric.quality-moderate .hero-metric-icon,.current-layout .hero-metric.quality-sensitive .hero-metric-icon,.current-layout .hero-metric.quality-unhealthy .hero-metric-icon,.current-layout .hero-metric.quality-hazardous .hero-metric-icon{background:#ffffff1f;color:#fff}
        .current-layout .hero{min-height:240px;height:240px;padding-top:16px;padding-bottom:16px}.current-layout .hero-metrics{width:100%;max-width:none;margin:0;align-self:center;transform:translate(-10px,12px)}.current-layout .hero-metric{grid-template-columns:32px minmax(0,1fr) auto;min-height:44px;gap:10px;padding:3px 0}.current-layout .hero-metric-icon{width:28px;height:28px}.current-layout .hero-metric-icon svg{width:17px;height:17px}.current-layout .hero-metric-label{font-size:13px}.current-layout .hero-metric-value{font-size:16px}.hero-current .feels{font-size:12px;margin-top:5px}
        .history-climate-layout{display:grid;grid-template-columns:minmax(0,2fr) minmax(0,3fr);align-items:stretch;gap:16px;margin-top:24px}.history-climate-layout.summary-only{grid-template-columns:1fr}.history-climate-layout.long-history{align-items:start}#detail-days.section{min-width:0;margin-top:0;display:flex;flex-direction:column}.history-table-panel{height:100%;min-height:100%;display:flex;flex:1;flex-direction:column;padding:0;overflow:hidden}.history-table-head{padding:18px 20px 14px}.history-table-scroll{flex:1;overflow-x:auto}.history-table{width:100%;border-collapse:collapse;table-layout:fixed;color:var(--ink);font-size:12px}.history-table th,.history-table td{padding:12px 10px;border-bottom:1px solid #eaf0f5}.history-table th{position:sticky;top:0;z-index:2;background:#f6f9fc;color:#63788e;font-size:11px;font-weight:750;text-align:center;white-space:nowrap}.history-table th:nth-child(1){width:21%}.history-table th:nth-child(2){width:35%}.history-table th:nth-child(3){width:19%}.history-table th:nth-child(4){width:25%}.history-table td{text-align:center}.history-table td:nth-child(2){text-align:left}.history-table tbody tr:last-child td{border-bottom:0}.history-weather{display:flex;align-items:center;gap:8px;white-space:nowrap}.history-weather-icon{font-size:19px;line-height:1}.history-weather-label{overflow:hidden;text-overflow:ellipsis}.history-table .empty{padding:24px;text-align:center}.climate-side-panel.section{height:100%;min-width:0;margin:0;padding:18px;display:flex;flex-direction:column}.history-climate-layout.long-history .climate-side-panel.section{height:auto}.climate-side-panel>.section-head{align-items:flex-start;gap:8px;margin-bottom:12px}.climate-side-panel .section-title{font-size:17px}.climate-side-panel .section-subtitle{font-size:11px;line-height:1.4}.climate-side-panel .map-level{flex:0 0 auto;font-size:9px}.climate-side-panel #climate-summary{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;flex:1}.climate-side-panel .climate-card{min-height:120px;padding:12px;border-radius:15px;gap:5px}.climate-side-panel .climate-card strong{font-size:16px;line-height:1.25}.climate-side-panel .climate-feature strong{font-size:14px}.climate-side-panel .climate-kicker{font-size:9px;letter-spacing:.5px}.climate-side-panel .climate-small{font-size:10px;line-height:1.4}@media(max-width:1000px){.history-climate-layout{grid-template-columns:1fr;gap:18px}#detail-days.section{display:block}.history-table-panel{height:auto;min-height:0}.climate-side-panel.section{height:auto}.climate-side-panel .climate-card{min-height:112px}}@media(max-width:640px){.history-climate-layout{margin-top:18px;gap:15px}.history-table{min-width:560px}.history-table th,.history-table td{padding:10px 8px}.history-table-head{padding:15px 15px 12px}.climate-side-panel.section{padding:15px}.climate-side-panel>.section-head{align-items:flex-start}.climate-side-panel .section-title{font-size:16px}}
        #weather-map .leaflet-tile-pane{filter:grayscale(1) brightness(1.08) contrast(.88)}.weather-map-popup{min-width:245px}.weather-map-popup .leaflet-popup-content-wrapper{border-radius:17px;box-shadow:0 12px 34px #183b5c28}.weather-map-popup .leaflet-popup-content{width:270px!important;margin:15px 16px}.map-popup{display:grid;gap:9px;color:#52677a;font:500 12px/1.45 'Plus Jakarta Sans',sans-serif}.map-popup-head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px}.map-popup-title{color:#173956;font-size:15px;font-weight:800;line-height:1.3}.map-popup-time{flex:0 0 auto;color:#71879a;font-size:10px;font-weight:600}.map-popup-current{display:flex;align-items:center;gap:10px;padding:10px 11px;border-radius:13px;background:#edf6fd}.map-popup-condition{display:flex;align-items:center;gap:7px;min-width:0;color:#244e71;font-weight:700}.map-popup-icon{font-size:27px;line-height:1}.map-popup-temp{margin-left:auto;white-space:nowrap;color:#175b98;font-size:24px;font-weight:800;letter-spacing:-.5px}.map-popup-details{display:grid;grid-template-columns:1fr 1fr;gap:7px}.map-popup-detail{display:grid;gap:2px;padding:9px 10px;border:1px solid #e8eef3;border-radius:11px;background:#fff}.map-popup-detail span{color:#74879a;font-size:10px}.map-popup-detail b{color:#1b3b5a;font-size:13px}.map-fullscreen-button,.map-up-button,.map-refresh-button{position:absolute;z-index:500;top:22px;display:flex;align-items:center;gap:7px;padding:9px 12px;border:1px solid #dce7ee;border-radius:11px;background:#fff;color:#244762;font-size:12px;font-weight:700;box-shadow:0 4px 14px #183b5c20}.map-fullscreen-button{right:22px}.map-refresh-button{right:155px}.map-up-button{left:22px}.map-up-button[hidden]{display:none}.map-fullscreen-button:hover,.map-up-button:hover,.map-refresh-button:hover{background:#f3f8fb}.map-fullscreen-button svg,.map-refresh-button svg{width:16px;height:16px}.map-refresh-button:disabled{opacity:.65;cursor:wait}.map-panel:fullscreen{display:flex;flex-direction:column;gap:0;padding:14px;border:0;border-radius:0;background:#f7fafc}.map-panel:fullscreen #weather-map{height:auto;min-height:0;flex:1;border-radius:12px}.map-panel:fullscreen .map-note{flex:0 0 auto}.map-panel:fullscreen .map-fullscreen-button,.map-panel:fullscreen .map-up-button,.map-panel:fullscreen .map-refresh-button{top:24px}
        .history-only[hidden]{display:none!important}
        #map-section{display:none!important}.shell.map-page{display:flex;height:100dvh;min-height:0;max-width:none;flex-direction:column;overflow:hidden;padding:0}.shell.map-page .topbar{flex:0 0 auto;padding:0 24px}.shell.map-page .page{display:flex;flex:1;min-height:0;height:auto;padding:10px 16px 14px}.shell.map-page .page>:not(#map-section){display:none!important}.shell.map-page #map-section{display:flex!important;flex:1;min-height:0;flex-direction:column;margin:0}.shell.map-page #map-section>.section-head{flex:0 0 auto;margin-bottom:8px}.shell.map-page .map-panel{display:flex;flex:1;min-height:0;flex-direction:column;padding:10px}.shell.map-page #weather-map{height:auto;min-height:0;flex:1}.shell.map-page .footer{display:none}.map-popup-title-button{padding:0;border:0;background:transparent;color:#173956;font:800 15px/1.3 'Plus Jakarta Sans',sans-serif;text-align:left;cursor:pointer}.map-popup-title-button:hover{color:#2476bd;text-decoration:underline}@media(max-width:640px){.shell.map-page .topbar{height:auto;min-height:68px;padding:6px 12px;gap:8px}.shell.map-page .page{padding:8px}.shell.map-page #map-section>.section-head{align-items:center}.shell.map-page #map-section .section-title{font-size:16px}.shell.map-page .map-panel{padding:6px}.shell.map-page #weather-map{min-height:0}}
        .map-body{display:grid;grid-template-columns:218px minmax(0,1fr);gap:12px;height:410px;min-height:0}.map-region-sidebar{min-width:0;overflow:auto;padding:12px;border:1px solid #e3ebf1;border-radius:15px;background:#f8fafc}.map-region-sidebar h3{margin:1px 0 9px;color:#38536b;font-size:12px;font-weight:800}.map-region-list{display:grid;gap:5px}.map-region-option{width:100%;display:flex;align-items:center;justify-content:space-between;gap:8px;padding:9px 10px;border:1px solid transparent;border-radius:10px;background:transparent;color:#48647b;font:600 11px 'Plus Jakarta Sans',sans-serif;text-align:left;cursor:pointer}.map-region-option:hover{background:#eef5fa}.map-region-option[aria-current="true"]{border-color:#b8d5e9;background:#eaf5fc;color:#18578a;font-weight:800}.map-region-option span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.map-region-option small{flex:0 0 auto;color:#8294a4;font-size:9px;font-weight:600}.map-canvas{position:relative;min-width:0;min-height:0;overflow:hidden;border-radius:15px}.map-canvas #weather-map{height:100%;min-height:0}.map-canvas .map-up-button{top:22px;left:12px}.map-actions{position:absolute;z-index:500;top:22px;right:22px;display:flex;align-items:center;gap:10px}.map-actions .map-refresh-button,.map-actions .map-fullscreen-button{position:static;white-space:nowrap}.map-canvas .leaflet-control-zoom{margin:12px}.map-panel:fullscreen .map-body{flex:1;height:auto;min-height:0}.map-panel:fullscreen .map-canvas{flex:1}.map-panel:fullscreen .map-canvas #weather-map{height:100%;min-height:0}.map-panel:fullscreen .map-canvas .map-actions,.map-panel:fullscreen .map-canvas .map-up-button{top:24px}.shell.map-page .map-body{flex:1;height:auto;min-height:0}.shell.map-page .map-canvas{height:100%}.shell.map-page .map-canvas #weather-map{height:100%;min-height:0;flex:initial}@media(max-width:640px){.map-body{grid-template-columns:minmax(0,1fr);grid-template-rows:auto minmax(260px,1fr);height:470px;gap:8px}.map-region-sidebar{max-height:104px;padding:8px 9px}.map-region-sidebar h3{margin:0 0 5px;font-size:10px}.map-region-list{display:flex;gap:5px;overflow-x:auto;padding-bottom:2px}.map-region-option{width:auto;min-width:max-content;padding:7px 9px;font-size:10px}.map-canvas{min-height:260px}.map-canvas .map-up-button{top:12px;left:12px}.map-actions{top:12px;right:12px;gap:5px}.map-actions .map-refresh-button,.map-actions .map-fullscreen-button{padding:7px;font-size:0}.map-actions svg{width:17px;height:17px}.map-canvas .leaflet-control-zoom{margin:10px}.map-panel:fullscreen .map-body,.shell.map-page .map-body{grid-template-rows:auto minmax(0,1fr);height:auto}.shell.map-page .map-region-sidebar{max-height:92px}}
    </style>
    <style>
        body{background:radial-gradient(ellipse at 8% 0%,#f8fbff 0%,#edf4fa 44%,#e7f0f7 100%)}
        .topbar{border-bottom-color:#d5e3ef}
        .panel,.metric-card,.day-card,.hour-panel,.region-forecast,.history-table-panel,.climate-side-panel,.news-card{box-shadow:0 12px 30px rgba(38,79,112,.075)}
        .metric-card{background:linear-gradient(150deg,#fff 0%,#f8fbff 100%)}
        .day-card{background:linear-gradient(160deg,#fff 0%,#f5faff 100%)}
        .day-card.today{background:linear-gradient(150deg,#fffdf5 0%,#fff7da 100%)}
        .hour-panel,.history-table-panel{background:linear-gradient(150deg,#fff 0%,#f8fbff 100%)}
        .climate-side-panel{background:linear-gradient(150deg,#f6fbff 0%,#eef6fb 54%,#fff8e5 100%)}
        .climate-summary-editorial .climate-card{background:linear-gradient(150deg,#fff 0%,#f9fcff 100%)}
        .climate-summary-editorial .climate-card-rain{background:linear-gradient(145deg,#fff 0%,#eef7ff 100%)}
        .climate-summary-editorial .climate-card-dry{background:linear-gradient(145deg,#fff 0%,#fff9e7 100%)}
        .news-card{background:linear-gradient(150deg,#fff 0%,#f8fbff 100%)}
        .news-grid{display:block;overflow:hidden;border:1px solid #dce7f0;border-radius:18px;background:linear-gradient(155deg,#fff,#f9fcff);box-shadow:0 12px 30px rgba(38,79,112,.075)}
        .news-card{position:relative;display:grid;grid-template-columns:minmax(0,1fr);gap:0;min-height:0;padding:15px 20px 15px 24px;border:0;border-bottom:1px solid #e7eef4;border-radius:0;background:transparent;box-shadow:none;transition:background .16s ease}
        .news-card:last-child{border-bottom:0}
        .news-card:not(.news-empty):before{position:absolute;top:0;bottom:0;left:0;width:3px;background:#d8eaf7;content:"";transition:background .16s ease}
        .news-card:hover{border-color:#e7eef4;background:#f3f9fe;transform:none}
        .news-card:hover:before{background:#f0c541}
        .news-card-meta{justify-content:flex-start;gap:10px;font-size:10px}
        .news-card-source{padding:3px 7px;border-radius:7px;background:#eaf4fb;color:#2676ad}
        .news-card-title{margin:6px 0 4px;font-size:14px;line-height:1.45}
        .news-card-summary{max-width:1000px;font-size:11px;-webkit-line-clamp:2}
        .news-card-tags{gap:5px;margin:0;padding-top:8px}
        .news-empty{display:flex;min-height:96px;align-items:center;justify-content:center;padding:18px;border:0;color:var(--muted);font-size:12px;text-align:center}
        @media(max-width:640px){.news-card{padding:13px 13px 13px 18px}.news-card-title{font-size:13px}.news-card-summary{-webkit-line-clamp:3}.news-card-meta{flex-wrap:wrap;gap:6px}}
        .history-climate-layout.long-history{align-items:stretch}.history-climate-layout.long-history #detail-days.section,.history-climate-layout.long-history .climate-side-panel.section{height:400px}.history-climate-layout.long-history .history-table-panel{height:100%;min-height:0}.history-climate-layout.long-history .history-table-scroll{min-height:0;overflow:auto;scrollbar-width:thin;scrollbar-color:#b8cddd #f4f8fb}
        .climate-summary-editorial{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:9px!important}.climate-summary-editorial .climate-card{min-width:0;min-height:0;padding:13px 12px;background:#fff}.climate-card-heading{display:flex;align-items:center;gap:7px;min-width:0}.climate-card-icon{width:25px;height:25px;flex:0 0 25px;display:grid;place-items:center;border-radius:8px;background:#edf6fd;font-size:14px}.climate-card-rain .climate-card-icon{background:#e9f5ff}.climate-card-temperature .climate-card-icon{background:#fff2e8}.climate-card-humidity .climate-card-icon{background:#e9f8f8}.climate-card-dry .climate-card-icon{background:#fff7d9}.climate-summary-editorial .climate-kicker{font-size:9px;line-height:1.25;letter-spacing:.45px}.climate-summary-editorial .climate-card strong{margin-top:2px;font-size:18px;line-height:1.2}.climate-summary-editorial .climate-small{font-size:10px;line-height:1.4}
        .climate-chart-card{overflow:hidden;border-color:#dce8f2;background:linear-gradient(155deg,#fff 0%,#fbfdff 100%)}.climate-chart-head{align-items:flex-start}.climate-chart-heading{display:grid;gap:5px}.climate-chart-heading strong{font-size:19px!important;line-height:1.3;letter-spacing:-.35px}.climate-chart-subtitle{max-width:620px;color:#70849a;font-size:12px;line-height:1.55}.climate-chart-context{display:inline-flex;align-items:center;gap:6px;margin-top:2px;color:#6e8296;font-size:10px}.climate-chart-context:before{width:7px;height:7px;border-radius:50%;background:#3c91c2;content:""}.climate-chart-controls{display:flex;align-items:end;justify-content:flex-end;gap:10px;margin:3px 0 10px}.climate-chart-controls label{display:grid;gap:4px;color:#6f8298;font-size:10px;font-weight:700}.climate-chart-controls select{min-width:148px;padding:7px 30px 7px 10px;border:1px solid #dce7f0;border-radius:10px;background:#fff;color:#244762;font:600 11px 'Plus Jakarta Sans',sans-serif}.climate-chart-controls select:focus{outline:2px solid #9ec9e8;outline-offset:1px}.climate-legend>span:first-child{display:flex;align-items:center;gap:6px}.climate-line{fill:none;stroke:#3188bd;stroke-width:3;stroke-linecap:round;stroke-linejoin:round}.climate-point{fill:#fff;stroke:#3188bd;stroke-width:2.5;transition:r .12s}.climate-point:hover{r:6}.climate-empty-label{fill:#6f8298;font:600 13px 'Plus Jakarta Sans',sans-serif}.climate-chart-card[hidden]{display:none!important}
        @media(max-width:640px){.history-climate-layout.long-history #detail-days.section,.history-climate-layout.long-history .climate-side-panel.section{height:404px}.climate-chart-controls{justify-content:stretch;gap:7px}.climate-chart-controls label{flex:1;min-width:0}.climate-chart-controls select{width:100%;min-width:0;padding-right:20px;font-size:10px}}
        .weather-reading-wrap{position:relative;z-index:1;display:inline-flex;align-items:center;justify-content:flex-end;gap:6px;white-space:nowrap}.weather-reading-wrap>span:first-child{font:inherit;color:inherit}.weather-reading-wrap.weather-reading-open{z-index:1002}.weather-indicator{display:inline-flex;align-items:center;justify-content:center;width:18px;min-height:22px;padding:2px 0;border:0;background:transparent;color:inherit;font:inherit;cursor:pointer}.weather-indicator[aria-expanded="true"]{position:relative;z-index:1002}.weather-indicator:focus-visible{outline:2px solid #8bc8ed;outline-offset:3px;border-radius:10px}.weather-color-dot{width:12px;height:12px;flex:0 0 12px;border:1px solid #ffffff85;border-radius:50%;box-shadow:inset 0 1px 2px #ffffff70,0 1px 3px #173b5c30}.aqi-tone-good{background:#32c783}.aqi-tone-moderate{background:#f3c640}.aqi-tone-sensitive{background:#ff814b}.aqi-tone-unhealthy{background:#e64b5f}.aqi-tone-very-unhealthy{background:#8652c8}.aqi-tone-hazardous{background:#79545b}.visibility-tone-excellent{background:#32c783}.visibility-tone-good{background:#32c783}.visibility-tone-moderate{background:#f3c640}.visibility-tone-poor{background:#ff814b}.visibility-tone-danger{background:#e64b5f}.weather-category{position:absolute;right:-6px;bottom:calc(100% + 7px);z-index:1003;width:max-content;max-width:min(230px,72vw);padding:7px 10px;border:1px solid #dce8f1;border-radius:10px;background:#fff;color:#25445e;box-shadow:0 7px 22px #173b5c2e;font-size:11px;font-weight:700;line-height:1.35;text-align:center;white-space:normal;overflow-wrap:anywhere}.weather-category:after{content:"";position:absolute;right:9px;top:100%;width:8px;height:8px;border-right:1px solid #dce8f1;border-bottom:1px solid #dce8f1;background:#fff;transform:translateY(-50%) rotate(45deg)}.weather-category[hidden]{display:none!important}.weather-category-aqi{width:min(340px,82vw);padding:12px 14px;border-radius:13px;text-align:left;white-space:normal}.weather-category-aqi strong{display:block;margin-bottom:5px;color:#1d3d59;font-size:12px}.weather-category-aqi span{display:block;color:#526a7e;font-size:11px;font-weight:500;line-height:1.5}.hero-metric-value{display:inline-flex;align-items:center;justify-content:flex-end;gap:6px}.map-popup-aqi{display:flex;align-items:center;justify-content:flex-end;gap:5px;min-width:0}.map-popup-reading .weather-reading-wrap{font:inherit;color:inherit}.map-popup-reading .weather-color-dot{width:12px;height:12px;flex-basis:12px}@media(max-width:640px){.weather-color-dot{width:11px;height:11px;flex-basis:11px}}
        .news-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.news-card{display:flex;min-width:0;min-height:205px;flex-direction:column;padding:17px 18px;border:1px solid var(--line);border-radius:18px;background:#fff;box-shadow:var(--shadow)}.news-card-meta{display:flex;align-items:center;justify-content:space-between;gap:8px;color:#74879a;font-size:10px;font-weight:650}.news-card-source{color:#2879b5;font-weight:800}.news-card-title{margin:12px 0 7px;color:var(--ink);font-size:14px;font-weight:800;line-height:1.45}.news-card-summary{display:-webkit-box;overflow:hidden;color:#6f8298;font-size:11px;line-height:1.55;-webkit-box-orient:vertical;-webkit-line-clamp:4}.news-card-tags{display:flex;flex-wrap:wrap;gap:5px;margin-top:auto;padding-top:12px}.news-card-tag{padding:4px 7px;border-radius:7px;background:#edf6fd;color:#3b789f;font-size:9px;font-weight:750}.news-card:hover{border-color:#bfd9eb;transform:translateY(-1px)}.news-empty{grid-column:1/-1;min-height:90px;justify-content:center;color:var(--muted);font-size:12px;text-align:center}.news-attribution{margin:9px 3px 0;color:#8493a1;font-size:10px;line-height:1.5}@media(max-width:900px){.news-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:640px){.news-grid{grid-template-columns:1fr;gap:9px}.news-card{min-height:0;padding:14px}.news-card-title{font-size:13px}.news-card-summary{-webkit-line-clamp:3}.news-section .section-head{align-items:flex-start}}
        .advice-card{align-items:flex-start;gap:13px;padding:16px;border-radius:17px;box-shadow:0 5px 18px #25476408}.advice-emoji{width:38px;height:38px;flex:0 0 38px;background:#eef6fc;color:#2878ad}.advice-emoji svg{width:21px;height:21px}.advice-content{flex:1;min-width:0}.advice-heading{display:flex;flex-direction:column;align-items:flex-start;gap:6px}.advice-title{color:var(--ink);font-size:14px;font-weight:800;line-height:1.35}.status{margin:0;padding:4px 9px;border:1px solid transparent;border-radius:999px;font-size:10px;font-weight:800;letter-spacing:.2px;text-transform:none}.status:before{width:6px;height:6px;border-radius:50%;background:currentColor;content:""}.status.baik{border-color:#ccebdc;background:#effaf4;color:#218154}.status.waspada{border-color:#f2dfb6;background:#fff8e8;color:#96670d}.status.saran{border-color:#d8e7f2;background:#f1f7fb;color:#426c89}.status.informasi{border-color:#dce5ee;background:#f5f8fb;color:#63788e}.advice-message{margin:11px 0 0;color:#435c72;font-size:12px;line-height:1.6}.advice-reason{margin-top:11px;padding-top:9px;border-top:1px solid #edf1f5}.advice-reason-label{display:block;margin-bottom:6px;color:#8293a3;font-size:9px;font-weight:800;letter-spacing:.55px;text-transform:uppercase}.advice-reason-list{display:flex;flex-wrap:wrap;gap:5px}.advice-reason-chip{padding:4px 7px;border-radius:7px;background:#f4f7fa;color:#61768a;font-size:10px;line-height:1.35}.advice-empty{grid-column:1/-1;justify-content:center}@media(max-width:640px){.advice-card{gap:10px;padding:13px}.advice-emoji{width:34px;height:34px;flex-basis:34px}.advice-title{font-size:13px}.advice-message{font-size:11px}.advice-reason-chip{font-size:9px}}
    </style>
</head>
<body>
<div class="shell">
    <header class="topbar">
        <a class="brand" href="/" aria-label="Cerah, beranda">
            <img class="brand-logo" src="{{ asset('images/cerah-logo.png') }}" alt="Logo Cerah">
            <span><strong class="brand-name">CERAH</strong><small class="brand-tagline">Cuaca Elok Rutin Antar Hari</small></span>
        </a>
        <nav class="main-nav" aria-label="Navigasi utama">
            <a class="nav-link active" href="#beranda" id="nav-home" aria-current="page">Beranda</a>
            <a class="nav-link" href="#map-section" id="nav-map">Peta</a>
            <a class="nav-link" href="#climate-insights" id="nav-climate">Iklim</a>
            <a class="nav-link" href="#news-section" id="nav-news">Berita</a>
            <div class="location-control">
                <button class="location-toggle nav-link" id="location-toggle" type="button" aria-expanded="false" aria-controls="location-menu">
                    <span>Wilayah</span><span class="nav-location-current"><small id="location-level">Kota</small><strong id="location-name">Tanjung Pinang</strong></span>
                    <svg class="location-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="location-menu" id="location-menu" hidden>
                    <div class="location-menu-head"><div><small>JELAJAHI WILAYAH</small><strong id="location-menu-title">Pilih wilayah</strong></div></div>
                    <div class="location-options" id="location-options"></div>
                    <div class="geo-status" id="geo-status" role="status" aria-live="polite" hidden></div>
                    <button class="geo-button" id="use-location" type="button">⌖ Gunakan lokasi saya</button>
                </div>
            </div>
        </nav>
        <div class="top-actions"><span class="live-pill"><i class="live-dot"></i><span id="live-label">Memuat data terkini</span></span><button class="icon-btn" id="refresh-button" type="button" aria-label="Perbarui data cuaca" title="Perbarui data cuaca"><svg viewBox="0 0 24 24" fill="none"><path d="M20 11a8 8 0 0 0-14.9-3M4 4v4h4M4 13a8 8 0 0 0 14.9 3M20 20v-4h-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></button></div>
    </header>
    <main class="page">
        <div class="intro" id="beranda">
            <div><p class="eyebrow">PRAKIRAAN CUACA WILAYAH KEPULAUAN RIAU</p><h1>Pantau Cuaca Lokal</h1><p>Kenali kondisi langit, pantau perubahan cuaca, dan siapkan aktivitasmu dengan lebih percaya diri.</p></div>
        </div>
        <div class="error-banner" id="error-banner" role="status"></div>
        <section class="section detail-only" id="current-hero-wrap" data-anchor="prakiraan">
            <div class="current-layout"><div class="hero" aria-live="polite"><div class="hero-content" id="hero-content"><div class="loading">Mengambil prakiraan cuaca…</div></div></div><aside class="forecast24-card"><h2 class="forecast24-title">Prakiraan 24 Jam Mendatang</h2><div class="forecast-table-wrap"><table class="forecast-table"><thead><tr><th>Jam</th><th>Kondisi Cuaca</th><th>Suhu</th><th>Peluang Hujan</th></tr></thead><tbody id="hours-list"><tr><td colspan="4">Memuat prakiraan per jam…</td></tr></tbody></table></div></aside></div>
        </section>
        <section class="section" id="map-section">
            <div class="section-head"><div><h2 class="section-title">Peta wilayah Kepulauan Riau</h2><p class="section-subtitle">Pilih kabupaten/kota di sisi kiri, lalu telusuri kecamatan hingga kelurahan/desa.</p></div><span class="map-level" id="map-level">Kabupaten/Kota · Kepulauan Riau</span></div>
            <div class="map-panel" id="map-panel">
                <div class="map-body">
                    <aside class="map-region-sidebar" aria-label="Pilih kabupaten atau kota"><h3>Kabupaten / Kota</h3><div class="map-region-list" id="map-region-list"><span class="map-region-loading">Memuat daftar wilayah…</span></div></aside>
                    <div class="map-canvas">
                        <button class="map-up-button" id="map-up" type="button" hidden>← Kembali</button>
                        <div class="map-actions">
                            <button class="map-refresh-button" id="map-boundary-refresh" type="button" title="Minta ulang batas wilayah dari BIG"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 7v5h-5M4 17v-5h5"/><path d="M5.6 9a7 7 0 0 1 11.6-2L20 12M4 12l2.8 5a7 7 0 0 0 11.6-2"/></svg><span>Muat ulang batas</span></button>
                            <button class="map-fullscreen-button" id="map-fullscreen" type="button" aria-label="Tampilkan peta layar penuh" title="Tampilkan peta layar penuh"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3H5a2 2 0 0 0-2 2v3m13-5h3a2 2 0 0 1 2 2v3M3 16v3a2 2 0 0 0 2 2h3m13-5v3a2 2 0 0 1-2 2h-3"/></svg><span>Layar penuh</span></button>
                        </div>
                        <div id="weather-map" role="application" aria-label="Peta interaktif cuaca Provinsi Kepulauan Riau"><div class="map-fallback">Memuat peta wilayah…</div></div>
                    </div>
                </div>
                <div class="map-legend" id="map-legend" hidden></div><div class="map-note"><span id="map-note-text">Pilih batas kecamatan untuk membuka kelurahan/desa.</span><span>© OpenStreetMap · Batas wilayah BIG</span></div>
            </div>
        </section>
        <div class="climate-chart-card">
            <div class="climate-chart-head"><div class="climate-chart-heading"><strong id="climate-chart-title">Riwayat Iklim Harian</strong><span class="climate-chart-subtitle" id="climate-chart-subtitle">Amati perubahan cuaca dari hari ke hari dan sesuaikan rentang waktu untuk membandingkan polanya.</span><span id="climate-source" class="climate-chart-context"></span></div></div>
            <div class="climate-chart-controls">
                <label>Visualisasi<select id="climate-metric"><option value="rain">Jumlah curah hujan</option><option value="temperature">Suhu rata-rata</option><option value="aqi">Kualitas udara (US AQI)</option><option value="humidity">Kelembapan</option></select></label>
                <label>Rentang waktu<select id="climate-range"><option value="7">7 hari</option><option value="30" selected>30 hari</option><option value="90">90 hari</option><option value="365">1 tahun</option></select></label>
            </div>
            <div class="climate-chart-wrap" id="climate-chart-wrap"><svg class="climate-chart" id="climate-chart" viewBox="0 0 960 270" role="img" aria-label="Grafik riwayat cuaca"></svg></div>
            <div class="climate-legend"><span><i class="rain-dot" id="climate-chart-dot"></i><span id="climate-chart-legend">Curah hujan harian (mm)</span></span><span><i class="missing-dot"></i>Data tidak tersedia</span><span id="climate-chart-range"></span></div>
        </div>
        <p class="climate-disclaimer" id="climate-disclaimer">Data historis berbasis reanalisis model, bukan pengukuran langsung di tiap kelurahan. Ini bukan prakiraan musim.</p>
        <div class="history-climate-layout">
            <section class="section climate-side-panel" id="climate-insights">
                <div class="section-head"><div><h2 class="section-title">Catatan Iklim Wilayah</h2></div><span class="map-level" id="climate-coverage">Memuat data</span></div>
                <div class="climate-summary climate-summary-editorial" id="climate-summary">
                    <article class="climate-card climate-card-rain"><div class="climate-card-heading"><span class="climate-card-icon">🌧️</span><span class="climate-kicker">TOTAL CURAH HUJAN · 30 HARI</span></div><strong id="climate-rain">—</strong><span id="climate-baseline" class="climate-small">Dibandingkan dengan pola historis</span></article>
                    <article class="climate-card climate-card-temperature"><div class="climate-card-heading"><span class="climate-card-icon">🌡️</span><span class="climate-kicker">SUHU RATA-RATA</span></div><strong id="climate-temperature">—</strong><span id="climate-temperature-note" class="climate-small">30 hari historis terakhir</span></article>
                    <article class="climate-card climate-card-humidity"><div class="climate-card-heading"><span class="climate-card-icon">💧</span><span class="climate-kicker">KELEMBAPAN RATA-RATA</span></div><strong id="climate-humidity">—</strong><span id="climate-humidity-note" class="climate-small">30 hari historis terakhir</span></article>
                    <article class="climate-card climate-card-dry"><div class="climate-card-heading"><span class="climate-card-icon">☀️</span><span class="climate-kicker">HARI TANPA HUJAN</span></div><strong id="climate-dry-days">—</strong><span id="climate-dry-note" class="climate-small"></span></article>
                </div>
            </section>
            <section class="section history-only" id="detail-days" data-anchor="prakiraan" hidden>
                <div class="panel history-table-panel"><div class="history-table-head"><div><h2 class="section-title" id="history-title">Riwayat Cuaca 7 Hari Terakhir</h2></div></div><div class="history-table-scroll"><table class="history-table"><thead><tr><th>Tgl</th><th>Cuaca</th><th>Suhu (°C)</th><th>Curah Hujan (mm)</th></tr></thead><tbody id="days-grid"><tr><td colspan="4" class="empty">Menyiapkan riwayat cuaca…</td></tr></tbody></table></div></div>
            </section>
        </div>
        <section class="section detail-only" id="activity-advice">
            <div class="section-head"><div><h2 class="section-title">Saran aktivitas</h2><p class="section-subtitle">Saran praktis berdasarkan kondisi cuaca terkini</p></div></div>
            <div class="content-grid">
                <div class="advice-list" id="advice-list"><div class="advice-card"><div class="advice-message">Menyiapkan saran aktivitas…</div></div></div>
            </div>
        </section>
        <section class="section news-section" id="news-section">
            <div class="section-head"><div><h2 class="section-title">Berita Cuaca & Lingkungan Kepri</h2></div><span class="map-level">Sumber: ANTARA Kepri</span></div>
            <div class="news-grid" id="news-grid" aria-live="polite"><article class="news-card news-empty">Memuat berita terkait…</article></div>
            <p class="news-attribution">Judul, ringkasan, dan waktu terbit berasal dari ANTARA Kepri. Buka tautan untuk membaca artikel lengkap di situs sumber.</p>
        </section>
    </main>
    <footer class="footer"><span>Cuaca diperbarui berkala · Waktu lokal WIB (Asia/Jakarta)</span><span class="attribution"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3v18M3 12h18M5.6 5.6l12.8 12.8m0-12.8L5.6 18.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>Data cuaca dari <a href="https://open-meteo.com/" target="_blank" rel="noopener">Open-Meteo</a></span></footer>
</div>
<script>
(() => {
    const regionsUrl = @json($regionsEndpoint);
    const mapWeatherUrl = @json(url('/api/weather/map'));
    const mapBoundaryUrl = @json(url('/api/weather/boundaries'));
    const refreshUrl = @json(url('/api/weather/refresh'));
    const newsUrl = @json(url('/api/news'));
    const provinceCode = '21';
    const cityCode = '21.72';
    const locationToggle = document.getElementById('location-toggle');
    const locationMenu = document.getElementById('location-menu');
    const errorBanner = document.getElementById('error-banner');
    let regions = [];
    let selectedCode = provinceCode;
    let weatherRequestToken = 0;
    let weatherRequestController = null;
    let climateRequestController = null;
    const weatherResponseCache = new Map();
    const climateResponseCache = new Map();
    let weatherMap = null;
    let mapBoundaries = null;
    let kepriMapBounds = null;
    let boundaryRenderToken = 0;
    const boundaryCache = new Map();
    const boundaryRequests = new Map();
    let boundaryPrefetchTimer = null;
    let popupCloseTimer = null;
    let mapPath = [{level:'municipalities', code:provinceCode, name:'Kepulauan Riau'}];
    let mapWeatherData = [];
    let mapLayer = 'temperature';

    const icons = {sun:'☀️', moon:'🌙', partly:'🌤️', clouds:'☁️', fog:'🌫️', drizzle:'🌦️', rain:'🌧️', storm:'⛈️', snow:'🌨️'};
    function localIsDay(isDay, at) {
        if (isDay !== null && isDay !== undefined) return Number(isDay) === 1;
        const hour = Number(new Intl.DateTimeFormat('en-GB', {timeZone:'Asia/Jakarta', hour:'2-digit', hourCycle:'h23'}).format(dateParts(at)));
        return hour >= 6 && hour < 18;
    }
    function condition(code, isDay, at) {
        const n = Number(code);
        const day = localIsDay(isDay, at ?? new Date());
        if ([0].includes(n)) return {label:day ? 'Cerah' : 'Cerah Malam', icon:day ? icons.sun : icons.moon};
        if ([1,2].includes(n)) return {label:n === 1 ? (day ? 'Cerah Berawan' : 'Cerah Berawan Malam') : 'Cerah Berawan', icon:day ? icons.partly : (n === 1 ? icons.moon : icons.clouds)};
        if (n === 3) return {label:'Berawan', icon:icons.clouds};
        if ([45,48].includes(n)) return {label:'Berkabut', icon:icons.fog};
        if ([51,53,55,56,57].includes(n)) return {label:'Gerimis', icon:icons.drizzle};
        if ([61,63,65,66,67].includes(n)) return {label:'Hujan', icon:icons.rain};
        if ([71,73,75,77,85,86].includes(n)) return {label:'Hujan Salju', icon:icons.snow};
        if ([80,81,82].includes(n)) return {label:'Hujan Lokal', icon:icons.rain};
        if ([95,96,99].includes(n)) return {label:'Hujan Petir', icon:icons.storm};
        return {label:'Kondisi Cuaca', icon:icons.partly};
    }
    function number(value, digits=0) {
        if (value === null || value === undefined || value === '') return '—';
        return new Intl.NumberFormat('id-ID', {maximumFractionDigits:digits, minimumFractionDigits:digits}).format(Number(value));
    }
    function dateParts(value) { return new Date(value); }
    function compass(degrees) {
        if (degrees === null || degrees === undefined) return '—';
        return ['Utara','Timur Laut','Timur','Tenggara','Selatan','Barat Daya','Barat','Barat Laut'][Math.round(Number(degrees) / 45) % 8];
    }
    function localDay(date) { return new Intl.DateTimeFormat('id-ID',{timeZone:'Asia/Jakarta',weekday:'short'}).format(date); }
    function localHour(date) { return new Intl.DateTimeFormat('id-ID',{timeZone:'Asia/Jakarta',hour:'2-digit',minute:'2-digit'}).format(date); }
    function safeText(element, value) { element.textContent = value ?? ''; }
    function currentRegion() { return regions.find(region => region.code === selectedCode); }
    function typeLabel(type) {
        return ({province:'Provinsi',regency:'Kabupaten',city:'Kota',district:'Kecamatan',village:'Kelurahan/Desa'})[type] ?? 'Wilayah';
    }
    function ancestors(region) {
        const chain = [];
        let item = region;
        while (item) {
            chain.unshift(item);
            item = item.parent_code ? regions.find(candidate => candidate.code === item.parent_code) : null;
        }
        return chain;
    }
    function closeLocationMenu() {
        locationMenu.hidden = true;
        locationToggle.setAttribute('aria-expanded', 'false');
    }
    function renderLocationPicker() {
        const region = currentRegion();
        if (!region) return;
        document.getElementById('location-level').textContent = typeLabel(region.type);
        document.getElementById('location-name').textContent = ['city','regency'].includes(region.type) ? region.name.replace(/^(Kota|Kabupaten)\s+/i, '') : region.name;
        const chain = ancestors(region);
        document.getElementById('location-menu-title').textContent = 'Provinsi, kabupaten/kota, kecamatan, dan kelurahan';
        const options = document.getElementById('location-options');
        options.replaceChildren();
        const province = regions.find(item => item.code === provinceCode);
        if (province) appendRegionOption(options, province, chain);
        renderWeatherMap();
    }
    function appendRegionOption(container, region, selectedChain) {
        const children = regions.filter(item => item.parent_code === region.code);
        const row = document.createElement('div');
        row.className = `region-tree-row level-${region.type}`;
        const label = document.createElement('button');
        label.type = 'button'; label.className = 'region-tree-select';
        label.setAttribute('aria-current', region.code === selectedCode ? 'true' : 'false');
        const name = document.createElement('span'); name.textContent = region.name;
        const kind = document.createElement('small'); kind.textContent = typeLabel(region.type);
        label.append(name, kind); label.addEventListener('click', () => selectRegion(region.code));
        row.append(label);
        if (children.length) {
            const toggle = document.createElement('button');
            toggle.type = 'button'; toggle.className = 'region-tree-toggle';
            toggle.textContent = '⌄'; toggle.setAttribute('aria-label', `Tampilkan ${typeLabel(children[0].type)} di ${region.name}`);
            toggle.setAttribute('aria-expanded', String(children.some(child => selectedChain.some(item => item.code === child.code))));
            row.append(toggle);
            const nested = document.createElement('div'); nested.className = 'region-tree-children';
            nested.hidden = !children.some(child => selectedChain.some(item => item.code === child.code));
            toggle.addEventListener('click', () => {
                nested.hidden = !nested.hidden;
                toggle.setAttribute('aria-expanded', String(!nested.hidden));
            });
            children.forEach(child => appendRegionOption(nested, child, selectedChain));
            container.append(row, nested);
        } else container.append(row);
    }
    function initWeatherMap() {
        const container = document.getElementById('weather-map');
        const fullscreenButton = document.getElementById('map-fullscreen');
        const mapPanel = document.getElementById('map-panel');
        document.getElementById('map-up').addEventListener('click', () => {
            if (mapPath.length <= 1) return;
            mapPath.pop();
            renderMapRegionList();
            showMapLevel(mapPath[mapPath.length - 1]);
        });
        document.getElementById('map-boundary-refresh').addEventListener('click', async event => {
            const button = event.currentTarget;
            button.disabled = true;
            document.getElementById('map-note-text').textContent = 'Meminta ulang batas wilayah dari BIG…';
            const current = mapPath[mapPath.length - 1];
            boundaryCache.delete(`${current.level}:${current.code}`);
            try {
                await showMapLevel(current, true, true);
            } finally {
                button.disabled = false;
            }
        });
        fullscreenButton.addEventListener('click', async () => {
            try {
                if (document.fullscreenElement === mapPanel) await document.exitFullscreen();
                else await mapPanel.requestFullscreen();
            } catch (error) {
                document.getElementById('map-note-text').textContent = 'Mode layar penuh tidak didukung oleh browser ini.';
            }
        });
        document.addEventListener('fullscreenchange', () => {
            const active = document.fullscreenElement === mapPanel;
            fullscreenButton.querySelector('span').textContent = active ? 'Keluar layar penuh' : 'Layar penuh';
            fullscreenButton.setAttribute('aria-label', active ? 'Keluar dari layar penuh' : 'Tampilkan peta layar penuh');
            fullscreenButton.title = active ? 'Keluar dari layar penuh' : 'Tampilkan peta layar penuh';
            window.setTimeout(() => weatherMap?.invalidateSize(), 120);
        });
        if (!window.L) {
            container.innerHTML = '<div class="map-fallback">Peta belum dapat dimuat. Periksa koneksi internet; pilihan wilayah tetap tersedia lewat menu Wilayah.</div>';
            document.getElementById('map-note-text').textContent = 'Peta memerlukan koneksi internet untuk memuat tampilan peta.';
            return;
        }
        kepriMapBounds = L.latLngBounds([[-1.5,103.0],[5.0,109.6]]);
        weatherMap = L.map(container, {
            scrollWheelZoom:false,
            zoomControl:false,
            maxBounds:kepriMapBounds,
            maxBoundsViscosity:.9,
            minZoom:5,
            maxZoom:18,
        });
        weatherMap.fitBounds(kepriMapBounds, {padding:[18,18], maxZoom:8});
        weatherMap.on('tileerror', () => {
            const fallback = container.querySelector('.map-tile-fallback');
            if (fallback) return;
            const message = document.createElement('div');
            message.className = 'map-tile-fallback';
            message.textContent = 'Latar peta tidak dapat dimuat. Periksa koneksi internet.';
            container.append(message);
        });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>'
        }).addTo(weatherMap);
        L.control.zoom({position:'bottomleft'}).addTo(weatherMap);
        mapBoundaries = L.layerGroup().addTo(weatherMap);
        renderWeatherMap();
        loadMapWeather();
    }
    async function loadMapWeather() {
        try {
            const response = await fetch(mapWeatherUrl, {headers:{Accept:'application/json'},cache:'no-store'});
            if (!response.ok) throw new Error('Data peta belum tersedia.');
            mapWeatherData = await response.json();
            renderWeatherMap();
        } catch (error) {
            document.getElementById('map-note-text').textContent = 'Data cuaca belum dapat dimuat. Batas wilayah tetap dapat dijelajahi.';
        }
    }
    function mapMarkerColor(data) {
        if (mapLayer === 'temperature') return data.temperature_c == null ? '#8293a3' : Number(data.temperature_c) < 27 ? '#3486bd' : Number(data.temperature_c) < 31 ? '#40a87c' : '#e69635';
        if (mapLayer === 'rain') return data.precipitation_probability == null ? '#8293a3' : Number(data.precipitation_probability) >= 70 ? '#cf5d52' : Number(data.precipitation_probability) >= 35 ? '#e2a43a' : '#55a7bb';
        if (mapLayer === 'wind') return data.wind_speed_kmh == null ? '#8293a3' : Number(data.wind_speed_kmh) >= 30 ? '#c45456' : Number(data.wind_speed_kmh) >= 15 ? '#d3983a' : '#4189b8';
        return data.pm2_5_ug_m3 == null ? '#8293a3' : Number(data.pm2_5_ug_m3) > 55.4 ? '#dc8050' : Number(data.pm2_5_ug_m3) > 15.5 ? '#dea13e' : '#39a476';
    }
    function updateMapLegend() {
        const legends = {
            temperature:['Suhu',['< 27°C','27–30,9°C','≥ 31°C'],['#3486bd','#40a87c','#e69635']],
            rain:['Peluang hujan',['< 35%','35–69%','≥ 70%'],['#55a7bb','#e2a43a','#cf5d52']],
            wind:['Kecepatan angin',['< 15 km/jam','15–29 km/jam','≥ 30 km/jam'],['#4189b8','#d3983a','#c45456']],
            air:['PM2.5 (µg/m³)',['0–15,5','15,6–55,4','> 55,4'],['#39a476','#dea13e','#dc8050']],
        };
        const [label,steps,colors] = legends[mapLayer];
        document.getElementById('map-legend').innerHTML = `<strong>${label}</strong>${steps.map((step,index)=>`<span><i class="legend-swatch" style="background:${colors[index]}"></i>${step}</span>`).join('')}<span><i class="legend-swatch" style="background:#8293a3"></i>Belum tersedia</span>`;
        document.querySelectorAll('[data-map-layer]').forEach(button => button.classList.toggle('active', button.dataset.mapLayer === mapLayer));
    }
    async function getBoundaryFeatures(level, code, forceRefresh = false) {
        const key = `${level}:${code}`;
        if (!forceRefresh && boundaryCache.has(key)) return boundaryCache.get(key);
        if (!forceRefresh && boundaryRequests.has(key)) return boundaryRequests.get(key);
        const request = (async () => {
            let lastError;
            for (let attempt = 0; attempt < 3; attempt++) {
                try {
                    const refreshParam = forceRefresh ? '?refresh=1' : '';
                    const response = await fetch(`${mapBoundaryUrl}/${level}/${encodeURIComponent(code)}${refreshParam}`, {headers:{Accept:'application/geo+json, application/json'}, cache:'no-store'});
                    if (!response.ok) {
                        let detail = '';
                        try { detail = (await response.json()).message ?? ''; } catch (error) {}
                        throw new Error(detail || 'Batas administrasi belum dapat dimuat.');
                    }
                    const data = await response.json();
                    if (data.type !== 'FeatureCollection' || !Array.isArray(data.features) || data.features.length === 0) throw new Error('Data batas wilayah kosong.');
                    boundaryCache.set(key, data);
                    return data;
                } catch (error) {
                    lastError = error;
                    if (attempt < 2) await new Promise(resolve => setTimeout(resolve, 250 * (attempt + 1)));
                }
            }
            throw lastError ?? new Error('Batas administrasi belum dapat dimuat.');
        })();
        boundaryRequests.set(key, request);
        try {
            return await request;
        } finally {
            boundaryRequests.delete(key);
        }
    }
    function featureProperty(feature, name) {
        const props = feature.properties ?? {};
        const key = Object.keys(props).find(candidate => candidate.toLowerCase() === name.toLowerCase());
        return key ? props[key] : null;
    }
    function normalizedAdminCode(value) {
        if (value == null || value === '') return '';
        const raw = String(value).trim();
        if (raw.includes('.')) return raw;
        const digits = raw.replace(/\D/g, '');
        if (digits.length === 2) return digits;
        if (digits.length === 4) return `${digits.slice(0,2)}.${digits.slice(2,4)}`;
        if (digits.length === 6) return `${digits.slice(0,2)}.${digits.slice(2,4)}.${digits.slice(4,6)}`;
        if (digits.length >= 10) return `${digits.slice(0,2)}.${digits.slice(2,4)}.${digits.slice(4,6)}.${digits.slice(6,10)}`;
        return digits;
    }
    function mapFeatureRegion(feature, level) {
        const props = feature.properties ?? {};
        const fields = level === 'municipalities' ? ['kdpkab','kdbbps'] : level === 'districts' ? ['kdcpum','kdcbps'] : ['kdepum','kdebps'];
        const nameFields = level === 'municipalities' ? ['wadmkk','namobj'] : level === 'districts' ? ['wadmkc','namobj'] : ['wadmkd','namobj'];
        const code = fields.map(field => normalizedAdminCode(featureProperty(feature, field))).find(Boolean) ?? '';
        const name = nameFields.map(field => featureProperty(feature, field)).find(Boolean) ?? 'Wilayah';
        const type = level === 'municipalities' ? 'Kabupaten/Kota' : level === 'districts' ? 'Kecamatan' : 'Kelurahan/Desa';
        return {code, name:String(name), type};
    }
    function mapAreaColor(region) {
        const key = region.code || region.name;
        let hash = 0;
        for (let i = 0; i < key.length; i++) hash = (hash * 31 + key.charCodeAt(i)) | 0;
        return `hsl(${Math.abs(hash) % 360} 43% 79%)`;
    }
    function mapWeatherForBoundary(region) {
        const normalized = region.code.replace(/\D/g, '');
        return mapWeatherData.find(item => item.code.replace(/\D/g, '') === normalized) ?? null;
    }
    function mapWeatherPopup(region, values) {
        const name = `<button type="button" class="map-popup-title-button" data-map-region-code="${escapeHtml(region.code)}">${escapeHtml(region.name)}</button>`;
        if (!values) return `<div class="map-popup"><div class="map-popup-head">${name}<span class="map-popup-time">${escapeHtml(region.type)}</span></div><p class="map-popup-unavailable">Data cuaca wilayah ini belum tersedia.</p></div>`;
        const info = condition(values.weather_code, values.is_day, values.valid_at);
        const observedAt = values.valid_at ? new Intl.DateTimeFormat('id-ID', {hour:'2-digit', minute:'2-digit', timeZone:'Asia/Jakarta'}).format(new Date(values.valid_at)) : '—';
        return `<div class="map-popup"><div class="map-popup-head">${name}<span class="map-popup-time">${observedAt} WIB</span></div><div class="map-popup-current"><span class="map-popup-condition"><span class="map-popup-icon">${info.icon}</span>${escapeHtml(info.label)}</span><strong class="map-popup-temp">${values.temperature_c == null ? '—' : `${number(values.temperature_c,1)}°C`}</strong></div><div class="map-popup-details"><div class="map-popup-detail"><span>Kelembapan</span><b>${values.relative_humidity == null ? '—' : `${number(values.relative_humidity)}%`}</b></div><div class="map-popup-detail"><span>Peluang hujan</span><b>${values.precipitation_probability == null ? '—' : `${number(values.precipitation_probability)}%`}</b></div><div class="map-popup-detail"><span>Jarak Pandang</span><b class="map-popup-reading">${visibilityReading(values.visibility_m)}</b></div><div class="map-popup-detail"><span>Kualitas Udara (AQI)</span><b class="map-popup-reading">${aqiReading(values.us_aqi)}</b></div></div></div>`;
    }
    function renderMapRegionList() {
        const list = document.getElementById('map-region-list');
        if (!list) return;
        const municipalities = regions.filter(region => region.parent_code === provinceCode && ['city','regency'].includes(region.type));
        list.replaceChildren();
        if (!municipalities.length) {
            const empty = document.createElement('span');
            empty.className = 'map-region-loading';
            empty.textContent = 'Kabupaten/kota belum tersedia.';
            list.append(empty);
            return;
        }
        const activeCode = mapPath.find(item => item.level === 'districts')?.code;
        municipalities.forEach(region => {
            const option = document.createElement('button');
            option.type = 'button';
            option.className = 'map-region-option';
            option.setAttribute('aria-current', String(region.code === activeCode));
            const name = document.createElement('span');
            name.textContent = mapMunicipalityLabel(region.name);
            const kind = document.createElement('small');
            kind.textContent = typeLabel(region.type);
            option.append(name, kind);
            option.addEventListener('click', () => {
                mapPath = [
                    {level:'municipalities', code:provinceCode, name:'Kepulauan Riau'},
                    {level:'districts', code:region.code, name:region.name},
                ];
                renderMapRegionList();
                showMapLevel(mapPath[1], true);
                window.setTimeout(() => weatherMap?.invalidateSize(), 100);
            });
            list.append(option);
        });
    }
    function mapMunicipalityLabel(name) {
        const clean = String(name ?? '').replace(/^(Kota|Kabupaten)\s+/i, '').trim();
        if (/^Kepulauan Anambas$/i.test(clean)) return 'Kep. Anambas';
        if (/^Tanjung Pinang$/i.test(clean)) return 'Tanjungpinang';
        return clean;
    }
    async function showMapLevel(node, fit = true, forceRefresh = false) {
        if (!weatherMap || !mapBoundaries) return;
        const renderToken = ++boundaryRenderToken;
        const button = document.getElementById('map-up');
        button.hidden = mapPath.length <= 1;
        const levelLabel = node.level === 'municipalities' ? 'Kabupaten/Kota' : node.level === 'districts' ? 'Kecamatan' : 'Kelurahan/Desa';
        document.getElementById('map-level').textContent = `${levelLabel} · ${mapMunicipalityLabel(node.name)}`;
        document.getElementById('map-note-text').textContent = `Memuat batas ${levelLabel.toLocaleLowerCase('id')} ${node.name}…`;
        try {
            const data = await getBoundaryFeatures(node.level, node.code, forceRefresh);
            if (renderToken !== boundaryRenderToken) return;
            const polygonFeatures = data.features.filter(feature => ['Polygon','MultiPolygon'].includes(feature.geometry?.type));
            if (!polygonFeatures.length) throw new Error('Data dari BIG tidak berisi geometri polygon.');
            mapBoundaries.clearLayers();
            const layer = L.geoJSON({...data, features:polygonFeatures}, {
            style: feature => {
                const region = mapFeatureRegion(feature, node.level);
                const selected = mapPath.length > 1 && region.code === mapPath[mapPath.length - 1].code;
                return {color:selected ? '#174b69' : '#5b707d', weight:selected ? 3 : 1.6, opacity:.95, fillColor:mapAreaColor(region), fillOpacity:.42};
            },
            onEachFeature: (feature, polygon) => {
                const region = mapFeatureRegion(feature, node.level);
                if (node.level === 'villages') {
                    const values = mapWeatherForBoundary(region);
                    polygon.bindPopup(mapWeatherPopup(region, values), {className:'weather-map-popup', maxWidth:300, closeButton:false, closeOnClick:false});
                    polygon.on('popupopen', event => {
                        const popupElement = event.popup.getElement();
                        popupElement?.addEventListener('mouseenter', () => window.clearTimeout(popupCloseTimer));
                        popupElement?.addEventListener('mouseleave', () => polygon.closePopup());
                    });
                }
                polygon.on('mouseover', () => {
                    window.clearTimeout(popupCloseTimer);
                    polygon.setStyle({weight:2.8, fillOpacity:.62});
                    if (node.level === 'villages') polygon.openPopup();
                    if (node.level === 'districts') {
                        window.clearTimeout(boundaryPrefetchTimer);
                        boundaryPrefetchTimer = window.setTimeout(() => {
                            if (region.code) getBoundaryFeatures('villages', region.code).catch(() => {});
                        }, 220);
                    }
                });
                polygon.on('mouseout', () => {
                    polygon.setStyle({weight:1.6, fillOpacity:.42});
                    if (node.level === 'villages') {
                        popupCloseTimer = window.setTimeout(() => {
                            const popupElement = polygon.getPopup()?.getElement();
                            if (!popupElement?.matches(':hover')) polygon.closePopup();
                        }, 100);
                    }
                });
                polygon.on('click', () => {
                    if (node.level === 'municipalities' && region.code) {
                        const level = 'districts';
                        mapPath.push({level, code:region.code, name:region.name});
                        renderMapRegionList();
                        showMapLevel(mapPath[mapPath.length - 1]);
                        return;
                    }
                    if (node.level === 'districts' && region.code) {
                        const level = 'villages';
                        mapPath.push({level, code:region.code, name:region.name});
                        showMapLevel(mapPath[mapPath.length - 1]);
                        return;
                    }
                    if (node.level === 'villages') polygon.openPopup();
                });
            }
            }).addTo(mapBoundaries);
            const bounds = layer.getBounds();
            if (!bounds.isValid()) throw new Error('Polygon batas diterima, tetapi koordinatnya tidak bisa dibaca peta.');
            if (fit && bounds.isValid()) weatherMap.fitBounds(bounds, {padding:[30,30], maxZoom:node.level === 'municipalities' ? 8 : node.level === 'districts' ? 12 : 14});
            const nextAction = node.level === 'municipalities' ? 'memilih kabupaten/kota' : node.level === 'districts' ? 'melihat kelurahannya' : 'melihat cuacanya';
            const boundaryLabel = node.level === 'municipalities' ? 'kabupaten/kota' : node.level === 'districts' ? 'kecamatan' : 'kelurahan/desa';
            document.getElementById('map-note-text').textContent = `${layer.getLayers().length} batas ${boundaryLabel} dimuat. Klik wilayah untuk ${nextAction}.`;
        } catch (error) {
            if (node.level === 'municipalities') weatherMap.fitBounds(kepriMapBounds, {padding:[18,18], maxZoom:8});
            if (renderToken === boundaryRenderToken) document.getElementById('map-note-text').textContent = `${error.message || 'Batas wilayah belum dapat dimuat.'} Tekan “Muat ulang batas” untuk mencoba lagi.`;
        }
    }
    function renderWeatherMap() {
        renderMapRegionList();
        if (!weatherMap || !mapBoundaries) return;
        updateMapLegend();
        showMapLevel(mapPath[mapPath.length - 1], true);
    }
    function isVillage(region) { return region?.type === 'village'; }
    function setViewMode(region) {
        const detail = isVillage(region);
        document.querySelectorAll('.detail-only').forEach(element => { element.hidden = !detail; });
        const showHistory = ['district', 'village'].includes(region.type);
        document.getElementById('detail-days').hidden = !showHistory;
        const historyLayout = document.querySelector('.history-climate-layout');
        historyLayout?.classList.toggle('summary-only', !showHistory);
        historyLayout?.classList.toggle('long-history', detail);
        document.getElementById('history-title').textContent = detail ? 'Riwayat Cuaca 30 Hari Terakhir' : 'Riwayat Cuaca 7 Hari Terakhir';
        if (detail) {
            const district = regions.find(item => item.code === region.parent_code && item.type === 'district');
            const municipality = district && regions.find(item => item.code === district.parent_code && ['city', 'regency'].includes(item.type));
            const mapNode = mapPath[mapPath.length - 1];
            if (district && municipality && (mapNode?.level !== 'villages' || mapNode?.code !== district.code)) {
                mapPath = [
                    {level:'municipalities', code:provinceCode, name:'Kepulauan Riau'},
                    {level:'districts', code:municipality.code, name:municipality.name},
                    {level:'villages', code:district.code, name:district.name},
                ];
                renderMapRegionList();
                showMapLevel(mapPath[mapPath.length - 1], true);
            }
        }
    }
    function selectRegion(code) {
        selectedCode = code;
        renderLocationPicker();
        closeLocationMenu();
        loadWeather();
    }

    function distanceKm(lat1, lon1, lat2, lon2) {
        const radians = value => value * Math.PI / 180;
        const dLat = radians(lat2 - lat1);
        const dLon = radians(lon2 - lon1);
        const a = Math.sin(dLat / 2) ** 2 + Math.cos(radians(lat1)) * Math.cos(radians(lat2)) * Math.sin(dLon / 2) ** 2;
        return 6371 * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function pointInRing(latitude, longitude, ring) {
        let inside = false;
        for (let i = 0, j = ring.length - 1; i < ring.length; j = i++) {
            const [xi, yi] = ring[i];
            const [xj, yj] = ring[j];
            const crosses = ((yi > latitude) !== (yj > latitude))
                && longitude < ((xj - xi) * (latitude - yi)) / (yj - yi) + xi;
            if (crosses) inside = !inside;
        }
        return inside;
    }

    function pointInGeometry(latitude, longitude, geometry) {
        if (!geometry) return false;
        const inPolygon = polygon => Array.isArray(polygon?.[0])
            && pointInRing(latitude, longitude, polygon[0])
            && !polygon.slice(1).some(hole => pointInRing(latitude, longitude, hole));
        if (geometry.type === 'Polygon') return inPolygon(geometry.coordinates);
        if (geometry.type === 'MultiPolygon') return geometry.coordinates.some(inPolygon);
        return false;
    }

    function regionByBoundaryFeature(feature, type, parentCode = null) {
        const mapped = mapFeatureRegion(feature, type === 'district' ? 'districts' : 'villages');
        const mappedCode = mapped.code.replace(/\D/g, '');
        const candidates = regions.filter(region => region.type === type && (!parentCode || region.parent_code === parentCode));
        return candidates.find(region => mappedCode && region.code.replace(/\D/g, '') === mappedCode)
            ?? candidates.find(region => region.name.trim().toLocaleLowerCase('id') === mapped.name.trim().toLocaleLowerCase('id'))
            ?? null;
    }

    async function findVillageByBoundaries(latitude, longitude) {
        const municipalities = regions
            .filter(region => ['city', 'regency'].includes(region.type))
            .map(region => ({region, distance:distanceKm(latitude, longitude, Number(region.latitude), Number(region.longitude))}))
            .sort((a, b) => a.distance - b.distance);

        for (const {region:municipality} of municipalities) {
            let districtFeatures;
            try {
                const data = await getBoundaryFeatures('districts', municipality.code);
                districtFeatures = data.features.filter(feature => pointInGeometry(latitude, longitude, feature.geometry));
            } catch (error) {
                continue;
            }
            if (!districtFeatures.length) continue;

            const district = districtFeatures
                .map(feature => ({feature, region:regionByBoundaryFeature(feature, 'district', municipality.code)}))
                .find(item => item.region);
            if (!district) continue;

            try {
                const villageData = await getBoundaryFeatures('villages', district.region.code);
                const matches = villageData.features
                    .filter(feature => pointInGeometry(latitude, longitude, feature.geometry))
                    .map(feature => regionByBoundaryFeature(feature, 'village', district.region.code))
                    .filter(Boolean);
                if (matches.length) {
                    return matches.sort((a, b) => distanceKm(latitude, longitude, Number(a.latitude), Number(a.longitude))
                        - distanceKm(latitude, longitude, Number(b.latitude), Number(b.longitude)))[0];
                }
            } catch (error) {
                // Continue to the nearest-centroid fallback if BIG is unavailable.
            }
        }

        return null;
    }

    function geoStatus(message) {
        const status = document.getElementById('geo-status');
        status.textContent = message;
        status.hidden = !message;
    }

    function locateUser() {
        const button = document.getElementById('use-location');
        button.disabled = true;
        button.textContent = 'Mencari lokasi…';
        geoStatus('Meminta izin lokasi untuk menentukan kelurahan terdekat.');

        if (!navigator.geolocation) {
            geoStatus('Browser ini tidak mendukung lokasi otomatis. Pilih kelurahan secara manual.');
            button.disabled = false;
            button.textContent = '⌖ Gunakan lokasi saya';
            return Promise.resolve(false);
        }

        return new Promise(resolve => navigator.geolocation.getCurrentPosition(async position => {
            const {latitude, longitude} = position.coords;
            const province = regions.find(region => region.code === provinceCode);
            const villages = regions.filter(region => region.type === 'village');
            const nearest = villages
                .map(region => ({region, distance:distanceKm(latitude, longitude, Number(region.latitude), Number(region.longitude))}))
                .sort((a, b) => a.distance - b.distance)[0];
            const coords = villages.map(region => ({lat:Number(region.latitude),lon:Number(region.longitude)}));
            const insideKepriExtent = coords.length > 0
                && latitude >= Math.min(...coords.map(point => point.lat)) - .25
                && latitude <= Math.max(...coords.map(point => point.lat)) + .25
                && longitude >= Math.min(...coords.map(point => point.lon)) - .25
                && longitude <= Math.max(...coords.map(point => point.lon)) + .25;

            if (!province || !nearest || !insideKepriExtent || nearest.distance > 100) {
                selectedCode = provinceCode;
                renderLocationPicker();
                geoStatus('Lokasimu di luar cakupan Kepulauan Riau. Prakiraan provinsi ditampilkan.');
                button.disabled = false;
                button.textContent = '⌖ Gunakan lokasi saya';
                loadWeather();
                resolve(false);
                return;
            }

            let locationMatch = null;
            try {
                locationMatch = await findVillageByBoundaries(latitude, longitude);
            } catch (error) {
                // Use the nearest coordinate when administrative boundaries cannot be reached.
            }
            const selectedVillage = locationMatch ?? nearest.region;
            selectedCode = selectedVillage.code;
            renderLocationPicker();
            const accuracy = Number(position.coords.accuracy);
            geoStatus(locationMatch
                ? (accuracy > 1500 ? `Lokasi ditemukan di ${selectedVillage.name}; akurasi perangkat sekitar ±${number(accuracy)} m.` : '')
                : `Batas kelurahan belum dapat dicocokkan. Menampilkan wilayah terdekat dari titik koordinat: ${selectedVillage.name}.`);
            button.disabled = false;
            button.textContent = '⌖ Perbarui lokasi saya';
            loadWeather();
            resolve(true);
        }, error => {
            const message = error.code === 1
                ? 'Izin lokasi belum diberikan. Kamu tetap bisa memilih kelurahan secara manual.'
                : error.code === 2
                    ? 'Lokasi perangkat belum tersedia. Coba lagi atau pilih kelurahan secara manual.'
                    : 'Pencarian lokasi terlalu lama. Coba lagi atau pilih kelurahan secara manual.';
            geoStatus(message);
            button.disabled = false;
            button.textContent = '⌖ Gunakan lokasi saya';
            loadWeather();
            resolve(false);
        }, {enableHighAccuracy:true, timeout:20000, maximumAge:30000}));
    }

    function renderHero(data) {
        const region = data.wilayah;
        const current = data.cuaca_saat_ini;
        const info = condition(current?.weather_code, current?.is_day, current?.valid_at);
        const timestamp = current?.valid_at ? new Date(current.valid_at) : null;
        const placeParts = ancestors(region).filter(item => item.type !== 'province').reverse();
        const placeNames = (placeParts.length ? placeParts : [region]).map(item => item.name.replace(/^(Kota|Kabupaten)\s+/i, ''));
        const place = placeNames[0] ?? region.name;
        const placeContext = placeNames.slice(1).join(' · ');
        const usAqi = current?.us_aqi == null ? null : Number(current.us_aqi);
        const airClass = classifyUsAqi(usAqi);
        const rainChance = current?.precipitation_probability == null ? null : Number(current.precipitation_probability);
        document.getElementById('hero-content').innerHTML = `
            <div class="hero-today"><div class="hero-current"><div class="hero-current-meta"><div class="hero-place"><span class="hero-place-main">${escapeHtml(place)}</span>${placeContext ? `<span class="hero-place-context">${escapeHtml(placeContext)}</span>` : ''}</div><div class="hero-date">${timestamp ? new Intl.DateTimeFormat('id-ID',{weekday:'long',day:'numeric',month:'long',timeZone:'Asia/Jakarta'}).format(timestamp) : 'Data cuaca terbaru'}</div></div><div class="hero-current-weather"><div class="temp-row"><span class="weather-symbol">${info.icon}</span><div class="temperature">${number(current?.temperature_c)}<sup>°</sup></div></div><div class="condition">${info.label}</div><div class="feels">Terasa seperti ${number(current?.apparent_temperature_c)}°C</div></div></div>
            <div class="hero-metrics">${heroMetric('Kelembapan','humidity',`${number(current?.relative_humidity)}%`)}${heroMetric('Curah Hujan','rain',`${number(current?.rain_mm,1)} mm`)}${heroMetric('Jarak Pandang','visibility',visibilityReading(current?.visibility_m))}${heroMetric('Kualitas Udara (US AQI)','air',aqiReading(usAqi),`quality-${airClass.tone}`)}</div></div>`;
        const freshness = data.ditarik_pada ? new Date(data.ditarik_pada) : (data.diperbarui_pada ? new Date(data.diperbarui_pada) : null);
        document.getElementById('live-label').textContent = freshness ? `Diperbarui ${localHour(freshness)}` : 'Menunggu data cuaca';
    }
    function classifyUsAqi(value) {
        if (value == null || !Number.isFinite(value)) return {label:'Belum tersedia', tone:'unknown'};
        if (value <= 50) return {label:'Baik', tone:'good'};
        if (value <= 100) return {label:'Sedang', tone:'moderate'};
        if (value <= 150) return {label:'Tidak Sehat untuk Kelompok Sensitif', tone:'sensitive'};
        if (value <= 200) return {label:'Tidak Sehat', tone:'unhealthy'};
        if (value <= 300) return {label:'Sangat Tidak Sehat', tone:'very-unhealthy'};
        return {label:'Berbahaya', tone:'hazardous'};
    }
    function describeUsAqi(value) {
        if (value <= 50) return 'Kualitas udara sangat baik. Tidak ada risiko kesehatan bagi siapa pun. Aman untuk aktivitas luar ruangan.';
        if (value <= 100) return 'Kualitas udara dapat diterima. Namun, orang yang sangat sensitif terhadap polusi udara (seperti penderita asma) mungkin mengalami gejala pernapasan.';
        if (value <= 150) return 'Kelompok sensitif (anak-anak, lansia, ibu hamil, dan penderita penyakit jantung atau paru) dapat merasakan dampak kesehatan. Masyarakat umum biasanya belum terdampak.';
        if (value <= 200) return 'Semua orang mulai merasakan dampak kesehatan. Anggota kelompok sensitif dapat mengalami dampak yang lebih serius. Kurangi aktivitas fisik berat di luar rumah.';
        if (value <= 300) return 'Peringatan kesehatan darurat. Seluruh populasi sangat mungkin terdampak. Hindari aktivitas di luar ruangan dan gunakan masker seperti N95.';
        return 'Kondisi darurat tingkat tinggi. Kualitas udara memicu tingkat bahaya serius bagi kesehatan semua orang. Semua aktivitas luar ruangan harus dihentikan.';
    }
    function weatherCategoryReading(displayValue, category, tone, description) {
        const aqi = description === 'US AQI';
        const numericValue = Number(String(displayValue).replace(/[^0-9,.-]/g, '').replace(',', '.'));
        const popup = aqi
            ? `<span class="weather-category weather-category-aqi" hidden><strong>${escapeHtml(category)}</strong><span>${escapeHtml(describeUsAqi(numericValue))}</span></span>`
            : `<span class="weather-category" hidden>${escapeHtml(category)}</span>`;
        return `<span class="weather-reading-wrap"><span>${displayValue}</span><button type="button" class="weather-indicator" data-weather-category-trigger aria-expanded="false" aria-label="Tampilkan kategori ${escapeHtml(description)}: ${escapeHtml(category)}" title="Tekan untuk melihat kategori"><i class="weather-color-dot ${tone}" aria-hidden="true"></i>${popup}</button></span>`;
    }
    function aqiReading(value, suffix='') {
        if (value == null || !Number.isFinite(Number(value))) return '—';
        const numericValue = Number(value);
        const category = classifyUsAqi(numericValue);
        const unit = suffix ? ` ${escapeHtml(suffix)}` : '';
        return weatherCategoryReading(`${number(numericValue)}${unit}`, category.label, `aqi-tone-${category.tone}`, 'US AQI');
    }
    function classifyVisibility(distanceKm) {
        if (distanceKm == null || !Number.isFinite(Number(distanceKm))) return null;
        const distance = Number(distanceKm);
        if (distance > 10) return {label:'Sangat Baik', tone:'visibility-tone-excellent'};
        if (distance >= 5) return {label:'Baik / Normal', tone:'visibility-tone-good'};
        if (distance >= 2) return {label:'Sedang / Udara Kabur', tone:'visibility-tone-moderate'};
        if (distance >= 1) return {label:'Buruk / Rendah', tone:'visibility-tone-poor'};
        return {label:'Sangat Buruk / Berbahaya', tone:'visibility-tone-danger'};
    }
    function visibilityReading(valueMeters) {
        if (valueMeters == null || !Number.isFinite(Number(valueMeters))) return '—';
        const distanceKm = Number(valueMeters) / 1000;
        const category = classifyVisibility(distanceKm);
        return weatherCategoryReading(`${number(distanceKm,1)} km`, category.label, category.tone, 'jarak pandang');
    }
    function weatherIllustration(code, isDay=1) {
        const n = Number(code);
        const sun = [0,1,2].includes(n) && Number(isDay) === 1 ? '<circle class="weather-sun" cx="77" cy="28" r="15" fill="#ffd669"/><path class="sun-rays" d="M77 5v-5M77 56v-5M54 28h-6M106 28h-6M61 12l-4-4M97 48l-4-4M93 12l4-4M57 48l4-4" stroke="#ffe49a" stroke-width="4" stroke-linecap="round"/>' : '';
        const moon = [0,1,2].includes(n) && Number(isDay) === 0 ? '<path d="M82 12a16 16 0 1 0 16 22A14 14 0 0 1 82 12Z" fill="#ffe39a"/>' : '';
        const rain = [51,53,55,61,63,65,80,81,82,95,96,99].includes(n);
        const storm = [95,96,99].includes(n);
        const cloud = '<g class="weather-cloud" fill="#fff"><path d="M27 70c-13 0-21-8-21-19 0-10 8-18 19-19 3-13 14-21 28-21 15 0 27 10 30 24 12-2 23 7 23 19 0 9-8 16-18 16H27Z"/></g>';
        const drops = rain ? '<g class="weather-drops" stroke="#bfe8ff" stroke-width="5" stroke-linecap="round"><path d="m35 75-5 11"/><path d="m55 78-5 11"/><path d="m75 75-5 11"/></g>' : '';
        const lightning = storm ? '<path class="weather-lightning" d="m85 70-10 15h9l-5 12 16-19h-10l6-8Z" fill="#ffd24e"/>' : '';
        return `<svg class="weather-illustration ${rain ? 'is-rainy' : ''}" viewBox="0 0 112 100" aria-hidden="true">${sun}${moon}${cloud}${drops}${lightning}</svg>`;
    }
    function heroMetric(label, icon, value, extraClass='') { return `<div class="hero-metric ${extraClass}"><span class="hero-metric-icon">${metricIcon(icon)}</span><span class="hero-metric-label">${label}</span><strong class="hero-metric-value">${value}</strong></div>`; }
    function escapeHtml(value) { const node = document.createElement('span'); node.textContent = value ?? ''; return node.innerHTML; }

    function renderNews(articles) {
        const grid = document.getElementById('news-grid');
        if (!Array.isArray(articles) || articles.length === 0) {
            grid.innerHTML = '<article class="news-card news-empty">Belum ada artikel yang cocok dengan topik cuaca dan lingkungan. Berita akan muncul setelah RSS diperbarui.</article>';
            return;
        }
        grid.innerHTML = articles.map(article => {
            const date = article.published_at
                ? new Intl.DateTimeFormat('id-ID',{timeZone:'Asia/Jakarta',day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}).format(dateParts(article.published_at))
                : '';
            let url = '#';
            try {
                const parsed = new URL(article.url);
                if (parsed.protocol === 'https:') url = parsed.href;
            } catch (error) {}
            const tags = (Array.isArray(article.keywords) ? article.keywords : []).slice(0,3)
                .map(keyword => `<span class="news-card-tag">${escapeHtml(keyword)}</span>`).join('');
            return `<a class="news-card" href="${escapeHtml(url)}" target="_blank" rel="noopener noreferrer"><span class="news-card-meta"><span class="news-card-source">${escapeHtml(article.source)}</span><time>${escapeHtml(date)}</time></span><strong class="news-card-title">${escapeHtml(article.title)}</strong><span class="news-card-summary">${escapeHtml(article.summary || 'Buka artikel di situs sumber untuk membaca informasi selengkapnya.')}</span><span class="news-card-tags">${tags}</span></a>`;
        }).join('');
    }

    async function loadNews() {
        try {
            const response = await fetch(newsUrl, {headers:{Accept:'application/json'},cache:'no-store'});
            if (!response.ok) throw new Error('Berita belum dapat dimuat.');
            const data = await response.json();
            renderNews(data.articles);
        } catch (error) {
            document.getElementById('news-grid').innerHTML = '<article class="news-card news-empty">Berita sementara tidak dapat dimuat. Silakan coba lagi nanti.</article>';
        }
    }

    function renderMetrics(current) {
        const air = classifyUsAqi(current?.us_aqi == null ? null : Number(current.us_aqi));
        const metrics = [
            ['Kelembapan', 'humidity', `${number(current?.relative_humidity)}%`, Number(current?.relative_humidity) >= 75 ? 'Udara terasa lembap' : 'Kondisi kelembapan udara'],
            ['Curah Hujan', 'rain', `${number(current?.rain_mm,1)} mm`, 'Dalam periode pengamatan'],
            ['Jarak Pandang', 'visibility', visibilityReading(current?.visibility_m), 'Perkiraan jarak pandang'],
            ['Kualitas Udara', 'air', aqiReading(current?.us_aqi, 'AQI'), `US AQI · ${air.label}`],
        ];
        document.getElementById('metric-grid').innerHTML = metrics.map(([label, icon, value, note]) => `<article class="metric-card"><div class="metric-top"><span>${label}</span><span class="metric-icon ${label === 'Kualitas udara' ? `quality-${air.tone}` : ''}">${metricIcon(icon)}</span></div><div class="metric-value">${value}</div><div class="metric-note">${note}</div></article>`).join('');
    }
    function metricIcon(name) {
        const paths = {
            humidity:'<path d="M12 3s6 6.6 6 11a6 6 0 0 1-12 0c0-4.4 6-11 6-11Z"/><path d="M9 15a3 3 0 0 0 3 3"/>',
            wind:'<path d="M3 8h13a3 3 0 1 0-3-3M2 12h17a2.5 2.5 0 1 1-2.5 2.5M4 16h8"/>',
            rain:'<path d="M7 16v4m5-4v4m5-4v4"/><path d="M6.5 14a4.5 4.5 0 0 1-.3-9A6 6 0 0 1 17 7a3.5 3.5 0 0 1 .5 7H6.5Z"/>',
            visibility:'<path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/>',
            air:'<path d="M4 18V9m5 9V5m5 13v-6m5 6V7"/><path d="M3 20h18"/>',
        };
        return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${paths[name]}</svg>`;
    }

    function renderDays(history) {
        if (!history?.length) { document.getElementById('days-grid').innerHTML = '<tr><td colspan="4" class="empty">Riwayat cuaca belum tersedia. Jalankan sinkronisasi data historis terlebih dahulu.</td></tr>'; return; }
        document.getElementById('days-grid').innerHTML = history.map(item => {
            const date = dateParts(item.observed_on);
            const dateLabel = new Intl.DateTimeFormat('id-ID',{timeZone:'Asia/Jakarta',weekday:'short',day:'numeric',month:'short'}).format(date);
            const info = item.weather_code == null
                ? { icon: '☁️', label: 'Kondisi belum tersedia' }
                : condition(item.weather_code, 1, item.observed_on);
            const max = item.temperature_max_c == null ? '—' : number(item.temperature_max_c);
            const min = item.temperature_min_c == null ? '—' : number(item.temperature_min_c);
            const rain = item.precipitation_mm == null ? '—' : number(item.precipitation_mm,1);
            return `<tr><td>${dateLabel}</td><td><div class="history-weather"><span class="history-weather-icon">${info.icon}</span><span class="history-weather-label">${escapeHtml(info.label)}</span></div></td><td>${max}–${min}</td><td>${rain}</td></tr>`;
        }).join('');
    }

    function renderHours(forecast) {
        const now = Date.now();
        const rows = (forecast ?? []).filter(item => dateParts(item.valid_at).getTime() > now).slice(0,24);
        document.getElementById('hours-list').innerHTML = rows.map(item => {
            const info = condition(item.weather_code, item.is_day, item.valid_at);
            const date = dateParts(item.valid_at);
            const chance = item.precipitation_probability == null ? '—' : `${number(item.precipitation_probability)}%`;
            return `<tr><td class="forecast-hour-time">${localHour(date)}</td><td><span class="forecast-condition"><span class="forecast-condition-icon">${info.icon}</span><span>${escapeHtml(info.label)}</span></span></td><td class="forecast-hour-temp">${number(item.temperature_c)}°</td><td class="forecast-hour-rain">${chance}</td></tr>`;
        }).join('') || '<tr><td colspan="4">Prakiraan per jam belum tersedia.</td></tr>';
    }

    const adviceTitles = {
        olahraga_outdoor:['M30 14 18 26l-6 18 9-3 5 14 6-1-4-16 12-5-14-5 4-14Z','Olahraga Luar Ruang'],
        berkendara:['M5 18h14l2 5v13H3V23l2-5Zm2 0 3-8h10l3 8M6 28h.01M17 28h.01','Berkendara'],
        berkebun:['M12 21c0-9 5-14 15-15-1 10-6 15-15 15Zm0 0v11m0-8c-5-7-10-8-14-8 1 7 5 10 14 10','Berkebun'],
        menjemur_pakaian:['M4 8h24M7 8v22m18-22v22M8 30l5-10 5 10m-1-7h8m-8 4h8','Menjemur Pakaian'],
        piknik_pantai:['M3 27h26M5 27l12-17 12 17M17 10v17M4 32c4-3 7 3 11 0s7 3 12 0','Piknik di pantai'],
        bersepeda:['M8 27a5 5 0 1 0 0 .01M25 27a5 5 0 1 0 0 .01M8 27l6-11 6 11H8l5-8h8l4 8m-11-15h4','Bersepeda'],
        melaut_menyeberang:['M3 23h27l-4 9H9l-6-9Zm5-3 5-10h8l6 10M17 10V5m-13 30c4-3 7 3 11 0s7 3 12 0','Melaut / Menyeberang'],
        kualitas_udara:['M4 13h16a4 4 0 1 0-4-4M3 19h21a4 4 0 1 1-4 4M6 25h10','Kualitas Udara'],
        penyeberangan_pompong:['M3 24h28l-4 8H9l-6-8Zm7-3 5-9h8l5 9M17 12V6','Penyeberangan Pompong'],
        nelayan_kelong:['M3 24h28l-4 8H9l-6-8Zm14-13v13m-7-13h14M5 35c4-3 7 3 11 0s7 3 12 0','Nelayan Tradisional & Kelong'],
        penjemuran_hasil_laut:['M4 8h24M7 8v22m18-22v22M8 30l5-10 5 10m-1-7h8m-8 4h8','Penjemuran Hasil Laut'],
        penerbangan_rhf:['M18 3 15 15 5 21l1 3 11-3 5 8 3-1-3-9 7-5-1-2-9 3 2-12Z','Penerbangan Bandara RHF'],
        kuliner_tepi_laut:['M4 27h26M6 27l11-17 11 17M17 10v17M5 33c4-3 7 3 11 0s7 3 12 0','Kuliner Tepi Laut'],
        olahraga_rekreasi:['M18 5a3 3 0 1 0 0 .01M18 10l-7 8 7 4 5-6m-12 0-5 8m12-4 4 9m-7-11-4 11','Olahraga & Rekreasi'],
        pertanian_sayur:['M17 31V13m0 8c-8 0-11-4-11-10 7 0 11 3 11 10Zm0-3c0-7 4-11 11-11 0 7-4 11-11 11ZM7 32h20','Pertanian Sayur Lokal'],
    };
    function renderAdvice(advice) {
        const entries = Object.entries(advice ?? {});
        document.getElementById('advice-list').innerHTML = entries.length ? entries.map(([key,item]) => {
            const [path,title] = adviceTitles[key] ?? ['M17 4v28M3 18h28','Aktivitas'];
            const statusInfo = ({baik:['baik','Baik'],waspada:['waspada','Waspada'],saran:['saran','Disarankan'],informasi:['informasi','Informasi']})[item.status] ?? ['informasi','Informasi'];
            const reasons = String(item.reason ?? '').split('·').map(reason => reason.trim()).filter(Boolean);
            const reasonsHtml = reasons.length ? `<div class="advice-reason"><span class="advice-reason-label">Pertimbangan cuaca</span><div class="advice-reason-list">${reasons.map(reason => `<span class="advice-reason-chip">${escapeHtml(reason)}</span>`).join('')}</div></div>` : '';
            return `<article class="advice-card"><div class="advice-emoji"><svg viewBox="0 0 34 36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="${path}"/></svg></div><div class="advice-content"><div class="advice-heading"><div class="advice-title">${escapeHtml(title)}</div><span class="status ${statusInfo[0]}">${statusInfo[1]}</span></div><p class="advice-message">${escapeHtml(item.message)}</p>${reasonsHtml}</div></article>`;
        }).join('') : '<div class="advice-card advice-empty"><div class="advice-message">Saran aktivitas belum tersedia.</div></div>';
    }
    function renderRegionWeather(region, data) {
        if (!isVillage(region)) {
            const freshness = data.diperbarui_pada ? new Date(data.diperbarui_pada) : null;
            const pulledAt = data.ditarik_pada ? new Date(data.ditarik_pada) : freshness;
            document.getElementById('live-label').textContent = pulledAt ? `Diperbarui ${localHour(pulledAt)}` : 'Menunggu data cuaca';
            renderDays(data.riwayat_7_hari);
            return;
        }
        const pulledAt = data.ditarik_pada ? new Date(data.ditarik_pada) : null;
        document.getElementById('live-label').textContent = pulledAt ? `Diperbarui ${localHour(pulledAt)}` : 'Data cuaca tersedia';
        renderHero(data);
        renderDays(data.riwayat_30_hari);
        renderHours(data.prakiraan_7_hari);
        renderAdvice(data.saran_aktivitas);
    }

    async function loadWeather() {
        const refresh = document.getElementById('refresh-button');
        weatherRequestController?.abort();
        weatherRequestController = new AbortController();
        const controller = weatherRequestController;
        const requestToken = ++weatherRequestToken;
        const region = currentRegion();
        if (!region) return;
        const isLatest = () => requestToken === weatherRequestToken && region.code === selectedCode;
        const cached = weatherResponseCache.get(region.code);
        refresh.disabled = true;
        errorBanner.classList.remove('show');
        setViewMode(region);
        void loadClimateForSelectedRegion(region);
        if (cached) {
            renderRegionWeather(region, cached.data);
            if (Date.now() - cached.savedAt > 90 * 1000) document.getElementById('live-label').textContent += ' · memperbarui';
        } else {
            document.getElementById('live-label').textContent = 'Memuat data cuaca…';
        }
        try {
            const response = await fetch(`${regionsUrl}/${encodeURIComponent(region.code)}`, {headers:{Accept:'application/json'},cache:'no-store',signal:controller.signal});
            if (!response.ok) throw new Error(response.status === 404 ? 'Data cuaca belum tersedia untuk wilayah ini.' : 'Data cuaca belum dapat dimuat.');
            const data = await response.json();
            if (!isLatest()) return;
            weatherResponseCache.set(region.code, {data, savedAt:Date.now()});
            renderRegionWeather(region, data);
        } catch (error) {
            if (!isLatest()) return;
            errorBanner.textContent = `${error.message} Pastikan sinkronisasi pertama sudah selesai.`;
            errorBanner.classList.add('show');
            if (!cached) document.getElementById('hero-content').innerHTML = '<div class="loading">Data cuaca belum tersedia untuk wilayah ini.</div>';
        } finally {
            if (isLatest()) refresh.disabled = false;
        }
    }

    function renderClimate(climate) {
        if (!climate?.tersedia) {
            document.getElementById('climate-rain').textContent = 'Belum tersedia';
            document.getElementById('climate-baseline').textContent = 'Jalankan sinkronisasi riwayat iklim untuk wilayah ini.';
            document.getElementById('climate-temperature').textContent = '—';
            document.getElementById('climate-temperature-note').textContent = 'Data historis belum tersedia';
            document.getElementById('climate-humidity').textContent = '—';
            document.getElementById('climate-humidity-note').textContent = 'Data historis belum tersedia';
            document.getElementById('climate-dry-days').textContent = '—';
            document.getElementById('climate-dry-note').textContent = 'Data historis belum tersedia';
            document.getElementById('climate-coverage').textContent = 'Belum tersedia';
            document.getElementById('climate-source').textContent = 'Data historis belum tersedia untuk wilayah ini.';
            renderClimateChart(null);
            return;
        }
        document.getElementById('climate-coverage').textContent = `Data sampai ${new Intl.DateTimeFormat('id-ID',{timeZone:'Asia/Jakarta',day:'numeric',month:'short',year:'numeric'}).format(dateParts(climate.data_sampai))}`;
        document.getElementById('climate-rain').textContent = `${number(climate.hujan_30_hari_mm,1)} mm`;
        const rainDelta = climate.hujan_vs_normal_persen;
        const rainNote = rainDelta == null
            ? (climate.normal_hujan_bulan_ini_mm == null ? 'Normal historis belum cukup untuk pembanding' : `Normal historis ${number(climate.normal_hujan_bulan_ini_mm,1)} mm`)
            : Math.abs(Number(rainDelta)) < 5 ? 'Mendekati normal historis untuk periode ini' : `${Math.abs(Number(rainDelta))}% ${Number(rainDelta) < 0 ? 'di bawah' : 'di atas'} normal periode ini`;
        document.getElementById('climate-baseline').textContent = rainNote;
        document.getElementById('climate-temperature').textContent = climate.suhu_rata_rata_30_hari_c == null ? '—' : `${number(climate.suhu_rata_rata_30_hari_c,1)}°C`;
        const tempNormal = climate.suhu_normal_bulan_ini_c;
        const tempMean = climate.suhu_rata_rata_30_hari_c;
        document.getElementById('climate-temperature-note').textContent = tempNormal == null
            ? 'Normal historis belum cukup untuk pembanding'
            : `Normal historis ${number(tempNormal,1)}°C${tempMean == null ? '' : ` · ${Math.abs(Number(tempMean) - Number(tempNormal)).toFixed(1).replace('.', ',')}°C ${Number(tempMean) >= Number(tempNormal) ? 'lebih hangat' : 'lebih sejuk'}`}`;
        document.getElementById('climate-humidity').textContent = climate.kelembapan_rata_rata_30_hari == null ? '—' : `${number(climate.kelembapan_rata_rata_30_hari)}%`;
        document.getElementById('climate-humidity-note').textContent = climate.normal_kelembapan_bulan_ini == null
            ? 'Normal historis belum cukup untuk pembanding'
            : `Baseline historis ${number(climate.normal_kelembapan_bulan_ini)}%`;
        document.getElementById('climate-dry-days').textContent = climate.wilayah_agregat ? 'Per kelurahan' : (climate.hari_tanpa_hujan == null ? 'Belum diketahui' : `${climate.hari_tanpa_hujan} hari`);
        document.getElementById('climate-dry-note').textContent = climate.wilayah_agregat
            ? `${climate.jumlah_kelurahan} kelurahan memiliki data HTH`
            : `Rekor bulan ini ${climate.hari_kering_terpanjang_bulan_ini ?? '—'} hari · dihitung s.d. ${climate.dianalisis_sampai}`;
        document.getElementById('climate-source').textContent = climate.sumber;
        document.querySelector('.climate-chart-card').hidden = Boolean(climate.wilayah_agregat);
        renderClimateChart(climate);
        document.getElementById('climate-disclaimer').textContent = climate.wilayah_agregat
            ? 'Angka hujan dan suhu merupakan rata-rata ringkasan kelurahan; HTH ditampilkan per kelurahan agar variasi lokal tidak tertutup oleh rata-rata.'
            : `Data historis berbasis reanalisis model, bukan pengukuran langsung di tiap kelurahan. ${climate.definisi_hth}`;
    }

    function renderClimateChart(climate) {
        const chart = document.getElementById('climate-chart');
        const selectedMetric = document.getElementById('climate-metric').value;
        const metric = ({
            rain:{key:'hujan_mm',title:'Curah Hujan',subtitle:'Jumlah hujan yang tercatat setiap hari pada rentang pilihan.',legend:'Curah hujan harian (mm)',axis:'Curah hujan (mm)',unit:'mm',color:'#358ec3',zeroBase:true},
            temperature:{key:'suhu_rata_rata_c',title:'Suhu Rata-rata',subtitle:'Perubahan suhu rata-rata harian pada wilayah terpilih.',legend:'Suhu rata-rata harian (°C)',axis:'Suhu (°C)',unit:'°C',color:'#e58a45',zeroBase:false},
            aqi:{key:'us_aqi_rata_rata',title:'Kualitas Udara',subtitle:'Perubahan nilai US AQI harian; nilai yang lebih tinggi menunjukkan kualitas udara yang lebih buruk.',legend:'US AQI harian',axis:'US AQI',unit:'US AQI',color:'#9a72c7',zeroBase:true},
            humidity:{key:'kelembapan_rata_rata',title:'Kelembapan',subtitle:'Perubahan kelembapan relatif rata-rata harian pada wilayah terpilih.',legend:'Kelembapan rata-rata harian (%)',axis:'Kelembapan (%)',unit:'%',color:'#36a6a1',zeroBase:true},
        })[selectedMetric] ?? null;
        const days = Number(document.getElementById('climate-range').value || 30);
        const title = metric ? metric.title : 'Riwayat Iklim Harian';
        const periodLabel = days === 365 ? '1 tahun terakhir' : `${days} hari terakhir`;
        document.getElementById('climate-chart-title').textContent = title;
        document.getElementById('climate-chart-subtitle').textContent = metric ? `${metric.subtitle} Periode: ${periodLabel}.` : 'Pilih jenis data untuk melihat perubahannya dari waktu ke waktu.';
        document.getElementById('climate-chart-legend').textContent = metric?.legend ?? 'Data historis';
        document.getElementById('climate-chart-dot').style.background = metric?.color ?? '#358ec3';
        chart.setAttribute('aria-label', metric ? `${metric.legend} selama ${days} hari` : 'Grafik riwayat iklim');

        const series = Array.isArray(climate?.seri_historis) ? climate.seri_historis : [];
        const points = series.map(row => {
            const raw = metric ? row[metric.key] : null;
            return {...row, value:raw == null || raw === '' || !Number.isFinite(Number(raw)) ? null : Number(raw)};
        });
        const values = points.map(point => point.value).filter(value => value !== null);
        const firstDate = points[0]?.tanggal;
        const lastDate = points.at(-1)?.tanggal;
        document.getElementById('climate-chart-range').textContent = firstDate && lastDate
            ? `${new Intl.DateTimeFormat('id-ID',{timeZone:'Asia/Jakarta',day:'numeric',month:'short'}).format(dateParts(firstDate))} – ${new Intl.DateTimeFormat('id-ID',{timeZone:'Asia/Jakarta',day:'numeric',month:'short',year:'numeric'}).format(dateParts(lastDate))}`
            : `Rentang ${days} hari`;

        if (!metric || values.length === 0) {
            chart.innerHTML = `<text class="climate-empty-label" x="480" y="132" text-anchor="middle">${metric ? `Belum ada data ${escapeHtml(metric.title.toLowerCase())} untuk rentang ini.` : 'Pilih jenis visualisasi.'}</text>`;
            return;
        }

        const plot = {left:82, top:28, width:842, height:170};
        const dataMin = Math.min(...values);
        const dataMax = Math.max(...values);
        const span = Math.max(dataMax - dataMin, Math.abs(dataMax) * .08, selectedMetric === 'rain' ? .1 : 1);
        const rawStep = span / 4;
        const magnitude = 10 ** Math.floor(Math.log10(rawStep));
        const tickStep = Math.max(magnitude, Math.ceil(rawStep / magnitude) * magnitude);
        const axisMin = metric.zeroBase ? 0 : Math.floor((dataMin - tickStep * .35) / tickStep) * tickStep;
        const axisMax = metric.zeroBase
            ? Math.max(tickStep * 4, Math.ceil(dataMax / tickStep) * tickStep)
            : Math.max(axisMin + tickStep * 4, Math.ceil((dataMax + tickStep * .35) / tickStep) * tickStep);
        const yFor = value => plot.top + plot.height - ((value - axisMin) / (axisMax - axisMin)) * plot.height;
        const xFor = index => plot.left + (points.length <= 1 ? plot.width / 2 : index * plot.width / (points.length - 1));
        let svg = `<text class="climate-axis-title" x="${plot.left}" y="15">${escapeHtml(metric.axis)}</text><text class="climate-axis-title" x="${plot.left + plot.width / 2}" y="263" text-anchor="middle">Tanggal</text>`;
        const tickCount = 4;
        const displayStep = (axisMax - axisMin) / tickCount;
        for (let tick = 0; tick <= tickCount; tick++) {
            const value = axisMin + displayStep * tick;
            const y = yFor(value);
            const label = number(value, metric.zeroBase ? 0 : 1);
            svg += `<line class="climate-grid-line" x1="${plot.left}" y1="${y}" x2="${plot.left + plot.width}" y2="${y}"/><text class="climate-axis-label" x="${plot.left - 12}" y="${y + 4}" text-anchor="end">${label}</text>`;
        }
        svg += `<line class="climate-axis-line" x1="${plot.left}" y1="${plot.top}" x2="${plot.left}" y2="${plot.top + plot.height}"/><line class="climate-axis-line" x1="${plot.left}" y1="${plot.top + plot.height}" x2="${plot.left + plot.width}" y2="${plot.top + plot.height}"/>`;
        const dateStep = Math.max(1, Math.ceil(points.length / 7));

        if (selectedMetric === 'rain') {
            const slotWidth = plot.width / Math.max(points.length, 1);
            const barWidth = Math.max(2, Math.min(24, slotWidth * .5));
            points.forEach((point, index) => {
                const x = plot.left + index * slotWidth + (slotWidth - barWidth) / 2;
                const date = new Intl.DateTimeFormat('id-ID',{timeZone:'Asia/Jakarta',day:'numeric',month:'short'}).format(dateParts(point.tanggal));
                if (point.value === null) {
                    svg += `<rect class="climate-bar-missing" x="${x}" y="${plot.top + plot.height - 4}" width="${barWidth}" height="4" rx="2"><title>${escapeHtml(date)} · Data tidak tersedia</title></rect>`;
                } else {
                    const y = yFor(point.value);
                    const height = point.value === 0 ? 0 : Math.max(2, plot.top + plot.height - y);
                    svg += `<rect class="climate-bar" style="fill:${metric.color}" x="${x}" y="${y}" width="${barWidth}" height="${height}" rx="3"><title>${escapeHtml(date)} · ${number(point.value,1)} ${metric.unit}</title></rect>`;
                }
                if (index % dateStep === 0 || index === points.length - 1) svg += `<text class="climate-date-label" x="${xFor(index)}" y="${plot.top + plot.height + 23}" text-anchor="middle">${escapeHtml(date)}</text>`;
            });
        } else {
            const segments = [];
            let segment = [];
            points.forEach((point, index) => {
                if (point.value === null) {
                    if (segment.length) segments.push(segment);
                    segment = [];
                } else segment.push({x:xFor(index), y:yFor(point.value), point, index});
            });
            if (segment.length) segments.push(segment);
            segments.forEach(line => {
                if (line.length > 1) svg += `<polyline class="climate-line" style="stroke:${metric.color}" points="${line.map(point => `${point.x},${point.y}`).join(' ')}"/>`;
                line.forEach(({x,y,point,index}) => {
                    const date = new Intl.DateTimeFormat('id-ID',{timeZone:'Asia/Jakarta',day:'numeric',month:'short'}).format(dateParts(point.tanggal));
                    svg += `<circle class="climate-point" style="stroke:${metric.color}" cx="${x}" cy="${y}" r="3.5"><title>${escapeHtml(date)} · ${number(point.value,1)} ${metric.unit}</title></circle>`;
                    if (index % dateStep === 0 || index === points.length - 1) svg += `<text class="climate-date-label" x="${x}" y="${plot.top + plot.height + 23}" text-anchor="middle">${escapeHtml(date)}</text>`;
                });
            });
        }
        chart.innerHTML = svg;
    }

    async function loadClimateForSelectedRegion(region) {
        const climateEndpoint = @json(url('/api/climate/summary'));
        const days = Number(document.getElementById('climate-range').value || 30);
        const cacheKey = `${region.code}:${days}`;
        climateRequestController?.abort();
        climateRequestController = new AbortController();
        const controller = climateRequestController;
        const cached = climateResponseCache.get(cacheKey);
        if (cached) renderClimate(cached);
        try {
            const response = await fetch(`${climateEndpoint}/${encodeURIComponent(region.code)}?days=${days}`, {headers:{Accept:'application/json'},cache:'no-store',signal:controller.signal});
            if (!response.ok) throw new Error('Ringkasan iklim belum dapat dimuat.');
            const climate = await response.json();
            climateResponseCache.set(cacheKey, climate);
            if (region.code !== selectedCode || days !== Number(document.getElementById('climate-range').value || 30)) return;
            renderClimate(climate);
        } catch (error) {
            if (error.name === 'AbortError') return;
            if (cached) return;
            if (region.code !== selectedCode || days !== Number(document.getElementById('climate-range').value || 30)) return;
            document.getElementById('climate-coverage').textContent = 'Gagal memuat';
            document.getElementById('climate-rain').textContent = '—';
            document.getElementById('climate-baseline').textContent = 'Data iklim belum dapat dimuat. Coba lagi setelah sinkronisasi historis.';
            document.getElementById('climate-temperature').textContent = '—';
            document.getElementById('climate-temperature-note').textContent = 'Data iklim belum dapat dimuat';
            document.getElementById('climate-humidity').textContent = '—';
            document.getElementById('climate-humidity-note').textContent = 'Data iklim belum dapat dimuat';
            document.getElementById('climate-dry-days').textContent = '—';
            document.getElementById('climate-dry-note').textContent = 'Data iklim belum dapat dimuat';
        }
    }

    async function refreshWeatherNow() {
        const refresh = document.getElementById('refresh-button');
        if (refresh.disabled) return;
        refresh.disabled = true;
        document.getElementById('live-label').textContent = 'Memperbarui cuaca…';
        errorBanner.classList.remove('show');
        try {
            const response = await fetch(refreshUrl, {
                method:'POST',
                headers:{Accept:'application/json', 'Content-Type':'application/json', 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content ?? ''},
                body:JSON.stringify({region_code:currentRegion()?.code}),
                cache:'no-store'
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Data cuaca belum berhasil diperbarui.');
            await loadWeather();
            document.getElementById('live-label').textContent += ' · baru saja';
        } catch (error) {
            errorBanner.textContent = error.message;
            errorBanner.classList.add('show');
            document.getElementById('live-label').textContent = 'Pembaruan gagal';
            refresh.disabled = false;
        }
    }

    async function start() {
        try {
            const response = await fetch(regionsUrl, {headers:{Accept:'application/json'},cache:'no-store'});
            if (!response.ok) throw new Error('Daftar wilayah belum dapat dimuat.');
            regions = await response.json();
            renderLocationPicker();
            setViewMode(currentRegion());
            initWeatherMap();
            void loadNews();
            await locateUser();
        } catch (error) {
            errorBanner.textContent = `${error.message} Periksa koneksi aplikasi dan database.`;
            errorBanner.classList.add('show');
        }
    }

    locationToggle.addEventListener('click', () => {
        const opening = locationMenu.hidden;
        locationMenu.hidden = !opening;
        locationToggle.setAttribute('aria-expanded', String(opening));
        if (opening) renderLocationPicker();
    });
    document.addEventListener('click', event => {
        if (!event.target.closest('.location-control')) closeLocationMenu();
    });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') closeLocationMenu(); });
    document.getElementById('refresh-button').addEventListener('click', refreshWeatherNow);
    document.getElementById('use-location').addEventListener('click', locateUser);
    document.getElementById('climate-range').addEventListener('change', () => {
        const region = currentRegion();
        if (region) void loadClimateForSelectedRegion(region);
    });
    document.getElementById('climate-metric').addEventListener('change', () => {
        const region = currentRegion();
        if (!region) return;
        const days = Number(document.getElementById('climate-range').value || 30);
        const cached = climateResponseCache.get(`${region.code}:${days}`);
        if (cached) renderClimate(cached);
        else void loadClimateForSelectedRegion(region);
    });
    function navigateTo(event, targetId) {
        event.preventDefault();
        document.querySelector('.shell').classList.remove('map-page');
        const target = document.getElementById(targetId);
        target.scrollIntoView({behavior:'smooth', block:'start'});
        document.querySelectorAll('.main-nav > a.nav-link').forEach(link => link.classList.remove('active'));
        event.currentTarget.classList.add('active');
    }
    function showMapPage(event) {
        event.preventDefault();
        closeLocationMenu();
        document.querySelector('.shell').classList.add('map-page');
        document.querySelectorAll('.main-nav > a.nav-link').forEach(link => link.classList.remove('active'));
        document.getElementById('nav-map').classList.add('active');
        window.requestAnimationFrame(() => {
            weatherMap?.invalidateSize({pan:false});
            showMapLevel(mapPath[mapPath.length - 1], true);
        });
    }
    document.getElementById('nav-home').addEventListener('click', event => {
        event.preventDefault();
        document.querySelector('.shell').classList.remove('map-page');
        document.querySelectorAll('.main-nav > a.nav-link').forEach(link => link.classList.remove('active'));
        event.currentTarget.classList.add('active');
        window.scrollTo({top:0, behavior:'smooth'});
    });
    document.getElementById('nav-climate').addEventListener('click', event => navigateTo(event, 'climate-insights'));
    document.getElementById('nav-news').addEventListener('click', event => navigateTo(event, 'news-section'));
    document.getElementById('nav-map').addEventListener('click', showMapPage);
    document.addEventListener('click', event => {
        const categoryButton = event.target.closest('[data-weather-category-trigger]');
        if (categoryButton) {
            event.preventDefault();
            event.stopPropagation();
            const opening = categoryButton.getAttribute('aria-expanded') !== 'true';
            document.querySelectorAll('[data-weather-category-trigger]').forEach(button => {
                button.setAttribute('aria-expanded', 'false');
                button.parentElement?.classList.remove('weather-reading-open');
                const label = button.querySelector('.weather-category');
                if (label) label.hidden = true;
            });
            categoryButton.setAttribute('aria-expanded', String(opening));
            categoryButton.parentElement?.classList.toggle('weather-reading-open', opening);
            const label = categoryButton.querySelector('.weather-category');
            if (label) label.hidden = !opening;
            return;
        }
        document.querySelectorAll('[data-weather-category-trigger][aria-expanded="true"]').forEach(button => {
            button.setAttribute('aria-expanded', 'false');
            button.parentElement?.classList.remove('weather-reading-open');
            const label = button.querySelector('.weather-category');
            if (label) label.hidden = true;
        });
        const regionButton = event.target.closest('[data-map-region-code]');
        if (!regionButton) return;
        event.preventDefault();
        const code = regionButton.dataset.mapRegionCode;
        document.querySelector('.shell').classList.remove('map-page');
        document.querySelectorAll('.main-nav > a.nav-link').forEach(link => link.classList.remove('active'));
        document.getElementById('nav-home').classList.add('active');
        selectRegion(code);
        window.requestAnimationFrame(() => document.getElementById('current-hero-wrap').scrollIntoView({behavior:'smooth', block:'start'}));
    });
    start();
    window.setInterval(loadWeather, 5 * 60 * 1000);
})();
</script>
</body>
</html>
