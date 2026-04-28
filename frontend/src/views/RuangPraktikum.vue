<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
      <q-card v-if="user.user.role_id == 3">
        <q-card-section>
          <q-breadcrumbs>
            <q-breadcrumbs-el :label="user.user.name" icon="home" to="/" class="text-green-7" />
            <q-breadcrumbs-el label="Praktikum" icon="cast_for_education" to="/praktikum" />
            <q-breadcrumbs-el label="Ruang Praktikum" icon="science" />
          </q-breadcrumbs>

          <q-banner class="bg-green-1 text-green-10 q-mt-md" rounded>
            <div class="row items-center justify-between q-col-gutter-md">
              <div class="col-12 col-md-7">
                <div class="text-subtitle1 text-weight-bold">{{ ruangPraktikum }}</div>
                <div class="text-caption">Kelas: {{ kelasPraktikum || '-' }} | Guru: {{ guruPengampu || user.user.name }}</div>
              </div>
              <div class="col-12 col-md-5 text-right q-gutter-sm">
                <q-btn label="Materi" icon="o_menu_book" color="green-7" unelevated no-caps @click="dialogInsert = true" />
                <q-btn label="Tugas" icon="o_assignment" color="orange-8" unelevated no-caps @click="dialogTugas = true" />
                <q-btn label="Absensi" icon="o_how_to_reg" color="blue-7" unelevated no-caps @click="dialogAbsen = true" />
              </div>
            </div>
          </q-banner>
        </q-card-section>
        <q-card-section>
          <!-- MODUL AJAR -->
          <q-list v-if="moduls.length" padding dense>
            <div class="section-head q-mb-sm">
              <div class="row items-center justify-between">
                <div class="row items-center no-wrap">
                  <q-avatar color="green-7" text-color="white" icon="o_menu_book" />
                  <div class="q-ml-sm">
                    <div class="text-subtitle1 text-weight-bold">Modul Ajar</div>
                    <div class="text-caption text-grey-7">Materi pembelajaran untuk kelas {{ kelasPraktikum || '-' }}</div>
                  </div>
                </div>
                <q-chip dense color="green-1" text-color="green-9" icon="o_inventory_2">{{ moduls.length }} Materi</q-chip>
              </div>
            </div>
            <div v-for="row in moduls" :key="row.id" class="q-mb-sm">
              <q-expansion-item class="section-item" header-class="text-green-9" switch-toggle-side dense>
                <template v-slot:header>
                  <q-item-section avatar>
                    <q-avatar icon="far fa-clipboard" color="green-7" text-color="white" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="text-h6">{{ row.judul }}</q-item-label>
                    <q-item-label caption lines="2">{{ row.des }}</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <div class="row items-center">
                      <q-btn icon="more_vert" flat round>
                        <q-menu auto-close>
                          <q-list style="min-width: 100px">
                            <q-item clickable @click="edit(row.id)">
                              <q-item-section>Edit</q-item-section>
                            </q-item>
                            <q-item clickable @click="hapus(row.id)">
                              <q-item-section>Hapus</q-item-section>
                            </q-item>
                          </q-list>
                        </q-menu>
                      </q-btn>
                    </div>
                  </q-item-section>
                </template>
                <q-card>
                  <q-card-section>
                    <!-- Banner Modul (File dari Laboran) -->
                    <a :href="getDownloadUrl(row.modul_file_path)" target="_blank" v-if="row.modul_file_path">
                      <q-banner rounded class="bg-grey-3">
                        <template v-slot:avatar>
                          <q-icon
                            :name="getFileIcon(row.modul_extension, row.modul_file_path)"
                            :color="getIconColor(row.modul_extension, row.modul_file_path)"
                            size="lg"
                          />
                        </template>
                        <div>{{ row.modul_file_name }}</div>
                      </q-banner>
                    </a>


                    <!-- 🔗 Link Tambahan (Jika Ada) -->
                    <q-card-section v-if="row.link_tambahan" class="q-pa-none">
                      <q-separator spaced />
                      <q-item clickable tag="a" :href="row.link_tambahan" target="_blank">
                        <q-item-section avatar>
                          <q-icon name="open_in_new" color="primary" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label class="text-primary">🔗 Link Tambahan</q-item-label>
                          <q-item-label caption lines="2">{{ row.link_tambahan }}</q-item-label>
                        </q-item-section>
                      </q-item>
                    </q-card-section>
                  </q-card-section>
                </q-card>
              </q-expansion-item>
            </div>
          </q-list>

          <!-- TUGAS & ABSENSI — TIDAK DIUBAH -->
          <q-list v-if="tugas.length" padding class="q-my-sm">
            <div class="section-head q-mb-sm">
              <div class="row items-center justify-between">
                <div class="row items-center no-wrap">
                  <q-avatar color="orange-8" text-color="white" icon="o_assignment" />
                  <div class="q-ml-sm">
                    <div class="text-subtitle1 text-weight-bold">Penugasan</div>
                    <div class="text-caption text-grey-7">Daftar tugas aktif untuk siswa di kelas ini</div>
                  </div>
                </div>
                <q-chip dense color="orange-1" text-color="orange-9" icon="o_checklist">{{ tugas.length }} Tugas</q-chip>
              </div>
            </div>
            <div v-for="tgs in tugas" :key="tgs.id" class="q-mb-sm">
              <q-expansion-item class="section-item" header-class="text-green-9" switch-toggle-side>
                <template v-slot:header>
                  <q-item-section avatar>
                    <q-avatar icon="far fa-clipboard" color="green-7" text-color="white" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="text-h6">{{ tgs.jt }}</q-item-label>
                    <q-item-label caption lines="2">{{ tgs.soal }}</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <div class="row items-center">
                      <q-btn icon="more_vert" flat round>
                        <q-menu auto-close>
                          <q-list style="min-width: 100px">
                            <q-item clickable @click="editTugas(tgs.id)">
                              <q-item-section>Edit</q-item-section>
                            </q-item>
                            <q-item clickable @click="hapusTugas(tgs.id)">
                              <q-item-section>Hapus</q-item-section>
                            </q-item>
                          </q-list>
                        </q-menu>
                      </q-btn>
                    </div>
                  </q-item-section>
                </template>
                <q-card>
                  <q-card-section>
                    <q-banner rounded class="bg-grey-3">
                      <data-penugasan :tugas_id="tgs.id" />
                    </q-banner>
                  </q-card-section>
                </q-card>
              </q-expansion-item>
            </div>
          </q-list>

          <q-list v-if="absensis.length" padding>
            <div class="section-head q-mb-sm">
              <div class="row items-center justify-between">
                <div class="row items-center no-wrap">
                  <q-avatar color="blue-7" text-color="white" icon="o_how_to_reg" />
                  <div class="q-ml-sm">
                    <div class="text-subtitle1 text-weight-bold">Absensi</div>
                    <div class="text-caption text-grey-7">Kontrol sesi kehadiran dan status buka/tutup</div>
                  </div>
                </div>
                <q-chip dense color="blue-1" text-color="blue-9" icon="o_event_available">{{ absensis.length }} Sesi</q-chip>
              </div>
            </div>
            <div v-for="abs in absensis" :key="abs.id" class="q-mb-sm">
              <q-expansion-item class="section-item" header-class="text-green-9" switch-toggle-side>
                <template v-slot:header>
                  <q-item-section avatar>
                    <q-avatar icon="far fa-clipboard" color="green-7" text-color="white" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="text-h6">
                      Absensi Hari {{ hari(abs.tgl_absen) }} Tanggal {{ dateTime(abs.tgl_absen) }}
                    </q-item-label>
                    <q-item-label caption lines="2">Buka: {{ abs.jam_buka }} Tutup: {{ abs.jam_tutup }}</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <div class="row items-center">
                      <q-btn rounded :label="statusAbsensiLabel(abs.status)" :color="statusAbsensiColor(abs.status)" text-color="white" />
                      <q-btn icon="more_vert" flat round class="q-ml-sm">
                        <q-menu auto-close>
                          <q-list style="min-width: 100px">
                            <q-item clickable @click="setStatus(abs.id)">
                              <q-item-section>{{ isAbsensiClosed(abs.status) ? 'Buka Absensi' : 'Tutup Absensi' }}</q-item-section>
                            </q-item>
                            <q-item clickable @click="hapusAbsen(abs.id)">
                              <q-item-section>Hapus</q-item-section>
                            </q-item>
                          </q-list>
                        </q-menu>
                      </q-btn>
                    </div>
                  </q-item-section>
                </template>
                <q-card>
                  <q-card-section>
                    <q-banner rounded class="bg-grey-3">
                      <data-absen :absen_id="abs.id" />
                    </q-banner>
                  </q-card-section>
                </q-card>
              </q-expansion-item>
            </div>
          </q-list>
        </q-card-section>
      </q-card>
    </div>
    <!-- DIALOG MATERI AJAR -->
    <q-dialog v-model="dialogInsert">
      <q-card style="width: 500px; max-width: 80vw;">
        <q-toolbar>
          <q-toolbar-title class="text-green-7"><span class="text-weight-medium">MATERI AJAR</span></q-toolbar-title>
          <q-btn flat round dense icon="close" v-close-popup />
        </q-toolbar>
        <q-separator />
        <q-card-section style="max-height: 60vh" class="scroll">
          <q-form>
            <q-input v-model="judul" label="Judul Materi" />
            <q-input v-model="des" label="Deskripsi" autogrow />
            <!-- Pilih Modul dari Laboran -->
            <div class="q-mb-md">
              <q-input
                v-model="selectedModulJudul"
                label="Modul yang Dipilih"
                readonly
                dense
                :bg-color="selectedModulId ? 'green-1' : 'grey-2'"
              >
                <template v-slot:prepend>
                  <q-icon :name="selectedModulId ? 'check_circle' : 'help'" :color="selectedModulId ? 'green' : 'grey'" />
                </template>
                <template v-slot:append>
                  <q-btn label="Pilih Modul" color="primary" dense @click="loadModulDariLaboran" />
                </template>
              </q-input>
              <div class="q-mt-sm">
                <q-btn label="Upload Modul" color="green-7" outline dense icon="upload_file" @click="dialogUploadModul = true" />
              </div>
              <div v-if="selectedModulId" class="text-caption text-grey q-mt-xs">
                File: {{ selectedModulFileName }}
              </div>
            </div>
            <!-- 🔗 Link Tambahan (Opsional) -->
            <q-input
              v-model="link_tambahan"
              label="Link Tambahan (Opsional)"
              placeholder="https://youtu.be/..., https://drive.google.com/..."
              dense
              clearable
              class="q-mt-md"
            >
              <template v-slot:prepend>
                <q-icon name="link" />
              </template>
            </q-input>
            <div v-if="link_tambahan" class="text-caption text-primary q-mt-xs">
              Contoh: Video penjelasan, slide tambahan, dll.
            </div>
          </q-form>
        </q-card-section>
        <q-separator />
        <q-card-actions align="right" class="bg-white">
          <q-btn label="simpan" color="green-10" @click="simpan" />
          <q-btn label="Batal" color="red-10" @click="batal" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- DIALOG PILIH MODUL -->
    <q-dialog v-model="dialogPilihModul" persistent>
      <q-card style="min-width: 500px; max-width: 90vw; width: 600px;">
        <q-card-section>
          <div class="text-h6">Pilih Modul dari Laboran</div>
          <q-input
            v-model="modulFilter"
            dense
            placeholder="Cari judul/modul/uploader by..."
            clearable
            debounce="300"
            class="q-mt-sm"
          >
            <template v-slot:prepend>
              <q-icon name="search" />
            </template>
          </q-input>
        </q-card-section>

        <q-card-section class="q-pt-none" style="max-height: 400px; overflow-y: auto;">
          <q-list dense separator>
            <q-item
              v-for="modul in filteredModulList"
              :key="modul.id"
              clickable
              v-ripple
              :active="selectedModulId === modul.id"
              @click="selectModul(modul)"
            >
              <q-item-section avatar>
                <q-icon :name="getFileIcon(modul.extension, modul.file_path)" :color="getIconColor(modul.extension, modul.file_path)" />
              </q-item-section>
              <q-item-section>
                <q-item-label>{{ modul.judul }}</q-item-label>
                <q-item-label caption>{{ modul.file_name }} · {{ modul.uploader_name || 'Laboran' }}</q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-btn
                  flat
                  dense
                  round
                  :icon="isExternalLink(modul.file_path) ? 'open_in_new' : 'download'"
                  @click.stop="downloadModul(modul)"
                />
              </q-item-section>
            </q-item>
            <q-item v-if="filteredModulList.length === 0">
              <q-item-section>
                <q-item-label class="text-grey">Tidak ada modul ditemukan</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Batal" color="primary" v-close-popup />
          <q-btn flat label="Pilih" color="primary" :disable="!selectedModulId" @click="confirmPilihModul" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- DIALOG UPLOAD MODUL -->
    <q-dialog v-model="dialogUploadModul" persistent>
      <q-card style="width: 520px; max-width: 90vw;">
        <q-card-section>
          <div class="text-h6">Upload Modul LKPD</div>
        </q-card-section>
        <q-card-section class="q-pt-none">
          <q-input v-model="uploadModulJudul" label="Judul Modul*" dense outlined class="q-mb-sm" />
          <q-file
            v-model="uploadModulFile"
            label="Pilih File Modul*"
            outlined
            dense
            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
          >
            <template v-slot:prepend>
              <q-icon name="attach_file" />
            </template>
          </q-file>
          <div class="text-caption text-grey q-mt-xs">Maks 20MB (PDF, DOCX, XLSX, PPTX)</div>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat color="grey-8" label="Batal" @click="resetUploadModul" />
          <q-btn color="green-7" label="Upload" :loading="uploadingModul" @click="uploadModulMandiri" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- DIALOG TUGAS & ABSENSI — TIDAK DIUBAH -->
    <q-dialog v-model="dialogTugas">
      <q-card style="width: 500px; max-width: 80vw;">
        <q-toolbar>
          <q-toolbar-title class="text-green-7"><span class="text-weight-medium">TUGAS</span></q-toolbar-title>
          <q-btn flat round dense icon="close" v-close-popup />
        </q-toolbar>
        <q-separator />
        <q-card-section style="max-height: 60vh" class="scroll">
          <q-form>
            <q-input v-model="jt" label="Judul Tugas" />
            <q-input v-model="soal" label="Deskripsi" autogrow />
            <!-- ✅ Opsi Pengumpulan Tugas -->
            <div class="q-mt-sm">
              <div class="text-caption text-grey-7 q-mb-xs">Opsi Pengumpulan:</div>
              <div class="row q-gutter-sm">
                <q-checkbox dense v-model="tipe_esay" label="Izinkan Esay" color="green-7" />
                <q-checkbox dense v-model="tipe_upload" label="Izinkan Upload File" color="primary" />
                <q-checkbox dense v-model="tipe_link" label="Izinkan Tautan" color="purple" />
              </div>
            </div>
          </q-form>
        </q-card-section>
        <q-separator />
        <q-card-actions align="right" class="bg-white">
          <q-btn label="simpan" color="green-10" @click="post" />
          <q-btn label="Batal" color="red-10" @click="batal" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogAbsen">
      <q-card style="width: 500px; max-width: 80vw;">
        <q-toolbar>
          <q-toolbar-title class="text-green-7"><span class="text-weight-medium">ABSENSI</span></q-toolbar-title>
          <q-btn flat round dense icon="close" v-close-popup />
        </q-toolbar>
        <q-separator />
        <q-card-section style="max-height: 60vh" class="scroll">
          <q-form>
            <q-input
              dense
              v-model="tgl_absen"
              label="Tanggal"
              class="q-my-sm"
              mask="date"
            >
              <template v-slot:append>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                    <q-date v-model="tgl_absen" color="green-7">
                      <div class="row items-center justify-end">
                        <q-btn v-close-popup label="Close" color="green-7" flat />
                      </div>
                    </q-date>
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
            <q-input
              dense
              v-model="jam_buka"
              label="Jam Buka"
              mask="time"
              class="q-my-sm"
              style="width: 200px"
            >
              <template v-slot:append>
                <q-icon name="access_time" class="cursor-pointer">
                  <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                    <q-time v-model="jam_buka" color="green-7">
                      <div class="row items-center justify-end">
                        <q-btn v-close-popup label="Close" color="green-7" flat />
                      </div>
                    </q-time>
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
            <q-input
              dense
              v-model="jam_tutup"
              label="Jam Tutup"
              mask="time"
              class="q-my-sm"
              style="width: 200px"
            >
              <template v-slot:append>
                <q-icon name="access_time" class="cursor-pointer">
                  <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                    <q-time v-model="jam_tutup" color="green-7">
                      <div class="row items-center justify-end">
                        <q-btn v-close-popup label="Close" color="green-7" flat />
                      </div>
                    </q-time>
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
          </q-form>
        </q-card-section>
        <q-separator />
        <q-card-actions align="right" class="bg-white">
          <q-btn label="simpan" color="green-10" @click="absenPost" />
          <q-btn label="Batal" color="red-10" @click="batal" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { ref } from '@vue/reactivity';
import { mapGetters, mapState } from 'vuex';
import DataAbsen from '@/components/DataAbsen.vue';
import DataPenugasan from '@/components/DataPenugasan.vue';
import axios from 'axios';
import moment from 'moment';
import 'moment/locale/id';
moment.locale('id');

export default {
  components: {
    DataAbsen,
    DataPenugasan,
  },
  props: ['class_id'],
  setup() {
    return {
      dialogInsert: ref(false),
      dialogTugas: ref(false),
      dialogAbsen: ref(false),
      dialogPilihModul: ref(false),
      dialogUploadModul: ref(false),
      uploadingModul: ref(false),
      moduls: ref([]),
      tugas: ref([]),
      absensis: ref([]),
      ruangPraktikum: ref(''),
      kelasPraktikum: ref(''),
      guruPengampu: ref(''),
      id: ref(''),
      judul: ref(''),
      des: ref(''),
      jt: ref(''),
      soal: ref(''),
      // ✅ Tipe Submission (Default TRUE)
      tipe_esay: ref(true),
      tipe_upload: ref(true),
      tipe_link: ref(true),
      tgl_absen: ref(''),
      jam_buka: ref(''),
      jam_tutup: ref(''),
      // State untuk modul
      selectedModulId: ref(null),
      selectedModulJudul: ref(''),
      selectedModulFileName: ref(''),
      modulList: ref([]),
      modulFilter: ref(''),
      uploadModulJudul: ref(''),
      uploadModulFile: ref(null),
      // ✅ BARU: Link Tambahan
      link_tambahan: ref(''),
    };
  },
  computed: {
    ...mapGetters({
      authenticated: 'auth/authenticated',
      user: 'auth/user',
    }),
    filteredModulList() {
      const q = (this.modulFilter || '').toString().toLowerCase();
      if (!q) return this.modulList || [];
      return (this.modulList || []).filter(m => {
        const judul = (m.judul || '').toString().toLowerCase();
        const file = (m.file_name || '').toString().toLowerCase();
        const uploader = (m.uploader_name || '').toString().toLowerCase();
        return judul.includes(q) || file.includes(q) || uploader.includes(q);
      });
    },
    ...mapState('kontrol', ['url']),
  },
  methods: {
    dateTime(value) {
      return moment(value).format('LL');
    },
    hari(value) {
      return moment(value).format('dddd');
    },
    batal() {
      this.dialogInsert = false;
      this.dialogTugas = false;
      this.dialogAbsen = false;
      // Reset form materi
      this.id = '';
      this.judul = '';
      this.des = '';
      this.selectedModulId = null;
      this.selectedModulJudul = '';
      this.selectedModulFileName = '';
      this.selectedModulFileName = '';
      this.link_tambahan = '';
      this.resetUploadModul();
      // Reset Tipe Submission
      this.tipe_esay = true;
      this.tipe_upload = true;
      this.tipe_link = true;
    },

    async cekRuang() {
      try {
        const res = await axios.get(`classroom/cek/${this.class_id}`);
        this.ruangPraktikum = res.data.katalog.topik;
        this.kelasPraktikum = res.data.kelas?.kelas || '';
        this.guruPengampu = res.data.user?.name || this.user.user.name;
      } catch (e) {
        this.ruangPraktikum = 'Ruang Praktikum';
        this.kelasPraktikum = '';
        this.guruPengampu = this.user.user.name;
      }
    },
    async getMateri() {
      try {
        const res = await axios.get(`materi_ajar/${this.class_id}`);
        this.moduls = res.data;
      } catch (e) {
        this.moduls = [];
        this.$toast.error('Gagal memuat materi ajar');
      }
    },
    async getTugas() {
      try {
        const res = await axios.get(`penugasan/${this.class_id}`);
        this.tugas = res.data;
      } catch (e) {
        this.tugas = [];
        this.$toast.error('Gagal memuat tugas');
      }
    },
    async getAbsensi() {
      try {
        const res = await axios.get(`absensi/${this.class_id}`);
        this.absensis = res.data;
      } catch (e) {
        this.absensis = [];
        this.$toast.error('Gagal memuat absensi');
      }
    },

    // Method Modul
    async fetchModulList() {
      const res = await axios.get('modul/lkpd');
      this.modulList = res.data;
      return res.data;
    },
    async loadModulDariLaboran() {
      this.dialogPilihModul = true;
      try {
        await this.fetchModulList();
      } catch (error) {
        this.$toast.error('Gagal memuat daftar modul');
      }
    },
    resetUploadModul() {
      this.uploadModulJudul = '';
      this.uploadModulFile = null;
      this.uploadingModul = false;
      this.dialogUploadModul = false;
    },
    async uploadModulMandiri() {
      if (!this.uploadModulJudul || !this.uploadModulJudul.toString().trim()) {
        this.$toast.warning('Judul modul wajib diisi');
        return;
      }

      if (!this.uploadModulFile) {
        this.$toast.warning('File modul wajib dipilih');
        return;
      }

      const form = new FormData();
      form.append('judul', this.uploadModulJudul);
      form.append('file', this.uploadModulFile);

      this.uploadingModul = true;
      try {
        const res = await axios.post('modul/lkpd', form, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });

        this.$toast.success('Modul berhasil diupload');
        await this.fetchModulList();

        const uploaded = res.data || {};
        this.selectedModulId = uploaded.id || null;
        this.selectedModulJudul = uploaded.judul || this.uploadModulJudul;
        this.selectedModulFileName = uploaded.file_name || (this.uploadModulFile && this.uploadModulFile.name) || '';

        this.resetUploadModul();
      } catch (error) {
        const msg = error.response?.data?.message || 'Gagal upload modul';
        this.$toast.error(msg);
      } finally {
        this.uploadingModul = false;
      }
    },
    selectModul(modul) {
      this.selectedModulId = modul.id;
      this.selectedModulJudul = modul.judul;
      this.selectedModulFileName = modul.file_name;
    },
    confirmPilihModul() {
      this.dialogPilihModul = false;
    },
    downloadModul(modul) {
      const url = this.getDownloadUrl(modul.file_path);
      window.open(url, '_blank');
    },

    // ✅ Simpan dengan link_tambahan — pakai this.$toast
    async simpan() {
  if (!this.judul || !this.judul.toString().trim()) {
    this.$toast.warning('Judul materi wajib diisi');
    return;
  }

  const form = new FormData();
  form.append('id', this.id);
  form.append('class_id', this.class_id);
  form.append('judul', this.judul);
  form.append('des', this.des);
  // ✅ modul_id opsional — kirim hanya jika dipilih
  if (this.selectedModulId) {
    form.append('modul_id', this.selectedModulId);
  }
  // ✅ link_tambahan opsional — kirim hanya jika diisi
  if (this.link_tambahan) {
    form.append('link_tambahan', this.link_tambahan);
  }

  try {
    await axios.post('materi_ajar', form);
    this.$toast.success('Materi berhasil disimpan');
    this.batal();
    this.getMateri();
  } catch (error) {
    console.error('Error simpan materi:', error);
    const resp = error.response && error.response.data;
    if (resp && resp.message) {
      if (resp.errors) {
        const first = Object.values(resp.errors)[0];
        this.$toast.error(first[0] || resp.message);
      } else {
        this.$toast.error(resp.message);
      }
    } else {
      this.$toast.error('Gagal menyimpan materi');
    }
  }
},

    async edit($id) {
      try {
        const res = await axios.get(`materi_ajar/edit/${$id}`);
        const data = res.data;
        this.id = data.id;
        this.judul = data.judul;
        this.des = data.des;
        this.link_tambahan = data.link_tambahan || '';
        if (data.modul_id) {
          this.selectedModulId = data.modul_id;
          this.selectedModulJudul = data.modul_judul || 'Modul tidak ditemukan';
          this.selectedModulFileName = data.modul_file_name || '';
        }
        this.dialogInsert = true;
      } catch (e) {
        console.error('Error edit materi:', e);
        this.$toast.error('Gagal memuat data');
      }
    },

    // Helper Icon
    isExternalLink(path) {
      const value = (path || '').toString().trim().toLowerCase();
      return value.startsWith('http://') || value.startsWith('https://');
    },
    getFileIcon(ext, filePath = '') {
      if (this.isExternalLink(filePath)) return 'link';
      const e = ext?.toLowerCase() || '';
      if (e === 'pdf') return 'picture_as_pdf';
      if (['doc', 'docx'].includes(e)) return 'description';
      if (['xls', 'xlsx'].includes(e)) return 'grid_on';
      if (['ppt', 'pptx'].includes(e)) return 'slideshow';
      return 'insert_drive_file';
    },
    getIconColor(ext, filePath = '') {
      if (this.isExternalLink(filePath)) return 'teal';
      const e = ext?.toLowerCase() || '';
      if (e === 'pdf') return 'red';
      if (['doc', 'docx'].includes(e)) return 'blue';
      if (['xls', 'xlsx'].includes(e)) return 'green';
      if (['ppt', 'pptx'].includes(e)) return 'orange';
      return 'grey';
    },
    getDownloadUrl(filePath) {
      if (this.isExternalLink(filePath)) return filePath;
      return `${this.url}${filePath}`;
    },
    isAbsensiClosed(status) {
      if (status === null || status === undefined) return false;
      const normalized = String(status).toLowerCase().trim();
      return ['0', 'close', 'closed', 'false'].includes(normalized);
    },
    statusAbsensiLabel(status) {
      return this.isAbsensiClosed(status) ? 'Close' : 'Open';
    },
    statusAbsensiColor(status) {
      return this.isAbsensiClosed(status) ? 'grey-7' : 'green-7';
    },

    // Method lain — semua pakai this.$toast
    async post() {
      const form = new FormData();
      form.append('tugas_id', this.tugas_id);
      form.append('class_id', this.class_id);
      form.append('jt', this.jt);
      form.append('soal', this.soal);
      form.append('tipe_esay', this.tipe_esay ? 1 : 0);
      form.append('tipe_upload', this.tipe_upload ? 1 : 0);
      form.append('tipe_link', this.tipe_link ? 1 : 0);
      try {
        await axios.post('penugasan', form);
        this.$toast.success('berhasil tersimpan');
        this.batal();
        this.getTugas();
      } catch (e) {
        this.$toast.error('Gagal, Mohon Cek kembali');
      }
    },
    async absenPost() {
      const form = new FormData();
      form.append('class_id', this.class_id);
      form.append('absen_id', this.absen_id);
      form.append('tgl_absen', this.tgl_absen);
      form.append('jam_buka', this.jam_buka);
      form.append('jam_tutup', this.jam_tutup);
      try {
        await axios.post('absensi', form);
        this.$toast.success('berhasil dibuat');
        this.getAbsensi();
        this.dialogAbsen = false;
      } catch (e) {
        this.$toast.error('Gagal, Mohon Cek kembali');
      }
    },
    async hapus($id) {
      try {
        await axios.delete(`materi_ajar/hapus/${$id}`);
        this.$toast.success('berhasil dihapus');
        this.getMateri();
      } catch (e) {
        this.$toast.error('Gagal menghapus materi');
      }
    },
    async hapusTugas($id) {
      try {
        await axios.delete(`penugasan/hapus/${$id}`);
        this.$toast.success('berhasil dihapus');
        this.getTugas();
      } catch (e) {
        this.$toast.error('Gagal menghapus tugas');
      }
    },
    async hapusAbsen($id) {
      try {
        await axios.delete(`absensi/hapus/${$id}`);
        this.$toast.success('berhasil dihapus');
        this.getAbsensi();
      } catch (e) {
        this.$toast.error('Gagal menghapus absensi');
      }
    },
    async setStatus($id) {
      try {
        await axios.put(`absensi/status/${$id}`);
        this.getAbsensi();
        this.$toast.success('Status berhasil diubah');
      } catch (e) {
        this.$toast.error('Gagal mengubah status');
      }
    },
    async editTugas($id) {
      try {
        const res = await axios.get(`penugasan/edit/${$id}`);
        this.tugas_id = res.data.id;
        this.jt = res.data.jt;
        this.soal = res.data.soal;
        // Load opsi (handle jika null -> anggap true untuk backward compatibility)
        this.tipe_esay = res.data.tipe_esay !== 0;
        this.tipe_upload = res.data.tipe_upload !== 0;
        this.tipe_link = res.data.tipe_link !== 0;
        this.dialogTugas = true;
      } catch (e) {
        this.$toast.error('Gagal memuat data tugas');
      }
    },
  },
  created() {
    this.cekRuang();
    this.getMateri();
    this.getTugas();
    this.getAbsensi();
  },
};
</script>

<style scoped lang="sass">
.section-head
  background: linear-gradient(180deg, #f4fbf5 0%, #ffffff 100%)
  border: 1px solid #d8e8da
  border-radius: 12px
  padding: 10px 12px

.section-item
  border: 1px solid #dfe9e1
  border-radius: 12px
  background: #ffffff
  overflow: hidden
</style>
