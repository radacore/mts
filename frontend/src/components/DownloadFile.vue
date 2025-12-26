<template>
    <q-btn label="download" flat rounded dense @click="tableToExcel('table', 'mts Table')"/>
    <div id="document" style="display: none">
        <table class="table1" width="100%" ref="table">
          <thead>
            <tr>
              <th class="th">NAMA SISWA</th>
              <th class="th">NILAI</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="lst in files" :key="lst.id">
              <td>{{lst.user.name}}</td>
              <td>
                {{lst.nilai}}
              </td>
            </tr>
          </tbody>
        </table>  
      </div>
</template>

<script>
import { ref } from '@vue/reactivity'
import axios from 'axios'
export default {
props:["tid"],
setup(){
    return{
        files:ref([]),
        excelName: "Tugas Praktikum File",
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
    tid(){
        this.getDataTugasFile()
    }
},
methods:{
 async getDataTugasFile(){
  await axios.get("dataTugas/file/"+this.tid).then((response)=>{
    this.files=response.data
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
this.getDataTugasFile()
}
}
</script>
