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
              <div class="q-my-sm">
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
                        <q-banner rounded class="bg-white">
                            <template v-slot:avatar>
                                <a :href="url+mod.file" target="_blank">
                                <img
                                v-if="mod.file.split('.').pop()=='pdf'"
                                src="../assets/pdf2.png"
                                style="width: 60px; height: 60px"
                              />
                              <img
                                v-else
                                src="../assets/ppt.jpg"
                                style="width: 60px; height: 50px"
                              />
                              </a>
                            </template>
                            <p>{{mod.judul}}<br/>
                                <span class="text-caption">{{mod.des}}</span>
                            </p>
                          </q-banner>
                        <q-separator/>
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

