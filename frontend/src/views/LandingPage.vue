<template>
  <q-page>
    <!-- HERO SECTION -->
    <section class="hero-section relative-position overflow-hidden">
      <!-- Background Shapes -->
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>

      <div class="row items-start justify-center hero-container q-pa-md q-py-xl">

        <!-- Left: Lab Title + Informasi Terkini & Eksplorasi -->
        <div class="col-12 col-md-7 q-pa-md z-top animate__animated animate__fadeInLeft">

          <!-- Row 1: Lab Title with Logos -->
          <div class="row items-center justify-center q-mb-md q-gutter-sm">
            <img src="@/assets/logo-mtsn.png" alt="Logo MTsN" class="hero-logo" />
            <img src="@/assets/logo-lab.png" alt="Logo Lab IPA" class="hero-logo hero-logo-lab" />
            <div class="hero-lab-title text-green-8 q-ml-xl">LABORATORIUM DIGITAL<br>MTsN 1 KOTA MAKASSAR</div>
          </div>

          <!-- Row 2: Informasi Terkini + Eksplorasi Sains -->
          <div class="row items-center q-col-gutter-md">

            <!-- Informasi Terkini -->
            <div class="col-12 col-sm-5 self-start info-card-col" v-if="informasiAktif">
              <div class="hero-info-card">
                <div class="info-header">
                  <span class="info-live-dot"></span>
                  <span class="info-label">INFORMASI TERKINI</span>
                </div>
                <div class="info-body">
                  <div class="info-judul">{{ informasiAktif.judul }}</div>
                  <div v-if="informasiAktif.isi" class="info-isi">{{ informasiAktif.isi }}</div>
                </div>
                <div v-if="informasiAktif.mulai_at || informasiAktif.selesai_at" class="info-footer">
                  <q-icon name="o_schedule" size="13px" class="q-mr-xs" />
                  {{ formatPeriodeInfo(informasiAktif) }}
                </div>
              </div>
            </div>

            <!-- Eksplorasi Sains -->
            <div :class="informasiAktif ? 'col-12 col-sm-7' : 'col-12'">
              <div class="glass-morph q-pa-lg rounded-xl">
                <h1 class="text-h2 text-weight-bolder text-grey-9 q-mb-md leading-tight">
                  Eksplorasi Sains <br>
                  <span class="text-green-6">Tanpa Batas</span>
                </h1>
                <p class="text-h6 text-grey-7 q-mb-xl" style="line-height: 1.6;">
                  Tingkatkan pengalaman belajar IPA dengan sistem manajemen laboratorium yang terintegrasi, modern, dan mudah diakses.
                </p>
                <div class="q-gutter-md row">
                  <q-btn
                    label="Jelajahi Fitur"
                    color="white"
                    text-color="green-8"
                    outline
                    rounded
                    padding="12px 32px"
                    class="btn-action"
                    @click="scrollToSection('features')"
                  />
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Right: Slider -->
        <div class="col-12 col-md-5 q-pa-md animate__animated animate__fadeInRight">
          <div class="slider-container shadow-20 rounded-borders overflow-hidden border-white">
            <q-carousel
              v-model="slide"
              animated
              infinite
              autoplay
              transition-prev="slide-right"
              transition-next="slide-left"
              height="480px"
              class="bg-white"
            >
              <q-carousel-slide
                v-for="(item, index) in slides"
                :key="item.id"
                :name="index"
                class="column no-wrap flex-center q-pa-none"
              >
                 <q-img 
                   :src="url + item.gambar" 
                   style="height: 320px; width: 100%"
                   fit="cover"
                 />
                 <div class="full-width q-pa-md text-center bg-white" style="flex-grow: 1;">
                    <div class="text-h6 text-weight-bold text-green-9 q-mb-xs">{{ item.judul }}</div>
                    <div class="text-body2 text-grey-7">{{ item.ket }}</div>
                 </div>
              </q-carousel-slide>
              
              <!-- Empty State -->
              <q-carousel-slide v-if="slides.length === 0" :name="0" class="flex flex-center bg-grey-2">
                 <div class="text-center">
                    <q-icon name="o_image" size="50px" color="grey-5" />
                    <div class="text-grey-6 q-mt-sm">Memuat Slide...</div>
                 </div>
              </q-carousel-slide>
            </q-carousel>
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURES SECTION -->
    <section id="features" class="q-py-xl bg-gradient">
      <div class="container q-pa-md">
        <div class="text-center q-mb-xl" data-aos="fade-up">
           <div class="text-overline text-green-7 q-mb-sm">KEUNGGULAN KAMI</div>
          <h2 class="text-h3 text-weight-bold text-grey-9 q-mt-none">Fitur Unggulan</h2>
          <p class="text-grey-7 text-h6" style="max-width: 600px; margin: 0 auto;">
            Solusi yang dirancang khusus untuk mempermudah administrasi dan kegiatan praktikum.
          </p>
        </div>
        
        <div class="row q-col-gutter-lg">
          <div class="col-12 col-sm-6 col-md-3" v-for="(feature, idx) in features" :key="idx">
            <q-card class="feature-card text-center q-pa-lg full-height no-shadow bg-white">
              <div class="text-h6 text-weight-bold q-mb-sm">{{ feature.title }}</div>
              <div class="text-body2 text-grey-7">{{ feature.desc }}</div>
            </q-card>
          </div>
        </div>
      </div>
    </section>

    <!-- STATISTICS SECTION -->
    <section class="q-py-xl bg-stats-gradient text-white relative-position overflow-hidden">
       <!-- Decorative Circles -->
       <div class="circle-deco circle-1"></div>
       <div class="circle-deco circle-2"></div>

      <div class="container q-pa-md relative-position">
        <div class="text-center q-mb-xl" data-aos="fade-up">
           <div class="text-overline text-green-2 q-mb-sm">DATA TERKINI</div>
          <h2 class="text-h3 text-weight-bold text-white q-mt-none">Statistik Laboratorium</h2>
          <p class="text-green-1 text-h6 opacity-80" style="max-width: 600px; margin: 0 auto;">
            Gambaran umum aktivitas dan sumber daya laboratorium secara real-time.
          </p>
        </div>

        <div class="row q-col-gutter-lg text-center items-center justify-center">
          <div class="col-6 col-sm-3 col-md-3" v-for="stat in statsDisplay" :key="stat.label">
            <div class="stat-card q-pa-lg glass-effect relative-position overflow-hidden">
              <div class="absolute-full gradient-overlay"></div>
              
              <div class="relative-position z-top column items-center">
                 <div class="icon-ring q-mb-md flex flex-center">
                    <q-icon :name="stat.icon" size="36px" class="text-white icon-anim" />
                 </div>
                 <div class="text-h3 text-weight-bolder text-white q-mb-xs count-up">{{ stat.value }}</div>
                 <div class="text-subtitle2 text-uppercase text-green-1 tracking-wider opacity-80">{{ stat.label }}</div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- ANALITIK TAHUNAN SECTION -->
    <section class="q-py-xl bg-analytics-soft">
      <div class="container q-pa-md">
        <div class="text-center q-mb-xl" data-aos="fade-up">
          <div class="text-overline text-green-7 q-mb-sm">ANALITIK TAHUNAN</div>
          <h2 class="text-h4 text-weight-bold text-grey-9 q-mt-none">Ringkasan Aktivitas Lab {{ stats.tahun_aktif || new Date().getFullYear() }}</h2>
          <p class="text-grey-7 text-subtitle1" style="max-width: 700px; margin: 0 auto;">
            Rekap peminjaman bulanan dan ringkasan inventaris alat untuk memudahkan pemantauan operasional laboratorium.
          </p>
        </div>

        <div class="row q-col-gutter-lg">
          <div class="col-12">
            <div class="analytics-card q-pa-lg bg-white shadow-2">
              <div class="text-h6 text-weight-bold text-green-9 q-mb-md">
                Peminjaman Tahun {{ stats.tahun_aktif || new Date().getFullYear() }}
              </div>
              <div class="row q-col-gutter-sm">
                <div class="col-4 col-sm-3" v-for="item in peminjamanBulanDisplay" :key="item.bulan">
                  <div class="month-chip-solid q-pa-sm text-center">
                    <div class="text-caption text-grey-7">{{ item.label }}</div>
                    <div class="text-subtitle2 text-green-9 text-weight-bold">{{ item.total }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- MAPS SECTION -->
    <section class="q-py-xl bg-location-soft" v-if="siteSettings.maps_embed_url">
      <div class="container q-pa-md">
         <div class="row items-center q-col-gutter-xl">
            <div class="col-12 col-md-5">
               <div class="text-overline text-green-7">LOKASI KAMI</div>
               <p class="text-h6 text-grey-7 q-mb-lg">{{ siteSettings.school_address || 'Alamat sekolah belum diatur.' }}</p>
               
               <div class="q-list">
                  <div class="row items-center q-mb-md text-grey-8">
                     <q-icon name="place" color="green-7" size="sm" class="q-mr-md" />
                     <span>{{ siteSettings.school_address_detail || 'Detail alamat belum diatur' }}</span>
                  </div>
                  <div class="row items-center q-mb-md text-grey-8">
                     <q-icon name="email" color="green-7" size="sm" class="q-mr-md" />
                     <span>{{ siteSettings.school_email || 'email@sekolah.sch.id' }}</span>
                  </div>
                   <div class="row items-center text-grey-8">
                     <q-icon name="phone" color="green-7" size="sm" class="q-mr-md" />
                     <span>{{ siteSettings.school_phone || '(021) 123456' }}</span>
                  </div>
               </div>
            </div>
            
            <div class="col-12 col-md-7">
               <div class="map-frame shadow-10 rounded-xl overflow-hidden">
                  <iframe
                    :src="siteSettings.maps_embed_url"
                    width="100%"
                    height="450"
                    style="border: 0"
                    allowfullscreen
                    loading="lazy"
                    class="grayscale-map hover-color"
                  ></iframe>
               </div>
            </div>
         </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-blue-grey-10 text-white q-py-lg">
      <div class="container q-pa-md">
        <div class="row justify-between items-center text-grey-6 text-caption">
          <div>&copy; {{ new Date().getFullYear() }} E-IPA Lab Management. All rights reserved.</div>
          <div class="row q-gutter-sm">
             <q-icon name="facebook" class="cursor-pointer hover-white" size="xs" />
             <q-icon name="ion-logo-instagram" class="cursor-pointer hover-white" size="xs" />
             <q-icon name="ion-logo-twitter" class="cursor-pointer hover-white" size="xs" />
          </div>
        </div>
      </div>
    </footer>
  </q-page>
</template>

<script>
import { ref } from 'vue';
import axios from 'axios';
import { mapState } from 'vuex';

export default {
  name: 'LandingPage',
  setup() {
    return {
      slide: ref(0),
      slides: ref([]),
      informasiAktif: ref(null),
      stats: ref({
        guru: 0,
        siswa: 0,
        katalog: 0,
        classroom: 0,
        tahun_aktif: new Date().getFullYear(),
        peminjaman_per_bulan: [],
        jumlah_jenis_alat: 0,
        total_unit_alat: 0,
      }),
      siteSettings: ref({
        maps_embed_url: '',
        school_name: '',
        school_address: '',
        school_address_detail: '',
        school_email: '',
        school_phone: ''
      }),
      features: [
        { icon: 'science', title: 'Manajemen Lab', desc: 'Sistem inventarisasi alat dan bahan praktikum yang terstruktur dan mudah dipantau.' },
        { icon: 'event_note', title: 'Penjadwalan', desc: 'Booking ruang laboratorium secara oniline, menghindari bentrok jadwal antar kelas.' },
        { icon: 'menu_book', title: 'E-Modul', desc: 'Akses ribuan materi ajar, modul praktikum, dan LKPD digital dimana saja.' },
        { icon: 'school', title: 'Data Siswa', desc: 'Pangkalan data siswa yang terintegrasi untuk pemantauan aktivitas lab.' },
      ],
    };
  },
  computed: {
    ...mapState('kontrol', ['url']),
    statsDisplay() {
      return [
        { label: 'Guru Aktif', value: this.stats.guru, icon: 'school' },
        { label: 'Siswa Terdaftar', value: this.stats.siswa, icon: 'groups' },
        { label: 'Katalog Alat', value: this.stats.katalog, icon: 'biotech' },
        { label: 'Kelas Praktikum', value: this.stats.classroom, icon: 'class' },
      ];
    },
    peminjamanBulanDisplay() {
      const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
      const source = Array.isArray(this.stats.peminjaman_per_bulan) ? this.stats.peminjaman_per_bulan : [];

      return labels.map((label, idx) => {
        const bulan = idx + 1;
        const found = source.find((item) => Number(item.bulan) === bulan);
        return {
          bulan,
          label,
          total: found ? Number(found.total || 0) : 0,
        };
      });
    },
  },
  methods: {
    async loadSlides() {
      try {
        const res = await axios.get('landing/slides');
        this.slides = res.data;
      } catch (e) {
        // Fallback dummy slides if empty or error, for UI preview
        if(this.slides.length === 0) {
           // Optional: logic to show dummy slides
        }
      }
    },
    async loadStats() {
      try {
        const res = await axios.get('landing/stats');
        this.stats = res.data;
      } catch (e) { console.log(e) }
    },
    async loadSettings() {
      try {
        const res = await axios.get('landing/settings');
        this.siteSettings = res.data;
      } catch (e) { console.log(e) }
    },
    async loadInformasi() {
      try {
        const res = await axios.get('informasi-terkini/aktif');
        const data = res.data || [];
        this.informasiAktif = data.length > 0 ? data[0] : null;
      } catch (e) {
        this.informasiAktif = null;
      }
    },
    labelTipe(tipe) {
      if (tipe === 'penutupan_lab') return 'PENUTUPAN LAB';
      if (tipe === 'peringatan') return 'PERINGATAN';
      return 'INFO';
    },
    toneInfo(tipe) {
      if (tipe === 'penutupan_lab') return { bg: 'red-8' };
      if (tipe === 'peringatan') return { bg: 'orange-8' };
      return { bg: 'green-7' };
    },
    formatPeriodeInfo(item) {
      const fmt = (value) => {
        if (!value) return '-';
        const date = new Date(String(value).replace(' ', 'T'));
        if (isNaN(date.getTime())) return '-';
        const d = String(date.getDate()).padStart(2, '0');
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const y = date.getFullYear();
        return `${d}-${m}-${y}`;
      };
      return `${fmt(item.mulai_at)} s/d ${fmt(item.selesai_at)}`;
    },
    scrollToSection(id) {
      const el = document.getElementById(id);
      if (el) el.scrollIntoView({ behavior: 'smooth' });
    },
  },
  created() {
    this.loadSlides();
    this.loadStats();
    this.loadSettings();
    this.loadInformasi();
  },
};
</script>

<style scoped lang="scss">
// UTILS
.container {
  max-width: 1200px;
  margin: 0 auto;
}

// FEATURES
.bg-gradient {
   background: linear-gradient(180deg, white 0%, #f1f8e9 100%);
}
.bg-green-limit {
   background-color: #e8f5e9; /* Green-1 matches svg fill */
}
.hero-section {
  background: white;
  position: relative;
}

.hero-container {
  max-width: 1600px;
  margin: 0 auto;
  width: 100%;
}

.blob {
  position: absolute;
  filter: blur(80px);
  z-index: 0;
  opacity: 0.6;
}
.blob-1 {
  top: -10%;
  left: -10%;
  width: 600px;
  height: 600px;
  background: #dcfce7; // green-100
  border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
  animation: morph 20s infinite;
}
.blob-2 {
  bottom: -10%;
  right: -5%;
  width: 500px;
  height: 500px;
  background: #ecfccb; // lime-100
  border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
  animation: morph 15s infinite reverse;
}

@keyframes morph {
  0% { border-radius: 40% 60% 60% 40% / 60% 30% 70% 40%; }
  100% { border-radius: 40% 60% 60% 40% / 60% 30% 70% 40%; }
}

.glass-morph {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.5);
}

.slider-container {
   border: 8px solid white; 
   transform: rotate(2deg);
   transition: transform 0.5s ease;
}
.slider-container:hover {
   transform: rotate(0deg) scale(1.02);
}

.custom-caption {
  background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
  width: 100%;
}

// FEATURES
.bg-green-limit {
   background-color: #e8f5e9;
}

.feature-card {
  border-radius: 20px;
  border: 1px solid #f0f0f0;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.icon-box {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.3s ease;
}

.hero-logo {
  width: 105px;
  height: 105px;
  object-fit: contain;
}
.hero-logo-lab {
  width: 105px;
  height: 105px;
}

.hero-lab-title {
  font-size: 28px;
  font-weight: 800;
  letter-spacing: 0.08em;
  line-height: 1.25;
}

.hero-info-card {
  background: linear-gradient(160deg, #1b5e20 0%, #2e7d32 45%, #388e3c 100%);
  border-radius: 16px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 8px 32px rgba(27, 94, 32, 0.25);
}

.info-header {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 14px 20px 10px;
  border-bottom: 1px solid rgba(255,255,255,0.12);
}

.info-live-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #69f0ae;
  box-shadow: 0 0 6px #69f0ae;
  animation: pulse-dot 2s ease-in-out infinite;
}

@keyframes pulse-dot {
  0%, 100% { opacity: 1; box-shadow: 0 0 6px #69f0ae; }
  50% { opacity: 0.5; box-shadow: 0 0 2px #69f0ae; }
}

.info-label {
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.16em;
  color: rgba(255,255,255,0.7);
  text-transform: uppercase;
}

.info-body {
  padding: 16px 20px;
  flex: 1;
}

.info-tipe-row {
  margin-bottom: 12px;
}

.info-badge {
  font-size: 0.68rem;
  font-weight: 600;
  padding: 3px 10px;
  letter-spacing: 0.03em;
  border-radius: 4px;
}

.info-judul {
  font-size: 1.18rem;
  font-weight: 700;
  color: #ffffff;
  line-height: 1.4;
  margin-bottom: 8px;
}

.info-isi {
  font-size: 0.84rem;
  color: rgba(255,255,255,0.72);
  line-height: 1.6;
}

.info-footer {
  display: flex;
  align-items: center;
  padding: 10px 20px;
  font-size: 0.72rem;
  font-weight: 600;
  color: rgba(255,255,255,0.55);
  letter-spacing: 0.02em;
  border-top: 1px solid rgba(255,255,255,0.1);
  background: rgba(0,0,0,0.08);
}

@media (max-width: 1023px) {
  .hero-lab-title {
    font-size: 22px;
    letter-spacing: 0.06em;
  }
}

@media (max-width: 599px) {
  .hero-lab-title {
    font-size: 18px;
    letter-spacing: 0.04em;
  }
  .hero-logo {
    width: 65px;
    height: 65px;
  }
  .hero-logo-lab {
    width: 65px;
    height: 65px;
  }
}

// Info card alignment — only offset on desktop (sm+)
.info-card-col {
  margin-top: 0;
}
@media (min-width: 600px) {
  .info-card-col {
    margin-top: 80px;
  }
}

// STATS
.bg-stats-gradient {
  background: linear-gradient(135deg, #1b5e20 0%, #004d40 100%); /* Green-9 to Teal-9 */
}
.bg-analytics-soft {
  background: linear-gradient(180deg, #f7fff8 0%, #ecf8ef 100%);
}

.bg-location-soft {
  background: linear-gradient(180deg, #e8f5e9 0%, #dff3e3 100%);
}
.circle-deco {
   position: absolute;
   background: rgba(255,255,255,0.05);
   border-radius: 50%;
}
.circle-1 { top: -100px; left: -100px; width: 400px; height: 400px; }
.circle-2 { bottom: -50px; right: -50px; width: 300px; height: 300px; }

.stat-card {
   transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
   border: 1px solid rgba(255,255,255,0.1);
   border-radius: 24px; /* Rounded XL */
}

.stat-detail-card {
   border: 1px solid rgba(255,255,255,0.14);
   border-radius: 24px;
}

.analytics-card {
   border-radius: 18px;
   border: 1px solid #e2f0e5;
}

.month-chip {
   border-radius: 12px;
   background: rgba(255,255,255,0.12);
   border: 1px solid rgba(255,255,255,0.12);
}

.month-chip-solid {
   border-radius: 12px;
   background: #f5faf6;
   border: 1px solid #e0eee3;
}

.glass-effect {
   background: rgba(255,255,255,0.1);
   backdrop-filter: blur(10px);
}
.gradient-overlay {
   background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
   opacity: 0;
   transition: opacity 0.4s ease;
}


.icon-ring {
   width: 60px;
   height: 60px;
   background: rgba(255,255,255,0.15); /* Soft blended background */
   border-radius: 50%;
   transition: all 0.4s ease;
   box-shadow: inset 0 0 10px rgba(255,255,255,0.1);
}
.icon-anim {
   transition: transform 0.4s ease;
}

.text-yellow-4 { color: #facc15; }
.opacity-80 { opacity: 0.8; }

// MAPS
.map-frame {
   border: 8px solid white;
}
.grayscale-map {
   filter: grayscale(100%);
   transition: filter 0.5s ease;
}
.grayscale-map:hover {
   filter: grayscale(0%);
}

// FOOTER
.footer-link {
  color: #b0bec5;
  text-decoration: none;
  transition: color 0.3s;
}
.footer-link:hover {
  color: #4caf50;
  padding-left: 5px;
}
.hover-white:hover {
   color: white;
}
.opacity-20 { opacity: 0.2; }
</style>
