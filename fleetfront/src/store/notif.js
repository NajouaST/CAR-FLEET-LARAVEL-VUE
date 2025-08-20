import { defineStore } from "pinia";
import axiosInstance from "@/lib/axios";
import router from "@/router";

export const useNotifStore = defineStore('authNotif', {
    state: () =>{
        return {
            count : 0,
            notifs: [],
        }
    },
    actions: {
        async getNotif() {
            if (localStorage.getItem('token')) {
                try {
                    const res = await axiosInstance.get('/notifications', {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem('token')}`
                        }
                    });
                    this.notifs = res.data;
                } catch (error) {
                    this.notifs = [];
                    this.goLogin(error)
                }
            }
        },

        async getNotifCount() {
            if (localStorage.getItem('token')) {
                try {
                    const res = await axiosInstance.get('/notifications/count', {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem('token')}`
                        }
                    });

                    this.count = res.data.unread_count;
                } catch (error) {
                    this.count = 0;
                    this.goLogin(error)
                }
            }
        },



        goLogin(error) {
            if (error.response?.data?.message === 'Unauthenticated.') {
                localStorage.removeItem('token');
                router.push("/login");
            }
        },
    },
})
