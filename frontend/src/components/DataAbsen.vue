<template>
  <div>
    <q-list dense>
      <q-item>
        <q-item-section>
          <q-btn label="download" flat dense rounded style="max-width:100px"  @click="tableToExcel('table', 'mts Table')"/>
        </q-item-section>
      </q-item>
      <q-separator/>
        <q-item v-for="row in datas" :key="row.id">
            <q-item-section avatar>
              <q-avatar color="teal" text-color="white">
                <img :src="url+row.user.foto_profile.foto"/>
              </q-avatar>
            </q-item-section>
    
            <q-item-section>
                <q-item-label>{{row.user.name}}</q-item-label>
                <q-item-label caption>Absen Tanggal {{dateTime(row.tgl_absen)}}. Pukul {{pukul(row.created_at)}} Wita</q-item-label>
            </q-item-section>
          </q-item>
    </q-list>
    <div id="document" style="display: none">
      <table class="table1" width="100%" ref="table">
        <thead>
          <tr>
            <th class="th">NAMA SISWA</th>
            <th class="th">TANGGAL ABSEN</th>
            <th class="th">JAM ABSEN</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="lst in datas" :key="lst.id">
            <td>{{lst.user.name}}</td>
            <td>
              {{dateTime(lst.tgl_absen)}}
            </td>
            <td>
              {{pukul(lst.created_at)}}
            </td>
          </tr>
        </tbody>
      </table>  
    </div>
  </div>
</template>

<script>
import { ref } from '@vue/reactivity'
import axios from 'axios'
import { mapState } from 'vuex'
import moment from "moment";
import "moment/locale/id";
moment.locale("id");
export default {
props:["absen_id"],
setup(){
    return{
        datas:ref([]),
        excelName: "Data Absensi",
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
watch:{
absen_id(){
    this.getAbsens();
}
},
computed:{
...mapState("kontrol",["url"])
},
methods:{
    dateTime(value) {
      return moment(value).format('LL');
    },
    pukul(value) {
      return moment(value).format('h:mm:ss');
    },
    async getAbsens(){
        await axios.get("dataAbsen/"+this.absen_id).then((response)=>{
            this.datas=response.data
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
this.getAbsens();
}
}
</script>

