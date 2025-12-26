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
              <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
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
        <q-item-label header>Jawaban | <download-file :tid="tugas_id"/></q-item-label>
        <q-item v-for="row in files" :key="row.id">
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
              <div class="images" v-viewer>
              <img :src="url+row.file" style="width:50px" class="rounded-borders shadow-8"/>
              </div>
            </q-item-label>
          </q-item-section>
  
          <q-item-section side top>
            Nilai: {{row.nilai}}
            <q-popup-edit v-model="row.nilai" auto-save v-slot="scope">
              <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
            </q-popup-edit>
            <nilai-tugas :id="row.id" :nilai="row.nilai"/>
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
                <a :href="url+row.tautan" target="_blank">
                  <span>{{row.tautan}}</span>
                  </a>
              </div>
            </q-item-label>
          </q-item-section>
  
          <q-item-section side top>
            Nilai: {{row.nilai}}
            <q-popup-edit v-model="row.nilai" auto-save v-slot="scope">
              <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
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
},
created(){
this.getDataTugas()
this.getDataTugasFile()
this.getTugasLink()
}
}
</script>

