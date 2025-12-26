<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
        <q-card v-if="user.user.role_id==3">
          <q-card-section>
            <q-breadcrumbs>
                <q-breadcrumbs-el :label="user.user.name" icon="home" to="/" class="text-green-7" />
                <q-breadcrumbs-el label="Ruang Belajar" icon="cast_for_education" />
              </q-breadcrumbs>
          </q-card-section>
          <q-card-section>
            <div class="row justify-start">
              <div v-for="row in datas" :key="row.id" class="col-12 col-sm-12 col-md-3 col-lg-3 col-xl-3 q-ma-xs">
                <q-card flat bordered class="my-card bg-grey-1">
                  <q-card-section>
                    <div class="row items-center no-wrap">
                      <div class="col">
                        <div class="text-h6">{{row.katalog.topik}}</div>
                        <div class="text-subtitle2">{{user.user.name}}</div>
                      </div>
                      <div class="col-auto">
                        <q-btn color="grey-7" round flat icon="more_vert">
                          <q-menu cover auto-close>
                            <q-list>
                              <q-item clickable @click="konfirmasi(row.id)">
                                <q-item-section>Hapus Kelas</q-item-section>
                              </q-item>
                              <q-item clickable @click="edit(row.id)">
                                <q-item-section>Edit Kelas</q-item-section>
                              </q-item>
                            </q-list>
                          </q-menu>
                        </q-btn>
                      </div>
                    </div>
                  </q-card-section>
                  <q-card-section>
                   {{row.kelas.kelas}}
                  </q-card-section>
            
                  <q-separator />
            
                  <q-card-actions>
                    <q-btn :to="{ name: 'ruang-praktikum', params: { class_id: row.id }}" flat>Masuk Kelas</q-btn>
                  </q-card-actions>
                </q-card>
              </div>
            </div>
          </q-card-section>
         
        <q-page-sticky position="bottom-right" :offset="[18, 18]">
            <q-fab
            vertical-actions-align="right"
            color="green"
            glossy
            icon="add"
            direction="up"
          >
            <q-fab-action label-position="left" color="orange"  icon="airplay" label="Tugas" />
            <q-fab-action label-position="left" color="accent" @click="dialogInsert=true" icon="topic" label="Buat Kelas" />
          </q-fab>
        </q-page-sticky>
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
            <q-toolbar-title class="text-green-7"><span class="text-weight-medium">BUAT </span>KELAS</q-toolbar-title>
            <q-btn flat round dense icon="close" v-close-popup />
          </q-toolbar>
          <q-separator/>
        <q-card-section style="max-height: 60vh" class="scroll">
          <q-form>
                <q-select outlined v-model="form.kelas_id" :options="kelas" emit-value map-options option-value="id" option-label="kelas" label="Kelas" class="q-my-sm" dense/>
                <q-select outlined v-model="form.katalog_id" :options="katalog" emit-value map-options option-value="id" option-label="topik" label="Topik" dense/>
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
export default {
setup(){
    return{
      dialogInsert:ref(false),
      confirm:ref(false),
      datas:ref([]),
    }
},
data:()=>({
  form:{
    id:"",
    katalog_id:"",
    kelas_id:"",
  }
}),
computed:{
    ...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
    ...mapState("kontrol",["kelas"]),
    ...mapState("kontrol",["katalog"]),
},
methods:{
  batal(){
    this.confirm=false
    this.dialogInsert=false
    this.form.id=""
    this.form.katalog_id=""
    this.form.kelas_id=""
  },
  konfirmasi($id){
    this.form.id=$id
    this.confirm=true
  },
  async getClassroom(){
    await axios.get("classroom").then((response)=>{
      this.datas=response.data
    })
  },
  async simpan(){
    await axios.post("classroom", this.form).then((response)=>{
      this.batal();
      this.getClassroom();
      this.$toast.success(`berhasil tersimpan`)
      return response
    })
  },
  async edit($id){
    await axios.get("classroom/"+$id).then((response)=>{
      this.dialogInsert=true
      this.form.id=response.data.id
      this.form.katalog_id=response.data.katalog_id
      this.form.kelas_id=response.data.kelas_id
    })
  },
  async hapus(){
    await axios.delete("classroom/"+this.form.id).then((response)=>{
      this.batal()
      this.getClassroom();
      return response
    })
  }
},
created(){
  this.getClassroom();
  this.$store.dispatch("kontrol/getKelas")
  this.$store.dispatch("kontrol/getKatalog")
}
}
</script>

