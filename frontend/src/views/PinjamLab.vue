<template>
    <q-page class="q-pa-sm">
        <div v-if="authenticated">
            <q-card v-if="user.user.role_id==1 || user.user.role_id==3 || user.user.role_id==2">
              <q-card-section>
                 <q-table
                    title="Peminjaman Lab"
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
                  <q-input
                    v-model="filter"
                    label="Search"
                    debounce="300"
                    clearable
                    dense
                    outlined
                    style="min-width: 220px"
                  >
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
                <template v-slot:header-cell-alba="props">
                  <q-th :props="props">
                    <q-icon name="thermostat_auto" size="1.5em" />
                  </q-th>
                </template>
                <template v-slot:header-cell-print="props">
                  <q-th :props="props">
                    <q-icon name="mark_email_read" size="1.5em" />
                  </q-th>
                </template>
                <template v-slot:body-cell-copy="props">
                  <q-td :props="props">
                  <kopi-lab :pinjam_id="props.row.id"/>
                  </q-td>
                </template>
                <template v-slot:body-cell-hari="props">
                  <q-td :props="props">
                   {{hari(props.row.tgl)}}, {{dateTime(props.row.tgl)}}
                  </q-td>
                </template>
                <template v-slot:body-cell-kelas="props">
                  <q-td :props="props">
                    {{props.row.kelas.kelas}}
                  </q-td>
                </template>
                <template v-slot:body-cell-topik="props">
                  <q-td :props="props">
                    {{props.row.katalog.topik}}
                  </q-td>
                </template>
                 <template v-slot:body-cell-lkpd="props">
                   <q-td :props="props">
                    <div class="column q-gutter-xs">
                      <div v-if="props.row.lkpd">
                       <a :href="url+props.row.lkpd" target="_blank">
                         <img src="../assets/pdf.png" style="max-width:20px;"/>
                       </a>
                      </div>
                      <q-chip
                        v-for="modul in props.row.modul_lkpd"
                        :key="modul.id"
                        dense
                        clickable
                        color="green-1"
                        text-color="green-10"
                        icon="description"
                        @click="openModul(modul)"
                      >
                        {{ modul.judul }}
                      </q-chip>
                    </div>
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
                <template v-slot:body-cell-alba="props">
                  <q-td :props="props">
                    <div class="row justify-center">
                    <list-pinjam :plid="props.row.id" :katalog_id="props.row.katalog_id" :role_id="user.user.role_id"/>
                </div>
                  </q-td>
                </template>
                <template v-slot:body-cell-print="props">
                  <q-td :props="props">
                   <div v-if="props.row.status =='disetujui'">
                    <bukti-lab :peminjam="props.row.peminjam" :topiks="props.row.katalog.topik"
                    :kelas="props.row.kelas.kelas"
                    :jam_start="props.row.jam"
                    :jam_end="props.row.jam_selesai"
                    :tanggal="props.row.tgl"
                    :pid="props.row.id"
                    :kid="props.row.katalog_id"
                    :dt="props.row.updated_at"
                    :nip="props.row.user.bioguru ? props.row.user.bioguru.nip : '-'"
                    :hp="props.row.user.bioguru ? props.row.user.bioguru.hp : '-'"
                    />
                   </div>
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
             <q-card v-else flat>
              <q-banner dense class="bg-red text-white">
                <template v-slot:avatar>
                  <q-icon name="fas fa-user-lock" color="white" />
                </template>
                MAAF, Anda Tidak Berhak Mengakses Halaman ini
                <template v-slot:action>
                  <q-btn flat color="white" label="Back To Dashboard" to="/" />
                </template>
              </q-banner>
             </q-card>
              <!-- DIALOG -->
              <q-dialog v-model="dialogInsert">
                <q-card style="width: 500px; max-width: 80vw;">
                  <q-toolbar>
                      <q-toolbar-title class="text-green-7"><span class="text-weight-medium">Peminjaman</span>Laboratorium</q-toolbar-title>
                      <q-btn flat round dense icon="close" v-close-popup />
                    </q-toolbar>
                    <q-separator/>
                  <q-card-section style="max-height: 60vh" class="scroll">
                    <q-form>
                       <q-input outlined dense v-model="form.tgl" label="Tanggal *" class="q-my-sm" mask="date">
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
                        <q-input outlined dense v-model="form.jam" label="Jam Mulai *" mask="time" class="q-my-sm" style="width:200px;" >
                            <template v-slot:append>
                              <q-icon name="access_time" class="cursor-pointer">
                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                  <q-time v-model="form.jam" color="green-7" format24h>
                                    <div class="row items-center justify-end">
                                      <q-btn v-close-popup label="Close" color="green-7" flat />
                                    </div>
                                  </q-time>
                                </q-popup-proxy>
                              </q-icon>
                            </template>
                          </q-input>
                        <q-input outlined dense v-model="form.jam_selesai" label="Jam Selesai *" mask="time" class="q-my-sm" style="width:200px;" >
                            <template v-slot:append>
                              <q-icon name="access_time" class="cursor-pointer">
                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                  <q-time v-model="form.jam_selesai" color="green-7" format24h>
                                    <div class="row items-center justify-end">
                                      <q-btn v-close-popup label="Close" color="green-7" flat />
                                    </div>
                                  </q-time>
                                </q-popup-proxy>
                              </q-icon>
                            </template>
                          </q-input>
                           <q-select outlined v-model="form.kelas_id" :options="kelasOptions" emit-value map-options option-value="id" option-label="kelas" label="Kelas *" class="q-my-sm" dense/>
                           <q-banner v-if="isKelasPinjaman" dense rounded class="bg-orange-1 text-orange-10 q-my-sm">
                             Anda tidak mengajar di kelas ini. Pastikan peminjaman ini untuk kebutuhan siswa.
                           </q-banner>
                           <q-select
                             outlined
                             v-model="form.topik_id"
                             :options="katalogOptions"
                             emit-value
                             map-options
                             option-value="id"
                             option-label="topik"
                             label="Topik *"
                             dense
                             use-input
                             input-debounce="0"
                             @filter="filterKatalogOptions"
                           />
                           <div class="q-my-sm">
                             <div class="row items-center justify-between q-mb-xs">
                               <div class="text-subtitle2 text-grey-8">Pilih LKPD / Modul Praktikum</div>
                               <q-btn label="Pilih Modul" icon="o_menu_book" color="green-7" dense outline no-caps @click="bukaDialogPilihModul" />
                             </div>
                             <div v-if="selectedModulLkpd.length" class="row q-gutter-xs">
                               <q-chip
                                 v-for="modul in selectedModulLkpd"
                                 :key="modul.id"
                                 removable
                                 clickable
                                 dense
                                 color="green-1"
                                 text-color="green-10"
                                 icon="description"
                                 @click="openModul(modul)"
                                 @remove="hapusModulTerpilih(modul.id)"
                               >
                                 {{ modul.judul }}
                               </q-chip>
                             </div>
                             <div v-else class="text-caption text-grey-7">Belum ada modul dipilih</div>
                           </div>
                       <q-input outlined v-model="form.pekan" label="Pekan Ke- *" class="q-my-sm" color="green-3" dense />
                     </q-form>
                  </q-card-section>
                  <q-separator/>
                  <q-card-actions align="right" class="bg-white">
                    <q-btn label="simpan" color="green-10" @click="simpan"/>
                    <q-btn label="Batal" color="red-10" @click="batal"/>
                  </q-card-actions>
                </q-card>
              </q-dialog>
              <q-dialog v-model="dialogPilihModul" persistent>
                <q-card style="min-width: 500px; max-width: 90vw; width: 640px;">
                  <q-card-section>
                    <div class="text-h6 text-green-8">Pilih Modul Praktikum</div>
                    <q-input v-model="modulFilter" dense placeholder="Cari judul modul, nama file, atau uploader..." clearable debounce="300" class="q-mt-sm">
                      <template v-slot:prepend>
                        <q-icon name="search" />
                      </template>
                    </q-input>
                  </q-card-section>
                  <q-card-section class="q-pt-none" style="max-height: 420px; overflow-y: auto;">
                    <q-list dense separator>
                      <q-item
                        v-for="modul in filteredModulLkpdOptions"
                        :key="modul.id"
                        clickable
                        v-ripple
                        :active="isModulTerpilih(modul.id)"
                        active-class="bg-green-1 text-green-10"
                        @click="toggleModulTerpilih(modul)"
                      >
                        <q-item-section avatar>
                          <q-checkbox :model-value="isModulTerpilih(modul.id)" color="green-7" @update:model-value="toggleModulTerpilih(modul)" @click.stop />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>{{ modul.judul }}</q-item-label>
                          <q-item-label caption>{{ modul.file_name }} · {{ modul.uploader_name || 'Laboran' }}</q-item-label>
                          <q-item-label caption>
                            <q-badge :color="modulSourceColor(modul)" text-color="white">{{ modulSourceLabel(modul) }}</q-badge>
                          </q-item-label>
                        </q-item-section>
                        <q-item-section side>
                          <q-btn flat dense round icon="open_in_new" @click.stop="openModul(modul)" />
                        </q-item-section>
                      </q-item>
                      <q-item v-if="filteredModulLkpdOptions.length === 0">
                        <q-item-section>
                          <q-item-label class="text-grey">Tidak ada modul ditemukan</q-item-label>
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-card-section>
                  <q-card-actions align="right">
                    <q-btn flat label="Batal" color="grey-8" v-close-popup />
                    <q-btn label="Gunakan Modul Terpilih" color="green-7" unelevated @click="dialogPilihModul=false" />
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
                    <div class="text-subtitle1 text-red-8">Alasan Penolakan Peminjaman Lab</div>
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
              </div>
      </q-page>
</template>

<script>
import { ref } from '@vue/reactivity';
import axios from 'axios';
import { mapGetters, mapState } from 'vuex';
import ListPinjam from '@/components/ListPinjam.vue';
import KopiLab from '@/components/KopiLab.vue';
import BuktiLab from '@/components/BuktiLab.vue';
import moment from "moment";
import "moment/locale/id";
moment.locale("id");
export default {
components:{
ListPinjam,
KopiLab,
BuktiLab,
},
setup(){
    const columns = [
        { name: 'copy', align: 'left', label: 'kopi', sortable: true },
        { name: 'peminjam', align: 'left', label: 'peminjam', field: 'peminjam', sortable: true },
        { name: 'hari', align: 'left', label: 'hari', field: 'tgl', sortable: true },
        { name: 'jam', align: 'left', label: 'jam', field:'jam', sortable: true },
        { name: 'jam_selesai', align: 'left', label: 'jam Selesai', field:'jam_selesai', format: val => val ? String(val).slice(0, 5) : '', sortable: true },
        { name: 'pekan', align: 'left', label: 'Pekan Ke-', field:'pekan', sortable: true },
        { name: 'kelas', align: 'left', label: 'Kelas', field:'kelas', sortable: true },
        { name: 'topik', align: 'left', label: 'Topik', field:'topik', sortable: true },
        { name: 'status', align: 'center', label: 'Status', field:'status', sortable: true },
        { name: 'proses', align: 'left', label: 'Proses', sortable: false },
        { name: 'alba', align: 'center'},
        { name: 'lkpd', align: 'left', label: 'LKPD', field:'lkpd', sortable: true },
        { name: 'print', align: 'center'},
        { name: 'aksi', align: 'left', label: 'Aksi', sortable: true },
    ]
    return{
      pagination: {
          sortBy: 'hari',
          descending: true,
          rowsPerPage: 15
         },
        columns,
        rows:ref([]),
        dialogInsert:ref(false),
        dialogPilihModul:ref(false),
        confirm:ref(false),
        loading:ref(false),
        filter:ref(null),
        modulFilter:ref(''),
        statusFilter:ref('Semua'),
        modulLkpdOptions:ref([]),
        guruClassrooms:ref([]),
        katalogOptions:ref([]),
        dialogAlasanPenolakan:ref(false),
        prosesRowId:ref(null),
        alasanPenolakanInput:ref(''),
    }
},
data:()=>({
    form:{
        id:"",
        tgl:"",
        jam:"",
        jam_selesai:"",
        pekan:"",
        kelas_id:"",
        topik_id:"",
        status:"",
        modul_lkpd_ids:[],
    }
}),
computed:{
    ...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
    ...mapState("kontrol",["kelas"]),
    ...mapState("kontrol",["katalog"]),
    ...mapState("kontrol",["triger"]),
    ...mapState("kontrol",["url"]),
    displayedColumns() {
      if (this.user.user.role_id === 1) {
        return this.columns.filter(col => col.name !== 'copy')
      }
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
    },
    kelasOptions() {
      return this.kelas;
    },
    isKelasPinjaman() {
      if (!this.user || !this.user.user || this.user.user.role_id !== 3) return false;
      if (!this.form.kelas_id) return false;
      if (!Array.isArray(this.guruClassrooms) || this.guruClassrooms.length === 0) return false;
      return !this.guruClassrooms.some(
        (item) => item.kelas && item.kelas.id === this.form.kelas_id
      );
    },
    selectedModulLkpd() {
      const selectedIds = this.form.modul_lkpd_ids || [];
      return (this.modulLkpdOptions || []).filter((modul) => selectedIds.includes(modul.id));
    },
    filteredModulLkpdOptions() {
      const q = (this.modulFilter || '').toString().toLowerCase();
      if (!q) return this.modulLkpdOptions || [];
      return (this.modulLkpdOptions || []).filter((modul) => {
        const judul = (modul.judul || '').toString().toLowerCase();
        const file = (modul.file_name || '').toString().toLowerCase();
        const uploader = (modul.uploader_name || '').toString().toLowerCase();
        return judul.includes(q) || file.includes(q) || uploader.includes(q);
      });
    }
},
watch:{
triger(){
  this.getPinjam()
}
},
methods:{
    dateTime(value) {
      return moment(value).format('ll');
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
        this.form.tgl=""
        this.form.jam=""
        this.form.jam_selesai=""
        this.form.pekan=""
        this.form.kelas_id=""
        this.form.topik_id=""
        this.form.status=""
        this.form.modul_lkpd_ids=[]
    },
    konfirmasi($id){
        this.form.id=$id
        this.confirm=true
    },
    async getPinjam(){
        this.loading=true
        await axios.get("pinjamLab").then((response)=>{
            this.rows=response.data
        }).finally(()=>{
            this.loading=false
        })
    },
    async getModulLkpd(){
        await axios.get("modul/lkpd").then((response)=>{
            this.modulLkpdOptions=response.data
        })
    },
    async getGuruClassrooms(){
        if (this.user.user.role_id !== 3) return
        await axios.get("classroom").then((response)=>{
            this.guruClassrooms=response.data
        })
    },
    resetKatalogOptions(){
      this.katalogOptions=this.katalog || []
    },
    filterKatalogOptions(val, update){
      update(() => {
        const needle = (val || '').toString().toLowerCase()
        const source = this.katalog || []
        this.katalogOptions = needle
          ? source.filter((item) => (item.topik || '').toString().toLowerCase().includes(needle))
          : source
      })
    },
    modulLkpdLabel(modul){
      if (!modul) return ''
      const source = this.modulSourceLabel(modul)
      return `${modul.judul} - ${source}`
    },
    modulSourceLabel(modul){
      return modul.uploaded_by === this.user.user.id || modul.uploader_name === this.user.user.name ? 'Milik Saya' : 'Disediakan Laboran'
    },
    modulSourceColor(modul){
      return this.modulSourceLabel(modul) === 'Milik Saya' ? 'green-7' : 'blue-7'
    },
    modulLkpdLink(modul){
      if (!modul || !modul.file_path) return '#'
      if (/^https?:\/\//.test(modul.file_path)) return modul.file_path
      return this.url + modul.file_path
    },
    openModul(modul){
      window.open(this.modulLkpdLink(modul), '_blank')
    },
    bukaDialogPilihModul(){
      this.modulFilter=''
      this.dialogPilihModul=true
    },
    isModulTerpilih(id){
      return (this.form.modul_lkpd_ids || []).includes(id)
    },
    toggleModulTerpilih(modul){
      if (this.isModulTerpilih(modul.id)) {
        this.hapusModulTerpilih(modul.id)
        return
      }
      this.form.modul_lkpd_ids=[...(this.form.modul_lkpd_ids || []), modul.id]
    },
    hapusModulTerpilih(id){
      this.form.modul_lkpd_ids=(this.form.modul_lkpd_ids || []).filter((modulId)=>modulId !== id)
    },
    async simpan(){
        await axios.post("pinjamLab",this.form).then((response)=>{
            this.batal()
            this.getPinjam()
            this.$toast.success(`berhasil tersimpan`)
            this.$store.commit('kontrol/SET_TRIGER')
            return response
        }).catch((error)=>{
            const msg = error.response?.data?.message || `Gagal, Mohon Cek kebali`
            this.$toast.error(msg,{
            position: "top",
            duration:2000,
            dismissible:true
         });
            return error
        })
    },
    async edit($id){
        await axios.get("pinjamLab/"+$id).then((response)=>{
            this.dialogInsert=true
            this.form.id=response.data.id
            this.form.tgl=response.data.tgl
            this.form.jam=response.data.jam
            this.form.jam_selesai=response.data.jam_selesai
            this.form.pekan=response.data.pekan
            this.form.kelas_id=response.data.kelas_id
            this.form.topik_id=response.data.katalog_id
            this.form.status=response.data.status
            this.form.modul_lkpd_ids=(response.data.modul_lkpd || []).map((modul)=>modul.id)
        })
    },
    async hapus(){
        await axios.delete("pinjamLab/"+this.form.id).then((response)=>{
            this.batal()
            this.getPinjam()
            this.$toast.success(`berhasil terhapus`)
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

      await axios.put(`peminjaman/lab/${id}/${status}`, payload).then(()=>{
        this.$toast.success('Status berhasil diperbarui')
        this.getPinjam()
      }).catch((error)=>{
        const msg = error.response?.data?.message || 'Gagal memperbarui status'
        this.$toast.error(msg)
      })
    },
    startAutoRefresh(){
      if (this.user.user.role_id === 3) {
        this._refreshTimer = setInterval(() => {
          this.getPinjam()
        }, 15000)
      }
    }
},
created(){
this.getPinjam()
this.getModulLkpd()
this.getGuruClassrooms()
this.$store.dispatch("kontrol/getKelas")
this.$store.dispatch("kontrol/getKatalog").then(()=>this.resetKatalogOptions())
this.startAutoRefresh()
},
beforeUnmount() {
  if (this._refreshTimer) {
    clearInterval(this._refreshTimer)
  }
}
}
</script>
