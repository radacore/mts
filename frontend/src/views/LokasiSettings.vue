<template>
  <q-page class="q-pa-sm">
    <q-card>
      <q-card-section>
        <div class="text-h6">Pengaturan Lokasi & Site</div>
        <div class="text-caption text-grey">Hanya Super Admin</div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <q-form @submit.prevent="saveSettings" class="q-gutter-md">
          <q-input
            v-model="schoolName"
            label="Nama Sekolah/Institusi"
            outlined
            dense
          />
          <q-input
            v-model="schoolAddress"
            label="Alamat Lengkap (Untuk Header/Footer)"
            outlined
            dense
            type="textarea"
            rows="2"
          />
          <q-input
            v-model="schoolAddressDetail"
            label="Detail Alamat (Wonosari, Kec...)"
            outlined
            dense
          />
          <div class="row q-col-gutter-sm">
            <div class="col-12 col-md-6">
              <q-input
                v-model="schoolEmail"
                label="Email Sekolah"
                outlined
                dense
              />
            </div>
            <div class="col-12 col-md-6">
              <q-input
                v-model="schoolPhone"
                label="No. Telepon"
                outlined
                dense
              />
            </div>
          </div>
          <q-input
            v-model="mapsEmbedUrl"
            label="Google Maps Embed URL"
            outlined
            dense
            hint="Contoh: https://www.google.com/maps/embed?pb=..."
          >
            <template v-slot:append>
              <q-btn flat dense icon="help" @click="showHelp = true" />
            </template>
          </q-input>

          <!-- Preview Maps -->
          <div v-if="mapsEmbedUrl" class="q-mt-md">
            <div class="text-subtitle2 q-mb-sm">Preview Maps:</div>
            <div class="rounded-borders overflow-hidden" style="border: 1px solid #ddd">
              <iframe
                :src="mapsEmbedUrl"
                width="100%"
                height="300"
                style="border: 0"
                loading="lazy"
              ></iframe>
            </div>
          </div>

          <q-btn
            label="Simpan Pengaturan"
            type="submit"
            color="green-7"
            icon="save"
            :loading="loading"
          />
        </q-form>
      </q-card-section>
    </q-card>

    <!-- Help Dialog -->
    <q-dialog v-model="showHelp">
      <q-card style="max-width: 500px">
        <q-card-section>
          <div class="text-h6">Cara Mendapatkan Google Maps Embed URL</div>
        </q-card-section>
        <q-card-section>
          <ol class="q-pl-md">
            <li>Buka <a href="https://maps.google.com" target="_blank">Google Maps</a></li>
            <li>Cari lokasi sekolah Anda</li>
            <li>Klik tombol "Share" (Bagikan)</li>
            <li>Pilih tab "Embed a map"</li>
            <li>Klik "Copy HTML"</li>
            <li>Paste di sini, ambil bagian URL dari <code>src="..."</code></li>
          </ol>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Tutup" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { ref } from 'vue';
import axios from 'axios';

export default {
  name: 'LokasiSettings',
  setup() {
    return {
      schoolName: ref(''),
      schoolAddress: ref(''),
      schoolAddressDetail: ref(''),
      schoolEmail: ref(''),
      schoolPhone: ref(''),
      mapsEmbedUrl: ref(''),
      loading: ref(false),
      showHelp: ref(false),
    };
  },
  methods: {
    async loadSettings() {
      try {
        const res = await axios.get('landing/settings');
        this.schoolName = res.data.school_name || '';
        this.schoolAddress = res.data.school_address || '';
        this.schoolAddressDetail = res.data.school_address_detail || '';
        this.schoolEmail = res.data.school_email || '';
        this.schoolPhone = res.data.school_phone || '';
        this.mapsEmbedUrl = res.data.maps_embed_url || '';
      } catch (e) {
        console.error('Error loading settings', e);
      }
    },
    async saveSettings() {
      this.loading = true;
      try {
        await axios.post('site-settings', {
          school_name: this.schoolName,
          school_address: this.schoolAddress,
          school_address_detail: this.schoolAddressDetail,
          school_email: this.schoolEmail,
          school_phone: this.schoolPhone,
          maps_embed_url: this.mapsEmbedUrl,
        });
        this.$toast.success('Pengaturan berhasil disimpan');
      } catch (e) {
        this.$toast.error('Gagal menyimpan pengaturan');
      } finally {
        this.loading = false;
      }
    },
  },
  created() {
    this.loadSettings();
  },
};
</script>
