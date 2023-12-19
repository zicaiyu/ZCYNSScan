const express = require('express');
const { createProxyMiddleware } = require('http-proxy-middleware');

const app = express();

app.use('/', createProxyMiddleware({
  target: 'https://up.daojia.com',
  changeOrigin: true,

  onProxyReq(proxyReq, req, res) {
    console.log('Request Method:', req.method);
    console.log('Request URL:', req.url);
    console.log('Request Headers:', req.headers);
    console.log('Request Body:', req.body);
  },
}));

app.listen(3000, () => {
  console.log('Proxy server is running on port 3000');
});