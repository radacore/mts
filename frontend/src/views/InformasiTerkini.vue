<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
      <q-card v-if="user.user.role_id==1 || user.user.role_id==2">
        <q-card-section>
          <q-table
            title="Informasi Terkini"
            :rows="rows"
            :columns="columns"
            :filter="filter"
            :loading="loading"
            row-key="id"
            flat
            dense
            separator="cell"
          >
            <template v-slot:top-right>
              <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
              </q-input>
              <q-btn label="Input" class="q-ml-md" icon="o_add" color="green-7" @click="dialog=true" />
            </template>

            <template v-slot:body-cell-isi="props">
              <q-td :props="props" style="white-space: pre-wrap; max-width: 320px;">
                {{ props.row.isi }}
              </q-td>
            </template>

            <template v-slot:body-cell-aktif="props">
              <q-td :props="props">
                <q-toggle
                  :model-value="props.row.status === 'aktif'"
                  color="green-7"
                  checked-icon="o_check"
                  unchecked-icon="o_close"
                  @update:model-value="activateInformasi(props.row, $event)"
                />
              </q-td>
            </template>



            <template v-slot:body-cell-aksi="props">
              <q-td :props="props">
                <q-btn flat round size="sm" color="green-7" icon="o_edit" @click="edit(props.row.id)" />
                <q-btn flat round size="sm" color="red" icon="o_delete" @click="confirmDelete(props.row.id)" />
              </q-td>
            </template>
          </q-table>
        </q-card-section>
      </q-card>

      <q-card v-else flat>
        <q-banner dense class="bg-red text-white">
          <template v-slot:avatar>
            <q-icon name="fas fa-user-lock" color="white" />
          </template>
          MAAF, Anda Tidak Berhak Mengakses Halaman ini
        </q-banner>
      </q-card>
    </div>

    <q-dialog v-model="dialog" persistent>
      <q-card style="width: 760px; max-width: 95vw;">
        <q-toolbar>
          <q-toolbar-title class="text-green-7">Informasi Terkini</q-toolbar-title>
          <q-btn flat round dense icon="close" v-close-popup @click="resetForm" />
        </q-toolbar>
        <q-separator />
        <q-card-section>
          <q-input outlined dense v-model="form.judul" label="Judul*" class="q-mb-sm" />
          <q-input outlined dense type="textarea" v-model="form.isi" label="Isi Informasi*" class="q-mb-sm" autogrow />
        </q-card-section>
        <q-separator />
        <q-card-actions align="right">
          <q-btn label="Batal" flat color="red" @click="resetForm" />
          <q-btn label="Simpan" color="green-7" @click="save" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="confirm" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <q-icon color="red" name="fas fa-exclamation-circle" class="q-mr-sm" />
          <span>Hapus informasi ini?</span>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Batal" color="primary" @click="confirm=false" />
          <q-btn unelevated label="Hapus" color="red" @click="hapus" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { ref } from '@vue/reactivity';
import axios from 'axios';
import { mapGetters } from 'vuex';

export default {
  setup() {
    const columns = [
      { name: 'judul', label: 'Judul', align: 'left', field: 'judul', sortable: true },
      { name: 'isi', label: 'Isi', align: 'left', field: 'isi' },
      { name: 'aktif', label: 'Aktif', align: 'center', field: 'status', sortable: false },
      { name: 'aksi', label: 'Aksi', align: 'left', field: 'aksi' },
    ]

    return {
      columns,
      rows: ref([]),
      loading: ref(false),
      filter: ref(''),
      dialog: ref(false),
      confirm: ref(false),
    }
  },
  data: () => ({
    hapusId: null,
    form: {
      id: '',
      judul: '',
      isi: '',
    },
  }),
  computed: {
    ...mapGetters({
      authenticated: 'auth/authenticated',
      user: 'auth/user',
    }),
  },
  methods: {
    resetForm() {
      this.dialog = false
      this.confirm = false
      this.hapusId = null
      this.form = {
        id: '',
        judul: '',
        isi: '',
      }
    },
    async getData() {
      this.loading = true
      await axios.get('informasi-terkini').then((response) => {
        this.rows = response.data
      }).finally(() => {
        this.loading = false
      })
    },
    async save() {
      const payload = {
        id: this.form.id,
        judul: this.form.judul,
        isi: this.form.isi,
        tipe: 'info',
        status: 'aktif',
      }

      await axios.post('informasi-terkini', payload).then(() => {
        this.$toast.success('Informasi berhasil disimpan. Informasi lain otomatis dinonaktifkan.')
        this.resetForm()
        this.getData()
      }).catch(() => {
        this.$toast.error('Gagal menyimpan informasi')
      })
    },
    async activateInformasi(row, isOn) {
      if (!isOn) {
        this.$toast.info('Minimal satu informasi harus aktif.')
        this.getData()
        return
      }

      const payload = {
        id: row.id,
        judul: row.judul,
        isi: row.isi,
        tipe: 'info',
        status: 'aktif',
      }

      await axios.post('informasi-terkini', payload).then(() => {
        this.$toast.success('Informasi aktif berhasil diperbarui.')
        this.getData()
      }).catch(() => {
        this.$toast.error('Gagal mengubah informasi aktif')
        this.getData()
      })
    },
    async edit(id) {
      await axios.get(`informasi-terkini/${id}`).then((response) => {
        const d = response.data
        this.form.id = d.id
        this.form.judul = d.judul
        this.form.isi = d.isi
        this.dialog = true
      })
    },
    confirmDelete(id) {
      this.hapusId = id
      this.confirm = true
    },
    async hapus() {
      await axios.delete(`informasi-terkini/${this.hapusId}`).then(() => {
        this.$toast.success('Informasi berhasil dihapus')
        this.resetForm()
        this.getData()
      }).catch(() => {
        this.$toast.error('Gagal menghapus informasi')
      })
    },
  },
  created() {
    this.getData()
  },
}
</script>
