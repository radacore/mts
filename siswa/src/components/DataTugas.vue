<template>
  <div>
    <q-btn flat label="jawaban Singkat/essay" rounded class="q-mx-sm q-px-md" color="green-7" @click="esay" dense/>
    <q-btn flat label="Upload Tugas" rounded class="q-mx-sm q-px-md" color="primary" @click="up" dense/>
    <q-btn flat label="Link Tugas" rounded class="q-mx-sm q-px-md" color="purple" @click="link" dense/>
    <q-separator class="q-my-sm"/>
    <div v-if="status==1">
      <div v-for="es in dataEsay" :key="es.id">
        Jawaban Anda:
        <p v-html="es.esay"></p>
        <q-avatar v-if="es.nilai" color="green-7" size="25px" class="q-mr-sm q-mb-sm text-white">
          {{es.nilai}}
        </q-avatar>
        <q-icon name="o_edit" size="xs" color="green-7" @click="editEsay(es.id)"/> | 
        <q-icon name="o_delete" size="xs" color="red" @click="hapusEsay(es.id)"/>
        <q-separator/>
      </div>
      <q-editor v-model="teks" min-height="5rem" />
      <q-btn :disable="!teks" label="serahkan" rounded class="q-mt-sm" color="green-7" @click="postEsay"/>
    </div>
    <div v-if="status==2">
      <q-card v-for="up in dataFile" :key="up.id" class="bayangan q-mb-sm" style="max-width:400px">
        <q-card-section horizontal>
          <div class="images" v-viewer>
          <img :src="url+up.file" style="width:100px"/>
          </div>
          <q-card-actions vertical class="justify-around q-px-md">
            <q-avatar v-if="up.nilai" color="green-7" size="25px" class="q-mr-sm q-mb-sm text-white">
              {{up.nilai}}
            </q-avatar>
            <q-icon name="delete" color="red" @click="hapusUpload(up.id)"/>
          </q-card-actions>
        </q-card-section>
       
      </q-card>
      <q-file filled bottom-slots v-model="file" label="Pilih File" counter>
        <template v-slot:prepend>
          <q-icon name="cloud_upload" @click.stop.prevent />
        </template>
        <template v-slot:append>
          <q-icon name="close" @click.stop.prevent="model = null" class="cursor-pointer" />
        </template>

        <template v-slot:hint>
          .jpg, .jpeg, .png
        </template>
      </q-file>
      <q-btn label="upload tugas" rounded class="q-mt-sm" color="green-7" @click="postUpload"/>
    </div>
    <div v-if="status==3">
      <div>
        <q-list>
          <q-item v-for="li in dataLink" :key="li.id">
            <q-item-section avatar>
              <q-icon name="link"/>
            </q-item-section>
            <q-item-section>
              <a :href="li.tautan" target="_blank">
              {{li.tautan}}
              </a>
            </q-item-section>
            <q-item-section side top>
             <div class="row justify-start">
              <q-avatar v-if="li.nilai" color="green-7" size="sm" text-color="white">
                {{li.nilai}}
              </q-avatar>
              <q-icon name="delete" size="xs" color="red" @click="hapusLink(li.id)"/>
             </div>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
      <div>
        <q-input outlined v-model="tautan" label="Link Tugas" class="q-my-sm" />
        <q-btn label="tautkan" color="green-7" rounded @click="saveLink"/>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from '@vue/reactivity'
import axios from 'axios'
import { mapState } from 'vuex'
export default {
props:["tugas_id"],
setup(){
  return{
    status:ref(0),
    id:ref(""),
    teks:ref(""),
    tautan:ref(""),
    file:ref(null),
    dataEsay:ref([]),
    dataFile:ref([]),
    dataLink:ref([]),
  }
},
computed:{
...mapState("kontrol",["url"])
},
methods:{
  esay(){
    this.status=1
  },
  up(){
    this.status=2
  },
  link(){
    this.status=3
  },
  async getEsay(){
    await axios.get("tugasSiswa/esay/"+this.tugas_id).then((response)=>{
      this.dataEsay=response.data
    })
  },
  async postEsay(){
    const form=new FormData
    form.append("penugasan_id", this.tugas_id)
    form.append("teks", this.teks)
    form.append("id", this.id)
  await axios.post("tugasSiswa/esay",form).then((response)=>{
    this.teks=""
    this.id=""
    this.getEsay()
    return response
  })
  },
  async editEsay($id){
    await axios.get("tugasSiswa/esay/edit/"+$id).then((response)=>{
      this.id=response.data.id
      this.teks=response.data.esay
    })
  },
  async hapusEsay($id){
    await axios.delete("tugasSiswa/esay/hapus/"+$id).then((response)=>{
      this.getEsay()
      return response
    })
  },
  async postUpload(){
    const form=new FormData
    form.append("file", this.file)
    form.append("penugasan_id", this.tugas_id)
    await axios.post("tugasSiswa/upload",form).then((response)=>{
      this.file=null
      this.getUpload()
      return response
    })
  },
  async getUpload(){
    await axios.get("tugasSiswa/upload/"+this.tugas_id).then((response)=>{
      this.dataFile=response.data
    })
  },
  async hapusUpload($id){
    await axios.delete("tugasSiswa/upload/hapus/"+$id).then((response)=>{
      this.getUpload()
      return response
    })
  },
  async hapusLink($id){
    await axios.delete("tugasSiswa/tautan/hapus/"+$id).then((response)=>{
      this.getLinks()
      return response
    })
  },
  async getLinks(){
    await axios.get("tugasSiswa/tautan/"+this.tugas_id).then((response)=>{
      this.dataLink=response.data
    })
  },
  async saveLink()
  {
    const form=new FormData
    form.append("penugasan_id", this.tugas_id)
    form.append("tautan", this.tautan)
    await axios.post("tugasSiswa/tautan", form).then((response)=>{
      this.tautan=""
      this.getLinks();
      return response
    })
  }
},
created(){
  this.getEsay();
  this.getUpload();
  this.getLinks();
}
}
</script>
<style lang="sass">
.q-card .bayangan
  box-shadow: 0 10px 30px rgba(146, 153, 184, 0.15) !important

</style>

