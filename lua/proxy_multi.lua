--
-- proxy mulit
-- User: Bevan
-- Date: 2018/9/18
-- Time: 16:22
-- To change this template use File | Settings | File Templates.

--加载 json 库
local json = require "cjson";
local tool = require "resty.tool"
local zhttp = require "resty.http"

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
if next(api_p) == nil then
    return tool.respClient(4103, '未携带请求参数')
end

-- init redis
local redis_client = require('resty.redis_client')
local redis = redis_client:new()
local httpc = zhttp.new()
--拼接子请求

-- 获取 列表  做 负载
local node_list = redis:exec(function(red) return red:SMEMBERS('node_load_balancing') end)

math.randomseed(tostring(os.time()):reverse():sub(1, 6))
local index = math.random(1, table.getn(node_list))

local request_base_uri = node_list[index]
--local request_base_uri = redis:exec(function(red) return red:get('apiplatform_service_base_uri') end)
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

local list = {}

for api, p in pairs(api_p) do
    local tmp = {}

    local p_table = {}
    if p ~= '' then
        p_table = json.decode(p or '{}')
    end

    if type(p_table) ~= 'table' then
        tool.respClient(4053, '参数不正确')
    end

    -- request map
    local route_map_table = redis:exec(function(red)
        local info = red:HGETALL('services_map:' .. api)
        local rt = {}

        for i = 1, #info, 2 do
            rt[info[i]] = info[i + 1]
        end
        --            ngx.print(cjson.encode(rt))
        return rt
    end)

    if next(route_map_table) == nil or route_map_table['internal_api_path'] == nil then
        return tool.respClient(4033, '未发现对应服务')
    end

    if next(p_table) then
        local headers_t = {}
        if p_table["headers"] then
            headers_t = json.decode(p_table["headers"])
        else
            headers_t = {}
        end

        headers_t['X-Forwarded-For'] = ngx.var.remote_addr
        headers_t['Accept-Encoding'] = 'default'

        -- request method validate
        if (string.upper(p_table['method']) ~= route_map_table['request_method']) then
            return tool.respClient(4034, '请求方式不正确')
        end

        tmp = {
            path = route_map_table['internal_api_path'],
            method = route_map_table['internal_request_method'],
            headers = headers_t,
            body = p_table['body'] or "",
            query = p_table['args'] or "",
            keepalive_timeout = 60,
            keepalive_pool = 100
        }
    else
        local headers_t = {}
        headers_t['X-Forwarded-For'] = ngx.var.remote_addr
        headers_t['Accept-Encoding'] = 'default'
        tmp = {
            path = route_map_table['internal_api_path'],
            headers = headers_t,
            method = "GET",
            body = "",
            query = "",
            keepalive_timeout = 60,
            keepalive_pool = 100
        }
    end
    table.insert(list, tmp);
end

local http_flag = true
httpc:connect(request_base_uri, request_port)
local timeout = timeout or 5000
httpc:set_timeout(timeout)
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
        --                                ngx.print(json.encode(r))
        if r.status == ngx.HTTP_OK then
            --            dealMultiResponse(r)
            if pcall(function(r) table.insert(response_list, json.decode(r:read_body())) end, r) then
            else
                http_flag = false
                tool.respClient(5001, '部分服务异常! 请稍后再试~')
            end
            --                        table.insert(response_list, json.decode(r:read_body()))
            --                ngx.print(i)
            --                ngx.say(r.status)
            --                ngx.print(r:read_body())
        end
    end
end
-- response hander handle
--function dealMultiResponse(response)
--    table.insert(response_list, json.decode(response:read_body()))
--end
--
--function multiErrorHandle()
--    local trace = debug.traceback()
--    local d = debug.debug()
--    tool.respClient(5001, '部分服务异常! 请稍后再试~')
--end
-- response
if http_flag then
    tool.respClient(200, 'success', response_list)
end






