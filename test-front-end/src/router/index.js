import { createWebHistory, createRouter } from "vue-router"
import Register from "@/views/RegisterPage.vue"
import Login from "@/views/LoginPage.vue"

const routes=[
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
]

const router = createRouter({
    history: createWebHistory(),
    routes
  })
  
  export default router