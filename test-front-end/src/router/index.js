import { createWebHistory, createRouter } from "vue-router"
import Register from "@/views/RegisterPage.vue"
import Login from "@/views/LoginPage.vue"
import MainHub from "@/views/MainHubPage.vue"
import Redirect from "@/components/RedirectMain.vue"

const routes=[
    {
        path:"/",
        name:"redirect",
        component:Redirect,
    },
    {
        path:"/register",
        name: "register",
        component: Register,
    },
    {
        path:"/login",
        name: "login",
        component: Login,
    },
    {
        path : "/mainHub",
        name: "mainHub",
        component : MainHub,
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
  })
  
  export default router