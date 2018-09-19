--
-- proxy mulit
-- User: Bevan
-- Date: 2018/9/18
-- Time: 16:22
-- To change this template use File | Settings | File Templates.
--

--加载 json 库
local json = require "cjson";
--获取请求方式
local request_method = ngx.var.request_method;
--判断请求方式
if request_method == "GET" then
    ngx.say(json.encode({"only post"}));
    return;
end;

--读取 post 参数.表单需要是 x-www-form-urlencoded
ngx.req.read_body();
local api_p = ngx.req.get_post_args();
--拼接子请求
local list = {};
for api,p in pairs(api_p) do
    local tmp = {api,{args=p,method=ngx.HTTP_GET}};
    table.insert(list, tmp);
end;
--发送子请求
local response = {ngx.location.capture_multi(list)};
--合并响应
local data = {};
for num,resp in pairs(response) do
    resp = json.decode(resp["body"]);
    data[resp["uri"]] = resp;
end;
--响应到客户端
ngx.say(json.encode(data));

