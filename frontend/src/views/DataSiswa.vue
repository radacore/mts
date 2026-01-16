<template>
  <q-page class="q-pa-sm">
    <div v-if="authenticated">
        <q-card v-if="user.user.role_id==2">
            <q-card-section>
              <q-table
              title="Data Siswa"
              :rows="rows"
              :columns="columns"
              :filter="filter"
              :loading="loading"
              :pagination="pagination"
              separator="cell"
              row-key="id"
              selection="multiple"
              v-model:selected="selected"
              flat
              dense
              class="my-sticky-header-table"
             >
             <template v-slot:loading>
                <q-inner-loading showing>
                    <q-spinner-ios size="30px" color="green-7" />
                </q-inner-loading>
             </template>
             <template v-slot:top>
                <div class="text-h6">Data Siswa</div>
                <q-space />
                <!-- Filter Kelas -->
                <q-select
                  v-model="filterKelas"
                  :options="kelasOptions"
                  label="Filter Kelas"
                  option-value="id"
                  option-label="kelas"
                  emit-value
                  map-options
                  clearable
                  dense
                  outlined
                  style="min-width: 150px"
                  class="q-mr-md"
                  @update:model-value="getImport"
                />
                <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
                  <template v-slot:append>
                    <q-icon name="search" />
                  </template>
                </q-input>
                <q-btn 
                  v-if="selected.length > 0" 
                  :label="`Hapus (${selected.length})`" 
                  class="q-ml-md" 
                  icon="o_delete" 
                  color="red" 
                  @click="confirmMultiple=true" 
                />
                <q-btn label="Import" class="q-ml-md" icon="o_upload" color="green-7" @click="dialogImport=true" />
              </template>
              <template v-slot:body-cell-kelas="props">
                <q-td :props="props">
                    {{props.row.kelas.kelas}}
                </q-td>
              </template>    
              <template v-slot:body-cell-user="props">
                <q-td :props="props">
                    <reg-siswa :email="props.row.email" :dataId="props.row.id"/>
                </q-td>
              </template>    
              <template v-slot:body-cell-aksi="props">
                <q-td :props="props">
                    <q-btn round color="red" unelevated icon="delete" size="sm" @click="konfirmasi(props.row.id)"/>
                </q-td>
              </template>    
            </q-table>
            </q-card-section>
        </q-card>
        <!-- IMPORT -->
        <q-dialog v-model="dialogImport" persistent>
            <q-card style="min-width: 350px">
              <q-card-section>
                <div class="text-h6">Import Data Siswa</div>
              </q-card-section>
      
              <q-card-section class="q-pt-none">
                <q-file v-model="file" label="pilih file excel" />
                <div class="text-caption text-grey q-mt-sm">
                  Format Excel: NIS | NAMA | KELAS_ID | EMAIL (dengan header)
                </div>
              </q-card-section>
      
              <q-card-actions align="right" class="text-primary">
                <q-btn flat label="Cancel" v-close-popup />
                <q-btn flat label="Import" @click="importData" :loading="importing" />
              </q-card-actions>
            </q-card>
          </q-dialog>
          
        <!-- LAPORAN IMPORT -->
        <q-dialog v-model="dialogLaporan" persistent>
            <q-card style="min-width: 500px; max-width: 700px">
              <q-card-section class="bg-green-7 text-white">
                <div class="text-h6">Laporan Import</div>
              </q-card-section>
      
              <q-card-section>
                <div class="row q-gutter-md q-mb-md">
                  <q-chip color="green" text-color="white" icon="check_circle">
                    Berhasil: {{ laporanImport.imported }}
                  </q-chip>
                  <q-chip color="red" text-color="white" icon="error">
                    Ditolak: {{ laporanImport.total_duplicates }}
                  </q-chip>
                </div>
                
                <div v-if="laporanImport.duplicates && laporanImport.duplicates.length > 0">
                  <div class="text-subtitle2 q-mb-sm">Data yang Ditolak:</div>
                  <q-table
                    :rows="laporanImport.duplicates"
                    :columns="duplicateColumns"
                    dense
                    flat
                    bordered
                    :pagination="{ rowsPerPage: 5 }"
                  />
                </div>
              </q-card-section>
      
              <q-card-actions align="right">
                <q-btn flat label="Tutup" color="primary" v-close-popup />
              </q-card-actions>
            </q-card>
          </q-dialog>
          
         <!-- KONFIRMASI SINGLE -->
       <q-dialog v-model="confirm" persistent>
        <q-card>
          <q-card-section class="row items-center">
            <q-item>
              <q-item-section side>
                <q-icon color="red" name="fas fa-exclamation-circle" />
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-subtitle2">Apakah Anda Ingin Menghapus Data ini?</q-item-label>
                <q-item-label caption lines="2">Data akan dihapus beserta akun user terkait</q-item-label>
              </q-item-section>
            </q-item>
          </q-card-section>
    
          <q-card-actions align="right">
            <q-btn label="No" color="primary" @click="batal" dense />
            <q-btn label="Yes" color="red" @click="hapusData" dense />
          </q-card-actions>
        </q-card>
      </q-dialog>   
      
      <!-- KONFIRMASI MULTIPLE DELETE -->
       <q-dialog v-model="confirmMultiple" persistent>
        <q-card>
          <q-card-section class="row items-center">
            <q-item>
              <q-item-section side>
                <q-icon color="red" name="fas fa-exclamation-circle" />
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-subtitle2">Hapus {{ selected.length }} data siswa?</q-item-label>
                <q-item-label caption lines="2">Data akan dihapus beserta akun user terkait</q-item-label>
              </q-item-section>
            </q-item>
          </q-card-section>
    
          <q-card-actions align="right">
            <q-btn label="No" color="primary" @click="confirmMultiple=false" dense />
            <q-btn label="Yes" color="red" @click="hapusMultiple" dense :loading="deleting" />
          </q-card-actions>
        </q-card>
      </q-dialog>   
    </div>
  </q-page>
</template>

<script>
import { ref } from '@vue/reactivity';
import axios from 'axios';
import { mapGetters, mapState } from 'vuex';
import RegSiswa from '@/components/RegSiswa.vue'
export default {
components:{
RegSiswa,
},
setup(){
    const columns=[
        { name: 'nis', align: 'left', label: 'NIS', field:'nis', sortable: true },
        { name: 'nama', align: 'left', label: 'NAMA LENGKAP',field:'nama', sortable: true },
        { name: 'kelas', align: 'left', label: 'KELAS',field:'kelas', sortable: true },
        { name: 'email', align: 'left', label: 'E-mail',field:'email', sortable: true },
        { name: 'user', align: 'left', label: 'Status', sortable: true },
        { name: 'aksi', align: 'left', label: 'AKSI', sortable: true },
    ]
    const duplicateColumns=[
        { name: 'nis', align: 'left', label: 'NIS', field:'nis' },
        { name: 'nama', align: 'left', label: 'Nama', field:'nama' },
        { name: 'alasan', align: 'left', label: 'Alasan Ditolak', field:'alasan' },
    ]
    return{
        pagination: {
          rowsPerPage: 15
         },
        columns,
        duplicateColumns,
        rows:ref([]),
        filter:ref(null),
        filterKelas:ref(null),
        kelasOptions:ref([]),
        loading:ref(false),
        dialogImport:ref(false),
        dialogLaporan:ref(false),
        confirm:ref(false),
        confirmMultiple:ref(false),
        file:ref(null),
        id:ref(""),
        selected:ref([]),
        importing:ref(false),
        deleting:ref(false),
        laporanImport:ref({
            imported: 0,
            total_duplicates: 0,
            duplicates: []
        }),
    }
},
computed:{
    ...mapGetters({
      authenticated: "auth/authenticated",
      user: "auth/user",
    }),
    ...mapState("kontrol",["triger"])
},
watch:{
    triger(){
        this.getImport();
    }
},
methods:{
    konfirmasi($id){
        this.id=$id
        this.confirm=true
    },
    batal(){
        this.id=""
        this.confirm=false
    },
    async importData(){
        this.importing = true
        const form=new FormData
        form.append("file", this.file)
        await axios.post("importSiswa", form).then((response)=>{
           this.dialogImport=false
           this.file=null
           
           // Tampilkan laporan
           this.laporanImport = response.data
           this.dialogLaporan = true
           
           this.getImport()
           return response
        }).catch((error)=>{
            this.$toast.error(`Gagal, Mohon Cek kembali`,{
            position: "top",
            duration:2000,
            dismissible:true
         });
            return error
        }).finally(()=>{
            this.importing = false
        })
    },
    async getImport(){
        this.loading=true
        let url = "importSiswa"
        if (this.filterKelas) {
            url += `?kelas_id=${this.filterKelas}`
        }
        await axios.get(url).then((response)=>{
            this.rows=response.data
        }).finally(()=>{
            setTimeout(()=>{
              this.loading=false
          },500)
        })
    },
    async getKelas(){
        await axios.get("rombel").then((response)=>{
            this.kelasOptions = response.data
        })
    },
    async hapusData(){
        await axios.delete("importSiswa/"+this.id).then((response)=>{
            this.$toast.success(`berhasil di hapus`) 
            this.getImport()
            this.batal()
            return response
        })
    },
    async hapusMultiple(){
        this.deleting = true
        const ids = this.selected.map(item => item.id)
        await axios.post("importSiswa/multiple-delete", { ids }).then((response)=>{
            this.$toast.success(`${response.data.deleted} data berhasil dihapus`) 
            this.getImport()
            this.selected = []
            this.confirmMultiple = false
            return response
        }).catch((error)=>{
            this.$toast.error(`Gagal menghapus data`)
            return error
        }).finally(()=>{
            this.deleting = false
        })
    }
},
created(){
    this.getImport()
    this.getKelas()
}
}
</script>
