<template>
    <div style="display: flex; justify-content: center; align-items: center;">
        <input type="file" name="csvFile" accept=".csv" v-on:change="takeFile">
    </div>
</template>
<script>
// import axios from 'axios';
import Papa from 'papaparse';


export default{
    data(){
        return{
            file: null,
            dataJSON:null,
        }
    },
    methods:{
        async takeFile(event){
            this.file = event.target.files[0];
            this.parseFile();
        },
        parseFile() {
            Papa.parse(this.file, {
            complete: (result) => {
                const donneesCSV = result.data;
                const hearders = donneesCSV[0];
                const lines = donneesCSV.slice(1);
                this.dataJSON = lines.map((line) => {
                    const obj = {};
                    hearders.forEach((header, index) => {
                    obj[header] = line[index];
                });
                return obj;
            });
            console.log(this.dataJSON)
        }
      });
    },
    }
}
</script>
<style>

</style>