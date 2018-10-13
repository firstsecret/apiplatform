-- require tool
local tool = require('resty.tool')
local zhttp = require("resty.http")
--local json = require "cjson";
--local string = require('resty.string')

-- body
ngx.req.read_body();

--local re_args = ngx.req.get_uri_args()
--re_args.insert(get_headers)
-- get headers

-- set header
-- http handle
local capture_headers = {}
function httpHandler()
    local re_headers = ngx.req.get_headers()
    for key, val in pairs(re_headers) do
        --        ngx.say(key)
        capture_headers[key] = val
        ngx.req.set_header(key, val);
    end
end

-- gzip handle
ngx.req.set_header('Accept-Encoding', 'default')
capture_headers['Accept-Encoding'] = 'default'
-- http error deal
function httpErrorHandler(err)
    --    print("Http headers deal error:", err)
    tool.respClient(4070, 'http头信息格式不正确')
end

status = xpcall(httpHandler, httpErrorHandler)

local request_uri = ngx.var.request_uri
local request_method = ngx.var.request_method
--ngx.say(request_method)
--ngx.print(json.encode(ngx.var.remote_addr))

-- add real ip
capture_headers['X-Forwarded-For'] = ngx.var.remote_addr

-- http module
local httpc = zhttp.new()

-- redis
local redis = tool.getRedis()

-- 获取 服务 请求 列表
-- 根据服务端的 心跳包 确定 服务是否 已挂


-- post args handle
local post_args = ngx.req.get_post_args()
local post_str = ''
for k, v in pairs(post_args) do
    post_str = post_str .. k .. '=' .. v .. '&'
end
--string.len(post_str)
--local len = string.len(post_str) - 1
--ngx.print(len)
post_str = string.sub(post_str, 1, string.len(post_str) - 1)

-- response header handler
--for k, v in pairs(res.header) do
--    if k ~= "Transfer-Encoding" and k ~= "Connection" then
--        ngx.header[k] = v
--    end
--end
--
--     response handle
tool.rewriteResponse('Server', 'xiaoyumi')
tool.rewriteResponse('Content-Type', 'Application/json')

-- dev env
local dev_module = redis:get('apiplatform_service_dev')
local request_base_uri = 'http://' .. redis:get('apiplatform_service_base_uri')
--local url = request_base_uri .. request_uri
--ngx.say(request_uri)

if dev_module == 'true' then
    local timeout = timeout or 5000
    httpc:set_timeout(timeout)
    --    local ngx_http_flag = tool.getCpatureMethod(request_method)
    local body = ngx.req.read_body();
    --    ngx.print(url)
    local res, err_ = httpc:request_uri(request_base_uri, {
        path = request_uri,
        method = request_method,
        body = body,
        headers = capture_headers,
        --                headers = {
        --                    ["Content-Type"] = "application/x-www-form-urlencoded",
        --                }
    })

    -- error handle
    if not res then
        ngx.log(ngx.CRIT, 'http request service error:' .. err_)
        tool.respClient(5103, '服务异常' .. err_)
    else
        ngx.print(res.body)
        -- 重写header头
        --        for k, v in pairs(res.header) do
        --            if k ~= "Transfer-Encoding" and k ~= "Connection" then
        --                ngx.header[k] = v
        --            end
        --        end
        --        ngx.print('dfd')
        --        tool.rewriteResponse('RequestUri', request_uri)
        --        tool.rewriteResponse('Server', 'xiaoyumi')
        --        tool.setNgxVar('resp_body', res.body)
        --        local resp_headers = res.header
        --
        -- if res.status == 200 then
        -- ngx.print(res.body)
        -- else
        -- ngx.ctx.log_msg = res.status .. 'body: ' .. res.body .. ',header: ' .. tool.serialize(res.header)
        -- ngx.ctx.log_msg = res.status .. 'body: ' .. res.body
        -- local err_status_code = res.body['status_code']
        -- if err_status_code == nil then
        -- err_status_code = 4059
        -- end
        -- ngx.ctx.err_message = res.body['message']
        -- ngx.ctx.err_status_code = err_status_code
        -- return ngx.exit(res.status)
        -- end
    end
else
    return tool.respClient(5555, '正式环境未开发完成')
end

--if (request_method == 'POST') then
--    res = ngx.location.capture('/internal/' .. request_uri, { method = ngx.HTTP_POST, args = re_args })
--elseif (request_method == 'GET') then
--    res = ngx.location.capture('/internal/' .. request_uri, { method = ngx.HTTP_GET, args = re_args })
--elseif (request_method == 'PUT') then
--    res = ngx.location.capture('/internal/' .. request_uri, { method = ngx.HTTP_PUT, args = re_args })
--elseif (request_method == 'DELETE') then
--    res = ngx.location.capture('/internal/' .. request_uri, { method = ngx.HTTP_DELETE, args = re_args })
--elseif (request_method == 'OPTIONS') then
--    res = ngx.location.capture('/internal/' .. request_uri, { method = ngx.HTTP_OPTIONS, args = re_args })
--end
--ngx.say(request_uri)
--ngx.req.set_header("Content-Type", "application/json;charset=utf8");
--ngx.req.set_header("Accept", "application/json");
--get response header
--for k, v in pairs(res.header) do
--    if k ~= "Transfer-Encoding" and k ~= "Connection" then
--        ngx.header[k] = v
--    end
--end

-- response handle
--ngx.header['Server'] = 'xiaoyumi'
--tool.rewriteResponse('RequestUri', request_uri)
--tool.rewriteResponse('Server', 'xiaoyumi')
-- ctx
--ngx.ctx.log_msg = res.body

-- response

--local resp_body = string.sub(ngx.arg[1], 1, 1000)
--ngx.ctx.buffered = (ngx.ctx.buffered or "") .. resp_body
--if ngx.arg[2] then
--    ngx.var.resp_body = ngx.ctx.buffered
--end
--ngx.say(ngx.arg[1])
--ngx.var.resp_body = res.body
--tool.setNgxVar('resp_body', res.body)
--local resp_headers = res.header
--for k, h in pairs(resp_headers) do

--if res.status == 200 then
--    -- 后期 与 服务方 确定 返回 信息
--    --    tool.respClient()
--    ngx.print(res.body)
--else
--    ngx.ctx.log_msg = res.status .. 'body: ' .. res.body .. ',header: ' .. tool.serialize(res.header)
--    ngx.ctx.log_msg = res.status .. 'body: ' .. res.body
--    local err_status_code = res.body['status_code']
--    if err_status_code == nil then
--        err_status_code = 4059
--    end
--    ngx.ctx.err_message = res.body['message']
--    ngx.ctx.err_status_code = err_status_code
--    return ngx.exit(res.status)
--    --    ngx.say(ngx.ctx.log_msg)
--end

-- debug log
--file = io.open("/tmp/capture.log", "a+")
--file:write(res.body)
--for k, v in pairs(res.header) do
--    file:write(k .. ':' .. v)
--end
--file:close()
--local res = ngx.location.capture('/testInternal', {args = re_args})
--ngx.say(res.status)

--if res.status == 200 then
--    ngx.print(res.body)
--end)