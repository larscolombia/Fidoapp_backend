<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end offcanvas-booking" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <InputField class="col-md-12" type="text" :is-required="true" :label="$t('service.lbl_service_name')" placeholder="" v-model="name" :error-message="errors['name']" :error-messages="errorMessages['name']"></InputField>
        <InputField class="col-md-12" type="text" :is-required="true" :label="$t('service.lbl_duration_min')"  placeholder="" v-model="duration_min" :error-message="errors['duration_min']" :error-messages="errorMessages['duration_min']"></InputField>
        <InputField class="col-md-12" type="text" :is-required="true" :label="`${$t('service.lbl_default_price')} (${CURRENCY_SYMBOL})`"  placeholder="" v-model="default_price" :error-message="errors['default_price']" :error-messages="errorMessages['default_price']"></InputField>


        <div class="form-group">
          <label class="form-label" for="category_id">{{ $t('service.lbl_category') }} <span class="text-danger">*</span> </label>
          <Multiselect v-model="category_id" :value="category_id" v-bind="categories" id="category_id" @select="changeCategory"></Multiselect>
          <span v-if="errorMessages['category_id']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['category_id']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.category_id }}</span>
        </div>

        <div class="form-group" v-if="subCategories.options.length > 0">
          <label class="form-label" for="sub_category_id">{{ $t('service.lbl_sub_category') }} </label>
          <Multiselect v-model="sub_category_id" :value="sub_category_id" v-bind="subCategories" id="sub_category_id"></Multiselect>
          <span v-if="errorMessages['sub_category_id']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['sub_category_id']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.sub_category_id }}</span>
        </div>

        <div class="form-group">
          <div class="d-flex justify-content-between align-items-center">
            <label class="form-label" for="feature_image">{{ $t('service.lbl_feature_image') }} <span v-if="IsnewRecord ==0" class="text-danger">*</span> </label>
            <a href="javascript:void(0)" v-if="feature_image" class="text-danger" @click="removeImage">Remove</a>
          </div>
          <input type="file" class="form-control" id="feature_image" ref="refInput" @change="fileUpload" accept=".jpeg, .jpg, .png, .gif" />
          <span v-if="errorMessages['feature_image']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['feature_image']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.feature_image }}</span>
        </div>

        <div v-for="field in customefield" :key="field.id">
          <FormElement v-model="custom_fields_data" :name="field.name" :label="field.label" :type="field.type" :required="field.required" :options="field.value" :field_id="field.id"></FormElement>
        </div>

        <div class="form-group col-md-12">
              <label class="form-label" for="description">{{$t('service.lbl_description')}}</label>
              <textarea class="form-control" v-model="description" id="description"></textarea>
              <span v-if="errorMessages['description']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['description']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.description }}</span>
            </div>

        <div class="form-group mb-0">
          <div class="d-flex justify-content-between align-items-center">
            <label class="form-label" for="category-status">{{ $t('service.lbl_status') }}</label>
            <div class="form-check form-switch">
              <input class="form-check-input" :value="status" :checked="status" name="status" id="category-status" type="checkbox" v-model="status" />
            </div>
          </div>
        </div>
      </div>
      <FormFooter></FormFooter>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL, CATEGORY_LIST } from '../constant/service'
import { useField, useForm } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'

import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import { buildMultiSelectObject } from '@/helpers/utilities'
import { readFile } from '@/helpers/utilities'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import FormElement from '@/helpers/custom-field/FormElement.vue'

// props
defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  customefield: { type: Array, default: () => [] }
})
const CURRENCY_SYMBOL = ref(window.defaultCurrencySymbol)
const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()
const IsnewRecord=ref(0)
// Edit Form Or Create Form
const currentId = useModuleId(() => {
  IsnewRecord.value=0
  subCategories.value.options = []
  if (currentId.value > 0) {

    IsnewRecord.value=1

    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status) {
        setFormData(res.data)
        // changeCategory(category_id.value)
        // if (res.data.sub_category_id.value != 0) {
        //   sub_category_id.value = res.data.sub_category_id
        // }
      }
    })
  } else {
    setFormData(defaultData())
  }
})

// File Upload Function
const ImageViewer = ref(null)
const refInput = ref(null)
const fileUpload = async (e) => {
  let file = e.target.files[0]
  await readFile(file, (fileB64) => {
    ImageViewer.value = fileB64
  })
  feature_image.value = file
}

// Function to delete Images
const removeImage = () => {
  ImageViewer.value = null
  feature_image.value = null
  refInput.value = ''
  document.getElementById('feature_image').value = ''
}

/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    name: '',
    description: '',
    duration_min: '',
    default_price: '',
    status: 1,
    category_id: '',
    sub_category_id: '',
    feature_image: null,
    custom_fields_data: {}
  }
}

//  Reset Form
const setFormData = (data) => {
  ImageViewer.value = data.feature_image
  resetForm({
    values: {
      name: data.name,
      description: data.description,
      duration_min: data.duration_min,
      default_price: data.default_price,
      status: data.status,
      category_id: data.category_id,
      sub_category_id: data.sub_category_id,
      feature_image: data.feature_image,
      custom_fields_data: data.custom_field_data
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
    IsnewRecord.value=0
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

const numberRegex = /^\d+$/
// Validations
const validationSchema = yup.object({

  feature_image: yup
  .string()
  .test('feature_image', 'Este campo es obligatorio.', function (value) {
    if (currentId === 0 && !value) {
      return false;
    }
    return true;
  })
  .label('Service Image'),
  name: yup.string().required('Este campo es obligatorio.'),
  duration_min: yup.string().required('Este campo es obligatorio.').matches(/^[1-9]\d*$/, 'Sólo se permiten números mayores que 0'),
  default_price: yup.string().required('Este campo es obligatorio.').matches(/^[1-9]\d*$/, 'Sólo se permiten números mayores que 0'),
  category_id: yup.string().required('Este campo es obligatorio.').matches(/^\d+$/, 'Sólo se permiten números'),
   description:yup.string().nullable().test('no-script-tags', 'El campo no puede contener etiquetas de script.', function(value) {
    const scriptTagRegex = /<script\b[^>]*>(.*?)/is;
    return !scriptTagRegex.test(value);
  }),
})


const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: name } = useField('name')
const { value: description } = useField('description')
const { value: duration_min } = useField('duration_min')
const { value: default_price } = useField('default_price')
const { value: status } = useField('status')
const { value: category_id } = useField('category_id')
const { value: sub_category_id } = useField('sub_category_id')
const { value: feature_image } = useField('feature_image')
const { value: custom_fields_data } = useField('custom_fields_data')

const errorMessages = ref({})

const categories = ref({
  searchable: true,
  options: []
})

const subCategories = ref({
  searchable: true,
  options: []
})

const getCategoryList = () => {
  listingRequest({ url: CATEGORY_LIST, data: { id: 0 } }).then((res) => (categories.value.options = buildMultiSelectObject(res, { value: 'id', label: 'name' })))
}

const changeCategory = (value) => {
  sub_category_id.value = null
  listingRequest({ url: CATEGORY_LIST, data: { id: value } }).then((res) => (subCategories.value.options = buildMultiSelectObject(res, { value: 'id', label: 'name' })))
}

onMounted(() => {
  getCategoryList()
  setFormData(defaultData())
})

// Form Submit
const formSubmit = handleSubmit(async (values) => {
  const saveButton = document.getElementById('save-button');
  saveButton.disabled = true; 

  values.custom_fields_data = JSON.stringify(values.custom_fields_data);

  try {
    if (currentId.value > 0) {
      await updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
    } else {
      await storeRequest({ url: STORE_URL, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
      document.getElementById('feature_image').value = '';
    }
  } catch (error) {
    console.error('Error:', error);
  } finally {
    saveButton.disabled = false;  
  }
});

useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))
</script>
