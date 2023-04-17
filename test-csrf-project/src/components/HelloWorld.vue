<template>
    <div class="hello">
        <h1>{{ msg }}</h1>
        <p>
            该网站用于测试目标网站是否含有CSRF，采用半自动化进行发送请求.
        </p>
        <h3>发送HTTP请求</h3>
        <div>
            <label>URL：</label>
            <input v-model="url" type="text" class="url-input"/>
        </div>
        <div>
            <label>请求类型：</label>
            <select v-model="method">
                <option value="POST">POST</option>
                <option value="GET">GET</option>
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
            <label>请求参数： </label>
            <textarea v-model="body" class="param-input"></textarea>
        </div>
        <div>
            <h2>Cookie</h2>
            <textarea v-model="cookie" class="param-input"></textarea>
        </div>
        <button @click="sendRequest">发送</button>

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
                method: "POST",
                body: "",
                cookie: "",
            };
        },
        methods: {
            sendRequest() {
                axios.post('http://127.0.0.1:8082/test/csrf', {
                    data: {
                    url: this.url,
                    body: this.body,
                    cookie: this.cookie,
                }}, {
                    withCredentials: true, headers: {"Content-Type": "application/json"}}
                    )
                    .then(response => {
                        console.log(response.data);
                    })
                    .catch(error => {
                        console.log(error);
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
