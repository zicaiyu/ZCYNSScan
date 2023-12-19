<template>
  <div>
    <h1>API Response:</h1>
    <p>{{ apiResponse }}</p>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      apiResponse: '',
    };
  },
  mounted() {
    this.getAPIResponse();
  },
  methods: {
    getAPIResponse() {
      const proxyUrl = 'http://192.168.1.101:3000/api'; // 代理服务器的URL

      axios
        .get(proxyUrl)
        .then(response => {
          this.apiResponse = response.data;
          console.log(response.data); // 可选：在控制台打印返回的值
        })
        .catch(error => {
          console.error(error);
        });
    },
  },
};
</script>