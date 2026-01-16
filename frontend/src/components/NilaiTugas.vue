<template>
  <div>

  </div>
</template>

<script>
import axios from 'axios';

export default {
props:["id","nilai"],
setup(){
    return{

    }
},
watch:{
nilai(){
    this.updateNilai();
}
},
computed:{

},
methods:{
    async updateNilai(){
        await axios.put("dataTugas/nilai/"+this.id+"/"+this.nilai).then((response)=>{
            this.$store.commit('kontrol/SET_TRIGER')
            return response
        })
    }
},
created(){

}
}
</script>

