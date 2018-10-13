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
local zhttp = require("resty.http")
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


-- init redis
-- redis
local redis = tool.getRedis()
local httpc = zhttp.new()
--拼接子请求

-- is dev env
local dev_module = redis:get('apiplatform_service_dev')
local request_base_uri = redis:get('apiplatform_service_base_uri')
local request_port = 80
--ngx.print(request_base_uri)
local request_msg = tool.split(request_base_uri, ':')
--
for k, v in pairs(request_msg) do
    if k == 1 then
        request_base_uri = v
    else
        request_port = v
    end
end


local response_list = {}

if dev_module == 'true' then
    local timeout = timeout or 5000
    httpc:set_timeout(timeout)

    local list = {}

    for api, p in pairs(api_p) do
        local tmp = {}

        local p_table = json.decode(p)

        if type(p_table) ~= 'table' then
            tool.respClient(4053, '参数不正确')
        end

        if p_table then
            tmp = {
                path = api,
                method = p_table['method'] or "GET",
                headers = {},
                body = p_table['body'] or "",
                query = p_table['args'] or ""
            }
        else
            tmp = {
                path = api,
                headers = {},
                method = "GET",
                body = "",
                query = ""
            }
        end
        table.insert(list, tmp);
    end
    httpc:connect(request_base_uri, request_port)
    httpc:set_timeout(3000)
    --        ngx.say(json.encode(list))
    local responses, err_ = httpc:request_pipeline(list)
    --    ngx.print(json.encode(responses))
    --    ngx.print(json.encode(err_))
    if not responses or next(responses) == nil then
        --        ngx.print(type(err_))
        --        ngx.print(json.encode(err_))
        ngx.log(ngx.CRIT, 'http request multi service error:' .. err_)
        tool.respClient(5103, '服务异常')
    else
        for i, r in ipairs(responses) do
            --            ngx.print(json.encode(r))
            if r.status then
                table.insert(response_list, json.decode(r:read_body()))
                --                ngx.print(i)
                --                ngx.say(r.status)
                --                ngx.print(r:read_body())
            end
        end
    end
else
    tool.respClient(5555, '正式环境未开发完成')
end

-- response hander handle


-- response
tool.respClient(200, 'success', response_list)

-- send requests



--local list = {};
--for api, p in pairs(api_p) do
--    local tmp = {}
--
--    local p_table = json.decode(p)
--
--    if type(p_table) ~= 'table' then
--        tool.respClient(4053, '参数不正确')
--    end
--
--    local ngx_http_flag = ngx.HTTP_GET
--    if p_table['method'] then
--        ngx_http_flag = tool.getCpatureMethod(p_table['method'])
--    end
--
--    if p_table then
--        tmp = { '/internal' .. api, { args = p_table['args'], method = ngx_http_flag, body = p_table['body'] or "" } };
--    else
--        tmp = { '/internal' .. api };
--    end
--    table.insert(list, tmp);
--end;
--
--local response = { ngx.location.capture_multi(list) };
--
--local data = {};
--for num, resp in pairs(response) do
--    local header = resp['header']
--    data[header['RequestUri']] = resp['body'];
--end;
--
--ngx.say(json.encode(data));


