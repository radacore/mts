<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
        <q-card v-if="user.user.role_id==1 || user.user.role_id==2">
          <q-card-section>
             <q-table
                title="Ruang Belajar"
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
              <q-btn label="Insert" class="q-ml-md" icon="o_add" color="green-7" @click="dialogInsert=true" />
            </template>
             <template v-slot:body-cell-aksi="props">
               <q-td :props="props" class="q-gutter-xs">
                 <q-btn @click="edit(props.row.id)" round icon="edit_square" color="green-7" size="xs" flat>
                   <q-tooltip>Edit Rombel</q-tooltip>
                 </q-btn>
                 <q-btn @click="konfirmasi(props.row.id)" round icon="delete" color="red" size="xs" flat>
                   <q-tooltip>Hapus Rombel</q-tooltip>
                 </q-btn>
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
               <q-toolbar class="q-px-md q-pt-sm">
                   <q-toolbar-title class="text-green-7">
                     <div class="text-subtitle1 text-weight-bold">Rombel / Ruang Belajar</div>
                     <div class="text-caption text-grey-7">Kelola nama rombel yang digunakan pada data kelas.</div>
                   </q-toolbar-title>
                   <q-btn flat round dense icon="close" v-close-popup />
                 </q-toolbar>
                 <q-separator/>
               <q-card-section style="max-height: 60vh" class="scroll q-pt-md">
                 <q-form>
                   <q-input
                     outlined
                     v-model="kelas"
                     label="Nama Rombel"
                     hint="Contoh: Kelas VII A atau Ruang Belajar IPA"
                     persistent-hint
                     class="q-my-sm"
                     color="green-3"
                     dense
                   />
                 </q-form>
               </q-card-section>
               <q-separator/>
               <q-card-actions align="right" class="bg-white q-pa-md q-gutter-sm">
                 <q-btn label="Batal" color="blue-grey-7" flat no-caps @click="batal"/>
                 <q-btn label="Simpan" icon="save" color="green-7" unelevated no-caps @click="simpan"/>
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
                    <q-item-label class="text-subtitle2">Hapus Rombel ini?</q-item-label>
                    <q-item-label caption lines="2">Data Ruang Belajar yang dipilih akan dihapus.</q-item-label>
                  </q-item-section>
                </q-item>
              </q-card-section>
        
              <q-card-actions align="right">
                <q-btn label="Batal" color="primary" @click="batal" dense />
                <q-btn label="Hapus" color="red" @click="hapus" dense />
              </q-card-actions>
            </q-card>
          </q-dialog>
          </div>
  </q-page>
</template>

<script>
import { ref } from '@vue/reactivity';
import { mapGetters } from 'vuex';
import axios from 'axios';
export default {
setup(){
    const columns = [
        { name: 'id', align: 'left', label: 'id', field: 'id', sortable: true },
        { name: 'kelas', align: 'left', label: 'Kelas', field:'kelas', sortable: true },
        { name: 'aksi', align: 'left', label: 'Aksi', sortable: true },
    ]
    return{
        columns,
        dialogInsert:ref(false),
        confirm:ref(false),
        loading:ref(false),
        filter:ref(null),
        pagination:ref({
            rowsPerPage:15,
        }),
        rows:ref([]),
        id:ref(""),
        kelas:ref(""),
    }
},
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
        this.id=""
        this.kelas=""
    },
    konfirmasi($id){
    this.id=$id
    this.confirm=true
  },
    async simpan(){
        const form=new FormData()
        form.append("id", this.id)
        form.append("kelas", this.kelas)
    await axios.post("rombel",form).then((response)=>{
      this.batal()
      this.$toast.success(`berhasil tersimpan`)
      this.batal();
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
  async getData(){
    this.loading=true
    await axios.get("rombel").then((response)=>{
      this.rows=response.data
    }).finally(()=>{
      this.loading=false
    })
  },
  async edit($id){
    await axios.get("rombel/"+$id).then((response)=>{
      this.id=response.data.id
      this.kelas=response.data.kelas
      this.dialogInsert=true
    })
  },
  async hapus(){
    await axios.delete("rombel/"+this.id).then((response)=>{
      this.getData()
      this.batal();
      this.$toast.success(`berhasil dihapus`)
      return response
    })
  }
},
created(){
this.getData()
}
}
</script>
<style lang="sass">
.my-sticky-header-table
.q-table thead tr th
    background: rgb(248, 249, 251)
</style>
