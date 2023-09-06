<script setup>
import axios from "axios";
import { onMounted, ref, watch } from "vue";
import { Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../../toastr.js';
import UserListItem from './UserListItem.vue'
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';


const users = ref({data:[]});
const editing = ref(false);
const formValues = ref();
const form = ref(null);
const toastr = useToastr();

const getUsers = (page = 1) => {
    axios.get(`/api/users?page=${page}`).then((response) => {
        users.value = response.data;
        selectedUsers.value = [];
        selectAll.value = false;
    });
};

const createUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(8),
})

const editUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().when((password, schema) => {
        return password ? schema.required().min(8) : schema;
    }),
})

const createUser = (values, {setErrors}) => {
    axios.post('/api/users', values)
    .then((response) => {
        // users.value.push(response.data);
        // if (response.status === true) {
             users.value.data.unshift(response.data.data);
            $('#userFormModal').modal('hide');
            form.value.resetForm();
            toastr.success('User created successfully!');
        // }else{
        //     toastr.success(response.message);
        // }
       
    }).catch((error) => {
        console.log(error);
        setErrors(error.response.data.errors);
        console.log(error);
    });
}

const addUser = () => {
    editing.value = false;
    $('#userFormModal').modal('show');
}

const editUser = (user) => {
    editing.value = true;
    form.value.resetForm();
    $('#userFormModal').modal('show');
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email,
    }
}

const updateUser = (values, { setErrors }) => {
    axios.put('/api/users/' + formValues.value.id, values)
        .then((response) => {
            // if (response.status === true) {
                const index = users.value.data.findIndex(user => user.id === response.data.data.id);
                users.value.data[index] = response.data.data;
                $('#userFormModal').modal('hide');
                toastr.success('User updated successfully!');
            // }else{
            //     toastr.success(response.message);
            // }
        }).catch((error) => {
            setErrors(error.response.data.errors);
            console.log(error);
        });
}

const handleSubmit = (values, actions) => {
    if(editing.value){
        updateUser(values, actions);
    }else{
        createUser(values, actions);
    }
}

const userDeleted = (userId) => {
    users.value.data = users.value.data.filter(user => user.id !== userId);
}

const bulkDelete = () => {
    axios.delete('/api/users', {
        data: {
            ids: selectedUsers.value
        }
    })
    .then(response => {
        users.value.data = users.value.data.filter(user => !selectedUsers.value.includes(user.id));
        selectedUsers.value = [];
        selectAll.value = false;
        toastr.success(response.data.message);
    });
};

const selectAll = ref(false);
const selectAllUsers = () => {
    if (selectAll.value) {
        selectedUsers.value = users.value.data.map(user => user.id);
    } else {
        selectedUsers.value = [];
    }
    console.log(selectedUsers.value);
}

const searchQuery = ref(null);

const search = () =>{
    axios.get('/api/users', {
        params: {
            search: searchQuery.value
        }
    }).then( response =>{
        users.value = response.data.data
    }).catch(error => {
        console.log(error);
    })
}

watch(searchQuery, debounce(() =>{
    search();
}, 300));

const selectedUsers = ref([]);

const toggleSelection = (user) => {
    const index = selectedUsers.value.indexOf(user.id);
    if (index === -1) {
        selectedUsers.value.push(user.id);
    } else {
        selectedUsers.value.splice(index, 1);
    }
    console.log(selectedUsers.value);
}

onMounted(() => {
    getUsers();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">User</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">User</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
             <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <button @click="addUser" type="button" class="mb-2 btn btn-primary">
                        <i class="fa fa-plus-circle mr-1"></i>
                        Add New User
                    </button>
                    <div v-if="selectedUsers.length > 0">
                        <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Delete Selected
                        </button>
                        <span class="ml-2">Selected {{ selectedUsers.length }} users</span>
                    </div>
                </div>
                <div>
                    <input type="text" v-model="searchQuery" class="form-control" placeholder="Search..." />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" v-model="selectAll" @change="selectAllUsers" /></th>
                                        <th style="width: 10px">#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody v-if="users.data.length > 0">
                                    <UserListItem  v-for="(user, index) in users.data" 
                                    :key="user.id"
                                    :user=user
                                    :index=index
                                    @edit-user="editUser"
                                    @user-deleted="userDeleted"
                                    @toggle-selection="toggleSelection"
                                     :select-all="selectAll"
                                    />
                                </tbody>
                                <tbody v-else>
                                    <tr><td colspan="6" class="text-center">No result</td></tr>
                                </tbody>
                            </table>

                            
                        </div>
                    </div>
                    <Bootstrap4Pagination :data="users" @pagination-change-page="getUsers" />
                  
                </div>
            </div>
        </div>
    </div>
   <!-- Modal -->
    <div class="modal fade" id="userFormModal" data-backdrop="static" tabindex="-1" role="dialog"
            aria-labelledby="userFormModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userFormModalLabel">
                        <span v-if="!editing">Add New User</span>
                        <span v-else>Edit User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form ref="form" 
                @submit="handleSubmit" 
                :validation-schema="editing ? editUserSchema : createUserSchema"
                v-slot="{ errors }" 
                :initial-values="formValues">
                    <div class="modal-body">
                    
                        <div class="form-group">
                            <label for="name">Name</label>
                            <Field name="name" type="text" class="form-control" :class="{'is-invalid': errors.name}" id="name"
                                aria-describedby="nameHelp" placeholder="Enter full name" />
                            <span class="invalid-feedback">{{ errors.name }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <Field name="email" type="email" class="form-control" :class="{'is-invalid': errors.email}"  id="email"
                                aria-describedby="nameHelp" placeholder="Enter full name" />
                            <span class="invalid-feedback">{{ errors.email }}</span>
                        </div>
                    

                        <div class="form-group">
                            <label for="password">Password</label>
                            <Field name="password" type="password" class="form-control" :class="{'is-invalid': errors.password}"  id="password"
                                aria-describedby="nameHelp" placeholder="Enter password" />
                                
                            <span class="invalid-feedback">{{ errors.password }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>
    
</template>
