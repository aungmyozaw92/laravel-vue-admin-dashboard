<script setup>

import axios from 'axios';
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToastr } from '../../../toastr.js';
import { Form } from 'vee-validate';
import flatpickr from "flatpickr";
import 'flatpickr/dist/themes/light.css';


const router = useRouter();
const route = useRoute();
const toastr = useToastr();

const form = reactive({
    title: '',
    client: '',
    start_time: '',
    end_time: '',
    description: '',
});

const clients = ref();

const getClients = () => {
    axios.get('/api/clients')
    .then((response)=>  {
        clients.value =  response.data.data;
    })
}

const handleSubmit = (values, actions) => {
    if (editMode.value) {
        editAppointment(values, actions);
    }else{
        createAppointment(values, actions);
    }
};

const createAppointment = (values, actions) => {
    axios.post('/api/appointments', form)
    .then((response) => {
        router.push('/admin/appointments');
        toastr.success('Appointment created successfuly');
    }).catch((error) => {
        console.log('er', error.response.data)
        actions.setErrors(error.response.data.errors);
        console.log(error);
    })
}

const editAppointment = (values, actions) => {
     axios.put(`/api/appointments/${route.params.id}`, form)
    .then((response) => {
        router.push('/admin/appointments');
        toastr.success('Appointment updated successfuly');
    }).catch((error) => {
        actions.setErrors(error.response.data.errors);
        console.log(error);
    })
}

const getAppointment = () => {
    axios.get(`/api/appointments/${route.params.id}`)
    .then(({data}) => {
        form.title = data.data.title,
        form.client_id = data.data.client.id,
        form.start_time = data.data.start_time,
        form.end_time = data.data.end_time,
        form.description = data.data.description
    })
};

const editMode = ref(false);

onMounted(() => {
    if (route.name === 'admin.appointments.edit') {
        editMode.value = true;
        getAppointment();
    }
    getClients();
    flatpickr(".flatpickr", {
        enableTime: true,
        dateFormat: "Y-m-d h:i K",
        defaultHour: 10
    });
})

</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <span v-if="editMode">Edit</span> 
                        <span v-else>Create</span> 
                        Appointment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard">Home</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/admin/appointments">Appointments</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span v-if="editMode">Edit</span> 
                            <span v-else>Create</span> 
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <Form @submit="handleSubmit" v-slot:default="{ errors }">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input v-model="form.title" type="text" class="form-control" :class="{ 'is-invalid': errors.title }" id="title" placeholder="Enter Title">
                                             <span class="invalid-feedback">{{ errors.title }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="client">Client Name</label>
                                            <select v-model="form.client_id" id="client" class="form-control" :class="{ 'is-invalid': errors.client_id }">
                                                <option disabled>Please select one</option>
                                                <option v-for="client in clients" :value="client.id" :key="client.id">{{ client.first_name }} {{ client.last_name }}</option>
                                            </select>
                                             <span class="invalid-feedback">{{ errors.client_id }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date">Start Time</label>
                                            <input v-model="form.start_time" type="text" class="form-control flatpickr" :class="{ 'is-invalid': errors.start_time }" id="date">
                                             <span class="invalid-feedback">{{ errors.start_time }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="time">End Time</label>
                                            <input v-model="form.end_time" type="text" class="form-control flatpickr" :class="{ 'is-invalid': errors.end_time }" id="time">
                                             <span class="invalid-feedback">{{ errors.end_time }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea v-model="form.description" class="form-control" :class="{ 'is-invalid': errors.description }" id="description" rows="3"
                                        placeholder="Enter Description"></textarea>
                                         <span class="invalid-feedback">{{ errors.description }}</span>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>