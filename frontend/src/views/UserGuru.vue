<template>
    <q-page class="q-pa-sm">
      <div v-if="authenticated">
        <q-card v-if="user.user.role_id==1 || user.user.role_id==2">
         <q-card-section>
            <q-table
               title="User Guru"
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
               <foto-inv :id="props.row.id"/>
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
                   <q-toolbar-title class="text-green-7"><span class="text-weight-medium">Data</span>Super User</q-toolbar-title>
                   <q-btn flat round dense icon="close" v-close-popup />
                 </q-toolbar>
                 <q-separator/>
               <q-card-section style="max-height: 60vh" class="scroll">
                 <q-form>
                   <q-input outlined v-model="form.name" label="Nama Lengkap" class="q-my-sm" color="green-3" dense />
                   <q-input outlined v-model="form.username" label="Username" class="q-my-sm" color="green-3" dense />
                   <q-input outlined v-model="form.email" label="E-mail" class="q-my-sm" color="green-3" dense />
                   <q-input outlined v-model="form.password" label="Password" class="q-my-sm" color="green-3" dense />
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
import { ref } from '@vue/reactivity'
import axios from 'axios'
import { mapGetters } from 'vuex'
export default {
setup(){
    const columns = [
        { name: 'name', align: 'left', label: 'Name', field: 'name', sortable: true },
        { name: 'username', align: 'left', label: 'Username', field: 'username', sortable: true },
        { name: 'email', align: 'left', label: 'E-mail', field: 'email', sortable: true },
        { name: 'role', align: 'left', label: 'Role', field:'role', sortable: true },
        { name: 'aksi', align: 'left', label: 'Aksi', sortable: true },
    ]
    return{
        columns,
        rows:ref([]),
        filter:ref(null),
        dialogInsert:ref(false),
        confirm:ref(false),
        loading:ref(false),
    }
},
data:()=>({
form:{
    id:"",
    name:"",
    username:"",
    email:"",
    password:"mts2023",
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
        this.form.username=""
        this.form.email=""
        this.form.password=""
    },
    konfirmasi($id){
        this.form.id=$id
        this.confirm=true
    },
    async getUser(){
        this.loading=true
        await axios.get("user/guru").then((response)=>{
            this.rows=response.data
        }).finally(()=>{
            this.loading=false
        })
    },
    async simpan(){
        await axios.post("user/guru",this.form).then((response)=>{
            this.batal()
            this.$toast.success(`berhasil tersimpan`)
            this.getUser()
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
        await axios.get("user/guru/"+$id).then((response)=>{
            this.form.id=response.data.id
            this.form.name=response.data.name
            this.form.username=response.data.username
            this.form.email=response.data.email
            this.dialogInsert=true
        })
    },
    async hapus(){
        await axios.delete("user/guru/"+this.form.id).then((response)=>{
            this.batal()
            this.$toast.success(`berhasil dihapus`)
            this.getUser();
            return response
        })
    }
},
created(){
    this.getUser();
}
}
</script>
<style lang="sass">
.my-sticky-header-table
.q-table thead tr th
    background: rgb(248, 249, 251)
</style>

