<template>
    <div class="hello">
        <h1>{{ msg }}</h1>
        <p>
            该网站用于测试目标网站是否含有CSRF，采用半自动化进行发送请求
        </p>
        <h3>发送HTTP请求</h3>
        <div>
            <label>URL：</label>
            <input v-model="url" type="text" class="url-input"/>
        </div>
        <div>
            <label>请求类型：</label>
            <select v-model="method">
                <option value="GET">GET</option>
                <option value="POST">POST</option>
                <option value="PUT">PUT</option>
                <option value="DELETE">DELETE</option>
            </select>
        </div>
        <p>参数填写格式参考:</p>
        <textarea class="param-input-such" readonly>
            {
                    "name": "John",
                    "age": 30,
                    "isStudent": true,
                    "hobbies": ["reading", "swimming", "hiking"],
                    "address": {
                    "street": "123 Main St",
                    "city": "Anytown",
                    "state": "CA",
                    "zip": "12345"
                    }
            }
        </textarea>
        <div>
            <label>请求参数：</label>
            <textarea v-model="params" class="param-input"></textarea>
        </div>

        <button @click="sendRequest">发送</button>
        <div>
            <h2>响应结果</h2>
            <pre>{{ response }}</pre>
        </div>
    </div>
</template>

<script>
    import axios from "axios";

    export default {
        name: 'HelloWorld',
        props: {
            msg: String
        },
        data() {
            return {
                url: "",
                method: "GET",
                params: "",
                response: "",
            };
        },
        methods: {
            sendRequest() {
                const headers = {};
                headers['Origin'] = this.url;
                if (this.method === 'post' || this.method === 'put') {
                    headers['Content-Type'] = 'application/json';
                    this.params = JSON.parse(this.params);
                }
                // 根据选择的请求类型和请求参数发送HTTP请求
                // 将响应结果存储到 response 属性中
                // 这里使用 axios 库来发送请求
                axios({
                    method: this.method,
                    url: this.url,
                    data: this.params,
                    headers: headers,
                })
                    .then((response) => {
                        this.response = JSON.stringify(response.data, null, 2);
                    })
                    .catch((error) => {
                        this.response = JSON.stringify(error.response.data, null, 2);
                    });
            },
        },
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
    h3 {
        margin: 40px 0 0;
    }

    ul {
        list-style-type: none;
        padding: 0;
    }

    li {
        display: inline-block;
        margin: 0 10px;
    }

    a {
        color: #42b983;
    }

    form {
        display: flex;
        flex-direction: column;
        width: 400px;
        margin: 0 auto;
    }

    label {
        display: flex;
        flex-direction: column;
        margin-bottom: 10px;
    }

    input,
    textarea {
        padding: 5px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    select {
        padding: 5px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
        appearance: none;
        -webkit-appearance: none;
        background: url("data:image/svg+xml,%3Csvg fill='%23666' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z'/%3E%3Cpath d='M0 0h24v24H0z' fill='none'/%3E%3C/svg%3E") no-repeat;
        background-position: calc(100% - 10px) center;
        background-size: 16px;
    }

    button {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        font-size: 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: #3e8e41;
    }

    /* 设置 JSON 对象和数组的外观和样式 */
    .json-block {
        background-color: #f4f4f4;
        font-family: Consolas, monospace;
        font-size: 1rem;
        line-height: 1.5;
        border: 1px solid #ddd;
        overflow: auto;
    }

    .url-input {
        width: 1000px;
        height: 40px;
        margin: 10px 0;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f5f5f5;
        font-size: 16px;
        color: #333;
    }

    .param-input {
        width: 50%;
        height: 400px;
        margin: 10px 0;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f5f5f5;
        font-size: 16px;
        color: #333;
        resize: vertical;
    }
    .param-input-such {
        width: 50%;
        height: 300px;
        margin: 10px 0;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #ccc;
        font-size: 16px;
        color: #333;
        resize: vertical;
    }
    pre {
        background-color: #f4f4f4;
        padding: 1rem;
        margin: 1rem 0;
        font-family: Consolas, monospace;
        font-size: 1rem;
        line-height: 1.5;
        border: 1px solid #ddd;
        overflow: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
</style>
