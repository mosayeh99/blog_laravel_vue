<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Users</a></li>
                        <li class="breadcrumb-item active">List Users</li>
                    </ol>
                </div>
            </div>
            <div class="row justify-content-between mt-4">
                <div class="col-sm-6">
                    <a href="#" @click="createUser" type="button" class="btn btn-primary" data-toggle="modal" data-target="#userFormModal">
                        Add User
                    </a>
                </div>
                <div class="col-sm-3">
                    <input class="form-control" placeholder="Search by email..." @input="filterUsers($event.target.value)">
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <UsersList @edit-user="editUser" @user-deleted="userDeleted" :users="users"/>
        </div>
        <Bootstrap4Pagination :data="users" @pagination-change-page="getUsers"/>
    </div>

    <!-- Modal | Store & Update User -->
    <div class="modal fade" id="userFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span v-if="isEditing">Update User</span>
                        <span v-else>Create New User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form @submit="handleSubmit" :validation-schema="isEditing ? updateUserSchema : storeUserSchema" v-slot="{ errors }" :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-form-label">Name</label>
                            <Field name="name" type="text" class="form-control" :class="{'is-invalid' : errors.name}" />
                            <span class="text-danger" v-if="errors.name">{{ errors.name }}</span>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Email</label>
                            <Field name="email" type="email" class="form-control" :class="{'is-invalid' : errors.email}" />
                            <span class="text-danger" v-if="errors.email">{{ errors.email }}</span>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Password</label>
                            <Field name="password" type="password" class="form-control" :class="{'is-invalid' : errors.password}" />
                            <span class="text-danger" v-if="errors.password">{{ errors.password }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>
</template>

<script setup>
import {Form, Field} from "vee-validate";
import {onMounted, ref} from "vue";
import * as yup from 'yup';
import UsersList from "@/pages/user/UsersList.vue";
import { Bootstrap4Pagination } from 'laravel-vue-pagination';

// --------------------------Setup------------------------
const users = ref({data: []});
const isEditing = ref(false);
const formValues = ref(null);

// --------------Api Connection Methods-------------------
const getUsers = (page = 1) => {
    axios.get(`/api/users?page=${page}`)
        .then(res => users.value = res.data)
        .catch(err => console.log(err.data))
};

const storeUser = (values, { resetForm, setErrors }) => {
    axios.post('/api/users', values)
        .then(res => {
            users.value.unshift(res.data);
            $('#userFormModal').modal('hide');
            toastr.success('User Created Successfully');
            resetForm();
        })
        .catch(err => setErrors(err.response.data.errors));
};

const updateUser = (values, { setErrors }) => {
    axios.put(`/api/users/${formValues.value.id}`, values)
        .then(res => {
            const index = users.value.findIndex(user => user.id === res.data.id);
            users.value[index] = res.data;
            $('#userFormModal').modal('hide');
            toastr.success('User Updated Successfully');
        })
        .catch(err => setErrors(err.response.data.errors));
};

// --------------Methods-------------------
const handleSubmit = (values, actions) => {
    if(isEditing.value) {
        updateUser(values, actions);
    } else {
        storeUser(values, actions);
    }
}

const createUser = () => {
    isEditing.value = false;
    formValues.value = {};
}

const editUser = (user) => {
    isEditing.value = true;
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email
    }
}

const userDeleted = (userId) => {
    users.value = users.value.filter(user => user.id !== userId);
}

const filterUsers = (searchValue) => {
    let trs = document.querySelectorAll('table tbody tr');
    trs.forEach(tr => {
        if (tr.children[2].textContent.toLowerCase().indexOf(searchValue.toLowerCase()) > -1) {
            tr.style.display = ""
        } else {
            tr.style.display = "none"
        }
    })
}

onMounted(() => {
    getUsers();
});
// --------------Validation Schema-------------------
const storeUserSchema = yup.object({
    email: yup.string().required().email(),
    name: yup.string().required(),
    password: yup.string().required().min(8),
});

const updateUserSchema = yup.object({
    email: yup.string().required().email(),
    name: yup.string().required(),
    password: yup.string().when((password, schema) => {
        return password ? schema.min(8) : schema;
    }),
})

</script>

<style scoped>

</style>
