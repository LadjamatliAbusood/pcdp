<script setup>
import { ref, watch, defineProps, defineEmits } from 'vue';
import Select from 'primevue/select';
import currencyData from '@/Data/currency.json';
import axios from 'axios';

const props = defineProps({
  modelValue: String,
  amount: {
    type: Number,
    required: true
  },
  toCurrency: {
    type: String,
    default: 'PHP'
  }
});

const emit = defineEmits([
  'update:modelValue',
  'converted'
]);

const selectedCurrency = ref(props.modelValue);
const exchangeRate = ref(0);

// Populate options
const currencyOptions = Object.keys(currencyData).map(code => ({
  code,
  name: currencyData[code].name
}));

// Fetch rate from Laravel backend once when component mounts
const fetchRate = async () => {
  try {
    const res = await axios.get('/currency/rate'); // Laravel endpoint
    exchangeRate.value = res.data.rate || 0;
    convert(selectedCurrency.value);
  } catch (error) {
    console.error('Failed to fetch exchange rate', error);
  }
};

const convert = (from) => {
  if (!from || !props.amount || !exchangeRate.value) return;

  // Simple local conversion using stored rate
  const result = (props.amount * exchangeRate.value).toFixed(2);

  emit('converted', result);
};

// Watch for changes
watch(selectedCurrency, (val) => {
  emit('update:modelValue', val);
  convert(val);
});

watch(() => props.amount, () => {
  convert(selectedCurrency.value);
});

// Fetch rate once on mount
fetchRate();

</script>

<template>
  <Select
    v-model="selectedCurrency"
    :options="currencyOptions"
    option-label="code"
    option-value="code"
    class="w-full"
  />
</template>
