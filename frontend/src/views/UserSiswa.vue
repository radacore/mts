<template>
  <q-page class="q-ma-sm">
    <div v-if="authenticated">
      <q-card v-if="user.user.role_id==1 || user.user.role_id==2">
        <q-card-section>
           <q-table
              title="User Siswa"
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
            <q-btn label="Insert" class="q-ml-md" icon="o_add" color="green-7" @click="dialogInsert=true" />
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
                <q-toolbar-title class="text-green-7"><span class="text-weight-medium">Data</span>Ruangan Belajar</q-toolbar-title>
                <q-btn flat round dense icon="close" v-close-popup />
              </q-toolbar>
              <q-separator/>
            <q-card-section style="max-height: 60vh" class="scroll">
              <q-form>
                <q-input outlined v-model="form.name" label="Nama Lengkap" class="q-my-sm" color="green-3" dense />
                <q-input outlined v-model="form.username" label="Username" class="q-my-sm" color="green-3" dense />
                <q-input outlined v-model="form.email" label="E-mail" class="q-my-sm" color="green-3" dense />
                <q-input outlined v-model="form.password" label="Password" class="q-my-sm" color="green-3" dense />
                <q-select outlined v-model="form.rombel_id" :options="kelas" emit-value map-options option-value="id" option-label="kelas" label="Kelas" dense/>
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
import { mapGetters } from 'vuex';
export default {
setup(){
  const columns = [
        { name: 'name', align: 'left', label: 'Nama Lengkap', field: 'name', sortable: true },
        { name: 'kelas', align: 'left', label: 'Kelas', field:'kelas', sortable: true },
        { name: 'username', align: 'left', label: 'Username', field:'username', sortable: true },
        { name: 'email', align: 'left', label: 'E-mail', field:'email', sortable: true },
        { name: 'aksi', align: 'left', label: 'Aksi', sortable: true },
    ]
  return{
    columns,
    filter:ref(null),
    dialogInsert:ref(false),
    confirm:ref(false),
    loading:ref(false),
    rows:ref([]),
    kelas:ref([]),
  }
},
data:()=>({
form:{
  id:"",
  name:"",
  username:"",
  email:"",
  password:"",
  rombel_id:"",
}
}),
computed:{
  ...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
},
methods:{
  batal(){
    this.dialogInsert=false
    this.confirm=false
    this.form.id=""
    this.form.name=""
    this.form.email=""
    this.form.username=""
    this.form.password="mtsn2023"
    this.form.rombel_id=""
  },
  konfirmasi($id){
    this.form.id=$id
    this.confirm=true
  },
  async getData(){
    this.loading=true
    await axios.get("user/siswa").then((response)=>{
      this.rows=response.data
    }).finally(()=>{
      this.loading=false
    })
  },
  async simpan(){
    await axios.post("user/siswa",this.form).then((response)=>{
      this.batal()
      this.$toast.success(`berhasil tersimpan`)
      this.getData();
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
    await axios.get("user/siswa/"+$id).then((response)=>{
      this.form.id=response.data.id
      this.form.rombel_id=response.data.rombel_id
      this.form.name=response.data.name
      this.form.username=response.data.username
      this.form.email=response.data.email
      this.dialogInsert=true
    })
  },
  async hapus(){
    await axios.delete("user/siswa/"+this.form.id).then((response)=>{
      this.getData()
      this.batal();
      this.$toast.success(`berhasil dihapus`)
      return response
    })
  },
  async getKelas(){
    await axios.get("rombel").then((response)=>{
      this.kelas=response.data
    })
  }
},
created(){
this.getData()
this.getKelas()
}
}
</script>

