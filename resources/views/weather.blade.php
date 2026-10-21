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
        :root{--ink:#142f4f;--muted:#6f8298;--line:#e2ebf4;--paper:#fff;--canvas:#f2f7fc;--teal:#2476bd;--teal-dark:#155a98;--mint:#e7f2fc;--blue:#eaf4ff;--amber:#f4c542;--shadow:0 12px 36px rgba(30,76,120,.08);--radius:22px}
        *{box-sizing:border-box}body{margin:0;background:var(--canvas);color:var(--ink);font:15px/1.55 'Plus Jakarta Sans',sans-serif}button,select{font:inherit}button{cursor:pointer}a{color:inherit;text-decoration:none}.shell{max-width:1440px;min-height:100vh;margin:auto;padding:0 34px 42px}.topbar{height:82px;display:flex;align-items:center;justify-content:space-between;gap:20px;border-bottom:1px solid var(--line)}.brand{display:flex;align-items:center;gap:10px;letter-spacing:0}.brand-logo{width:52px;height:52px;object-fit:contain;mix-blend-mode:multiply;flex:0 0 auto}.brand-name{display:block;color:#175b98;font:800 18px/1 'Plus Jakarta Sans',sans-serif;letter-spacing:1.4px}.brand-tagline{display:block;margin-top:4px;color:#55728c;font:600 10px/1.3 'Plus Jakarta Sans',sans-serif;letter-spacing:.1px}.brand-mark{display:none}.top-actions{display:flex;align-items:center;gap:12px}.live-pill{display:flex;align-items:center;gap:7px;color:#52749a;font-size:13px;font-weight:600}.live-dot{width:8px;height:8px;border-radius:50%;background:#45b887;box-shadow:0 0 0 4px #45b88720}.icon-btn{width:40px;height:40px;display:grid;place-items:center;border:1px solid var(--line);border-radius:13px;background:#fff;color:var(--ink)}.icon-btn svg{width:17px}.icon-btn:hover{border-color:#a8c9e6;background:#f8fbff}.page{padding-top:28px}.intro{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:22px}.eyebrow{margin:0 0 5px;color:var(--teal);font-size:12px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase}.intro h1{margin:0;font:800 clamp(27px,3vw,36px)/1.2 'Plus Jakarta Sans',sans-serif;letter-spacing:-1px}.intro p{margin:7px 0 0;color:var(--muted);font-size:15px}.error-banner{display:none;margin-bottom:18px;padding:13px 16px;border:1px solid #f4dda4;border-radius:14px;background:#fff6e2;color:#8a5b08}.error-banner.show{display:block}.hero{position:relative;min-height:270px;overflow:hidden;padding:30px 34px;border-radius:27px;background:linear-gradient(118deg,#175b98,#277db9 57%,#62add4);color:#fff;box-shadow:0 18px 40px #1d66a527}.hero:before,.hero:after{position:absolute;border:1px solid #ffffff24;border-radius:50%;content:"";pointer-events:none}.hero:before{top:-270px;right:6%;width:410px;height:410px}.hero:after{top:-220px;right:-4%;width:520px;height:520px}.hero-content{position:relative;z-index:1;display:grid;grid-template-columns:1.1fr .9fr;align-items:center;gap:25px}.hero-place{color:#e0f1ff;font-size:14px;font-weight:600}.hero-date{margin-top:3px;color:#d0e8fa;font-size:13px}.temp-row{display:flex;align-items:center;gap:20px;margin-top:9px}.weather-symbol{font-size:54px;line-height:1}.temperature{font:700 clamp(64px,8vw,91px)/.98 'Plus Jakarta Sans',sans-serif;letter-spacing:-5px}.temperature sup{margin-left:3px;font-size:27px;vertical-align:top}.condition{margin:6px 0 1px;font-size:19px;font-weight:700}.feels{color:#e3f2ff;font-size:13px}.hero-summary{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:11px}.hero-stat{padding:14px 15px;border:1px solid #ffffff45;border-radius:16px;background:#ffffff18;backdrop-filter:blur(4px)}.hero-stat-label{display:flex;align-items:center;gap:7px;color:#e1f1fc;font-size:12px}.hero-stat-value{margin-top:7px;font:700 20px 'Plus Jakarta Sans',sans-serif}.hero-stat-unit{margin-left:3px;color:#e1f1fc;font:500 12px 'Plus Jakarta Sans',sans-serif}.section{margin-top:30px}.section-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:14px}.section-title{margin:0;font:800 19px 'Plus Jakarta Sans',sans-serif;letter-spacing:-.35px}.section-subtitle{margin:4px 0 0;color:var(--muted);font-size:13px}.section-link{color:var(--teal);font-size:13px;font-weight:700}.metric-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:13px}.metric-card,.panel,.day-card,.advice-card{border:1px solid var(--line);border-radius:var(--radius);background:var(--paper);box-shadow:var(--shadow)}.metric-card{min-height:112px;padding:17px 18px}.metric-top{display:flex;align-items:center;justify-content:space-between;color:var(--muted);font-size:13px;font-weight:600}.metric-icon{width:32px;height:32px;display:grid;place-items:center;border-radius:11px;background:var(--mint);color:var(--teal)}.metric-icon svg{width:16px}.metric-value{margin-top:9px;font:700 25px 'Plus Jakarta Sans',sans-serif;letter-spacing:-.5px}.metric-note{margin-top:1px;color:var(--muted);font-size:12px}.panel{padding:20px 21px}.days-grid{display:grid;grid-template-columns:repeat(7,minmax(105px,1fr));gap:10px}.day-card{padding:15px 10px 13px;text-align:center;border-radius:18px;box-shadow:none}.day-card.today{border-color:#f0d46c;background:#fffcf0}.day-name{color:var(--muted);font-size:12px;font-weight:700}.day-icon{margin:10px 0 5px;font-size:25px}.day-condition{min-height:30px;color:var(--muted);font-size:11px}.day-temps{display:flex;justify-content:center;gap:9px;margin-top:8px;font:700 14px 'Plus Jakarta Sans',sans-serif}.day-temps .low{color:#94a2b0;font-weight:500}.rain-chance{margin-top:9px;color:#397cae;font-size:11px}.hour-panel{overflow:hidden;padding:18px 20px 20px}.hours{display:grid;grid-auto-columns:minmax(78px,1fr);grid-auto-flow:column;gap:8px;overflow-x:auto;padding-bottom:4px;scrollbar-width:thin}.hour-card{padding:12px 7px 11px;border-radius:15px;background:#f6faff;text-align:center}.hour-time{color:var(--muted);font-size:11px;font-weight:600}.hour-icon{margin:8px 0 5px;font-size:21px}.hour-temp{font:700 15px 'Plus Jakarta Sans',sans-serif}.hour-rain{margin-top:6px;color:#397cae;font-size:11px}.content-grid{display:grid;grid-template-columns:1.35fr .85fr;gap:15px}.advice-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.advice-card{display:flex;gap:12px;padding:15px 16px;border-radius:17px;box-shadow:none}.advice-emoji{width:34px;height:34px;display:grid;flex:0 0 34px;place-items:center;border-radius:12px;background:#fff7dc;font-size:17px}.advice-title{font-size:13px;font-weight:700}.advice-message{margin-top:4px;color:var(--muted);font-size:12px;line-height:1.55}.status{display:inline-flex;align-items:center;gap:5px;margin-top:7px;color:var(--teal);font-size:10px;font-weight:700;letter-spacing:.4px;text-transform:uppercase}.status:before{width:6px;height:6px;border-radius:50%;background:#4caf84;content:""}.status.waspada{color:#ad6b22}.status.waspada:before{background:#edae4e}.status.saran{color:#5b7890}.status.saran:before{background:#6c9fc2}.marine-card{height:100%;padding:20px 21px;border:1px solid #d8e8f5;border-radius:var(--radius);background:linear-gradient(145deg,#edf6ff,#fff 70%);box-shadow:var(--shadow)}.marine-heading{display:flex;align-items:center;justify-content:space-between}.marine-badge{padding:6px 9px;border-radius:9px;background:#fff4c9;color:#80630b;font-size:11px;font-weight:700}.marine-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-top:17px}.marine-metric{padding:13px;border:1px solid #e8f0f0;border-radius:15px;background:#ffffffba}.marine-label{color:var(--muted);font-size:11px}.marine-value{margin-top:5px;font:700 20px 'Plus Jakarta Sans',sans-serif}.marine-note{margin:13px 2px 0;color:#7c8d8a;font-size:11px;line-height:1.55}.empty{padding:22px;color:var(--muted);font-size:13px;text-align:center}.loading{color:#e1f1fc}.footer{display:flex;justify-content:space-between;gap:14px;margin-top:27px;padding:17px 3px;border-top:1px solid var(--line);color:#8592a0;font-size:11px}.footer a{color:var(--teal);font-weight:700}.attribution{display:flex;align-items:center;gap:5px}.attribution svg{width:13px;color:var(--teal)}
        .brand{gap:10px;letter-spacing:0}.brand-logo{width:52px;height:52px;object-fit:contain;mix-blend-mode:multiply;flex:0 0 auto}.brand-name{display:block;font:800 18px/1 Manrope,sans-serif;letter-spacing:1.4px;color:#175b98}.brand-tagline{display:block;color:#55728c;font:600 9px/1.3 'DM Sans',sans-serif;letter-spacing:.1px;margin-top:4px}.brand-mark{display:none}
        .hero-summary{gap:12px}.hero-stat{min-height:102px;padding:15px 16px;background:linear-gradient(145deg,#ffffff20,#ffffff12);transition:transform .18s ease,background .18s ease}.hero-stat:hover{transform:translateY(-2px);background:#ffffff24}.hero-stat-label{font-size:11px;font-weight:600}.hero-stat-icon{width:27px;height:27px;display:grid;place-items:center;border-radius:9px;background:#ffffff20;color:#fff}.hero-stat-icon svg{width:15px;height:15px}.hero-stat-value{margin-top:9px;font-size:19px}.hero-stat-note{margin-top:3px;color:#d7ebfa;font-size:10px;line-height:1.35}
        @media(max-width:640px){.brand-logo{width:45px;height:45px}.brand-name{font-size:16px}.brand-tagline{font-size:8px}}
        @media(max-width:1000px){.shell{padding:0 22px 32px}.hero-content{grid-template-columns:1fr .9fr}.days-grid{grid-template-columns:repeat(7,120px);overflow-x:auto;padding-bottom:5px}.metric-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.content-grid{grid-template-columns:1fr}.marine-grid{grid-template-columns:repeat(4,1fr)}}
        @media(max-width:640px){.shell{padding:0 15px 25px}.topbar{height:68px}.brand{font-size:17px}.brand-mark{height:35px;width:35px}.live-pill{font-size:0}.live-dot{width:9px;height:9px}.page{padding-top:22px}.intro{display:block}.intro h1{font-size:27px}.select-wrap{margin-top:16px;justify-content:space-between}.select-wrap select{max-width:none;flex:1}.hero{padding:23px 21px;min-height:0;border-radius:23px}.hero-content{grid-template-columns:1fr;gap:19px}.hero-summary{grid-template-columns:repeat(2,1fr)}.hero-stat{padding:11px 12px}.hero-stat-value{font-size:18px}.temperature{font-size:75px}.temperature sup{font-size:23px}.weather-symbol{font-size:45px}.metric-card{padding:14px;min-height:105px;border-radius:18px}.metric-value{font-size:21px}.section{margin-top:24px}.section-head{align-items:flex-start}.section-title{font-size:17px}.panel{padding:16px;border-radius:19px}.advice-list{grid-template-columns:1fr}.marine-grid{grid-template-columns:repeat(2,1fr)}.footer{flex-direction:column}.day-card{min-width:110px}}
        .location-control{position:relative;width:min(370px,100%);flex:0 0 auto}.location-caption{display:block;color:#748ba3;font-size:10px;font-weight:700;letter-spacing:1.15px;margin:0 0 6px 3px}.location-toggle{width:100%;min-height:58px;display:flex;align-items:center;gap:12px;text-align:left;padding:9px 13px;background:#fff;border:1px solid var(--line);border-radius:16px;color:var(--ink);box-shadow:0 7px 20px #1e4b7308;transition:.18s}.location-toggle:hover,.location-toggle[aria-expanded=true]{border-color:#9dc4e4;box-shadow:0 8px 24px #2679bd18}.location-pin{width:36px;height:36px;display:grid;place-items:center;border-radius:12px;background:var(--mint);color:var(--teal);flex:0 0 auto}.location-pin svg{width:18px}.location-copy{min-width:0;flex:1}.location-level{display:block;color:#8194a7;font-size:10px;margin-bottom:1px}.location-name{display:block;font-weight:700;font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.location-chevron{width:17px;color:#71869a;transition:transform .18s}.location-toggle[aria-expanded=true] .location-chevron{transform:rotate(180deg)}.location-menu{position:absolute;z-index:10;right:0;top:calc(100% + 10px);width:100%;padding:15px;background:#fff;border:1px solid var(--line);border-radius:19px;box-shadow:0 20px 50px #183b5e20;animation:menu-in .16s ease-out}.location-menu[hidden]{display:none}@keyframes menu-in{from{opacity:0;transform:translateY(-5px)}to{opacity:1;transform:translateY(0)}}.location-menu-head{display:flex;align-items:center;justify-content:space-between;margin:2px 3px 10px}.location-menu-head small{display:block;color:var(--teal);font-size:9px;letter-spacing:1px;font-weight:700}.location-menu-head strong{font:700 14px Manrope,sans-serif}.location-up{border:0;background:#edf5fc;color:var(--teal);border-radius:10px;width:34px;height:34px;font-size:16px}.location-up[hidden]{display:none}.location-crumbs{display:flex;gap:5px;align-items:center;flex-wrap:wrap;margin:0 3px 12px;color:#99a8b5;font-size:10px}.location-crumbs button{border:0;background:transparent;color:var(--teal);padding:2px 0;font-size:10px;font-weight:700}.location-options{display:grid;gap:7px;max-height:290px;overflow:auto}.location-option{width:100%;display:flex;align-items:center;justify-content:space-between;text-align:left;padding:11px 12px;border:1px solid #eaf0f5;background:#f9fcff;border-radius:13px;color:var(--ink);transition:.16s}.location-option:hover{background:#eef7ff;border-color:#c1dbef;transform:translateY(-1px)}.location-option strong{display:block;font-size:12px}.location-option small{display:block;color:var(--muted);font-size:10px;margin-top:2px}.location-option .arrow{color:#78a0bf;font-size:17px}.location-empty{padding:12px;color:var(--muted);font-size:11px;text-align:center;background:#f6faff;border-radius:12px}
        .region-forecasts{display:grid;gap:16px}.region-forecast{background:#fff;border:1px solid var(--line);border-radius:21px;padding:17px 18px;box-shadow:var(--shadow)}.region-forecast-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px}.region-forecast-name{font:700 15px Manrope,sans-serif}.region-forecast-type{color:var(--muted);font-size:10px;margin-top:2px}.region-forecast-open{border:1px solid #d4e5f3;background:#f4f9fe;color:var(--teal);border-radius:11px;padding:8px 11px;font-size:11px;font-weight:700;white-space:nowrap}.region-day-row{display:grid;grid-template-columns:repeat(7,minmax(88px,1fr));gap:8px;overflow:auto}.region-day{min-width:88px;text-align:center;background:#f6faff;border-radius:14px;padding:10px 7px}.region-day:nth-child(1){background:#fffbee}.region-day-date{font-size:10px;font-weight:700;color:#61778d}.region-day-icon{font-size:21px;margin:6px 0 3px}.region-day-condition{font-size:9px;color:var(--muted);min-height:27px}.region-day-temp{font:700 12px Manrope,sans-serif;margin-top:5px}.region-day-rain{font-size:9px;color:#397cae;margin-top:3px}.compact-forecast{display:none}.compact-forecast .section-head{margin-bottom:13px}.compact-forecast.show{display:block}.detail-only[hidden],.overview-only[hidden]{display:none!important}
        .geo-status{margin:7px 3px 0;color:#5c7590;font-size:10px;line-height:1.45}.geo-status[hidden],.geo-button[hidden]{display:none}.geo-button{margin:8px 0 0 2px;padding:7px 10px;border:1px solid #d5e6f4;border-radius:10px;background:#fff;color:#246ca5;font-size:10px;font-weight:700}.geo-button:hover{background:#f2f8fe;border-color:#abcde8}
        .topbar{height:80px}.main-nav{display:flex;align-items:center;gap:5px;margin-left:auto}.nav-link{display:flex;align-items:center;gap:8px;padding:9px 13px;border:0;border-radius:11px;background:transparent;color:#526b84;font-size:12px;font-weight:700;white-space:nowrap;transition:background .16s,color .16s}.nav-link:hover,.nav-link.active{background:#e8f2fb;color:#185f99}.location-control{width:auto;flex:0 0 auto}.location-toggle{min-height:40px;width:auto;padding:8px 11px;box-shadow:none;color:#526b84}.location-toggle:hover,.location-toggle[aria-expanded=true]{border-color:transparent;background:#e8f2fb;box-shadow:none;color:#185f99}.nav-location-current{display:grid;gap:0;margin-left:3px;padding-left:9px;border-left:1px solid #dce7f1;text-align:left}.nav-location-current small{color:#8293a5;font-size:9px;line-height:1.15;font-weight:500}.nav-location-current strong{max-width:135px;overflow:hidden;color:var(--ink);font-size:10px;text-overflow:ellipsis}.main-nav .location-chevron{width:14px}.location-menu{top:calc(100% + 9px);right:0;left:auto;width:320px}.top-actions{margin-left:13px}.geo-status{position:static;width:auto;margin:12px 0 0;padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:#f7fbff;color:#526b84;font-size:11px;line-height:1.45;box-shadow:none}.geo-button{position:static;width:100%;margin:12px 0 0;padding:9px 11px;border:1px solid #d5e6f4;border-radius:10px;background:#f4f9fe;color:#246ca5;font-size:11px;font-weight:700;text-align:center;white-space:nowrap}.geo-button:hover{background:#eaf4fc;border-color:#abcde8}
        @media(max-width:1000px){.topbar{gap:8px}.main-nav{gap:1px}.nav-link{padding:8px 9px}.top-actions{margin-left:4px}}
        @media(max-width:640px){.topbar{height:auto;min-height:68px;padding:8px 0;flex-wrap:wrap}.main-nav{order:3;width:100%;justify-content:center;margin:0;border-top:1px solid var(--line);padding-top:7px}.top-actions{order:2;margin-left:auto}.brand{order:1}.nav-link{font-size:11px}.nav-location-current strong{max-width:90px}.location-control{width:auto;margin-top:0}.location-menu{left:auto;right:0;width:min(320px,calc(100vw - 30px));transform:none}}
        @media(max-width:640px){.region-forecast{padding:14px}.region-forecast-head{align-items:flex-start}.region-forecast-open{padding:7px 9px}.region-day-row{grid-template-columns:repeat(7,88px)}}
        @media(max-width:640px){.location-control{width:auto;margin-top:0}.location-menu{left:auto;right:0}.location-toggle{min-height:40px}.location-options{max-height:45vh}}
        .map-panel{overflow:hidden;padding:12px;border:1px solid var(--line);border-radius:22px;background:#fff;box-shadow:var(--shadow)}#weather-map{height:390px;width:100%;border-radius:15px;background:#edf4f8;z-index:1}.map-note{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:11px 5px 2px;color:var(--muted);font-size:11px}.map-level{flex:0 0 auto;padding:5px 8px;border-radius:8px;background:#fff6d7;color:#7a620e;font-size:10px;font-weight:700}.map-pin{width:28px;height:28px;display:grid;place-items:center;border:2px solid #fff;border-radius:50% 50% 50% 4px;background:#3182bd;color:#fff;font:700 11px Manrope,sans-serif;box-shadow:0 3px 10px #173c5c55;transform:rotate(-45deg)}.map-pin span{transform:rotate(45deg)}.map-pin.selected{background:#f2bd28;color:#183b5d;box-shadow:0 0 0 4px #f2bd2855,0 3px 10px #173c5c55}.leaflet-tooltip.map-tooltip{border:0;border-radius:8px;padding:5px 8px;color:#18314e;font:700 11px 'DM Sans',sans-serif;box-shadow:0 4px 12px #183b5c22}.map-fallback{min-height:150px;display:grid;place-items:center;padding:24px;color:var(--muted);text-align:center}
        @media(max-width:640px){#weather-map{height:330px}.map-panel{padding:8px}.map-note{align-items:flex-start;font-size:10px}}
        .climate-summary{display:grid;grid-template-columns:1.5fr repeat(3,1fr);gap:12px}.climate-card,.climate-chart-card{border:1px solid var(--line);border-radius:19px;background:#fff;padding:17px 18px;box-shadow:var(--shadow)}.climate-card{display:flex;flex-direction:column;gap:6px;min-height:105px}.climate-card strong{font:700 20px 'Plus Jakarta Sans',sans-serif;letter-spacing:-.4px}.climate-feature{background:linear-gradient(135deg,#f6fbff,#fff8e4);border-color:#e9e4cd}.climate-feature strong{font-size:17px}.climate-kicker{color:#758ba0;font-size:10px;font-weight:800;letter-spacing:.8px}.climate-small{color:var(--muted);font-size:11px;line-height:1.4}.climate-chart-card{margin-top:13px}.climate-chart-head,.climate-legend{display:flex;align-items:center;justify-content:space-between;gap:12px;color:var(--muted);font-size:11px}.climate-chart-head>div{display:grid;gap:3px}.climate-chart-head strong{color:var(--ink);font-size:13px}.climate-chart{height:112px;display:flex;align-items:flex-end;gap:4px;overflow-x:auto;margin:20px 2px 6px;padding:0 3px}.rain-column{position:relative;min-width:11px;flex:1;height:100%;display:flex;align-items:flex-end;justify-content:center}.rain-column:before{content:"";position:absolute;bottom:0;left:50%;width:1px;height:100%;background:#eef3f7;z-index:0}.rain-bar{position:relative;z-index:1;width:min(18px,80%);min-height:2px;border-radius:5px 5px 2px 2px;background:linear-gradient(180deg,#63b6db,#2783bd)}.rain-column[title]:hover .rain-bar{background:#f1c443}.climate-legend{padding-top:9px;border-top:1px solid #eef2f6}.climate-legend>span:first-child{display:flex;align-items:center;gap:6px}.rain-dot{width:8px;height:8px;border-radius:50%;background:#3189c1}.climate-chart-head>span{white-space:nowrap}.climate-disclaimer{margin:10px 3px;color:var(--muted);font-size:10px;line-height:1.5}.climate-chart-card[hidden],#climate-summary[hidden]{display:none}.main-nav>.nav-link{font-size:13px}.intro p,.section-subtitle{font-size:14px}.section-title{font-family:'Plus Jakarta Sans',sans-serif}.location-name{font-size:14px}.region-forecast-name{font-family:'Plus Jakarta Sans',sans-serif}.region-day-date{font-size:11px}.region-day-condition{font-size:10px}.map-note{font-size:12px}
        @media(max-width:800px){.climate-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.climate-feature{grid-column:span 2}}
        @media(max-width:640px){.climate-summary{gap:8px}.climate-card{min-height:96px;padding:13px}.climate-card strong{font-size:17px}.climate-feature strong{font-size:15px}.climate-kicker{font-size:9px}.climate-small{font-size:10px}.climate-chart-card{padding:15px}.climate-chart-head,.climate-legend{font-size:10px}.main-nav .nav-link{font-size:11px}}
        .topbar{height:72px}.page{padding-top:19px}.intro{align-items:center;margin-bottom:15px}.intro h1{font-size:clamp(25px,2.5vw,32px)}.intro p{margin-top:4px}.location-menu{width:min(390px,calc(100vw - 32px));padding:12px}.location-options{gap:2px;max-height:min(65vh,460px);padding:2px}.region-tree-row{display:flex;align-items:center;gap:5px;min-height:39px;padding-left:calc(var(--tree-depth,0)*13px)}.region-tree-row.level-district{--tree-depth:1}.region-tree-row.level-village{--tree-depth:2}.region-tree-select{flex:1;min-width:0;display:flex;align-items:center;justify-content:space-between;gap:8px;padding:8px 10px;border:0;border-radius:10px;background:transparent;color:var(--ink);text-align:left}.region-tree-select:hover,.region-tree-select[aria-current=true]{background:#edf6ff;color:#145f9f}.region-tree-select span{overflow:hidden;font-size:12px;font-weight:650;text-overflow:ellipsis;white-space:nowrap}.region-tree-select small{flex:0 0 auto;color:#8295a8;font-size:10px}.region-tree-toggle{width:30px;height:30px;flex:0 0 30px;border:1px solid #e3edf5;border-radius:9px;background:#fff;color:#327caf;font-size:17px;line-height:1;transition:transform .18s,background .18s}.region-tree-toggle[aria-expanded=true]{transform:rotate(180deg);background:#eef7ff}.region-tree-children{border-left:1px solid #dce9f3;margin-left:15px;padding-left:4px}.region-tree-children[hidden]{display:none}.location-crumbs,.location-up{display:none!important}.hero-place{font-size:14px}.hero-stat-note{font-size:11px}.hero-stat-note::first-letter{text-transform:uppercase}
        @media(max-width:640px){.topbar{height:auto;min-height:68px}.page{padding-top:16px}.intro h1{font-size:26px}.region-tree-row.level-district{--tree-depth:0}.region-tree-row.level-village{--tree-depth:1}.location-menu{left:auto;right:0;width:min(370px,calc(100vw - 30px))}}
        #climate-insights{padding:22px;border:1px solid #e4edf5;border-radius:24px;background:linear-gradient(155deg,#f8fbff 0%,#f3f8fc 48%,#fff9e8 100%)}#climate-insights>.section-head{align-items:center;margin-bottom:18px}.climate-summary{grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.climate-card{position:relative;min-height:126px;padding:18px;border-radius:18px;box-shadow:0 8px 22px #264d6c0b}.climate-feature{grid-column:span 1;background:linear-gradient(145deg,#fff,#edf7ff);border-color:#dceaf5}.climate-card strong{font-size:22px;line-height:1.3}.climate-feature strong{font-size:17px}.climate-kicker{font-size:10px;letter-spacing:.9px}.climate-small{font-size:12px;line-height:1.5}.climate-chart-card{margin-top:14px;padding:18px 20px;border-radius:19px;box-shadow:0 8px 24px #264d6c0b}.climate-chart-head{align-items:flex-start;font-size:12px}.climate-chart-head>div{gap:4px}.climate-chart-head strong{font-size:15px}.climate-chart-head #climate-source{font-size:11px;line-height:1.5}.climate-chart-wrap{width:100%;overflow-x:auto;margin-top:8px}.climate-chart{display:block;width:100%;min-width:650px;height:auto;overflow:visible;margin:0;padding:0}.climate-grid-line{stroke:#e8eef4;stroke-width:1}.climate-axis-line{stroke:#9aabb9;stroke-width:1.2}.climate-axis-label,.climate-date-label{fill:#70849a;font:11px 'Plus Jakarta Sans',sans-serif}.climate-date-label{font-size:10px}.climate-bar{fill:#358ec3;transition:fill .15s}.climate-bar:hover{fill:#edbd36}.climate-bar-missing{fill:#e7edf2;stroke:#c6d3dd;stroke-width:1;stroke-dasharray:2 2}.climate-legend{justify-content:flex-start;flex-wrap:wrap;gap:8px 18px;margin-top:3px;padding-top:12px;font-size:11px}.climate-legend #climate-chart-range{margin-left:auto;color:#8191a0}.rain-dot,.missing-dot{width:9px;height:9px;border-radius:50%;background:#358ec3}.missing-dot{background:#e7edf2;border:1px dashed #aab9c6}.climate-disclaimer{margin:12px 3px 0;padding:11px 13px;border:1px solid #e8edf1;border-radius:12px;background:#ffffffa8;color:#687f93;font-size:11px;line-height:1.6}.climate-chart-card[hidden],#climate-summary[hidden]{display:none}
        @media(max-width:900px){.climate-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.climate-feature{grid-column:span 2}}
        @media(max-width:640px){#climate-insights{padding:15px;border-radius:20px}.climate-summary{gap:9px}.climate-card{min-height:112px;padding:13px;border-radius:15px}.climate-card strong{font-size:18px}.climate-feature strong{font-size:15px}.climate-kicker{font-size:9px}.climate-small{font-size:10px}.climate-chart-card{padding:14px}.climate-chart-head,.climate-legend{font-size:10px}.climate-legend #climate-chart-range{width:100%;margin:0}.climate-chart{min-width:640px}}
        .climate-axis-title{fill:#4e687f;font:700 11px 'Plus Jakarta Sans',sans-serif}.metric-icon.quality-good{color:#168555;background:#e7f8ee}.metric-icon.quality-moderate{color:#9b7100;background:#fff6d7}.metric-icon.quality-sensitive{color:#b75b18;background:#fff0e2}.content-grid{grid-template-columns:1fr}.advice-list{grid-template-columns:repeat(3,minmax(0,1fr))}.advice-emoji svg{width:23px;height:23px}.hero-stat.quality-good{border-left:4px solid #36c982}.hero-stat.quality-moderate{border-left:4px solid #57b6d8}.hero-stat.quality-sensitive{border-left:4px solid #f0b83f}.hero-stat.quality-unhealthy{border-left:4px solid #e55a53}.hero-stat.quality-hazardous{border-left:4px solid #8d9398}.pm25-scale{position:relative;display:block;height:6px;margin-top:7px;border-radius:8px;background:linear-gradient(90deg,#39b66d 0 6.2%,#369bd1 6.2% 22.1%,#f1c644 22.1% 60%,#e45b51 60% 99.9%,#858a8e 99.9%)}.pm25-scale i{position:absolute;top:50%;width:9px;height:9px;border:2px solid #fff;border-radius:50%;background:#183954;transform:translate(-50%,-50%)}.pm25-scale-label{display:block;margin-top:4px;color:#fff;font-size:9px;line-height:1.4}@media(max-width:900px){.advice-list{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:640px){.advice-list{grid-template-columns:1fr}}
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
            <a class="nav-link active" href="/" aria-current="page">Beranda</a>
            <a class="nav-link" href="#prakiraan" id="nav-forecast">Prakiraan</a>
            <a class="nav-link" href="#map-section" id="nav-map">Peta</a>
            <a class="nav-link" href="#climate-insights" id="nav-climate">Iklim</a>
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
        <div class="intro">
            <div><p class="eyebrow">PRAKIRAAN CUACA LOKAL</p><h1>Langit Hari Ini</h1><p>Pantau cuaca dan rencanakan aktivitasmu dengan lebih nyaman.</p></div>
        </div>
        <div class="error-banner" id="error-banner" role="status"></div>
        <section class="compact-forecast section overview-only" id="overview-forecast" hidden>
            <div class="section-head"><div><h2 class="section-title" id="overview-title">Prakiraan 7 hari</h2><p class="section-subtitle" id="overview-subtitle">Gambaran cuaca di setiap wilayah</p></div></div>
            <div class="region-forecasts" id="region-forecasts"><div class="panel empty">Memuat prakiraan wilayah…</div></div>
            <div class="marine-card" id="marine-card" hidden style="margin-top:16px"><div class="marine-heading"><div><p class="eyebrow">Perairan sekitar kota</p><h2 class="section-title">Kondisi laut</h2></div><span class="marine-badge">Tanjungpinang</span></div><div class="marine-grid" id="marine-grid"></div><p class="marine-note" id="marine-note">Prakiraan laut bersifat informasi umum. Periksa sumber maritim resmi sebelum mengambil keputusan pelayaran.</p></div>
        </section>
        <section class="section detail-only" id="current-hero-wrap" data-anchor="prakiraan">
            <div class="section-head"><div><h2 class="section-title">Kondisi saat ini</h2><p class="section-subtitle">Suhu, keadaan langit, dan rincian cuaca di kelurahan terpilih</p></div></div>
            <div class="hero" aria-live="polite"><div class="hero-content" id="hero-content"><div class="loading">Mengambil prakiraan cuaca…</div></div></div>
        </section>
        <section class="section detail-only" id="detail-hours">
            <div class="section-head"><div><h2 class="section-title">24 jam mendatang</h2><p class="section-subtitle">Suhu dan peluang hujan per jam</p></div></div>
            <div class="hour-panel"><div class="hours" id="hours-list"><div class="empty">Memuat prakiraan per jam…</div></div></div>
        </section>
        <section class="section detail-only" id="detail-days" data-anchor="prakiraan">
            <div class="section-head"><div><h2 class="section-title">Prakiraan 7 hari</h2><p class="section-subtitle">Gambaran cuaca harian untuk seminggu ke depan</p></div></div>
            <div class="panel"><div class="days-grid" id="days-grid"><div class="day-card"><div class="empty">Menyiapkan prakiraan…</div></div></div></div>
        </section>
        <section class="section" id="map-section">
            <div class="section-head"><div><h2 class="section-title">Peta cuaca Tanjungpinang</h2><p class="section-subtitle">Geser dan perbesar peta, lalu pilih penanda untuk membuka prakiraan wilayah.</p></div><span class="map-level" id="map-level">Kecamatan</span></div>
            <div class="map-panel"><div id="weather-map" role="application" aria-label="Peta interaktif cuaca Kota Tanjungpinang"><div class="map-fallback">Memuat peta wilayah…</div></div><div class="map-legend" id="map-legend" hidden></div><div class="map-note"><span id="map-note-text">Pilih penanda wilayah untuk melihat ringkasan cuaca.</span><span>© OpenStreetMap</span></div></div>
        </section>
        <section class="section detail-only" id="activity-advice">
            <div class="section-head"><div><h2 class="section-title">Saran aktivitas</h2><p class="section-subtitle">Saran praktis berdasarkan kondisi cuaca terkini</p></div></div>
            <div class="content-grid">
                <div class="advice-list" id="advice-list"><div class="advice-card"><div class="advice-message">Menyiapkan saran aktivitas…</div></div></div>
            </div>
        </section>
        <section class="section" id="climate-insights">
            <div class="section-head"><div><h2 class="section-title">Catatan iklim wilayah</h2><p class="section-subtitle">Ringkasan historis hujan, suhu, dan hari tanpa hujan</p></div><span class="map-level" id="climate-coverage">Memuat data</span></div>
            <div class="climate-summary" id="climate-summary"><article class="climate-card climate-feature"><span class="climate-kicker">POLA HUJAN 30 HARI</span><strong id="climate-monthly-label">Menyiapkan ringkasan…</strong><span id="climate-baseline" class="climate-small"></span></article><article class="climate-card"><span class="climate-kicker">HARI TANPA HUJAN</span><strong id="climate-dry-days">—</strong><span id="climate-dry-note" class="climate-small"></span></article><article class="climate-card"><span class="climate-kicker">HUJAN 30 HARI</span><strong id="climate-rain">—</strong><span id="climate-wet-days" class="climate-small"></span></article><article class="climate-card"><span class="climate-kicker">SUHU RATA-RATA</span><strong id="climate-temperature">—</strong><span class="climate-small">30 hari historis terakhir</span></article></div>
            <div class="climate-chart-card"><div class="climate-chart-head"><div><strong>Curah hujan harian</strong><span id="climate-source"></span></div><span>30 hari terakhir</span></div><div class="climate-chart-wrap" id="climate-chart-wrap"><svg class="climate-chart" id="climate-chart" viewBox="0 0 960 270" role="img" aria-label="Grafik curah hujan harian historis"></svg></div><div class="climate-legend"><span><i class="rain-dot"></i> Curah hujan harian (mm)</span><span><i class="missing-dot"></i> Data tidak tersedia</span><span id="climate-chart-range"></span></div></div>
            <p class="climate-disclaimer" id="climate-disclaimer">Data historis berbasis reanalisis model, bukan pengukuran langsung di tiap kelurahan. Ini bukan prakiraan musim.</p>
        </section>
    </main>
    <footer class="footer"><span>Cuaca diperbarui berkala · Waktu lokal WIB (Asia/Jakarta)</span><span class="attribution"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3v18M3 12h18M5.6 5.6l12.8 12.8m0-12.8L5.6 18.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>Data cuaca dari <a href="https://open-meteo.com/" target="_blank" rel="noopener">Open-Meteo</a></span></footer>
</div>
<script>
(() => {
    const regionsUrl = @json($regionsEndpoint);
    const mapWeatherUrl = @json(url('/api/weather/map'));
    const refreshUrl = @json(url('/api/weather/refresh'));
    const cityCode = '21.72';
    const locationToggle = document.getElementById('location-toggle');
    const locationMenu = document.getElementById('location-menu');
    const errorBanner = document.getElementById('error-banner');
    let regions = [];
    let selectedCode = cityCode;
    let weatherMap = null;
    let mapMarkers = null;
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
        if ([0].includes(n)) return {label:day ? 'Cerah' : 'Cerah malam', icon:day ? icons.sun : icons.moon};
        if ([1,2].includes(n)) return {label:n === 1 ? (day ? 'Cerah berawan' : 'Cerah berawan malam') : 'Cerah berawan', icon:day ? icons.partly : (n === 1 ? icons.moon : icons.clouds)};
        if (n === 3) return {label:'Berawan', icon:icons.clouds};
        if ([45,48].includes(n)) return {label:'Berkabut', icon:icons.fog};
        if ([51,53,55,56,57].includes(n)) return {label:'Gerimis', icon:icons.drizzle};
        if ([61,63,65,66,67].includes(n)) return {label:'Hujan', icon:icons.rain};
        if ([71,73,75,77,85,86].includes(n)) return {label:'Hujan salju', icon:icons.snow};
        if ([80,81,82].includes(n)) return {label:'Hujan lokal', icon:icons.rain};
        if ([95,96,99].includes(n)) return {label:'Hujan petir', icon:icons.storm};
        return {label:'Kondisi cuaca', icon:icons.partly};
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
    function typeLabel(type) { return type === 'city' ? 'Kota' : type === 'district' ? 'Kecamatan' : 'Kelurahan'; }
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
        document.getElementById('location-name').textContent = region.type === 'city' ? region.name.replace(/^Kota\s+/i, '') : region.name;
        const chain = ancestors(region);
        document.getElementById('location-menu-title').textContent = 'Kota, kecamatan, dan kelurahan';
        const options = document.getElementById('location-options');
        options.replaceChildren();
        const city = regions.find(item => item.code === cityCode);
        if (city) appendRegionOption(options, city, chain);
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
        if (!window.L) {
            container.innerHTML = '<div class="map-fallback">Peta belum dapat dimuat. Periksa koneksi internet; pilihan wilayah tetap tersedia lewat menu Wilayah.</div>';
            document.getElementById('map-note-text').textContent = 'Peta memerlukan koneksi internet untuk memuat tampilan peta.';
            return;
        }
        const city = regions.find(region => region.code === cityCode);
        const center = city ? [Number(city.latitude), Number(city.longitude)] : [0.9167, 104.4650];
        weatherMap = L.map(container, {scrollWheelZoom:false}).setView(center, 12);
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
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors'
        }).addTo(weatherMap);
        mapMarkers = L.layerGroup().addTo(weatherMap);
        loadMapWeather();
    }
    async function loadMapWeather() {
        try {
            const response = await fetch(mapWeatherUrl, {headers:{Accept:'application/json'},cache:'no-store'});
            if (!response.ok) throw new Error('Data peta belum tersedia.');
            mapWeatherData = await response.json();
            renderWeatherMap();
        } catch (error) {
            document.getElementById('map-note-text').textContent = 'Data penanda cuaca belum dapat dimuat. Pilihan wilayah tetap tersedia di menu.';
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
    function renderWeatherMap() {
        if (!weatherMap || !mapMarkers) return;
        const region = currentRegion();
        if (!region) return;
        const allChildren = regions.filter(item => item.parent_code === region.code);
        const parent = region.type === 'village' ? regions.find(item => item.code === region.parent_code) : region;
        const markers = region.type === 'village'
            ? regions.filter(item => item.parent_code === parent?.code)
            : allChildren;
        const levelName = markers[0] ? typeLabel(markers[0].type) : typeLabel(region.type);
        document.getElementById('map-level').textContent = markers.length ? levelName : typeLabel(region.type);
        document.getElementById('map-note-text').textContent = region.type === 'city'
            ? 'Pilih penanda kecamatan untuk melihat prakiraan cuaca.'
            : region.type === 'district'
                ? `Pilih penanda kelurahan di Kecamatan ${region.name} untuk melihat prakiraannya.`
                : `Kelurahan terpilih: ${region.name}. Pilih penanda lain untuk berpindah kelurahan.`;
        mapMarkers.clearLayers();
        updateMapLegend();
        const points = [];
        markers.forEach(child => {
            const lat = Number(child.latitude), lon = Number(child.longitude);
            if (!Number.isFinite(lat) || !Number.isFinite(lon)) return;
            points.push([lat, lon]);
            const values = mapWeatherData.find(item => item.code === child.code) ?? {};
            const pin = L.divIcon({
                className: '',
                html: `<span class="map-pin${child.code === selectedCode ? ' selected' : ''}" aria-hidden="true"><span>•</span></span>`,
                iconSize: [28, 28], iconAnchor: [14, 28]
            });
            const marker = L.marker([lat, lon], {icon:pin, title:child.name, alt:`${typeLabel(child.type)} ${child.name}`});
            const popup = `<div class="map-popup"><strong>${escapeHtml(child.name)}</strong><span>${escapeHtml(condition(values.weather_code,values.is_day,values.valid_at).label)} · ${values.temperature_c == null ? '—' : `${number(values.temperature_c,1)}°C`}</span><span>Peluang hujan ${values.precipitation_probability == null ? '—' : `${number(values.precipitation_probability)}%`}</span><button type="button" data-map-open="${escapeHtml(child.code)}">Lihat detail</button></div>`;
            marker.bindPopup(popup, {closeButton:true, className:'weather-map-popup'});
            marker.on('click', () => document.getElementById('map-note-text').textContent = `${typeLabel(child.type)} ${child.name} · pilih “Lihat detail” untuk membuka prakiraan.`);
            marker.on('popupopen', event => event.popup.getElement()?.querySelector('[data-map-open]')?.addEventListener('click', () => selectRegion(child.code)));
            marker.addTo(mapMarkers);
        });
        if (points.length > 1) weatherMap.fitBounds(points, {padding:[35,35], maxZoom:region.type === 'city' ? 12 : 14});
        else if (points.length === 1) weatherMap.setView(points[0], 13);
        else if (region.latitude && region.longitude) weatherMap.setView([Number(region.latitude), Number(region.longitude)], 12);
    }
    function isVillage(region) { return region?.type === 'village'; }
    function setViewMode(region) {
        const detail = isVillage(region);
        document.querySelectorAll('.detail-only').forEach(element => { element.hidden = !detail; });
        const overview = document.getElementById('overview-forecast');
        overview.hidden = detail;
        overview.classList.toggle('show', !detail);
        document.getElementById('overview-title').textContent = region.type === 'city' ? 'Prakiraan 7 hari per kecamatan' : 'Prakiraan 7 hari per kelurahan';
        document.getElementById('overview-subtitle').textContent = region.type === 'city'
            ? `Gambaran cuaca di kecamatan ${region.name}`
            : `Gambaran cuaca di kelurahan Kecamatan ${region.name}`;
        document.getElementById('marine-card').hidden = region.type !== 'city';
        document.getElementById('nav-forecast').href = region.type === 'village' ? '#current-hero-wrap' : '#overview-forecast';
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

        return new Promise(resolve => navigator.geolocation.getCurrentPosition(position => {
            const {latitude, longitude} = position.coords;
            const city = regions.find(region => region.code === cityCode);
            const villages = regions.filter(region => region.type === 'village');
            const nearest = villages
                .map(region => ({region, distance:distanceKm(latitude, longitude, Number(region.latitude), Number(region.longitude))}))
                .sort((a, b) => a.distance - b.distance)[0];

            if (!city || !nearest || distanceKm(latitude, longitude, Number(city.latitude), Number(city.longitude)) > 12) {
                selectedCode = cityCode;
                renderLocationPicker();
                geoStatus('Lokasimu di luar cakupan Kota Tanjungpinang. Prakiraan kota ditampilkan.');
                button.disabled = false;
                button.textContent = '⌖ Gunakan lokasi saya';
                loadWeather();
                resolve(false);
                return;
            }

            selectedCode = nearest.region.code;
            renderLocationPicker();
            geoStatus('');
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
        }, {enableHighAccuracy:false, timeout:10000, maximumAge:300000}));
    }

    function renderHero(data) {
        const region = data.wilayah;
        const current = data.cuaca_saat_ini;
        const info = condition(current?.weather_code, current?.is_day, current?.valid_at);
        const timestamp = current?.valid_at ? new Date(current.valid_at) : null;
        const place = ancestors(region).filter(item => item.type !== 'city' || item.name).map(item => item.type === 'city' ? item.name.replace(/^Kota\s+/i, '') : item.name).join(' · ');
        const pm25 = current?.pm2_5_ug_m3 == null ? null : Number(current.pm2_5_ug_m3);
        const airClass = classifyPm25(pm25);
        document.getElementById('hero-content').innerHTML = `
            <div class="hero-main"><div class="hero-place">${escapeHtml(place)}</div><div class="hero-date">${timestamp ? new Intl.DateTimeFormat('id-ID',{weekday:'long',day:'numeric',month:'long',timeZone:'Asia/Jakarta'}).format(timestamp) : 'Data cuaca terbaru'}</div>
            <div class="temp-row"><span class="weather-symbol">${info.icon}</span><div class="temperature">${number(current?.temperature_c)}<sup>°</sup></div></div><div class="condition">${info.label}</div><div class="feels">Terasa seperti ${number(current?.apparent_temperature_c)}°C</div></div>
            <div class="hero-summary">
                ${heroStat('Kelembapan','humidity', `${number(current?.relative_humidity)}%`, Number(current?.relative_humidity) >= 75 ? 'Terasa lembap' : 'Kelembapan udara')}
                ${heroStat('Curah hujan','rain', `${number(current?.rain_mm,1)} mm`, `Peluang hujan ${number(data.prakiraan_7_hari?.[0]?.precipitation_probability)}%`)}
                ${heroStat('Jarak pandang','visibility', current?.visibility_m == null ? '—' : `${number(current.visibility_m / 1000,1)} km`, 'Perkiraan jarak pandang')}
                ${heroStat('Kualitas udara','air', pm25 == null ? '—' : `${number(pm25,1)} µg/m³`, `Partikulat PM2.5 · ${airClass.label}${qualityIndicator(pm25)}`, `quality-${airClass.tone}`)}
            </div>`;
        const freshness = data.ditarik_pada ? new Date(data.ditarik_pada) : (data.diperbarui_pada ? new Date(data.diperbarui_pada) : null);
        document.getElementById('live-label').textContent = freshness ? `Diperbarui ${localHour(freshness)}` : 'Menunggu data cuaca';
    }
    function classifyPm25(value) {
        if (value == null || !Number.isFinite(value)) return {label:'Belum tersedia', tone:'unknown'};
        if (value <= 15.5) return {label:'Baik', tone:'good'};
        if (value <= 55.4) return {label:'Sedang', tone:'moderate'};
        if (value <= 150.4) return {label:'Tidak sehat', tone:'sensitive'};
        if (value <= 250.5) return {label:'Sangat tidak sehat', tone:'unhealthy'};
        return {label:'Berbahaya', tone:'hazardous'};
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
    function qualityIndicator(pm25) {
        if (pm25 == null || !Number.isFinite(pm25)) return '<span class="pm25-scale-label">Kategori berdasarkan konsentrasi PM2.5 (µg/m³)</span>';
        const position = Math.max(0, Math.min(100, Number(pm25) / 250.5 * 100));
        return `<span class="pm25-scale"><i style="left:${position}%"></i></span><span class="pm25-scale-label">Baik ≤15,5 · Sedang 15,6–55,4 · Tidak sehat 55,5–150,4 · Sangat tidak sehat 150,5–250,5 · Berbahaya >250,5 µg/m³</span>`;
    }
    function heroStat(label, icon, value, note, extraClass='') { return `<div class="hero-stat ${extraClass}"><div class="hero-stat-label"><span class="hero-stat-icon">${metricIcon(icon)}</span>${label}</div><div class="hero-stat-value">${value}</div><div class="hero-stat-note">${note}</div></div>`; }
    function escapeHtml(value) { const node = document.createElement('span'); node.textContent = value ?? ''; return node.innerHTML; }

    function renderMetrics(current) {
        const pm25 = current?.pm2_5_ug_m3 == null ? null : Number(current.pm2_5_ug_m3);
        const air = classifyPm25(pm25);
        const metrics = [
            ['Kelembapan', 'humidity', `${number(current?.relative_humidity)}%`, Number(current?.relative_humidity) >= 75 ? 'Udara terasa lembap' : 'Kondisi kelembapan udara'],
            ['Curah hujan', 'rain', `${number(current?.rain_mm,1)} mm`, 'Dalam periode pengamatan'],
            ['Jarak pandang', 'visibility', current?.visibility_m == null ? '—' : `${number(current.visibility_m / 1000,1)} km`, 'Perkiraan jarak pandang'],
            ['Kualitas udara', 'air', pm25 == null ? '—' : `${number(pm25,1)} µg/m³`, `PM2.5 · ${air.label}${qualityIndicator(pm25)}`],
        ];
        document.getElementById('metric-grid').innerHTML = metrics.map(([label, icon, value, note]) => `<article class="metric-card"><div class="metric-top"><span>${label}</span><span class="metric-icon ${label === 'Kualitas udara' ? `quality-${air.tone}` : ''}">${metricIcon(icon)}</span></div><div class="metric-value">${value}</div><div class="metric-note">${note}</div></article>`).join('');
    }
    function metricIcon(name) {
        const paths = {
            humidity:'<path d="M12 3s6 6.6 6 11a6 6 0 0 1-12 0c0-4.4 6-11 6-11Z"/><path d="M9 15a3 3 0 0 0 3 3"/>',
            wind:'<path d="M3 8h13a3 3 0 1 0-3-3M2 12h17a2.5 2.5 0 1 1-2.5 2.5M4 16h8"/>',
            rain:'<path d="M7 16v4m5-4v4m5-4v4"/><path d="M6.5 14a4.5 4.5 0 0 1-.3-9A6 6 0 0 1 17 7a3.5 3.5 0 0 1 .5 7H6.5Z"/>',
            visibility:'<path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/>',
            air:'<path d="M5 8h.01M12 5h.01M18 9h.01M8 14h.01M16 16h.01" stroke-width="3"/><circle cx="12" cy="12" r="9"/>',
        };
        return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${paths[name]}</svg>`;
    }

    function renderDays(forecast) {
        if (!forecast?.length) { document.getElementById('days-grid').innerHTML = '<div class="empty">Prakiraan belum tersedia.</div>'; return; }
        const groups = new Map();
        forecast.forEach(item => {
            const date = dateParts(item.valid_at);
            const key = new Intl.DateTimeFormat('en-CA',{timeZone:'Asia/Jakarta',year:'numeric',month:'2-digit',day:'2-digit'}).format(date);
            if (!groups.has(key)) groups.set(key, []);
            groups.get(key).push(item);
        });
        const days = [...groups.values()].slice(0,7);
        document.getElementById('days-grid').innerHTML = days.map((items,index) => {
            const temps = items.map(item => Number(item.temperature_c)).filter(Number.isFinite);
            const midday = [...items].sort((a,b) => Math.abs(Number(a.valid_at.slice(11,13))-13)-Math.abs(Number(b.valid_at.slice(11,13))-13))[0];
            const chance = Math.max(...items.map(item => Number(item.precipitation_probability ?? 0)));
            const min = Math.min(...temps), max = Math.max(...temps);
            const info = condition(midday?.weather_code, midday?.is_day, midday?.valid_at);
            const date = dateParts(midday?.valid_at ?? items[0].valid_at);
            const dateLabel = new Intl.DateTimeFormat('id-ID',{timeZone:'Asia/Jakarta',weekday:'short',day:'numeric',month:'short'}).format(date);
            return `<article class="day-card ${index===0?'today':''}"><div class="day-name">${dateLabel}</div>${index===0?'<span class="today-chip">Hari ini</span>':''}<div class="day-icon">${info.icon}</div><div class="day-condition">${info.label}</div><div class="day-temps"><span>${number(max)}°</span><span class="low">${number(min)}°</span></div><div class="rain-chance">💧 ${chance}% peluang hujan</div></article>`;
        }).join('');
    }

    function dailySummaries(forecast) {
        const groups = new Map();
        (forecast ?? []).forEach(item => {
            const date = dateParts(item.valid_at);
            const key = new Intl.DateTimeFormat('en-CA',{timeZone:'Asia/Jakarta',year:'numeric',month:'2-digit',day:'2-digit'}).format(date);
            if (!groups.has(key)) groups.set(key, []);
            groups.get(key).push(item);
        });
        return [...groups.values()].slice(0,7).map((items,index) => {
            const temps = items.map(item => Number(item.temperature_c)).filter(Number.isFinite);
            const midday = [...items].sort((a,b) => Math.abs(Number(a.valid_at.slice(11,13))-13)-Math.abs(Number(b.valid_at.slice(11,13))-13))[0] ?? items[0];
            const info = condition(midday?.weather_code, midday?.is_day, midday?.valid_at);
            return {index, date:dateParts(midday.valid_at), info,
                high:temps.length ? Math.max(...temps) : null, low:temps.length ? Math.min(...temps) : null,
                chance:Math.max(...items.map(item => Number(item.precipitation_probability ?? 0)))};
        });
    }

    async function renderRegionForecasts(region) {
        const children = regions.filter(item => item.parent_code === region.code);
        const container = document.getElementById('region-forecasts');
        container.innerHTML = '<div class="panel empty">Memuat prakiraan 7 hari…</div>';
        if (!children.length) {
            container.innerHTML = '<div class="panel empty">Belum ada wilayah di bawahnya.</div>';
            return;
        }
        const results = await Promise.all(children.map(async child => {
            const response = await fetch(`${regionsUrl}/${encodeURIComponent(child.code)}`, {headers:{Accept:'application/json'},cache:'no-store'});
            if (!response.ok) return {child, days:[]};
            const data = await response.json();
            return {child, days:dailySummaries(data.prakiraan_7_hari)};
        }));
        container.innerHTML = results.map(({child,days}) => `
            <article class="region-forecast">
                <div class="region-forecast-head"><div><div class="region-forecast-name">${escapeHtml(child.name)}</div><div class="region-forecast-type">${typeLabel(child.type)} · 7 hari ke depan</div></div>
                    <button type="button" class="region-forecast-open" data-region-code="${escapeHtml(child.code)}">Lihat ${child.type === 'district' ? 'kelurahan' : 'detail'} <span aria-hidden="true">›</span></button>
                </div>
                ${days.length ? `<div class="region-day-row">${days.map(day => `<div class="region-day"><div class="region-day-date">${new Intl.DateTimeFormat('id-ID',{timeZone:'Asia/Jakarta',weekday:'short',day:'numeric',month:'short'}).format(day.date)}</div><div class="region-day-icon">${day.info.icon}</div><div class="region-day-condition">${day.info.label}</div><div class="region-day-temp">${number(day.high)}° <span style="color:#71879a;font-weight:500">${number(day.low)}°</span></div><div class="region-day-rain">💧 ${number(day.chance)}% hujan</div></div>`).join('')}</div>` : '<div class="empty">Prakiraan belum tersedia.</div>'}
            </article>`).join('');
        container.querySelectorAll('[data-region-code]').forEach(button => button.addEventListener('click', () => selectRegion(button.dataset.regionCode)));
    }

    function renderHours(forecast) {
        const now = Date.now() - 60 * 60 * 1000;
        const upcoming = (forecast ?? []).filter(item => dateParts(item.valid_at).getTime() >= now).slice(0,24);
        const rows = upcoming.length ? upcoming : (forecast ?? []).slice(0,24);
        document.getElementById('hours-list').innerHTML = rows.map((item,index) => {
            const info = condition(item.weather_code, item.is_day, item.valid_at);
            const date = dateParts(item.valid_at);
            const chance = Number(item.precipitation_probability ?? 0);
            return `<div class="hour-card"><div class="hour-time">${index===0?'Sekarang':localHour(date)}</div><div class="hour-icon">${info.icon}</div><div class="hour-temp">${number(item.temperature_c)}°</div><div class="hour-rain">💧 ${number(chance)}%</div></div>`;
        }).join('') || '<div class="empty">Prakiraan per jam belum tersedia.</div>';
    }

    const adviceTitles = {
        olahraga_outdoor:['M30 14 18 26l-6 18 9-3 5 14 6-1-4-16 12-5-14-5 4-14Z','Olahraga luar ruang'],
        berkendara:['M5 18h14l2 5v13H3V23l2-5Zm2 0 3-8h10l3 8M6 28h.01M17 28h.01','Berkendara'],
        berkebun:['M12 21c0-9 5-14 15-15-1 10-6 15-15 15Zm0 0v11m0-8c-5-7-10-8-14-8 1 7 5 10 14 10','Berkebun'],
        menjemur_pakaian:['M4 8h24M7 8v22m18-22v22M8 30l5-10 5 10m-1-7h8m-8 4h8','Menjemur pakaian'],
        piknik_pantai:['M3 27h26M5 27l12-17 12 17M17 10v17M4 32c4-3 7 3 11 0s7 3 12 0','Piknik di pantai'],
        bersepeda:['M8 27a5 5 0 1 0 0 .01M25 27a5 5 0 1 0 0 .01M8 27l6-11 6 11H8l5-8h8l4 8m-11-15h4','Bersepeda'],
        melaut_menyeberang:['M3 23h27l-4 9H9l-6-9Zm5-3 5-10h8l6 10M17 10V5m-13 30c4-3 7 3 11 0s7 3 12 0','Melaut / menyeberang'],
        kualitas_udara:['M4 13h16a4 4 0 1 0-4-4M3 19h21a4 4 0 1 1-4 4M6 25h10','Kualitas udara'],
        penyeberangan_pompong:['M3 24h28l-4 8H9l-6-8Zm7-3 5-9h8l5 9M17 12V6','Penyeberangan pompong'],
        nelayan_kelong:['M3 24h28l-4 8H9l-6-8Zm14-13v13m-7-13h14M5 35c4-3 7 3 11 0s7 3 12 0','Nelayan tradisional & kelong'],
        penjemuran_hasil_laut:['M4 8h24M7 8v22m18-22v22M8 30l5-10 5 10m-1-7h8m-8 4h8','Penjemuran hasil laut'],
        penerbangan_rhf:['M18 3 15 15 5 21l1 3 11-3 5 8 3-1-3-9 7-5-1-2-9 3 2-12Z','Penerbangan Bandara RHF'],
        komuter_roda_dua:['M7 27a5 5 0 1 0 0 .01M25 27a5 5 0 1 0 0 .01M7 27l6-11h7l5 11M13 16l-3 11h15','Komuter roda dua'],
        kuliner_tepi_laut:['M4 27h26M6 27l11-17 11 17M17 10v17M5 33c4-3 7 3 11 0s7 3 12 0','Kuliner tepi laut'],
        olahraga_rekreasi:['M18 5a3 3 0 1 0 0 .01M18 10l-7 8 7 4 5-6m-12 0-5 8m12-4 4 9m-7-11-4 11','Olahraga & rekreasi'],
        konstruksi_lapangan:['M7 30 22 7l5 4-15 23Zm-3 3h25M9 20l7 5','Proyek & konstruksi'],
        jemuran_pakaian:['M4 8h24M7 8v22m18-22v22M8 30l5-10 5 10m-1-7h8m-8 4h8','Jemuran pakaian'],
    };
    function renderAdvice(advice) {
        const entries = Object.entries(advice ?? {});
        document.getElementById('advice-list').innerHTML = entries.length ? entries.map(([key,item]) => {
            const [path,title] = adviceTitles[key] ?? ['M17 4v28M3 18h28','Aktivitas'];
            const state = item.status === 'waspada' ? 'waspada' : item.status === 'saran' ? 'saran' : item.status === 'informasi' ? 'informasi' : '';
            return `<article class="advice-card"><div class="advice-emoji"><svg viewBox="0 0 34 36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="${path}"/></svg></div><div><div class="advice-title">${title}</div><div class="advice-message">${escapeHtml(item.message)}</div><div class="advice-reason">${escapeHtml(item.reason ?? '')}</div><div class="status ${state}">${escapeHtml(item.status)}</div></div></article>`;
        }).join('') : '<div class="advice-card"><div class="advice-message">Saran aktivitas belum tersedia.</div></div>';
    }
    function renderMarine(data) {
        const panel = document.getElementById('marine-card');
        if (!data || !data.saat_ini) { panel.hidden = true; return; }
        panel.hidden = false;
        const current = data.saat_ini;
        const items = [
            ['Tinggi gelombang',current.wave_height_m == null ? '—' : `${number(current.wave_height_m,1)} m`],
            ['Periode gelombang',current.wave_period_s == null ? '—' : `${number(current.wave_period_s,1)} dtk`],
            ['Arah gelombang',current.wave_direction_deg == null ? '—' : `${compass(current.wave_direction_deg)} ${number(current.wave_direction_deg)}°`],
            ['Suhu permukaan laut',current.sea_surface_temperature_c == null ? '—' : `${number(current.sea_surface_temperature_c,1)}°C`],
        ];
        document.getElementById('marine-grid').innerHTML = items.map(([label,value]) => `<div class="marine-metric"><div class="marine-label">${label}</div><div class="marine-value">${value}</div></div>`).join('');
        document.getElementById('marine-note').textContent = data.catatan || 'Informasi laut bersifat umum; periksa sumber maritim resmi sebelum berlayar.';
    }

    async function loadWeather() {
        const refresh = document.getElementById('refresh-button');
        refresh.disabled = true;
        document.getElementById('live-label').textContent = 'Memuat data cuaca…';
        errorBanner.classList.remove('show');
        try {
            const region = currentRegion();
            if (!region) throw new Error('Wilayah tidak ditemukan.');
            setViewMode(region);
            if (!isVillage(region)) {
                await renderRegionForecasts(region);
                const freshnessResponse = await fetch(`${regionsUrl}/${encodeURIComponent(region.code)}`, {headers:{Accept:'application/json'},cache:'no-store'});
                if (freshnessResponse.ok) {
                    const overviewData = await freshnessResponse.json();
                    const freshness = overviewData.diperbarui_pada ? new Date(overviewData.diperbarui_pada) : null;
                    const pulledAt = overviewData.ditarik_pada ? new Date(overviewData.ditarik_pada) : freshness;
                    document.getElementById('live-label').textContent = pulledAt ? `Diperbarui ${localHour(pulledAt)}` : 'Menunggu data cuaca';
                    renderMarine(region.type === 'city' ? overviewData.kondisi_laut_kota : null);
                } else throw new Error('Waktu pembaruan belum dapat dimuat.');
                await loadClimateForSelectedRegion(region);
                return;
            }
            const response = await fetch(`${regionsUrl}/${encodeURIComponent(selectedCode)}`, {headers:{Accept:'application/json'},cache:'no-store'});
            if (!response.ok) throw new Error(response.status === 404 ? 'Wilayah belum memiliki data.' : 'Data cuaca belum dapat dimuat.');
            const data = await response.json();
            await loadClimateForSelectedRegion(region);
            renderHero(data);
            renderDays(data.prakiraan_7_hari);
            renderHours(data.prakiraan_7_hari);
            renderAdvice(data.saran_aktivitas);
            renderMarine(null);
        } catch (error) {
            errorBanner.textContent = `${error.message} Pastikan sinkronisasi pertama sudah selesai.`;
            errorBanner.classList.add('show');
            document.getElementById('hero-content').innerHTML = '<div class="loading">Data cuaca belum tersedia untuk wilayah ini.</div>';
        } finally { refresh.disabled = false; }
    }

    function renderClimate(climate) {
        const summary = document.getElementById('climate-summary');
        const chart = document.getElementById('climate-chart');
        if (!climate?.tersedia) {
            summary.innerHTML = '<article class="climate-card climate-feature"><span class="climate-kicker">DATA HISTORIS</span><strong>Belum tersedia</strong><span class="climate-small">Jalankan sinkronisasi riwayat iklim untuk wilayah ini.</span></article>';
            chart.innerHTML = '<span class="climate-small">Belum ada seri historis untuk ditampilkan.</span>';
            document.getElementById('climate-coverage').textContent = 'Belum tersedia';
            return;
        }
        document.getElementById('climate-coverage').textContent = `Data sampai ${new Intl.DateTimeFormat('id-ID',{timeZone:'Asia/Jakarta',day:'numeric',month:'short',year:'numeric'}).format(dateParts(climate.data_sampai))}`;
        document.getElementById('climate-monthly-label').textContent = climate.wilayah_agregat ? 'Pola historis kelurahan' : climate.pola_bulan_ini;
        document.getElementById('climate-baseline').textContent = climate.normal_hujan_bulan_ini_mm == null ? `Hujan 30 hari: ${number(climate.hujan_30_hari_mm,1)} mm. Normal historis belum cukup untuk pembanding.` : `Hujan 30 hari ${number(climate.hujan_30_hari_mm,1)} mm · normal bulanan ${number(climate.normal_hujan_bulan_ini_mm,1)} mm (${climate.rentang_normal_tahun})`;
        document.getElementById('climate-dry-days').textContent = climate.wilayah_agregat ? 'Per kelurahan' : (climate.hari_tanpa_hujan == null ? 'Belum diketahui' : `${climate.hari_tanpa_hujan} hari`);
        document.getElementById('climate-dry-note').textContent = climate.wilayah_agregat ? `${climate.jumlah_kelurahan} dari ${climate.wilayah_cakupan} kelurahan memiliki data. ${climate.definisi_hth}` : `Dihitung s.d. ${climate.dianalisis_sampai}; ${climate.definisi_hth}`;
        document.getElementById('climate-rain').textContent = `${number(climate.hujan_30_hari_mm,1)} mm`;
        document.getElementById('climate-wet-days').textContent = `${climate.hari_basah_30_hari} hari dengan hujan ≥ 1 mm`;
        document.getElementById('climate-temperature').textContent = climate.suhu_rata_rata_30_hari_c == null ? '—' : `${number(climate.suhu_rata_rata_30_hari_c,1)}°C`;
        document.getElementById('climate-source').textContent = climate.sumber;
        const series = climate.seri_30_hari ?? [];
        document.querySelector('.climate-chart-card').hidden = Boolean(climate.wilayah_agregat);
        const plot = {left:58, top:16, width:884, height:194};
        const values = series.map(row => row.hujan_mm == null ? null : Number(row.hujan_mm)).filter(value => value !== null && Number.isFinite(value));
        const maxValue = Math.max(0, ...values);
        const tickStep = maxValue <= 20 ? 5 : Math.ceil(maxValue / 4 / 5) * 5;
        const axisMax = Math.max(tickStep, Math.ceil(maxValue / tickStep) * tickStep);
        const stepX = series.length ? plot.width / series.length : plot.width;
        const barWidth = Math.min(18, stepX * .62);
        let svg = `<line class="climate-axis-line" x1="${plot.left}" y1="${plot.top}" x2="${plot.left}" y2="${plot.top + plot.height}"/><line class="climate-axis-line" x1="${plot.left}" y1="${plot.top + plot.height}" x2="${plot.left + plot.width}" y2="${plot.top + plot.height}"/><text class="climate-axis-title" x="${plot.left}" y="11">Curah hujan (mm)</text><text class="climate-axis-title" x="${plot.left + plot.width / 2}" y="266" text-anchor="middle">Tanggal</text>`;
        for (let value = 0; value <= axisMax; value += tickStep) {
            const y = plot.top + plot.height - (value / axisMax) * plot.height;
            svg += `<line class="climate-grid-line" x1="${plot.left}" y1="${y}" x2="${plot.left + plot.width}" y2="${y}"/><text class="climate-axis-label" x="${plot.left - 10}" y="${y + 4}" text-anchor="end">${value}</text>`;
        }
        const dateStep = Math.max(1, Math.ceil(series.length / 6));
        series.forEach((row, index) => {
            const value = row.hujan_mm == null || !Number.isFinite(Number(row.hujan_mm)) ? null : Number(row.hujan_mm);
            const x = plot.left + index * stepX + (stepX - barWidth) / 2;
            const height = value === null ? 5 : Math.max(value > 0 ? 2 : 1, value / axisMax * plot.height);
            const y = plot.top + plot.height - height;
            const date = new Intl.DateTimeFormat('id-ID',{timeZone:'Asia/Jakarta',day:'numeric',month:'short'}).format(dateParts(row.tanggal));
            svg += `<rect class="${value === null ? 'climate-bar-missing' : 'climate-bar'}" x="${x}" y="${y}" width="${barWidth}" height="${height}" rx="3"><title>${escapeHtml(date)} · ${value === null ? 'data tidak tersedia' : `${number(value,1)} mm`}</title></rect>`;
            if (index % dateStep === 0 || index === series.length - 1) svg += `<text class="climate-date-label" x="${x + barWidth / 2}" y="${plot.top + plot.height + 23}" text-anchor="middle">${escapeHtml(date)}</text>`;
        });
        chart.innerHTML = svg;
        document.getElementById('climate-chart-range').textContent = series.length ? `${series[0].tanggal} — ${series[series.length - 1].tanggal}` : 'Belum ada seri historis';
        document.getElementById('climate-disclaimer').textContent = climate.wilayah_agregat
            ? 'Angka hujan dan suhu merupakan rata-rata ringkasan kelurahan; HTH ditampilkan per kelurahan agar variasi lokal tidak tertutup oleh rata-rata.'
            : `Data historis berbasis reanalisis model, bukan pengukuran langsung di tiap kelurahan. ${climate.definisi_hth}`;
    }

    async function loadClimateForSelectedRegion(region) {
        const climateEndpoint = @json(url('/api/climate/summary'));
        try {
            const response = await fetch(`${climateEndpoint}/${encodeURIComponent(region.code)}`, {headers:{Accept:'application/json'},cache:'no-store'});
            if (!response.ok) throw new Error('Ringkasan iklim belum dapat dimuat.');
            renderClimate(await response.json());
        } catch (error) {
            document.getElementById('climate-coverage').textContent = 'Gagal memuat';
            document.getElementById('climate-monthly-label').textContent = 'Data iklim belum dapat dimuat';
            document.getElementById('climate-baseline').textContent = 'Coba muat ulang halaman setelah sinkronisasi historis selesai.';
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
                headers:{Accept:'application/json', 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content ?? ''},
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
    function navigateTo(event, targetId) {
        event.preventDefault();
        const target = document.getElementById(targetId);
        target.scrollIntoView({behavior:'smooth', block:'start'});
        document.querySelectorAll('.main-nav > a.nav-link').forEach(link => link.classList.remove('active'));
        event.currentTarget.classList.add('active');
    }
    document.getElementById('nav-climate').addEventListener('click', event => navigateTo(event, 'climate-insights'));
    document.getElementById('nav-map').addEventListener('click', event => navigateTo(event, 'map-section'));
    document.getElementById('nav-forecast').addEventListener('click', event => navigateTo(event, currentRegion()?.type === 'village' ? 'current-hero-wrap' : 'overview-forecast'));
    start();
    window.setInterval(loadWeather, 5 * 60 * 1000);
})();
</script>
</body>
</html>
