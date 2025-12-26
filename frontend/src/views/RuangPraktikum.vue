<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
        <q-card v-if="user.user.role_id==3">
          <q-card-section>
            <q-breadcrumbs>
                <q-breadcrumbs-el :label="user.user.name" icon="home" to="/" class="text-green-7" />
                <q-breadcrumbs-el label="Praktikum" icon="cast_for_education" to="/praktikum" />
                <q-breadcrumbs-el label="Ruang Praktikum" icon="science" />
              </q-breadcrumbs>
              <q-btn label="buat" icon="add" color="green-7" class="q-mt-md" rounded>
                <q-menu
                  transition-show="scale"
                  transition-hide="scale"
                  auto-close
                >
                  <q-list style="min-width: 300px">
                    <q-item clickable @click="dialogInsert=true">
                      <q-item-section avatar>
                        <q-avatar icon="o_assignment_add" />
                      </q-item-section>              
                      <q-item-section>Materi Ajar</q-item-section>
                    </q-item>
                    <q-item clickable @click="dialogTugas=true">
                      <q-item-section avatar>
                        <q-avatar icon="o_assignment_add" />
                      </q-item-section>     
                      <q-item-section>Tugas</q-item-section>
                    </q-item>
                    <q-separator />
                    <q-item clickable @click="dialogAbsen=true">
                      <q-item-section avatar>
                        <q-avatar icon="o_front_hand" />
                      </q-item-section>     
                      <q-item-section>Absensi</q-item-section>
                    </q-item>
                  </q-list>
                </q-menu>
              </q-btn>
          </q-card-section>
          <q-card-section>
            <q-list v-if="moduls.length" padding dense>
              <q-item>
                <q-item-section>
                  <q-item-label overline>{{ruangPraktikum}}</q-item-label>
                  <q-item-label class="text-h6">Modul Ajar</q-item-label>
                </q-item-section>
                <q-item-section side top>
                  <q-item-label caption>{{user.user.name}}</q-item-label>
                </q-item-section>
              </q-item>
              <q-separator size="2px" color="green-7"/>
              <div v-for="row in moduls" :key="row.id">
              <q-expansion-item header-class="text-green-7" switch-toggle-side dense>
                <template v-slot:header>
                  <q-item-section avatar>
                    <q-avatar icon="far fa-clipboard" color="green-7" text-color="white" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="text-h6">{{row.judul}}</q-item-label>
                    <q-item-label caption lines="2">{{row.des}}</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <div class="row items-center">
                      <q-btn icon="more_vert" flat round>
                        <q-menu auto-close>
                          <q-list style="min-width: 100px">
                            <q-item clickable @click="edit(row.id)">
                              <q-item-section>Edit</q-item-section>
                            </q-item>
                            <q-item clickable @click="hapus(row.id)">
                              <q-item-section>Hapus</q-item-section>
                            </q-item>
                          </q-list>  
                        </q-menu>
                      </q-btn>
                    </div>
                  </q-item-section>
                </template>
                <q-card>
                  <q-card-section>
                    <a :href="url+row.file" target="_blank">
                    <q-banner rounded class="bg-grey-3">
                      <template v-slot:avatar>
                        <img
                          v-if="row.file.split('.').pop()=='pdf'"
                          src="../assets/pdf2.png"
                          style="width: 50px; height: 50px"
                        >
                        <img
                          v-else
                          src="../assets/ppt.jpg"
                          style="width: 50px; height: 30px"
                        >
                      </template>
                    </q-banner>
                    </a>
                  </q-card-section>
                </q-card>
              </q-expansion-item>
              <q-separator/>
              </div>
            </q-list>
             <!-- TUGAS -->
             <q-list v-if="tugas.length" padding class="q-my-sm">
              <q-item>
                <q-item-section>
                  <q-item-label overline>{{ruangPraktikum}}</q-item-label>
                  <q-item-label class="text-h6">Penugasan</q-item-label>
                </q-item-section>
                <q-item-section side top>
                  <q-item-label caption>{{user.user.name}}</q-item-label>
                </q-item-section>
              </q-item>
              <q-separator size="2px" color="green-7"/>
              <div v-for="tgs in tugas" :key="tgs.id">
              <q-expansion-item header-class="text-green-7" switch-toggle-side>
                <template v-slot:header>
                  <q-item-section avatar>
                    <q-avatar icon="far fa-clipboard" color="green-7" text-color="white" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="text-h6">{{tgs.jt}}</q-item-label>
                    <q-item-label caption lines="2">{{tgs.soal}}</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <div class="row items-center">
                      <q-btn icon="more_vert" flat round>
                        <q-menu auto-close>
                          <q-list style="min-width: 100px">
                            <q-item clickable @click="editTugas(tgs.id)">
                              <q-item-section>Edit</q-item-section>
                            </q-item>
                            <q-item clickable @click="hapusTugas(tgs.id)">
                              <q-item-section>Hapus</q-item-section>
                            </q-item>
                          </q-list>  
                        </q-menu>
                      </q-btn>
                    </div>
                  </q-item-section>
                </template>
                <q-card>
                  <q-card-section>
                  
                    <q-banner rounded class="bg-grey-3">
                      <data-penugasan :tugas_id="tgs.id"/>
                    </q-banner>
                  
                  </q-card-section>
                </q-card>
              </q-expansion-item>
              <q-separator/>
              </div>
            </q-list>
            <!-- ABSENSI -->
              <q-list v-if="absensis.length" padding>
                <q-item>
                  <q-item-section>
                    <q-item-label overline>{{ruangPraktikum}}</q-item-label>
                    <q-item-label class="text-h6">Absensi</q-item-label>
                  </q-item-section>
                  <q-item-section side top>
                    <q-item-label caption>{{user.user.name}}</q-item-label>
                  </q-item-section>
                </q-item>
                <q-separator size="2px" color="green-7"/>
                <div v-for="abs in absensis" :key="abs.id">
                <q-expansion-item header-class="text-green-7" switch-toggle-side>
                  <template v-slot:header>
                    <q-item-section avatar>
                      <q-avatar icon="far fa-clipboard" color="green-7" text-color="white" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label class="text-h6">Absensi Hari {{hari(abs.tgl_absen)}} Tanggal {{dateTime(abs.tgl_absen)}}</q-item-label>
                      <q-item-label caption lines="2">Buka: {{abs.jam_buka}} Tutup: {{abs.jam_tutup}}</q-item-label>
                    </q-item-section>
                    <q-item-section side>
                      <div class="row items-center">
                        <q-btn v-if="abs.status=='close'" rounded :label="abs.status" color="grey"/>
                        <q-btn v-else rounded :label="abs.status" color="green-7" text-color="white"/>
                        <q-btn icon="more_vert" flat round class="q-ml-sm">
                          <q-menu auto-close>
                            <q-list style="min-width: 100px">
                              <q-item clickable @click="setStatus(abs.id)">
                                <q-item-section>Ubah Status</q-item-section>
                              </q-item>
                              <q-item clickable @click="hapusAbsen(abs.id)">
                                <q-item-section>Hapus</q-item-section>
                              </q-item>
                            </q-list>  
                          </q-menu>
                        </q-btn>
                      </div>
                    </q-item-section>
                  </template>
                  <q-card>
                    <q-card-section>
                    
                      <q-banner rounded class="bg-grey-3">
                        <data-absen :absen_id="abs.id"/>
                      </q-banner>
                    
                    </q-card-section>
                  </q-card>
                </q-expansion-item>
                <q-separator/>
                </div>
              </q-list>
          </q-card-section>

        </q-card>
    </div>
    <!-- DIALOG -->
    <q-dialog v-model="dialogInsert">
      <q-card style="width: 500px; max-width: 80vw;">
        <q-toolbar>
            <q-toolbar-title class="text-green-7"><span class="text-weight-medium">MATERI AJAR</span></q-toolbar-title>
            <q-btn flat round dense icon="close" v-close-popup />
          </q-toolbar>
          <q-separator/>
        <q-card-section style="max-height: 60vh" class="scroll">
          <q-form>
            <q-input v-model="judul" label="Judul Materi" />
            <q-input v-model="des" label="Deskripsi" autogrow />
            <q-file bottom-slots color="purple-12" v-model="file" label="Pilih modul Ajar">
              <template v-slot:prepend>
                <q-icon name="o_photo_camera" />
              </template>
              <template v-slot:hint>
                .pdf, .ppt, pptx
              </template>
            </q-file>
          </q-form>
        </q-card-section>
        <q-separator/>
        <q-card-actions align="right" class="bg-white">
          <q-btn label="simpan" color="green-10" @click="simpan"/>
          <q-btn label="Batal" color="red-10" @click="batal"/>
        </q-card-actions>
      </q-card>
    </q-dialog>  
    <!-- DIALOG -->
    <q-dialog v-model="dialogTugas">
      <q-card style="width: 500px; max-width: 80vw;">
        <q-toolbar>
            <q-toolbar-title class="text-green-7"><span class="text-weight-medium">TUGAS</span></q-toolbar-title>
            <q-btn flat round dense icon="close" v-close-popup />
          </q-toolbar>
          <q-separator/>
        <q-card-section style="max-height: 60vh" class="scroll">
          <q-form>
            <q-input v-model="jt" label="Judul Tugas" />
            <q-input v-model="soal" label="Deskripsi" autogrow />
          </q-form>
        </q-card-section>
        <q-separator/>
        <q-card-actions align="right" class="bg-white">
          <q-btn label="simpan" color="green-10" @click="post"/>
          <q-btn label="Batal" color="red-10" @click="batal"/>
        </q-card-actions>
      </q-card>
    </q-dialog>  
    <!-- ABSENSI -->
    <q-dialog v-model="dialogAbsen">
      <q-card style="width: 500px; max-width: 80vw;">
        <q-toolbar>
            <q-toolbar-title class="text-green-7"><span class="text-weight-medium">ABSENSI</span></q-toolbar-title>
            <q-btn flat round dense icon="close" v-close-popup />
          </q-toolbar>
          <q-separator/>
        <q-card-section style="max-height: 60vh" class="scroll">
          <q-form>
            <q-input  dense v-model="tgl_absen" label="Tanggal" class="q-my-sm" mask="date">
              <template v-slot:append>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                    <q-date v-model="tgl_absen" color="green-7">
                      <div class="row items-center justify-end">
                        <q-btn v-close-popup label="Close" color="green-7" flat />
                      </div>
                    </q-date>
                  </q-popup-proxy>
                </q-icon>
              </template>
          </q-input>
          <q-input dense v-model="jam_buka" label="Jam Buka" mask="time" class="q-my-sm" style="width:200px;" >
            <template v-slot:append>
              <q-icon name="access_time" class="cursor-pointer">
                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                  <q-time v-model="jam_buka" color="green-7">
                    <div class="row items-center justify-end">
                      <q-btn v-close-popup label="Close" color="green-7" flat />
                    </div>
                  </q-time>
                </q-popup-proxy>
              </q-icon>
            </template>
        </q-input>
          <q-input dense v-model="jam_tutup" label="Jam Tutup" mask="time" class="q-my-sm" style="width:200px;" >
            <template v-slot:append>
              <q-icon name="access_time" class="cursor-pointer">
                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                  <q-time v-model="jam_tutup" color="green-7">
                    <div class="row items-center justify-end">
                      <q-btn v-close-popup label="Close" color="green-7" flat />
                    </div>
                  </q-time>
                </q-popup-proxy>
              </q-icon>
            </template>
        </q-input>
          </q-form>
        </q-card-section>
        <q-separator/>
        <q-card-actions align="right" class="bg-white">
          <q-btn label="simpan" color="green-10" @click="absenPost"/>
          <q-btn label="Batal" color="red-10" @click="batal"/>
        </q-card-actions>
      </q-card>
    </q-dialog>

  </q-page>
</template>

<script>
import { ref } from '@vue/reactivity';
import { mapGetters, mapState } from 'vuex';
import DataAbsen from '@/components/DataAbsen.vue';
import DataPenugasan from '@/components/DataPenugasan.vue';
import axios from 'axios';
import moment from "moment";
import "moment/locale/id";
moment.locale("id");
export default {
components:{
DataAbsen,
DataPenugasan,
},
props:["class_id"],
setup(){
    return{
      dialogInsert:ref(false),
      dialogTugas:ref(false),
      dialogAbsen:ref(false),
      confirm:ref(false),
      moduls:ref([]),
      tugas:ref([]),
      absensis:ref([]),
      ruangPraktikum:ref(""),
      id:ref(""),
      judul:ref(""),
      des:ref(""),
      file:ref(null),
      tugas_id:ref(""),
      jt:ref(""),
      soal:ref(""),
      absen_id:ref(""),
      tgl_absen:ref(""),
      jam_buka:ref(""),
      jam_tutup:ref(""),
    }
},
computed:{
    ...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
    ...mapState("kontrol",["url"])
},
methods:{
dateTime(value) {
      return moment(value).format('LL');
    },
hari(value) {
      return moment(value).format('dddd');
    },
batal(){
  this.dialogInsert=false,
  this.confirm=false,
  this.dialogTugas=false,
  this.dialogAbsen=false,
  this.judul=""
  this.file=""
  this.id=""
  this.des=""
  this.tugas_id=""
  this.jt=""
  this.soal=""
},
async cekRuang(){
  await axios.get("classroom/cek/"+this.class_id).then((response)=>{
    this.ruangPraktikum=response.data.katalog.topik
  })
},
async simpan(){
  const form=new FormData
  form.append("id", this.id)
  form.append("class_id", this.class_id)
  form.append("judul", this.judul)
  form.append("des", this.des)
  form.append("file", this.file)
  await axios.post("materi_ajar", form).then((response)=>{
    this.batal()
    this.getMateri()
    this.$toast.success(`berhasil tersimpan`)
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
async absenPost(){
  const form=new FormData
  form.append("class_id", this.class_id)
  form.append("absen_id", this.absen_id)
  form.append("tgl_absen", this.tgl_absen)
  form.append("jam_buka", this.jam_buka)
  form.append("jam_tutup", this.jam_tutup)
  await axios.post("absensi", form).then((response)=>{
    this.$toast.success(`berhasil dibuat`)
    this.getAbsensi()
    this.dialogAbsen=false
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
async post(){
  const form=new FormData
  form.append("tugas_id", this.tugas_id)
  form.append("class_id", this.class_id)
  form.append("jt", this.jt)
  form.append("soal", this.soal)
  await axios.post("penugasan", form).then((response)=>{
    this.batal();
    this.getTugas()
    this.$toast.success(`berhasil tersimpan`)
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
async getMateri(){
  await axios.get("materi_ajar/"+this.class_id).then((response)=>{
    this.moduls=response.data
  })
},
async getTugas(){
  await axios.get("penugasan/"+this.class_id).then((response)=>{
    this.tugas=response.data
  })
},
async getAbsensi(){
await axios.get("absensi/"+this.class_id).then((response)=>{
  this.absensis=response.data
})
},
async edit($id){
  await axios.get("materi_ajar/edit/"+$id).then((response)=>{
    this.id=response.data.id
    this.des=response.data.des
    this.judul=response.data.judul
    this.dialogInsert=true
  })
},
async editTugas($id){
  await axios.get('penugasan/edit/'+$id).then((response)=>{
    this.tugas_id=response.data.id
    this.jt=response.data.jt
    this.soal=response.data.soal
    this.dialogTugas=true
  })
},
async hapus($id){
  await axios.delete("materi_ajar/hapus/"+$id).then((response)=>{
    this.getMateri()
    this.$toast.success(`berhasil dihapus`)
    return response
  })
},
async hapusTugas($id){
  await axios.delete("penugasan/hapus/"+$id).then((response)=>{
    this.getTugas()
    this.$toast.success(`berhasil dihapus`)
    return response
  })
},
async hapusAbsen($id){
  await axios.delete("absensi/hapus/"+$id).then((response)=>{
    this.getAbsensi()
    this.$toast.success(`berhasil dihapus`)
    return response
  })
},
async setStatus($id){
  await axios.put("absensi/status/"+$id).then((response)=>{
    this.getAbsensi();
    return response
  })
}
},
created(){
    this.cekRuang();
    this.getMateri();
    this.getTugas();
    this.getAbsensi();
}
}
</script>
