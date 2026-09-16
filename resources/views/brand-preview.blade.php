<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Preview Varian Branding — Asset Tagging</title>
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; background: #f1f5f9; color: #0f172a; }
  .wrap { max-width: 1080px; margin: 0 auto; padding: 32px 20px 80px; }
  .hero { text-align: center; margin-bottom: 12px; }
  .hero h1 { font-size: 28px; font-weight: 800; letter-spacing: -0.02em; }
  .hero p { color: #475569; margin-top: 8px; font-size: 14px; }
  .nav { position: sticky; top: 0; z-index: 50; background: rgb(241 245 249 / 0.92); backdrop-filter: blur(8px);
         display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; padding: 12px 0; margin: 16px 0 28px; }
  .nav a { font-size: 12px; font-weight: 700; padding: 8px 14px; border-radius: 999px; background: #fff;
           border: 1px solid #e2e8f0; color: #0f172a; text-decoration: none; }
  .nav a:hover { border-color: #0066ff; color: #0066ff; }
  section.variant { background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; margin-bottom: 32px; }
  .vhead { display: flex; align-items: center; gap: 12px; padding: 18px 24px; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap; }
  .vnum { font-size: 12px; font-weight: 800; background: #0f172a; color: #fff; border-radius: 8px; padding: 4px 10px; }
  .vhead h2 { font-size: 17px; font-weight: 800; }
  .vhead small { color: #64748b; font-size: 12px; }
  .tokens { display: flex; gap: 8px; flex-wrap: wrap; padding: 14px 24px; border-top: 1px solid #e2e8f0; background: #f8fafc; }
  .tokens code { font-size: 11px; font-weight: 700; background: #0f172a; color: #fff; padding: 4px 10px; border-radius: 999px; }
  .tokens code.l { background: #fff; color: #0f172a; border: 1px solid #e2e8f0; }
  .stage { min-height: 560px; display: flex; align-items: center; justify-content: center; padding: 48px 20px; position: relative; overflow: hidden; }

  /* ===== mock login bersama ===== */
  .mock { width: 380px; max-width: 100%; padding: 32px 30px; position: relative; }
  .mock .m-logo { display: flex; justify-content: center; margin-bottom: 14px; }
  .mock .m-badge { display: inline-flex; align-items: center; gap: 7px; font-size: 11px; font-weight: 700;
                   letter-spacing: 0.08em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px; }
  .mock .m-dot { width: 7px; height: 7px; border-radius: 999px; }
  .mock .m-center { text-align: center; margin-bottom: 20px; }
  .mock h3 { font-size: 21px; font-weight: 800; letter-spacing: -0.01em; margin: 14px 0 6px; }
  .mock .m-sub { font-size: 13px; line-height: 1.6; }
  .mock label { display: block; font-size: 12px; font-weight: 700; margin: 14px 0 6px; }
  .mock .m-in { border: 1px solid; padding: 11px 14px; font-size: 13px; color: #94a3b8; }
  .mock .m-row { display: flex; justify-content: space-between; align-items: center; margin: 14px 0 18px; font-size: 12px; }
  .mock .m-btn { display: block; width: 100%; text-align: center; font-size: 14px; font-weight: 700; padding: 12px; cursor: default; }
  .mock .m-foot { display: flex; justify-content: center; gap: 12px; margin-top: 18px; font-size: 11px; font-weight: 600; }

  /* ===== V1: Official Blue (induk) ===== */
  .stage.v1 { background: radial-gradient(42rem 26rem at 12% -6%, rgb(0 102 255 / 0.12), transparent 60%),
              radial-gradient(38rem 24rem at 95% 8%, rgb(2 132 199 / 0.10), transparent 62%),
              linear-gradient(180deg, #eef6ff 0%, #ffffff 45%, #f8fafc 100%); }
  .v1 .mock { background: rgb(255 255 255 / 0.9); border: 1px solid #e2e8f0; border-radius: 20px;
              box-shadow: 0 12px 32px -12px rgb(15 23 42 / 0.18), 0 24px 64px -24px rgb(0 102 255 / 0.22); }
  .v1 .m-badge { color: #0f172a; background: linear-gradient(180deg, #eef6ff, #dbeafe); border: 1px solid #bfdbfe; }
  .v1 .m-dot { background: #16a34a; box-shadow: 0 0 0 4px rgb(22 163 74 / 0.18); }
  .v1 h3 { color: #0f172a; } .v1 .m-sub, .v1 label { color: #475569; }
  .v1 .m-in { border-color: #cbd5e1; border-radius: 12px; background: #fff; }
  .v1 .m-row { color: #475569; }
  .v1 .m-btn { background: #0066ff; color: #fff; border-radius: 999px; box-shadow: 0 8px 20px -8px rgb(0 102 255 / 0.6); }
  .v1 .m-foot { color: #64748b; }

  /* ===== V2: Midnight Gold ===== */
  .stage.v2 { background: radial-gradient(40rem 24rem at 85% -5%, rgb(245 158 11 / 0.16), transparent 60%),
              radial-gradient(36rem 22rem at 8% 110%, rgb(245 158 11 / 0.08), transparent 60%),
              linear-gradient(180deg, #0f172a, #1e293b 60%, #0f172a); }
  .v2 .mock { background: rgb(255 255 255 / 0.06); border: 1px solid rgb(245 158 11 / 0.35); border-radius: 20px;
              box-shadow: 0 0 0 1px rgb(255 255 255 / 0.05) inset, 0 24px 64px -24px rgb(0 0 0 / 0.8); backdrop-filter: blur(12px); }
  .v2 .m-mono { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center;
                font-weight: 800; font-size: 22px; color: #0f172a;
                background: linear-gradient(135deg, #fde68a, #f59e0b 60%, #b45309); box-shadow: 0 8px 24px -8px rgb(245 158 11 / 0.7); }
  .v2 .m-badge { color: #fde68a; background: rgb(245 158 11 / 0.12); border: 1px solid rgb(245 158 11 / 0.4); }
  .v2 .m-dot { background: #f59e0b; box-shadow: 0 0 0 4px rgb(245 158 11 / 0.2); }
  .v2 h3 { color: #fff; } .v2 .m-sub { color: #cbd5e1; } .v2 label { color: #e2e8f0; }
  .v2 .m-in { border-color: #334155; border-radius: 12px; background: rgb(255 255 255 / 0.05); }
  .v2 .m-row { color: #cbd5e1; }
  .v2 .m-btn { background: linear-gradient(180deg, #fbbf24, #d97706); color: #0f172a; border-radius: 999px;
               box-shadow: 0 8px 20px -8px rgb(245 158 11 / 0.7); }
  .v2 .m-foot { color: #94a3b8; }

  /* ===== V3: Ember Night ===== */
  .stage.v3 { background: radial-gradient(38rem 22rem at 50% -8%, rgb(249 115 22 / 0.20), transparent 60%),
              radial-gradient(30rem 20rem at 95% 105%, rgb(239 68 68 / 0.14), transparent 60%), #0c0a09; }
  .v3 .mock { background: #1c1917; border: 1px solid #44403c; border-radius: 16px;
              box-shadow: 0 24px 64px -24px rgb(0 0 0 / 0.9), 0 0 40px -18px rgb(249 115 22 / 0.5); }
  .v3 .m-mono { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
                font-weight: 800; font-size: 22px; color: #fff;
                background: linear-gradient(135deg, #fb923c, #ef4444); box-shadow: 0 8px 24px -8px rgb(249 115 22 / 0.8); }
  .v3 .m-badge { color: #fed7aa; background: rgb(249 115 22 / 0.12); border: 1px solid rgb(249 115 22 / 0.4); }
  .v3 .m-dot { background: #fb923c; box-shadow: 0 0 0 4px rgb(251 146 60 / 0.2); }
  .v3 h3 { color: #fafaf9; } .v3 .m-sub { color: #a8a29e; } .v3 label { color: #e7e5e4; }
  .v3 .m-in { border-color: #57534e; border-radius: 12px; background: #292524; }
  .v3 .m-row { color: #a8a29e; }
  .v3 .m-btn { background: linear-gradient(90deg, #f97316, #ef4444); color: #fff; border-radius: 12px;
               box-shadow: 0 8px 20px -8px rgb(249 115 22 / 0.8); }
  .v3 .m-foot { color: #78716c; }

  /* ===== V4: ESG Forest ===== */
  .stage.v4 { background: radial-gradient(40rem 24rem at 10% -6%, rgb(16 185 129 / 0.22), transparent 60%),
              radial-gradient(34rem 22rem at 95% 108%, rgb(5 150 105 / 0.20), transparent 60%),
              linear-gradient(180deg, #052e16, #064e3b 60%, #052e16); }
  .v4 .mock { background: #fff; border: 1px solid #a7f3d0; border-radius: 20px;
              box-shadow: 0 24px 64px -20px rgb(0 0 0 / 0.6); }
  .v4 .m-badge { color: #065f46; background: #ecfdf5; border: 1px solid #6ee7b7; }
  .v4 .m-dot { background: #059669; box-shadow: 0 0 0 4px rgb(5 150 105 / 0.18); }
  .v4 h3 { color: #052e16; } .v4 .m-sub, .v4 label { color: #475569; }
  .v4 .m-in { border-color: #a7f3d0; border-radius: 12px; background: #f0fdf4; }
  .v4 .m-row { color: #475569; }
  .v4 .m-btn { background: #059669; color: #fff; border-radius: 999px; box-shadow: 0 8px 20px -8px rgb(5 150 105 / 0.7); }
  .v4 .m-foot { color: #059669; }

  /* ===== V5: Safety Steel ===== */
  .stage.v5 { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); }
  .v5 .mock { background: #fff; border: 1px solid #cbd5e1; border-radius: 14px; overflow: hidden;
              box-shadow: 0 16px 40px -16px rgb(15 23 42 / 0.25); padding-top: 0; }
  .v5 .m-hazard { height: 10px; margin: 0 -30px 24px;
      background: repeating-linear-gradient(-45deg, #facc15 0 16px, #0f172a 16px 32px); }
  .v5 .m-badge { color: #0f172a; background: #fef9c3; border: 1px solid #facc15; border-radius: 6px; letter-spacing: 0.06em; }
  .v5 .m-dot { background: #0f172a; box-shadow: none; border-radius: 2px; }
  .v5 h3 { color: #0f172a; } .v5 .m-sub, .v5 label { color: #475569; }
  .v5 .m-in { border-color: #94a3b8; border-radius: 8px; background: #f8fafc; }
  .v5 .m-row { color: #475569; }
  .v5 .m-btn { background: #facc15; color: #0f172a; border-radius: 8px; border: 2px solid #0f172a;
               box-shadow: 4px 4px 0 #0f172a; }
  .v5 .m-foot { color: #64748b; }

  /* ===== V6: Clean Minimal ===== */
  .stage.v6 { background: #fafafa; }
  .v6 .mock { background: #fff; border: 1px solid #e5e5e5; border-radius: 12px; box-shadow: none; }
  .v6 .m-eyebrow { font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: #0066ff; }
  .v6 h3 { color: #0f172a; font-weight: 700; } .v6 .m-sub { color: #737373; } .v6 label { color: #404040; font-weight: 600; }
  .v6 .m-in { border-color: #e5e5e5; border-radius: 8px; background: #fff; }
  .v6 .m-row { color: #737373; }
  .v6 .m-btn { background: #0f172a; color: #fff; border-radius: 8px; box-shadow: none; font-weight: 600; }
  .v6 .m-foot { color: #a3a3a3; }

  /* ===== V7: Split Corporate ===== */
  .stage.v7 { background: linear-gradient(180deg, #eef6ff, #f8fafc); }
  .v7 .mock-split { display: flex; width: 760px; max-width: 100%; background: #fff; border: 1px solid #e2e8f0;
                    border-radius: 20px; overflow: hidden; box-shadow: 0 24px 64px -24px rgb(15 23 42 / 0.25); }
  .v7 .m-form { flex: 1; padding: 36px 32px; }
  .v7 .m-side { flex: 1; background: linear-gradient(160deg, #0f172a, #1d4ed8 130%); color: #fff; padding: 36px 32px;
                display: flex; flex-direction: column; justify-content: center; gap: 18px; }
  .v7 .m-side q { font-size: 17px; font-weight: 700; line-height: 1.5; quotes: '“' '”'; }
  .v7 .m-stats { display: flex; gap: 20px; }
  .v7 .m-stats div b { display: block; font-size: 20px; font-weight: 800; color: #fbbf24; }
  .v7 .m-stats div span { font-size: 11px; color: #bfdbfe; }
  .v7 h3 { color: #0f172a; margin: 12px 0 6px; font-size: 21px; font-weight: 800; }
  .v7 .m-sub { color: #475569; font-size: 13px; line-height: 1.6; }
  .v7 label { display: block; font-size: 12px; font-weight: 700; color: #475569; margin: 14px 0 6px; }
  .v7 .m-in { border: 1px solid #cbd5e1; border-radius: 10px; padding: 11px 14px; font-size: 13px; color: #94a3b8; background: #fff; }
  .v7 .m-btn { display: block; width: 100%; text-align: center; background: #0066ff; color: #fff; font-size: 14px;
               font-weight: 700; padding: 12px; border-radius: 10px; margin-top: 18px; }
  @media (max-width: 720px) { .v7 .mock-split { flex-direction: column; } }
</style>
</head>
<body>
<div class="wrap">
  <div class="hero">
    <h1>Pilih Branding &amp; Login Page</h1>
    <p>7 alternatif untuk <b>PT Borneo Prima — Asset Tagging</b>. Lihat, bandingkan, lalu balas dengan nomor varian (boleh pilih lebih dari satu untuk digabung).</p>
  </div>
  <div class="nav">
    <a href="#v1">1 Official</a><a href="#v2">2 Midnight Gold</a><a href="#v3">3 Ember</a>
    <a href="#v4">4 ESG Forest</a><a href="#v5">5 Safety Steel</a><a href="#v6">6 Minimal</a><a href="#v7">7 Split</a>
  </div>

  <!-- V1 -->
  <section class="variant" id="v1">
    <div class="vhead"><span class="vnum">Varian 1</span><h2>Official Blue</h2><small>Persis web induk borneoprima.com — aman &amp; korporat (yang aktif sekarang)</small></div>
    <div class="stage v1"><div class="mock">
      <div class="m-logo"><img src="/images/logo-bp.svg" alt="logo" style="height:64px"></div>
      <div class="m-center"><span class="m-badge"><span class="m-dot"></span>PT Borneo Prima &bull; Asset Tagging</span>
        <h3>Selamat datang kembali</h3>
        <p class="m-sub">Masuk untuk mengelola inventaris aset PT Borneo Prima.</p></div>
      <label>Alamat email</label><div class="m-in">nama@borneoprima.co.id</div>
      <label>Kata sandi</label><div class="m-in">••••••••</div>
      <div class="m-row"><span>☐ Ingat saya</span><span style="color:#0066ff;font-weight:700">Lupa sandi?</span></div>
      <div class="m-btn">Masuk ke Dashboard →</div>
      <div class="m-foot"><span>◧ Scan QR</span><span>🛡 Data aman</span><span>▣ Mobile</span></div>
    </div></div>
    <div class="tokens"><code>#0066FF</code><code>#0F172A</code><code class="l">#EEF6FF</code><code class="l">tombol pill • light • logo resmi</code></div>
  </section>

  <!-- V2 -->
  <section class="variant" id="v2">
    <div class="vhead"><span class="vnum">Varian 2</span><h2>Midnight Gold</h2><small>Premium &amp; eksklusif — navy malam + emas, cocok untuk jajaran direksi</small></div>
    <div class="stage v2"><div class="mock">
      <div class="m-logo"><div class="m-mono">BP</div></div>
      <div class="m-center"><span class="m-badge"><span class="m-dot"></span>PT Borneo Prima &bull; Asset Tagging</span>
        <h3>Selamat datang kembali</h3>
        <p class="m-sub">Portal inventaris aset — akses internal karyawan.</p></div>
      <label>Alamat email</label><div class="m-in">nama@borneoprima.co.id</div>
      <label>Kata sandi</label><div class="m-in">••••••••</div>
      <div class="m-row"><span>☐ Ingat saya</span><span style="color:#fbbf24;font-weight:700">Lupa sandi?</span></div>
      <div class="m-btn">Masuk ke Dashboard →</div>
      <div class="m-foot"><span>◧ Scan QR</span><span>🛡 Data aman</span><span>▣ Mobile</span></div>
    </div></div>
    <div class="tokens"><code>#0F172A</code><code>#F59E0B</code><code class="l">tombol pill emas • dark • monogram</code></div>
  </section>

  <!-- V3 -->
  <section class="variant" id="v3">
    <div class="vhead"><span class="vnum">Varian 3</span><h2>Ember Night</h2><small>Nuansa tambang malam hari — bara api, maskulin &amp; berani</small></div>
    <div class="stage v3"><div class="mock">
      <div class="m-logo"><div class="m-mono">BP</div></div>
      <div class="m-center"><span class="m-badge"><span class="m-dot"></span>PT Borneo Prima &bull; Asset Tagging</span>
        <h3>Selamat datang kembali</h3>
        <p class="m-sub">Operasi pit tak pernah tidur — kelola aset kapan saja.</p></div>
      <label>Alamat email</label><div class="m-in">nama@borneoprima.co.id</div>
      <label>Kata sandi</label><div class="m-in">••••••••</div>
      <div class="m-row"><span>☐ Ingat saya</span><span style="color:#fb923c;font-weight:700">Lupa sandi?</span></div>
      <div class="m-btn">Masuk ke Dashboard →</div>
      <div class="m-foot"><span>◧ Scan QR</span><span>🛡 Data aman</span><span>▣ Mobile</span></div>
    </div></div>
    <div class="tokens"><code>#0C0A09</code><code>#F97316→#EF4444</code><code class="l">tombol kotak gradient • dark</code></div>
  </section>

  <!-- V4 -->
  <section class="variant" id="v4">
    <div class="vhead"><span class="vnum">Varian 4</span><h2>ESG Forest</h2><small>Menonjolkan keberlanjutan &amp; reklamasi — hijau reklamasi progresif</small></div>
    <div class="stage v4"><div class="mock">
      <div class="m-logo"><img src="/images/logo-bp.svg" alt="logo" style="height:64px"></div>
      <div class="m-center"><span class="m-badge"><span class="m-dot"></span>Menambang Bertanggung Jawab</span>
        <h3>Selamat datang kembali</h3>
        <p class="m-sub">Kelola aset dengan jejak hijau — selaras komitmen ESG.</p></div>
      <label>Alamat email</label><div class="m-in">nama@borneoprima.co.id</div>
      <label>Kata sandi</label><div class="m-in">••••••••</div>
      <div class="m-row"><span>☐ Ingat saya</span><span style="color:#059669;font-weight:700">Lupa sandi?</span></div>
      <div class="m-btn">Masuk ke Dashboard →</div>
      <div class="m-foot"><span>◧ Scan QR</span><span>🛡 Data aman</span><span>▣ Mobile</span></div>
    </div></div>
    <div class="tokens"><code>#052E16</code><code>#059669</code><code class="l">tombol pill hijau • dark stage, kartu putih</code></div>
  </section>

  <!-- V5 -->
  <section class="variant" id="v5">
    <div class="vhead"><span class="vnum">Varian 5</span><h2>Safety Steel</h2><small>Industrial K3 — garis hazard kuning-hitam, tegas &amp; fungsional</small></div>
    <div class="stage v5"><div class="mock">
      <div class="m-hazard"></div>
      <div class="m-center"><span class="m-badge"><span class="m-dot"></span>K3 • Zero Harm</span>
        <h3>Selamat datang kembali</h3>
        <p class="m-sub">Keselamatan utama — masuk untuk kelola aset site.</p></div>
      <label>Alamat email</label><div class="m-in">nama@borneoprima.co.id</div>
      <label>Kata sandi</label><div class="m-in">••••••••</div>
      <div class="m-row"><span>☐ Ingat saya</span><span style="color:#0f172a;font-weight:700">Lupa sandi?</span></div>
      <div class="m-btn">Masuk ke Dashboard →</div>
      <div class="m-foot"><span>◧ Scan QR</span><span>🛡 Data aman</span><span>▣ Mobile</span></div>
    </div></div>
    <div class="tokens"><code>#FACC15</code><code>#0F172A</code><code class="l">tombol kuning tegas • light • aksen hazard</code></div>
  </section>

  <!-- V6 -->
  <section class="variant" id="v6">
    <div class="vhead"><span class="vnum">Varian 6</span><h2>Clean Minimal</h2><small>Bersih tanpa dekorasi — fokus penuh ke form, ringan &amp; cepat</small></div>
    <div class="stage v6"><div class="mock">
      <div class="m-center" style="text-align:left">
        <div class="m-eyebrow">PT Borneo Prima — Asset Tagging</div>
        <h3>Masuk</h3>
        <p class="m-sub">Gunakan akun karyawan Anda untuk melanjutkan.</p></div>
      <label>Alamat email</label><div class="m-in">nama@borneoprima.co.id</div>
      <label>Kata sandi</label><div class="m-in">••••••••</div>
      <div class="m-row"><span>☐ Ingat saya</span><span style="font-weight:700">Lupa sandi?</span></div>
      <div class="m-btn">Masuk →</div>
    </div></div>
    <div class="tokens"><code>#0F172A</code><code class="l">#FAFAFA</code><code class="l">tombol kotak navy • tanpa dekorasi</code></div>
  </section>

  <!-- V7 -->
  <section class="variant" id="v7">
    <div class="vhead"><span class="vnum">Varian 7</span><h2>Split Corporate</h2><small>Form + panel profil perusahaan — paling informatif &amp; berkesan</small></div>
    <div class="stage v7"><div class="mock-split">
      <div class="m-form">
        <img src="/images/logo-bp.svg" alt="logo" style="height:44px">
        <h3>Selamat datang kembali</h3>
        <p class="m-sub">Masuk untuk mengelola inventaris aset.</p>
        <label>Alamat email</label><div class="m-in">nama@borneoprima.co.id</div>
        <label>Kata sandi</label><div class="m-in">••••••••</div>
        <div class="m-btn">Masuk ke Dashboard →</div>
      </div>
      <div class="m-side">
        <q>High-quality coking coal producer dari Murung Raya untuk baja dunia.</q>
        <div class="m-stats">
          <div><b>15rb</b><span>Hektar konsesi</span></div>
          <div><b>95%</b><span>Kadar vitrinite</span></div>
          <div><b>0.005%</b><span>Maks. fosfor</span></div>
        </div>
      </div>
    </div></div>
    <div class="tokens"><code>#0066FF</code><code>#0F172A</code><code class="l">layout 2 kolom • panel profil navy</code></div>
  </section>
</div>
</body>
</html>
