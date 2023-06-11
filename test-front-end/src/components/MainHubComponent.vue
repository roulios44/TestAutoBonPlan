<template>
    <hearderComp />
    <div class="headInfo">
        <div class="welcomeMessage">
            <p>Bonjour ! Nous sommes <strong>{{ days[new Date().getDay()] }}</strong></p><br>
            <p>Voici ce qui a changé depuis {{ days[new Date().getDay()] }} dernier</p>
        </div>
        <div class="stats">
            <div class="callReceive">
                <p>TOTAL APPELS REÇUS</p> <br>
                <h1>200</h1>
            </div>
            <div class="callTake">
                <p>TOTAL PRISE EN CHARGE</p>
                <h1>56%</h1>
            </div>
        </div>
        <div class="uploadFile">
            <input type="file" name="csvFile" accept=".csv" v-on:change="takeFile" placeholder="">
            <p style="color: red">{{ errorMessage }}</p>
            <button v-if="dataJSON!=null" v-on:click="sendDataCSV()">Send CSV</button>
        </div>
    </div>
    <div>

        <label>Comparer des semaines ? (par défaut ce sont des jours qui seront comparés)</label><br>
        <input type="radio" v-bind:value="true" v-model="weeklyCompare" />
        <label for="one">Oui</label>
        <input type="radio" v-bind:value="false" v-model="weeklyCompare" />
        <label for="two">Non</label><br>
        <label v-if="weeklyCompare">A noter: les jours choisis correspondent aux 1er jours de la semaine à étudier</label><br v-if="weeklyCompare">
        <label for="firstDate">Choisisez la 1ere date à comparer</label>
        <input type="date" v-model="firstDate" name="firstDate" v-on:change="checkIfDateAvalaible(firstDate,true)"><br>
        <p v-if="!firstDateIn">Cette date na pas encore d'entrée dans la base de donnée</p>
        <label for="secondDate">Choisisez la 2eme date à comparer</label>
        <input type="date" v-model="secondDate" name="secondDate" v-on:change="checkIfDateAvalaible(secondDate,false)"><br>
        <p v-if="!secondDateIn">Cette date na pas encore d'entrée dans la base de donnée</p>
        <button v-if="firstDateIn && secondDateIn && firstDate && secondDate" v-on:click="getData">Confirm</button>
        <div class="chart">
            <GChart
                v-if="dataCall"
                type="ColumnChart"
                :data="chartData"
                :options="chartOptions"
            />
        </div>
        <div>
            <GChart
                v-if="callTakenChartData"
                type="BarChart"
                :data="callTakenChartData"
                :options="callTakenChartOptions"
            />
        </div>
    </div>
</template>
<script>
import axios from 'axios';
import Papa from 'papaparse';
import hearderComp from "@/components/HeaderComponent.vue"
import { GChart } from 'vue-google-charts'



export default{
    data(){
        return{
            file: null,
            dataJSON:null,
            errorMessage:"",
            days:["Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"],
            today : "",
            allDates:null,
            firstDate:null,
            secondDate:null,
            firstDateIn:true,
            secondDateIn:true,
            dataCall:null,

            weeklyCompare:false,
            chartData: null,
            chartOptions: {
                title: "Nombre d'ppels Reçus par concession",
            },

            callTakenChartData: null,
            callTakenChartOptions : {
                    
                    title: "Appels prit par concession (En %)",
                    'width':800,
                    'height':1000
            }
      


        }
    },
    components:{
        hearderComp,
        GChart,
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
            }
        });
        },
        async sendDataCSV(){
            if(this.dataJSON!=null){
                const data = {"arrayJSON":this.dataJSON}
                const req = await axios.post("http://localhost/calls",data)
                const res = await req.data
                console.log(res)
            }else{
                this.errorMessage = "Please choose a file before adding it"
            }
        },
        async getAllDates(){
            const req = await axios.get("http://localhost/date")
            const res = await req.data
            res.forEach(async(item,index) => {
                res[index] = await this.formatDate(new Date(item.date))
            });
            this.allDates = res
        },
        async formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
        async checkIfDateAvalaible(date,isFirst){
            if(this.allDates.includes(date)){
                if(this.weeklyCompare){
                    const dateParts = date.split("-");
                    const dateEnd = new Date(dateParts[0], dateParts[1]-1, dateParts[2])
                    dateEnd.setDate(dateEnd.getDate()+7)
                    if(this.allDates.includes(await this.formatDate(dateEnd))){
                        if(isFirst)this.firstDateIn = true
                        else this.secondDateIn = true
                    }
                }
                else if(isFirst)this.firstDateIn = true
                else this.secondDateIn = true
            }else{
                if(isFirst)this.firstDateIn = false
                else this.secondDateIn = false
            }
        },
        async getData(){
            const req = await axios.get("http://localhost/calls/day/" + this.firstDate + "/" + this.secondDate);
            console.log("http://localhost/calls/day/" + this.firstDate + "/" + this.secondDate)
            const res = await req.data
            this.dataCall = await res
            await this.callReceiveChart();
            
        },
        async callReceiveChart(){
            let graphData = []
            let callTaken = []
            this.dataCall[0].forEach(call => {
                if(callTaken.find(item=>item.label===call.salePoint)){
                    const index = callTaken.findIndex(item=>item.label===call.salePoint)
                    if(call.duration==="00:00:00")callTaken[index].firstDateNoTake++
                    else callTaken[index].firstDateTake++
                }else{
                    if(call.duration==="00:00:00"){
                        callTaken.push({
                        label:call.salePoint,
                        "firstDateNoTake":1,
                        "firstDateTake":0,
                    })
                    }else{
                        callTaken.push({
                        label:call.salePoint,
                        "firstDateNoTake":0,
                        "firstDateTake":1,
                    })
                    }
                }
                if(graphData.find(item=>item.label===call.salePoint)){
                    graphData[graphData.findIndex(item=>item.label===call.salePoint)].firstDateCall++
                }
                else{
                    graphData.push(
                        {
                            "label": call.salePoint,
                            "firstDate": this.firstDate,
                            "firstDateCall": 1,
                            "secondDateCall":0,
                        })
                }
            });
            this.dataCall[1].forEach(call => {
                if(callTaken.find(item=>item.label===call.salePoint)){
                    const index = callTaken.findIndex(item=>item.label===call.salePoint)
                    if(call.duration==="00:00:00")callTaken[index].secondDateNoTake++
                    else callTaken[index].secondDateTake++
                }else{
                    if(call.duration==="00:00:00"){
                        callTaken.push({
                        label:call.salePoint,
                        "secondDateNoTake":1,
                        "secondDateTake":0,
                    })
                    }else{
                        callTaken.push({
                        label:call.salePoint,
                        "secondDateNoTake":0,
                        "secondDateTake":1,
                    })
                    }
                }
                if(graphData.find(item=>item.label===call.salePoint)){
                    graphData[graphData.findIndex(item=>item.label===call.salePoint)].secondDateCall++
                }
                else{
                    graphData.push(
                        {
                            "label": call.salePoint,
                            "secondDate": this.secondDate,
                            "secondDateCall": 1
                        })
                }
            });
            await this.makeGraph(graphData,callTaken)
        },
        async makeGraph(graphData,callTaken){
            graphData.forEach(call => {
                if(call.secondDate==null)call.secondDate = this.secondDate
                if(call.secondDateCall==null)call.secondDateCall = 0;
                if(call.firstDate==null)call.firstDate = this.firstDate
                if(call.firstDateCall==null)call.firstDateCall = 0
            });

            callTaken.forEach(call=>{
                if(!call.secondDateTake)call.secondDateTake = 0
                if(!call.secondDateNoTake)call.secondDateNoTake = 0;
                if(!call.firstDateNoTake)call.firstDateNoTake = 0
                if(!call.firstDateTake)call.firstDateTake = 0
            })
            this.chartData = []
            this.chartData.push(["Concession",this.firstDate,this.secondDate])
            graphData.forEach(call => {
                this.chartData.push([call.label,call.firstDateCall,call.secondDateCall])
            });
            this.callTakenChartData = []
            this.callTakenChartData.push(["Concession",this.firstDate,this.secondDate])
            callTaken.forEach(call=>{
                const firstDatePercent = call.firstDateTake / (call.firstDateNoTake+call.firstDateTake)
                const secondDatePercent = call.secondDateTake / (call.secondDateNoTake+call.secondDateTake)
                this.callTakenChartData.push([call.label,Math.round(firstDatePercent*100),Math.round(secondDatePercent*100)])
            })
        }
    },
    async mounted(){
        const date = new Date()
        this.today = date.getDay() + "/" + date.getMonth() + "/" + date.getFullYear()
        await this.getAllDates()
    }
}
</script>
<style>
.headInfo{
    align-content: center;
    justify-content: flex-start;
    width: 100%;
    height: 100%;
    padding: 0;
    margin-top: 2%;

    display: flex;
	flex-direction: row;
	flex-wrap: wrap;
	justify-content: flex-start;
	align-items: stretch;
	align-content: stretch;

}
.welcomeMessage{
    height: 100%;
    width: 25%;
    display: flex;
	flex-direction: row;
	flex-wrap: wrap;
	justify-content: flex-start;
	align-items: stretch;
	align-content: space-between;
}
.stats{
    width: 40%;
    height: 100%;
    display: flex;
	flex-direction: row;
	flex-wrap: wrap;
	justify-content: flex-start;
	align-items: stretch;
	align-content: space-between;
}
.callReceive{
    height: 100%;

    display: flex;
    margin-left: 5%;
    margin-right: 5%;
}.callTake{
    height: 100%;

    display: flex;
    margin-left: 5%;
    margin-right: 5%;
}
.uploadFile{
    display: flex;
	flex-direction: row;
	flex-wrap: wrap;
	justify-content: flex-start;
	align-items: stretch;
	align-content: space-between;
    height: 100%;
    width: 20%;
}

</style>