<template>
  <div class="text-center q-my-lg q-pt-lg">
    <span class="text-h6">Fasilitator</span>
  </div>
 <div class="row q-pa-md">
   <div v-for="row in datas" :key="row.id" class="col-12 col-sm-3 col-md-4 col-lg-4 q-my-sm">
    <q-list bordered padding class="shadow-2 rounded-borders q-mx-sm">
        <q-item>
            <q-item-section top avatar>
              <q-avatar>
                <img :src="url+row.foto_profile.foto">
              </q-avatar>
            </q-item-section>
    
            <q-item-section>
              <q-item-label>{{row.name}}</q-item-label>
              <q-item-label caption>{{row.bioguru.nip}}</q-item-label>
            </q-item-section>
          </q-item>
    </q-list>
   </div>
 </div>
</template>

<script>
import { ref } from '@vue/reactivity'
import axios from 'axios';
import { mapState } from 'vuex';
export default {
name:'MyGuru',
setup(){
  return{
    datas:ref([])
  }
},
computed:{
...mapState("kontrol",["url"])
},
methods:{
  async getGuru(){
    await axios.get("gurus").then((response)=>{
      this.datas=response.data
    })
  }
},
created(){
  this.getGuru()
}
}
</script>

