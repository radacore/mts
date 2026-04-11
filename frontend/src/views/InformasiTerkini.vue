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

            <template v-slot:body-cell-tipe="props">
              <q-td :props="props">
                <q-badge :color="badgeTipe(props.row.tipe).color" text-color="white">
                  {{ badgeTipe(props.row.tipe).label }}
                </q-badge>
              </q-td>
            </template>

            <template v-slot:body-cell-status="props">
              <q-td :props="props">
                <q-badge :color="props.row.status === 'aktif' ? 'green-7' : 'grey-7'" text-color="white">
                  {{ props.row.status }}
                </q-badge>
              </q-td>
            </template>

            <template v-slot:body-cell-mulai_at="props">
              <q-td :props="props">
                {{ formatDateTimeView(props.row.mulai_at) }}
              </q-td>
            </template>

            <template v-slot:body-cell-selesai_at="props">
              <q-td :props="props">
                {{ formatDateTimeView(props.row.selesai_at) }}
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
          <div class="row q-col-gutter-sm">
            <div class="col-12 col-md-3">
              <q-select
                outlined
                dense
                v-model="form.tipe"
                :options="tipeOptions"
                emit-value
                map-options
                option-value="value"
                option-label="label"
                label="Tipe*"
              />
            </div>
            <div class="col-12 col-md-4">
              <q-select
                outlined
                dense
                v-model="form.status"
                :options="statusOptions"
                emit-value
                map-options
                option-value="value"
                option-label="label"
                label="Status*"
              />
            </div>
          </div>

          <q-card flat bordered class="q-pa-sm q-mt-sm bg-green-1">
            <div class="text-subtitle2 text-green-9 q-mb-sm">Periode Berlaku (Opsional)</div>
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.mulai_tanggal" label="Tanggal Mulai" readonly>
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                        <q-date v-model="form.mulai_tanggal" mask="YYYY-MM-DD" color="green-7">
                          <div class="row items-center justify-end">
                            <q-btn v-close-popup label="Tutup" color="green-7" flat />
                          </div>
                        </q-date>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.mulai_jam" label="Jam Mulai" readonly>
                  <template v-slot:append>
                    <q-icon name="access_time" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                        <q-time v-model="form.mulai_jam" mask="HH:mm" format24h color="green-7">
                          <div class="row items-center justify-end">
                            <q-btn v-close-popup label="Tutup" color="green-7" flat />
                          </div>
                        </q-time>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.selesai_tanggal" label="Tanggal Selesai" readonly>
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                        <q-date v-model="form.selesai_tanggal" mask="YYYY-MM-DD" color="green-7">
                          <div class="row items-center justify-end">
                            <q-btn v-close-popup label="Tutup" color="green-7" flat />
                          </div>
                        </q-date>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
              <div class="col-12 col-md-3">
                <q-input outlined dense v-model="form.selesai_jam" label="Jam Selesai" readonly>
                  <template v-slot:append>
                    <q-icon name="access_time" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                        <q-time v-model="form.selesai_jam" mask="HH:mm" format24h color="green-7">
                          <div class="row items-center justify-end">
                            <q-btn v-close-popup label="Tutup" color="green-7" flat />
                          </div>
                        </q-time>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
            </div>
          </q-card>
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
      { name: 'tipe', label: 'Tipe', align: 'left', field: 'tipe', sortable: true },
      { name: 'status', label: 'Status', align: 'left', field: 'status', sortable: true },
      { name: 'mulai_at', label: 'Mulai', align: 'left', field: 'mulai_at', sortable: true },
      { name: 'selesai_at', label: 'Selesai', align: 'left', field: 'selesai_at', sortable: true },
      { name: 'isi', label: 'Isi', align: 'left', field: 'isi' },
      { name: 'aksi', label: 'Aksi', align: 'left', field: 'aksi' },
    ]

    return {
      columns,
      rows: ref([]),
      loading: ref(false),
      filter: ref(''),
      dialog: ref(false),
      confirm: ref(false),
      tipeOptions: [
        { label: 'Info', value: 'info' },
        { label: 'Peringatan', value: 'peringatan' },
        { label: 'Penutupan Lab', value: 'penutupan_lab' },
      ],
      statusOptions: [
        { label: 'Aktif', value: 'aktif' },
        { label: 'Nonaktif', value: 'nonaktif' },
      ],
    }
  },
  data: () => ({
    hapusId: null,
    form: {
      id: '',
      judul: '',
      isi: '',
      tipe: 'info',
      status: 'aktif',
      mulai_tanggal: '',
      mulai_jam: '',
      selesai_tanggal: '',
      selesai_jam: '',
    },
  }),
  computed: {
    ...mapGetters({
      authenticated: 'auth/authenticated',
      user: 'auth/user',
    }),
  },
  methods: {
    splitDateTime(value) {
      if (!value) return { tanggal: '', jam: '' }
      const dt = String(value).replace('T', ' ')
      const [tanggal = '', timeFull = ''] = dt.split(' ')
      const jam = timeFull ? timeFull.slice(0, 5) : ''
      return { tanggal, jam }
    },
    composeDateTime(tanggal, jam) {
      if (!tanggal) return null
      return `${tanggal} ${jam || '00:00'}:00`
    },
    formatDateTimeView(value) {
      if (!value) return '-'
      const date = new Date(String(value).replace(' ', 'T'))
      if (isNaN(date.getTime())) return '-'
      const d = String(date.getDate()).padStart(2, '0')
      const m = String(date.getMonth() + 1).padStart(2, '0')
      const y = date.getFullYear()
      const h = String(date.getHours()).padStart(2, '0')
      const i = String(date.getMinutes()).padStart(2, '0')
      return `${d}-${m}-${y} ${h}:${i}`
    },
    badgeTipe(tipe) {
      if (tipe === 'penutupan_lab') return { label: 'Penutupan Lab', color: 'red-8' }
      if (tipe === 'peringatan') return { label: 'Peringatan', color: 'orange-8' }
      return { label: 'Info', color: 'blue-7' }
    },
    resetForm() {
      this.dialog = false
      this.confirm = false
      this.hapusId = null
      this.form = {
        id: '',
        judul: '',
        isi: '',
        tipe: 'info',
        status: 'aktif',
        mulai_tanggal: '',
        mulai_jam: '',
        selesai_tanggal: '',
        selesai_jam: '',
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
      const mulaiAt = this.composeDateTime(this.form.mulai_tanggal, this.form.mulai_jam)
      const selesaiAt = this.composeDateTime(this.form.selesai_tanggal, this.form.selesai_jam)

      if (mulaiAt && selesaiAt && new Date(mulaiAt) > new Date(selesaiAt)) {
        this.$toast.error('Waktu selesai harus lebih besar atau sama dengan waktu mulai')
        return
      }

      const payload = {
        id: this.form.id,
        judul: this.form.judul,
        isi: this.form.isi,
        tipe: this.form.tipe,
        status: this.form.status,
        mulai_at: mulaiAt,
        selesai_at: selesaiAt,
      }

      await axios.post('informasi-terkini', payload).then(() => {
        this.$toast.success('Informasi berhasil disimpan')
        this.resetForm()
        this.getData()
      }).catch(() => {
        this.$toast.error('Gagal menyimpan informasi')
      })
    },
    async edit(id) {
      await axios.get(`informasi-terkini/${id}`).then((response) => {
        const d = response.data
        this.form.id = d.id
        this.form.judul = d.judul
        this.form.isi = d.isi
        this.form.tipe = d.tipe
        this.form.status = d.status
        const mulai = this.splitDateTime(d.mulai_at)
        const selesai = this.splitDateTime(d.selesai_at)
        this.form.mulai_tanggal = mulai.tanggal
        this.form.mulai_jam = mulai.jam
        this.form.selesai_tanggal = selesai.tanggal
        this.form.selesai_jam = selesai.jam
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
