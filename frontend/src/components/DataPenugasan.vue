<template>
  <div>
    <q-btn flat rounded label="Esay" color="green-7" class="q-mx-sm" @click="esay"/>
    <q-btn flat rounded label="File Tugas" color="green-7" class="q-mx-sm" @click="upload"/>
    <q-btn flat rounded label="Tugas Tautan" color="green-7" class="q-mx-sm" @click="tugaslink"/>
    <q-separator class="q-mt-sm"/>
    <div v-if="status==1">
      <q-list bordered class="rounded-borders">
        <q-item-label header>Jawaban | <q-btn label="download" flat rounded dense @click="tableToExcel('table', 'mts Table')"/></q-item-label>
        <q-item v-for="row in esays" :key="row.id">
          <q-item-section avatar>
            <q-avatar>
              <img :src="url+row.user.foto_profile.foto">
            </q-avatar>
          </q-item-section>
  
          <q-item-section>
            <q-item-label lines="1">{{row.user.name}}</q-item-label>
            <q-item-label caption>
              <span class="text-weight-bold">Jawaban</span>
              <p v-html="row.esay"/>
            </q-item-label>
          </q-item-section>
  
          <q-item-section side top>
            Nilai: {{row.nilai}}
            <q-popup-edit v-model="row.nilai" auto-save v-slot="scope">
              <q-input v-model.number="scope.value" type="number" :rules="[ val => val <= 100 || 'Maksimal 100', val => val >= 0 || 'Min 0' ]" dense autofocus counter @keyup.enter="scope.set" />
            </q-popup-edit>
            <nilai-tugas :id="row.id" :nilai="row.nilai"/>
          </q-item-section>
          <q-separator/>
        </q-item>
        <q-separator inset="item" />
      </q-list>
    </div>
    <div v-if="status==2">
      <q-list bordered class="rounded-borders">
        <q-item-label header>Jawaban File | <download-file :tid="tugas_id"/></q-item-label>
        <q-item v-for="row in files" :key="row.id">
          <q-item-section avatar>
            <q-avatar>
              <img :src="url+row.user.foto_profile.foto">
            </q-avatar>
          </q-item-section>
  
          <q-item-section>
            <q-item-label lines="1">{{row.user.name}}</q-item-label>
            <q-item-label caption>
              <span class="text-weight-bold">File: {{ row.file_name }}</span>
              <br/>
              <span class="text-grey-7">Ukuran: {{ formatFileSize(row.file_size) }}</span>
              <br/>
              <!-- ✅ PREVIEW IMAGE untuk file gambar -->
              <div class="images q-mt-sm" v-viewer v-if="isImageFile(row.file_name)">
                <img :src="url+row.file" style="width:100px" class="rounded-borders shadow-8 cursor-pointer"/>
              </div>
              <!-- ✅ PREVIEW FILE TYPE ICON untuk non-image -->
              <div v-else class="q-mt-sm">
                <q-icon 
                  :name="getFileIcon(getFileExtension(row.file_name))"
                  :color="getIconColor(getFileExtension(row.file_name))"
                  size="lg"
                />
                <q-btn 
                  label="Buka File" 
                  flat rounded color="primary" size="sm"
                  @click="openFile(url+row.file)"
                  class="q-ml-sm"
                />
              </div>
            </q-item-label>
          </q-item-section>
  
          <q-item-section side top>
            <div class="text-right">
              <div>Nilai: {{row.nilai}}</div>
              <q-popup-edit v-model="row.nilai" auto-save v-slot="scope">
                <q-input v-model.number="scope.value" type="number" :rules="[ val => val <= 100 || 'Maksimal 100', val => val >= 0 || 'Min 0' ]" dense autofocus counter @keyup.enter="scope.set" />
              </q-popup-edit>
              <nilai-tugas :id="row.id" :nilai="row.nilai"/>
              <!-- ✅ TOMBOL DOWNLOAD -->
              <q-btn 
                flat round icon="download" 
                color="primary" size="sm"
                :href="url+row.file"
                target="_blank"
                download
                class="q-mt-sm"
              />
            </div>
          </q-item-section>
          <q-separator/>
        </q-item>
        <q-separator inset="item" />
      </q-list>
    </div>
    <div v-if="status==3">
      <q-list bordered class="rounded-borders">
        <q-item-label header>Jawaban | <download-link :lid="tugas_id"/></q-item-label>
        <q-item v-for="row in tautans" :key="row.id">
          <q-item-section avatar>
            <q-avatar>
              <img :src="url+row.user.foto_profile.foto">
            </q-avatar>
          </q-item-section>
  
          <q-item-section>
            <q-item-label lines="1">{{row.user.name}}</q-item-label>
            <q-item-label caption>
              <span class="text-weight-bold">Jawaban</span>
              <br/>
              <div>
                <a :href="row.tautan" target="_blank">
                  <span>{{row.tautan}}</span>
                  </a>
              </div>
            </q-item-label>
          </q-item-section>
  
          <q-item-section side top>
            Nilai: {{row.nilai}}
            <q-popup-edit v-model="row.nilai" auto-save v-slot="scope">
              <q-input v-model.number="scope.value" type="number" :rules="[ val => val <= 100 || 'Maksimal 100', val => val >= 0 || 'Min 0' ]" dense autofocus counter @keyup.enter="scope.set" />
            </q-popup-edit>
            <nilai-tugas :id="row.id" :nilai="row.nilai"/>
          </q-item-section>
          <q-separator/>
        </q-item>
        <q-separator inset="item" />
      </q-list>
    </div>
    <div id="document" style="display: none">
      <table class="table1" width="100%" ref="table">
        <thead>
          <tr>
            <th class="th">NAMA SISWA</th>
            <th class="th">JAWABAN</th>
            <th class="th">NILAI</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="lst in esays" :key="lst.id">
            <td>{{lst.user.name}}</td>
            <td>
              <p v-html="lst.esay"/>
            </td>
            <td>
              {{lst.nilai}}
            </td>
          </tr>
        </tbody>
      </table>  
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { ref } from '@vue/reactivity';
import { mapState } from 'vuex';
import NilaiTugas from './NilaiTugas.vue';
import DownloadFile from './DownloadFile.vue'
import DownloadLink from './DownloadLink.vue';
export default {
components:{
NilaiTugas,
DownloadFile,
DownloadLink,
},
props:["tugas_id"],
setup(){
    return{
      esays:ref([]),
      files:ref([]),
      tautans:ref([]),
      status:ref(0),
      excelName: "Tugas Praktikum",
            uri: "data:application/vnd.ms-excel;base64,",
            template:
            '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64: function (s) {
            return window.btoa(unescape(encodeURIComponent(s)));
            },
            format: function (s, c) {
            return s.replace(/{(\w+)}/g, function (m, p) {
                return c[p];
            });
            },
    }
},
computed:{
...mapState("kontrol",["url"]),
...mapState("kontrol",["triger"])
},
watch:{
  triger(){
    this.getDataTugas();
  }
},
methods:{
  esay(){
    this.status=1
  },
  upload(){
    this.status=2
  },
  tugaslink(){
    this.status=3
  },
async getDataTugas(){
  await axios.get("dataTugas/esay/"+this.tugas_id).then((response)=>{
    this.esays=response.data
  })
},
async getDataTugasFile(){
  await axios.get("dataTugas/file/"+this.tugas_id).then((response)=>{
    this.files=response.data
  })
},
async getTugasLink(){
  await axios.get("dataTugas/tautan/"+this.tugas_id).then((response)=>{
    this.tautans=response.data
  })
},
tableToExcel(table, name) {
      if (!table.nodeType) table = this.$refs.table;
      var ctx = { worksheet: name || "Worksheet", table: table.innerHTML };
      var link = document.createElement("a");
      link.download = !this.excelName.split(".").pop().length
        ? this.excelName + ".xls"
        : this.excelName;
      link.href = this.uri + this.base64(this.format(this.template, ctx));
      link.click();
    },
    // ✅ Helper untuk format ukuran file
    formatFileSize(bytes) {
      if (!bytes || bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    },
    // ✅ Helper untuk check tipe file gambar
    isImageFile(fileName) {
      if (!fileName) return false;
      const ext = fileName.split('.').pop().toLowerCase();
      return ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
    },
    // ✅ Helper untuk mendapatkan extension dari file path
    getFileExtension(filePath) {
      if (!filePath) return '';
      return filePath.split('.').pop().toLowerCase();
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
    // ✅ Helper untuk buka file di tab baru
    openFile(fileUrl) {
      window.open(fileUrl, '_blank');
    }
},
created(){
this.getDataTugas()
this.getDataTugasFile()
this.getTugasLink()
}
}
</script>

