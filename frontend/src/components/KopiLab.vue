<template>
    <q-btn round icon="o_content_copy" color="primary" unelevated size="sm" @click="copyData"/>
</template>

<script>
import axios from 'axios';

export default {
props:["pinjam_id"],
setup(){
    return{

    }
},
watch:{
pinjam_id(){
    this.copyData()
}
},
methods:{
    async copyData(){
        await axios.get("pinjamLab/copy/"+this.pinjam_id).then((response)=>{
            this.$store.commit('kontrol/SET_TRIGER')
            return response
        })
    }
},
created(){

}
}
</script>

