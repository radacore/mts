<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
      <q-card v-if="user.user.role_id==1 || user.user.role_id==2">
         <q-card-section>
            <q-table
            grid
            title="Katalog"
            :rows="rows"
            :columns="columns"
            row-key="name"
            :filter="filter"
            :loading="loading"
            hide-header
            dense
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
      
            <template v-slot:item="props">
              <div class="q-pa-xs col-xs-12 col-sm-6 col-md-4">
                <q-card class="bayangan">
                  <q-card-section class="text-center">
                    <div class="row justify-between">
                    <div>
                        <span class="text-green-7 text-weight-bold">Topik:</span> <strong>{{ props.row.topik }}</strong>
                    </div>
                    <div>
                        <q-icon name="edit_square" size="xs" color="green" @click="edit(props.row.id)"/>
                        <q-icon name="delete" size="xs" color="red" @click="konfirmasi(props.row.id)"/>
                    </div>
                    </div>
                  </q-card-section>
                  <q-separator />
                  <q-card-section class="flex">
                    <data-katalog :katalog_id="props.row.id"/>
                  </q-card-section>
                </q-card>
              </div>
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
    </div> 
     <!-- DIALOG -->
     <q-dialog v-model="dialogInsert">
        <q-card style="width: 500px; max-width: 80vw;">
          <q-toolbar>
              <q-toolbar-title class="text-green-7"><span class="text-weight-medium">Katalog</span> Praktikum</q-toolbar-title>
              <q-btn flat round dense icon="close" v-close-popup />
            </q-toolbar>
            <q-separator/>
          <q-card-section style="max-height: 60vh" class="scroll">
            <q-form>
              <q-input outlined v-model="topik" label="Topik" class="q-my-sm" color="green-3" dense />
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
import { mapGetters,mapState } from 'vuex';
import DataKatalog from '@/components/DataKatalog.vue'
export default {
components:{
DataKatalog,
},
setup(){
    const columns = [
        { name: 'topik', align: 'center', label: 'Topik', field: 'topik', sortable: true },
        { name: 'alat', align: 'center', label: 'Alat dan Bahan', field: 'alat', sortable: true },
    ]
    return{
        filter:ref(null),
        columns,
        rows:ref([]),
        dialogInsert:ref(false),
        confirm:ref(false),
        id:ref(""),
        topik:ref(""),
        loading:ref(false),
    }
},
computed:{
    ...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
    ...mapState("kontrol",["triger"])
},
watch:{
triger(){
    this.getData();
}
},
methods:{
    batal(){
        this.dialogInsert=false
        this.confirm=false
        this.id=""
        this.topik=""
    },
    konfirmasi($id){
        this.id=$id
        this.confirm=true
    },
    async simpan(){
        const form=new FormData
        form.append("id", this.id)
        form.append("topik", this.topik)
    await axios.post("katalog", form).then((response)=>{
        this.batal();
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
    async getData(){
      this.loading=true
        await axios.get("katalog").then((response)=>{
            this.rows=response.data
        }).finally(()=>{
          setTimeout(()=>{
              this.loading=false
          },1000);
        })
    },
    async edit($id){
        await axios.get("katalog/"+$id).then((response)=>{
            this.id=response.data.id
            this.topik=response.data.topik
            this.dialogInsert=true
        })
    },
    async hapus(){
        await axios.delete("katalog/"+this.id).then((response)=>{
            this.batal()
            this.$toast.success(`berhasil dihapus`)
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

<style lang="sass">
.q-card .bayangan
  box-shadow: 0 10px 30px rgba(146, 153, 184, 0.15) !important
</style>