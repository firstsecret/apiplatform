--
-- proxy mulit
-- User: Bevan
-- Date: 2018/9/18
-- Time: 16:22
-- To change this template use File | Settings | File Templates.
--

--加载 json 库
local json = require "cjson";
local tool = require "resty.tool"
--获取请求方式
local request_method = ngx.var.request_method;

-- gzip handle
ngx.req.set_header('Accept-Encoding', 'default')

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

--拼接子请求

-- response hander handle

-- send requests

--ngx.print(json.encode(api_p))
local list = {};
for api, p in pairs(api_p) do
    local tmp = {}
--    ngx.print(type(json.decode(p)))
    local p_table = {}
    if(p ~= '') then
        p_table = json.decode(p)
    end
--
    local ngx_http_flag = ngx.HTTP_GET
    if p_table['method'] then
        ngx_http_flag = tool.getCpatureMethod(p_table['method'])
    end

    if p_table then
        tmp = { '/internal' .. api, { args = p_table['args'], method = ngx_http_flag, body = p_table['body'] or "" } };
    else
        tmp = { '/internal' .. api };
    end
    table.insert(list, tmp);
end;

local response = { ngx.location.capture_multi(list) };
--
tool.respClient(200, 'success', response)




