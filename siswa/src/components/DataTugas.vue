<template>
  <div>
    <q-btn v-if="checkPermission('esay')" flat label="jawaban Singkat/essay" rounded class="q-mx-sm q-px-md" color="green-7" @click="esay" dense/>
    <q-btn v-if="checkPermission('upload')" flat label="Upload Tugas" rounded class="q-mx-sm q-px-md" color="primary" @click="up" dense/>
    <q-btn v-if="checkPermission('link')" flat label="Link Tugas" rounded class="q-mx-sm q-px-md" color="purple" @click="link" dense/>
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
      <!-- ✅ Tampilkan file yang sudah diupload dengan banner format -->
      <div v-for="up in dataFile" :key="up.id" class="q-mb-md">
        <a :href="getDownloadUrl(up.file)" target="_blank">
          <q-banner rounded class="bg-grey-3 q-mb-md">
            <template v-slot:avatar>
              <q-icon
                :name="getFileIcon(getFileExtension(up.file_name || up.file))"
                :color="getIconColor(getFileExtension(up.file_name || up.file))"
                size="lg"
              />
            </template>
            <div>
              <div class="text-weight-bold">{{ up.file_name || getFileName(up.file) }}</div>
              <div class="text-caption text-grey-7">{{ formatFileSize(up.file_size) }}</div>
            </div>
          </q-banner>
        </a>
        <!-- Tampilkan nilai jika ada -->
        <div v-if="up.nilai" class="q-pl-md">
          <q-avatar color="green-7" size="30px" text-color="white" class="q-mr-sm">
            {{ up.nilai }}
          </q-avatar>
          <span class="text-caption">Nilai dari guru</span>
        </div>
        <!-- Tombol hapus -->
        <q-btn flat icon="delete" color="red" size="sm" @click="hapusUpload(up.id)" class="q-mt-sm"/>
      </div>

      <!-- ✅ Form upload file baru -->
      <q-file filled bottom-slots v-model="file" label="Pilih File" counter>
        <template v-slot:prepend>
          <q-icon name="cloud_upload" @click.stop.prevent />
        </template>
        <template v-slot:append>
          <q-icon name="close" @click.stop.prevent="file = null" class="cursor-pointer" />
        </template>

        <template v-slot:hint>
          Format: jpg, jpeg, png, pdf, doc, docx, xls, xlsx, ppt, pptx
        </template>
      </q-file>
      <q-btn label="upload tugas" rounded class="q-mt-sm" color="green-7" @click="postUpload" :disable="!file"/>
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
props:["tugas_id", "tugas_data"],
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
  },
  // ✅ Helper untuk format ukuran file
  formatFileSize(bytes) {
    if (!bytes || bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
  },
  // ✅ Helper untuk mendapatkan extension dari file path
  getFileExtension(filePath) {
    if (!filePath) return '';
    return filePath.split('.').pop().toLowerCase();
  },
  // ✅ Helper untuk mendapatkan nama file dari path
  getFileName(filePath) {
    if (!filePath) return 'File';
    return filePath.split('/').pop();
  },
  // ✅ Helper untuk download URL
  getDownloadUrl(filePath) {
    if (!filePath) return '#';
    if (filePath.startsWith('http')) {
      return filePath;
    }
    return `${this.url}${filePath}`;
  },
  // ✅ Helper untuk file icon
  getFileIcon(extension) {
    if (!extension) return 'help';
    const ext = extension.toLowerCase();
    if (ext === 'pdf') return 'picture_as_pdf';
    if (['ppt', 'pptx'].includes(ext)) return 'slideshow';
    if (['doc', 'docx'].includes(ext)) return 'description';
    if (['xls', 'xlsx'].includes(ext)) return 'table_chart';
    if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return 'image';
    return 'file_present';
  },
  // ✅ Helper untuk icon color
  getIconColor(extension) {
    if (!extension) return 'grey';
    const ext = extension.toLowerCase();
    if (ext === 'pdf') return 'red';
    if (['ppt', 'pptx'].includes(ext)) return 'orange';
    if (['doc', 'docx'].includes(ext)) return 'blue';
    if (['xls', 'xlsx'].includes(ext)) return 'green';
    if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return 'purple';
    return 'grey';
  },
  // ✅ Helper Check Permission
  checkPermission(type) {
    if (!this.tugas_data) return true; // Default true
    
    // Ambil value based on type
    let val;
    if (type === 'esay') val = this.tugas_data.tipe_esay;
    if (type === 'upload') val = this.tugas_data.tipe_upload;
    if (type === 'link') val = this.tugas_data.tipe_link;

    // Handle null/undefined -> Default True (for backward compatibility)
    if (val === undefined || val === null) return true;

    // Handle number/string '0' -> False
    if (val == 0) return false;

    return true;
  }
},
created(){
  this.getEsay();
  this.getUpload();
  this.getLinks();
  console.log('Tugas Data:', this.tugas_data);
}
}
</script>
<style lang="sass">
.q-card .bayangan
  box-shadow: 0 10px 30px rgba(146, 153, 184, 0.15) !important

</style>

