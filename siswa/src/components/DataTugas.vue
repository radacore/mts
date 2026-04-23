<template>
  <div>
    <q-btn v-if="checkPermission('esay')" flat label="jawaban Singkat/essay" rounded class="q-mx-sm q-px-md" color="green-7" @click="esay" dense/>
    <q-btn v-if="checkPermission('upload')" flat label="Upload Tugas" rounded class="q-mx-sm q-px-md" color="primary" @click="up" dense/>
    <q-btn v-if="checkPermission('link')" flat label="Link Tugas" rounded class="q-mx-sm q-px-md" color="purple" @click="link" dense/>
    <q-separator class="q-my-sm"/>
    <div v-if="status==1">
      <div v-for="es in dataEsay" :key="es.id">
        <div class="submission-row">
          <div class="submission-main">
            <div class="text-caption text-grey-7">Jawaban Anda:</div>
            <p class="q-mt-xs q-mb-none" v-html="es.esay"></p>
          </div>
          <div class="submission-actions">
            <q-avatar v-if="es.nilai" color="green-7" size="25px" class="q-mb-xs text-white">
              {{es.nilai}}
            </q-avatar>
            <q-btn v-if="canModify(es)" dense flat round icon="more_vert" color="grey-7">
              <q-tooltip>Hapus kiriman (jika belum dinilai)</q-tooltip>
              <q-menu auto-close>
                <q-list style="min-width: 120px">
                  <q-item clickable @click="hapusEsay(es.id)">
                    <q-item-section class="text-red">Hapus</q-item-section>
                  </q-item>
                </q-list>
              </q-menu>
            </q-btn>
            <q-chip v-else dense color="grey-3" text-color="grey-8" icon="o_lock" size="sm">Sudah dinilai</q-chip>
          </div>
        </div>
        <q-separator/>
      </div>
      <div v-if="dataEsay.length === 0">
        <q-editor v-model="teks" min-height="5rem" />
        <q-btn :disable="!teks" label="serahkan" rounded class="q-mt-sm" color="green-7" @click="postEsay"/>
      </div>
      <q-banner v-else rounded class="bg-grey-2 text-grey-8 q-mt-sm">
        Anda sudah mengirim jawaban essay. Hapus data lama (jika belum dinilai) untuk kirim ulang.
      </q-banner>
    </div>
    <div v-if="status==2">
      <!-- ✅ Tampilkan file yang sudah diupload dengan banner format -->
      <div v-for="up in dataFile" :key="up.id" class="q-mb-md">
        <div class="submission-row">
          <div class="submission-main">
            <a :href="getDownloadUrl(up.file)" target="_blank">
              <q-banner rounded class="bg-grey-3 q-mb-sm">
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
            <div v-if="up.nilai" class="text-caption text-grey-7">Sudah dinilai guru</div>
          </div>
          <div class="submission-actions">
            <q-avatar v-if="up.nilai" color="green-7" size="30px" text-color="white" class="q-mb-xs">
              {{ up.nilai }}
            </q-avatar>
            <q-btn v-if="canModify(up)" dense flat round icon="more_vert" color="grey-7">
              <q-tooltip>Hapus kiriman (jika belum dinilai)</q-tooltip>
              <q-menu auto-close>
                <q-list style="min-width: 120px">
                  <q-item clickable @click="hapusUpload(up.id)">
                    <q-item-section class="text-red">Hapus</q-item-section>
                  </q-item>
                </q-list>
              </q-menu>
            </q-btn>
            <q-chip v-else dense color="grey-3" text-color="grey-8" icon="o_lock" size="sm">Sudah dinilai</q-chip>
          </div>
        </div>
      </div>

      <!-- ✅ Form upload file baru -->
      <div v-if="dataFile.length === 0">
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
      <q-banner v-else rounded class="bg-grey-2 text-grey-8 q-mt-sm">
        Anda sudah mengirim upload tugas. Hapus data lama (jika belum dinilai) untuk kirim ulang.
      </q-banner>
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
             <div class="row justify-start items-center">
               <q-avatar v-if="li.nilai" color="green-7" size="sm" text-color="white">
                 {{li.nilai}}
               </q-avatar>
               <q-btn v-if="canModify(li)" dense flat round icon="more_vert" color="grey-7">
                 <q-tooltip>Hapus kiriman (jika belum dinilai)</q-tooltip>
                 <q-menu auto-close>
                   <q-list style="min-width: 120px">
                     <q-item clickable @click="hapusLink(li.id)">
                       <q-item-section class="text-red">Hapus</q-item-section>
                     </q-item>
                   </q-list>
                 </q-menu>
               </q-btn>
               <q-chip v-else dense color="grey-3" text-color="grey-8" icon="o_lock" size="sm">Terkunci</q-chip>
              </div>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
      <div v-if="dataLink.length === 0">
        <q-input outlined v-model="tautan" label="Link Tugas" class="q-my-sm" />
        <q-btn label="tautkan" color="green-7" rounded @click="saveLink"/>
      </div>
      <q-banner v-else rounded class="bg-grey-2 text-grey-8 q-mt-sm">
        Anda sudah mengirim link tugas. Hapus data lama (jika belum dinilai) untuk kirim ulang.
      </q-banner>
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
  canModify(row) {
    return !this.hasNilai(row?.nilai);
  },
  hasNilai(nilai) {
    return nilai !== null && nilai !== undefined && nilai !== '';
  },
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
    this.$toast.success('Jawaban essay berhasil dikirim')
    return response
  }).catch((error)=>{
    const msg = error.response?.data?.message || 'Gagal mengirim jawaban essay'
    this.$toast.error(msg)
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
      this.$toast.success('Jawaban essay berhasil dihapus')
      return response
    }).catch((error)=>{
      const msg = error.response?.data?.message || 'Gagal menghapus jawaban essay'
      this.$toast.error(msg)
    })
  },
  async postUpload(){
    const form=new FormData
    form.append("file", this.file)
    form.append("penugasan_id", this.tugas_id)
    await axios.post("tugasSiswa/upload",form).then((response)=>{
      this.file=null
      this.getUpload()
      this.$toast.success('Upload tugas berhasil dikirim')
      return response
    }).catch((error)=>{
      const msg = error.response?.data?.message || 'Gagal upload tugas'
      this.$toast.error(msg)
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
      this.$toast.success('Upload tugas berhasil dihapus')
      return response
    }).catch((error)=>{
      const msg = error.response?.data?.message || 'Gagal menghapus upload tugas'
      this.$toast.error(msg)
    })
  },
  async hapusLink($id){
    await axios.delete("tugasSiswa/tautan/hapus/"+$id).then((response)=>{
      this.getLinks()
      this.$toast.success('Tautan tugas berhasil dihapus')
      return response
    }).catch((error)=>{
      const msg = error.response?.data?.message || 'Gagal menghapus tautan tugas'
      this.$toast.error(msg)
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
      this.$toast.success('Tautan tugas berhasil dikirim')
      return response
    }).catch((error)=>{
      const msg = error.response?.data?.message || 'Gagal mengirim tautan tugas'
      this.$toast.error(msg)
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
    if (Number(val) === 0) return false;

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
<style scoped lang="sass">
.q-card .bayangan
  box-shadow: 0 10px 30px rgba(146, 153, 184, 0.15) !important

.submission-row
  display: flex
  align-items: flex-start
  justify-content: space-between
  gap: 10px

.submission-main
  flex: 1
  min-width: 0

.submission-actions
  display: flex
  flex-direction: column
  align-items: flex-end
  gap: 6px

</style>
