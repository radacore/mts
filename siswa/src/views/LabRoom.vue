<template>
  <q-page class="q-pa-sm">
    <div class="row justify-center">
        <q-card class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8">
            <q-card-section>
                <q-banner rounded class="bg-green-1 text-green-10 q-mb-md">
                  <template v-slot:avatar>
                    <q-icon name="o_cast_for_education" color="green-8" />
                  </template>
                  <div class="text-subtitle2 text-weight-bold">{{ kelasInfo.topik || 'Ruang Praktikum' }}</div>
                  <div class="text-caption">
                    Kelas: {{ kelasInfo.kelas || '-' }} | Guru: {{ kelasInfo.guru || '-' }}
                  </div>
                </q-banner>

                <q-list v-if="moduls.length" padding dense>
                  <div class="section-head q-mb-sm">
                    <div class="row items-center justify-between">
                      <div class="row items-center no-wrap">
                        <q-avatar color="green-7" text-color="white" icon="o_menu_book" />
                        <div class="q-ml-sm">
                          <div class="text-subtitle1 text-weight-bold">Modul Ajar</div>
                          <div class="text-caption text-grey-7">Materi pembelajaran untuk kelas {{ kelasInfo.kelas || '-' }}</div>
                        </div>
                      </div>
                      <q-chip dense color="green-1" text-color="green-9" icon="o_inventory_2">{{ moduls.length }} Materi</q-chip>
                    </div>
                  </div>

                  <div v-for="mod in moduls" :key="mod.id" class="q-mb-sm">
                    <q-expansion-item class="section-item" header-class="text-green-9" switch-toggle-side>
                      <template v-slot:header>
                        <q-item-section avatar>
                          <q-avatar icon="o_article" color="green-7" text-color="white" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label class="text-h6">{{ mod.judul }}</q-item-label>
                          <q-item-label caption lines="2">{{ mod.des }}</q-item-label>
                        </q-item-section>
                      </template>

                      <q-card>
                        <q-card-section>
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

                          <q-card-section v-if="mod.link_tambahan" class="q-pa-none">
                            <q-item clickable tag="a" :href="mod.link_tambahan" target="_blank">
                              <q-item-section avatar>
                                <q-icon name="open_in_new" color="primary" />
                              </q-item-section>
                              <q-item-section>
                                <q-item-label class="text-primary">Link Tambahan</q-item-label>
                                <q-item-label caption lines="2">{{ mod.link_tambahan }}</q-item-label>
                              </q-item-section>
                            </q-item>
                          </q-card-section>
                        </q-card-section>
                      </q-card>
                    </q-expansion-item>
                  </div>
                </q-list>

                <q-list v-if="tugas.length" padding class="q-my-sm">
                  <div class="section-head q-mb-sm">
                    <div class="row items-center justify-between">
                      <div class="row items-center no-wrap">
                        <q-avatar color="orange-8" text-color="white" icon="o_assignment" />
                        <div class="q-ml-sm">
                          <div class="text-subtitle1 text-weight-bold">Penugasan</div>
                          <div class="text-caption text-grey-7">Daftar tugas aktif untuk kelas ini</div>
                        </div>
                      </div>
                      <q-chip dense color="orange-1" text-color="orange-9" icon="o_checklist">{{ tugas.length }} Tugas</q-chip>
                    </div>
                  </div>

                  <div v-for="tgs in tugas" :key="tgs.id" class="q-mb-sm">
                    <q-expansion-item class="section-item" header-class="text-green-9" switch-toggle-side>
                      <template v-slot:header>
                        <q-item-section avatar>
                          <q-avatar icon="o_assignment" color="green-7" text-color="white" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label class="text-h6">{{ tgs.jt }}</q-item-label>
                          <q-item-label caption lines="2">{{ tgs.soal }}</q-item-label>
                        </q-item-section>
                      </template>
                      <q-card>
                        <q-card-section>
                          <data-tugas :tugas_id="tgs.id" :tugas_data="tgs" />
                        </q-card-section>
                      </q-card>
                    </q-expansion-item>
                  </div>
                </q-list>

                <q-list v-if="absens.length" padding>
                  <div class="section-head q-mb-sm">
                    <div class="row items-center justify-between">
                      <div class="row items-center no-wrap">
                        <q-avatar color="blue-7" text-color="white" icon="o_how_to_reg" />
                        <div class="q-ml-sm">
                          <div class="text-subtitle1 text-weight-bold">Absensi</div>
                          <div class="text-caption text-grey-7">Sesi kehadiran kelas dan status buka/tutup</div>
                        </div>
                      </div>
                      <q-chip dense color="blue-1" text-color="blue-9" icon="o_event_available">{{ absens.length }} Sesi</q-chip>
                    </div>
                  </div>

                  <div v-for="abs in absens" :key="abs.id" class="q-mb-sm">
                    <q-expansion-item class="section-item" header-class="text-green-9" switch-toggle-side>
                      <template v-slot:header>
                        <q-item-section avatar>
                          <q-avatar icon="o_fact_check" color="green-7" text-color="white" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label class="text-h6">Absensi</q-item-label>
                          <q-item-label caption lines="2">Buka: {{ abs.jam_buka }} | Tutup: {{ abs.jam_tutup }} Wita</q-item-label>
                        </q-item-section>
                        <q-item-section side>
                          <q-chip dense :color="isAbsenClosed(abs.status) ? 'grey-7' : 'green-7'" text-color="white">
                            {{ isAbsenClosed(abs.status) ? 'Close' : 'Open' }}
                          </q-chip>
                        </q-item-section>
                      </template>

                      <q-card>
                        <q-card-section>
                          <div class="text-caption text-grey-8 q-mb-sm">
                            Absensi dibuka pada pukul <strong>{{ abs.jam_buka }}</strong> Wita dan ditutup pada pukul <strong>{{ abs.jam_tutup }}</strong> Wita.
                          </div>
                          <div v-if="serverTime > abs.jam_tutup" class="q-my-sm text-red text-weight-bold row items-center">
                            <q-icon name="warning" size="sm" class="q-mr-sm"/>
                            Waktu absensi telah habis!
                          </div>
                          <data-absen
                            :absen_id="abs.id"
                            :late="serverTime > abs.jam_tutup"
                            :is-closed="isAbsenClosed(abs.status)"
                          />
                        </q-card-section>
                      </q-card>
                    </q-expansion-item>
                  </div>
                </q-list>
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
        serverTime:ref(''),
        kelasInfo:ref({
            topik: '',
            kelas: '',
            guru: ''
        })
    }
},
computed:{
...mapState("kontrol",["url"])
},
methods:{
    async getAbsen(){
        await axios.get("absenSiswa/"+this.class_id).then((response)=>{
            this.absens=response.data.data
            this.serverTime=response.data.jam
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
    async getKelasInfo(){
        await axios.get("classroom/cek/"+this.class_id).then((response)=>{
            const data = response.data || {}
            this.kelasInfo = {
              topik: data.katalog?.topik || '',
              kelas: data.kelas?.kelas || '',
              guru: data.user?.name || data.User?.name || ''
            }
        }).catch(()=>{
            this.kelasInfo = {
              topik: '',
              kelas: '',
              guru: ''
            }
        })
    },
    isAbsenClosed(status){
        if (status === null || status === undefined) return false
        const normalized = String(status).toLowerCase().trim()
        return ['0', 'close', 'closed', 'false'].includes(normalized)
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
        return `${this.url}${filePath}`;
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
 this.getKelasInfo()
 this.getAbsen()
 this.getMateri()
 this.getTugas()
}
}
</script>
<style scoped lang="sass">
a
  color: unset !important
  text-decoration: none !important

.section-head
  background: linear-gradient(180deg, #f4fbf5 0%, #ffffff 100%)
  border: 1px solid #d8e8da
  border-radius: 12px
  padding: 10px 12px

.section-item
  border: 1px solid #dfe9e1
  border-radius: 12px
  background: #ffffff
  overflow: hidden
</style>
