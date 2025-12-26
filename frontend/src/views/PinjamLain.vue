<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
      <q-card v-if="user.user.role_id==3">
        <q-card-section>
          <q-table
          title="Peminjaman Alat"
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
        <q-btn label="Input" class="q-ml-md" icon="o_add" color="green-7" @click="dialogInsert=true" />
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
          <list-alat :paid="props.row.id" :kat_id="props.row.katalog_id"/>
      </div>
        </q-td>
      </template>
      <template v-slot:body-cell-aksi="props">
        <q-td :props="props">
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
              <q-input outlined dense v-model="form.tgl" label="Tanggal Pemakaian" class="q-my-sm" mask="date" style="width:250px;">
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
              <q-input outlined dense v-model="form.mulai" label="Jam pemakaian" mask="time" class="q-my-sm" style="width:200px;" >
                  <template v-slot:append>
                    <q-icon name="access_time" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                        <q-time v-model="form.mulai" color="green-7">
                          <div class="row items-center justify-end">
                            <q-btn v-close-popup label="Close" color="green-7" flat />
                          </div>
                        </q-time>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
              </q-input>
              <q-input outlined dense v-model="form.selesai" label="Jam selesai" mask="time" class="q-my-sm" style="width:200px;" >
                  <template v-slot:append>
                    <q-icon name="access_time" class="cursor-pointer">
                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                        <q-time v-model="form.selesai" color="green-7">
                          <div class="row items-center justify-end">
                            <q-btn v-close-popup label="Close" color="green-7" flat />
                          </div>
                        </q-time>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
              </q-input>
              <q-input outlined v-model="form.kegiatan" label="Keperluan" class="q-my-sm" color="green-3" autogrow dense />
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
  </q-page>
</template>

<script>
import { ref } from '@vue/reactivity';
import axios from 'axios';
import { mapGetters } from 'vuex';
import moment from "moment";
import "moment/locale/id";
moment.locale("id");
export default {
setup(){
  const columns=[
    { name: 'tgl', align: 'left', label: 'Tanggal Pinjam', sortable: true },
    { name: 'mulai', align: 'left', label: 'Jam Mulai', field:'mulai', sortable: true },
    { name: 'selesai', align: 'left', label: 'Jam Selesai', field:'selesai', sortable: true },
    { name: 'kegiatan', align: 'left', label: 'Kegiatan', field:'kegiatan', sortable: true },
    { name: 'status', align: 'left', label: 'Status', sortable: true },
    { name: 'aksi', align: 'left', label: 'Aksi', sortable: true },
  ]
  return{
    columns,
    dialogInsert:ref(false),
    confirm:ref(false),
    loading:ref(false),
    filter:ref(null),
    rows:ref([]),
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
},
methods:{
  dateTime(value) {
      return moment(value).format('LL');
    },
  hari(value) {
      return moment(value).format('dddd');
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
      this.$toast.error(`Gagal, Mohon Cek kebali`,{
            position: "top",
            duration:2000,
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
  }
  
},
created(){
this.getData()
}
}
</script>

