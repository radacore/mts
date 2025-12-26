<template>
<q-page class="q-pa-sm">
  <div class="row">
    <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
      <q-card style="width:100p%;height:410px" class="q-mx-md q-my-sm">
        <q-card-section class="gradasi text-white" style="min-height:180px">
          <div class="text-h6">Informasi Alat dan Bahan</div>
          <div class="text-subtitle2">Laboratorium IPA</div>
        </q-card-section>
        <q-card-section>
          <q-card class="bg-white absolute-center bayangan" style="width:90%; height:150px;border-radius:10px;margin-top:-20px">
            <div class="row justify-between">
              <q-card-section>
                <span class="text-h5 text-primary">{{total_inv}}</span> 
                <p class="text-caption text-grey">Alat & Bahan Praktikum</p> 
              </q-card-section>
              <q-card-section>
                <span class="text-h5 text-green-7">{{kondisi}}</span> 
                <p class="text-caption text-grey">Kondisi Baik</p> 
              </q-card-section>
            </div>
              <q-linear-progress rounded size="5px" :value="value" color="deep-orange" style="width:90%" class="q-mx-md" />
            <q-card-actions>
              <q-btn flat round icon="fas fa-chart-bar" color="green" />
              <q-btn flat text-color="deep-orange">
                {{Math.round((kondisi/total_inv)*100)}} %
              </q-btn>
             
            </q-card-actions>
          </q-card>
          <q-card class="bg-white absolute-center bayangan" style="width:90%; height:100px; margin-top:120px;border-radius:10px">
            <q-card-section>
              waiting.....
            </q-card-section>
          </q-card>
        </q-card-section>
      </q-card>
    </div>
    <div class="col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8">
      <q-card style=" min-height:400px" class="q-mx-md q-my-sm bayang">
        <q-card-section>
          <jadwal-lab/>
        </q-card-section>
      </q-card>
    </div>
    <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
      
    </div>
     <div class="col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8">
      <q-card style=" min-height:400px" class="q-mx-md q-my-sm bayang">
        <q-card-section>
          <jadwal-lain/>
        </q-card-section>
      </q-card>
    </div>
  </div>
</q-page>
</template>

<script>
import { ref } from '@vue/reactivity'
import axios from 'axios';
import JadwalLab from '@/components/JadwalLab.vue';
import JadwalLain from '@/components/JadwalLain.vue';


export default {
  name: 'HomeView',
  components: {
    JadwalLab,
    JadwalLain
  },
  setup(){
    return{
      total_inv:ref(0),
      kondisi:ref(0)
    }
  },
  computed:{
    value: function(){
      return this.kondisi/this.total_inv
    }
  },
  methods:{
    async getStat(){
      await axios.get("statistik").then((response)=>{
        this.total_inv=response.data.inv 
        this.kondisi=response.data.kondisi
      })
    }
  },
  created(){
    this.getStat()
  }
}
</script>
<style lang="sass">
.q-card .bayangan
  box-shadow: 0 10px 30px rgba(146, 153, 184, 0.15) !important

.gradasi
  background: linear-gradient(to right, #355924, #73f261)
</style>
