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
                    {{ text }} Tool
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

                    <!-- Icon -->
                    <validation-provider
                        #default="validationContext"
                        name="icon"
                    >
                        <b-form-group
                            label="Icon"
                            label-for="icon"
                            :state="getValidationState(validationContext)"
                        >
                            <div class="row">
                                <div class="col-sm-2 d-flex justify-content-center align-items-center">
                                    <feather-icon :icon="data.icon" size="24"></feather-icon>
                                </div>
                                <div class="col-sm-10">
                                    <v-select
                                        v-model="data.icon"
                                        :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                                        :options="iconList"
                                        :reduce="val => val.value"
                                        :clearable="false"
                                        input-id="icon"
                                    />

                                </div>
                            </div>
                            <b-form-invalid-feedback :state="getValidationState(validationContext)">
                                {{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Name -->
                    <validation-provider
                        #default="validationContext"
                        name="Name"
                        rules="required"
                    >
                        <b-form-group
                            label="Name"
                            label-for="name"
                        >
                            <b-form-input
                                id="name"
                                v-model="data.name"
                                :state="getValidationState(validationContext)"
                                trim
                            />

                            <b-form-invalid-feedback>
                                {{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <!-- Uri -->
                    <validation-provider
                        #default="validationContext"
                        name="Uri"
                    >
                        <b-form-group
                            label="Uri"
                            label-for="uri"
                        >
                            <b-form-input
                                id="uri"
                                v-model="data.uri"
                                :state="getValidationState(validationContext)"
                                trim
                            />

                            <b-form-invalid-feedback>
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
    BDropdown, BDropdownDivider, BDropdownItem
} from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { ref } from '@vue/composition-api'
import { required } from '@validations'
import formValidation from '@core/comp-functions/forms/form-validation'
import Ripple from 'vue-ripple-directive'
import vSelect from 'vue-select'
import callApi from "@/axios";
import * as icons from 'vue-feather-icons'

export default {
    name: 'Popup-Tool',
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
        BDropdown,
        BDropdownDivider,
        BDropdownItem,
    },
    directives: {
        Ripple,
    },
    props: {
        isPopupActive: {
            type: Boolean,
            required: true
        },
        tool: {
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
            iconInput: '',
            valueSearch: '',
            icons: [],
            textDropdown: "<feather-icon icon='AirplayIcon'/> AirplayIcon"
        }
    },
    created() {
        this.icons = Object.keys(icons)
    },
    methods: {
        selectIcon(item) {
            this.iconInput = item
            // this.$root.$emit('selectIcon', {icon: item, index: this.index})
        }
    },
    computed: {
        iconList() {
            const data = []
            this.icons.forEach((icon) => {
                data.push({ value: icon, label: icon })
            })
            return data
        },
    },
    setup(props, { emit }) {
        let blankData = props.tool
        let isUpdate = props.isUpdate
        const data = ref(JSON.parse(JSON.stringify(blankData)))
        const resetUserData = () => {
            data.value = JSON.parse(JSON.stringify(blankData))
        }
        let uri = 'manage/tools/store'
        let method = 'post'
        if (isUpdate) {
            const id = blankData.id
            uri = `manage/tools/${ id }/update`
            method = 'put'
        }
        const onsubmit = () => {
            callApi.request({
                method: method,
                url: uri,
                data: {
                    icon: data.value.icon,
                    uri: data.value.uri,
                    parent_id: blankData.parentId,
                    name: data.value.name,
                    client_id: data.value.clientId
                }
            }).then((response) => {
                response.isUpdate = isUpdate
                emit('update:is-popup-active', false)
                emit('update-data-tool', response)
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
            data,
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
