<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end offcanvas-booking" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
    
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL,LISTING_URL } from '../constant/earning'
import { useField, useForm } from 'vee-validate'

import { useModuleId, useRequest,useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'

import { buildMultiSelectObject } from '@/helpers/utilities'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'

// props
const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' }
})

const singleSelectOption = ref({
  searchable: true,
  createOption: true,
  clearable: false

})
  const formatCurrencyVue = window.currencyFormat

  const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

  const payment_method_data = ref([])

  const type = 'earning_payment_method'

listingRequest({ url: LISTING_URL, data: {type: type} }).then((res) => {
  payment_method_data.value.options = buildMultiSelectObject(res.results, {
    value: 'id',
    label: 'text'
  })
})

// Edit Form Or Create Form
const currentId = useModuleId(() => {
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status) {
        setFormData(res.data)
      }
    })
  } else {
    setFormData(defaultData())
  }
})

// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    description: '',
    amount: '',
    payment_method: ''
  }
}

const setFormData = (data) => {
  resetForm({
    values: {
      id: data.id,
      full_name: data.full_name,
      email: data.email,
      mobile: data.mobile,
      profile_image: data.profile_image,
      description: '',
      commission_earn: data.commission_earn,
      tip_earn: data.tip_earn,
      amount: data.amount,
      payment_method: data.payment_method
    }
  })
}



// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const reset_datatable_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    renderedDataTable.ajax.reload(null, false)
    bootstrap.Offcanvas.getInstance('#form-offcanvas').hide()
    setFormData(defaultData())
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

// Validations
const validationSchema = yup.object({
  payment_method: yup.string().required("Payment method is required field"),
  amount: yup.string().required(),
  description: yup.string().required("Description is required field")
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const fieldMappings = {
  id: useField('id'),
  full_name: useField('full_name'),
  email: useField('email'),
  mobile: useField('mobile'),
  profile_image: useField('profile_image'),
  description: useField('description'),
  commission_earn: useField('commission_earn'),
  tip_earn: useField('tip_earn'),
  amount: useField('amount'),
  payment_method: useField('payment_method')
};

// Access the values using destructuring
const { value: id } = fieldMappings.id;
const { value: full_name } = fieldMappings.full_name;
const { value: email } = fieldMappings.email;
const { value: mobile } = fieldMappings.mobile;
const { value: profile_image } = fieldMappings.profile_image;
const { value: description } = fieldMappings.description;
const { value: commission_earn } = fieldMappings.commission_earn;
const { value: tip_earn } = fieldMappings.tip_earn;
const { value: amount } = fieldMappings.amount;
const { value: payment_method } = fieldMappings.payment_method;

const errorMessages = ref({})

// Form Submit
const formSubmit = handleSubmit((values) => {
  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  }
})
useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))
</script>
<style>
.form__input {
  font-family: 'Roboto', sans-serif;
  color: #333;
  font-size: 1.2rem;
	margin: 0 auto;
  padding: 1.5rem 2rem;
  border-radius: 0.2rem;
  background-color: rgb(255, 255, 255);
  border: none;
  width: 90%;
  display: block;
  border-bottom: 0.3rem solid transparent;
  transition: all 0.3s;
}
</style>
