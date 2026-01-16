<template>
    <q-page class="q-pa-sm">
        <div v-if="authenticated">
            <q-card v-if="user.user.role_id==1 || user.user.role_id==3">
              <q-card-section>
                 <q-table
                    title="Peminjaman Lab"
                    :rows="rows"
                    :columns="columns"
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
                  <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
                    <template v-slot:append>
                      <q-icon name="search" />
                    </template>
                  </q-input>
                  <q-btn label="Input" class="q-ml-md" icon="o_add" color="green-7" @click="dialogInsert=true" />
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
                   <div v-if="props.row.lkpd">
                    <a :href="url+props.row.lkpd" target="_blank">
                      <img src="../assets/pdf.png" style="max-width:20px;"/>
                    </a>
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
                  </q-td>
                </template>
                <template v-slot:body-cell-alba="props">
                  <q-td :props="props">
                    <div class="row justify-center">
                    <list-pinjam :plid="props.row.id" :katalog_id="props.row.katalog_id"/>
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
                    :nip="props.row.user.bioguru.nip"
                    :hp="props.row.user.bioguru.hp"
                    />
                   </div>
                  </q-td>
                </template>
                <template v-slot:body-cell-aksi="props">
                  <q-td :props="props">
                    <q-btn @click="edit(props.row.id)" round icon="far fa-edit" color="green-7" size="xs" flat/>
                    <q-btn @click="konfirmasi(props.row.id)" round icon="fas fa-trash-alt" color="red" size="xs" flat=""/>
                    <lk-pd :id="props.row.id"/>
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
                       <q-input outlined dense v-model="form.tgl" label="Tanggal" class="q-my-sm" mask="date">
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
                        <q-input outlined dense v-model="form.jam" label="Jam Mulai" mask="time" class="q-my-sm" style="width:200px;" >
                            <template v-slot:append>
                              <q-icon name="access_time" class="cursor-pointer">
                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                  <q-time v-model="form.jam" color="green-7">
                                    <div class="row items-center justify-end">
                                      <q-btn v-close-popup label="Close" color="green-7" flat />
                                    </div>
                                  </q-time>
                                </q-popup-proxy>
                              </q-icon>
                            </template>
                          </q-input>
                        <q-input outlined dense v-model="form.jam_selesai" label="Jam Selesai" mask="time" class="q-my-sm" style="width:200px;" >
                            <template v-slot:append>
                              <q-icon name="access_time" class="cursor-pointer">
                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                  <q-time v-model="form.jam_selesai" color="green-7">
                                    <div class="row items-center justify-end">
                                      <q-btn v-close-popup label="Close" color="green-7" flat />
                                    </div>
                                  </q-time>
                                </q-popup-proxy>
                              </q-icon>
                            </template>
                          </q-input>
                          <q-select outlined v-model="form.kelas_id" :options="kelas" emit-value map-options option-value="id" option-label="kelas" label="Kelas" class="q-my-sm" dense/>
                          <q-select outlined v-model="form.topik_id" :options="katalog" emit-value map-options option-value="id" option-label="topik" label="Topik" dense/>
                      <q-input outlined v-model="form.pekan" label="Pekan Ke-" class="q-my-sm" color="green-3" dense />
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
              </div>
      </q-page>
</template>

<script>
import { ref } from '@vue/reactivity';
import axios from 'axios';
import { mapGetters, mapState } from 'vuex';
import ListPinjam from '@/components/ListPinjam.vue';
import LkPd from '@/components/LkPd.vue';
import KopiLab from '@/components/KopiLab.vue';
import BuktiLab from '@/components/BuktiLab.vue';
import moment from "moment";
import "moment/locale/id";
moment.locale("id");
export default {
components:{
ListPinjam,
LkPd,
KopiLab,
BuktiLab,
},
setup(){
    const columns = [
        { name: 'copy', align: 'left', label: 'kopi', sortable: true },
        { name: 'hari', align: 'left', label: 'hari', sortable: true },
        { name: 'jam', align: 'left', label: 'jam', field:'jam', sortable: true },
        { name: 'jam_selesai', align: 'left', label: 'jam Selesai', field:'jam_selesai', sortable: true },
        { name: 'pekan', align: 'left', label: 'Pekan Ke-', field:'pekan', sortable: true },
        { name: 'kelas', align: 'left', label: 'Kelas', field:'kelas', sortable: true },
        { name: 'topik', align: 'left', label: 'Topik', field:'topik', sortable: true },
        { name: 'status', align: 'center', label: 'Status', field:'status', sortable: true },
        { name: 'alba', align: 'center'},
        { name: 'lkpd', align: 'left', label: 'LKPD', field:'lkpd', sortable: true },
        { name: 'print', align: 'center'},
        { name: 'aksi', align: 'left', label: 'Aksi', sortable: true },
    ]
    return{
      pagination: {
          rowsPerPage: 15
         },
        columns,
        rows:ref([]),
        dialogInsert:ref(false),
        confirm:ref(false),
        loading:ref(false),
        filter:ref(null),
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
    async simpan(){
        await axios.post("pinjamLab",this.form).then((response)=>{
            this.batal()
            this.getPinjam()
            this.$toast.success(`berhasil tersimpan`)
            this.$store.commit('kontrol/SET_TRIGER')
            return response
        }).catch((error)=>{
            this.$toast.error(`Gagal, Mohon Cek kebali`,{
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
        })
    },
    async hapus(){
        await axios.delete("pinjamLab/"+this.form.id).then((response)=>{
            this.batal()
            this.getPinjam()
            this.$toast.success(`berhasil terhapus`)
            return response
        })
    }
},
created(){
this.getPinjam()
this.$store.dispatch("kontrol/getKelas")
this.$store.dispatch("kontrol/getKatalog")
}
}
</script>

