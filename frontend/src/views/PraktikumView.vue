<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
        <q-card v-if="user.user.role_id==3" class="praktikum-panel" flat>
          <q-card-section class="q-pb-sm">
            <q-breadcrumbs>
              <q-breadcrumbs-el :label="user.user.name" icon="home" to="/" class="text-green-7" />
              <q-breadcrumbs-el label="Ruang Belajar" icon="cast_for_education" />
            </q-breadcrumbs>
          </q-card-section>

          <q-card-section>
            <div class="row items-center justify-between q-col-gutter-md q-mb-sm">
              <div class="col-12 col-md-8">
                <div class="text-h5 text-weight-bold text-green-10">Kelas Praktikum Saya</div>
                <div class="text-caption text-grey-7">Kelola ruang praktikum, materi, tugas, dan absensi siswa.</div>
              </div>
              <div class="col-12 col-md-4">
                <q-card flat class="summary-card q-pa-sm">
                  <div class="text-caption text-grey-7">Total siswa lintas semua kelas</div>
                  <div class="text-h6 text-weight-bold text-green-9">{{ totalSiswaTerdaftar }} Siswa</div>
                </q-card>
              </div>
            </div>

            <q-inner-loading :showing="loading">
              <q-spinner-ios size="30px" color="green-7" />
            </q-inner-loading>

            <div v-if="!loading && datas.length === 0" class="text-center q-py-xl text-grey-7">
              <q-icon name="o_school" size="48px" color="grey-5" />
              <div class="text-subtitle1 q-mt-sm">Belum ada kelas praktikum.</div>
              <div class="text-caption">Klik tombol tambah di kanan bawah untuk membuat kelas baru.</div>
            </div>

            <div v-else class="row q-col-gutter-md">
              <div v-for="row in datas" :key="row.id" class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3">
                <q-card class="my-card" flat>
                  <q-card-section class="my-card__head text-white">
                    <div class="row items-start no-wrap">
                      <div class="col">
                        <div class="text-subtitle1 text-weight-bold ellipsis-2-lines">{{ row.katalog.topik }}</div>
                        <div class="text-caption text-green-1 q-mt-xs">{{ row.kelas.kelas }}</div>
                      </div>
                      <div class="col-auto">
                        <q-btn color="white" text-color="green-8" round flat dense icon="more_vert">
                          <q-menu cover auto-close>
                            <q-list>
                              <q-item clickable @click="edit(row.id)">
                                <q-item-section>Edit Kelas</q-item-section>
                              </q-item>
                              <q-item clickable @click="konfirmasi(row.id)">
                                <q-item-section class="text-red">Hapus Kelas</q-item-section>
                              </q-item>
                            </q-list>
                          </q-menu>
                        </q-btn>
                      </div>
                    </div>
                  </q-card-section>

                  <q-card-section>
                    <div class="row items-center justify-between">
                      <div class="text-caption text-grey-7">Pengajar</div>
                      <div class="text-caption text-grey-8 text-weight-medium">{{ user.user.name }}</div>
                    </div>
                    <div class="row items-center justify-between q-mt-sm">
                      <q-badge color="green-1" text-color="green-9" class="q-pa-sm">
                        <q-icon name="o_groups" size="16px" class="q-mr-xs" />
                        {{ row.jumlah_siswa || 0 }} Siswa
                      </q-badge>
                    </div>
                  </q-card-section>

                  <q-separator />

                  <q-card-actions align="right" class="q-px-md q-pb-md">
                    <q-btn
                      :to="{ name: 'ruang-praktikum', params: { class_id: row.id }}"
                      unelevated
                      color="green-7"
                      icon="o_login"
                      label="Masuk Kelas"
                      no-caps
                    />
                  </q-card-actions>
                </q-card>
              </div>
            </div>
          </q-card-section>
          
        <q-page-sticky position="bottom-right" :offset="[18, 18]">
            <q-fab
            vertical-actions-align="right"
            color="green-7"
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
      loading:ref(false),
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
    totalSiswaTerdaftar(){
      return this.datas.reduce((total, row) => total + Number(row.jumlah_siswa || 0), 0)
    }
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
    this.loading = true
    await axios.get("classroom").then((response)=>{
      this.datas=response.data
    }).finally(()=>{
      this.loading = false
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

<style lang="sass">
.praktikum-panel
  background: linear-gradient(180deg, #f6fff7 0%, #ffffff 100%)
  border: 1px solid #d9ebdc

.my-card
  border: 1px solid #dfebe1
  border-radius: 14px
  box-shadow: 0 10px 28px rgba(109, 139, 116, 0.12)
  transition: transform 0.25s ease, box-shadow 0.25s ease
  &:hover
    transform: translateY(-4px)
    box-shadow: 0 14px 30px rgba(109, 139, 116, 0.18)

.my-card__head
  background: linear-gradient(135deg, #1b5e20 0%, #43a047 100%)

.summary-card
  background: linear-gradient(180deg, #eefaf0 0%, #ffffff 100%)
  border: 1px solid #d7eadb
  border-radius: 12px
</style>
