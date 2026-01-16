<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
        <q-card v-if="user.user.role_id==1 || user.user.role_id==2">
          <q-card-section>
             <q-table
                title="Peminjaman Laboratorium"
                :rows="rows"
                :columns="columns"
                :filter="filter"
                :loading="loading"
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
            <template v-slot:body-cell-hari="props">
                <q-td :props="props">
                 {{day(props.row.tgl)}}, {{dateTime(props.row.tgl)}}
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
                    <img src="../assets/pdf.png" style="max-width:25px;"/>
                  </a>
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
              <template v-slot:body-cell-status="props">
                <q-td :props="props">
                 <div class="row justify-around">
                    <q-chip v-if="props.row.status=='disetujui'" color="green-7" text-color="white" icon="o_check_circle" dense>
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
                  <list-pinjam-lab :plid="props.row.id" :katalog_id="props.row.katalog_id"/>
                </q-td>
              </template>
            <template v-slot:body-cell-aksi="props">
              <q-td :props="props">
                <q-btn-dropdown color="green-7" label="Proses" dense unelevated style="width:100px">
                    <q-list>
                      <q-item clickable v-close-popup @click="proses(props.row.id,'disetujui')">
                        <q-item-section>
                          <q-item-label>Disetujui</q-item-label>
                        </q-item-section>
                      </q-item>
              
                      <q-item clickable v-close-popup @click="proses(props.row.id,'ditolak')">
                        <q-item-section>
                          <q-item-label>Ditolak</q-item-label>
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-btn-dropdown>
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
import ListPinjamLab from '@/components/ListPinjamLab.vue';
import BuktiLab from '@/components/BuktiLab.vue';
import moment from "moment";
import "moment/locale/id";
moment.locale("id");
export default {
components:{
ListPinjamLab,
BuktiLab,
},
setup(){
    const columns = [
        { name: 'peminjam', align: 'left', label: 'Peminjam',field:'peminjam', sortable: true },
        { name: 'hari', align: 'left', label: 'hari', sortable: true },
        { name: 'jam', align: 'left', label: 'jam Mulai', field:'jam', sortable: true },
        { name: 'selesai', align: 'left', label: 'jam Selesai', field:'jam_selesai', sortable: true },
        { name: 'pekan', align: 'left', label: 'Pekan Ke-', field:'pekan', sortable: true },
        { name: 'kelas', align: 'left', label: 'Kelas', field:'kelas', sortable: true },
        { name: 'topik', align: 'left', label: 'Topik', field:'topik', sortable: true },
        { name: 'status', align: 'left', label: 'Status', field:'status', sortable: true },
        { name: 'alba', align: 'center'},
        { name: 'lkpd', align: 'left', label: 'LKPD', field:'lkpd', sortable: true },
        { name: 'print', align: 'center'},
        { name: 'aksi', align: 'left', label: 'Aksi', sortable: true },
    ]
    return{
        columns,
        rows:ref([]),
        dialogInsert:ref(false),
        confirm:ref(false),
        loading:ref(false),
        filter:ref(null),
    }
},
computed:{
    ...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
    ...mapState("kontrol",["url"]),
},
methods:{
    dateTime(value) {
      return moment(value).format('ll');
    },
    day(value) {
      return moment(value).format('dddd');
    },
    async getData(){
        this.loading=true
        await axios.get("peminjaman/lab").then((response)=>{
            this.rows=response.data
        }).finally(()=>{
            this.loading=false
        })
    },
    async proses($id,$data){
        await axios.put("peminjaman/lab/"+$id+"/"+$data).then((response)=>{
            this.getData();
            return response
        })
    }
},
created(){
    this.getData()
}
}
</script>

