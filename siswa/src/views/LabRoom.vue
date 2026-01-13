<template>
  <q-page class="q-pa-sm">
    <div class="row justify-center">
        <q-card class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8">
            <q-card-section>
                <div v-for="abs in absens" :key="abs.id">
                <q-expansion-item
                class="shadow-1 overflow-hidden"
                style="border-radius: 10px"
                icon="o_pan_tool"
                label="Absensi"
                header-class="bg-green-11 text-dark"
                expand-icon-class="text-white"
              >
                <q-card>
                  <q-card-section>
                    Absensi Dibuka Pada Pukul {{abs.jam_buka}} Wita, dan Ditutup pada Pukul {{abs.jam_tutup}} Wita
                    <data-absen :absen_id="abs.id"/>
                  </q-card-section>
                </q-card>
              </q-expansion-item>
              </div>
              <div class="q-my-sm" v-if="moduls.length">
                <q-expansion-item
                class="shadow-1 overflow-hidden"
                style="border-radius: 10px"
                icon="o_book"
                label="Modul Ajar"
                header-class="bg-green-1 text-dark"
                expand-icon-class="text-white"
              >
                <q-card>
                  <q-card-section>
                    <div v-for="mod in moduls" :key="mod.id" >
                        <!-- ✅ BANNER FILE MODUL (DARI LABORAN) -->
                        <a :href="getDownloadUrl(mod.modul_file_path)" target="_blank" v-if="mod.modul_file_path">
                          <q-banner rounded class="bg-grey-3 q-mb-md">
                            <template v-slot:avatar>
                              <q-icon
                                :name="getFileIcon(mod.modul_extension)"
                                :color="getIconColor(mod.modul_extension)"
                                size="lg"
                              />
                            </template>
                            <div>{{ mod.modul_file_name }}</div>
                          </q-banner>
                        </a>
                        <q-banner v-else rounded class="bg-grey-3 q-mb-md">
                          <template v-slot:avatar>
                            <q-icon name="error" color="red" />
                          </template>
                          Modul tidak ditemukan
                        </q-banner>

                        <!-- ✅ JUDUL DAN DESKRIPSI MODUL -->
                        <div class="q-mb-md">
                          <div class="text-h6">{{ mod.judul }}</div>
                          <div class="text-caption text-grey-7">{{ mod.des }}</div>
                        </div>

                        <!-- ✅ LINK TAMBAHAN (JIKA ADA) -->
                        <q-card-section v-if="mod.link_tambahan" class="q-pa-none q-mb-md">
                          <q-item clickable tag="a" :href="mod.link_tambahan" target="_blank">
                            <q-item-section avatar>
                              <q-icon name="open_in_new" color="primary" />
                            </q-item-section>
                            <q-item-section>
                              <q-item-label class="text-primary">🔗 Link Tambahan</q-item-label>
                              <q-item-label caption lines="2">{{ mod.link_tambahan }}</q-item-label>
                            </q-item-section>
                          </q-item>
                        </q-card-section>

                        <q-separator class="q-my-md"/>
                    </div>
                  </q-card-section>
                </q-card>
              </q-expansion-item>
              </div>
              <q-expansion-item
              class="shadow-1 overflow-hidden"
              style="border-radius: 10px"
              icon="o_assignment"
              label="Penugasan"
              header-class="bg-green-3 text-dark"
              expand-icon-class="text-white"
            >
              <q-card>
                <q-card-section>
                  <div v-for="tgs in tugas" :key="tgs.id">
                    <p>
                        {{tgs.jt}}
                        <br/>
                        <span class="text-caption">{{tgs.soal}}</span>
                    </p>
                    <q-separator class="q-mb-sm"/>
                    <data-tugas :tugas_id="tgs.id"/>
                  </div>
                </q-card-section>
              </q-card>
            </q-expansion-item>
            </q-card-section>
        </q-card>
    </div>
  </q-page>
</template>

<script>
import axios from 'axios';
import { ref } from '@vue/reactivity';
import DataAbsen from '@/components/DataAbsen.vue'
import DataTugas from '@/components/DataTugas.vue'
import { mapState } from 'vuex';

export default {
components:{
DataAbsen,
DataTugas,
},
props:["class_id"],
setup(){
    return{
        absens:ref([]),
        moduls:ref([]),
        tugas:ref([]),
    }
},
computed:{
...mapState("kontrol",["url"])
},
methods:{
    async getAbsen(){
        await axios.get("absenSiswa/"+this.class_id).then((response)=>{
            this.absens=response.data.data
        })
    },
    async getMateri(){
        await axios.get("modulAjar/"+this.class_id).then((response)=>{
            this.moduls=response.data
        })
    },
    async getTugas(){
        await axios.get("tugasSiswa/"+this.class_id).then((response)=>{
            this.tugas=response.data
        })
    },
    // ✅ Helper function untuk download URL modul
    getDownloadUrl(filePath) {
        if (!filePath) return '#';
        // Jika file_path sudah full URL dari storage
        if (filePath.startsWith('http')) {
            return filePath;
        }
        // Jika relative path, tambahkan base URL dari localStorage atau default
        // Gunakan same-origin storage URL untuk development & production
        return `http://127.0.0.1:8000/storage/${filePath}`;
    },
    // ✅ Helper function untuk get file icon
    getFileIcon(extension) {
        if (!extension) return 'help';
        const ext = extension.toLowerCase();
        if (ext === 'pdf') return 'picture_as_pdf';
        if (ext === 'ppt' || ext === 'pptx') return 'slideshow';
        if (ext === 'doc' || ext === 'docx') return 'description';
        if (ext === 'xls' || ext === 'xlsx') return 'table_chart';
        return 'file_present';
    },
    // ✅ Helper function untuk get icon color
    getIconColor(extension) {
        if (!extension) return 'grey';
        const ext = extension.toLowerCase();
        if (ext === 'pdf') return 'red';
        if (ext === 'ppt' || ext === 'pptx') return 'orange';
        if (ext === 'doc' || ext === 'docx') return 'blue';
        if (ext === 'xls' || ext === 'xlsx') return 'green';
        return 'grey';
    }
},
created(){
this.getAbsen()
this.getMateri()
this.getTugas()
}
}
</script>
<style scoped>
a {
    color: unset !important;
    text-decoration: none !important;
  }
</style>

