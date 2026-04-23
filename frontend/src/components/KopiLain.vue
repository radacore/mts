<template>
  <q-btn round icon="o_content_copy" color="primary" unelevated size="sm" @click="copyData"/>
</template>

<script>
import axios from 'axios'

export default {
  props:["lain_id"],
  methods:{
    async copyData(){
      await axios.get("pinjamLain/copy/" + this.lain_id).then((response)=>{
        this.$store.commit('kontrol/SET_TRIGER')
        return response
      })
    }
  }
}
</script>
