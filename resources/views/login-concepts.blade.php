<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Konsep Login — Asset Tagging (layout tetap)</title>
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; background: #f1f5f9; color: #0f172a; }
  .wrap { max-width: 1160px; margin: 0 auto; padding: 32px 20px 90px; }
  .hero { text-align: center; }
  .hero h1 { font-size: 28px; font-weight: 800; letter-spacing: -0.02em; }
  .hero p { color: #475569; margin-top: 8px; font-size: 14px; line-height: 1.7; max-width: 760px; margin-left: auto; margin-right: auto; }
  .hero code { background: #0f172a; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 999px; }
  .nav { position: sticky; top: 0; z-index: 50; background: rgb(241 245 249 / 0.92); backdrop-filter: blur(8px);
         display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; padding: 12px 0; margin: 18px 0 28px; }
  .nav a { font-size: 12px; font-weight: 700; padding: 8px 14px; border-radius: 999px; background: #fff;
           border: 1px solid #e2e8f0; color: #0f172a; text-decoration: none; }
  .nav a:hover { border-color: #0066ff; color: #0066ff; }
  section.variant { background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; margin-bottom: 32px; }
  .vhead { display: flex; align-items: center; gap: 12px; padding: 18px 24px; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap; }
  .vnum { font-size: 12px; font-weight: 800; background: #0066ff; color: #fff; border-radius: 8px; padding: 4px 10px; }
  .vhead h2 { font-size: 17px; font-weight: 800; }
  .vhead small { color: #64748b; font-size: 12px; }
  .tokens { display: flex; gap: 8px; flex-wrap: wrap; padding: 14px 24px; border-top: 1px solid #e2e8f0; background: #f8fafc; }
  .tokens code { font-size: 11px; font-weight: 700; background: #0f172a; color: #fff; padding: 4px 10px; border-radius: 999px; }
  .tokens code.l { background: #fff; color: #0f172a; border: 1px solid #e2e8f0; }
  .stage { padding: 28px; background: #eef2f7; overflow: hidden; }

  /* ===== Kerangka split yang SAMA untuk semua varian ===== */
  .split { display: grid; grid-template-columns: 1fr 1fr; border-radius: 18px; overflow: hidden;
           border: 1px solid #e2e8f0; background: #fff; min-height: 560px;
           box-shadow: 0 24px 64px -28px rgb(15 23 42 / 0.30); }
  .form-col { padding: 44px 42px; display: flex; flex-direction: column; justify-content: center;
              background: linear-gradient(180deg, #eef6ff 0%, #ffffff 52%, #f8fafc 100%); position: relative; }
  .form-col::before { content: ''; position: absolute; inset: 0; pointer-events: none;
      background-image: linear-gradient(to right, rgb(15 23 42 / 0.045) 1px, transparent 1px),
                        linear-gradient(to bottom, rgb(15 23 42 / 0.045) 1px, transparent 1px);
      background-size: 44px 44px;
      mask-image: radial-gradient(ellipse 80% 70% at 50% 40%, black 30%, transparent 78%); }
  .form-inner { position: relative; max-width: 26rem; width: 100%; margin: 0 auto; }
  .logo { height: 56px; margin-bottom: 16px; }
  .badge { display: inline-flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 800;
           letter-spacing: 0.08em; text-transform: uppercase; padding: 6px 13px; border-radius: 999px; margin-bottom: 18px; }
  .dot { width: 8px; height: 8px; border-radius: 999px; }
  h3.heading { font-size: 26px; font-weight: 800; letter-spacing: -0.02em; color: #0f172a; }
  p.sub { margin-top: 8px; margin-bottom: 22px; font-size: 13.5px; line-height: 1.7; color: #475569; }
  p.sub b { color: #0f172a; }
  label.f { display: block; font-size: 12px; font-weight: 700; color: #334155; margin: 14px 0 6px; }
  .in { display: flex; align-items: center; gap: 10px; padding: 11px 14px; font-size: 13px; color: #94a3b8; background: #fff; }
  .in .ic { flex-shrink: 0; width: 30px; height: 30px; border-radius: 9px; display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 800; }
  .row { display: flex; justify-content: space-between; align-items: center; margin: 14px 0 18px; font-size: 12px; color: #475569; }
  .row .link { color: #0066ff; font-weight: 700; }
  .btn { display: block; width: 100%; text-align: center; font-size: 14px; font-weight: 800; padding: 13px; cursor: default; }
  .foot { display: flex; justify-content: center; align-items: center; gap: 12px; margin-top: 20px; font-size: 11px; font-weight: 600; color: #64748b; }
  .foot i { width: 3px; height: 3px; border-radius: 999px; background: #cbd5e1; }

  .side { position: relative; overflow: hidden; background: #0f172a; color: #fff;
          display: flex; flex-direction: column; justify-content: flex-end; padding: 36px 32px; gap: 16px; min-height: 100%; }
  .side .photo { position: absolute; inset: 0;
      background-image: url('/images/login-side.jpg'); background-size: cover; background-position: center; }
  .side .veil { position: absolute; inset: 0;
      background: linear-gradient(200deg, rgb(15 23 42 / 0.25) 0%, rgb(15 23 42 / 0.55) 55%, rgb(15 23 42 / 0.88) 100%); }
  .side > *:not(.photo):not(.veil) { position: relative; z-index: 2; }
  .edge { position: absolute; top: 0; left: 0; bottom: 0; width: 7rem; z-index: 1; pointer-events: none;
      background: linear-gradient(90deg, rgb(248 250 252 / 0.98), rgb(248 250 252 / 0) 100%); }
  .card { background: rgb(255 255 255 / 0.08); border: 1px solid rgb(255 255 255 / 0.14);
          border-radius: 14px; padding: 14px 15px; backdrop-filter: blur(8px); display: flex; gap: 12px; align-items: flex-start; }
  .card .ci { flex-shrink: 0; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center;
              justify-content: center; background: linear-gradient(135deg, #0066ff, #00a3ff); font-weight: 800; }
  .card b { display: block; font-size: 13.5px; }
  .card span { font-size: 12px; line-height: 1.6; color: #cbd5e1; }
  .side-foot { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; border-top: 1px solid rgb(255 255 255 / 0.16);
               padding-top: 16px; font-size: 12px; color: #e2e8f0; }
  .side-foot a { color: #fff; font-weight: 800; background: rgb(245 158 11 / 0.16); border: 1px solid rgb(251 191 36 / 0.55);
                 border-radius: 999px; padding: 7px 15px; text-decoration: none; font-size: 12px; }

  /* ===== A: Official Clean (baseline aktif) ===== */
  .a .badge { color: #0f172a; background: linear-gradient(180deg, #eef6ff, #dbeafe); border: 1px solid #bfdbfe; }
  .a .dot { background: #16a34a; box-shadow: 0 0 0 4px rgb(22 163 74 / 0.18); }
  .a .in { border: 1px solid #cbd5e1; border-radius: 12px; }
  .a .in .ic { background: #eef6ff; color: #0066ff; }
  .a .btn { background: #0066ff; color: #fff; border-radius: 999px; box-shadow: 0 8px 20px -8px rgb(0 102 255 / 0.6); }

  /* ===== B: Corporate Card (input filled + panel statistik) ===== */
  .b .form-col { background: linear-gradient(180deg, #ffffff, #f4f8ff 70%, #eef6ff); }
  .b .badge { color: #0066ff; background: #fff; border: 1px solid #bfdbfe; box-shadow: 0 2px 10px -4px rgb(0 102 255 / 0.25); }
  .b .dot { background: #0066ff; box-shadow: 0 0 0 4px rgb(0 102 255 / 0.15); }
  .b .in { border: 1px solid transparent; border-radius: 12px; background: #f1f5f9; color: #64748b; }
  .b .in .ic { background: #fff; color: #0066ff; border: 1px solid #e2e8f0; }
  .b .btn { background: #0066ff; color: #fff; border-radius: 12px; box-shadow: 0 10px 22px -10px rgb(0 102 255 / 0.7); }
  .b .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
  .b .stat { background: rgb(255 255 255 / 0.08); border: 1px solid rgb(255 255 255 / 0.14); border-radius: 14px;
             padding: 14px 12px; text-align: center; backdrop-filter: blur(8px); }
  .b .stat b { display: block; font-size: 22px; font-weight: 800; color: #fbbf24; }
  .b .stat span { font-size: 11px; color: #bfdbfe; line-height: 1.5; display: block; margin-top: 4px; }
  .b .side-title { font-size: 20px; font-weight: 800; line-height: 1.45; }

  /* ===== C: Trust Line (input garis bawah + panel kutipan) ===== */
  .c .badge { color: #0f172a; background: transparent; border: 1px solid #cbd5e1; border-radius: 8px; letter-spacing: 0.1em; }
  .c .dot { background: #0f172a; border-radius: 2px; }
  .c .in { border: none; border-bottom: 2px solid #cbd5e1; border-radius: 0; padding-left: 2px; background: transparent; }
  .c .in .ic { background: transparent; color: #0066ff; }
  .c .btn { background: #0f172a; color: #fff; border-radius: 999px; }
  .c .quote { font-size: 21px; font-weight: 800; line-height: 1.5; }
  .c .quote small { display: block; margin-top: 12px; font-size: 12px; font-weight: 600; color: #bfdbfe; line-height: 1.7; }
  .c .ticks { display: flex; gap: 8px; flex-wrap: wrap; }
  .c .ticks span { font-size: 11px; font-weight: 700; background: rgb(255 255 255 / 0.10);
                   border: 1px solid rgb(255 255 255 / 0.16); padding: 6px 12px; border-radius: 999px; }

  /* ===== D: Service Focus (ikon kotak + panel bantuan) ===== */
  .d .badge { color: #065f46; background: #ecfdf5; border: 1px solid #6ee7b7; }
  .d .dot { background: #059669; box-shadow: 0 0 0 4px rgb(5 150 105 / 0.18); }
  .d .in { border: 1px solid #cbd5e1; border-radius: 12px; padding: 8px 8px 8px 14px; }
  .d .in .ic { background: #0066ff; color: #fff; }
  .d .btn { background: #0066ff; color: #fff; border-radius: 999px; box-shadow: 0 8px 20px -8px rgb(0 102 255 / 0.6); }
  .d .help { background: #fff; color: #0f172a; border-radius: 14px; padding: 16px; display: flex; gap: 12px; align-items: flex-start; }
  .d .help .hi { flex-shrink: 0; width: 40px; height: 40px; border-radius: 12px; background: #eef6ff; color: #0066ff;
                 display: flex; align-items: center; justify-content: center; font-weight: 800; }
  .d .help b { font-size: 13.5px; display: block; }
  .d .help span { font-size: 12px; color: #475569; line-height: 1.6; }
  .d .mini { display: flex; gap: 10px; }
  .d .mini div { flex: 1; background: rgb(255 255 255 / 0.08); border: 1px solid rgb(255 255 255 / 0.14);
                 border-radius: 12px; padding: 12px; font-size: 11.5px; backdrop-filter: blur(8px); }
  .d .mini b { display: block; font-size: 12.5px; margin-bottom: 3px; }
  .d .mini span { color: #cbd5e1; line-height: 1.55; }

  @media (max-width: 860px) {
    .split { grid-template-columns: 1fr; }
    .form-col { padding: 32px 22px; }
    .stage { padding: 16px; }
  }
</style>
</head>
<body>
<div class="wrap">
  <div class="hero">
    <h1>4 Konsep Login — layout tetap</h1>
    <p>Struktur <b>split form + panel foto</b> sama persis di semua varian. Yang dibedakan hanya
       <code>badge</code> <code>tombol</code> <code>input</code> dan isi panel samping.
       Arah gaya: <b>korporat rapi</b> selaras web induk. Balas dengan <b>A / B / C / D</b> (boleh gabung, mis. “B + panel A”).</p>
  </div>
  <div class="nav">
    <a href="#a">A Official Clean</a><a href="#b">B Corporate Card</a><a href="#c">C Trust Line</a><a href="#d">D Service Focus</a>
  </div>

  <!-- A -->
  <section class="variant" id="a">
    <div class="vhead"><span class="vnum">Varian A</span><h2>Official Clean</h2><small>Baseline yang aktif sekarang — paling aman &amp; familiar</small></div>
    <div class="stage"><div class="split a">
      <div class="form-col"><div class="form-inner">
        <img class="logo" src="/images/logo-bp.svg" alt="PT Borneo Prima">
        <span class="badge"><span class="dot"></span>PT Borneo Prima • Asset Tagging</span>
        <h3 class="heading">Selamat Datang</h3>
        <p class="sub">Masuk untuk mengelola <b>inventaris aset PT Borneo Prima</b> — cepat, akurat, dan terpantau.</p>
        <label class="f">Alamat email</label><div class="in"><span class="ic">✉</span>nama@borneoprima.com</div>
        <label class="f">Kata sandi</label><div class="in"><span class="ic">🔒</span>••••••••</div>
        <div class="row"><span>☑ Ingat saya</span><span class="link">Lupa sandi?</span></div>
        <div class="btn">Masuk ke Dashboard →</div>
        <div class="foot"><span>Scan QR cepat</span><i></i><span>Mobile ready</span></div>
      </div></div>
      <div class="side"><div class="photo"></div><div class="veil"></div><div class="edge"></div>
        <div class="card"><span class="ci">◧</span><div><b>Scan QR Sekejap</b><span>Identifikasi aset dalam hitungan detik dari HP.</span></div></div>
        <div class="card"><span class="ci">▤</span><div><b>Stok &amp; Mutasi Terkendali</b><span>Setiap perpindahan tercatat rapi dan terpantau.</span></div></div>
        <div class="card"><span class="ci">▦</span><div><b>Pantau Real-time</b><span>Dashboard status dan distribusi selalu terkini.</span></div></div>
        <div class="side-foot"><span>Kunjungi website perusahaan</span><a href="https://borneoprima.com/" target="_blank" rel="noopener">borneoprima.com ↗</a></div>
      </div>
    </div></div>
    <div class="tokens"><code>#0066FF</code><code>#0F172A</code><code class="l">input outline • badge pill biru-muda • tombol pill</code><code class="l">panel: 3 kartu fitur</code></div>
  </section>

  <!-- B -->
  <section class="variant" id="b">
    <div class="vhead"><span class="vnum">Varian B</span><h2>Corporate Card</h2><small>Form terasa ringan dengan input filled + panel angka kepercayaan</small></div>
    <div class="stage"><div class="split b">
      <div class="form-col"><div class="form-inner">
        <img class="logo" src="/images/logo-bp.svg" alt="PT Borneo Prima">
        <span class="badge"><span class="dot"></span>Portal Internal Karyawan</span>
        <h3 class="heading">Masuk ke Asset Tagging</h3>
        <p class="sub">Satu pintu untuk <b>stok, mutasi, dan monitoring aset</b> di seluruh site.</p>
        <label class="f">Alamat email</label><div class="in"><span class="ic">✉</span>nama@borneoprima.com</div>
        <label class="f">Kata sandi</label><div class="in"><span class="ic">🔒</span>••••••••</div>
        <div class="row"><span>☑ Ingat saya di perangkat ini</span><span class="link">Lupa sandi?</span></div>
        <div class="btn">Masuk ke Dashboard →</div>
        <div class="foot"><span>© PT Borneo Prima</span><i></i><span>Akses internal saja</span></div>
      </div></div>
      <div class="side"><div class="photo"></div><div class="veil"></div><div class="edge"></div>
        <div class="side-title">Inventaris yang bisa dipertanggungjawabkan.</div>
        <div class="stat-grid">
          <div class="stat"><b>12rb+</b><span>Aset tercatat</span></div>
          <div class="stat"><b>± detik</b><span>Identifikasi via QR</span></div>
          <div class="stat"><b>100%</b><span>Mutasi tercatat</span></div>
        </div>
        <div class="side-foot"><span>Kunjungi website perusahaan</span><a href="https://borneoprima.com/" target="_blank" rel="noopener">borneoprima.com ↗</a></div>
      </div>
    </div></div>
    <div class="tokens"><code>#0066FF</code><code>#0F172A</code><code class="l">input filled abu • badge outline • tombol kotak-halus</code><code class="l">panel: statistik ringkas</code></div>
  </section>

  <!-- C -->
  <section class="variant" id="c">
    <div class="vhead"><span class="vnum">Varian C</span><h2>Trust Line</h2><small> Paling minimal — input garis bawah + kutipan operasional</small></div>
    <div class="stage"><div class="split c">
      <div class="form-col"><div class="form-inner">
        <img class="logo" src="/images/logo-bp.svg" alt="PT Borneo Prima">
        <span class="badge"><span class="dot"></span>K3 • Zero Harm</span>
        <h3 class="heading">Masuk</h3>
        <p class="sub">Gunakan <b>akun karyawan Anda</b> untuk melanjutkan ke dashboard aset.</p>
        <label class="f">Alamat email</label><div class="in"><span class="ic">✉</span>nama@borneoprima.com</div>
        <label class="f">Kata sandi</label><div class="in"><span class="ic">🔒</span>••••••••</div>
        <div class="row"><span>☑ Ingat saya</span><span class="link">Lupa sandi?</span></div>
        <div class="btn">Masuk →</div>
        <div class="foot"><span>Scan QR</span><i></i><span>Data aman</span><i></i><span>Mobile</span></div>
      </div></div>
      <div class="side"><div class="photo"></div><div class="veil"></div><div class="edge"></div>
        <div class="quote">“Setiap aset tercatat, setiap mutasi terpantau.”<small>Prinsip operasional PT Borneo Prima — dari pit hingga workshop.</small></div>
        <div class="ticks"><span>◧ Scan QR</span><span>▤ Mutasi rapi</span><span>▦ Real-time</span></div>
        <div class="side-foot"><span>Kunjungi website perusahaan</span><a href="https://borneoprima.com/" target="_blank" rel="noopener">borneoprima.com ↗</a></div>
      </div>
    </div></div>
    <div class="tokens"><code>#0F172A</code><code>#0066FF</code><code class="l">input underline • badge kotak • tombol navy pill</code><code class="l">panel: kutipan internal</code></div>
  </section>

  <!-- D -->
  <section class="variant" id="d">
    <div class="vhead"><span class="vnum">Varian D</span><h2>Service Focus</h2><small>Ramah pengguna baru — panel bantuan + kontak admin site</small></div>
    <div class="stage"><div class="split d">
      <div class="form-col"><div class="form-inner">
        <img class="logo" src="/images/logo-bp.svg" alt="PT Borneo Prima">
        <span class="badge"><span class="dot"></span>Butuh bantuan? Hubungi admin site</span>
        <h3 class="heading">Selamat Datang</h3>
        <p class="sub">Masuk untuk mengelola <b>inventaris aset PT Borneo Prima</b> — cepat, akurat, dan terpantau.</p>
        <label class="f">Alamat email</label><div class="in"><span class="ic">✉</span>nama@borneoprima.com</div>
        <label class="f">Kata sandi</label><div class="in"><span class="ic">🔒</span>••••••••</div>
        <div class="row"><span>☑ Ingat saya di perangkat ini</span><span class="link">Lupa sandi?</span></div>
        <div class="btn">Masuk ke Dashboard →</div>
        <div class="foot"><span>Scan QR cepat</span><i></i><span>Mobile ready</span></div>
      </div></div>
      <div class="side"><div class="photo"></div><div class="veil"></div><div class="edge"></div>
        <div class="help"><span class="hi">✆</span><div><b>Kendala masuk akun?</b><span>Hubungi admin site / IT dengan menyertakan NIK dan nama site Anda.</span></div></div>
        <div class="mini">
          <div><b>◧ Scan QR</b><span>Identifikasi aset dari HP dalam hitungan detik.</span></div>
          <div><b>▦ Real-time</b><span>Status dan distribusi aset selalu terkini.</span></div>
        </div>
        <div class="side-foot"><span>Kunjungi website perusahaan</span><a href="https://borneoprima.com/" target="_blank" rel="noopener">borneoprima.com ↗</a></div>
      </div>
    </div></div>
    <div class="tokens"><code>#0066FF</code><code>#059669</code><code class="l">input ikon kotak • badge hijau-bantuan • tombol pill</code><code class="l">panel: kartu bantuan + 2 mini fitur</code></div>
  </section>
</div>
</body>
</html>
