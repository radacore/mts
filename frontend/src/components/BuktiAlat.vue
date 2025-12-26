<template>
    <div>
      <img src="../assets/user-interface.png" style="width:30px" @click="generateReport"/>
      <vue3-html2pdf
        :show-layout="false"
        :float-layout="true"
        :enable-download="true"
        :preview-modal="false"
        :paginate-elements-by-height="1400"
        filename="Peminjaman Alat Lab"
        :pdf-quality="2"
        :manual-pagination="false"
        pdf-format="a4"
        pdf-orientation="portrait"
        pdf-content-width="800px"
        @hasStartedGeneration="hasStartedGeneration()"
        @hasGenerated="hasGenerated($event)"
        ref="html2Pdf"
      >
      <template v-slot:pdf-content>
        <div class="q-ma-md">
          <table
          width="100%"
          border="0"
          style="align: center"
          cellspacing="0"
          cellpadding="0"
        >
          <tr class="th">
            <td rowspan="2" width="10%" class="text-center td">
              <img src="../assets/depag.png" width="60" />
            </td>
            <td class="text-center td" width="50%">LABORATORIUM DIGITAL IPA TERPADU</td>
            <td style="width:10%">&nbsp;</td>
          </tr>
          <tr>
            <td class="text-center">MTsN 1 KOTA MAKASSAR</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
              <td colspan="3">
                  <q-separator color="dark" size="5px" class="q-mb-xs q-mt-xs"/>
                  <q-separator color="dark"/>
              </td>
          </tr>
          <tr>
              <td colspan="3" class="text-center">
                  <p class="q-pt-sm">FORMULIR PEMINJAMAN ALAT</p>
              </td>
          </tr>
         
        </table>
        <table border="0">
          <tr>
            <td>Nama</td>
            <td>:&nbsp; {{peminjam}}</td>
          </tr>
          <tr>
            <td>NIP/NIK/NIM</td>
            <td>:&nbsp; {{ nip }} </td>
          </tr>
          <tr>
            <td>No Handphone</td>
            <td>:&nbsp;{{ hp }} </td>
          </tr>
          <tr>
            <td>Topik</td>
            <td>:&nbsp; {{topiks}}</td>
          </tr>
          <tr>
            <td>Kelas</td>
            <td>:&nbsp; {{kelas}}</td>
          </tr>
          <tr>
            <td>Lokasi Penggunaan Alat</td>
            <td>:&nbsp; {{lokasi}}</td>
          </tr>
          <tr>
            <td>Jam Penggunaan</td>
            <td>:&nbsp; {{jam_start}} Wita</td>
          </tr>
          <tr>
            <td>Tanggal digunakan</td>
            <td>:&nbsp; {{dateTime(tanggal)}}</td>
          </tr>
          <tr>
            <td>Tanggal Pengembalian</td>
            <td>:&nbsp; {{dateTime(tgl_kembalis)}}</td>
          </tr>
        </table>
        <table border="1" class="border-ramping q-mt-md" width="100%" cellpadding="1" cellspacing="1">
          <thead>
            <tr>
              <th>No</th>
              <th>No Inventaris</th>
              <th>Nama Alat</th>
              <th>Jumlah</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, index) in datas" :key="index++">
              <td>&nbsp;{{index}}</td>
              <td style="width:30%">&nbsp;{{row.noreg}}</td>
              <td>&nbsp;{{row.nabar}}</td>
              <td align="center">{{row.diberi}}</td>
            </tr>
          </tbody>
        </table>
        <table class="q-mt-md" width="100%">
          <tr>
            <td width="33%"></td>
            <td width="33%"></td>
            <td class="text-right">
              Makassar, {{ dateTime(dt) }}
            </td>
          </tr>
          <tr>
            <td>
              Mengetahui, <br/>
              Laboran
            </td>
            <td></td>
            <td class="text-right">Peminjam,
  
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td></td>
            <td class="text-right">&nbsp;</td>
          </tr>
          <tr>
            <td> Yunita <br/>
            </td>
            <td class="text-center" valign="bottom">Menyetujui,<br>
              Kepala Laboratorium Digital IPA Terpadu
            </td>
            <td class="text-right">
              {{ peminjam }} <br/>
              {{ nip }}
            </td>
          </tr>
          <tr>
            <td></td>
            <td class="text-center">
              <img src="../assets/kepala.png" style="width:50px"/>
            </td>
            <td></td>
          </tr>
          <tr>
            <td></td>
            <td class="text-center" valign="bottom">
              Dra. Zumrita Ningrum<br/>
              NIP.19681030 199703 2001
  
            </td>
            <td></td>
          </tr>
        </table>
        </div>
      </template>    
      </vue3-html2pdf>
    </div>
  </template>
  
  <script>
  import Vue3Html2pdf from 'vue3-html2pdf'
  import moment from "moment";
  import "moment/locale/id";
import axios from 'axios';
import { ref } from '@vue/reactivity';
  moment.locale("id");
  export default {
  components:{
      Vue3Html2pdf,
  },
  props:["peminjam","topiks","kelas",
        "jam_start","jam_end","tanggal",
        "pid","kid","dt","nip","hp","tgl_kembalis","lokasi"
      ],
  setup(){
      return{
        datas:ref([])
      }
  },
  methods:{
    dateTime(value) {
      return moment(value).format('LL');
    },
      generateReport() {
        this.$refs.html2Pdf.generatePdf();
      },
      async getKatalog(){
      this.loading=true
      await axios.get("filterTopikAlat/"+this.kid+"/"+this.pid).then((response)=>{
          this.datas=response.data
      })
  },
  },
  created(){
    this.getKatalog()
  }
  
  }
  </script>
  <style lang="sass">
  .border-ramping
    border-collapse: collapse
    border-style: solid
    border-color: #ececec
  </style>
  