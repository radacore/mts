<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
      <q-card v-if="user.user.role_id==1 || user.user.role_id==3 || user.user.role_id==2">
        <q-card-section>
          <q-table
          title="Peminjaman Lain"
          :rows="filteredRows"
          :columns="displayedColumns"
          :filter="filter"
          :loading="loading"
          :pagination="pagination"
          separator="cell"
          row-key="name"
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
        <q-select
            v-model="statusFilter"
            :options="['Semua', 'diajukan', 'disetujui', 'ditolak']"
            label="Filter Status"
            dense
            flat
            outlined
            class="q-mx-md"
            style="min-width: 150px"
          />
        <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
        <q-btn v-if="user.user.role_id==3" label="Input" class="q-ml-md" icon="o_add" color="green-7" @click="bukaTambah" />
      </template>
      <template v-slot:header-cell-copy="props">
        <q-th :props="props">
          <q-icon name="o_content_copy" size="1.5em" />
        </q-th>
      </template>
      <template v-slot:body-cell-copy="props">
        <q-td :props="props">
          <kopi-lain :lain_id="props.row.id"/>
        </q-td>
      </template>
      <template v-slot:body-cell-tgl="props">
        <q-td :props="props">
          {{hari(props.row.tgl)}}. {{dateTime(props.row.tgl)}}
        </q-td>
      </template>
      <template v-slot:body-cell-status="props">
        <q-td :props="props">
          <div class="row justify-around">
            <q-chip v-if="props.row.status=='disetujui'" color="green-7" text-color="white" icon="approval_delegation" dense>
                {{props.row.status}}
            </q-chip>
            <q-chip v-else-if="props.row.status=='ditolak'" color="red-7" text-color="white" icon="o_water_drop" dense>
                {{props.row.status}}
            </q-chip>
            <q-chip v-else color="yellow-7" text-color="white" icon="pending" dense>
                {{props.row.status}}
            </q-chip>
          </div>
          <div
            v-if="props.row.status==='ditolak'"
            class="text-caption text-negative q-mt-xs"
          >
            Alasan: {{ alasanPenolakanLabel(props.row.alasan_penolakan) }}
          </div>
        </q-td>
      </template>
      <template v-slot:body-cell-proses="props">
        <q-td :props="props">
          <q-btn-dropdown
            v-if="user.user.role_id==1 || user.user.role_id==2"
            dense
            unelevated
            color="green-7"
            :label="props.row.status === 'diajukan' ? 'Proses' : 'Final'"
            :disable="props.row.status !== 'diajukan'"
            no-caps
          >
            <q-list dense>
              <q-item clickable v-close-popup @click="ubahStatus(props.row.id,'disetujui')">
                <q-item-section>disetujui</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="bukaDialogPenolakan(props.row.id)">
                <q-item-section>ditolak</q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </q-td>
      </template>
      <template v-slot:body-cell-aksi="props">
        <q-td :props="props" v-if="user.user.role_id!=2">
          <q-btn @click="edit(props.row.id)" round icon="far fa-edit" color="green-7" size="xs" flat/>
          <q-btn @click="konfirmasi(props.row.id)" round icon="fas fa-trash-alt" color="red" size="xs" flat=""/>
        </q-td>
      </template>
       </q-table>
        </q-card-section>
      </q-card>
    </div>
     <!-- DIALOG -->
     <q-dialog v-model="dialogInsert">
      <q-card style="width: 500px; max-width: 80vw;">
        <q-toolbar>
            <q-toolbar-title class="text-green-7"><span class="text-weight-medium">Peminjaman</span>Alat Laboratorium</q-toolbar-title>
            <q-btn flat round dense icon="close" v-close-popup />
          </q-toolbar>
          <q-separator/>
        <q-card-section style="max-height: 60vh" class="scroll">
          <q-form>
              <q-input outlined dense v-model="form.tgl" label="Tanggal Pemakaian *" class="q-my-sm" mask="date" style="width:250px;">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                        <q-date v-model="form.tgl" color="green-7">
                          <div class="row items-center justify-end">
                            <q-btn v-close-popup label="Close" color="green-7" flat />
                          </div>
                        </q-date>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
              </q-input>
              <q-input outlined dense v-model="form.mulai" label="Jam Mulai *" mask="time" class="q-my-sm" style="width:200px;" >
                  <template v-slot:append>
                    <q-icon name="access_time" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                        <q-time v-model="form.mulai" color="green-7" format24h>
                          <div class="row items-center justify-end">
                            <q-btn v-close-popup label="Close" color="green-7" flat />
                          </div>
                        </q-time>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
              </q-input>
              <q-input outlined dense v-model="form.selesai" label="Jam Selesai *" mask="time" class="q-my-sm" style="width:200px;" >
                  <template v-slot:append>
                    <q-icon name="access_time" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                        <q-time v-model="form.selesai" color="green-7" format24h>
                          <div class="row items-center justify-end">
                            <q-btn v-close-popup label="Close" color="green-7" flat />
                          </div>
                        </q-time>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
              </q-input>
              <q-input outlined v-model="form.kegiatan" label="Keperluan *" class="q-my-sm" color="green-3" autogrow dense />
          </q-form>
        </q-card-section>
        <q-separator/>
        <q-card-actions align="right" class="bg-white">
          <q-btn label="simpan" color="green-10" @click="simpan"/>
          <q-btn label="Batal" color="red-10" @click="batal"/>
        </q-card-actions>
      </q-card>
    </q-dialog>
    <!-- KONFIRMASI -->
    <q-dialog v-model="confirm" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <q-item>
            <q-item-section side>
              <q-icon color="red" name="fas fa-exclamation-circle" />
            </q-item-section>
            <q-item-section>
              <q-item-label class="text-subtitle2">Apakah Anda Ingin Menghapus Data ini?</q-item-label>
              <q-item-label caption lines="2">Data Inventaris</q-item-label>
            </q-item-section>
          </q-item>
        </q-card-section>
  
        <q-card-actions align="right">
          <q-btn label="No" color="primary" @click="batal" dense />
          <q-btn label="Yes" color="red" @click="hapus" dense />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogAlasanPenolakan" persistent>
      <q-card style="width: 460px; max-width: 90vw;">
        <q-card-section>
          <div class="text-subtitle1 text-red-8">Alasan Penolakan Peminjaman Kegiatan Lain</div>
          <q-input
            v-model="alasanPenolakanInput"
            type="textarea"
            autogrow
            outlined
            dense
            class="q-mt-md"
            maxlength="2000"
            counter
            label="Alasan Penolakan *"
            placeholder="Tuliskan alasan kenapa pengajuan ditolak"
          />
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Batal" color="grey-7" @click="tutupDialogPenolakan" />
          <q-btn label="Simpan Penolakan" color="red-7" unelevated @click="kirimPenolakan" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { ref } from '@vue/reactivity';
import axios from 'axios';
import { mapGetters, mapState } from 'vuex';
import KopiLain from '@/components/KopiLain.vue';
import moment from "moment";
import "moment/locale/id";
moment.locale("id");
export default {
components:{
  KopiLain,
},
setup(){
  const columns=[
    { name: 'copy', align: 'left', label: 'kopi', sortable: true },
    { name: 'tgl', align: 'left', label: 'Tanggal Pinjam', field: 'tgl', sortable: true },
    { name: 'peminjam', align: 'left', label: 'peminjam', field: row => row.user.name, sortable: true },
    { name: 'mulai', align: 'left', label: 'Jam Mulai', field:'mulai', format: val => val ? String(val).slice(0, 5) : '', sortable: true },
    { name: 'selesai', align: 'left', label: 'Jam Selesai', field:'selesai', format: val => val ? String(val).slice(0, 5) : '', sortable: true },
    { name: 'kegiatan', align: 'left', label: 'Kegiatan', field:'kegiatan', sortable: true },
    { name: 'status', align: 'left', label: 'Status', field: 'status', sortable: true },
    { name: 'proses', align: 'left', label: 'Proses', sortable: false },
    { name: 'aksi', align: 'left', label: 'Aksi', sortable: true },
  ]
  return{
    pagination: {
      sortBy: 'tgl',
      descending: true,
      rowsPerPage: 15
    },
    columns,
    dialogInsert:ref(false),
    confirm:ref(false),
    loading:ref(false),
    filter:ref(null),
    rows:ref([]),
    statusFilter:ref('Semua'),
    dialogAlasanPenolakan:ref(false),
    prosesRowId:ref(null),
    alasanPenolakanInput:ref(''),
  }
},
data:()=>({
form:{
  id:"",
  mulai:"",
  selesai:"",
  kegiatan:"",
  tgl:"",
}
}),
computed:{
  ...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
    ...mapState("kontrol", ["triger"]),
    displayedColumns() {
      if (this.user.user.role_id === 2) {
        return this.columns.filter(col => col.name !== 'aksi' && col.name !== 'copy')
      }
      if (this.user.user.role_id === 3) {
        return this.columns.filter(col => col.name !== 'proses')
      }
      return this.columns
    },
    filteredRows() {
      if (this.statusFilter === 'Semua') {
        return this.rows;
      }
      return this.rows.filter(row => row.status === this.statusFilter);
    }
},
watch:{
  triger(){
    this.getData()
  }
},
methods:{
  dateTime(value) {
      return moment(value).format('LL');
    },
  hari(value) {
      return moment(value).format('dddd');
    },
  alasanPenolakanLabel(reason){
      if (!reason) return 'Tidak ada alasan (data lama)'
      return reason
    },
  
  tanggalHariIni(){
    return moment().format('YYYY/MM/DD')
  },
  bukaTambah(){
    this.batal()
    this.form.tgl=this.tanggalHariIni()
    this.dialogInsert=true
  },
  batal(){
    this.dialogInsert=false
    this.confirm=false
    this.form.id=""
    this.form.mulai=""
    this.form.selesai=""
    this.form.kegiatan=""
    this.form.tgl=""
  },
  konfirmasi($id){
    this.form.id=$id
    this.confirm=true
  },
  async getData(){
    this.loading=true
    await axios.get("pinjamLain").then((response)=>{
      this.rows=response.data
    }).finally(()=>{
      this.loading=false
    })
  },
  async simpan(){
    await axios.post("pinjamLain",this.form).then((response)=>{
      this.batal()
      this.$toast.success(`berhasil tersimpan`)
      this.getData()
      return response
    }).catch((error)=>{
      const msg = error.response?.data?.message || `Gagal, Mohon Cek kembali`
      this.$toast.error(msg,{
            position: "top",
            duration:3000,
            dismissible:true
         });
            return error
    })
  },
  async edit($id){
    await axios.get("pinjamLain/"+$id).then((response)=>{
      this.form.id=response.data.id
      this.form.mulai=response.data.mulai
      this.form.selesai=response.data.selesai
      this.form.tgl=response.data.tgl
      this.form.kegiatan=response.data.kegiatan
      this.dialogInsert=true
    })
  },
  async hapus(){
    await axios.delete("pinjamLain/"+this.form.id).then((response)=>{
      this.batal()
      this.$toast.success(`berhasil terhapus`)
      this.getData()
      return response
    })
  },
  bukaDialogPenolakan(id){
    this.prosesRowId = id
    this.alasanPenolakanInput = ''
    this.dialogAlasanPenolakan = true
  },
  tutupDialogPenolakan(){
    this.dialogAlasanPenolakan = false
    this.prosesRowId = null
    this.alasanPenolakanInput = ''
  },
  async kirimPenolakan(){
    const alasan = (this.alasanPenolakanInput || '').toString().trim()
    if (!alasan) {
      this.$toast.error('Alasan penolakan wajib diisi')
      return
    }

    await this.ubahStatus(this.prosesRowId, 'ditolak', alasan)
    this.tutupDialogPenolakan()
  },
  async ubahStatus(id, status, alasanPenolakan = null){
    const payload = status === 'ditolak'
      ? { alasan_penolakan: (alasanPenolakan || '').toString().trim() }
      : {}

    await axios.put(`peminjaman/lain/${id}/${status}`, payload).then(()=>{
      this.$toast.success('Status berhasil diperbarui')
      this.getData()
    }).catch((error)=>{
      const msg = error.response?.data?.message || 'Gagal memperbarui status'
      this.$toast.error(msg)
    })
  },
  startAutoRefresh(){
    if (this.user.user.role_id === 3) {
      this._refreshTimer = setInterval(() => {
        this.getData()
      }, 15000)
    }
  }
  
},
created(){
this.getData()
this.startAutoRefresh()
},
beforeUnmount() {
  if (this._refreshTimer) {
    clearInterval(this._refreshTimer)
  }
}
}
</script>
