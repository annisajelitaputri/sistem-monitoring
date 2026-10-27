// SMKN 9 Luwu - satu file JS untuk situs publik + panel admin
// Tempel SELURUH isi file ini ke public/assets/js/main.js (ganti isi lama).

/* ============================================================
   BAGIAN 1: SITUS PUBLIK
   ============================================================ */
(() => {
    try {
        /* Ikon Lucide: hanya jalan kalau halaman memuat script Lucide */
        if (window.lucide) lucide.createIcons();

        /* ===== Menu mobile ===== */
        const toggle = document.querySelector('.nav-toggle');
        if (toggle) {
            const box = toggle.closest('.nav') || document.getElementById('navMenu') || document.querySelector('.nav-menu');
            if (box) {
                const setOpen = on => {
                    box.classList.toggle('open', on);
                    toggle.setAttribute('aria-expanded', String(on));
                };
                toggle.addEventListener('click', () => setOpen(!box.classList.contains('open')));
                box.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setOpen(false)));
            }
        }

        /* ===== Slider background hero ===== */
        const slides = [...document.querySelectorAll('.hero-slide')];
        const dots = [...document.querySelectorAll('.hero-dots button')];
        let cur = 0, timer;
        const go = n => {
            cur = (n + slides.length) % slides.length;
            slides.forEach((s, i) => s.classList.toggle('on', i === cur));
            dots.forEach((d, i) => d.classList.toggle('on', i === cur));
        };
        const play = () => {
            clearInterval(timer);
            if (slides.length > 1) timer = setInterval(() => go(cur + 1), 5500);
        };
        dots.forEach((d, i) => d.addEventListener('click', () => { go(i); play(); }));
        if (slides.length) { go(0); play(); }

        /* ===== Modal detail berita / kegiatan / program ===== */
        const modal = document.getElementById('modal');
        const data = window.BERANDA || {};
        let lastFocus;

        // kartu non-link tetap bisa dibuka lewat keyboard
        document.querySelectorAll('[data-open]').forEach(el => {
            if (!el.matches('a, button')) {
                if (!el.hasAttribute('tabindex')) el.setAttribute('tabindex', '0');
                if (!el.hasAttribute('role')) el.setAttribute('role', 'button');
            }
        });

        if (modal) {
            const q = s => modal.querySelector(s);
            const set = (sel, fn) => { const el = q(sel); if (el) fn(el); };

            const open = key => {
                const [type, id] = key.split(':');
                const x = (data[type] || {})[id];
                if (!x) return;

                set('.m-img', img => {
                    img.hidden = !x.gambar;
                    img.style.backgroundImage = x.gambar ? `url("${String(x.gambar).replace(/"/g, '%22')}")` : '';
                });
                set('.m-tag', el => { el.textContent = x.kategori || ''; });
                set('.m-date', el => { el.hidden = !x.tanggal; });
                set('.m-date span', el => { el.textContent = x.tanggal || ''; });
                set('#m-title', el => { el.textContent = x.judul || ''; });
                set('.m-text', el => { el.textContent = x.isi || 'Belum ada deskripsi.'; });

                lastFocus = document.activeElement;
                modal.hidden = false;
                document.body.style.overflow = 'hidden';
                set('.m-x', el => el.focus());
            };

            const close = () => {
                modal.hidden = true;
                document.body.style.overflow = '';
                if (lastFocus) lastFocus.focus();
            };

            document.addEventListener('click', e => {
                const t = e.target.closest('[data-open]');
                if (t) { e.preventDefault(); return open(t.dataset.open); }
                if (!modal.hidden && (e.target === modal || e.target.closest('.m-x'))) close();
            });
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape' && !modal.hidden) close();
                if ((e.key === 'Enter' || e.key === ' ') && e.target.matches('[data-open][role="button"]')) {
                    e.preventDefault();
                    open(e.target.dataset.open);
                }
            });
        }
    } catch (err) {
        console.error('[main.js] Error di bagian publik:', err);
    }
})();

/* ============================================================
   BAGIAN 2: PANEL ADMIN / TU
   Aman dijalankan di halaman publik: semua dijaga pengecekan elemen.
   Memakai event delegation, jadi tombol tetap berfungsi walau
   elemennya dimuat belakangan, dan error di satu bagian tidak
   mematikan bagian lain.
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {
    var safe = function (name, fn) {
        try { fn(); } catch (err) { console.error('[main.js] Error di ' + name + ':', err); }
    };

    safe('lucide', function () {
        if (window.lucide) lucide.createIcons();
    });

    // ===== Sidebar mobile =====
    safe('sidebar', function () {
        var sidebar = document.getElementById('admSidebar');
        var burger = document.getElementById('admBurger');
        var backdrop = document.getElementById('admBackdrop');
        function toggleSidebar(open) {
            if (!sidebar) return;
            sidebar.classList.toggle('open', open);
            if (backdrop) backdrop.classList.toggle('show', open);
        }
        if (burger && sidebar) {
            burger.addEventListener('click', function () {
                toggleSidebar(!sidebar.classList.contains('open'));
            });
        }
        if (backdrop) backdrop.addEventListener('click', function () { toggleSidebar(false); });
    });

    // ===== Grafik garis =====
    safe('grafik', function () {
        var chartEl = document.getElementById('lineChart');
        if (chartEl && window.ADMIN_DASH) {
            drawLineChart(chartEl, window.ADMIN_DASH.labels || [], window.ADMIN_DASH.values || []);
        }
    });

    // ===== Filter otomatis =====
    safe('filter', function () {
        var filterForm = document.getElementById('filterForm');
        var searchInput = document.getElementById('searchInput');
        var filterSelect = document.getElementById('filterSelect');
        var typingTimer;
        if (filterForm && filterSelect) {
            filterSelect.addEventListener('change', function () { filterForm.submit(); });
        }
        if (filterForm && searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(function () { filterForm.submit(); }, 500);
            });
            if (searchInput.value) {
                searchInput.focus();
                searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
            }
        }
    });

    // ===== Modal tambah / edit / koreksi =====
    safe('modal', function () {
        var openModal = function (m) { m.classList.add('open'); };
        var closeModal = function (m) { m.classList.remove('open'); };

        // Klik tombol [data-open-modal] (delegation)
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-open-modal]');
            if (!btn) return;

            var modal = document.getElementById(btn.dataset.openModal);
            if (!modal) {
                console.warn('[main.js] Modal tidak ditemukan: #' + btn.dataset.openModal);
                return;
            }

            var form = modal.querySelector('form');
            if (!form) {
                console.warn('[main.js] Tidak ada <form> di dalam #' + modal.id);
                return;
            }

            // Judul & field method dicari dengan beberapa cara supaya tidak gampang gagal
            var title = modal.querySelector('#modalTitle, [data-modal-title]');
            var method = form.querySelector('#methodField, input[name="_method"]');
            if (!method) {
                method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'POST';
                form.appendChild(method);
            }

            var label = title ? title.textContent.replace(/^(Tambah|Edit)/, '').trim() : '';
            form.reset();

            if (btn.dataset.mode === 'edit') {
                form.action = btn.dataset.action;
                method.value = 'PUT';
                if (title) title.textContent = btn.dataset.title || ('Edit ' + label);

                form.querySelectorAll('[name]').forEach(function (el) {
                    if (['_token', '_method'].indexOf(el.name) !== -1 || el.type === 'file') return;
                    if (btn.dataset[el.name] !== undefined) el.value = btn.dataset[el.name];
                });

                // teks info (misalnya nama guru di modal koreksi)
                modal.querySelectorAll('[data-fill]').forEach(function (el) {
                    el.textContent = btn.dataset[el.dataset.fill] || '';
                });
            } else {
                if (form.dataset.storeUrl) form.action = form.dataset.storeUrl;
                method.value = 'POST';
                if (title) title.textContent = 'Tambah ' + label;
            }
            openModal(modal);
        });

        // Tutup modal: klik latar / tombol [data-close-modal] (delegation)
        document.addEventListener('click', function (e) {
            var closer = e.target.closest('[data-close-modal]');
            if (closer) {
                var m = closer.closest('.modal');
                if (m) closeModal(m);
                return;
            }
            if (e.target.classList && e.target.classList.contains('modal') && e.target.id !== 'modal') {
                closeModal(e.target);
            }
        });

        // Modal yang langsung terbuka saat halaman dimuat (misalnya karena validasi gagal)
        document.querySelectorAll('.modal:not(#modal)').forEach(function (m) {
            if (m.hasAttribute('data-open-on-load')) openModal(m);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') document.querySelectorAll('.modal.open').forEach(closeModal);
        });
    });

    // ===== Konfirmasi hapus =====
    safe('konfirmasi-hapus', function () {
        document.addEventListener('submit', function (e) {
            var f = e.target;
            if (f && f.matches && f.matches('form[data-confirm]')) {
                if (!confirm(f.dataset.confirm)) e.preventDefault();
            }
        });
    });

    // ===== Alert hilang otomatis =====
    safe('alert', function () {
        document.querySelectorAll('.alert-success').forEach(function (a) {
            setTimeout(function () { a.remove(); }, 4000);
        });
    });
});

(function () {
    function getModal() { return document.querySelector('.mg-modal'); }

    function parts(modal) {
        return {
            form: modal.querySelector('form'),
            method: modal.querySelector('input[name="_method"]'),
            title: modal.querySelector('.mg-modal-box h3'),
            fotoHint: modal.querySelector('#fHint')
        };
    }

    // Alamat "store" asli form (disimpan sekali, sebelum pernah diubah)
    function getStoreUrl(modal, form) {
        if (modal.dataset.storeUrl) return modal.dataset.storeUrl;
        if (!form.dataset.origAction) form.dataset.origAction = form.getAttribute('action');
        return form.dataset.origAction;
    }

    window.openCreate = function () {
        var modal = getModal();
        if (!modal) return console.warn('[main.js] .mg-modal tidak ditemukan');
        var p = parts(modal);
        if (!p.form) return console.warn('[main.js] <form> di dalam .mg-modal tidak ditemukan');

        var storeUrl = getStoreUrl(modal, p.form);
        p.form.reset();
        p.form.action = storeUrl;
        if (p.method) p.method.disabled = true;
        if (p.title && modal.dataset.titleCreate) p.title.textContent = modal.dataset.titleCreate;

        p.form.querySelectorAll('input[type="date"]').forEach(function (el) {
            el.value = new Date().toLocaleDateString('en-CA');
        });
        if (p.fotoHint) p.fotoHint.textContent = '';
        modal.hidden = false;
    };

    window.openEdit = function (btn) {
        var modal = getModal();
        if (!modal) return console.warn('[main.js] .mg-modal tidak ditemukan');
        var p = parts(modal);
        if (!p.form) return console.warn('[main.js] <form> di dalam .mg-modal tidak ditemukan');

        var storeUrl = getStoreUrl(modal, p.form);
        p.form.reset();

        // Pakai data-update-url kalau ada; kalau tidak, bentuk dari alamat store + ID
        if (modal.dataset.updateUrl) {
            p.form.action = modal.dataset.updateUrl.replace('__ID__', btn.dataset.id);
        } else {
            p.form.action = storeUrl.replace(/\/+$/, '') + '/' + btn.dataset.id;
        }

        if (p.method) p.method.disabled = false;
        if (p.title && modal.dataset.titleEdit) p.title.textContent = modal.dataset.titleEdit;
        else if (p.title) p.title.textContent = p.title.textContent.replace(/^Tambah/i, 'Edit');

        p.form.querySelectorAll('[name]').forEach(function (el) {
            if (['_token', '_method'].indexOf(el.name) !== -1 || el.type === 'file') return;
            if (btn.dataset[el.name] !== undefined) el.value = btn.dataset[el.name];
        });
        if (p.fotoHint) p.fotoHint.textContent = '(kosongkan jika tidak diganti)';
        modal.hidden = false;
    };

    window.closeModal = function () {
        var modal = getModal();
        if (modal) modal.hidden = true;
    };

    document.addEventListener('click', function (e) {
        var modal = getModal();
        if (modal && e.target === modal) window.closeModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.closeModal();
    });
})();


/* ============================================================
   GRAFIK GARIS (dashboard admin)
   ============================================================ */
function drawLineChart(el, labels, values) {
    if (!values.length) return;

    var W = 680, H = 250;
    var padX = 22, top = 24, bottom = 46;
    var plotH = H - top - bottom;
    var step = values.length > 1 ? (W - padX * 2) / (values.length - 1) : 0;

    var min = Math.min.apply(null, values);
    var max = Math.max.apply(null, values);
    if (max === min) { max = min + 10; min = Math.max(0, min - 10); }
    var span = max - min;
    var lo = min - span * 0.25;
    var hi = max + span * 0.1;

    var pts = values.map(function (v, i) {
        return { x: padX + step * i, y: top + plotH - ((v - lo) / (hi - lo)) * plotH };
    });

    var line = pts.map(function (p) { return p.x.toFixed(1) + ',' + p.y.toFixed(1); }).join(' ');
    var area = padX + ',' + (top + plotH) + ' ' + line + ' ' + pts[pts.length - 1].x.toFixed(1) + ',' + (top + plotH);

    var svg = '<svg viewBox="0 0 ' + W + ' ' + H + '" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Grafik kehadiran 7 hari terakhir">';
    svg += '<defs><linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">' +
           '<stop offset="0%" stop-color="#4f7bff" stop-opacity=".30"/>' +
           '<stop offset="100%" stop-color="#4f7bff" stop-opacity="0"/></linearGradient></defs>';
    svg += '<polygon points="' + area + '" fill="url(#areaGrad)"/>';
    svg += '<polyline points="' + line + '" fill="none" stroke="#2f5bff" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"/>';

    pts.forEach(function (p, i) {
        svg += '<circle cx="' + p.x.toFixed(1) + '" cy="' + p.y.toFixed(1) + '" r="5.5" fill="#fff" stroke="#2f5bff" stroke-width="3">' +
               '<title>' + (labels[i] || '') + ': ' + values[i] + '%</title></circle>';
        svg += '<text x="' + p.x.toFixed(1) + '" y="' + (H - 12) + '" text-anchor="middle" font-size="13" fill="#8a94b8" font-family="inherit">' +
               (labels[i] || '') + '</text>';
    });

    svg += '</svg>';
    el.innerHTML = svg;
}

/* =====================================================
   SMKN 9 Luwu — Halaman Tenaga Pendidik
   -----------------------------------------------------
   CARA MENGEDIT DATA:
   - foto   : isi dengan path gambar, mis. "images/guru/wiwis.jpg"
              (kosongkan "" untuk memakai ikon bawaan)
   - jabatan: boleh lebih dari satu, tulis sebagai daftar
   - mapel  : boleh lebih dari satu, tulis sebagai daftar
   ===================================================== */

const DEFAULT_KEPSEK = {
  nama: "HARIANTO GOMMO S.P.,M.Si",
  nip: "196902171996011001",
  foto: "", // contoh: "images/guru/kepala-sekolah.jpg"
  sambutan: "[Sambutan singkat kepala sekolah untuk siswa, orang tua, dan masyarakat]",
  jabatan: ["Kepala Sekolah"],
  mapel: [],
  label: "Kepala Sekolah",
  tema: "blue"
};

const DEFAULT_GURU = [
  {
    nama: "WIWIS PRATIWI, S.Pd",
    nip: "",
    foto: "",
    jabatan: ["Wali Kelas"],
    mapel: ["B.Indonesia"]
  },
  {
    nama: "HASBINA, S.Pd, Gr",
    nip: "",
    foto: "",
    jabatan: ["Wakil Kepala Sekolah Bidang Akademik"],
    mapel: ["Matematika"]
  },
  {
    nama: "ARMANTO, S.Pd",
    nip: "",
    foto: "",
    jabatan: ["Wali Kelas X TKR"],
    mapel: ["PJOK"]
  },
  {
    nama: "SATRIANI, S.Pd",
    nip: "",
    foto: "",
    jabatan: ["Kepala Bengkel"],
    mapel: ["B.Indonesia"]
  },
  {
    nama: "NAHRI, S.Pd",
    nip: "",
    foto: "",
    jabatan: ["Ketua Program Keahlian"],
    mapel: ["Pemeliharaan Mesin Kendaraan Ringan"]
  },
  {
    nama: "FERAWATI, S.Pd, Gr",
    nip: "",
    foto: "",
    jabatan: ["Kepala Perpustakaan"],
    mapel: ["B.Inggris"]
  },
  {
    nama: "ULIVIATI NUR, S.Pd",
    nip: "",
    foto: "",
    jabatan: ["Wali Kelas XII TKR"],
    mapel: ["Sejarah Indonesia"]
  },
  {
    nama: "RISNAWATI, S.Pd, Gr",
    nip: "",
    foto: "",
    jabatan: ["Kepala Laboratorium"],
    mapel: ["Projek IPAS"]
  }
];

/* Data dari Laravel (controller) jika ada, jika tidak pakai data contoh di atas */
const DATA_SERVER = window.GURU_DATA || {};
const KEPALA_SEKOLAH = DATA_SERVER.kepala_sekolah || DEFAULT_KEPSEK;
const GURU = DATA_SERVER.guru || DEFAULT_GURU;

/* ---------- Konfigurasi tampilan ---------- */
const TEMA = ["blue", "cyan", "violet", "amber"];
const MAX_CHIP_DI_KARTU = 1; // jumlah mapel yang tampil di kartu (sisanya jadi "+n")

/* Semua orang (kepala sekolah + guru) untuk navigasi di modal */
const SEMUA = [KEPALA_SEKOLAH, ...GURU.map((g, i) => ({ ...g, tema: TEMA[i % TEMA.length] }))];

/* ---------- Ikon (SVG) ---------- */
const ICON_USER = (size = 56) => `
  <svg viewBox="0 0 24 24" width="${size}" height="${size}" fill="none" stroke="currentColor"
       stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/>
  </svg>`;

const ICON_BOOK = `
  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
       stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
  </svg>`;

const ICON_EXPAND = `
  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
       stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <polyline points="15 3 21 3 21 9"/><line x1="21" y1="3" x2="14" y2="10"/>
    <polyline points="9 21 3 21 3 15"/><line x1="3" y1="21" x2="10" y2="14"/>
  </svg>`;

/* ---------- Helper ---------- */
const $ = (sel, root = document) => root.querySelector(sel);

function esc(text = "") {
  const d = document.createElement("div");
  d.textContent = text;
  return d.innerHTML;
}

/** Buat foto (jika ada) atau ikon pengganti. Jika file foto gagal dimuat -> ikon. */
function renderFoto(container, person, iconSize) {
  container.innerHTML = "";
  if (person.foto) {
    const img = new Image();
    img.src = person.foto;
    img.alt = "Foto " + person.nama;
    img.loading = "lazy";
    img.addEventListener("error", () => {
      img.remove();
      container.insertAdjacentHTML("beforeend", ICON_USER(iconSize));
    });
    container.appendChild(img);
  } else {
    container.insertAdjacentHTML("beforeend", ICON_USER(iconSize));
  }
}

/* =====================================================
   KEPALA SEKOLAH
   ===================================================== */
function renderKepsek() {
  $("#kepsekNama").textContent = KEPALA_SEKOLAH.nama;
  $("#kepsekNip").textContent = KEPALA_SEKOLAH.nip;
  $("#kepsekSambutan").textContent = KEPALA_SEKOLAH.sambutan;

  const box = $("#kepsekFoto");
  renderFoto(box, KEPALA_SEKOLAH, 72);
  if (!KEPALA_SEKOLAH.foto) {
    box.insertAdjacentHTML("beforeend", '<small class="kepsek__ph" style="position:absolute;bottom:18px;font-size:.8rem;font-weight:600">Foto kepala sekolah</small>');
  }

  const card = $("#kepsekCard");
  const open = () => openModal(0, card);
  card.addEventListener("click", open);
  card.addEventListener("keydown", (e) => {
    if (e.key === "Enter" || e.key === " ") { e.preventDefault(); open(); }
  });
}

/* =====================================================
   GRID GURU
   ===================================================== */
const grid = $("#guruGrid");

function buatKartu(guru, index) {
  const idxGlobal = index + 1; // +1 karena index 0 = kepala sekolah
  const tema = TEMA[index % TEMA.length];
  const mapel = guru.mapel || [];
  const tampil = mapel.slice(0, MAX_CHIP_DI_KARTU);
  const sisa = mapel.length - tampil.length;

  const card = document.createElement("article");
  card.className = `card card--${tema} reveal`;
  card.tabIndex = 0;
  card.setAttribute("role", "button");
  card.setAttribute("aria-label", `Lihat detail ${guru.nama}`);
  card.dataset.search = [guru.nama, ...(guru.jabatan || []), ...mapel].join(" ").toLowerCase();
  card.style.transitionDelay = `${(index % 4) * 70}ms`;

  card.innerHTML = `
    <div class="card__cover"></div>
    <span class="card__hint">${ICON_EXPAND}</span>
    <div class="card__avatar"></div>
    <h3 class="card__name">${esc(guru.nama)}</h3>
    <p class="card__role">${esc((guru.jabatan || [])[0] || "Guru")}</p>
    <div>
      ${tampil.map((m) => `<span class="chip">${esc(m)}</span>`).join("")}
      ${sisa > 0 ? `<span class="chip chip--more" title="${sisa} mapel lainnya">+${sisa}</span>` : ""}
    </div>
  `;

  renderFoto($(".card__avatar", card), guru, 44);

  const open = () => openModal(idxGlobal, card);
  card.addEventListener("click", open);
  card.addEventListener("keydown", (e) => {
    if (e.key === "Enter" || e.key === " ") { e.preventDefault(); open(); }
  });

  return card;
}

function renderGuru() {
  const frag = document.createDocumentFragment();
  GURU.forEach((g, i) => frag.appendChild(buatKartu(g, i)));
  grid.appendChild(frag);
}

/* ---------- Pencarian ---------- */
function setupSearch() {
  const input = $("#searchGuru");
  const empty = $("#emptyState");

  input.addEventListener("input", () => {
    const q = input.value.trim().toLowerCase();
    let ada = 0;
    grid.querySelectorAll(".card").forEach((card) => {
      const cocok = !q || card.dataset.search.includes(q);
      card.hidden = !cocok;
      if (cocok) ada++;
    });
    empty.hidden = ada > 0;
  });
}

/* =====================================================
   MODAL DETAIL
   ===================================================== */
const modal = $("#guruModal");
const dialog = $("#modalDialog");
let indexAktif = 0;
let pemicu = null; // elemen yang membuka modal (untuk mengembalikan fokus)

function isiModal(i) {
  const p = SEMUA[i];
  indexAktif = i;

  /* tema warna */
  dialog.className = `modal__dialog card--${p.tema || "blue"}`;

  /* foto */
  renderFoto($("#modalFoto"), p, 110);

  /* teks */
  $("#modalRole").textContent = p.label || (p.jabatan && p.jabatan[0]) || "Guru";
  $("#modalNama").textContent = p.nama;
  $("#modalNip").textContent = p.nip ? "NIP. " + p.nip : "";
  $("#modalNip").hidden = !p.nip;

  /* jabatan */
  const jabatan = p.jabatan && p.jabatan.length ? p.jabatan : ["Guru"];
  $("#modalJabatan").innerHTML = jabatan.map((j) => `<li>${esc(j)}</li>`).join("");

  /* mata pelajaran (semua ditampilkan, termasuk jika lebih dari 2) */
  const mapel = p.mapel || [];
  const secMapel = $("#modalMapel").closest(".modal__section");
  secMapel.hidden = mapel.length === 0;
  $("#modalCount").textContent = mapel.length;
  $("#modalMapel").innerHTML = mapel
    .map((m, n) => `<li style="animation-delay:${n * 60}ms">${ICON_BOOK}${esc(m)}</li>`)
    .join("");

  /* navigasi */
  $("#modalIndex").textContent = `${i + 1} / ${SEMUA.length}`;
  $("#btnPrev").disabled = i === 0;
  $("#btnNext").disabled = i === SEMUA.length - 1;
  $("#btnPrev").style.opacity = i === 0 ? ".4" : "1";
  $("#btnNext").style.opacity = i === SEMUA.length - 1 ? ".4" : "1";

  $(".modal__body", dialog).scrollTop = 0;
}

function openModal(i, trigger) {
  pemicu = trigger || null;
  isiModal(i);
  modal.classList.add("is-open");
  modal.setAttribute("aria-hidden", "false");
  document.body.classList.add("no-scroll");
  $(".modal__close", modal).focus();
}

function closeModal() {
  modal.classList.remove("is-open");
  modal.setAttribute("aria-hidden", "true");
  document.body.classList.remove("no-scroll");
  if (pemicu) pemicu.focus();
}

function setupModal() {
  modal.addEventListener("click", (e) => {
    if (e.target.closest("[data-close]")) closeModal();
  });

  $("#btnPrev").addEventListener("click", () => { if (indexAktif > 0) isiModal(indexAktif - 1); });
  $("#btnNext").addEventListener("click", () => { if (indexAktif < SEMUA.length - 1) isiModal(indexAktif + 1); });

  document.addEventListener("keydown", (e) => {
    if (!modal.classList.contains("is-open")) return;
    if (e.key === "Escape") closeModal();
    if (e.key === "ArrowLeft" && indexAktif > 0) isiModal(indexAktif - 1);
    if (e.key === "ArrowRight" && indexAktif < SEMUA.length - 1) isiModal(indexAktif + 1);

    /* jaga fokus tetap di dalam modal */
    if (e.key === "Tab") {
      const fokusable = [...modal.querySelectorAll("button:not([disabled])")];
      if (!fokusable.length) return;
      const first = fokusable[0];
      const last = fokusable[fokusable.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  });
}

/* =====================================================
   NAVBAR (mobile)
   ===================================================== */
function setupNavbar() {
  const toggle = $("#navToggle");
  const links = $("#navLinks");

  toggle.addEventListener("click", () => {
    const buka = links.classList.toggle("is-open");
    toggle.setAttribute("aria-expanded", String(buka));
  });

  links.addEventListener("click", (e) => {
    if (e.target.closest("a")) {
      links.classList.remove("is-open");
      toggle.setAttribute("aria-expanded", "false");
    }
  });
}

/* =====================================================
   ANIMASI MUNCUL SAAT SCROLL
   ===================================================== */
function setupReveal() {
  const items = document.querySelectorAll(".reveal");
  if (!("IntersectionObserver" in window)) {
    items.forEach((el) => el.classList.add("is-visible"));
    return;
  }
  const io = new IntersectionObserver((entries) => {
    entries.forEach((en) => {
      if (en.isIntersecting) {
        en.target.classList.add("is-visible");
        io.unobserve(en.target);
      }
    });
  }, { threshold: 0.12 });
  items.forEach((el) => io.observe(el));
}

/* =====================================================
   INIT
   ===================================================== */
document.addEventListener("DOMContentLoaded", () => {
  renderKepsek();
  renderGuru();
  setupSearch();
  setupModal();
  setupNavbar();
  setupReveal();
});
