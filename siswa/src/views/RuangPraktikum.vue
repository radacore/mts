<template>
  <q-page class="q-pa-sm">
    <div class="row justify-center">
        <q-card class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8">
            <q-card-section>
                <div class="row justify-start">
                    <div v-for="row in classroom" :key="row.id" class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-3">
                        <q-card class="bayangan">
                            <q-card-section class="bg-primary text-white">
                                <div class="text-h6">{{row.katalog.topik}}</div>
                                <div class="text-subtitle2">{{row.kelas.kelas}}</div>
                              </q-card-section>
                              <q-separator/>
                            <q-card-section>
                             <span class="text-caption">By {{row.user.name}}</span>
                            </q-card-section>
                            <q-card-actions align="right">
                                <q-btn flat color="primary" :to="{ name: 'labroom', params: { class_id: row.id }}">Masuk Lab</q-btn>
                              </q-card-actions>
                          </q-card>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    </div>
  </q-page>
</template>

<script>
import { ref } from '@vue/reactivity'
import axios from 'axios';
export default {
setup(){
    return{
        classroom:ref([]),
    }
},
methods:{
    async getClassroom(){
        await axios.get("labsiswa").then((response)=>{
            this.classroom=response.data
        })
    }
},
created(){
this.getClassroom();
}
}
</script>
<style lang="sass">
.q-card .bayangan
  box-shadow: 0 10px 30px rgba(146, 153, 184, 0.15) !important

.gradasi
  background: linear-gradient(to right, #355924, #73f261)
</style>

