--
-- sign validate.
-- User: Bevan
-- Date: 2018/10/11
-- Time: 14:49
-- To change this template use File | Settings | File Templates.
--

--加载 json 库
--local json = require "cjson";
local tool = require "resty.tool"
--local zhttp = require("resty.http")

-- get request method
local request_method = ngx.var.request_method;
local request_uri = ngx.var.uri
local remote_addr = ngx.var.remote_addr

-- gzip handle
ngx.req.set_header('Accept-Encoding', 'default')

--判断请求方式
if request_method == "GET" then
    --    local respData = {}
    --    respData['status_code'] = 4050
    --    respData['msg'] = '只支持post请求'
    --    ngx.say(json.encode(respData));
    tool.respClient(4050, '只支持post请求')
    return;
end;

-- get post args
--读取 post 参数.表单需要是 x-www-form-urlencoded
ngx.req.read_body();
local request_args = ngx.req.get_post_args();

-- check app key
if request_args['app_key'] == nil then
    return tool.respClient(4061, '缺少app_key')
end

if request_args['sign'] == nil then
    return tool.respClient(4062, '缺少sign')
end

if request_args['sequenceId'] == nil then
    return tool.respClient(4063, '缺少sequenceId')
end

if request_args['reqData'] == nil then
    return tool.respClient(4064, '缺少reqData')
end

-- 暂时 只 支持 md5
--local secret_method = request_args['method'] or 'md5'

local redis_client = require "resty.redis_client"
local redis = redis_client:new()

local r_app_secret = redis:exec(function(red)
    local info = red:hgetall('api_app_key:'.. request_args['app_key'])
    local rt = {}

    for i = 1, #info, 2 do
        rt[info[i]] = info[i + 1]
    end
    --            ngx.print(cjson.encode(rt))
    return rt
end)

if next(r_app_secret) == nil or r_app_secret == nil or r_app_secret == ngx.null then
    tool.respClient(4009, 'appkey不存在')
else
    -- get appsecret
--    local app_secret = string.sub(r_app_secret, 1, 32)
    local app_secret = r_app_secret['app_secret']
    -- get timestamp
    local req_timestamp = os.time()

    --  check is valid
    local old_req_record = redis:exec(function(red) return red:get(remote_addr .. request_uri) end)

    -- is requested
    if old_req_record and old_req_record ~= ngx.null then
        --        local differ = tonumber(req_timestamp) - tonumber(old_req_record)
        -- in 5 minutes
        return tool.respClient(4065, '请乎频繁的重复提交')
    else
        -- do record
        redis:exec(function(red)
            red:set(remote_addr .. request_uri, req_timestamp)
            red:expire(remote_addr .. request_uri, 300)
            return
        end)
    end

    -- check data valid
    -- make sign

    local reqData_str = request_args['reqData']
    --    ngx.print(reqData_str)
    reqData_str = tool.trim(string.gsub(reqData_str, " ", ""))

    local factory_sign = reqData_str .. request_args['sequenceId'] .. app_secret

    factory_sign = ngx.md5(factory_sign)

    --    ngx.print(factory_sign)
    if factory_sign == request_args['sign'] then
        -- to do
        return tool.respClient(200, '校验正确')
    else
        return tool.respClient(4066, 'sign不正确')
    end
end







