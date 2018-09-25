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

-- gzip handle
ngx.req.set_header('Accept-Encoding', 'default')

-- response header
ngx.header['Server'] = 'xiaoyumi'
ngx.header['Content-Type'] = 'Application/json'

--判断请求方式
if request_method == "GET" then
    local respData = {}
    respData['status_code'] = 4050
    respData['msg'] = '只支持post请求'
    ngx.say(json.encode(respData));
    return;
end;

--读取 post 参数.表单需要是 x-www-form-urlencoded
ngx.req.read_body();
local api_p = ngx.req.get_post_args();
--ngx.print(ngx.HTTP_GET)
--拼接子请求
local list = {};
for api, p in pairs(api_p) do
    local tmp = {}
    local api_p = json.decode(p)
    if type(api_p) ~= 'table' then
        tool.respClient(4053, '参数不正确')
    end

    if api_p then
        tmp = { '/internal' .. api, { args = api_p['args'] } };
    else
        tmp = { '/internal' .. api };
    end
    --    ngx.say(tmp)
    table.insert(list, tmp);
end;
--ngx.say(list)

--发送子请求
local response = { ngx.location.capture_multi(list) };
--合并响应
local data = {};
for num, resp in pairs(response) do
    --    ngx.say(num)
    --    ngx.print(json.encode(resp))
    local header = resp['header']
    --      ngx.print(json.encode(resp["body"]))
    --    resp = json.decode(resp);
    --    data[num] = resp
    --    ngx.print(json.encode(header))
    data[header['RequestUri']] = resp['body'];
end;
--响应到客户端
ngx.say(json.encode(data));


