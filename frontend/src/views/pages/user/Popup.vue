<template>
    <b-sidebar
        id="add-new-user-sidebar"
        :visible="isPopupActive"
        bg-variant="white"
        sidebar-class="sidebar-lg"
        shadow
        backdrop
        no-header
        right
        @hidden="resetForm"
        @change="(val) => { $emit('update:is-popup-active', val); }"
    >
        <template #default="{ hide }">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
                <h5 class="mb-0">
                    {{ text }} User
                </h5>

                <feather-icon
                    class="ml-1 cursor-pointer"
                    icon="XIcon"
                    size="16"
                    @click="hide"
                />

            </div>

            <!-- BODY -->
            <validation-observer
                #default="{ handleSubmit }"
                ref="refFormObserver"
            >
                <!-- Form -->
                <b-form
                    class="p-2"
                    @submit.prevent="handleSubmit(onsubmit)"
                    @reset.prevent="resetForm"
                >

                    <!-- Full Name -->
                    <validation-provider
                        #default="validationContext"
                        name="Name"
                        rules="required"
                    >
                        <b-form-group
                            label="Name"
                            label-for="full-name"
                        >
                            <b-form-input
                                id="full-name"
                                v-model="userData.name"
                                autofocus
                                :state="getValidationState(validationContext)"
                                trim
                                placeholder="John Doe"
                            />

                            <b-form-invalid-feedback>
                                {{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Username -->
                    <validation-provider
                        #default="validationContext"
                        name="Username"
                        rules="required|alpha-num"
                    >
                        <b-form-group
                            label="Username"
                            label-for="username"
                        >
                            <b-form-input
                                id="username"
                                v-model="userData.username"
                                :state="getValidationState(validationContext)"
                                trim
                            />

                            <b-form-invalid-feedback>
                                {{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Email -->
                    <validation-provider
                        #default="validationContext"
                        name="Email"
                        rules="required|email"
                    >
                        <b-form-group
                            label="Email"
                            label-for="email"
                        >
                            <b-form-input
                                id="email"
                                v-model="userData.email"
                                :state="getValidationState(validationContext)"
                                trim
                            />

                            <b-form-invalid-feedback>
                                {{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- User Role -->
                    <validation-provider
                        #default="validationContext"
                        name="User Role"
                        rules="required"
                    >
                        <b-form-group
                            label="User Role"
                            label-for="user-role"
                            :state="getValidationState(validationContext)"
                        >
                            <v-select
                                v-model="userData.roleId"
                                :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                                :options="roleOptions"
                                :reduce="val => val.value"
                                :clearable="false"
                                input-id="user-role"
                            />
                            <b-form-invalid-feedback :state="getValidationState(validationContext)">
                                {{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- status -->
                    <validation-provider
                        #default="validationContext"
                        name="Status"
                        rules="required"
                    >
                        <b-form-group
                            label="Status"
                            label-for="status"
                            :state="getValidationState(validationContext)"
                        >
                            <v-select
                                v-model="userData.status"
                                :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                                :options="statusOptions"
                                :reduce="val => val.value"
                                :clearable="false"
                                input-id="status"
                            />
                            <b-form-invalid-feedback :state="getValidationState(validationContext)">
                                {{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Form Actions -->
                    <div class="d-flex mt-2">
                        <b-button
                            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                            variant="primary"
                            class="mr-2"
                            type="submit"
                        >
                            {{ text }}
                        </b-button>
                        <b-button
                            v-ripple.400="'rgba(186, 191, 199, 0.15)'"
                            type="button"
                            variant="outline-secondary"
                            @click="hide"
                        >
                            Cancel
                        </b-button>
                    </div>

                </b-form>
            </validation-observer>
        </template>
    </b-sidebar>
</template>

<script>
import {
    BSidebar, BForm, BFormGroup, BFormInput, BFormInvalidFeedback, BButton,
} from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { ref } from '@vue/composition-api'
import { required, alphaNum, email } from '@validations'
import formValidation from '@core/comp-functions/forms/form-validation'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import callApi from '@/axios'

export default {
    name: 'Popup-User',
    components: {
        BSidebar,
        BForm,
        BFormGroup,
        BFormInput,
        BFormInvalidFeedback, BButton,
        vSelect,
        // Form Validation
        ValidationProvider,
        ValidationObserver,
    },
    directives: {
        Ripple,
    },
    props: {
        isPopupActive: {
            type: Boolean,
            required: true
        },
        roleOptions: {
            type: Array,
            default: [],
        },
        statusOptions: {
            type: Array,
            required: true,
        },
        user: {
            type: Object,
            required: true
        },
        isUpdate: {
            type: Boolean,
            required: true
        }
    },
    data() {
        return {
            required,
            email,
            alphaNum
        }
    },
    setup(props, { emit }) {
        let blankUserData = props.user
        let isUpdate = props.isUpdate
        const userData = ref(JSON.parse(JSON.stringify(blankUserData)))
        const resetUserData = () => {
            userData.value = JSON.parse(JSON.stringify(blankUserData))
        }
        let uri = 'manage/users/store'
        let method = 'post'
        if (isUpdate) {
            const userId = blankUserData.id
            uri = `manage/users/${ userId }/update`
            method = 'put'
        }
        const onsubmit = () => {
            callApi.request({
                method: method,
                url: uri,
                data: {
                    username: userData.value.username,
                    email: userData.value.email,
                    status: userData.value.status,
                    role_id: userData.value.roleId,
                    name: userData.value.name,
                }
            }).then((response) => {
                response.isUpdate = isUpdate
                // emit('update:is-update', false)
                emit('update:is-popup-active', false)
                emit('update-data', response)
            }).catch((error) => {})
        }
        const {
            refFormObserver,
            getValidationState,
            resetForm,
        } = formValidation(resetUserData)
        const text = isUpdate ? 'Update' : 'Add'
        return {
            refFormObserver,
            getValidationState,
            resetForm,
            userData,
            onsubmit,
            text
        }
    },
}
</script>

<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';

    #add-new-user-sidebar {
        .vs__dropdown-menu {
            max-height: 200px !important;
        }
    }
</style>
