<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
      <q-card v-if="user.user.role_id == 2">
        <q-card-section>
          <q-table
            title="Modul Ajar dan LKPD"
            :rows="rows"
            :columns="columns"
            :filter="filter"
            :loading="loading"
            separator="cell"
            row-key="id"
            flat
            dense
            class="my-sticky-header-table"
          >
            <template v-slot:loading>
              <q-inner-loading showing>
                <q-spinner-ios size="30px" color="green-7" />
              </q-inner-loading>
            </template>

            <template v-slot:top-right>
              <q-input
                borderless
                dense
                debounce="300"
                v-model="filter"
                placeholder="Search"
              >
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
              </q-input>
              <q-btn
                label="Tambah Modul"
                icon="add"
                color="green-7"
                class="q-ml-md"
                @click="dialogUpload = true"
              />
            </template>

            <template v-slot:body-cell-file="props">
              <q-td :props="props">
                <q-icon
                  :name="getFileIcon(props.row.extension)"
                  size="md"
                  :color="getIconColor(props.row.extension)"
                />
                <span class="q-ml-sm">{{ props.row.file_name }}</span>
              </q-td>
            </template>

            <template v-slot:body-cell-download="props">
              <q-td :props="props">
                <a
                  :href="getDownloadUrl(props.row.file_path)"
                  target="_blank"
                  download
                >
                  <q-btn
                    label="Download"
                    color="primary"
                    dense
                    rounded
                    icon="download"
                    size="sm"
                  />
                </a>
              </q-td>
            </template>

            <template v-slot:body-cell-aksi="props">
              <q-td :props="props">
                <q-btn
                  round
                  flat
                  icon="delete"
                  color="red"
                  size="sm"
                  class="q-mx-xs"
                  @click="confirmDelete(props.row.id)"
                />
                <q-btn
                  round
                  flat
                  icon="archive"
                  color="grey"
                  size="sm"
                  class="q-mx-xs"
                  disabled
                />
              </q-td>
            </template>
          </q-table>
        </q-card-section>
      </q-card>

      <!-- Dialog Konfirmasi Hapus -->
      <q-dialog v-model="dialogConfirmDelete" persistent>
        <q-card>
          <q-card-section class="row items-center">
            <q-avatar icon="warning" color="orange" text-color="white" />
            <span class="q-ml-sm">Hapus Modul?</span>
          </q-card-section>
          <q-card-section>
            Modul ini akan dihapus permanen dari sistem.
          </q-card-section>
          <q-card-actions align="right">
            <q-btn flat label="Batal" color="primary" v-close-popup />
            <q-btn flat label="Hapus" color="red" @click="deleteModul" />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </div>

    <!-- Dialog Upload -->
    <q-dialog v-model="dialogUpload" persistent>
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">Upload Modul/LKPD</div>
        </q-card-section>

        <q-card-section>
          <q-input
            v-model="judul"
            label="Judul / Keterangan"
            outlined
            dense
            class="q-mb-md"
          />
          <q-file
            v-model="file"
            label="Pilih File (PDF, DOCX, XLSX, PPTX)"
            outlined
            dense
            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
          >
            <template v-slot:prepend>
              <q-icon name="attach_file" />
            </template>
          </q-file>
          <div v-if="file" class="text-caption text-grey q-mt-xs">
            {{ file.name }} ({{ formatBytes(file.size) }})
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Batal" color="primary" v-close-popup />
          <q-btn
            flat
            label="Upload"
            color="primary"
            @click="upload"
            :loading="uploading"
            :disable="!file || !judul"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { ref } from '@vue/reactivity';
import axios from 'axios';
import { mapGetters, mapState } from 'vuex';

export default {
  setup() {
    const columns = [
      { name: 'file', align: 'left', label: 'FILE', field: 'file_name', sortable: true },
      { name: 'ket', align: 'left', label: 'Keterangan Modul/LKPD', field: 'judul', sortable: true },
      { name: 'download', align: 'center', label: 'Download', sortable: false },
      { name: 'aksi', align: 'center', label: 'Aksi', sortable: false },
    ];
    return {
      columns,
      rows: ref([]),
      loading: ref(false),
      uploading: ref(false),
      filter: ref(null),
      dialogUpload: ref(false),
      dialogConfirmDelete: ref(false),
      judul: ref(''),
      file: ref(null),
      selectedId: ref(null),
    };
  },
  computed: {
    ...mapGetters({
      authenticated: 'auth/authenticated',
      user: 'auth/user',
    }),
    ...mapState('kontrol', ['url']),
  },
  methods: {
    async getData() {
      this.loading = true;
      try {
        // ✅ Gunakan baseURL dari axios (sudah di-set ke http://127.0.0.1:8000/api/)
        const res = await axios.get('modul/lkpd'); // tanpa this.url
        this.rows = res.data;
      } catch (error) {
        console.error('Error load modul:', error);
        this.$toast.error('Gagal memuat data modul');
      } finally {
        this.loading = false;
      }
    },

    async upload() {
      if (!this.file || !this.judul) {
        this.$toast.error('Harap lengkapi judul dan file');
        return;
      }

      const formData = new FormData();
      formData.append('judul', this.judul);
      formData.append('file', this.file);

      this.uploading = true;
      try {
        // ✅ Gunakan endpoint relatif (baseURL sudah di-set)
        await axios.post('modul/lkpd', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });

        this.$toast.success('Modul berhasil diupload');
        this.dialogUpload = false;
        this.resetForm();
        this.getData();
      } catch (error) {
        let msg = 'Upload gagal';
        if (error.response?.data?.message) {
          msg = error.response.data.message;
        }
        console.error('Error upload:', error);
        this.$toast.error(msg);
      } finally {
        this.uploading = false;
      }
    },

    confirmDelete(id) {
      this.selectedId = id;
      this.dialogConfirmDelete = true;
    },

    async deleteModul() {
      this.dialogConfirmDelete = false;
      this.loading = true;
      try {
        await axios.delete(`modul/lkpd/${this.selectedId}`);
        this.$toast.success('Modul berhasil dihapus');
        this.getData();
      } catch (error) {
        console.error('Error hapus:', error);
        this.$toast.error('Gagal menghapus modul');
      } finally {
        this.loading = false;
      }
    },

    resetForm() {
      this.judul = '';
      this.file = null;
    },

    getFileIcon(ext) {
      const e = ext?.toLowerCase() || '';
      if (e === 'pdf') return 'picture_as_pdf';
      if (['doc', 'docx'].includes(e)) return 'description';
      if (['xls', 'xlsx'].includes(e)) return 'grid_on';
      if (['ppt', 'pptx'].includes(e)) return 'slideshow';
      return 'insert_drive_file';
    },

    getIconColor(ext) {
      const e = ext?.toLowerCase() || '';
      if (e === 'pdf') return 'red';
      if (['doc', 'docx'].includes(e)) return 'blue';
      if (['xls', 'xlsx'].includes(e)) return 'green';
      if (['ppt', 'pptx'].includes(e)) return 'orange';
      return 'grey';
    },

    getDownloadUrl(filePath) {
      // ✅ Sesuaikan dengan baseURL Anda
      return `http://127.0.0.1:8000/storage/${filePath}`;
    },

    formatBytes(bytes, decimals = 2) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const dm = decimals < 0 ? 0 : decimals;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    },
  },
  created() {
    this.getData();
  },
};
</script>

<style scoped>
.my-sticky-header-table :deep(.q-table__top) {
  padding-top: 0;
}
</style>