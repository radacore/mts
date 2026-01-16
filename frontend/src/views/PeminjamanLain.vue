<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
        <q-card v-if="user.user.role_id==2">
          <q-card-section>
            <q-table
            title="Peminjaman Untuk Kegiatan Lain"
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
          <template v-slot:body-cell-nama="props">
            <q-td :props="props">
             {{props.row.user.name}}
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
                <q-chip v-if="props.row.status=='disetujui'" color="green-7" text-color="white" icon="o_check_circle" dense>
                    {{props.row.status}}
                </q-chip>
                <q-chip v-else color="yellow-7" text-color="white" icon="pending" dense>
                    {{props.row.status}}
                </q-chip>
             </div>
            </q-td>
          </template>
          <template v-slot:body-cell-aksi="props">
            <q-td :props="props">
              <q-btn-dropdown color="green-7" label="Proses" dense unelevated style="width:150px">
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
    </div>    
  </q-page>
</template>

<script>
import axios from 'axios';
import { mapGetters } from 'vuex';
import { ref } from '@vue/reactivity';
import moment from "moment";
import "moment/locale/id";
moment.locale("id");
export default {
setup(){
    const columns=[
    { name: 'nama', align: 'left', label: 'Peminjam', sortable: true },
    { name: 'tgl', align: 'left', label: 'Tanggal Pinjam', sortable: true },
    { name: 'mulai', align: 'left', label: 'Jam Mulai', field:'mulai', sortable: true },
    { name: 'selesai', align: 'left', label: 'Jam Selesai', field:'selesai', sortable: true },
    { name: 'kegiatan', align: 'left', label: 'Kegiatan', field:'kegiatan', sortable: true },
    { name: 'status', align: 'left', label: 'Status', sortable: true },
    { name: 'aksi', align: 'left', label: 'Aksi', sortable: true },
    ]
    return{
        columns,
        loading:ref(false),
        filter:ref(null),
        rows:ref([]),
    }
},
computed:{
    ...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
},
methods:{
  dateTime(value) {
      return moment(value).format('LL');
    },
  hari(value) {
      return moment(value).format('dddd');
    },
    async getLain(){
        this.loading=true
        await axios.get("peminjaman/lain").then((response)=>{
            this.rows=response.data
        }).finally(()=>{
            this.loading=false
        })
    },
    async proses($id,$data){
        await axios.put("peminjaman/lain/"+$id+"/"+$data).then((response)=>{
            this.getLain();
            return response
        })
    }
},
created(){
this.getLain();
}
}
</script>

