<template>
    <q-btn round icon="o_content_copy" color="primary" unelevated size="sm" @click="copyData"/>
</template>

<script>
import axios from 'axios'

export default {
props:["alat_id"],
setup(){
    return{

    }
},
watch:{
    alat_id(){
        this.copyData()
    }
},
methods:{
    async copyData(){
        await axios.get("pinjamAlat/copy/"+this.alat_id).then((response)=>{
            this.$store.commit('kontrol/SET_TRIGER')
            return response
        })
    }
},
created(){

}
}
</script>

