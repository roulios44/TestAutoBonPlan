<template>
    <hearderComp />
    <div class="headInfo">
        <div class="welcomeMessage">
            <p>Bonjour ! Nous sommes <strong>{{ days[new Date().getDay()] }}</strong></p><br>
        </div>
        <div class="stats">
            <div class="callReceive" v-if="weeklyCompare">
                <p>TOTAL APPELS REÇUS</p> <br>
                <h1>{{totalCallReceive}} ({{ evolTotalCall}})</h1>
            </div>
            <div class="callTake" v-if="weeklyCompare">
                <p>TOTAL PRISE EN CHARGE</p>
                <h1>{{percentCallTake}}% ({{ evolCallTake }}%)</h1>
            </div>
        </div>
        <div class="uploadFile" v-if="canAdd" >
            <input type="file" name="csvFile" accept=".csv" v-on:change="takeFile" placeholder="">
            <p style="color: red">{{ errorMessage }}</p>
            <button v-if="dataJSON!=null" v-on:click="sendDataCSV()">Send CSV</button>
        </div>
    </div>
    <div class="choseDates">

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
        
    </div>
    <div>
            <GChart
                v-if="dataCall"
                type="ColumnChart"
                :data="chartData"
                :options="chartOptions"
            />
            <GChart
                v-if="callTakenChartData"
                type="BarChart"
                :data="callTakenChartData"
                :options="callTakenChartOptions"
            />
        </div>
        <div>
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
            canAdd:false,
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
            totalCallReceive:0,
            evolTotalCall:0,
            percentCallTake:0,
            evolCallTake:0,
            weeklyCompare:false,
            chartData: null,
            chartOptions: {
                title: "Nombre d'appels reçus par concession",
                'width':"max",
                'height':500,
                series:{
                    0:{color: "#0f97d1"},
                }
            },

            callTakenChartData: null,
            callTakenChartOptions : {
                title: "Appels prit par concession (En %)",
                'width':"max",
                'height':1000,

                series:{
                0:{color: "#0f97d1"},
                }
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
        // The above code is defining an asynchronous function called `sendDataCSV()`. This function
        // first checks if `this.dataJSON` is not null. If it is not null, it creates an object called
        // `data` with a property called `arrayJSON` that is set to the value of `this.dataJSON`. It
        // then sends a POST request to the URL "http://localhost/calls" with the `data` object as the
        // request body using the Axios library. The response from the server is stored in the `res`
        // variable. If `this.dataJSON` is null, it sets an
        async sendDataCSV(){
            if(this.dataJSON!=null){
                const data = {"arrayJSON":this.dataJSON}
                const req = await axios.post("http://localhost/calls",data)
                const res = await req.data
                res
            }else{
                this.errorMessage = "Please choose a file before adding it"
            }
            await this.getAllDates();
        },
        // The above code is defining an asynchronous method called `getAllDates()` in a Vue component.
        // This method sends a GET request to the "http://localhost/date" endpoint using Axios and
        // awaits the response. It then loops through the response data using the `forEach()` method
        // and formats each date using another asynchronous method called `formatDate()`. The formatted
        // dates are then stored in the `allDates` data property of the Vue component.
        async getAllDates(){
            const req = await axios.get("http://localhost/date")
            const res = await req.data
            res.forEach(async(item,index) => {
                res[index] = await this.formatDate(new Date(item.date))
            });
            this.allDates = res
        },
        // The above code defines an asynchronous function called `formatDate` that takes in a `date`
        // parameter. Inside the function, it extracts the year, month, and day from the `date` object
        // and formats them as a string in the format "YYYY-MM-DD". The `padStart` method is used to
        // ensure that the month and day values are always two digits long, with leading zeros added if
        // necessary. The function then returns the formatted date string.
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
        // The above code is defining an asynchronous method called `getData()` in a Vue component.
        async getData(){
            let stringAPi = "http://localhost/calls"
            if(this.weeklyCompare) stringAPi += "/week/"
            else stringAPi += "/day/"
            stringAPi += this.firstDate + "/" + this.secondDate
            const req = await axios.get(stringAPi);
            const res = await req.data
            // Here , dataCall is a array of 2 slice, each slice contains all calls info from 1st or 2nds date
            this.dataCall = await res
            await this.callReceiveChart(); 
        },
        async callReceiveChart(){
            let graphData = []
            let callTaken = []
            let firstDateCallTaken = 0;
            let secondDateCallTaken = 0;
            this.totalCallReceive = this.dataCall[1].length 
            this.evolTotalCall = this.dataCall[1].length - this.dataCall[0].length 
            this.dataCall[0].forEach(call => {
                if(callTaken.find(item=>item.label===call.salePoint)){
                    const index = callTaken.findIndex(item=>item.label===call.salePoint)
                    if(call.duration==="00:00:00")callTaken[index].firstDateNoTake++
                    else{
                        callTaken[index].firstDateTake++
                        firstDateCallTaken++
                    } 
                }else{
                    callTaken.push({
                        label:call.salePoint,
                        "firstDateNoTake":0,
                        "firstDateTake":0,
                        "seconDateNoTake":0,
                        "secondDateTake":0,
                    })
                    if(call.duration==="00:00:00")callTaken[callTaken.length-1].firstDateNoTake++
                    else{
                        callTaken[callTaken.length-1].firstDateTake++
                        firstDateCallTaken++
                    } 
                }
                if(graphData.find(item=>item.label===call.salePoint))graphData[graphData.findIndex(item=>item.label===call.salePoint)].firstDateCall++
                else{
                    graphData.push(
                        {
                            "label": call.salePoint,
                            "firstDate": this.firstDate,
                            "firstDateCall": 1,
                            "secondDateCall":0,
                            "secondDate": this.secondDate,
                        })
                }
            });
            this.dataCall[1].forEach(call => {
                if(callTaken.find(item=>item.label===call.salePoint)){
                    const index = callTaken.findIndex(item=>item.label===call.salePoint)
                    if(call.duration==="00:00:00")callTaken[index].secondDateNoTake++
                    else{
                       callTaken[index].secondDateTake++ 
                       secondDateCallTaken++
                    } 
                }else{
                    callTaken.push({
                        label:call.salePoint,
                        "firstDateNoTake":0,
                        "firstDateTake":0,
                        "seconDateNoTake":0,
                        "secondDateTake":0,
                    })
                    if(call.duration==="00:00:00")callTaken[callTaken.length-1].seconDateNoTake++
                    else{
                        callTaken[callTaken.length-1].secondDateTake++
                        secondDateCallTaken++
                    } 
                }
                if(graphData.find(item=>item.label===call.salePoint)){
                    graphData[graphData.findIndex(item=>item.label===call.salePoint)].secondDateCall++
                }
                else{
                    graphData.push(
                        {
                            "label": call.salePoint,
                            "firstDate": this.firstDate,
                            "secondDate": this.secondDate,
                            "firstDateCall": 0,
                            "secondDateCall":1,
                        })
                }
            });
            this.percentCallTake = Math.round((secondDateCallTaken/this.totalCallReceive) *100)
            this.evolCallTake = this.percentCallTake - Math.round((firstDateCallTaken/this.dataCall[0].length)*100)
            await this.makeGraph(graphData,callTaken)
        },
        async makeGraph(graphData,callTaken){
            // Here we verify if  every part of each array of object have all required fields
            graphData.forEach(call => {
                if(call.secondDate==null)call.secondDate = this.secondDate
                if(call.firstDate==null)call.firstDate = this.firstDate
                if(call.secondDateCall==null)call.secondDateCall = 0;
                if(call.firstDateCall==null)call.firstDateCall = 0
            });
            callTaken.forEach(call=>{
                if(!call.secondDateTake)call.secondDateTake = 0
                if(!call.secondDateNoTake)call.secondDateNoTake = 0;
                if(!call.firstDateNoTake)call.firstDateNoTake = 0
                if(!call.firstDateTake)call.firstDateTake = 0
            })
            this.chartData = []
            // Here we push into the array of data of chart to create a chart
            this.chartData.push(["Concession",this.firstDate,this.secondDate])
            graphData.forEach(call => {
                this.chartData.push([call.label,call.firstDateCall,call.secondDateCall])
            });
            this.callTakenChartData = []
            this.callTakenChartData.push(["Concession",this.firstDate,this.secondDate])
            callTaken.forEach(call=>{
                    const firstDatePercent = Math.round(call.firstDateTake / (call.firstDateNoTake+call.firstDateTake)*100)
                    const secondDatePercent = Math.round(call.secondDateTake / (call.secondDateNoTake+call.secondDateTake)*100)
                    this.callTakenChartData.push([call.label,firstDatePercent,secondDatePercent])
            })
        }
    },
    async mounted(){
        if(!localStorage.getItem("userID")) this.$router.push("/login")
        if(localStorage.getItem("canAdd")==true)this.canAdd = true
        else this.canAdd = false
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

.choseDates{
    display: flex;
	flex-direction: column;
    max-width: 15%;
    margin: 2%;
}

</style>